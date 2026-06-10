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


<marquee class="pt-1 pb-1 text-muted bg-light">
    <strong class="text-info d-flex mx-5 gap-5">
        <div>
            <i class="bi bi-browser-edge"></i> Selamat datang di Portal e-Sakip Deli Serdang
        </div>
        <div>
            <i class="bi bi-globe-central-south-asia"></i> Kunjungi website Bappedalitbang Deli Serdang <a class="text-decoration-none" href="https://bappedalitbang.deliserdangkab.go.id" target="_blank">disini</a>.
        </div>
    </strong>
</marquee>
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
                    <a class="nav-link" style="white-space: nowrap" href="<?= Url::to(['/site/portal']) ?>"><i class="bi bi-house-door-fill"></i> Home</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" style="white-space: nowrap" href="<?= Url::to(['/site/portal-publik']) ?>"><i class="bi bi-people-fill"></i> e-SAKIP Publik</a>
                </li>

                <!-- <li class="nav-item">
                    <a class="nav-link" style="white-space: nowrap" href=""><i class="bi bi-envelope-at-fill"></i> Dokumen</a>
                </li> -->

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarProfil" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-file-earmark-pdf-fill"></i> Dokumen
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