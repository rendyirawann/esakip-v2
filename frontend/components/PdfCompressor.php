<?php

namespace frontend\components;

/**
 * Kompresi PDF memakai Ghostscript (jika tersedia).
 *
 * Dipakai HANYA untuk salinan lokal di server eSakip — file yang dikirim ke
 * NextCloud tetap versi ASLI (tidak dikompres). Bersifat best-effort: bila
 * Ghostscript tidak ada / exec dimatikan / hasil tidak lebih kecil, file asli
 * dibiarkan apa adanya.
 */
class PdfCompressor
{
    private static $gsCache = null;

    /**
     * Kompres file PDF di tempat ($path ditimpa bila berhasil & lebih kecil).
     *
     * @return array ['ok' => bool, 'size' => int, 'message' => string]
     */
    public static function compress($path, array $cfg = [])
    {
        $size = is_file($path) ? (int) filesize($path) : 0;

        if (empty($cfg['enabled'])) {
            return ['ok' => false, 'size' => $size, 'message' => 'Kompresi dinonaktifkan.'];
        }
        if (!is_file($path) || $size === 0) {
            return ['ok' => false, 'size' => $size, 'message' => 'File tidak ditemukan.'];
        }
        if (!function_exists('exec')) {
            return ['ok' => false, 'size' => $size, 'message' => 'Fungsi exec() tidak tersedia.'];
        }

        $gs = self::findGs($cfg['gsBin'] ?? '');
        if (!$gs) {
            return ['ok' => false, 'size' => $size, 'message' => 'Ghostscript tidak ditemukan.'];
        }

        $level = in_array(($cfg['level'] ?? 'ebook'), ['screen', 'ebook', 'printer', 'prepress'], true)
            ? $cfg['level'] : 'ebook';

        $out = $path . '.gscompressed';
        // -dSAFER: aktifkan sandbox (batasi akses file & operator berbahaya).
        // -dNEWPDF=true: parser PDF baru yang lebih aman (default di gs 10).
        $cmd = escapeshellarg($gs)
            . ' -dSAFER -dBATCH -dNOPAUSE -dQUIET'
            . ' -sDEVICE=pdfwrite -dCompatibilityLevel=1.4'
            . ' -dPDFSETTINGS=/' . $level
            . ' -dDetectDuplicateImages=true'
            . ' -sOutputFile=' . escapeshellarg($out)
            . ' ' . escapeshellarg($path) . ' 2>&1';

        $o = [];
        $code = 1;
        @exec($cmd, $o, $code);

        if ($code === 0 && is_file($out) && filesize($out) > 0 && filesize($out) < $size) {
            @unlink($path);
            if (@rename($out, $path)) {
                return ['ok' => true, 'size' => (int) filesize($path), 'message' => 'Dikompres.'];
            }
        }
        if (is_file($out)) {
            @unlink($out);
        }
        return ['ok' => false, 'size' => $size, 'message' => 'Tidak dikompres (gagal / tidak lebih kecil).'];
    }

    /** Temukan binary Ghostscript (configured > kandidat umum). */
    private static function findGs($configured)
    {
        if (self::$gsCache !== null) {
            return self::$gsCache;
        }
        $cands = [];
        if ($configured) {
            $cands[] = $configured;
        }
        $cands = array_merge($cands, ['gswin64c', 'gswin32c', 'gs']);
        foreach ($cands as $c) {
            $o = [];
            $code = 1;
            @exec(escapeshellarg($c) . ' --version 2>&1', $o, $code);
            if ($code === 0 && !empty($o)) {
                return self::$gsCache = $c;
            }
        }
        return self::$gsCache = null;
    }
}
