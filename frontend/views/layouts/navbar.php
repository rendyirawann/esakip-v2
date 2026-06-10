<style>
  .bg-navy {
    background-color: #000080 !important;
  }

  /* Hover dropdown-style detail card */
  .esakip-detail-card {
    display: none;
    position: absolute;
    top: 100%;
    left: 0;
    z-index: 1000;
    background-color: #ffffff;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    padding: 1rem;
    width: 500px;
    /* Ubah sesuai kebutuhan */
    border-radius: 8px;
    transition: opacity 0.3s ease;
  }

  .esakip-detail-card h6 {
    font-size: 1rem;
    margin-bottom: 0.75rem;
  }

  .esakip-detail-card ul {
    list-style: none;
    padding-left: 0;
    margin: 0;
    display: flex;
    flex-direction: column;
    /* Pastikan list ditumpuk vertikal */
    gap: 4px;
  }

  .esakip-detail-card li {
    padding: 6px 0;
    border-bottom: 1px solid #f0f0f0;
    font-size: 0.95rem;
  }

  .esakip-detail-card li:last-child {
    border-bottom: none;
  }

  .progress-section:hover .esakip-detail-card,
  .progress-section:focus-within .esakip-detail-card {
    display: block;
  }
</style>

<?php

/** @var \yii\web\View $this */
/** @var string $content */

use frontend\assets\AppAsset;
use frontend\models\User;
use common\widgets\Alert;
use yii\bootstrap5\Breadcrumbs;
use yii\bootstrap5\Html;
use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;
use yii\helpers\Url;
use mdm\admin\components\MenuHelper;

AppAsset::register($this);

// Define the current user's refskpd_id
$refskpd_id = Yii::$app->user->identity->refskpd_id;

// Get refperiode_id from URL or default to current year
$refperiode_id = Yii::$app->request->get('refperiode_id');
if ($refperiode_id === null) {
  $currentYear = date('Y');
  $defaultPeriod = \frontend\models\SakipPeriode::find()->where(['periode' => $currentYear])->one();
  $refperiode_id = $defaultPeriod ? $defaultPeriod->refperiode_id : null;
}

// Count entries in each table for the given refskpd_id
$selectedPeriodForCount = \frontend\models\SakipPeriode::find()->where(['refperiode_id' => $refperiode_id])->one();
$refperiode_5tahun_id = $selectedPeriodForCount ? $selectedPeriodForCount->refperiode_5tahun_id : null;

$countSasaran = \frontend\models\SakipSasaranrenstra::find()->where(['refskpd_id' => $refskpd_id, 'refperiode_5tahun_id' => $refperiode_5tahun_id])->count();
$countIndikatorSasaran = \frontend\models\SakipIndikatorsasaranrenstra::find()->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])->count();
$countTujuan = \frontend\models\SakipTujuanrenstra::find()->where(['refskpd_id' => $refskpd_id, 'refperiode_5tahun_id' => $refperiode_5tahun_id])->count();
$countIndikatorTujuan = \frontend\models\SakipIndikatortujuanrenstra::find()->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])->count();
$countStrategi = \frontend\models\SakipStrategi::find()->where(['refskpd_id' => $refskpd_id, 'refperiode_5tahun_id' => $refperiode_5tahun_id])->count();
$countKebijakan = \frontend\models\SakipKebijakan::find()->where(['refskpd_id' => $refskpd_id, 'refperiode_5tahun_id' => $refperiode_5tahun_id])->count();
$countCascadingProgram = \frontend\models\SakipCascadingprogram::find()->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])->count();
$countCascadingKegiatan = \frontend\models\SakipCascadingkegiatan::find()->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])->count();
$countCascadingSubkegiatan = \frontend\models\SakipCascadingsubkegiatan::find()->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])->count();
$countIndikatorCascadingProgram = \frontend\models\SakipIndikatorcascadingprogram::find()->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])->count();
$countIndikatorCascadingKegiatan = \frontend\models\SakipIndikatorcascadingkegiatan::find()->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])->count();
$countIndikatorCascadingSubkegiatan = \frontend\models\SakipIndikatorcascadingsubkegiatan::find()->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])->count();
$countIndikatorSasaranTriwulan = \frontend\models\SakipIndikatorsasaranrenstraTriwulan::find()->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])->count();
$countIndikatorCascadingProgramTriwulan = \frontend\models\SakipIndikatorcascadingprogramTriwulan::find()->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])->count();
$countIndikatorCascadingKegiatanTriwulan = \frontend\models\SakipIndikatorcascadingkegiatanTriwulan::find()->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])->count();
$countIndikatorCascadingSubkegiatanTriwulan = \frontend\models\SakipIndikatorcascadingsubkegiatanTriwulan::find()->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])->count();

// Check for unfilled target fields in indicators
$indicatorsToCheck = [
  'sasaran' => \frontend\models\SakipIndikatorsasaranrenstra::find()->where(['refskpd_id' => $refskpd_id])
    ->andWhere([
      'or',
      ['indikatorsasaranrenstra_target' => null],
      ['target_rkt' => null],
      ['target_pk' => null],
      ['target_pk_p' => null],
      ['realisasi' => null],
      ['capaian' => null]
    ])
    ->andWhere(['refperiode_id' => $refperiode_id])
    ->count(),

  'cascading_program' => \frontend\models\SakipIndikatorcascadingprogram::find()->where(['refskpd_id' => $refskpd_id])
    ->andWhere([
      'or',
      ['target_rkt' => null],
      ['target_pk' => null],
      ['target_pk_p' => null],
      ['realisasi' => null],
      ['capaian' => null]
    ])
    ->andWhere(['refperiode_id' => $refperiode_id])
    ->count(),

  'cascading_kegiatan' => \frontend\models\SakipIndikatorcascadingkegiatan::find()->where(['refskpd_id' => $refskpd_id])
    ->andWhere([
      'or',
      ['target_rkt' => null],
      ['target_pk' => null],
      ['target_pk_p' => null],
      ['realisasi' => null],
      ['capaian' => null]
    ])
    ->andWhere(['refperiode_id' => $refperiode_id])
    ->count(),

  'cascading_subkegiatan' => \frontend\models\SakipIndikatorcascadingsubkegiatan::find()->where(['refskpd_id' => $refskpd_id])
    ->andWhere([
      'or',
      ['target_rkt' => null],
      ['target_pk' => null],
      ['target_pk_p' => null],
      ['realisasi' => null],
      ['capaian' => null]
    ])
    ->andWhere(['refperiode_id' => $refperiode_id])
    ->count(),
];

// Total number of entries needed for 100% completion
$totalRequirements = 16; // Adjust this based on the total number of tables being checked

// Calculate completed entries
$completedEntries = 0;
if ($countSasaran > 0) $completedEntries++;
if ($countIndikatorSasaran > 0) $completedEntries++;
if ($countTujuan > 0) $completedEntries++;
if ($countIndikatorTujuan > 0) $completedEntries++;
if ($countStrategi > 0) $completedEntries++;
if ($countKebijakan > 0) $completedEntries++;
if ($countCascadingProgram > 0) $completedEntries++;
if ($countCascadingKegiatan > 0) $completedEntries++;
if ($countCascadingSubkegiatan > 0) $completedEntries++;
if ($countIndikatorCascadingProgram > 0) $completedEntries++;
if ($countIndikatorCascadingKegiatan > 0) $completedEntries++;
if ($countIndikatorCascadingSubkegiatan > 0) $completedEntries++;
if ($countIndikatorSasaranTriwulan > 0) $completedEntries++;
if ($countIndikatorCascadingProgramTriwulan > 0) $completedEntries++;
if ($countIndikatorCascadingKegiatanTriwulan > 0) $completedEntries++;
if ($countIndikatorCascadingSubkegiatanTriwulan > 0) $completedEntries++;

// Calculate the progress percentage
$progressPercentage = ($completedEntries / $totalRequirements) * 100;

// Determine the color class based on progress percentage
if ($progressPercentage == 100) {
  $progressBarClass = 'bg-navy';
} elseif ($progressPercentage >= 76) {
  $progressBarClass = 'bg-success'; // Green
} elseif ($progressPercentage >= 51) {
  $progressBarClass = 'bg-warning'; // Yellow
} elseif ($progressPercentage >= 26) {
  $progressBarClass = 'bg-orange'; // Orange
} else {
  $progressBarClass = 'bg-danger'; // Red
}

$currentRoute = Yii::$app->controller->getRoute();
$currentUrl = Yii::$app->request->url;

// Menentukan route yang termasuk dalam menu utama RENSTRA
$renstraRoutes = [
  'sakip-visi/index',
  'sakip-sasaranrenstra/index',
  'sakip-tujuanrenstra/index',
  'sakip-sasaran-pakdhe/index',
  'sakip-tujuan-pakdhe/index',
  'sakip-indikatortujuanrenstra/index',
  'sakip-indikatorsasaranrenstra/index-formulasi',
  'sakip-strategi/index',
  'sakip-kebijakan/index',
  'sakip-cascadingprogram/index',
  'sakip-cascadingkegiatan/index',
  'sakip-cascadingsubkegiatan/index'
];

$isRenstraActive = in_array($currentRoute, $renstraRoutes);

// Khusus untuk submenu "Sasaran-Tujuan"
$sasaranTujuanRoutes = [
  'sakip-sasaranrenstra/index',
  'sakip-tujuanrenstra/index',
  'sakip-indikatortujuanrenstra/index',
  'sakip-indikatorsasaranrenstra/index-formulasi'
];
$isSasaranTujuanActive = in_array($currentRoute, $sasaranTujuanRoutes);

// Menentukan route yang termasuk dalam menu utama RKT
$rktRoutes = [
  'sakip-indikatorsasaranrenstra/index',
  'sakip-indikatorcascadingprogram/index',
  'sakip-indikatorcascadingkegiatan/index',
  'sakip-indikatorcascadingsubkegiatan/index',
  'sakip-indikatorcascadingprogram/index-anggaran-rkt',
  'sakip-indikatorcascadingkegiatan/index-anggaran-rkt',
  'sakip-indikatorcascadingsubkegiatan/index-anggaran-rkt',
];

$isRktActive = in_array($currentRoute, $rktRoutes);

// Menentukan route yang termasuk dalam menu utama PK
$pkRoutes = [
  'sakip-indikatorsasaranrenstra/index-tahunan-pk',
  'sakip-indikatorsasaranrenstra-triwulan/index-triwulan-pk',
  'sakip-indikatorcascadingprogram/index-tahunan-pk',
  'sakip-indikatorcascadingprogram-triwulan/index-triwulan-pk',
  'sakip-indikatorcascadingkegiatan/index-tahunan-pk',
  'sakip-indikatorcascadingkegiatan-triwulan/index-triwulan-pk',
  'sakip-indikatorcascadingsubkegiatan/index-tahunan-pk',
  'sakip-indikatorcascadingsubkegiatan-triwulan/index-triwulan-pk',
  'sakip-indikatorcascadingprogram/index-anggaran-pk',
  'sakip-indikatorcascadingkegiatan/index-anggaran-pk',
  'sakip-indikatorcascadingsubkegiatan/index-anggaran-pk',
];

$isPkActive = in_array($currentRoute, $pkRoutes);

// Menentukan route yang termasuk dalam menu utama Sasaran Triwulan dan Tahunan
$pkSasaranRoutes = [
  'sakip-indikatorsasaranrenstra/index-tahunan-pk',
  'sakip-indikatorsasaranrenstra-triwulan/index-triwulan-pk',
];

$isPkSasaranActive = in_array($currentRoute, $pkSasaranRoutes);

// Menentukan route yang termasuk dalam menu utama Program Triwulan dan Tahunan
$pkProgramRoutes = [
  'sakip-indikatorcascadingprogram/index-tahunan-pk',
  'sakip-indikatorcascadingprogram-triwulan/index-triwulan-pk',
];

$isPkProgramActive = in_array($currentRoute, $pkProgramRoutes);

// Menentukan route yang termasuk dalam menu utama Kegiatan Triwulan dan Tahunan
$pkKegiatanRoutes = [
  'sakip-indikatorcascadingkegiatan/index-tahunan-pk',
  'sakip-indikatorcascadingkegiatan-triwulan/index-triwulan-pk',
];

$isPkKegiatanActive = in_array($currentRoute, $pkKegiatanRoutes);

// Menentukan route yang termasuk dalam menu utama Sub Kegiatan Triwulan dan Tahunan
$pkSubkegiatanRoutes = [
  'sakip-indikatorcascadingsubkegiatan/index-tahunan-pk',
  'sakip-indikatorcascadingsubkegiatan-triwulan/index-triwulan-pk',
];

$isPkSubkegiatanActive = in_array($currentRoute, $pkSubkegiatanRoutes);

// Menentukan route yang termasuk dalam menu utama PK Perubahan
$pkpRoutes = [
  'sakip-indikatorsasaranrenstra/index-tahunan-pkp',
  'sakip-indikatorsasaranrenstra-triwulan/index-triwulan-pkp',
  'sakip-indikatorcascadingprogram/index-tahunan-pkp',
  'sakip-indikatorcascadingprogram-triwulan/index-triwulan-pkp',
  'sakip-indikatorcascadingkegiatan/index-tahunan-pkp',
  'sakip-indikatorcascadingkegiatan-triwulan/index-triwulan-pkp',
  'sakip-indikatorcascadingsubkegiatan/index-tahunan-pkp',
  'sakip-indikatorcascadingsubkegiatan-triwulan/index-triwulan-pkp',
  'sakip-indikatorcascadingprogram/index-anggaran-pkp',
  'sakip-indikatorcascadingkegiatan/index-anggaran-pkp',
  'sakip-indikatorcascadingsubkegiatan/index-anggaran-pkp',
];

$isPkpActive = in_array($currentRoute, $pkpRoutes);

// Menentukan route yang termasuk dalam menu utama Sasaran Triwulan dan Tahunan
$pkpSasaranRoutes = [
  'sakip-indikatorsasaranrenstra/index-tahunan-pkp',
  'sakip-indikatorsasaranrenstra-triwulan/index-triwulan-pkp',
];

$isPkpSasaranActive = in_array($currentRoute, $pkpSasaranRoutes);

// Menentukan route yang termasuk dalam menu utama Program Triwulan dan Tahunan
$pkpProgramRoutes = [
  'sakip-indikatorcascadingprogram/index-tahunan-pkp',
  'sakip-indikatorcascadingprogram-triwulan/index-triwulan-pkp',
];

$isPkpProgramActive = in_array($currentRoute, $pkpProgramRoutes);

// Menentukan route yang termasuk dalam menu utama Kegiatan Triwulan dan Tahunan
$pkpKegiatanRoutes = [
  'sakip-indikatorcascadingkegiatan/index-tahunan-pkp',
  'sakip-indikatorcascadingkegiatan-triwulan/index-triwulan-pkp',
];

$isPkpKegiatanActive = in_array($currentRoute, $pkpKegiatanRoutes);

// Menentukan route yang termasuk dalam menu utama Sub Kegiatan Triwulan dan Tahunan
$pkpSubkegiatanRoutes = [
  'sakip-indikatorcascadingsubkegiatan/index-tahunan-pkp',
  'sakip-indikatorcascadingsubkegiatan-triwulan/index-triwulan-pkp',
];

$isPkpSubkegiatanActive = in_array($currentRoute, $pkpSubkegiatanRoutes);

// Menentukan route yang termasuk dalam menu utama Capaian Kinerja
$capkinRoutes = [
  'sakip-indikatorsasaranrenstra-triwulan/index-triwulan-capaian',
  'sakip-indikatorsasaranrenstra/index-tahunan-capaian',
  'sakip-indikatorcascadingprogram-triwulan/index-triwulan-capaian',
  'sakip-indikatorcascadingprogram/index-tahunan-capaian',
  'sakip-indikatorcascadingkegiatan-triwulan/index-triwulan-capaian',
  'sakip-indikatorcascadingkegiatan/index-tahunan-capaian',
  'sakip-indikatorcascadingsubkegiatan-triwulan/index-triwulan-capaian',
  'sakip-indikatorcascadingsubkegiatan/index-tahunan-capaian',
  'sakip-indikatorcascadingsubkegiatan-triwulan/index-triwulan-penyerapan',
];

$isCapkinActive = in_array($currentRoute, $capkinRoutes);

// Menentukan route yang termasuk dalam menu utama Sasaran Triwulan dan Tahunan
$capkinSasaranRoutes = [
  'sakip-indikatorsasaranrenstra-triwulan/index-triwulan-capaian',
  'sakip-indikatorsasaranrenstra/index-tahunan-capaian',
];

$isCapkinSasaranActive = in_array($currentRoute, $capkinSasaranRoutes);

// Menentukan route yang termasuk dalam menu utama Program Triwulan dan Tahunan
$capkinProgramRoutes = [
  'sakip-indikatorcascadingprogram-triwulan/index-triwulan-capaian',
  'sakip-indikatorcascadingprogram/index-tahunan-capaian',
];

$isCapkinProgramActive = in_array($currentRoute, $capkinProgramRoutes);

// Menentukan route yang termasuk dalam menu utama Kegiatan Triwulan dan Tahunan
$capkinKegiatanRoutes = [
  'sakip-indikatorcascadingkegiatan-triwulan/index-triwulan-capaian',
  'sakip-indikatorcascadingkegiatan/index-tahunan-capaian',
];

$isCapkinKegiatanActive = in_array($currentRoute, $capkinKegiatanRoutes);

// Menentukan route yang termasuk dalam menu utama Sub Kegiatan Triwulan dan Tahunan
$capkinSubkegiatanRoutes = [
  'sakip-indikatorcascadingsubkegiatan-triwulan/index-triwulan-capaian',
  'sakip-indikatorcascadingsubkegiatan/index-tahunan-capaian',
];

$isCapkinSubkegiatanActive = in_array($currentRoute, $capkinSubkegiatanRoutes);

// Menentukan route yang termasuk dalam menu utama Laporan
$laporanRoutes = [
  'laporan/index-laporan-renstra',
  'laporan/index-laporan-renja-tahunan',
  'laporan/index-laporan-iku',
  'laporan/index-laporan-capkin-iku',
  'laporan/index-laporan-tapkin',
  'laporan/index-laporan-realisasi-anggaran',
  'laporan/index-laporan-analisis-sasaran-triwulan',
  'laporan/index-laporan-analisis-sasaran-tahunan',
  'laporan/index-laporan-rencana-aksi',
  'laporan/index-laporan-ekinerja',
  'laporan/index-evaluasi-rkpd',
  'sakip-evaluasi-renja/index',
];

$isLaporanActive = in_array($currentRoute, $laporanRoutes);

// Menentukan route yang termasuk dalam menu utama Sasaran Triwulan dan Tahunan
$laporanAnalisisRoutes = [
  'laporan/index-laporan-analisis-sasaran-triwulan',
  'laporan/index-laporan-analisis-sasaran-tahunan',
];

$isLaporanAnalisisActive = in_array($currentRoute, $laporanAnalisisRoutes);

$js = <<<JS
document.addEventListener("DOMContentLoaded", function() {
    const activeDropdown = document.querySelector('.pc-hasmenu.active');
    if (activeDropdown) {
        activeDropdown.classList.add('show');
        const submenu = activeDropdown.querySelector('.pc-submenu');
        if (submenu) submenu.classList.add('show');
    }
});
JS;
$this->registerJs($js);

?>
<!-- [ Pre-loader ] start -->
<div class="loader-bg">
  <div class="loader-track">
    <div class="loader-fill"></div>
  </div>
</div>
<!-- [ Pre-loader ] End -->
<!-- [ Sidebar Menu ] start -->
<nav class="pc-sidebar" style="font-size:small;">
  <div class="navbar-wrapper">
    <div class="m-header">
      <a href="<?= Url::to(['/site/index-esakip']) ?>" class="b-brand text-primary">
        <!-- ========   Change your logo from here   ============ -->
        <h5><img src="<?= Url::base(true) ?>/lightapp/assets/images/bappeda.png" alt="logo image" height="60" width="60" class="logo-lg" />eSakip<span class="badge bg-brand-color-2 rounded-pill ms-2 theme-version">v2.0</span></h5>

      </a>
    </div>
    <div class="navbar-content">
      <ul class="pc-navbar">
        <li class="pc-item pc-caption">
          <label>Menu Navigasi</label>
        </li>
        <li class="pc-item pc-hasmenu <?= $currentRoute == 'site/index-esakip' ? 'active' : '' ?>">
          <a href="<?= Url::to(['/site/index-esakip']) ?>" class="pc-link">
            <span class="pc-micon">
              <i class="ph-duotone ph-globe-simple"></i>
            </span>
            <span class="pc-mtext">Dashboard</span>
            <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
            <!-- <span class="pc-badge">2</span> -->
          </a>
        </li>
        <!-- <li class="pc-item pc-hasmenu">
          <a href="#!" class="pc-link"
            ><span class="pc-micon"> <i class="ph-duotone ph-layout"></i></span><span class="pc-mtext">Layouts</span
            ><span class="pc-arrow"><i data-feather="chevron-right"></i></span
          ></a>
          <ul class="pc-submenu">
            <li class="pc-item"><a class="pc-link" href="../demo/layout-compact.html">Compact</a></li>
            <li class="pc-item"><a class="pc-link" href="../demo/layout-horizontal.html">Horizontal</a></li>
            <li class="pc-item"><a class="pc-link" href="../demo/layout-2.html">Creative</a></li>
            <li class="pc-item"><a class="pc-link" href="../demo/layout-tab.html">Tab</a></li>
            <li class="pc-item"><a class="pc-link" href="../demo/layout-vertical.html">Vertical</a></li>
          </ul>
        </li> -->
        <?php
        // Jika superadmin/admin, tampilkan semua data
        if (isset($assignments['superadmin']) || isset($assignments['admin'])) {
        ?>
          <li class="pc-item pc-hasmenu">
            <a href="<?= Url::to(['/sakip-lke/index']) ?>" class="pc-link">
              <span class="pc-micon">
                <i class="ph-duotone ph-file"></i>
              </span>
              <span class="pc-mtext">LKE</span>
              <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
              <!-- <span class="pc-badge">2</span> -->
            </a>
          </li>
          <li class="pc-item pc-hasmenu">
            <a href="<?= Url::to(['/buku-laporan/index']) ?>" class="pc-link">
              <span class="pc-micon">
                <i class="ph-duotone ph-file"></i>
              </span>
              <span class="pc-mtext">Buku Laporan</span>
              <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
              <!-- <span class="pc-badge">2</span> -->
            </a>
          </li>
        <?php } ?>
        <li class="pc-item pc-caption">
          <label>Sidebar</label>
          <i class="ph-duotone ph-chart-pie"></i>
        </li>

        <li class="pc-item pc-hasmenu <?= $isRenstraActive ? 'active show' : '' ?>">

          <a href="#!" class="pc-link <?= $isRenstraActive ? 'active' : '' ?>" <?= $isRenstraActive ? 'aria-expanded="true"' : '' ?>>

            <span class="pc-micon">
              <i class="ph-duotone ph-suitcase"></i>
            </span>
            <span class="pc-mtext">RENSTRA</span><span class="pc-arrow"><i data-feather="chevron-right"></i></span></a>
          <ul class="pc-submenu <?= $isRenstraActive ? 'show' : '' ?>">

            <li class="pc-item <?= $currentRoute == 'sakip-visi/index' ? 'active' : '' ?>"><a class="pc-link" href="<?= Url::to(['/sakip-visi/index']) ?>">Data SKPD</a></li>
            <li class="pc-item pc-hasmenu <?= $isSasaranTujuanActive ? 'active show' : '' ?>">
              <a href="#!" class="pc-link <?= $isSasaranTujuanActive ? 'active' : '' ?>" <?= $isSasaranTujuanActive ? 'aria-expanded="true"' : '' ?>>
                Sasaran-Tujuan<span class="pc-arrow"><i data-feather="chevron-right"></i></span>
              </a>
              <ul class="pc-submenu">
                <li class="pc-item <?= $currentRoute == 'sakip-sasaranrenstra/index' ? 'active' : '' ?>"><a class="pc-link" href="<?= Url::to(['/sakip-sasaranrenstra/index']) ?>">Sasaran Renstra</a></li>
                <li class="pc-item <?= $currentRoute == 'sakip-tujuanrenstra/index' ? 'active' : '' ?>"><a class="pc-link" href="<?= Url::to(['/sakip-tujuanrenstra/index']) ?>">Tujuan Renstra</a></li>
                <li class="pc-item <?= $currentRoute == 'sakip-indikatortujuanrenstra/index' ? 'active' : '' ?>"><a class="pc-link" href="<?= Url::to(['/sakip-indikatortujuanrenstra/index']) ?>">Indikator Tujuan Renstra</a></li>
                <li class="pc-item <?= $currentRoute == 'sakip-indikatorsasaranrenstra/index-formulasi' ? 'active' : '' ?>"><a class="pc-link" href="<?= Url::to(['/sakip-indikatorsasaranrenstra/index-formulasi']) ?>">Formulasi Renstra</a></li>
                <li class="pc-item <?= $currentRoute == 'sakip-sasaran-pakdhe/index' ? 'active' : '' ?>"><a class="pc-link" href="<?= Url::to(['/sakip-sasaran-pakdhe/index']) ?>">Sasaran Renstra (Integrasi Pakdhe)</a></li>
                <li class="pc-item <?= $currentRoute == 'sakip-tujuan-pakdhe/index' ? 'active' : '' ?>"><a class="pc-link" href="<?= Url::to(['/sakip-tujuan-pakdhe/index']) ?>">Tujuan Renstra (Integrasi Pakdhe)</a></li>
              </ul>
            </li>

            <li class="pc-item <?= $currentRoute == 'sakip-strategi/index' ? 'active' : '' ?>"><a class="pc-link" href="<?= Url::to(['/sakip-strategi/index']) ?>">Strategi</a></li>
            <li class="pc-item <?= $currentRoute == 'sakip-kebijakan/index' ? 'active' : '' ?>"><a class="pc-link" href="<?= Url::to(['/sakip-kebijakan/index']) ?>">Kebijakan</a></li>
            <li class="pc-item <?= $currentRoute == 'sakip-cascadingprogram/index' ? 'active' : '' ?>"><a class="pc-link" href="<?= Url::to(['/sakip-cascadingprogram/index']) ?>">Program</a></li>
            <li class="pc-item <?= $currentRoute == 'sakip-cascadingkegiatan/index' ? 'active' : '' ?>"><a class="pc-link" href="<?= Url::to(['/sakip-cascadingkegiatan/index']) ?>">Kegiatan</a></li>
            <li class="pc-item <?= $currentRoute == 'sakip-cascadingsubkegiatan/index' ? 'active' : '' ?>"><a class="pc-link" href="<?= Url::to(['/sakip-cascadingsubkegiatan/index']) ?>">Sub Kegiatan</a></li>
          </ul>
        </li>

        <li class="pc-item pc-hasmenu <?= $isRktActive ? 'active show' : '' ?>">
          <a href="#!" class="pc-link <?= $isRktActive ? 'active' : '' ?>" <?= $isRktActive ? 'aria-expanded="true"' : '' ?>>
            <span class="pc-micon">
              <i class="ph-duotone ph-scroll"></i>
            </span>
            <span class="pc-mtext">RKT</span>
            <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
          </a>
          <ul class="pc-submenu <?= $isRktActive ? 'show' : '' ?>">
            <li class="pc-item <?= $currentRoute == 'sakip-indikatorsasaranrenstra/index' ? 'active' : '' ?>">
              <a class="pc-link" href="<?= Url::to(['/sakip-indikatorsasaranrenstra/index']) ?>">Target RKT Indikator Sasaran</a>
            </li>
            <li class="pc-item <?= $currentRoute == 'sakip-indikatorcascadingprogram/index' ? 'active' : '' ?>">
              <a class="pc-link" href="<?= Url::to(['/sakip-indikatorcascadingprogram/index']) ?>">Target RKT Indikator Program</a>
            </li>
            <li class="pc-item <?= $currentRoute == 'sakip-indikatorcascadingkegiatan/index' ? 'active' : '' ?>">
              <a class="pc-link" href="<?= Url::to(['/sakip-indikatorcascadingkegiatan/index']) ?>">Target RKT Indikator Kegiatan</a>
            </li>
            <li class="pc-item <?= $currentRoute == 'sakip-indikatorcascadingsubkegiatan/index' ? 'active' : '' ?>">
              <a class="pc-link" href="<?= Url::to(['/sakip-indikatorcascadingsubkegiatan/index']) ?>">Target RKT Indikator Sub Kegiatan</a>
            </li>
            <li class="pc-item <?= $currentRoute == 'sakip-indikatorcascadingprogram/index-anggaran-rkt' ? 'active' : '' ?>">
              <a class="pc-link" href="<?= Url::to(['/sakip-indikatorcascadingprogram/index-anggaran-rkt']) ?>">Anggaran RKT Program</a>
            </li>
            <li class="pc-item <?= $currentRoute == 'sakip-indikatorcascadingkegiatan/index-anggaran-rkt' ? 'active' : '' ?>">
              <a class="pc-link" href="<?= Url::to(['/sakip-indikatorcascadingkegiatan/index-anggaran-rkt']) ?>">Anggaran RKT Kegiatan</a>
            </li>
            <li class="pc-item <?= $currentRoute == 'sakip-indikatorcascadingsubkegiatan/index-anggaran-rkt' ? 'active' : '' ?>">
              <a class="pc-link" href="<?= Url::to(['/sakip-indikatorcascadingsubkegiatan/index-anggaran-rkt']) ?>">Anggaran RKT Sub Kegiatan</a>
            </li>
          </ul>
        </li>


        <li class="pc-item pc-hasmenu <?= $isPkActive ? 'active show' : '' ?>">
          <a href="#!" class="pc-link <?= $isPkActive ? 'active' : '' ?>" <?= $isPkActive ? 'aria-expanded="true"' : '' ?>>
            <span class="pc-micon">
              <i class="ph-duotone ph-clipboard-text"></i>
            </span>
            <span class="pc-mtext">PK</span><span class="pc-arrow"><i data-feather="chevron-right"></i></span>
          </a>
          <ul class="pc-submenu <?= $isPkActive ? 'show' : '' ?>">

            <!-- Sasaran -->
            <li class="pc-item pc-hasmenu <?= $isPkSasaranActive ? 'active show' : '' ?>">
              <a href="#!" class="pc-link <?= $isPkSasaranActive ? 'active' : '' ?>" <?= $isPkSasaranActive ? 'aria-expanded="true"' : '' ?>>
                Target PK Indikator Sasaran<span class="pc-arrow"><i data-feather="chevron-right"></i></span>
              </a>
              <ul class="pc-submenu">
                <li class="pc-item <?= $currentRoute == 'sakip-indikatorsasaranrenstra/index-tahunan-pk' ? 'active' : '' ?>">
                  <a class="pc-link" href="<?= Url::to(['/sakip-indikatorsasaranrenstra/index-tahunan-pk']) ?>">Tahunan</a>
                </li>
                <li class="pc-item <?= $currentRoute == 'sakip-indikatorsasaranrenstra-triwulan/index-triwulan-pk' ? 'active' : '' ?>">
                  <a class="pc-link" href="<?= Url::to(['/sakip-indikatorsasaranrenstra-triwulan/index-triwulan-pk']) ?>">Triwulan</a>
                </li>
              </ul>
            </li>

            <!-- Program -->
            <li class="pc-item pc-hasmenu <?= $isPkProgramActive ? 'active show' : '' ?>">
              <a href="#!" class="pc-link <?= $isPkProgramActive ? 'active' : '' ?>" <?= $isPkProgramActive ? 'aria-expanded="true"' : '' ?>>
                Target PK Indikator Program<span class="pc-arrow"><i data-feather="chevron-right"></i></span>
              </a>
              <ul class="pc-submenu">
                <li class="pc-item <?= $currentRoute == 'sakip-indikatorcascadingprogram/index-tahunan-pk' ? 'active' : '' ?>">
                  <a class="pc-link" href="<?= Url::to(['/sakip-indikatorcascadingprogram/index-tahunan-pk']) ?>">Tahunan</a>
                </li>
                <li class="pc-item <?= $currentRoute == 'sakip-indikatorcascadingprogram-triwulan/index-triwulan-pk' ? 'active' : '' ?>">
                  <a class="pc-link" href="<?= Url::to(['/sakip-indikatorcascadingprogram-triwulan/index-triwulan-pk']) ?>">Triwulan</a>
                </li>
              </ul>
            </li>

            <!-- Kegiatan -->
            <li class="pc-item pc-hasmenu <?= $isPkKegiatanActive ? 'active show' : '' ?>">
              <a href="#!" class="pc-link <?= $isPkKegiatanActive ? 'active' : '' ?>" <?= $isPkKegiatanActive ? 'aria-expanded="true"' : '' ?>>
                Target PK Indikator Kegiatan<span class="pc-arrow"><i data-feather="chevron-right"></i></span>
              </a>
              <ul class="pc-submenu">
                <li class="pc-item <?= $currentRoute == 'sakip-indikatorcascadingkegiatan/index-tahunan-pk' ? 'active' : '' ?>">
                  <a class="pc-link" href="<?= Url::to(['/sakip-indikatorcascadingkegiatan/index-tahunan-pk']) ?>">Tahunan</a>
                </li>
                <li class="pc-item <?= $currentRoute == 'sakip-indikatorcascadingkegiatan-triwulan/index-triwulan-pk' ? 'active' : '' ?>">
                  <a class="pc-link" href="<?= Url::to(['/sakip-indikatorcascadingkegiatan-triwulan/index-triwulan-pk']) ?>">Triwulan</a>
                </li>
              </ul>
            </li>

            <!-- Sub Kegiatan -->
            <li class="pc-item pc-hasmenu <?= $isPkSubkegiatanActive ? 'active show' : '' ?>">
              <a href="#!" class="pc-link <?= $isPkSubkegiatanActive ? 'active' : '' ?>" <?= $isPkSubkegiatanActive ? 'aria-expanded="true"' : '' ?>>
                Target PK Indikator Sub Kegiatan<span class="pc-arrow"><i data-feather="chevron-right"></i></span>
              </a>
              <ul class="pc-submenu">
                <li class="pc-item <?= $currentRoute == 'sakip-indikatorcascadingsubkegiatan/index-tahunan-pk' ? 'active' : '' ?>">
                  <a class="pc-link" href="<?= Url::to(['/sakip-indikatorcascadingsubkegiatan/index-tahunan-pk']) ?>">Tahunan</a>
                </li>
                <li class="pc-item <?= $currentRoute == 'sakip-indikatorcascadingsubkegiatan-triwulan/index-triwulan-pk' ? 'active' : '' ?>">
                  <a class="pc-link" href="<?= Url::to(['/sakip-indikatorcascadingsubkegiatan-triwulan/index-triwulan-pk']) ?>">Triwulan</a>
                </li>
              </ul>
            </li>
            <li class="pc-item <?= $currentRoute == 'sakip-indikatorcascadingprogram/index-anggaran-pk' ? 'active' : '' ?>">
              <a class="pc-link" href="<?= Url::to(['/sakip-indikatorcascadingprogram/index-anggaran-pk']) ?>">Anggaran PK Program</a>
            </li>
            <li class="pc-item <?= $currentRoute == 'sakip-indikatorcascadingkegiatan/index-anggaran-pk' ? 'active' : '' ?>">
              <a class="pc-link" href="<?= Url::to(['/sakip-indikatorcascadingkegiatan/index-anggaran-pk']) ?>">Anggaran PK Kegiatan</a>
            </li>
            <li class="pc-item <?= $currentRoute == 'sakip-indikatorcascadingsubkegiatan/index-anggaran-pk' ? 'active' : '' ?>">
              <a class="pc-link" href="<?= Url::to(['/sakip-indikatorcascadingsubkegiatan/index-anggaran-pk']) ?>">Anggaran PK Sub Kegiatan</a>
            </li>
          </ul>
        </li>


        <li class="pc-item pc-hasmenu <?= $isPkpActive ? 'active show' : '' ?>">
          <a href="#!" class="pc-link <?= $isPkpActive ? 'active' : '' ?>" <?= $isPkpActive ? 'aria-expanded="true"' : '' ?>>
            <span class="pc-micon">
              <i class="ph-duotone ph-note-pencil"></i>
            </span>
            <span class="pc-mtext">PK Perubahan</span><span class="pc-arrow"><i data-feather="chevron-right"></i></span>
          </a>
          <ul class="pc-submenu <?= $isPkpActive ? 'show' : '' ?>">

            <!-- Sasaran -->
            <li class="pc-item pc-hasmenu <?= $isPkpSasaranActive ? 'active show' : '' ?>">
              <a href="#!" class="pc-link <?= $isPkpSasaranActive ? 'active' : '' ?>" <?= $isPkpSasaranActive ? 'aria-expanded="true"' : '' ?>>
                Target PKP Indikator Sasaran<span class="pc-arrow"><i data-feather="chevron-right"></i></span>
              </a>
              <ul class="pc-submenu">
                <li class="pc-item <?= $currentRoute == 'sakip-indikatorsasaranrenstra/index-tahunan-pkp' ? 'active' : '' ?>">
                  <a class="pc-link" href="<?= Url::to(['/sakip-indikatorsasaranrenstra/index-tahunan-pkp']) ?>">Tahunan</a>
                </li>
                <li class="pc-item <?= $currentRoute == 'sakip-indikatorsasaranrenstra-triwulan/index-triwulan-pkp' ? 'active' : '' ?>">
                  <a class="pc-link" href="<?= Url::to(['/sakip-indikatorsasaranrenstra-triwulan/index-triwulan-pkp']) ?>">Triwulan</a>
                </li>
              </ul>
            </li>

            <!-- Program -->
            <li class="pc-item pc-hasmenu <?= $isPkpProgramActive ? 'active show' : '' ?>">
              <a href="#!" class="pc-link <?= $isPkpProgramActive ? 'active' : '' ?>" <?= $isPkpProgramActive ? 'aria-expanded="true"' : '' ?>>
                Target PKP Indikator Program<span class="pc-arrow"><i data-feather="chevron-right"></i></span>
              </a>
              <ul class="pc-submenu">
                <li class="pc-item <?= $currentRoute == 'sakip-indikatorcascadingprogram/index-tahunan-pkp' ? 'active' : '' ?>">
                  <a class="pc-link" href="<?= Url::to(['/sakip-indikatorcascadingprogram/index-tahunan-pkp']) ?>">Tahunan</a>
                </li>
                <li class="pc-item <?= $currentRoute == 'sakip-indikatorcascadingprogram-triwulan/index-triwulan-pkp' ? 'active' : '' ?>">
                  <a class="pc-link" href="<?= Url::to(['/sakip-indikatorcascadingprogram-triwulan/index-triwulan-pkp']) ?>">Triwulan</a>
                </li>

              </ul>
            </li>

            <!-- Kegiatan -->
            <li class="pc-item pc-hasmenu <?= $isPkpKegiatanActive ? 'active show' : '' ?>">
              <a href="#!" class="pc-link <?= $isPkpKegiatanActive ? 'active' : '' ?>" <?= $isPkpKegiatanActive ? 'aria-expanded="true"' : '' ?>>
                Target PKP Indikator Kegiatan<span class="pc-arrow"><i data-feather="chevron-right"></i></span>
              </a>
              <ul class="pc-submenu">
                <li class="pc-item <?= $currentRoute == 'sakip-indikatorcascadingkegiatan/index-tahunan-pkp' ? 'active' : '' ?>">
                  <a class="pc-link" href="<?= Url::to(['/sakip-indikatorcascadingkegiatan/index-tahunan-pkp']) ?>">Tahunan</a>
                </li>
                <li class="pc-item <?= $currentRoute == 'sakip-indikatorcascadingkegiatan-triwulan/index-triwulan-pkp' ? 'active' : '' ?>">
                  <a class="pc-link" href="<?= Url::to(['/sakip-indikatorcascadingkegiatan-triwulan/index-triwulan-pkp']) ?>">Triwulan</a>
                </li>

              </ul>
            </li>

            <!-- Sub Kegiatan -->
            <li class="pc-item pc-hasmenu <?= $isPkpSubkegiatanActive ? 'active show' : '' ?>">
              <a href="#!" class="pc-link <?= $isPkpSubkegiatanActive ? 'active' : '' ?>" <?= $isPkpSubkegiatanActive ? 'aria-expanded="true"' : '' ?>>
                Target PKP Indikator Sub Kegiatan<span class="pc-arrow"><i data-feather="chevron-right"></i></span>
              </a>
              <ul class="pc-submenu">
                <li class="pc-item <?= $currentRoute == 'sakip-indikatorcascadingsubkegiatan/index-tahunan-pkp' ? 'active' : '' ?>">
                  <a class="pc-link" href="<?= Url::to(['/sakip-indikatorcascadingsubkegiatan/index-tahunan-pkp']) ?>">Tahunan</a>
                </li>
                <li class="pc-item <?= $currentRoute == 'sakip-indikatorcascadingsubkegiatan-triwulan/index-triwulan-pkp' ? 'active' : '' ?>">
                  <a class="pc-link" href="<?= Url::to(['/sakip-indikatorcascadingsubkegiatan-triwulan/index-triwulan-pkp']) ?>">Triwulan</a>
                </li>

              </ul>
            </li>
            <li class="pc-item <?= $currentRoute == 'sakip-indikatorcascadingprogram/index-anggaran-pkp' ? 'active' : '' ?>">
              <a class="pc-link" href="<?= Url::to(['/sakip-indikatorcascadingprogram/index-anggaran-pkp']) ?>">Anggaran PKP Program</a>
            </li>
            <li class="pc-item <?= $currentRoute == 'sakip-indikatorcascadingkegiatan/index-anggaran-pkp' ? 'active' : '' ?>">
              <a class="pc-link" href="<?= Url::to(['/sakip-indikatorcascadingkegiatan/index-anggaran-pkp']) ?>">Anggaran PKP Kegiatan</a>
            </li>
            <li class="pc-item <?= $currentRoute == 'sakip-indikatorcascadingsubkegiatan/index-anggaran-pkp' ? 'active' : '' ?>">
              <a class="pc-link" href="<?= Url::to(['/sakip-indikatorcascadingsubkegiatan/index-anggaran-pkp']) ?>">Anggaran PKP Sub Kegiatan</a>
            </li>
          </ul>
        </li>


        <li class="pc-item pc-hasmenu <?= $isCapkinActive ? 'active show' : '' ?>">
          <a href="#!" class="pc-link <?= $isCapkinActive ? 'active' : '' ?>" <?= $isCapkinActive ? 'aria-expanded="true"' : '' ?>>
            <span class="pc-micon">
              <i class="ph-duotone ph-chart-line-up"></i>
            </span>
            <span class="pc-mtext">Capaian Kinerja</span><span class="pc-arrow"><i data-feather="chevron-right"></i></span>
          </a>
          <ul class="pc-submenu <?= $isCapkinActive ? 'show' : '' ?>">

            <!-- Sasaran -->
            <li class="pc-item pc-hasmenu <?= $isCapkinSasaranActive ? 'active show' : '' ?>">
              <a href="#!" class="pc-link <?= $isCapkinSasaranActive ? 'active' : '' ?>" <?= $isCapkinSasaranActive ? 'aria-expanded="true"' : '' ?>>
                Realisasi Indikator Sasaran<span class="pc-arrow"><i data-feather="chevron-right"></i></span>
              </a>
              <ul class="pc-submenu">
                <li class="pc-item <?= $currentRoute == 'sakip-indikatorsasaranrenstra-triwulan/index-triwulan-capaian' ? 'active' : '' ?>">
                  <a class="pc-link" href="<?= Url::to(['/sakip-indikatorsasaranrenstra-triwulan/index-triwulan-capaian']) ?>">Triwulan</a>
                </li>
                <li class="pc-item <?= $currentRoute == 'sakip-indikatorsasaranrenstra/index-tahunan-capaian' ? 'active' : '' ?>">
                  <a class="pc-link" href="<?= Url::to(['/sakip-indikatorsasaranrenstra/index-tahunan-capaian']) ?>">Tahunan</a>
                </li>

              </ul>
            </li>

            <!-- Program -->
            <li class="pc-item pc-hasmenu <?= $isCapkinProgramActive ? 'active show' : '' ?>">
              <a href="#!" class="pc-link <?= $isCapkinProgramActive ? 'active' : '' ?>" <?= $isCapkinProgramActive ? 'aria-expanded="true"' : '' ?>>
                Realisasi Indikator Program<span class="pc-arrow"><i data-feather="chevron-right"></i></span>
              </a>
              <ul class="pc-submenu">
                <li class="pc-item <?= $currentRoute == 'sakip-indikatorcascadingprogram-triwulan/index-triwulan-capaian' ? 'active' : '' ?>">
                  <a class="pc-link" href="<?= Url::to(['/sakip-indikatorcascadingprogram-triwulan/index-triwulan-capaian']) ?>">Triwulan</a>
                </li>
                <li class="pc-item <?= $currentRoute == 'sakip-indikatorcascadingprogram/index-tahunan-capaian' ? 'active' : '' ?>">
                  <a class="pc-link" href="<?= Url::to(['/sakip-indikatorcascadingprogram/index-tahunan-capaian']) ?>">Tahunan</a>
                </li>

              </ul>
            </li>

            <!-- Kegiatan -->
            <li class="pc-item pc-hasmenu <?= $isCapkinKegiatanActive ? 'active show' : '' ?>">
              <a href="#!" class="pc-link <?= $isCapkinKegiatanActive ? 'active' : '' ?>" <?= $isCapkinKegiatanActive ? 'aria-expanded="true"' : '' ?>>
                Realisasi Indikator Kegiatan<span class="pc-arrow"><i data-feather="chevron-right"></i></span>
              </a>
              <ul class="pc-submenu">
                <li class="pc-item <?= $currentRoute == 'sakip-indikatorcascadingkegiatan-triwulan/index-triwulan-capaian' ? 'active' : '' ?>">
                  <a class="pc-link" href="<?= Url::to(['/sakip-indikatorcascadingkegiatan-triwulan/index-triwulan-capaian']) ?>">Triwulan</a>
                </li>
                <li class="pc-item <?= $currentRoute == 'sakip-indikatorcascadingkegiatan/index-tahunan-capaian' ? 'active' : '' ?>">
                  <a class="pc-link" href="<?= Url::to(['/sakip-indikatorcascadingkegiatan/index-tahunan-capaian']) ?>">Tahunan</a>
                </li>

              </ul>
            </li>

            <!-- Sub Kegiatan -->
            <li class="pc-item pc-hasmenu <?= $isCapkinSubkegiatanActive ? 'active show' : '' ?>">
              <a href="#!" class="pc-link <?= $isCapkinSubkegiatanActive ? 'active' : '' ?>" <?= $isCapkinSubkegiatanActive ? 'aria-expanded="true"' : '' ?>>
                Realisasi Indikator Sub Kegiatan<span class="pc-arrow"><i data-feather="chevron-right"></i></span>
              </a>
              <ul class="pc-submenu">
                <li class="pc-item <?= $currentRoute == 'sakip-indikatorcascadingsubkegiatan-triwulan/index-triwulan-capaian' ? 'active' : '' ?>">
                  <a class="pc-link" href="<?= Url::to(['/sakip-indikatorcascadingsubkegiatan-triwulan/index-triwulan-capaian']) ?>">Triwulan</a>
                </li>
                <li class="pc-item <?= $currentRoute == 'sakip-indikatorcascadingsubkegiatan/index-tahunan-capaian' ? 'active' : '' ?>">
                  <a class="pc-link" href="<?= Url::to(['/sakip-indikatorcascadingsubkegiatan/index-tahunan-capaian']) ?>">Tahunan</a>
                </li>
              </ul>
            </li>

            <!-- Penyerapan -->
            <li class="pc-item <?= $currentRoute == 'sakip-indikatorcascadingsubkegiatan-triwulan/index-triwulan-penyerapan' ? 'active' : '' ?>">
              <a class="pc-link" href="<?= Url::to(['/sakip-indikatorcascadingsubkegiatan-triwulan/index-triwulan-penyerapan']) ?>">Penyerapan Anggaran</a>
            </li>

          </ul>
        </li>


        <li class="pc-item pc-hasmenu <?= $isLaporanActive ? 'active show' : '' ?>">
          <a href="#!" class="pc-link <?= $isLaporanActive ? 'active' : '' ?>" <?= $isLaporanActive ? 'aria-expanded="true"' : '' ?>>
            <span class="pc-micon">
              <i class="ph-duotone ph-file-text"></i>
            </span>
            <span class="pc-mtext">Laporan</span><span class="pc-arrow"><i data-feather="chevron-right"></i></span>
          </a>
          <ul class="pc-submenu <?= $isLaporanActive ? 'show' : '' ?>">

            <li class="pc-item <?= $currentRoute == 'laporan/index-laporan-renstra' ? 'active' : '' ?>">
              <a class="pc-link" href="<?= Url::to(['/laporan/index-laporan-renstra']) ?>">Renstra</a>
            </li>

            <li class="pc-item <?= $currentRoute == 'laporan/index-laporan-renja-tahunan' ? 'active' : '' ?>">
              <a class="pc-link" href="<?= Url::to(['/laporan/index-laporan-renja-tahunan']) ?>">Rencana Kinerja Tahunan</a>
            </li>

            <li class="pc-item <?= $currentRoute == 'laporan/index-laporan-iku' ? 'active' : '' ?>">
              <a class="pc-link" href="<?= Url::to(['/laporan/index-laporan-iku']) ?>">Indikator Kinerja Utama</a>
            </li>

            <li class="pc-item <?= $currentRoute == 'laporan/index-laporan-tapkin' ? 'active' : '' ?>">
              <a class="pc-link" href="<?= Url::to(['/laporan/index-laporan-tapkin']) ?>">Perjanjian Kinerja</a>
            </li>

            <li class="pc-item <?= $currentRoute == 'laporan/index-laporan-capkin-iku' ? 'active' : '' ?>">
              <a class="pc-link" href="<?= Url::to(['/laporan/index-laporan-capkin-iku']) ?>">Capaian Indikator Kinerja Utama</a>
            </li>

            <li class="pc-item <?= $currentRoute == 'laporan/index-laporan-realisasi-anggaran' ? 'active' : '' ?>">
              <a class="pc-link" href="<?= Url::to(['/laporan/index-laporan-realisasi-anggaran']) ?>">Pagu dan Realisasi Anggaran</a>
            </li>

            <li class="pc-item pc-hasmenu <?= $isLaporanAnalisisActive ? 'active show' : '' ?>">
              <a href="#!" class="pc-link <?= $isLaporanAnalisisActive ? 'active' : '' ?>" <?= $isLaporanAnalisisActive ? 'aria-expanded="true"' : '' ?>>
                Analisis Pencapaian Sasaran<span class="pc-arrow"><i data-feather="chevron-right"></i></span>
              </a>
              <ul class="pc-submenu">
                <li class="pc-item <?= $currentRoute == 'laporan/index-laporan-analisis-sasaran-triwulan' ? 'active' : '' ?>">
                  <a class="pc-link" href="<?= Url::to(['/laporan/index-laporan-analisis-sasaran-triwulan']) ?>">Triwulan</a>
                </li>
                <li class="pc-item <?= $currentRoute == 'laporan/index-laporan-analisis-sasaran-tahunan' ? 'active' : '' ?>">
                  <a class="pc-link" href="<?= Url::to(['/laporan/index-laporan-analisis-sasaran-tahunan']) ?>">Tahunan</a>
                </li>
              </ul>
            </li>

            <li class="pc-item <?= $currentRoute == 'laporan/index-laporan-rencana-aksi' ? 'active' : '' ?>">
              <a class="pc-link" href="<?= Url::to(['/laporan/index-laporan-rencana-aksi']) ?>">Rencana Aksi</a>
            </li>

            <li class="pc-item <?= $currentRoute == 'laporan/index-laporan-ekinerja' ? 'active' : '' ?>">
              <a class="pc-link" href="<?= Url::to(['/laporan/index-laporan-ekinerja']) ?>">Efisiensi dan Efektifitas Kinerja</a>
            </li>

            <li class="pc-item <?= $currentRoute == 'laporan/index-evaluasi-rkpd' ? 'active' : '' ?>">
              <a class="pc-link" href="<?= Url::to(['/laporan/index-evaluasi-rkpd']) ?>">Evaluasi RKPD</a>
            </li>

            <li class="pc-item <?= $currentRoute == 'sakip-evaluasi-renja/index' ? 'active' : '' ?>">
              <a class="pc-link" href="<?= Url::to(['/sakip-evaluasi-renja/index']) ?>">Evaluasi Renja</a>
            </li>

          </ul>
        </li>

        <?php
        // Jika superadmin/admin, tampilkan semua data
        if (isset($assignments['superadmin']) || isset($assignments['admin'])) {
        ?>
          <li class="pc-item pc-caption">
            <label>Site Manajemen</label>
            <i class="ph-duotone ph-compass-tool"></i>
          </li>
          <li class="pc-item pc-hasmenu">
            <a href="#!" class="pc-link"><span class="pc-micon"> <i class="ph-duotone ph-user-circle-minus"></i></span><span class="pc-mtext">RBAC Admin</span><span class="pc-arrow"><i data-feather="chevron-right"></i></span></a>
            <ul class="pc-submenu">
              <li class="pc-item"><a class="pc-link" href="<?= Url::to(['/admin/assignment']) ?>">Assignment</a></li>
              <li class="pc-item"><a class="pc-link" href="<?= Url::to(['/admin/menu']) ?>">Menu</a></li>
              <li class="pc-item"><a class="pc-link" href="<?= Url::to(['/admin/permission']) ?>">Permissions</a></li>
              <li class="pc-item"><a class="pc-link" href="<?= Url::to(['/admin/role']) ?>">Roles</a></li>
              <li class="pc-item"><a class="pc-link" href="<?= Url::to(['/admin/route']) ?>">Routes</a></li>
              <li class="pc-item"><a class="pc-link" href="<?= Url::to(['/admin/rule']) ?>">Rules</a></li>
            </ul>
          </li>
          <li class="pc-item pc-hasmenu">
            <a href="#!" class="pc-link"><span class="pc-micon"> <i class="ph-duotone ph-gear"></i></span><span class="pc-mtext">Pengaturan</span><span class="pc-arrow"><i data-feather="chevron-right"></i></span></a>
            <ul class="pc-submenu">
              <li class="pc-item"><a class="pc-link" href="<?= Url::to(['/instansi/index']) ?>">Instansi</a></li>
              <li class="pc-item"><a class="pc-link" href="<?= Url::to(['/user-group/index']) ?>">User Group</a></li>
              <li class="pc-item"><a class="pc-link" href="<?= Url::to(['/user/index']) ?>">User</a></li>
            </ul>
          </li>
        <?php } ?>

      </ul>
      <div class="card nav-action-card bg-brand-color-4">
        <div class="card-body" style="background-image: url('<?= Url::base(true) ?>/lightapp/assets/images/layout/nav-card-bg.svg')">
          <h5 class="text-dark">Account</h5>
          <p class="text-dark text-opacity-75"><?= Yii::$app->user->isGuest ? '' : Yii::$app->user->identity->username ?></p>
          <a href="<?= Url::to(['/site/logout']) ?>" class="btn btn-danger" data-method="post">Logout</a>
        </div>
      </div>
    </div>
    <div class="card pc-user-card">
      <!--  -->
      <!--  -->
      <div class="card-body">
        <div class="d-flex align-items-center">
          <div class="flex-shrink-0">
            <img src="<?= Url::base(true) ?>/lightapp/assets/images/face1.jpg" alt="user-image" class="user-avtar wid-45 rounded-circle" />
          </div>
          <div class="flex-grow-1 ms-3 me-2">
            <h6 class="mb-0"><?= Yii::$app->user->isGuest ? '' : Yii::$app->user->identity->username ?></h6>
            <small>
              <?php
              if (!Yii::$app->user->isGuest) {
                $authManager = Yii::$app->authManager;
                $userId = Yii::$app->user->id;
                $assignments = $authManager->getAssignments($userId);

                $rolesPermissions = [];
                foreach ($assignments as $assignment) {
                  $rolesPermissions[] = $assignment->roleName; // atau ->permissionName tergantung implementasi Anda
                }

                echo implode(', ', $rolesPermissions);
              }
              ?>
            </small>
          </div>

          <div class="dropdown">
            <a href="#" class="btn btn-icon btn-link-secondary avtar arrow-none dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" data-bs-offset="0,20">
              <i class="ph-duotone ph-windows-logo"></i>
            </a>
            <div class="dropdown-menu">
              <ul>
                <li><a class="pc-user-links">
                    <i class="ph-duotone ph-user"></i>
                    <span>My Account</span>
                  </a></li>
                <li><a class="pc-user-links">
                    <i class="ph-duotone ph-gear"></i>
                    <span>Settings</span>
                  </a></li>
                <li><a class="pc-user-links">
                    <i class="ph-duotone ph-lock-key"></i>
                    <span>Lock Screen</span>
                  </a></li>
                <li><a class="pc-user-links" href="<?= Url::to(['/site/logout']) ?>" data-method="post">
                    <i class="ph-duotone ph-power"></i>
                    <span>Logout</span>
                  </a>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</nav>
<!-- [ Sidebar Menu ] end -->
<!-- [ Header Topbar ] start -->
<header class="pc-header">
  <div class="header-wrapper"> <!-- [Mobile Media Block] start -->
    <div class="me-auto pc-mob-drp">
      <ul class="list-unstyled">
        <!-- ======= Menu collapse Icon ===== -->
        <li class="pc-h-item pc-sidebar-collapse">
          <a href="#" class="pc-head-link ms-0" id="sidebar-hide">
            <i class="ti ti-menu-2"></i>
          </a>
          <!-- Separator -->

          <!-- Progress Section with hoverable card -->

        </li>

        <li class="pc-h-item pc-sidebar-popup">
          <a href="#" class="pc-head-link ms-0" id="mobile-collapse">
            <i class="ti ti-menu-2"></i>
          </a>
          <!-- Separator -->

          <!-- Progress Section -->

          <!--  -->
        </li>


      </ul>
    </div>
    <!-- [Mobile Media Block end] -->
    <div class="ms-auto">
      <ul class="list-unstyled">
        <!-- <li class="dropdown pc-h-item">
      <a
        class="pc-head-link dropdown-toggle arrow-none me-0"
        data-bs-toggle="dropdown"
        href="#"
        role="button"
        aria-haspopup="false"
        aria-expanded="false"
      >
        <i class="ph-duotone ph-sun-dim"></i>
      </a>
      <div class="dropdown-menu dropdown-menu-end pc-h-dropdown">
        <a href="#!" class="dropdown-item" onclick="layout_change('dark')">
          <i class="ph-duotone ph-moon"></i>
          <span>Dark</span>
        </a>
        <a href="#!" class="dropdown-item" onclick="layout_change('light')">
          <i class="ph-duotone ph-sun-dim"></i>
          <span>Light</span>
        </a>
        <a href="#!" class="dropdown-item" onclick="layout_change_default()">
          <i class="ph-duotone ph-cpu"></i>
          <span>Default</span>
        </a>
      </div>
    </li> -->
        <!-- <li class="pc-h-item">
      <a class="pc-head-link pct-c-btn" href="#" data-bs-toggle="offcanvas" data-bs-target="#offcanvas_pc_layout">
        <i class="ph-duotone ph-gear-six"></i>
      </a>
    </li> -->
        <li class="dropdown pc-h-item">
          <a class="pc-head-link dropdown-toggle arrow-none me-0" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
            <i class="ph-duotone ph-diamonds-four"></i>
          </a>
          <div class="dropdown-menu dropdown-menu-end pc-h-dropdown">
            <!-- <a href="#!" class="dropdown-item">
              <i class="ph-duotone ph-user"></i>
              <span>My Account</span>
            </a>
            <a href="#!" class="dropdown-item">
              <i class="ph-duotone ph-gear"></i>
              <span>Settings</span>
            </a>
            <a href="#!" class="dropdown-item">
              <i class="ph-duotone ph-lifebuoy"></i>
              <span>Support</span>
            </a>
            <a href="#!" class="dropdown-item">
              <i class="ph-duotone ph-lock-key"></i>
              <span>Lock Screen</span>
            </a> -->
            <a href="<?= Url::to(['/site/logout']) ?>" class="dropdown-item" data-method="post">
              <i class="ph-duotone ph-power"></i>
              <span>Logout</span>
            </a>
          </div>
        </li>
        <!-- <li class="dropdown pc-h-item">
          <a class="pc-head-link dropdown-toggle arrow-none me-0" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
            <i class="ph-duotone ph-bell"></i>
            <span class="badge bg-success pc-h-badge">3</span>
          </a>
          <div class="dropdown-menu dropdown-notification dropdown-menu-end pc-h-dropdown">
            <div class="dropdown-header d-flex align-items-center justify-content-between">
              <h5 class="m-0">Notifications</h5>
              <ul class="list-inline ms-auto mb-0">
                <li class="list-inline-item">
                  <a href="../application/mail.html" class="avtar avtar-s btn-link-hover-primary">
                    <i class="ti ti-link f-18"></i>
                  </a>
                </li>
              </ul>
            </div>
            <div class="dropdown-body text-wrap header-notification-scroll position-relative" style="max-height: calc(100vh - 235px)">
              <ul class="list-group list-group-flush">
                <li class="list-group-item">
                  <p class="text-span">Today</p>
                  <div class="d-flex">
                    <div class="flex-shrink-0">
                      <img src="<?= Url::base(true) ?>/lightapp/assets/images/face1.jpg" alt="user-image" class="user-avtar avtar avtar-s" />
                    </div>
                    <div class="flex-grow-1 ms-3">
                      <div class="d-flex">
                        <div class="flex-grow-1 me-3 position-relative">
                          <h6 class="mb-0 text-truncate">Keefe Bond added new tags to ðŸ’ª Design system</h6>
                        </div>
                        <div class="flex-shrink-0">
                          <span class="text-sm">2 min ago</span>
                        </div>
                      </div>
                      <p class="position-relative mt-1 mb-2"><br /><span class="text-truncate">Lorem Ipsum has been the industry's standard dummy text ever since the 1500s.</span></p>
                      <span class="badge bg-light-primary border border-primary me-1 mt-1">web design</span>
                      <span class="badge bg-light-warning border border-warning me-1 mt-1">Dashobard</span>
                      <span class="badge bg-light-success border border-success me-1 mt-1">Design System</span>
                    </div>
                  </div>
                </li>
                <li class="list-group-item">
                  <div class="d-flex">
                    <div class="flex-shrink-0">
                      <div class="avtar avtar-s bg-light-primary">
                        <i class="ph-duotone ph-chats-teardrop f-18"></i>
                      </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                      <div class="d-flex">
                        <div class="flex-grow-1 me-3 position-relative">
                          <h6 class="mb-0 text-truncate">Message</h6>
                        </div>
                        <div class="flex-shrink-0">
                          <span class="text-sm">1 hour ago</span>
                        </div>
                      </div>
                      <p class="position-relative mt-1 mb-2"><br /><span class="text-truncate">Lorem Ipsum has been the industry's standard dummy text ever since the 1500s.</span></p>
                    </div>
                  </div>
                </li>
                <li class="list-group-item">
                  <p class="text-span">Yesterday</p>
                  <div class="d-flex">
                    <div class="flex-shrink-0">
                      <div class="avtar avtar-s bg-light-danger">
                        <i class="ph-duotone ph-user f-18"></i>
                      </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                      <div class="d-flex">
                        <div class="flex-grow-1 me-3 position-relative">
                          <h6 class="mb-0 text-truncate">Challenge invitation</h6>
                        </div>
                        <div class="flex-shrink-0">
                          <span class="text-sm">12 hour ago</span>
                        </div>
                      </div>
                      <p class="position-relative mt-1 mb-2"><br /><span class="text-truncate"><strong> Jonny aber </strong> invites to join the challenge</span></p>
                      <button class="btn btn-sm rounded-pill btn-outline-secondary me-2">Decline</button>
                      <button class="btn btn-sm rounded-pill btn-primary">Accept</button>
                    </div>
                  </div>
                </li>
                <li class="list-group-item">
                  <div class="d-flex">
                    <div class="flex-shrink-0">
                      <div class="avtar avtar-s bg-light-info">
                        <i class="ph-duotone ph-notebook f-18"></i>
                      </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                      <div class="d-flex">
                        <div class="flex-grow-1 me-3 position-relative">
                          <h6 class="mb-0 text-truncate">Forms</h6>
                        </div>
                        <div class="flex-shrink-0">
                          <span class="text-sm">2 hour ago</span>
                        </div>
                      </div>
                      <p class="position-relative mt-1 mb-2">Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard
                        dummy text ever since the 1500s.</p>
                    </div>
                  </div>
                </li>
                <li class="list-group-item">
                  <div class="d-flex">
                    <div class="flex-shrink-0">
                      <img src="<?= Url::base(true) ?>/lightapp/assets/images/user/avatar-2.jpg" alt="user-image" class="user-avtar avtar avtar-s" />
                    </div>
                    <div class="flex-grow-1 ms-3">
                      <div class="d-flex">
                        <div class="flex-grow-1 me-3 position-relative">
                          <h6 class="mb-0 text-truncate">Keefe Bond added new tags to ðŸ’ª Design system</h6>
                        </div>
                        <div class="flex-shrink-0">
                          <span class="text-sm">2 min ago</span>
                        </div>
                      </div>
                      <p class="position-relative mt-1 mb-2"><br /><span class="text-truncate">Lorem Ipsum has been the industry's standard dummy text ever since the 1500s.</span></p>
                      <button class="btn btn-sm rounded-pill btn-outline-secondary me-2">Decline</button>
                      <button class="btn btn-sm rounded-pill btn-primary">Accept</button>
                    </div>
                  </div>
                </li>
                <li class="list-group-item">
                  <div class="d-flex">
                    <div class="flex-shrink-0">
                      <div class="avtar avtar-s bg-light-success">
                        <i class="ph-duotone ph-shield-checkered f-18"></i>
                      </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                      <div class="d-flex">
                        <div class="flex-grow-1 me-3 position-relative">
                          <h6 class="mb-0 text-truncate">Security</h6>
                        </div>
                        <div class="flex-shrink-0">
                          <span class="text-sm">5 hour ago</span>
                        </div>
                      </div>
                      <p class="position-relative mt-1 mb-2">Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard
                        dummy text ever since the 1500s.</p>
                    </div>
                  </div>
                </li>
              </ul>
            </div>
            <div class="dropdown-footer">
              <div class="row g-3">
                <div class="col-6">
                  <div class="d-grid"><button class="btn btn-primary">Archive all</button></div>
                </div>
                <div class="col-6">
                  <div class="d-grid"><button class="btn btn-outline-secondary">Mark all as read</button></div>
                </div>
              </div>
            </div>
          </div>
        </li> -->
        <li class="dropdown pc-h-item header-user-profile">
          <a class="pc-head-link dropdown-toggle arrow-none me-0" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" data-bs-auto-close="outside" aria-expanded="false">
            <img src="<?= Url::base(true) ?>/lightapp/assets/images/face1.jpg" alt="user-image" class="user-avtar" />
          </a>
          <div class="dropdown-menu dropdown-user-profile dropdown-menu-end pc-h-dropdown">
            <div class="dropdown-header d-flex align-items-center justify-content-between">
              <h5 class="m-0">Profile</h5>
            </div>
            <div class="dropdown-body">
              <div class="profile-notification-scroll position-relative" style="max-height: calc(100vh - 225px)">
                <ul class="list-group list-group-flush w-100">
                  <li class="list-group-item">
                    <div class="d-flex align-items-center">
                      <div class="flex-shrink-0">
                        <img src="<?= Url::base(true) ?>/lightapp/assets/images/face1.jpg" alt="user-image" class="wid-50 rounded-circle" />
                      </div>
                      <div class="flex-grow-1 mx-3">
                        <h5 class="mb-0"><?= Yii::$app->user->isGuest ? '' : Yii::$app->user->identity->username ?></h5>
                        <a class="link-primary" href="mailto:<?= Yii::$app->user->identity->email ?>"><?= Yii::$app->user->isGuest ? '' : Yii::$app->user->identity->email ?></a>
                      </div>
                      <span class="badge bg-primary"><?php
                                                      if (!Yii::$app->user->isGuest) {
                                                        $authManager = Yii::$app->authManager;
                                                        $userId = Yii::$app->user->id;
                                                        $assignments = $authManager->getAssignments($userId);

                                                        $rolesPermissions = [];
                                                        foreach ($assignments as $assignment) {
                                                          $rolesPermissions[] = $assignment->roleName; // atau ->permissionName tergantung implementasi Anda
                                                        }

                                                        echo implode(', ', $rolesPermissions);
                                                      }
                                                      ?></span>
                    </div>
                  </li>
                  <!-- <li class="list-group-item">
                    <a href="#" class="dropdown-item">
                      <span class="d-flex align-items-center">
                        <i class="ph-duotone ph-key"></i>
                        <span>Change password</span>
                      </span>
                    </a>
                    <a href="#" class="dropdown-item">
                      <span class="d-flex align-items-center">
                        <i class="ph-duotone ph-envelope-simple"></i>
                        <span>Recently mail</span>
                      </span>
                      <div class="user-group">
                        <img src="<?= Url::base(true) ?>/lightapp/assets/images/face1.jpg" alt="user-image" class="avtar" />
                        <img src="<?= Url::base(true) ?>/lightapp/assets/images/face1.jpg" alt="user-image" class="avtar" />
                        <img src="<?= Url::base(true) ?>/lightapp/assets/images/face1.jpg" alt="user-image" class="avtar" />
                      </div>
                    </a>
                    <a href="#" class="dropdown-item">
                      <span class="d-flex align-items-center">
                        <i class="ph-duotone ph-calendar-blank"></i>
                        <span>Schedule meetings</span>
                      </span>
                    </a>
                  </li> -->
                  <!-- <li class="list-group-item">
                    <a href="#" class="dropdown-item">
                      <span class="d-flex align-items-center">
                        <i class="ph-duotone ph-heart"></i>
                        <span>Favorite</span>
                      </span>
                    </a>
                    <a href="#" class="dropdown-item">
                      <span class="d-flex align-items-center">
                        <i class="ph-duotone ph-arrow-circle-down"></i>
                        <span>Download</span>
                      </span>
                      <span class="avtar avtar-xs rounded-circle bg-danger text-white">10</span>
                    </a>
                  </li> -->
                  <!-- <li class="list-group-item">
                    <div class="dropdown-item">
                      <span class="d-flex align-items-center">
                        <i class="ph-duotone ph-globe-hemisphere-west"></i>
                        <span>Languages</span>
                      </span>
                      <span class="flex-shrink-0">
                        <select class="form-select bg-transparent form-select-sm border-0 shadow-none">
                          <option value="1">English</option>
                          <option value="2">Spain</option>
                          <option value="3">Arbic</option>
                        </select>
                      </span>
                    </div>
                    <a href="#" class="dropdown-item">
                      <span class="d-flex align-items-center">
                        <i class="ph-duotone ph-flag"></i>
                        <span>Country</span>
                      </span>
                    </a>
                    <div class="dropdown-item">
                      <span class="d-flex align-items-center">
                        <i class="ph-duotone ph-moon"></i>
                        <span>Dark mode</span>
                      </span>
                      <div class="form-check form-switch form-check-reverse m-0">
                        <input class="form-check-input f-18" id="dark-mode" type="checkbox" onclick="dark_mode()" role="switch" />
                      </div>
                    </div>
                  </li> -->
                  <!-- <li class="list-group-item">
                    <a href="#" class="dropdown-item">
                      <span class="d-flex align-items-center">
                        <i class="ph-duotone ph-user-circle"></i>
                        <span>Edit profile</span>
                      </span>
                    </a>
                    <a href="#" class="dropdown-item">
                      <span class="d-flex align-items-center">
                        <i class="ph-duotone ph-star text-warning"></i>
                        <span>Upgrade account</span>
                        <span class="badge bg-light-success border border-success ms-2">NEW</span>
                      </span>
                    </a>
                    <a href="#" class="dropdown-item">
                      <span class="d-flex align-items-center">
                        <i class="ph-duotone ph-bell"></i>
                        <span>Notifications</span>
                      </span>
                    </a>
                    <a href="#" class="dropdown-item">
                      <span class="d-flex align-items-center">
                        <i class="ph-duotone ph-gear-six"></i>
                        <span>Settings</span>
                      </span>
                    </a>
                  </li> -->
                  <li class="list-group-item">
                    <a href="<?= Url::to(['/site/portal']) ?>" class="dropdown-item">
                      <span class="d-flex align-items-center">
                        <i class="ph-duotone ph-plus-circle"></i>
                        <span>Kembali ke Portal</span>
                      </span>
                    </a>
                    <!-- Updated Switch Apps link -->
                    <!-- <a href="#" class="dropdown-item" id="switchAppBtn">
                      <span class="d-flex align-items-center">
                        <i class="ph-duotone ph-power"></i>
                        <span>Switch Apps</span>
                      </span>
                    </a> -->
                    <a href="<?= Url::to(['/site/index-main']) ?>" class="dropdown-item" data-method="post">
                      <span class="d-flex align-items-center">
                        <i class="ph-duotone ph-power"></i>
                        <span>Switch Dashboard</span>
                      </span>
                    </a>
                    <a href="<?= Url::to(['/user/change-profile']) ?>" class="dropdown-item" data-method="post">
                      <span class="d-flex align-items-center">
                        <i class="ph-duotone ph-user"></i>
                        <span>Change Password</span>
                      </span>
                    </a>

                    <a href="<?= Url::to(['/site/logout']) ?>" class="dropdown-item" data-method="post">
                      <span class="d-flex align-items-center">
                        <i class="ph-duotone ph-power"></i>
                        <span>Logout</span>
                      </span>
                    </a>
                  </li>

                </ul>
              </div>
            </div>
          </div>
        </li>
      </ul>
    </div>
  </div>
</header>
<!-- [ Header ] end -->

<!-- Modal -->
<!-- Modal -->
<div class="modal fade" id="appSwitchModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-fullscreen">
    <div class="modal-contents">
      <!-- No header -->
      <div class="modal-bodys" id="modalContent">
        <!-- Content will be loaded dynamically here -->
      </div>
    </div>
  </div>
</div>




<script>
  // Event listener for the "Switch Apps" button
  document.getElementById('switchAppBtn').addEventListener('click', function(e) {
    e.preventDefault(); // Prevent the default link behavior

    // Trigger the modal to show
    $('#appSwitchModal').modal('show');

    // Make an AJAX request to fetch the content of index-main
    $.ajax({
      url: '<?= Url::to(['/site/index-main']) ?>', // URL to fetch index-main
      type: 'GET',
      success: function(response) {
        // Place the returned content inside the modal body
        $('#modalContent').html(response);
      },
      error: function() {
        // Handle any errors here
        $('#modalContent').html('<p>Sorry, we could not load the app content at this time.</p>');
      }
    });
  });
</script>