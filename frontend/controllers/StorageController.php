<?php

namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;
use frontend\models\StorageItem;
use frontend\models\SakipSkpd;
use frontend\components\NextcloudService;
use frontend\components\PdfCompressor;

/**
 * EsakipStorage — file manager per-SKPD dengan sinkron ganda (server lokal + NextCloud).
 *
 * Akses:
 *  - superadmin / admin / developer  -> melihat SEMUA SKPD
 *  - skpd                            -> hanya folder SKPD sendiri
 *  - role lain                       -> tidak punya akses
 */
class StorageController extends Controller
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    ['allow' => true, 'roles' => ['@']],
                ],
                'denyCallback' => function ($rule, $action) {
                    return Yii::$app->response->redirect(['site/login']);
                },
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'create-folder' => ['POST'],
                    'upload' => ['POST'],
                    'delete' => ['POST'],
                    'retry-sync' => ['POST'],
                ],
            ],
        ]);
    }

    // ===================== Role & akses =====================

    private function assignments()
    {
        return Yii::$app->authManager->getAssignments(Yii::$app->user->getId());
    }

    private function canSeeAll()
    {
        $a = $this->assignments();
        return isset($a['superadmin']) || isset($a['admin']) || isset($a['developer']);
    }

    private function isSkpdRole()
    {
        $a = $this->assignments();
        return isset($a['skpd']);
    }

    /** Lempar error jika user tidak punya akses storage sama sekali. */
    private function guardAccess()
    {
        if ($this->canSeeAll() || $this->isSkpdRole()) {
            return;
        }
        throw new ForbiddenHttpException('Anda tidak memiliki akses ke EsakipStorage.');
    }

    /** Pastikan user boleh mengakses SKPD tertentu. */
    private function assertSkpdAllowed($skpdId)
    {
        if ($this->canSeeAll()) {
            return;
        }
        $own = (int) Yii::$app->user->identity->refskpd_id;
        if ((int) $skpdId !== $own || $own === 0) {
            throw new ForbiddenHttpException('Anda hanya dapat mengakses folder SKPD Anda sendiri.');
        }
    }

    // ===================== Util path =====================

    private function sanitize($name)
    {
        $name = str_replace(['/', '\\', '..', "\0"], ' ', (string) $name);
        $name = preg_replace('/[<>:"|?*]+/', '', $name);
        $name = preg_replace('/\s+/', ' ', $name);
        return trim($name);
    }

    private function skpdFolderName(SakipSkpd $skpd)
    {
        $label = ($skpd->kode_skpd ? $skpd->kode_skpd . ' - ' : '') . $skpd->nama_skpd;
        return $this->sanitize($label);
    }

    private function storageBase()
    {
        return rtrim(Yii::getAlias(Yii::$app->params['sakipStorage']['localBase']), '/\\');
    }

    private function localPathFor(SakipSkpd $skpd, $relPath)
    {
        $p = $this->storageBase() . '/' . $this->skpdFolderName($skpd);
        if ($relPath !== '') {
            $p .= '/' . $relPath;
        }
        return $p;
    }

    private function davPathFor(SakipSkpd $skpd, $relPath)
    {
        $p = $this->skpdFolderName($skpd);
        if ($relPath !== '') {
            $p .= '/' . $relPath;
        }
        return $p;
    }

    // ===================== Loader =====================

    private function loadSkpd($skpdId)
    {
        $skpd = SakipSkpd::findOne(['refskpd_id' => $skpdId]);
        if (!$skpd) {
            throw new NotFoundHttpException('SKPD tidak ditemukan.');
        }
        return $skpd;
    }

    private function loadFolder($id, $skpdId)
    {
        $f = StorageItem::findOne(['id' => $id, 'type' => StorageItem::TYPE_FOLDER, 'is_deleted' => 0]);
        if (!$f) {
            throw new NotFoundHttpException('Folder tidak ditemukan.');
        }
        if ((int) $f->refskpd_id !== (int) $skpdId) {
            throw new ForbiddenHttpException('Folder bukan milik SKPD ini.');
        }
        $this->assertSkpdAllowed($f->refskpd_id);
        return $f;
    }

    // ===================== Aksi =====================

    /**
     * @param int|null $skpd   id SKPD yang dibuka
     * @param int|null $folder id folder yang dibuka (null = root SKPD)
     */
    public function actionIndex($skpd = null, $folder = null)
    {
        $this->layout = 'main-storage';
        $this->guardAccess();

        // Role skpd: paksa ke SKPD sendiri.
        if (!$this->canSeeAll()) {
            $skpd = (int) Yii::$app->user->identity->refskpd_id;
            if (!$skpd) {
                throw new ForbiddenHttpException('Akun Anda belum tertaut ke SKPD manapun.');
            }
        }

        // Mode daftar SKPD (hanya untuk role lihat-semua, ketika belum memilih SKPD).
        if ($skpd === null) {
            $allSkpd = SakipSkpd::find()
                ->where(['skpd_isaktif' => 'T'])
                ->orderBy('nama_skpd ASC')
                ->all();

            // hitung jumlah item per SKPD
            $counts = StorageItem::find()
                ->select(['refskpd_id', 'c' => 'COUNT(*)'])
                ->where(['is_deleted' => 0, 'type' => StorageItem::TYPE_FILE])
                ->groupBy('refskpd_id')
                ->indexBy('refskpd_id')
                ->asArray()
                ->all();

            return $this->render('skpd-list', [
                'allSkpd' => $allSkpd,
                'counts' => $counts,
                'ncBaseFolder' => Yii::$app->params['nextcloud']['baseFolder'] ?? 'SAKIP',
            ]);
        }

        $this->assertSkpdAllowed($skpd);
        $skpdModel = $this->loadSkpd($skpd);

        $currentFolder = null;
        if ($folder !== null) {
            $currentFolder = $this->loadFolder($folder, $skpd);
        }

        $children = StorageItem::find()
            ->where([
                'refskpd_id' => $skpd,
                'parent_id' => $currentFolder ? $currentFolder->id : null,
                'is_deleted' => 0,
            ])
            ->orderBy(['type' => SORT_ASC, 'name' => SORT_ASC])
            ->all();

        $breadcrumb = $currentFolder ? array_merge($currentFolder->ancestors(), [$currentFolder]) : [];

        return $this->render('index', [
            'skpdModel' => $skpdModel,
            'skpdId' => (int) $skpd,
            'currentFolder' => $currentFolder,
            'children' => $children,
            'breadcrumb' => $breadcrumb,
            'canSeeAll' => $this->canSeeAll(),
            'maxSize' => (int) Yii::$app->params['sakipStorage']['maxSize'],
            'ncBaseFolder' => Yii::$app->params['nextcloud']['baseFolder'] ?? 'SAKIP',
            'ncSkpdFolder' => $this->skpdFolderName($skpdModel),
        ]);
    }

    public function actionCreateFolder()
    {
        $this->guardAccess();
        $req = Yii::$app->request;
        $skpdId = (int) $req->post('skpd');
        $parentId = $req->post('parent') ?: null;
        $name = $this->sanitize($req->post('name'));

        if (!$this->canSeeAll()) {
            $skpdId = (int) Yii::$app->user->identity->refskpd_id;
        }
        $this->assertSkpdAllowed($skpdId);
        $skpd = $this->loadSkpd($skpdId);

        $parent = $parentId ? $this->loadFolder($parentId, $skpdId) : null;

        if ($name === '') {
            Yii::$app->session->setFlash('error', 'Nama folder tidak boleh kosong.');
            return $this->redirectBack($skpdId, $parentId);
        }

        // Cegah duplikat nama di folder yang sama.
        $exists = StorageItem::find()->where([
            'refskpd_id' => $skpdId,
            'parent_id' => $parent ? $parent->id : null,
            'name' => $name,
            'is_deleted' => 0,
        ])->exists();
        if ($exists) {
            Yii::$app->session->setFlash('error', "Folder \"$name\" sudah ada di sini.");
            return $this->redirectBack($skpdId, $parentId);
        }

        $relPath = ($parent ? $parent->rel_path . '/' : '') . $name;

        $item = new StorageItem();
        $item->refskpd_id = $skpdId;
        $item->parent_id = $parent ? $parent->id : null;
        $item->name = $name;
        $item->type = StorageItem::TYPE_FOLDER;
        $item->rel_path = $relPath;
        $item->created_by = Yii::$app->user->getId();

        // Buat folder lokal.
        $localDir = $this->localPathFor($skpd, $relPath);
        if (!is_dir($localDir) && !@mkdir($localDir, 0775, true) && !is_dir($localDir)) {
            Yii::$app->session->setFlash('error', 'Gagal membuat folder di server lokal.');
            return $this->redirectBack($skpdId, $parentId);
        }

        // Sinkron ke NextCloud.
        $this->syncFolder($item, $skpd, $relPath);
        $item->save(false);

        Yii::$app->session->setFlash('success', "Folder \"$name\" dibuat.");
        return $this->redirectBack($skpdId, $parentId);
    }

    public function actionUpload()
    {
        $this->guardAccess();
        $req = Yii::$app->request;
        $skpdId = (int) $req->post('skpd');
        $parentId = $req->post('parent') ?: null;

        if (!$this->canSeeAll()) {
            $skpdId = (int) Yii::$app->user->identity->refskpd_id;
        }
        $this->assertSkpdAllowed($skpdId);
        $skpd = $this->loadSkpd($skpdId);
        $parent = $parentId ? $this->loadFolder($parentId, $skpdId) : null;

        $file = UploadedFile::getInstanceByName('file');
        if (!$file) {
            Yii::$app->session->setFlash('error', 'Tidak ada file yang dipilih.');
            return $this->redirectBack($skpdId, $parentId);
        }

        $cfg = Yii::$app->params['sakipStorage'];
        $ext = strtolower($file->extension);
        if (!in_array($ext, $cfg['allowedExt'], true)) {
            Yii::$app->session->setFlash('error', 'Hanya file PDF yang diperbolehkan.');
            return $this->redirectBack($skpdId, $parentId);
        }
        if ($file->size > $cfg['maxSize']) {
            Yii::$app->session->setFlash('error', 'Ukuran file melebihi batas ' . round($cfg['maxSize'] / 1048576) . ' MB.');
            return $this->redirectBack($skpdId, $parentId);
        }
        // Validasi MIME asli isi file.
        if (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $realMime = finfo_file($finfo, $file->tempName);
            finfo_close($finfo);
            if (!in_array($realMime, $cfg['allowedMime'], true)) {
                Yii::$app->session->setFlash('error', 'Isi file bukan PDF yang valid.');
                return $this->redirectBack($skpdId, $parentId);
            }
        }

        // Nama file unik (hindari menimpa).
        $baseName = $this->sanitize(pathinfo($file->name, PATHINFO_FILENAME));
        if ($baseName === '') {
            $baseName = 'dokumen';
        }
        $fileName = $baseName . '.' . $ext;
        $n = 1;
        while (StorageItem::find()->where([
            'refskpd_id' => $skpdId,
            'parent_id' => $parent ? $parent->id : null,
            'name' => $fileName,
            'is_deleted' => 0,
        ])->exists()) {
            $fileName = $baseName . ' (' . $n++ . ').' . $ext;
        }

        $relPath = ($parent ? $parent->rel_path . '/' : '') . $fileName;
        $localPath = $this->localPathFor($skpd, $relPath);
        $localDir = dirname($localPath);
        if (!is_dir($localDir) && !@mkdir($localDir, 0775, true) && !is_dir($localDir)) {
            Yii::$app->session->setFlash('error', 'Gagal menyiapkan folder penyimpanan.');
            return $this->redirectBack($skpdId, $parentId);
        }

        if (!$file->saveAs($localPath)) {
            Yii::$app->session->setFlash('error', 'Gagal menyimpan file di server lokal.');
            return $this->redirectBack($skpdId, $parentId);
        }

        $item = new StorageItem();
        $item->refskpd_id = $skpdId;
        $item->parent_id = $parent ? $parent->id : null;
        $item->name = $fileName;
        $item->type = StorageItem::TYPE_FILE;
        $item->rel_path = $relPath;
        $item->size = filesize($localPath);
        $item->mime = 'application/pdf';
        $item->local_path = $localPath;
        $item->created_by = Yii::$app->user->getId();

        // 1) Kirim file ASLI ke NextCloud lebih dulu.
        $this->syncFile($item, $skpd, $relPath, $localPath);
        // 2) Kompres salinan LOKAL — hanya setelah NextCloud terima file asli,
        //    sehingga retry (saat pending/failed) tetap mengirim versi asli.
        if ($item->sync_status === StorageItem::SYNC_SYNCED) {
            $cmp = PdfCompressor::compress($localPath, Yii::$app->params['sakipStorage']['compress'] ?? []);
            if ($cmp['ok']) {
                $item->size = $cmp['size'];
            }
        }
        $item->save(false);

        $msg = "File \"$fileName\" diunggah.";
        if ($item->sync_status !== StorageItem::SYNC_SYNCED) {
            $msg .= ' (Sinkron NextCloud: ' . $item->sync_status . ')';
        }
        Yii::$app->session->setFlash('success', $msg);
        return $this->redirectBack($skpdId, $parentId);
    }

    public function actionDownload($id)
    {
        $this->guardAccess();
        $item = StorageItem::findOne(['id' => $id, 'type' => StorageItem::TYPE_FILE, 'is_deleted' => 0]);
        if (!$item) {
            throw new NotFoundHttpException('File tidak ditemukan.');
        }
        $this->assertSkpdAllowed($item->refskpd_id);
        if (!$item->local_path || !is_file($item->local_path)) {
            throw new NotFoundHttpException('File fisik tidak ada di server.');
        }
        return Yii::$app->response->sendFile($item->local_path, $item->name, [
            'mimeType' => $item->mime ?: 'application/pdf',
            'inline' => false,
        ]);
    }

    public function actionDelete($id)
    {
        $this->guardAccess();
        $item = StorageItem::findOne(['id' => $id, 'is_deleted' => 0]);
        if (!$item) {
            throw new NotFoundHttpException('Item tidak ditemukan.');
        }
        $this->assertSkpdAllowed($item->refskpd_id);
        $skpd = $this->loadSkpd($item->refskpd_id);

        // Hapus di NextCloud (masuk trash NextCloud, bisa dipulihkan).
        $nc = new NextcloudService();
        if ($nc->isEnabled()) {
            $nc->delete($this->davPathFor($skpd, $item->rel_path));
        }

        // Soft-delete di registry (file fisik lokal tetap disimpan untuk pemulihan).
        $this->softDeleteTree($item);

        Yii::$app->session->setFlash('success', ($item->isFolder() ? 'Folder' : 'File') . " \"{$item->name}\" dihapus.");
        return $this->redirectBack($item->refskpd_id, $item->parent_id);
    }

    public function actionRetrySync($id)
    {
        $this->guardAccess();
        $item = StorageItem::findOne(['id' => $id, 'is_deleted' => 0]);
        if (!$item) {
            throw new NotFoundHttpException('Item tidak ditemukan.');
        }
        $this->assertSkpdAllowed($item->refskpd_id);
        $skpd = $this->loadSkpd($item->refskpd_id);

        if ($item->isFolder()) {
            $this->syncFolder($item, $skpd, $item->rel_path);
        } else {
            $this->syncFile($item, $skpd, $item->rel_path, $item->local_path);
            // File asli baru saja terkirim -> kompres salinan lokal.
            if ($item->sync_status === StorageItem::SYNC_SYNCED) {
                $cmp = PdfCompressor::compress($item->local_path, Yii::$app->params['sakipStorage']['compress'] ?? []);
                if ($cmp['ok']) {
                    $item->size = $cmp['size'];
                }
            }
        }
        $item->save(false);

        Yii::$app->session->setFlash(
            $item->sync_status === StorageItem::SYNC_SYNCED ? 'success' : 'error',
            'Sinkron NextCloud: ' . $item->sync_status . ($item->sync_message ? ' — ' . $item->sync_message : '')
        );
        return $this->redirectBack($item->refskpd_id, $item->parent_id);
    }

    // ===================== Helper internal =====================

    private function syncFolder(StorageItem $item, SakipSkpd $skpd, $relPath)
    {
        $nc = new NextcloudService();
        if (!$nc->isEnabled()) {
            $item->sync_status = StorageItem::SYNC_PENDING;
            $item->sync_message = 'Integrasi NextCloud belum dikonfigurasi.';
            return;
        }
        $davPath = $this->davPathFor($skpd, $relPath);
        $r = $nc->ensureFolder($davPath);
        $item->nextcloud_path = $davPath;
        $item->sync_status = $r['ok'] ? StorageItem::SYNC_SYNCED : StorageItem::SYNC_FAILED;
        $item->sync_message = $r['ok'] ? null : $r['message'];
    }

    private function syncFile(StorageItem $item, SakipSkpd $skpd, $relPath, $localPath)
    {
        $nc = new NextcloudService();
        if (!$nc->isEnabled()) {
            $item->sync_status = StorageItem::SYNC_PENDING;
            $item->sync_message = 'Integrasi NextCloud belum dikonfigurasi.';
            return;
        }
        $davPath = $this->davPathFor($skpd, $relPath);
        $r = $nc->putFile($davPath, $localPath);
        $item->nextcloud_path = $davPath;
        $item->sync_status = $r['ok'] ? StorageItem::SYNC_SYNCED : StorageItem::SYNC_FAILED;
        $item->sync_message = $r['ok'] ? null : $r['message'];
    }

    private function softDeleteTree(StorageItem $item)
    {
        $item->is_deleted = 1;
        $item->save(false);
        if ($item->isFolder()) {
            foreach (StorageItem::findAll(['parent_id' => $item->id, 'is_deleted' => 0]) as $child) {
                $this->softDeleteTree($child);
            }
        }
    }

    private function redirectBack($skpdId, $folderId)
    {
        $params = ['index', 'skpd' => $skpdId];
        if ($folderId) {
            $params['folder'] = $folderId;
        }
        return $this->redirect($params);
    }
}
