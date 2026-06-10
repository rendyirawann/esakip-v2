<?php

/** @var yii\web\View $this */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'e-SAKIP Deli Serdang';
$base = Url::base(true);
?>

<!-- ============ CAROUSEL — full width, gambar tampil PENUH (tidak terpotong) ============ -->
<div id="portalCarousel" class="carousel slide esk-carousel" data-bs-ride="carousel">
    <div class="carousel-indicators">
        <button type="button" data-bs-target="#portalCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
        <button type="button" data-bs-target="#portalCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
        <button type="button" data-bs-target="#portalCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
        <button type="button" data-bs-target="#portalCarousel" data-bs-slide-to="3" aria-label="Slide 4"></button>
        <button type="button" data-bs-target="#portalCarousel" data-bs-slide-to="4" aria-label="Slide 5"></button>
    </div>
    <div class="carousel-inner">
        <div class="carousel-item active" data-bs-interval="5000">
            <img src="<?= $base ?>/udema/bappeda/bappeda/ds-new20205.jpg" class="d-block w-100" alt="Pelantikan Bupati &amp; Wakil Bupati Kabupaten Deli Serdang Periode 2025-2030">
        </div>
        <div class="carousel-item" data-bs-interval="5000">
            <img src="<?= $base ?>/udema/bappeda/bappeda/ds-new20205_4.jpg" class="d-block w-100" alt="Website Bappedalitbang Deli Serdang">
        </div>
        <div class="carousel-item" data-bs-interval="5000">
            <img src="<?= $base ?>/lightapp/assets/images/carousel-3.jpg" class="d-block w-100" alt="Aplikasi eSAKIP">
        </div>
        <div class="carousel-item" data-bs-interval="5000">
            <img src="<?= $base ?>/lightapp/assets/images/carousel-4.jpg" class="d-block w-100" alt="Aplikasi eSAKIP">
        </div>
        <div class="carousel-item" data-bs-interval="5000">
            <img src="<?= $base ?>/lightapp/assets/images/carousel-5.jpg" class="d-block w-100" alt="Aplikasi eSAKIP">
        </div>
    </div>
    <button class="carousel-control-prev" type="button" data-bs-target="#portalCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Sebelumnya</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#portalCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Berikutnya</span>
    </button>
</div>
<!-- End carousel -->

<div class="esk-portal">

    <!-- ============ LAYANAN PUBLIK ============ -->
    <section class="esk-portal-section">
        <div class="esk-section-head">
            <span class="eyebrow">Layanan Publik</span>
            <h2>Menu e-SAKIP Publik</h2>
            <p>Akses informasi kinerja yang terbuka untuk masyarakat</p>
        </div>

        <div class="esk-pub-grid">
            <a class="esk-pub-card" href="<?= Url::to(['/site/portal-publik-tabulasi']) ?>">
                <span class="esk-pub-ic"><i class="bi bi-table"></i></span>
                <span class="t">Tabulasi</span>
                <span class="s">Tabulasi capaian sasaran per SKPD</span>
            </a>
            <a class="esk-pub-card" href="<?= Url::to(['/site/portal-publik-perencanaan']) ?>">
                <span class="esk-pub-ic"><i class="bi bi-diagram-3"></i></span>
                <span class="t">Perencanaan</span>
                <span class="s">Cascading perencanaan kinerja</span>
            </a>
            <a class="esk-pub-card" href="<?= Url::to(['/site/portal-publik-capkin']) ?>">
                <span class="esk-pub-ic"><i class="bi bi-graph-up-arrow"></i></span>
                <span class="t">Capaian Kinerja</span>
                <span class="s">Realisasi &amp; capaian indikator</span>
            </a>
            <a class="esk-pub-card" href="<?= Url::to(['/site/portal-publik-evaluasi-renja']) ?>">
                <span class="esk-pub-ic"><i class="bi bi-clipboard-data"></i></span>
                <span class="t">Evaluasi Renja</span>
                <span class="s">Hasil evaluasi Rencana Kerja</span>
            </a>
        </div>
    </section>

    <!-- ============ TAUTAN TERKAIT ============ -->
    <section class="esk-portal-section">
        <div class="esk-section-head">
            <span class="eyebrow">Tautan</span>
            <h2>Tautan Terkait</h2>
            <p>Situs resmi Pemerintah Kabupaten Deli Serdang</p>
        </div>

        <div class="esk-link-grid">
            <a class="esk-link-card" href="https://portal.deliserdangkab.go.id/" target="_blank" rel="noopener">
                <span class="ic"><img src="<?= $base ?>/lightapp/assets/images/bappeda.png" alt=""></span>
                <span>
                    <span class="t d-block">Portal Deli Serdang</span>
                    <span class="s">portal.deliserdangkab.go.id</span>
                </span>
                <i class="bi bi-box-arrow-up-right arr"></i>
            </a>

            <a class="esk-link-card" href="https://bappedalitbang.deliserdangkab.go.id/" target="_blank" rel="noopener">
                <span class="ic"><img src="<?= $base ?>/lightapp/assets/images/bappeda.png" alt=""></span>
                <span>
                    <span class="t d-block">Website Bappedalitbang</span>
                    <span class="s">bappedalitbang.deliserdangkab.go.id</span>
                </span>
                <i class="bi bi-box-arrow-up-right arr"></i>
            </a>
        </div>
    </section>

</div>
