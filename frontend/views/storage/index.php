<?php

use yii\helpers\Url;
use yii\helpers\Html;
use frontend\models\StorageItem;

/** @var yii\web\View $this */
/** @var \frontend\models\SakipSkpd $skpdModel */
/** @var int $skpdId */
/** @var \frontend\models\StorageItem|null $currentFolder */
/** @var \frontend\models\StorageItem[] $children */
/** @var \frontend\models\StorageItem[] $breadcrumb */
/** @var bool $canSeeAll */
/** @var int $maxSize */

$this->title = 'EsakipStorage';
$csrf = Yii::$app->request->csrfToken;
$csrfParam = Yii::$app->request->csrfParam;
$currentId = $currentFolder ? $currentFolder->id : '';

$fmtSize = function ($b) {
    if ($b === null) return '—';
    $u = ['B', 'KB', 'MB', 'GB'];
    $i = 0;
    $b = (float) $b;
    while ($b >= 1024 && $i < 3) { $b /= 1024; $i++; }
    return ($i === 0 ? (int) $b : number_format($b, 1)) . ' ' . $u[$i];
};

// Ikon inline (tidak bergantung font icon).
$icoFolder = '<svg viewBox="0 0 24 24" width="34" height="34"><path fill="#3b82f6" d="M10 4H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2h-8l-2-2z"/></svg>';
$icoPdf = '<svg viewBox="0 0 24 24" width="34" height="34"><path fill="#e3342f" d="M6 2a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6H6z"/><path fill="#fff" opacity=".35" d="M14 2v6h6z"/><text x="12" y="17" font-size="6.5" fill="#fff" text-anchor="middle" font-family="Arial" font-weight="bold">PDF</text></svg>';

$badge = function ($status) {
    switch ($status) {
        case StorageItem::SYNC_SYNCED:
            return '<span class="esk-syn esk-syn-ok">✔ Tersinkron</span>';
        case StorageItem::SYNC_FAILED:
            return '<span class="esk-syn esk-syn-fail">✖ Gagal</span>';
        default:
            return '<span class="esk-syn esk-syn-pend">⧖ Menunggu</span>';
    }
};

$this->registerCss(<<<'CSS'
.esk-fm{font-family:"Plus Jakarta Sans",sans-serif}
.esk-fm-bar{display:flex;flex-wrap:wrap;gap:12px;align-items:center;justify-content:space-between;background:#fff;border:1px solid #eef0f3;border-radius:14px;padding:14px 18px;margin-bottom:18px;box-shadow:0 2px 10px rgba(16,30,54,.04)}
.esk-fm-path{display:flex;flex-wrap:wrap;align-items:center;gap:6px;font-size:.92rem;color:#475569}
.esk-fm-path a{color:#0d9488;text-decoration:none;font-weight:600}
.esk-fm-path a:hover{text-decoration:underline}
.esk-fm-path .sep{color:#cbd5e1}
.esk-fm-path .esk-cloud{color:#2563eb;font-weight:700;background:#eff6ff;padding:3px 10px;border-radius:20px}
.esk-fm-note{font-size:.82rem;color:#94a3b8;margin:-8px 2px 16px}
.esk-fm-note b{color:#64748b}
.esk-fm-actions{display:flex;gap:8px;flex-wrap:wrap}
.esk-btn{border:none;border-radius:10px;padding:9px 16px;font-weight:600;font-size:.88rem;cursor:pointer;display:inline-flex;align-items:center;gap:6px}
.esk-btn-primary{background:linear-gradient(135deg,#0d9488,#0f766e);color:#fff}
.esk-btn-soft{background:#f1f5f9;color:#0f766e}
.esk-btn:hover{filter:brightness(1.05)}
.esk-fm-card{background:#fff;border:1px solid #eef0f3;border-radius:14px;overflow:hidden;box-shadow:0 2px 10px rgba(16,30,54,.04)}
.esk-fm-head{display:grid;grid-template-columns:1fr 150px 120px 150px;gap:10px;padding:12px 20px;font-size:.74rem;letter-spacing:.04em;text-transform:uppercase;color:#94a3b8;border-bottom:1px solid #f1f5f9;font-weight:700}
.esk-row{display:grid;grid-template-columns:1fr 150px 120px 150px;gap:10px;align-items:center;padding:10px 20px;border-bottom:1px solid #f5f7fa;transition:background .12s}
.esk-row:hover{background:#f8fafc}
.esk-name{display:flex;align-items:center;gap:12px;min-width:0}
.esk-name a{color:#1e293b;font-weight:600;text-decoration:none;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.esk-name a:hover{color:#0d9488}
.esk-syn{font-size:.76rem;font-weight:700;padding:3px 10px;border-radius:20px;white-space:nowrap}
.esk-syn-ok{background:#dcfce7;color:#15803d}
.esk-syn-fail{background:#fee2e2;color:#b91c1c}
.esk-syn-pend{background:#fef3c7;color:#b45309}
.esk-cell-act{display:flex;gap:6px;justify-content:flex-end}
.esk-ia{border:none;background:#f1f5f9;color:#475569;width:34px;height:34px;border-radius:9px;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;font-size:.95rem;text-decoration:none}
.esk-ia:hover{background:#e2e8f0;color:#0f172a}
.esk-ia-text{width:auto;padding:0 14px;gap:6px;font-weight:700;font-size:.82rem}
.esk-ia-danger{background:#fee2e2;color:#dc2626}
.esk-ia-danger:hover{background:#dc2626;color:#fff}
.esk-empty{padding:60px 20px;text-align:center;color:#94a3b8}
.esk-empty svg{opacity:.4;margin-bottom:10px}
@media (max-width:720px){.esk-fm-head{display:none}.esk-row{grid-template-columns:1fr auto;gap:6px}.esk-row .esk-col-size,.esk-row .esk-col-syn{display:none}}
CSS);
?>

<div class="pc-container">
  <div class="pc-content esk-fm">

    <div class="page-header">
      <div class="page-block">
        <div class="row align-items-center">
          <div class="col-md-12">
            <ul class="breadcrumb">
              <li class="breadcrumb-item"><a href="<?= Url::to(['/site/index-main']) ?>">Home</a></li>
              <?php if ($canSeeAll): ?>
                <li class="breadcrumb-item"><a href="<?= Url::to(['/storage/index']) ?>">EsakipStorage</a></li>
              <?php else: ?>
                <li class="breadcrumb-item">EsakipStorage</li>
              <?php endif; ?>
              <li class="breadcrumb-item" aria-current="page"><?= Html::encode($skpdModel->nama_skpd) ?></li>
            </ul>
          </div>
          <div class="col-md-12">
            <div class="page-header-title">
              <h2 class="mb-0">EsakipStorage</h2>
              <p class="mb-0 text-muted"><?= Html::encode($skpdModel->nama_skpd) ?></p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <?php foreach (Yii::$app->session->getAllFlashes() as $type => $msg): ?>
      <div class="alert alert-<?= $type === 'error' ? 'danger' : 'success' ?> alert-dismissible fade show" role="alert">
        <?= Html::encode(is_array($msg) ? implode(' ', $msg) : $msg) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    <?php endforeach; ?>

    <!-- Toolbar + breadcrumb path -->
    <div class="esk-fm-bar">
      <div class="esk-fm-path">
        <span class="esk-cloud" title="Folder NextCloud yang di-share ke KEMENPAN">&#9729; <?= Html::encode($ncBaseFolder) ?></span>
        <span class="sep">›</span>
        <a href="<?= Url::to(['/storage/index', 'skpd' => $skpdId]) ?>">&#128193; <?= Html::encode($ncSkpdFolder) ?></a>
        <?php foreach ($breadcrumb as $b): ?>
          <span class="sep">›</span>
          <a href="<?= Url::to(['/storage/index', 'skpd' => $skpdId, 'folder' => $b->id]) ?>"><?= Html::encode($b->name) ?></a>
        <?php endforeach; ?>
      </div>
      <div class="esk-fm-actions">
        <button type="button" class="esk-btn esk-btn-soft" data-bs-toggle="modal" data-bs-target="#mdlFolder">📁 Folder Baru</button>
        <button type="button" class="esk-btn esk-btn-primary" data-bs-toggle="modal" data-bs-target="#mdlUpload">⬆ Unggah PDF</button>
      </div>
    </div>

    <div class="esk-fm-note">
      Lokasi di NextCloud: <b><?= Html::encode($ncBaseFolder) ?> / <?= Html::encode($ncSkpdFolder) ?><?= $currentFolder ? ' / ' . Html::encode($currentFolder->rel_path) : '' ?></b>.
      Salinan di server eSakip dikompres otomatis untuk hemat ruang; versi di NextCloud tetap <b>asli</b>.
    </div>

    <!-- Listing -->
    <div class="esk-fm-card">
      <div class="esk-fm-head">
        <div>Nama</div>
        <div class="esk-col-syn">Status Sinkron</div>
        <div class="esk-col-size">Ukuran</div>
        <div style="text-align:right">Aksi</div>
      </div>

      <?php if (empty($children)): ?>
        <div class="esk-empty">
          <svg viewBox="0 0 24 24" width="60" height="60"><path fill="#94a3b8" d="M10 4H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2h-8l-2-2z"/></svg>
          <div>Folder ini masih kosong.</div>
          <div style="font-size:.85rem">Gunakan tombol <b>Unggah PDF</b> atau <b>Folder Baru</b> di atas.</div>
        </div>
      <?php else: ?>
        <?php foreach ($children as $it): ?>
          <div class="esk-row">
            <div class="esk-name">
              <?php if ($it->isFolder()): ?>
                <?= $icoFolder ?>
                <a href="<?= Url::to(['/storage/index', 'skpd' => $skpdId, 'folder' => $it->id]) ?>"><?= Html::encode($it->name) ?></a>
              <?php else: ?>
                <?= $icoPdf ?>
                <a href="<?= Url::to(['/storage/download', 'id' => $it->id]) ?>"><?= Html::encode($it->name) ?></a>
              <?php endif; ?>
            </div>
            <div class="esk-col-syn">
              <?= $badge($it->sync_status) ?>
              <?php if ($it->sync_status === StorageItem::SYNC_FAILED && $it->sync_message): ?>
                <span title="<?= Html::encode($it->sync_message) ?>" style="cursor:help;color:#b91c1c">ⓘ</span>
              <?php endif; ?>
            </div>
            <div class="esk-col-size"><?= $it->isFolder() ? '<span style="color:#94a3b8">folder</span>' : $fmtSize($it->size) ?></div>
            <div class="esk-cell-act">
              <?php if (!$it->isFolder()): ?>
                <a class="esk-ia" href="<?= Url::to(['/storage/download', 'id' => $it->id]) ?>" title="Unduh">⬇</a>
              <?php endif; ?>
              <?php if ($it->sync_status !== StorageItem::SYNC_SYNCED): ?>
                <?= Html::beginForm(['/storage/retry-sync', 'id' => $it->id], 'post', ['style' => 'display:inline']) ?>
                  <button class="esk-ia" type="submit" title="Coba sinkron ulang ke NextCloud">⟳</button>
                <?= Html::endForm() ?>
              <?php endif; ?>
              <?= Html::beginForm(['/storage/delete', 'id' => $it->id], 'post', ['style' => 'display:inline', 'onsubmit' => "return confirm('Hapus \"" . Html::encode(addslashes($it->name)) . "\"? Item juga dihapus di NextCloud (masuk Trash).');"]) ?>
                <button class="esk-ia esk-ia-text esk-ia-danger" type="submit" title="Hapus">🗑 Hapus</button>
              <?= Html::endForm() ?>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>

  </div>
</div>

<!-- Modal: Folder Baru -->
<div class="modal fade" id="mdlFolder" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="border-radius:16px">
      <form method="post" action="<?= Url::to(['/storage/create-folder']) ?>">
        <input type="hidden" name="<?= $csrfParam ?>" value="<?= $csrf ?>">
        <input type="hidden" name="skpd" value="<?= $skpdId ?>">
        <input type="hidden" name="parent" value="<?= $currentId ?>">
        <div class="modal-header" style="border:none"><h5 class="modal-title">📁 Folder Baru</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
          <label class="form-label">Nama Folder</label>
          <input type="text" name="name" class="form-control" required maxlength="255" placeholder="contoh: Laporan 2025">
        </div>
        <div class="modal-footer" style="border:none">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="esk-btn esk-btn-primary">Buat Folder</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal: Unggah PDF -->
<div class="modal fade" id="mdlUpload" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="border-radius:16px">
      <form method="post" action="<?= Url::to(['/storage/upload']) ?>" enctype="multipart/form-data">
        <input type="hidden" name="<?= $csrfParam ?>" value="<?= $csrf ?>">
        <input type="hidden" name="skpd" value="<?= $skpdId ?>">
        <input type="hidden" name="parent" value="<?= $currentId ?>">
        <div class="modal-header" style="border:none"><h5 class="modal-title">⬆ Unggah Dokumen PDF</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
          <label class="form-label">Pilih file PDF</label>
          <input type="file" name="file" class="form-control" accept=".pdf,application/pdf" required>
          <small class="text-muted d-block mt-2">Hanya <b>.pdf</b>, maksimal <b><?= round($maxSize / 1048576) ?> MB</b>. File disimpan di server eSakip <b>dan</b> disinkron ke NextCloud.</small>
        </div>
        <div class="modal-footer" style="border:none">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="esk-btn esk-btn-primary">Unggah</button>
        </div>
      </form>
    </div>
  </div>
</div>
