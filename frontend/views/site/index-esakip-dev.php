<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use frontend\models\SakipSkpd;
use frontend\models\SakipPeriode;
use frontend\models\SakipCascadingprogram;
use frontend\models\SakipCascadingkegiatan;
use frontend\models\SakipCascadingsubkegiatan;

/** @var yii\web\View $this */

$this->title = 'Dashboard eSakip (Admin)';

// SKPD terpilih (opsional) — dibaca lebih dulu agar daftar tahun bisa diskop
$selectedSkpd = Yii::$app->request->get('refskpd_id', '');

/*
 * Filter berbasis TAHUN -> PERIODE (refperiode_id), data dari tabel cascading.
 * Daftar tahun diambil dari periode yang benar-benar memiliki data (opsional
 * diskop ke SKPD terpilih) sehingga filter benar-benar mengubah angka.
 */
$baseFilter = !empty($selectedSkpd) ? ['refskpd_id' => $selectedSkpd] : [];

$pids = array_unique(array_merge(
    SakipCascadingprogram::find()->select('refperiode_id')->distinct()->where($baseFilter)->column(),
    SakipCascadingkegiatan::find()->select('refperiode_id')->distinct()->where($baseFilter)->column(),
    SakipCascadingsubkegiatan::find()->select('refperiode_id')->distinct()->where($baseFilter)->column()
));
$pids = array_filter($pids);

$periodeMap = []; // [tahun => refperiode_id]
if (!empty($pids)) {
    foreach (SakipPeriode::find()->where(['refperiode_id' => $pids])->orderBy(['periode' => SORT_DESC])->all() as $p) {
        $periodeMap[(int) $p->periode] = (int) $p->refperiode_id;
    }
}
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

$defaultYear = !empty($years) ? max(array_keys($years)) : (int) date('Y');
$selectedTahun = (int) Yii::$app->request->get('tahun', $defaultYear);
if (!isset($years[$selectedTahun])) {
    $selectedTahun = $defaultYear;
}
$selectedPeriodeId = $periodeMap[$selectedTahun] ?? null;

// Daftar SKPD
$skpdList = SakipSkpd::find()
    ->where(['skpd_isaktif' => 'T'])
    ->orderBy('nama_skpd ASC')
    ->all();
$skpdOptions = ArrayHelper::map($skpdList, 'refskpd_id', 'nama_skpd');

$totalSkpd = count($skpdList);
$totalProgram = 0;
$totalKegiatan = 0;
$totalSubKegiatan = 0;

if ($selectedPeriodeId) {
    try {
        $cond = ['refperiode_id' => $selectedPeriodeId];
        if (!empty($selectedSkpd)) {
            $cond['refskpd_id'] = $selectedSkpd;
        }
        $totalProgram = (int) SakipCascadingprogram::find()->where($cond)->count();
        $totalKegiatan = (int) SakipCascadingkegiatan::find()->where($cond)->count();
        $totalSubKegiatan = (int) SakipCascadingsubkegiatan::find()->where($cond)->count();
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
              <li class="breadcrumb-item"><a href="<?= Url::to(['/site/index-esakip-dev']) ?>">Home</a></li>
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
            <div class="card border-0 shadow-sm bg-light">
                <div class="card-body">
                    <?= Html::beginForm(['index-esakip-dev'], 'get', ['class' => 'd-flex align-items-end gap-3']); ?>
                    
                    <div class="form-group mb-0">
                        <?= Html::label('Pilih SKPD:', 'refskpd_id', ['class' => 'mb-1 fw-bold text-muted']); ?>
                        <?= Html::dropDownList('refskpd_id', $selectedSkpd, $skpdOptions, [
                            'class' => 'form-select',
                            'prompt' => '-- Semua SKPD --'
                        ]); ?>
                    </div>

                    <div class="form-group mb-0">
                        <?= Html::label('Pilih Tahun:', 'tahun', ['class' => 'mb-1 fw-bold text-muted']); ?>
                        <?= Html::dropDownList('tahun', $selectedTahun, $years, [
                            'class' => 'form-select',
                        ]); ?>
                    </div>

                    <div class="form-group mb-0">
                        <button type="submit" class="btn btn-primary">
                            <i class="ph-duotone ph-funnel"></i> Terapkan Filter
                        </button>
                    </div>

                    <?= Html::endForm(); ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row">
        <?php if (empty($selectedSkpd)): ?>
        <!-- Card 0: Total SKPD (Hanya muncul jika filter 'Semua SKPD') -->
        <div class="col-md-3 mb-3">
            <div class="card text-center border-0 shadow-sm" style="background:linear-gradient(135deg,#f3e5f5,#e1bee7);">
                <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="text-muted mb-0">Total SKPD Aktif</h6>
                    <i class="ph-duotone ph-buildings text-purple" style="font-size: 24px; color: #8e24aa;"></i>
                </div>
                <h3 class="mb-0 text-start" style="color:#8e24aa"><?= $totalSkpd ?></h3>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Card 1: Total Program -->
        <div class="col-md-<?= empty($selectedSkpd) ? '3' : '4' ?> mb-3">
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
        <div class="col-md-<?= empty($selectedSkpd) ? '3' : '4' ?> mb-3">
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
        <div class="col-md-<?= empty($selectedSkpd) ? '3' : '4' ?> mb-3">
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
        <?php if (!empty($selectedSkpd) && isset($skpdOptions[$selectedSkpd])): ?>
            <p class="badge bg-secondary">SKPD: <?= Html::encode($skpdOptions[$selectedSkpd]) ?></p>
        <?php else: ?>
            <p class="badge bg-secondary">Menampilkan Semua SKPD</p>
        <?php endif; ?>
      </div>
    </div>

  </div>
</div>