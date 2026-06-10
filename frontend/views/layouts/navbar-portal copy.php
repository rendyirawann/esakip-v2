<?php

/** @var \yii\web\View $this */
/** @var string $content */

use common\widgets\Alert;
use frontend\assets\MainPortalAsset;
use yii\bootstrap5\Breadcrumbs;
use yii\bootstrap5\Html;
use yii\helpers\Url;
use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;

MainPortalAsset::register($this);
?>
<!-- <div class="modal fade" id="modalToggle" aria-labelledby="exampleModalToggleLabel" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-display modal-content">

            <div class="modal-body">
                <h4 class="text-center livvic">
                    <img src="<?= Url::base(true) ?>/lightapp/assets/images/follow.jpg" style="width: 100px;">
                </h4>
                <h5 class="text-center livvic fw-bold mb-1">Bappedalitbang Deli Serdang<br><span>@</span></h5>
                <div class="container justify-content-center d-flex p-4">
                    <ul class="list-unstyled d-flex gap-4">

                        <li>
                            <a href="#" target="_blank" class="card bg-transparent border-0 p-2">
                                <h2 class="modal-socicon"><i class="bi bi-instagram"></i></h2>
                            </a>
                        </li>
                        <li>
                            <a href="#" target="_blank" class="card bg-transparent border-0 p-2">
                                <h2 class="modal-socicon"><i class="bi bi-youtube"></i></h2>
                            </a>
                        </li>
                        <li>
                            <a href="#" target="_blank" class="card bg-transparent border-0 p-2">
                                <h2 class="modal-socicon"><i class="bi bi-twitter-x"></i></h2>
                            </a>
                        </li>
                        <li>
                            <a href="#" target="_blank" class="card bg-transparent border-0 p-2">
                                <h2 class="modal-socicon"><i class="bi bi-facebook"></i></h2>
                            </a>
                        </li>
                        <li>
                            <a href="#" target="_blank" class="card bg-transparent border-0 p-2">
                                <h2 class="modal-socicon"><i class="bi bi-tiktok"></i></h2>
                            </a>
                        </li>

                    </ul>

                </div>
                <a class="card m-2 p-2 text-decoration-none text-center shadow border-0 text-light" style="background: rgb(13,202,240); background: radial-gradient(circle, rgba(13,202,240,1) 0%, rgba(0,102,204,1) 83%, rgba(0,25,76,1) 100%);" href="https://portal.deliserdangkab.go.id/wp-content/berkas/1726723199.pdf" target="_none">
                    <p>Sistem Akuntabilitas Kinerja Instansi Pemerintah Online</p>
                </a>

            </div>

        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const myModal = new bootstrap.Modal('#modalToggle', {
            keyboard: false
        });
        myModal.show();
    });
</script> -->
<div id="preloader">
    <div data-loader="circle-side"></div>
</div>
<!-- End Preload -->

<header class="header fadeInDown">
    <div id="logo">
        <a href="<?= Url::to(['site/index']) ?>"><img src="<?= Url::base(true) ?>/udema/bappeda/bappeda.png" width="32" alt=""></a> e-SAKIP Portal
    </div>
    <ul id="top_menu" style="color:black;">
        <li><a href="<?= Url::to(['site/index']) ?>" class="login">Home</a></li>
        <li><a href="<?= Url::to(['berita/index']) ?>" class="search-overlay-menu-btn"></a></li>
        <li class="hidden_tablet"><a href="<?= Url::to(['galeri/index']) ?>">Galeri</a></li>
        <li class="hidden_tablet"><a href="http://ppid.deliserdangkab.go.id/" target="_blank" rel="noopener noreferrer">PPID</a></li>
        <!-- <li class="hidden_tablet"><a href="admission.html" class="btn_1 rounded">Admission</a></li> -->
        <li>
            <div class="hamburger hamburger--spin">
                <div class="hamburger-box">
                    <div class="hamburger-inner"></div>
                </div>
            </div>
        </li>
    </ul>
    <!-- /top_menu -->
</header>

<div id="main_menu">
    <div class="container">
        <nav class="version_2">
            <div class="row">
                <div class="col-md-3">
                    <h3><a href="<?= Url::to(['site/index']) ?>" style="text-decoration:none; color:white;">Home</a></h3>
                </div>
                <div class="col-md-3">
                    <h3><a href="<?= Url::to(['berita/index']) ?>" style="text-decoration:none; color:white;">Berita Perencanaan</a></h3>
                    <ul>
                        <li><a href="<?= Url::to(['berita/index']) ?>">Berita Bidang PPED</a></li>
                        <li><a href="<?= Url::to(['berita/index']) ?>">Berita Bidang Infrastruktur dan Kewilayahan</a></li>
                        <li><a href="<?= Url::to(['berita/index']) ?>">Berita Bidang Penelitian dan Pengembangan</a></li>
                        <li><a href="<?= Url::to(['berita/index']) ?>">Berita Bidang Umum</a></li>
                        <li><a href="<?= Url::to(['berita/index']) ?>">Berita Bidang Program</a></li>
                        <li><a href="<?= Url::to(['berita/index']) ?>">Berita Bidang Keuangan</a></li>
                        <li><a href="<?= Url::to(['berita/index']) ?>">Berita Bidang Ekonomi dan SDA</a></li>
                        <li><a href="<?= Url::to(['berita/index']) ?>">Berita Bidang PPM</a></li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h3>Profil</h3>
                    <ul>
                        <li><a href="<?= Url::to(['visimisi/index']) ?>">Visi dan Misi</a></li>
                        <li><a href="<?= Url::to(['struktur/index']) ?>">Struktur Organisasi</a></li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h3>Galeri</h3>
                    <ul>
                        <li><a href="<?= Url::to(['galeri/index']) ?>">Galeri Kegiatan</a></li>
                    </ul>
                </div>
            </div>
            <!-- /row -->
        </nav>
        <div class="follow_us">
            <ul>
                <li>Follow us</li>
                <li><a href="https://web.facebook.com/pages/Bappeda-Deli-Serdang/366920706655586" target="_blank" rel="noopener noreferrer"><i class="bi bi-facebook"></i></a></li>
                <li><a href="https://www.instagram.com/bappedalitbangds/" target="_blank" rel="noopener noreferrer"><i class="bi bi-instagram"></i></a></li>
            </ul>
        </div>
    </div>
</div>
<!-- /main_menu -->

<nav class="navbar navbar-expand-lg navbar-dark shadow" style="background: rgb(13,202,240); background: radial-gradient(circle, rgba(13,202,240,1) 0%, rgba(0,102,204,1) 83%, rgba(0,25,76,1) 100%);">
    <div class="container">
        <a class="navbar-brand mt-2" href="<?= Url::to(['/site/index-portal']) ?>" style="color:#fff;height:60px;">
            <img src="<?= Url::base(true) ?>/lightapp/assets/images/bappeda.png" alt="" style="height:40px;"> e-SAKIP Portal
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">

            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">

                <!-- <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarProfil" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-globe-central-south-asia"></i> Profil
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="navbarProfil">
                        <li><a class="dropdown-item" href="https://portal.deliserdangkab.go.id/peta-deli-serdang.html">Peta Deli Serdang</a></li>
                        <li><a class="dropdown-item" href="https://portal.deliserdangkab.go.id/sejarah.html">Sejarah Kab. Deli Serdang</a></li>
                        <li><a class="dropdown-item" href="https://portal.deliserdangkab.go.id/visi-misi.html">Visi dan Misi</a></li>
                        <li><a class="dropdown-item" href="https://portal.deliserdangkab.go.id/prestasi.html">Prestasi</a></li>
                        <li><a class="dropdown-item" href="https://portal.deliserdangkab.go.id/lambang-dan-moto-daerah.html">Lambang Daerah</a></li>
                        <li><a class="dropdown-item" href="https://portal.deliserdangkab.go.id/profil-pimpinan-daerah.html">Profil Pimpinan</a></li>
                        <li><a class="dropdown-item" href="https://portal.deliserdangkab.go.id/wp-content/berkas/1715761065.pdf">Tugas Fungsi</a></li>
                        <li><a class="dropdown-item" href="https://portal.deliserdangkab.go.id/struktur-organisasi.html">Struktur Organisasi</a></li>
                    </ul>
                </li> -->

                <li class="nav-item">
                    <a class="nav-link" style="white-space: nowrap" href="<?= Url::to(['/site/portal']) ?>"><i class="bi bi-file-earmark-text-fill"></i> Home</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" style="white-space: nowrap" href="<?= Url::to(['/site/portal-publik']) ?>"><i class="bi bi-info-square-fill"></i> Tabulasi e-SAKIP</a>
                </li>

                <!-- <li class="nav-item">
                    <a class="nav-link" style="white-space: nowrap" href=""><i class="bi bi-envelope-at-fill"></i> Dokumen</a>
                </li> -->

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarProfil" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-envelope-at-fill"></i> Dokumen
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="navbarProfil">
                        <li><a class="dropdown-item" href="<?= Url::to(['/site/portal-dokumen-kegiatan']) ?>">Dokumen Kegiatan</a></li>
                        <li><a class="dropdown-item" href="<?= Url::to(['/site/portal-dokumen-subkegiatan']) ?>">Dokumen Sub Kegiatan</a></li>
                    </ul>
                </li>

                <li class="nav-item">
                    <a class="nav-link" style="white-space: nowrap" href="<?= Url::to(['/site/login']) ?>" target="_blank"><i class="bi bi-person-badge"></i> Login</a>
                </li>


            </ul>

        </div>

    </div>
</nav>
<!-- /header -->