<style>
    #heroCarousel img {
        width: 350px;
        /* Pastikan gambar memenuhi lebar container */
        height: 550px;
        /* Tetapkan tinggi konsisten */

    }
</style>

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

$this->title = 'My Yii Application';
?>


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

<div id="full-slider-wrapper">
    <div id="layerslider" style="width:100%;height:750px;">
        <!-- first slide -->
        <div class="ls-slide" data-ls="slidedelay: 5000; transition2d:85;">
            <img src="<?= Url::base(true) ?>/udema/bappeda/ds-15.jpg" class="ls-bg" alt="Slide background">
            <h3 class="ls-l slide_typo" style="top: 47%; left: 50%;" data-ls="offsetxin:0;durationin:2000;delayin:1000;easingin:easeOutElastic;rotatexin:90;transformoriginin:50% bottom 0;offsetxout:0;rotatexout:90;transformoriginout:50% bottom 0;"><strong></strong></h3>
            <p class="ls-l slide_typo_2" style="top:55%; left:50%;" data-ls="durationin:2000;delayin:1000;easingin:easeOutElastic;">
            </p>
            <a class="ls-l" style="top:65%; left:50%;white-space: nowrap;" data-ls="durationin:2000;delayin:1400;easingin:easeOutElastic;" href='courses-grid.html'></a>
        </div>
        <!-- second slide -->
        <div class="ls-slide" data-ls="slidedelay:5000; transition2d:103;">
            <img src="<?= Url::base(true) ?>/udema/bappeda/ds.png" class="ls-bg" alt="Slide background">
            <h3 class="ls-l slide_typo" style="top: 47%; left: 50%;" data-ls="offsetxin:0;durationin:2000;delayin:1000;easingin:easeOutElastic;rotatexin:90;transformoriginin:50% bottom 0;offsetxout:0;rotatexout:90;transformoriginout:50% bottom 0;"><strong></strong></h3>
            <p class="ls-l slide_typo_2" style="top:55%; left:50%;" data-ls="durationin:2000;delayin:1000;easingin:easeOutElastic;">
            </p>
            <a class="ls-l" style="top:65%; left:50%;white-space: nowrap;" data-ls="durationin:2000;delayin:1400;easingin:easeOutElastic;" href='courses-grid.html'></a>
        </div>
        <!-- third slide -->
        <div class="ls-slide" data-ls="slidedelay: 5000; transition2d:5;">
            <img src="<?= Url::base(true) ?>/udema/bappeda/dokrenbang.jpg" class="ls-bg" alt="Slide background">
            <h3 class="ls-l slide_typo" style="top:47%; left: 50%; background-color:black;" data-ls="offsetxin:0;durationin:2000;delayin:1000;easingin:easeOutElastic;rotatexin:90;transformoriginin:50% bottom 0;offsetxout:0;rotatexout:90;transformoriginout:50% bottom 0;"><strong>Aplikasi</strong> Dokrenbang</h3>
            <p class="ls-l slide_typo_2" style="top:55%; left:50%; background-color:black;" data-ls="durationin:2000;delayin:1000;easingin:easeOutElastic;">
                Dokumen Perencanaan Pembangunan
            </p>
            <a class="ls-l btn_1 rounded" style="top:65%; left:50%; background-color:#4974b1" data-ls="durationin:2000;delayin:1400;easingin:easeOutElastic;" href='https://dokrenbang.deliserdangkab.go.id/' target="_blank" rel="noopener noreferrer">Explore</a>
        </div>
        <!-- fourth slide -->
        <div class="ls-slide" data-ls="slidedelay: 5000; transition2d:85;">
            <img src="<?= Url::base(true) ?>/udema/bappeda/simona.jpg" class="ls-bg" alt="Slide background">
            <h3 class="ls-l slide_typo" style="top:47%; left: 50%; background-color:black;" data-ls="offsetxin:0;durationin:2000;delayin:1000;easingin:easeOutElastic;rotatexin:90;transformoriginin:50% bottom 0;offsetxout:0;rotatexout:90;transformoriginout:50% bottom 0;"><strong>Aplikasi</strong> Simona</h3>
            <p class="ls-l slide_typo_2" style="top:55%; left:50%; background-color:black;" data-ls="durationin:2000;delayin:1000;easingin:easeOutElastic;">
                Sistem Monitoring Perencanaan
            </p>
            <a class="ls-l btn_1 rounded" style="top:65%; left:50%; background-color:#4974b1" data-ls="durationin:2000;delayin:1400;easingin:easeOutElastic;" href='https://simona.deliserdangkab.go.id/' target="_blank" rel="noopener noreferrer">Explore</a>
        </div>
        <!-- fifth slide -->
        <div class="ls-slide" data-ls="slidedelay: 5000; transition2d:103;">
            <img src="<?= Url::base(true) ?>/udema/bappeda/ikmds.jpg" class="ls-bg" alt="Slide background">
            <h3 class="ls-l slide_typo" style="top:47%; left: 50%; background-color:black;" data-ls="offsetxin:0;durationin:2000;delayin:1000;easingin:easeOutElastic;rotatexin:90;transformoriginin:50% bottom 0;offsetxout:0;rotatexout:90;transformoriginout:50% bottom 0;"><strong>Aplikasi</strong> IKM</h3>
            <p class="ls-l slide_typo_2" style="top:55%; left:50%;  background-color:black;" data-ls="durationin:2000;delayin:1000;easingin:easeOutElastic;">
                Indeks Kepuasan Masyarakat
            </p>
            <a class="ls-l btn_1 rounded" style="top:65%; left:50%; background-color:#4974b1" data-ls="durationin:2000;delayin:1400;easingin:easeOutElastic;" href='https://ikmds.deliserdangkab.go.id/' target="_blank" rel="noopener noreferrer">Explore</a>
        </div>
    </div>
</div>

<div id="carouselExampleIndicators" class="carousel slide d-xl-block d-lg-block d-md-block d-sm-block" data-bs-ride="carousel">
    <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="<?= Url::base(true) ?>/lightapp/assets/images/ds-15.jpg" class="d-block w-100" alt="Slide 1">
                <div class="carousel-caption d-none d-md-block">
                    <h5>e-SAKIP</h5>
                    <p>Sistem Akuntabilitas Kinerja Instansi Pemerintah Online</p>
                </div>
            </div>
            <div class="carousel-item">
                <img src="<?= Url::base(true) ?>/lightapp/assets/images/carousel-2.png" class="d-block w-100" alt="Slide 2">
                <div class="carousel-caption d-none d-md-block">
                    <h5>e-SAKIP</h5>
                    <p>Sistem Akuntabilitas Kinerja Instansi Pemerintah Online</p>
                </div>
            </div>
            <div class="carousel-item">
                <img src="<?= Url::base(true) ?>/lightapp/assets/images/carousel-3.jpg" class="d-block w-100" alt="Slide 3">
                <div class="carousel-caption d-none d-md-block">
                    <h5>e-SAKIP</h5>
                    <p>Sistem Akuntabilitas Kinerja Instansi Pemerintah Online</p>
                </div>
            </div>
            <div class="carousel-item">
                <img src="<?= Url::base(true) ?>/lightapp/assets/images/carousel-4.jpg" class="d-block w-100" alt="Slide 4">
                <div class="carousel-caption d-none d-md-block">
                    <h5>e-SAKIP</h5>
                    <p>Sistem Akuntabilitas Kinerja Instansi Pemerintah Online</p>
                </div>
            </div>
            <div class="carousel-item">
                <img src="<?= Url::base(true) ?>/lightapp/assets/images/carousel-5.jpg" class="d-block w-100" alt="Slide 5">
                <div class="carousel-caption d-none d-md-block">
                    <h5>e-SAKIP</h5>
                    <p>Sistem Akuntabilitas Kinerja Instansi Pemerintah Online</p>
                </div>
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>
</div>


<div class="container p-2">
    <div class="row">

        <div class="col-md-12">

            <!-- Menu list -->

            <div class="d-flex justify-content-center">
                <ul class="list-unstyled d-flex gap-3">

                    <li class="menu-icon card shadow p-1" style="max-width: 150px;">
                        <a href="<?= Url::to(['/site/index-esakip']) ?>" target="_blank" class="text-decoration-none">
                            <img src="<?= Url::base(true) ?>/lightapp/assets/images/esakip.gif" class="card-img-top" alt="e-Sakip">
                        </a>
                        <span class="p-2 text-center"><b>e-SAKIP</b></span>
                    </li>

                    <li class="menu-icon card shadow p-1" style="max-width: 150px;">
                        <a href="<?= Url::to(['/site/index-simona']) ?>" target="_blank" class="text-decoration-none">
                            <img src="<?= Url::base(true) ?>/lightapp/assets/images/perencanaan.gif" class="card-img-top" alt="Perencanaan">
                        </a>
                        <span class="p-2 text-center"><b>Monitoring<br>Perencanaan</b></span>
                    </li>

                    <li class="menu-icon card shadow p-1" style="max-width: 150px;">
                        <a href="<?= Url::to(['/site/index-dokrenbang']) ?>" target="_blank" class="text-decoration-none">
                            <img src="<?= Url::base(true) ?>/lightapp/assets/images/dokrenbang-2.gif" class="card-img-top" alt="Dokumen">
                        </a>
                        <span class="p-2 text-center"><b>Dokumen<br>Perencanaan</b></span>
                    </li>

                </ul>
            </div>

            <!-- Menu list -->

        </div>
    </div>

</div>


<div class="container mt-1">
    <div class="row">

        <div class="col-md-12">

            <!-- Menu list -->

            <div class="d-flex justify-content-center">
                <ul class="list-unstyled d-flex gap-3">

                    <li class="menu-icon card shadow p-1" style="max-width: 130px;">
                        <a href="https://portal.deliserdangkab.go.id/" target="_blank" class="text-decoration-none">
                            <img src="<?= Url::base(true) ?>/lightapp/assets/images/bappeda.png" class="cpns card-img-top py-1 px-2" alt="Portal DS">
                        </a>
                        <span class="p-2 text-center"><b>Portal<br>Deli Serdang</b></span>
                    </li>

                    <li class="menu-icon card shadow p-1" style="max-width: 130px;">
                        <a href="https://bappedalitbang.deliserdangkab.go.id/" target="_blank" class="text-decoration-none">
                            <img src="<?= Url::base(true) ?>/lightapp/assets/images/bappeda.png" class="pilkada-2024 card-img-top pt-1 pb-0 px-2" alt="Bappeda DS">
                        </a>
                        <span class="p-2 text-center"><b>Website<br>Bappedalitbang</b></span>
                    </li>

                </ul>
            </div>

            <!-- Menu list -->

        </div>
    </div>

</div>


<!-- <div class="container mt-2">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-center">
                <ul class="list-unstyled d-flex gap-3">
                    <li class="menu-icon card shadow p-1" style="max-width: 100px;">
                        <a href="https://elhkpn.kpk.go.id/" target="_blank">
                            <img src="https://portal.deliserdangkab.go.id/wp-content/berkas/1626770322.jpg" class="card-img-top" alt="eLHKPN">
                        </a>
                    </li>
                    <li class="menu-icon card shadow p-1" style="max-width: 100px;">
                        <a href="https://www.kpk.go.id/gratifikasi/" target="_blank">
                            <img src="https://portal.deliserdangkab.go.id/wp-content/berkas/1626770400.jpg" class="card-img-top" alt="Gratifikasi">
                        </a>
                    </li>
                    <li class="menu-icon card shadow p-1" style="max-width: 100px;">
                        <a href="https://kws.kpk.go.id/" target="_blank">
                            <img src="https://portal.deliserdangkab.go.id/wp-content/berkas/1626770467.jpg" class="card-img-top" alt="KWS">
                        </a>
                    </li>
                    <li class="menu-icon card shadow p-1" style="max-width: 100px;">
                        <a href="https://acch.kpk.go.id/" target="_blank">
                            <img src="https://portal.deliserdangkab.go.id/wp-content/berkas/1626770550.jpg" class="card-img-top" alt="ACCH">
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div> -->



<div class="modal fade" id="modalToggle" aria-labelledby="exampleModalToggleLabel" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-display modal-content">

            <div class="modal-body">
                <h4 class="text-center livvic">
                    <img src="https://portal.deliserdangkab.go.id/wp-content/berkas/1723716696.png" style="width: 100px;">
                </h4>
                <h5 class="text-center livvic fw-bold mb-1">Bappedalitbang Deli Serdang<br><span>@</span></h5>
                <div class="container justify-content-center d-flex p-4">
                    <ul class="list-unstyled d-flex gap-4">

                        <li>
                            <a href="https://www.instagram.com/pemkab.deliserdang/" target="_blank" class="card bg-transparent border-0 p-2">
                                <h2 class="modal-socicon"><i class="bi bi-instagram"></i></h2>
                            </a>
                        </li>
                        <li>
                            <a href="https://www.youtube.com/channel/UCbdyQVBZnpJPPZSKS8ZocUw" target="_blank" class="card bg-transparent border-0 p-2">
                                <h2 class="modal-socicon"><i class="bi bi-youtube"></i></h2>
                            </a>
                        </li>
                        <li>
                            <a href="https://www.twitter.com/DeliSerdang_Kab" target="_blank" class="card bg-transparent border-0 p-2">
                                <h2 class="modal-socicon"><i class="bi bi-twitter-x"></i></h2>
                            </a>
                        </li>
                        <li>
                            <a href="https://www.facebook.com/deliserdangkab/" target="_blank" class="card bg-transparent border-0 p-2">
                                <h2 class="modal-socicon"><i class="bi bi-facebook"></i></h2>
                            </a>
                        </li>
                        <li>
                            <a href="https://www.tiktok.com/@pemkab.deliserdang" target="_blank" class="card bg-transparent border-0 p-2">
                                <h2 class="modal-socicon"><i class="bi bi-tiktok"></i></h2>
                            </a>
                        </li>

                    </ul>

                </div>
                <a class="card m-2 p-2 text-decoration-none text-center shadow border-0 text-light" style="background: rgb(0,107,213);background: radial-gradient(circle, rgba(0,107,213,1) 0%, rgba(0,0,153,1) 83%, rgba(0,25,76,1) 100%);" href="https://portal.deliserdangkab.go.id/wp-content/berkas/1726723199.pdf" target="_none">
                    <p>Lihat Pengumuman Resmi Hasil Akhir Seleksi Administrasi CPNS di Lingkungan Pemerintah Kabupaten Deli Serdang Tahun Anggaran 2024</p>
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
</script>