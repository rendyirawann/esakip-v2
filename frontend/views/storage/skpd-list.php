<?php

use yii\helpers\Url;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var \frontend\models\SakipSkpd[] $allSkpd */
/** @var array $counts  indexed by refskpd_id => ['c' => jumlah file] */
/** @var string $ncBaseFolder */

$this->title = 'EsakipStorage — Daftar SKPD';

$this->registerCss(<<<'CSS'
.esk-fm{font-family:"Plus Jakarta Sans",sans-serif}
.esk-fm-intro{background:linear-gradient(135deg,#0d9488,#0f766e);color:#fff;border-radius:16px;padding:22px 26px;margin-bottom:22px}
.esk-fm-intro h3{font-weight:800;margin:0 0 4px}
.esk-fm-intro p{margin:0;opacity:.9;font-size:.9rem}
.esk-skpd-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:16px}
.esk-skpd-card{display:flex;align-items:center;gap:14px;background:#fff;border:1px solid #eef0f3;border-radius:14px;padding:16px 18px;text-decoration:none;transition:transform .12s,box-shadow .12s;box-shadow:0 2px 10px rgba(16,30,54,.04)}
.esk-skpd-card:hover{transform:translateY(-3px);box-shadow:0 10px 24px rgba(13,148,136,.14);border-color:#99f6e4}
.esk-skpd-ico{flex:0 0 auto}
.esk-skpd-meta{min-width:0}
.esk-skpd-meta .nm{font-weight:700;color:#1e293b;display:block;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.esk-skpd-meta .sub{font-size:.78rem;color:#94a3b8}
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
              <li class="breadcrumb-item" aria-current="page">EsakipStorage</li>
            </ul>
          </div>
        </div>
      </div>
    </div>

    <div class="esk-fm-intro">
      <h3>📂 EsakipStorage</h3>
      <p>Pilih SKPD untuk membuka, mengunggah, dan menyinkronkan dokumen SAKIP. Tersinkron ke NextCloud: <b>&#9729; <?= Html::encode($ncBaseFolder) ?></b></p>
    </div>

    <?php if (empty($allSkpd)): ?>
      <div class="alert alert-info">Belum ada SKPD aktif.</div>
    <?php else: ?>
      <div class="esk-skpd-grid">
        <?php foreach ($allSkpd as $s): ?>
          <?php $c = isset($counts[$s->refskpd_id]) ? (int) $counts[$s->refskpd_id]['c'] : 0; ?>
          <a class="esk-skpd-card" href="<?= Url::to(['/storage/index', 'skpd' => $s->refskpd_id]) ?>">
            <span class="esk-skpd-ico">
              <svg viewBox="0 0 24 24" width="40" height="40"><path fill="#3b82f6" d="M10 4H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2h-8l-2-2z"/></svg>
            </span>
            <span class="esk-skpd-meta">
              <span class="nm"><?= Html::encode($s->nama_skpd) ?></span>
              <span class="sub"><?= $c ?> dokumen</span>
            </span>
          </a>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

  </div>
</div>
