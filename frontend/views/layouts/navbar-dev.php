<style>
  .bg-navy {
    background-color: #000080 !important;
    /* Navy Blue */
  }

  /* Style for the detail card */
  .esakip-detail-card {
    display: none;
    position: absolute;
    top: 100%;
    /* Position it just below the progress section */
    left: 0;
    z-index: 1000;
    background-color: #ffffff;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    padding: 1rem;
    width: 1200px;
    border-radius: 8px;
    transition: opacity 0.3s ease;
  }

  .progress-section:hover .esakip-detail-card,
  .progress-section:focus-within .esakip-detail-card {
    display: block;
  }

  /* Ensure modal takes full screen width and height */
  .modal-fullscreen {
    max-width: 100% !important;
    height: 100% !important;
    margin: 0;
  }

  .modal-content {
    height: 100% !important;
    border-radius: 0;
  }

  .modal-body {
    padding: 0;
    height: 100%;
    overflow-y: auto;
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


// =========================================================================
// AWAL DARI BLOK LOGIKA UNTUK MENU AKTIF
// =========================================================================

$currentRoute = Yii::$app->controller->getRoute();

// Menentukan route yang termasuk dalam menu utama RENSTRA
$renstraRoutes = [
  'sakip-visi/index-dev',
  'sakip-sasaranrenstra/index-dev',
  'sakip-tujuanrenstra/index-dev',
  'sakip-indikatortujuanrenstra/index-dev',
  'sakip-sasaranrenstra/index-formulasi-dev',
  'sakip-strategi/index-dev',
  'sakip-kebijakan/index-dev',
  'sakip-cascadingprogram/index-dev',
  'sakip-cascadingkegiatan/index-dev',
  'sakip-cascadingsubkegiatan/index-dev'
];
$isRenstraActive = in_array($currentRoute, $renstraRoutes);

// Khusus untuk submenu "Sasaran-Tujuan" di dalam RENSTRA
$sasaranTujuanRoutes = [
  'sakip-sasaranrenstra/index-dev',
  'sakip-tujuanrenstra/index-dev',
  'sakip-indikatortujuanrenstra/index-dev',
  'sakip-sasaranrenstra/index-formulasi-dev'
];
$isSasaranTujuanActive = in_array($currentRoute, $sasaranTujuanRoutes);

// Menentukan route yang termasuk dalam menu utama RKT
$rktRoutes = [
  'sakip-indikatorsasaranrenstra/index-rkt-dev',
  'sakip-indikatorcascadingprogram/index-rkt-dev',
  'sakip-indikatorcascadingkegiatan/index-rkt-dev',
  'sakip-indikatorcascadingsubkegiatan/index-rkt-dev',
  'sakip-indikatorcascadingprogram/index-anggaran-rkt-dev',
  'sakip-indikatorcascadingkegiatan/index-anggaran-rkt-dev',
  'sakip-indikatorcascadingsubkegiatan/index-anggaran-rkt-dev',
];
$isRktActive = in_array($currentRoute, $rktRoutes);

// Menentukan route yang termasuk dalam menu utama PK
$pkRoutes = [
  'sakip-indikatorsasaranrenstra/index-tahunan-pk-dev',
  'sakip-indikatorsasaranrenstra-triwulan/index-triwulan-pk-dev',
  'sakip-indikatorcascadingprogram/index-tahunan-pk-dev',
  'sakip-indikatorcascadingprogram-triwulan/index-triwulan-pk-dev',
  'sakip-indikatorcascadingkegiatan/index-tahunan-pk-dev',
  'sakip-indikatorcascadingkegiatan-triwulan/index-triwulan-pk-dev',
  'sakip-indikatorcascadingsubkegiatan/index-tahunan-pk-dev',
  'sakip-indikatorcascadingsubkegiatan-triwulan/index-triwulan-pk-dev',
  'sakip-indikatorcascadingprogram/index-anggaran-pk-dev',
  'sakip-indikatorcascadingkegiatan/index-anggaran-pk-dev',
  'sakip-indikatorcascadingsubkegiatan/index-anggaran-pk-dev',
];
$isPkActive = in_array($currentRoute, $pkRoutes);

// Submenu di dalam PK
$pkSasaranRoutes = [
  'sakip-indikatorsasaranrenstra/index-tahunan-pk-dev',
  'sakip-indikatorsasaranrenstra-triwulan/index-triwulan-pk-dev',
];
$isPkSasaranActive = in_array($currentRoute, $pkSasaranRoutes);

$pkProgramRoutes = [
  'sakip-indikatorcascadingprogram/index-tahunan-pk-dev',
  'sakip-indikatorcascadingprogram-triwulan/index-triwulan-pk-dev',
];
$isPkProgramActive = in_array($currentRoute, $pkProgramRoutes);

$pkKegiatanRoutes = [
  'sakip-indikatorcascadingkegiatan/index-tahunan-pk-dev',
  'sakip-indikatorcascadingkegiatan-triwulan/index-triwulan-pk-dev',
];
$isPkKegiatanActive = in_array($currentRoute, $pkKegiatanRoutes);

$pkSubkegiatanRoutes = [
  'sakip-indikatorcascadingsubkegiatan/index-tahunan-pk-dev',
  'sakip-indikatorcascadingsubkegiatan-triwulan/index-triwulan-pk-dev',
];
$isPkSubkegiatanActive = in_array($currentRoute, $pkSubkegiatanRoutes);

// Menentukan route yang termasuk dalam menu utama PK Perubahan
$pkpRoutes = [
  'sakip-indikatorsasaranrenstra/index-tahunan-pkp-dev',
  'sakip-indikatorsasaranrenstra-triwulan/index-triwulan-pkp-dev',
  'sakip-indikatorcascadingprogram/index-tahunan-pkp-dev',
  'sakip-indikatorcascadingprogram-triwulan/index-triwulan-pkp-dev',
  'sakip-indikatorcascadingkegiatan/index-tahunan-pkp-dev',
  'sakip-indikatorcascadingkegiatan-triwulan/index-triwulan-pkp-dev',
  'sakip-indikatorcascadingsubkegiatan/index-tahunan-pkp-dev',
  'sakip-indikatorcascadingsubkegiatan-triwulan/index-triwulan-pkp-dev',
  'sakip-indikatorcascadingprogram/index-anggaran-pkp-dev',
  'sakip-indikatorcascadingkegiatan/index-anggaran-pkp-dev',
  'sakip-indikatorcascadingsubkegiatan/index-anggaran-pkp-dev',
];
$isPkpActive = in_array($currentRoute, $pkpRoutes);

// Submenu di dalam PKP
$pkpSasaranRoutes = [
  'sakip-indikatorsasaranrenstra/index-tahunan-pkp-dev',
  'sakip-indikatorsasaranrenstra-triwulan/index-triwulan-pkp-dev',
];
$isPkpSasaranActive = in_array($currentRoute, $pkpSasaranRoutes);

$pkpProgramRoutes = [
  'sakip-indikatorcascadingprogram/index-tahunan-pkp-dev',
  'sakip-indikatorcascadingprogram-triwulan/index-triwulan-pkp-dev',
];
$isPkpProgramActive = in_array($currentRoute, $pkpProgramRoutes);

$pkpKegiatanRoutes = [
  'sakip-indikatorcascadingkegiatan/index-tahunan-pkp-dev',
  'sakip-indikatorcascadingkegiatan-triwulan/index-triwulan-pkp-dev',
];
$isPkpKegiatanActive = in_array($currentRoute, $pkpKegiatanRoutes);

$pkpSubkegiatanRoutes = [
  'sakip-indikatorcascadingsubkegiatan/index-tahunan-pkp-dev',
  'sakip-indikatorcascadingsubkegiatan-triwulan/index-triwulan-pkp-dev',
];
$isPkpSubkegiatanActive = in_array($currentRoute, $pkpSubkegiatanRoutes);

// Menentukan route yang termasuk dalam menu utama Capaian Kinerja
$capkinRoutes = [
  'sakip-indikatorsasaranrenstra-triwulan/index-triwulan-capaian-dev',
  'sakip-indikatorsasaranrenstra/index-tahunan-capaian-dev',
  'sakip-indikatorcascadingprogram-triwulan/index-triwulan-capaian-dev',
  'sakip-indikatorcascadingprogram/index-tahunan-capaian-dev',
  'sakip-indikatorcascadingkegiatan-triwulan/index-triwulan-capaian-dev',
  'sakip-indikatorcascadingkegiatan/index-tahunan-capaian-dev',
  'sakip-indikatorcascadingsubkegiatan-triwulan/index-triwulan-capaian-dev',
  'sakip-indikatorcascadingsubkegiatan/index-tahunan-capaian-dev',
  'sakip-indikatorcascadingsubkegiatan-triwulan/index-triwulan-penyerapan-dev',
];
$isCapkinActive = in_array($currentRoute, $capkinRoutes);

// Submenu di dalam Capaian Kinerja
$capkinSasaranRoutes = [
  'sakip-indikatorsasaranrenstra-triwulan/index-triwulan-capaian-dev',
  'sakip-indikatorsasaranrenstra/index-tahunan-capaian-dev',
];
$isCapkinSasaranActive = in_array($currentRoute, $capkinSasaranRoutes);

$capkinProgramRoutes = [
  'sakip-indikatorcascadingprogram-triwulan/index-triwulan-capaian-dev',
  'sakip-indikatorcascadingprogram/index-tahunan-capaian-dev',
];
$isCapkinProgramActive = in_array($currentRoute, $capkinProgramRoutes);

$capkinKegiatanRoutes = [
  'sakip-indikatorcascadingkegiatan-triwulan/index-triwulan-capaian-dev',
  'sakip-indikatorcascadingkegiatan/index-tahunan-capaian-dev',
];
$isCapkinKegiatanActive = in_array($currentRoute, $capkinKegiatanRoutes);

$capkinSubkegiatanRoutes = [
  'sakip-indikatorcascadingsubkegiatan-triwulan/index-triwulan-capaian-dev',
  'sakip-indikatorcascadingsubkegiatan/index-tahunan-capaian-dev',
];
$isCapkinSubkegiatanActive = in_array($currentRoute, $capkinSubkegiatanRoutes);

// Menentukan route yang termasuk dalam menu utama Laporan
$laporanRoutes = [
  'laporan/index-laporan-renstra-dev',
  'laporan/index-laporan-renja-tahunan-dev',
  'laporan/index-laporan-iku-dev',
  'laporan/index-laporan-capkin-iku-dev',
  'laporan/index-laporan-tapkin-dev',
  'laporan/index-laporan-realisasi-anggaran-dev',
  'laporan/index-laporan-analisis-sasaran-triwulan-dev',
  'laporan/index-laporan-analisis-sasaran-tahunan-dev',
  'laporan/index-laporan-rencana-aksi-dev',
  'laporan/index-laporan-ekinerja-dev',
  'laporan/index-evaluasi-rkpd-dev',
  'sakip-evaluasi-renja/index',
];
$isLaporanActive = in_array($currentRoute, $laporanRoutes);

// Submenu di dalam Laporan
$laporanAnalisisRoutes = [
  'laporan/index-laporan-analisis-sasaran-triwulan-dev',
  'laporan/index-laporan-analisis-sasaran-tahunan-dev',
];
$isLaporanAnalisisActive = in_array($currentRoute, $laporanAnalisisRoutes);

// =========================================================================
// AKHIR DARI BLOK LOGIKA UNTUK MENU AKTIF
// =========================================================================
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
      <a href="<?= Url::to(['/site/index-esakip-dev']) ?>" class="b-brand text-primary">
        <!-- ========   Change your logo from here   ============ -->
        <h5><img src="<?= Url::base(true) ?>/lightapp/assets/images/bappeda.png" alt="logo image" height="60" width="60" class="logo-lg" />eSakip<span class="badge bg-brand-color-2 rounded-pill ms-2 theme-version">v2.0</span></h5>

      </a>
    </div>
    <div class="navbar-content">
      <ul class="pc-navbar">
        <li class="pc-item pc-caption">
          <label>Menu Navigasi</label>
        </li>
        <li class="pc-item pc-hasmenu <?= $currentRoute == 'site/index-esakip-dev' ? 'active' : '' ?>">
          <a href="<?= Url::to(['/site/index-esakip-dev']) ?>" class="pc-link">
            <span class="pc-micon">
              <i class="ph-duotone ph-gauge"></i>
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
        <li class="pc-item pc-hasmenu <?= $currentRoute == 'sakip-lke/index-dev' ? 'active' : '' ?>">
          <a href="<?= Url::to(['/sakip-lke/index-dev']) ?>" class="pc-link">
            <span class="pc-micon">
              <i class="ph-duotone ph-file"></i>
            </span>
            <span class="pc-mtext">LKE</span>
            <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
            <!-- <span class="pc-badge">2</span> -->
          </a>
        </li>

        <li class="pc-item pc-hasmenu <?= $currentRoute == 'sakip-progress/index-dev' ? 'active' : '' ?>">
          <a href="<?= Url::to(['/sakip-progress/index-dev']) ?>" class="pc-link">
            <span class="pc-micon">
              <i class="ph-duotone ph-file"></i>
            </span>
            <span class="pc-mtext">Progress</span>
            <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
            <!-- <span class="pc-badge">2</span> -->
          </a>
        </li>
        <li class="pc-item pc-caption">
          <label>Recap</label>
          <i class="ph-duotone ph-chart-pie"></i>
        </li>

        <li class="pc-item pc-hasmenu <?= $isRenstraActive ? 'active show' : '' ?>">
          <a href="#!" class="pc-link <?= $isRenstraActive ? 'active' : '' ?>"> <span class="pc-micon">
              <i class="ph-duotone ph-file"></i>
            </span>
            <span class="pc-mtext">RENSTRA</span><span class="pc-arrow"><i data-feather="chevron-right"></i></span></a>
          <ul class="pc-submenu <?= $isRenstraActive ? 'show' : '' ?>">
            <li class="pc-item <?= $currentRoute == 'sakip-visi/index-dev' ? 'active' : '' ?>"><a class="pc-link" href="<?= Url::to(['/sakip-visi/index-dev']) ?>">Data SKPD</a></li>
            <li class="pc-item pc-hasmenu <?= $isSasaranTujuanActive ? 'active show' : '' ?>">
              <a href="#!" class="pc-link <?= $isSasaranTujuanActive ? 'active' : '' ?>">Sasaran-Tujuan<span class="pc-arrow"><i data-feather="chevron-right"></i></span></a>
              <ul class="pc-submenu <?= $isSasaranTujuanActive ? 'show' : '' ?>">
                <li class="pc-item <?= $currentRoute == 'sakip-sasaranrenstra/index-dev' ? 'active' : '' ?>"><a class="pc-link" href="<?= Url::to(['/sakip-sasaranrenstra/index-dev']) ?>">Sasaran Renstra</a></li>
                <li class="pc-item <?= $currentRoute == 'sakip-tujuanrenstra/index-dev' ? 'active' : '' ?>"><a class="pc-link" href="<?= Url::to(['/sakip-tujuanrenstra/index-dev']) ?>">Tujuan Renstra</a></li>
                <li class="pc-item <?= $currentRoute == 'sakip-indikatortujuanrenstra/index-dev' ? 'active' : '' ?>"><a class="pc-link" href="<?= Url::to(['/sakip-indikatortujuanrenstra/index-dev']) ?>">Indikator Tujuan Renstra</a></li>
                <li class="pc-item <?= $currentRoute == 'sakip-sasaranrenstra/index-formulasi-dev' ? 'active' : '' ?>"><a class="pc-link" href="<?= Url::to(['/sakip-sasaranrenstra/index-formulasi-dev']) ?>">Formulasi Renstra</a></li>
              </ul>
            </li>
            <li class="pc-item <?= $currentRoute == 'sakip-strategi/index-dev' ? 'active' : '' ?>"><a class="pc-link" href="<?= Url::to(['/sakip-strategi/index-dev']) ?>">Strategi</a></li>
            <li class="pc-item <?= $currentRoute == 'sakip-kebijakan/index-dev' ? 'active' : '' ?>"><a class="pc-link" href="<?= Url::to(['/sakip-kebijakan/index-dev']) ?>">Kebijakan</a></li>
            <li class="pc-item <?= $currentRoute == 'sakip-cascadingprogram/index-dev' ? 'active' : '' ?>"><a class="pc-link" href="<?= Url::to(['/sakip-cascadingprogram/index-dev']) ?>">Program</a></li>
            <li class="pc-item <?= $currentRoute == 'sakip-cascadingkegiatan/index-dev' ? 'active' : '' ?>"><a class="pc-link" href="<?= Url::to(['/sakip-cascadingkegiatan/index-dev']) ?>">Kegiatan</a></li>
            <li class="pc-item <?= $currentRoute == 'sakip-cascadingsubkegiatan/index-dev' ? 'active' : '' ?>"><a class="pc-link" href="<?= Url::to(['/sakip-cascadingsubkegiatan/index-dev']) ?>">Sub Kegiatan</a></li>
          </ul>
        </li>

        <li class="pc-item pc-hasmenu <?= $isRktActive ? 'active show' : '' ?>">
          <a href="#!" class="pc-link <?= $isRktActive ? 'active' : '' ?>">
            <span class="pc-micon">
              <i class="ph-duotone ph-scroll"></i>
            </span>
            <span class="pc-mtext">RKT</span><span class="pc-arrow"><i data-feather="chevron-right"></i></span></a>
          <ul class="pc-submenu <?= $isRktActive ? 'show' : '' ?>">
            <li class="pc-item <?= $currentRoute == 'sakip-indikatorsasaranrenstra/index-rkt-dev' ? 'active' : '' ?>"><a class="pc-link" href="<?= Url::to(['/sakip-indikatorsasaranrenstra/index-rkt-dev']) ?>">Target RKT Indikator Sasaran</a></li>
            <li class="pc-item <?= $currentRoute == 'sakip-indikatorcascadingprogram/index-rkt-dev' ? 'active' : '' ?>"><a class="pc-link" href="<?= Url::to(['/sakip-indikatorcascadingprogram/index-rkt-dev']) ?>">Target RKT Indikator Program</a></li>
            <li class="pc-item <?= $currentRoute == 'sakip-indikatorcascadingkegiatan/index-rkt-dev' ? 'active' : '' ?>"><a class="pc-link" href="<?= Url::to(['/sakip-indikatorcascadingkegiatan/index-rkt-dev']) ?>">Target RKT Indikator Kegiatan</a></li>
            <li class="pc-item <?= $currentRoute == 'sakip-indikatorcascadingsubkegiatan/index-rkt-dev' ? 'active' : '' ?>"><a class="pc-link" href="<?= Url::to(['/sakip-indikatorcascadingsubkegiatan/index-rkt-dev']) ?>">Target RKT Output Indikator Sub Kegiatan</a></li>
            <li class="pc-item <?= $currentRoute == 'sakip-indikatorcascadingprogram/index-anggaran-rkt-dev' ? 'active' : '' ?>"><a class="pc-link" href="<?= Url::to(['/sakip-indikatorcascadingprogram/index-anggaran-rkt-dev']) ?>">Anggaran Program RKT</a></li>
            <li class="pc-item <?= $currentRoute == 'sakip-indikatorcascadingkegiatan/index-anggaran-rkt-dev' ? 'active' : '' ?>"><a class="pc-link" href="<?= Url::to(['/sakip-indikatorcascadingkegiatan/index-anggaran-rkt-dev']) ?>">Anggaran Kegiatan RKT</a></li>
            <li class="pc-item <?= $currentRoute == 'sakip-indikatorcascadingsubkegiatan/index-anggaran-rkt-dev' ? 'active' : '' ?>"><a class="pc-link" href="<?= Url::to(['/sakip-indikatorcascadingsubkegiatan/index-anggaran-rkt-dev']) ?>">Anggaran Sub Kegiatan RKT</a></li>
          </ul>
        </li>

        <li class="pc-item pc-hasmenu <?= $isPkActive ? 'active show' : '' ?>">
          <a href="#!" class="pc-link <?= $isPkActive ? 'active' : '' ?>">
            <span class="pc-micon">
              <i class="ph-duotone ph-clipboard-text"></i>
            </span>
            <span class="pc-mtext">PK</span><span class="pc-arrow"><i data-feather="chevron-right"></i></span></a>
          <ul class="pc-submenu <?= $isPkActive ? 'show' : '' ?>">
            <li class="pc-item pc-hasmenu <?= $isPkSasaranActive ? 'active show' : '' ?>">
              <a href="#!" class="pc-link <?= $isPkSasaranActive ? 'active' : '' ?>">Target PK Indikator Sasaran<span class="pc-arrow"><i data-feather="chevron-right"></i></span></a>
              <ul class="pc-submenu <?= $isPkSasaranActive ? 'show' : '' ?>">
                <li class="pc-item <?= $currentRoute == 'sakip-indikatorsasaranrenstra/index-tahunan-pk-dev' ? 'active' : '' ?>"><a class="pc-link" href="<?= Url::to(['/sakip-indikatorsasaranrenstra/index-tahunan-pk-dev']) ?>">Tahunan</a></li>
                <li class="pc-item <?= $currentRoute == 'sakip-indikatorsasaranrenstra-triwulan/index-triwulan-pk-dev' ? 'active' : '' ?>"><a class="pc-link" href="<?= Url::to(['/sakip-indikatorsasaranrenstra-triwulan/index-triwulan-pk-dev']) ?>">Triwulan</a> </li>
              </ul>
            </li>
            <li class="pc-item pc-hasmenu <?= $isPkProgramActive ? 'active show' : '' ?>">
              <a href="#!" class="pc-link <?= $isPkProgramActive ? 'active' : '' ?>">Target PK Indikator Program<span class="pc-arrow"><i data-feather="chevron-right"></i></span></a>
              <ul class="pc-submenu <?= $isPkProgramActive ? 'show' : '' ?>">
                <li class="pc-item <?= $currentRoute == 'sakip-indikatorcascadingprogram/index-tahunan-pk-dev' ? 'active' : '' ?>"><a class="pc-link" href="<?= Url::to(['/sakip-indikatorcascadingprogram/index-tahunan-pk-dev']) ?>">Tahunan</a></li>
                <li class="pc-item <?= $currentRoute == 'sakip-indikatorcascadingprogram-triwulan/index-triwulan-pk-dev' ? 'active' : '' ?>"><a class="pc-link" href="<?= Url::to(['/sakip-indikatorcascadingprogram-triwulan/index-triwulan-pk-dev']) ?>">Triwulan</a> </li>
              </ul>
            </li>
            <li class="pc-item pc-hasmenu <?= $isPkKegiatanActive ? 'active show' : '' ?>">
              <a href="#!" class="pc-link <?= $isPkKegiatanActive ? 'active' : '' ?>">Target PK Indikator Kegiatan<span class="pc-arrow"><i data-feather="chevron-right"></i></span></a>
              <ul class="pc-submenu <?= $isPkKegiatanActive ? 'show' : '' ?>">
                <li class="pc-item <?= $currentRoute == 'sakip-indikatorcascadingkegiatan/index-tahunan-pk-dev' ? 'active' : '' ?>"><a class="pc-link" href="<?= Url::to(['/sakip-indikatorcascadingkegiatan/index-tahunan-pk-dev']) ?>">Tahunan</a></li>
                <li class="pc-item <?= $currentRoute == 'sakip-indikatorcascadingkegiatan-triwulan/index-triwulan-pk-dev' ? 'active' : '' ?>"><a class="pc-link" href="<?= Url::to(['/sakip-indikatorcascadingkegiatan-triwulan/index-triwulan-pk-dev']) ?>">Triwulan</a> </li>
              </ul>
            </li>
            <li class="pc-item pc-hasmenu <?= $isPkSubkegiatanActive ? 'active show' : '' ?>">
              <a href="#!" class="pc-link <?= $isPkSubkegiatanActive ? 'active' : '' ?>">Target PK Indikator Sub Kegiatan<span class="pc-arrow"><i data-feather="chevron-right"></i></span></a>
              <ul class="pc-submenu <?= $isPkSubkegiatanActive ? 'show' : '' ?>">
                <li class="pc-item <?= $currentRoute == 'sakip-indikatorcascadingsubkegiatan/index-tahunan-pk-dev' ? 'active' : '' ?>"><a class="pc-link" href="<?= Url::to(['/sakip-indikatorcascadingsubkegiatan/index-tahunan-pk-dev']) ?>">Tahunan</a></li>
                <li class="pc-item <?= $currentRoute == 'sakip-indikatorcascadingsubkegiatan-triwulan/index-triwulan-pk-dev' ? 'active' : '' ?>"><a class="pc-link" href="<?= Url::to(['/sakip-indikatorcascadingsubkegiatan-triwulan/index-triwulan-pk-dev']) ?>">Triwulan</a> </li>
              </ul>
            </li>
            <li class="pc-item <?= $currentRoute == 'sakip-indikatorcascadingprogram/index-anggaran-pk-dev' ? 'active' : '' ?>"><a class="pc-link" href="<?= Url::to(['/sakip-indikatorcascadingprogram/index-anggaran-pk-dev']) ?>">Anggaran Program PK</a></li>
            <li class="pc-item <?= $currentRoute == 'sakip-indikatorcascadingkegiatan/index-anggaran-pk-dev' ? 'active' : '' ?>"><a class="pc-link" href="<?= Url::to(['/sakip-indikatorcascadingkegiatan/index-anggaran-pk-dev']) ?>">Anggaran Kegiatan PK</a></li>
            <li class="pc-item <?= $currentRoute == 'sakip-indikatorcascadingsubkegiatan/index-anggaran-pk-dev' ? 'active' : '' ?>"><a class="pc-link" href="<?= Url::to(['/sakip-indikatorcascadingsubkegiatan/index-anggaran-pk-dev']) ?>">Anggaran Sub Kegiatan PK</a></li>
          </ul>
        </li>

        <li class="pc-item pc-hasmenu <?= $isPkpActive ? 'active show' : '' ?>">
          <a href="#!" class="pc-link <?= $isPkpActive ? 'active' : '' ?>">
            <span class="pc-micon">
              <i class="ph-duotone ph-note-pencil"></i>
            </span>
            <span class="pc-mtext">PK Perubahan</span><span class="pc-arrow"><i data-feather="chevron-right"></i></span></a>
          <ul class="pc-submenu <?= $isPkpActive ? 'show' : '' ?>">
            <li class="pc-item pc-hasmenu <?= $isPkpSasaranActive ? 'active show' : '' ?>">
              <a href="#!" class="pc-link <?= $isPkpSasaranActive ? 'active' : '' ?>">Target PK Perubahan Indikator Sasaran<span class="pc-arrow"><i data-feather="chevron-right"></i></span></a>
              <ul class="pc-submenu <?= $isPkpSasaranActive ? 'show' : '' ?>">
                <li class="pc-item <?= $currentRoute == 'sakip-indikatorsasaranrenstra/index-tahunan-pkp-dev' ? 'active' : '' ?>"><a class="pc-link" href="<?= Url::to(['/sakip-indikatorsasaranrenstra/index-tahunan-pkp-dev']) ?>">Tahunan</a></li>
                <li class="pc-item <?= $currentRoute == 'sakip-indikatorsasaranrenstra-triwulan/index-triwulan-pkp-dev' ? 'active' : '' ?>"><a class="pc-link" href="<?= Url::to(['/sakip-indikatorsasaranrenstra-triwulan/index-triwulan-pkp-dev']) ?>">Triwulan</a> </li>
              </ul>
            </li>
            <li class="pc-item pc-hasmenu <?= $isPkpProgramActive ? 'active show' : '' ?>">
              <a href="#!" class="pc-link <?= $isPkpProgramActive ? 'active' : '' ?>">Target PK Perubahan Indikator Program<span class="pc-arrow"><i data-feather="chevron-right"></i></span></a>
              <ul class="pc-submenu <?= $isPkpProgramActive ? 'show' : '' ?>">
                <li class="pc-item <?= $currentRoute == 'sakip-indikatorcascadingprogram/index-tahunan-pkp-dev' ? 'active' : '' ?>"><a class="pc-link" href="<?= Url::to(['/sakip-indikatorcascadingprogram/index-tahunan-pkp-dev']) ?>">Tahunan</a></li>
                <li class="pc-item <?= $currentRoute == 'sakip-indikatorcascadingprogram-triwulan/index-triwulan-pkp-dev' ? 'active' : '' ?>"><a class="pc-link" href="<?= Url::to(['/sakip-indikatorcascadingprogram-triwulan/index-triwulan-pkp-dev']) ?>">Triwulan</a> </li>
              </ul>
            </li>
            <li class="pc-item pc-hasmenu <?= $isPkpKegiatanActive ? 'active show' : '' ?>">
              <a href="#!" class="pc-link <?= $isPkpKegiatanActive ? 'active' : '' ?>">Target PK Perubahan Indikator Kegiatan<span class="pc-arrow"><i data-feather="chevron-right"></i></span></a>
              <ul class="pc-submenu <?= $isPkpKegiatanActive ? 'show' : '' ?>">
                <li class="pc-item <?= $currentRoute == 'sakip-indikatorcascadingkegiatan/index-tahunan-pkp-dev' ? 'active' : '' ?>"><a class="pc-link" href="<?= Url::to(['/sakip-indikatorcascadingkegiatan/index-tahunan-pkp-dev']) ?>">Tahunan</a></li>
                <li class="pc-item <?= $currentRoute == 'sakip-indikatorcascadingkegiatan-triwulan/index-triwulan-pkp-dev' ? 'active' : '' ?>"><a class="pc-link" href="<?= Url::to(['/sakip-indikatorcascadingkegiatan-triwulan/index-triwulan-pkp-dev']) ?>">Triwulan</a> </li>
              </ul>
            </li>
            <li class="pc-item pc-hasmenu <?= $isPkpSubkegiatanActive ? 'active show' : '' ?>">
              <a href="#!" class="pc-link <?= $isPkpSubkegiatanActive ? 'active' : '' ?>">Target PK Perubahan Indikator Sub Kegiatan<span class="pc-arrow"><i data-feather="chevron-right"></i></span></a>
              <ul class="pc-submenu <?= $isPkpSubkegiatanActive ? 'show' : '' ?>">
                <li class="pc-item <?= $currentRoute == 'sakip-indikatorcascadingsubkegiatan/index-tahunan-pkp-dev' ? 'active' : '' ?>"><a class="pc-link" href="<?= Url::to(['/sakip-indikatorcascadingsubkegiatan/index-tahunan-pkp-dev']) ?>">Tahunan</a></li>
                <li class="pc-item <?= $currentRoute == 'sakip-indikatorcascadingsubkegiatan-triwulan/index-triwulan-pkp-dev' ? 'active' : '' ?>"><a class="pc-link" href="<?= Url::to(['/sakip-indikatorcascadingsubkegiatan-triwulan/index-triwulan-pkp-dev']) ?>">Triwulan</a> </li>
              </ul>
            </li>
            <li class="pc-item <?= $currentRoute == 'sakip-indikatorcascadingprogram/index-anggaran-pkp-dev' ? 'active' : '' ?>"><a class="pc-link" href="<?= Url::to(['/sakip-indikatorcascadingprogram/index-anggaran-pkp-dev']) ?>">Anggaran Program PK Perubahan</a></li>
            <li class="pc-item <?= $currentRoute == 'sakip-indikatorcascadingkegiatan/index-anggaran-pkp-dev' ? 'active' : '' ?>"><a class="pc-link" href="<?= Url::to(['/sakip-indikatorcascadingkegiatan/index-anggaran-pkp-dev']) ?>">Anggaran Kegiatan PK Perubahan</a></li>
            <li class="pc-item <?= $currentRoute == 'sakip-indikatorcascadingsubkegiatan/index-anggaran-pkp-dev' ? 'active' : '' ?>"><a class="pc-link" href="<?= Url::to(['/sakip-indikatorcascadingsubkegiatan/index-anggaran-pkp-dev']) ?>">Anggaran Sub Kegiatan PK Perubahan</a></li>
          </ul>
        </li>

        <li class="pc-item pc-hasmenu <?= $isCapkinActive ? 'active show' : '' ?>">
          <a href="#!" class="pc-link <?= $isCapkinActive ? 'active' : '' ?>">
            <span class="pc-micon">
              <i class="ph-duotone ph-chart-line-up"></i>
            </span>
            <span class="pc-mtext">Capaian Kinerja</span><span class="pc-arrow"><i data-feather="chevron-right"></i></span></a>
          <ul class="pc-submenu <?= $isCapkinActive ? 'show' : '' ?>">
            <li class="pc-item pc-hasmenu <?= $isCapkinSasaranActive ? 'active show' : '' ?>">
              <a href="#!" class="pc-link <?= $isCapkinSasaranActive ? 'active' : '' ?>">Realisasi Indikator Sasaran<span class="pc-arrow"><i data-feather="chevron-right"></i></span></a>
              <ul class="pc-submenu <?= $isCapkinSasaranActive ? 'show' : '' ?>">
                <li class="pc-item <?= $currentRoute == 'sakip-indikatorsasaranrenstra-triwulan/index-triwulan-capaian-dev' ? 'active' : '' ?>"><a class="pc-link" href="<?= Url::to(['/sakip-indikatorsasaranrenstra-triwulan/index-triwulan-capaian-dev']) ?>">Triwulan</a></li>
                <li class="pc-item <?= $currentRoute == 'sakip-indikatorsasaranrenstra/index-tahunan-capaian-dev' ? 'active' : '' ?>"><a class="pc-link" href="<?= Url::to(['/sakip-indikatorsasaranrenstra/index-tahunan-capaian-dev']) ?>">Tahunan</a> </li>
              </ul>
            </li>
            <li class="pc-item pc-hasmenu <?= $isCapkinProgramActive ? 'active show' : '' ?>">
              <a href="#!" class="pc-link <?= $isCapkinProgramActive ? 'active' : '' ?>">Realisasi Indikator Program<span class="pc-arrow"><i data-feather="chevron-right"></i></span></a>
              <ul class="pc-submenu <?= $isCapkinProgramActive ? 'show' : '' ?>">
                <li class="pc-item <?= $currentRoute == 'sakip-indikatorcascadingprogram-triwulan/index-triwulan-capaian-dev' ? 'active' : '' ?>"><a class="pc-link" href="<?= Url::to(['/sakip-indikatorcascadingprogram-triwulan/index-triwulan-capaian-dev']) ?>">Triwulan</a></li>
                <li class="pc-item <?= $currentRoute == 'sakip-indikatorcascadingprogram/index-tahunan-capaian-dev' ? 'active' : '' ?>"><a class="pc-link" href="<?= Url::to(['/sakip-indikatorcascadingprogram/index-tahunan-capaian-dev']) ?>">Tahunan</a> </li>
              </ul>
            </li>
            <li class="pc-item pc-hasmenu <?= $isCapkinKegiatanActive ? 'active show' : '' ?>">
              <a href="#!" class="pc-link <?= $isCapkinKegiatanActive ? 'active' : '' ?>">Realisasi Indikator Kegiatan<span class="pc-arrow"><i data-feather="chevron-right"></i></span></a>
              <ul class="pc-submenu <?= $isCapkinKegiatanActive ? 'show' : '' ?>">
                <li class="pc-item <?= $currentRoute == 'sakip-indikatorcascadingkegiatan-triwulan/index-triwulan-capaian-dev' ? 'active' : '' ?>"><a class="pc-link" href="<?= Url::to(['/sakip-indikatorcascadingkegiatan-triwulan/index-triwulan-capaian-dev']) ?>">Triwulan</a></li>
                <li class="pc-item <?= $currentRoute == 'sakip-indikatorcascadingkegiatan/index-tahunan-capaian-dev' ? 'active' : '' ?>"><a class="pc-link" href="<?= Url::to(['/sakip-indikatorcascadingkegiatan/index-tahunan-capaian-dev']) ?>">Tahunan</a> </li>
              </ul>
            </li>
            <li class="pc-item pc-hasmenu <?= $isCapkinSubkegiatanActive ? 'active show' : '' ?>">
              <a href="#!" class="pc-link <?= $isCapkinSubkegiatanActive ? 'active' : '' ?>">Realisasi Indikator Sub Kegiatan<span class="pc-arrow"><i data-feather="chevron-right"></i></span></a>
              <ul class="pc-submenu <?= $isCapkinSubkegiatanActive ? 'show' : '' ?>">
                <li class="pc-item <?= $currentRoute == 'sakip-indikatorcascadingsubkegiatan-triwulan/index-triwulan-capaian-dev' ? 'active' : '' ?>"><a class="pc-link" href="<?= Url::to(['/sakip-indikatorcascadingsubkegiatan-triwulan/index-triwulan-capaian-dev']) ?>">Triwulan</a></li>
                <li class="pc-item <?= $currentRoute == 'sakip-indikatorcascadingsubkegiatan/index-tahunan-capaian-dev' ? 'active' : '' ?>"><a class="pc-link" href="<?= Url::to(['/sakip-indikatorcascadingsubkegiatan/index-tahunan-capaian-dev']) ?>">Tahunan</a> </li>
              </ul>
            </li>
            <li class="pc-item <?= $currentRoute == 'sakip-indikatorcascadingsubkegiatan-triwulan/index-triwulan-penyerapan-dev' ? 'active' : '' ?>"><a class="pc-link" href="<?= Url::to(['/sakip-indikatorcascadingsubkegiatan-triwulan/index-triwulan-penyerapan-dev']) ?>">Penyerapan Anggaran</a></li>
          </ul>
        </li>

        <li class="pc-item pc-hasmenu <?= $isLaporanActive ? 'active show' : '' ?>">
          <a href="#!" class="pc-link <?= $isLaporanActive ? 'active' : '' ?>">
            <span class="pc-micon">
              <i class="ph-duotone ph-file-text"></i>
            </span>
            <span class="pc-mtext">Laporan</span><span class="pc-arrow"><i data-feather="chevron-right"></i></span></a>
          <ul class="pc-submenu <?= $isLaporanActive ? 'show' : '' ?>">
            <li class="pc-item <?= $currentRoute == 'laporan/index-laporan-renstra-dev' ? 'active' : '' ?>"><a class="pc-link" href="<?= Url::to(['/laporan/index-laporan-renstra-dev']) ?>">Renstra</a></li>
            <li class="pc-item <?= $currentRoute == 'laporan/index-laporan-renja-tahunan-dev' ? 'active' : '' ?>"><a class="pc-link" href="<?= Url::to(['/laporan/index-laporan-renja-tahunan-dev']) ?>">Rencana Kinerja Tahunan</a></li>
            <li class="pc-item <?= $currentRoute == 'laporan/index-laporan-iku-dev' ? 'active' : '' ?>"><a class="pc-link" href="<?= Url::to(['/laporan/index-laporan-iku-dev']) ?>">Indikator Kinerja Utama</a></li>
            <li class="pc-item <?= $currentRoute == 'laporan/index-laporan-tapkin-dev' ? 'active' : '' ?>"><a class="pc-link" href="<?= Url::to(['/laporan/index-laporan-tapkin-dev']) ?>">Perjanjian Kinerja</a></li>
            <li class="pc-item <?= $currentRoute == 'laporan/index-laporan-capkin-iku-dev' ? 'active' : '' ?>"><a class="pc-link" href="<?= Url::to(['/laporan/index-laporan-capkin-iku-dev']) ?>">Capaian Indikator Kinerja Utama</a></li>
            <li class="pc-item <?= $currentRoute == 'laporan/index-laporan-realisasi-anggaran-dev' ? 'active' : '' ?>"><a class="pc-link" href="<?= Url::to(['/laporan/index-laporan-realisasi-anggaran-dev']) ?>">Pagu dan Realisasi Anggaran</a></li>
            <li class="pc-item pc-hasmenu <?= $isLaporanAnalisisActive ? 'active show' : '' ?>">
              <a href="#!" class="pc-link <?= $isLaporanAnalisisActive ? 'active' : '' ?>">Analisis Pencapaian Sasaran<span class="pc-arrow"><i data-feather="chevron-right"></i></span></a>
              <ul class="pc-submenu <?= $isLaporanAnalisisActive ? 'show' : '' ?>">
                <li class="pc-item <?= $currentRoute == 'laporan/index-laporan-analisis-sasaran-triwulan-dev' ? 'active' : '' ?>"><a class="pc-link" href="<?= Url::to(['/laporan/index-laporan-analisis-sasaran-triwulan-dev']) ?>">Triwulan</a></li>
                <li class="pc-item <?= $currentRoute == 'laporan/index-laporan-analisis-sasaran-tahunan-dev' ? 'active' : '' ?>"><a class="pc-link" href="<?= Url::to(['/laporan/index-laporan-analisis-sasaran-tahunan-dev']) ?>">Tahunan</a> </li>
              </ul>
            </li>
            <li class="pc-item <?= $currentRoute == 'laporan/index-laporan-rencana-aksi-dev' ? 'active' : '' ?>"><a class="pc-link" href="<?= Url::to(['/laporan/index-laporan-rencana-aksi-dev']) ?>">Rencana Aksi</a></li>
            <li class="pc-item <?= $currentRoute == 'laporan/index-laporan-ekinerja-dev' ? 'active' : '' ?>"><a class="pc-link" href="<?= Url::to(['/laporan/index-laporan-ekinerja-dev']) ?>">Efisiensi dan Efektifitas Kinerja</a></li>
            <li class="pc-item <?= $currentRoute == 'laporan/index-evaluasi-rkpd-dev' ? 'active' : '' ?>"><a class="pc-link" href="<?= Url::to(['/laporan/index-evaluasi-rkpd-dev']) ?>">Evaluasi RKPD</a></li>
            <li class="pc-item <?= $currentRoute == 'sakip-evaluasi-renja/index' ? 'active' : '' ?>"><a class="pc-link" href="<?= Url::to(['/sakip-evaluasi-renja/index']) ?>">Evaluasi Renja</a></li>

          </ul>
        </li>

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
                          <h6 class="mb-0 text-truncate">Keefe Bond added new tags to 💪 Design system</h6>
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
                          <h6 class="mb-0 text-truncate">Keefe Bond added new tags to 💪 Design system</h6>
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
                        <?php if (!Yii::$app->user->isGuest): ?>
                          <a class="link-primary" href="mailto:<?= Yii::$app->user->identity->email ?>">
                            <?= Yii::$app->user->identity->email ?>
                          </a>
                        <?php endif; ?>

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
                    <a href="<?= Url::to(['site/change-profile', 'id' => Yii::$app->user->id]) ?>" class="dropdown-item" data-method="post">
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
    <div class="modal-content">
      <!-- No header -->
      <div class="modal-body" id="modalContent">
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