<?php
use yii\helpers\Url;
use yii\helpers\Html;
use frontend\models\SakipPeriode;
use frontend\models\SakipCascadingprogram;
use frontend\models\SakipCascadingkegiatan;
use frontend\models\SakipCascadingsubkegiatan;

/** @var yii\web\View $this */

$this->title = 'Dashboard eSakip';

$user = Yii::$app->user->identity;
$refskpd_id = $user->refskpd_id ?? null;

/*
 * Filter berbasis TAHUN yang dipetakan ke PERIODE (refperiode_id).
 * Sumber data memakai tabel cascading (Program/Kegiatan/Sub Kegiatan) yang
 * memang dimiliki tiap SKPD & dikunci per periode — sehingga filter benar-benar
 * mengubah angka. Daftar tahun diambil dari periode yang punya data untuk SKPD ini.
 */
$periodeMap = []; // [tahun => refperiode_id]
if ($refskpd_id) {
    $pids = array_unique(array_merge(
        SakipCascadingprogram::find()->select('refperiode_id')->distinct()->where(['refskpd_id' => $refskpd_id])->column(),
        SakipCascadingkegiatan::find()->select('refperiode_id')->distinct()->where(['refskpd_id' => $refskpd_id])->column(),
        SakipCascadingsubkegiatan::find()->select('refperiode_id')->distinct()->where(['refskpd_id' => $refskpd_id])->column()
    ));
    $pids = array_filter($pids);
    if (!empty($pids)) {
        foreach (SakipPeriode::find()->where(['refperiode_id' => $pids])->orderBy(['periode' => SORT_DESC])->all() as $p) {
            $periodeMap[(int) $p->periode] = (int) $p->refperiode_id;
        }
    }
}
// Fallback: jika belum ada data, tampilkan seluruh periode yang tersedia
if (empty($periodeMap)) {
    foreach (SakipPeriode::find()->orderBy(['periode' => SORT_DESC])->all() as $p) {
        $periodeMap[(int) $p->periode] = (int) $p->refperiode_id;
    }
}

$years = [];
foreach (array_keys($periodeMap) as $y) {
    $years[$y] = $y;
}
krsort($years);

// Default = tahun TERBARU yang memiliki data; tetap valid bila tahun diminta tak tersedia
$defaultYear = !empty($years) ? max(array_keys($years)) : (int) date('Y');
$selectedTahun = (int) Yii::$app->request->get('tahun', $defaultYear);
if (!isset($years[$selectedTahun])) {
    $selectedTahun = $defaultYear;
}
$selectedPeriodeId = $periodeMap[$selectedTahun] ?? null;

$totalProgram = 0;
$totalKegiatan = 0;
$totalSubKegiatan = 0;

if ($refskpd_id && $selectedPeriodeId) {
    try {
        $totalProgram = (int) SakipCascadingprogram::find()->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $selectedPeriodeId])->count();
        $totalKegiatan = (int) SakipCascadingkegiatan::find()->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $selectedPeriodeId])->count();
        $totalSubKegiatan = (int) SakipCascadingsubkegiatan::find()->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $selectedPeriodeId])->count();
    } catch (\Exception $e) {
        // Fallback
    }
}
?>

<div class="pc-container">
  <div class="pc-content">
    <div class="page-header">
      <div class="page-block">
        <div class="row align-items-center">
          <div class="col-md-12">
            <ul class="breadcrumb">
              <li class="breadcrumb-item"><a href="<?= Url::to(['/site/index-esakip']) ?>">Home</a></li>
              <li class="breadcrumb-item" aria-current="page">Dashboard</li>
            </ul>
          </div>
          <div class="col-md-12">
            <div class="page-header-title">
              <h2 class="mb-0">Dashboard</h2>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Filter Form -->
    <div class="row mb-4">
        <div class="col-sm-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <?= Html::beginForm(['index-esakip'], 'get', ['class' => 'form-inline d-flex align-items-center']); ?>
                    <div class="form-group mb-0 me-3">
                        <?= Html::label('Pilih Tahun:', 'tahun', ['class' => 'me-2 fw-bold']); ?>
                        <?= Html::dropDownList('tahun', $selectedTahun, $years, [
                            'class' => 'form-select w-auto d-inline-block',
                            'onchange' => 'this.form.submit()'
                        ]); ?>
                    </div>
                    <?= Html::endForm(); ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row">
        <!-- Card 1: Total Program -->
        <div class="col-md-4 mb-3">
            <div class="card text-center border-0 shadow-sm" style="background:linear-gradient(135deg,#e3f2fd,#bbdefb);">
                <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="text-muted mb-0">Total Program</h6>
                    <i class="ph-duotone ph-briefcase text-primary" style="font-size: 24px;"></i>
                </div>
                <h3 class="mb-0 text-primary text-start"><?= $totalProgram ?></h3>
                </div>
            </div>
        </div>

        <!-- Card 2: Total Kegiatan -->
        <div class="col-md-4 mb-3">
            <div class="card text-center border-0 shadow-sm" style="background:linear-gradient(135deg,#fff3e0,#ffe0b2);">
                <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="text-muted mb-0">Total Kegiatan</h6>
                    <i class="ph-duotone ph-list-dashes text-warning" style="font-size: 24px; color: #e65100;"></i>
                </div>
                <h3 class="mb-0 text-start" style="color:#e65100"><?= $totalKegiatan ?></h3>
                </div>
            </div>
        </div>

        <!-- Card 3: Total Sub Kegiatan -->
        <div class="col-md-4 mb-3">
            <div class="card text-center border-0 shadow-sm" style="background:linear-gradient(135deg,#e8f5e9,#c8e6c9);">
                <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="text-muted mb-0">Total Sub Kegiatan</h6>
                    <i class="ph-duotone ph-check-circle text-success" style="font-size: 24px;"></i>
                </div>
                <h3 class="mb-0 text-success text-start"><?= $totalSubKegiatan ?></h3>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-5">
      <div class="col-lg-12 text-center text-muted">
        <img src="<?= Url::base(true) ?>/lightapp/assets/images/bappeda.png" alt="Logo" width="100" class="mb-3 opacity-50">
        <h4>Sistem eSakip - Deli Serdang</h4>
        <p>Menampilkan data perencanaan (cascading) Tahun <?= Html::encode($selectedTahun) ?>.</p>
      </div>
    </div>

  </div>
</div>