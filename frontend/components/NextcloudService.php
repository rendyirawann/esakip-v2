<?php

namespace frontend\components;

use Yii;

/**
 * Klien WebDAV NextCloud sederhana berbasis cURL (tanpa dependency composer baru).
 *
 * Semua path relatif ($davRel) dihitung DI BAWAH baseFolder, mis:
 *   "1.01.01 - Dinas Pendidikan/Laporan/file.pdf"
 * -> https://host/remote.php/dav/files/<davUser>/<baseFolder>/1.01.01 .../file.pdf
 *
 * Setiap method mengembalikan array ['ok' => bool, 'message' => string].
 */
class NextcloudService
{
    private $cfg;

    public function __construct()
    {
        $this->cfg = Yii::$app->params['nextcloud'] ?? [];
    }

    public function isEnabled()
    {
        return !empty($this->cfg['enabled'])
            && !empty($this->cfg['baseUrl'])
            && !empty($this->cfg['username']);
    }

    /** Bangun URL WebDAV penuh dari path relatif (di bawah baseFolder). */
    public function fullUrl($davRel)
    {
        $base = rtrim($this->cfg['baseUrl'], '/');
        $davUser = $this->cfg['davUser'] ?? 'admin';
        $folder = trim($this->cfg['baseFolder'] ?? '', '/');
        $path = ($folder !== '' ? $folder . '/' : '') . ltrim((string) $davRel, '/');

        $segs = array_map('rawurlencode', array_filter(explode('/', $path), 'strlen'));
        return $base . '/remote.php/dav/files/' . rawurlencode($davUser) . '/' . implode('/', $segs);
    }

    /** Eksekusi satu request WebDAV. */
    private function req($method, $url, $bodyFile = null)
    {
        $ch = curl_init($url);
        $opts = [
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_USERPWD => ($this->cfg['username'] ?? '') . ':' . ($this->cfg['password'] ?? ''),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 120,
            CURLOPT_CONNECTTIMEOUT => 15,
            CURLOPT_SSL_VERIFYPEER => !empty($this->cfg['verifySsl']),
            CURLOPT_SSL_VERIFYHOST => !empty($this->cfg['verifySsl']) ? 2 : 0,
            CURLOPT_HTTPHEADER => ['OCS-APIRequest: true'],
        ];

        $fh = null;
        if ($bodyFile !== null) {
            $fh = fopen($bodyFile, 'rb');
            $opts[CURLOPT_PUT] = true;
            $opts[CURLOPT_INFILE] = $fh;
            $opts[CURLOPT_INFILESIZE] = filesize($bodyFile);
        }

        curl_setopt_array($ch, $opts);
        $body = curl_exec($ch);
        $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err = curl_error($ch);
        curl_close($ch);
        if (is_resource($fh)) {
            fclose($fh);
        }

        return ['code' => $code, 'body' => $body, 'error' => $err];
    }

    /** Buat folder (beserta seluruh induknya) di bawah baseFolder. */
    public function ensureFolder($davRelFolder)
    {
        $parts = array_values(array_filter(explode('/', trim((string) $davRelFolder, '/')), 'strlen'));
        $cur = '';
        foreach ($parts as $p) {
            $cur = ($cur === '') ? $p : ($cur . '/' . $p);
            $r = $this->req('MKCOL', $this->fullUrl($cur));
            // 201 = dibuat, 405 = sudah ada, 301 = redirect (anggap ada).
            if (!in_array($r['code'], [201, 405, 301], true)) {
                return ['ok' => false, 'message' => "MKCOL \"$cur\" gagal (HTTP {$r['code']}) {$r['error']}"];
            }
        }
        return ['ok' => true, 'message' => 'ok'];
    }

    /** Hitung path induk (tanpa dirname agar aman dari backslash Windows). */
    private function parentOf($davRel)
    {
        $segs = array_values(array_filter(explode('/', trim((string) $davRel, '/')), 'strlen'));
        array_pop($segs);
        return implode('/', $segs);
    }

    /** Upload file ke NextCloud (folder induk dibuat otomatis). */
    public function putFile($davRelFile, $localFile)
    {
        if (!is_file($localFile)) {
            return ['ok' => false, 'message' => "File lokal tidak ditemukan: $localFile"];
        }
        $parent = $this->parentOf($davRelFile);
        if ($parent !== '') {
            $ef = $this->ensureFolder($parent);
            if (!$ef['ok']) {
                return $ef;
            }
        }
        $r = $this->req('PUT', $this->fullUrl($davRelFile), $localFile);
        if (in_array($r['code'], [200, 201, 204], true)) {
            return ['ok' => true, 'message' => 'ok'];
        }
        return ['ok' => false, 'message' => "PUT gagal (HTTP {$r['code']}) {$r['error']}"];
    }

    /** Hapus item di NextCloud (masuk ke trash NextCloud, bisa dipulihkan). */
    public function delete($davRel)
    {
        $r = $this->req('DELETE', $this->fullUrl($davRel));
        // 404 = sudah tidak ada -> anggap sukses (idempotent).
        if (in_array($r['code'], [200, 204, 404], true)) {
            return ['ok' => true, 'message' => 'ok'];
        }
        return ['ok' => false, 'message' => "DELETE gagal (HTTP {$r['code']}) {$r['error']}"];
    }

    /** Cek koneksi & kredensial (PROPFIND ke baseFolder). */
    public function ping()
    {
        $url = $this->fullUrl('');
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_CUSTOMREQUEST => 'PROPFIND',
            CURLOPT_USERPWD => ($this->cfg['username'] ?? '') . ':' . ($this->cfg['password'] ?? ''),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 20,
            CURLOPT_HTTPHEADER => ['Depth: 0', 'OCS-APIRequest: true'],
            CURLOPT_SSL_VERIFYPEER => !empty($this->cfg['verifySsl']),
            CURLOPT_SSL_VERIFYHOST => !empty($this->cfg['verifySsl']) ? 2 : 0,
        ]);
        curl_exec($ch);
        $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err = curl_error($ch);
        curl_close($ch);
        if ($code === 207) {
            return ['ok' => true, 'message' => 'Terhubung'];
        }
        return ['ok' => false, 'message' => "Gagal terhubung (HTTP $code) $err"];
    }
}
