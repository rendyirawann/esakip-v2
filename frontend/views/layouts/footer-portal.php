<?php

/** @var \yii\web\View $this */
/** @var string $content */

use common\widgets\Alert;
use frontend\assets\AppAsset;
use yii\bootstrap5\Breadcrumbs;
use yii\bootstrap5\Html;
use yii\helpers\Url;
use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;

AppAsset::register($this);
?>
<footer class="esk-footer">
    <div class="esk-footer-inner">
        <div>
            <img class="brand-logo" src="<?= Url::base(true) ?>/udema/bappeda/bappeda.png" alt="Deli Serdang">
            <p>Sistem Akuntabilitas Kinerja Instansi Pemerintah Online — Pemerintah Kabupaten Deli Serdang.</p>
            <div class="socials">
                <a href="https://web.facebook.com/pages/Bappeda-Deli-Serdang/366920706655586" target="_blank" rel="noopener" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
                <a href="https://www.instagram.com/bappedalitbangds/" target="_blank" rel="noopener" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
            </div>
        </div>

        <div>
            <h5>Tautan</h5>
            <ul class="footer-contact">
                <li><a href="<?= Url::to(['/site/portal']) ?>">Beranda Portal</a></li>
                <li><a href="<?= Url::to(['/site/portal-publik']) ?>">e-SAKIP Publik</a></li>
                <li><a href="https://bappedalitbang.deliserdangkab.go.id/" target="_blank" rel="noopener">Website Bappedalitbang</a></li>
                <li><a href="https://portal.deliserdangkab.go.id/" target="_blank" rel="noopener">Portal Deli Serdang</a></li>
            </ul>
        </div>

        <div>
            <h5>Kontak Kami</h5>
            <ul class="footer-contact">
                <li><i class="bi bi-telephone-fill"></i> <a href="tel://617951422">+ 61 79 5142 2</a></li>
                <li><i class="bi bi-envelope-fill"></i> <a href="mailto:bappedalitbang@deliserdangkab.go.id">bappedalitbang@deliserdangkab.go.id</a></li>
                <li><i class="bi bi-geo-alt-fill"></i> Bappedalitbang Kabupaten Deli Serdang</li>
            </ul>
        </div>
    </div>

    <div class="esk-footer-bottom">
        Copyright &copy; <?= date('Y') ?> e-SAKIP — Sistem Akuntabilitas Kinerja Instansi Pemerintah Online.
    </div>
</footer>
<!--/footer-->


<script type="text/javascript">
    'use strict';
    $('#layerslider').layerSlider({
        autoStart: true,
        navButtons: false,
        navStartStop: false,
        showCircleTimer: false,
        responsive: true,
        responsiveUnder: 1280,
        layersContainer: 1200,
        skinsPath: 'udema/layerslider/skins/'
        // Please make sure that you didn't forget to add a comma to the line endings
        // except the last line!
    });
</script>

<script>
    AOS.init();
</script>

<!-- Google tag (gtag.js) -->
<script async="" src="https://www.googletagmanager.com/gtag/js?id=G-PPES1ZFLYF"></script>
<script>
    window.dataLayer = window.dataLayer || [];

    function gtag() {
        dataLayer.push(arguments);
    }
    gtag('js', new Date());

    gtag('config', 'G-PPES1ZFLYF');
</script>


<script>
    const vm = new Vue({
        el: '#app',
        data: {
            results: [],
            url: 'https://portal.deliserdangkab.go.id/api/kategori?kategori_slug=berita',
        },
        mounted() {
            axios.get(this.url).then(response => {
                this.results = response.data.data
            })
        }
    });

    const vm2 = new Vue({
        el: '#app2',
        data: {
            results: [],
            url: 'https://portal.deliserdangkab.go.id/api/kategori_youtube?kategori_slug=video-youtube',
        },
        mounted() {
            axios.get(this.url).then(response => {
                this.results = response.data.data
            })
        }
    });

    const ipkkd = new Vue({
        el: '#ipkkd',
        data: {
            results: [],
            url: 'https://portal.deliserdangkab.go.id/api/berkas/pelengkap',
        },
        mounted() {
            axios.get(this.url).then(response => {
                this.results = response.data.data
                console.log(response.data)
            })
        }
    });
</script>

<script type="text/javascript" src="https://widget.kominfo.go.id/gpr-widget-kominfo.min.js"></script>

<!-- Countdown timer -->
<script>
    const countDownDate = new Date("27 November 2024 08:00:00").getTime();

    // countdown
    let timer = setInterval(function() {

        // get today's date
        const today = new Date().getTime();

        // get the difference
        let diff = countDownDate - today;

        // If the countdown is finished, stop the timer
        if (diff < 0) {
            clearInterval(timer);
            document.getElementById("timer").innerHTML =
                "<div class=\"days\"> \
    <div class=\"numbers\">0</div>hari</div> \
    <div class=\"hours\"> \
    <div class=\"numbers\">0</div>jam</div> \
    <div class=\"minutes\"> \
    <div class=\"numbers\">0</div>menit</div> \
    <div class=\"seconds\"> \
    <div class=\"numbers\">0</div>detik</div> \
    </div>";
            return;
        }

        // math
        let days = Math.floor(diff / (1000 * 60 * 60 * 24));
        let hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        let minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
        let seconds = Math.floor((diff % (1000 * 60)) / 1000);

        // display
        document.getElementById("timer").innerHTML =
            "<div class=\"days\"> \
  <div class=\"numbers\">" + days + "</div>hari</div> \
<div class=\"hours\"> \
  <div class=\"numbers\">" + hours + "</div>jam</div> \
<div class=\"minutes\"> \
  <div class=\"numbers\">" + minutes + "</div>menit</div> \
<div class=\"seconds\"> \
  <div class=\"numbers\">" + seconds + "</div>detik</div> \
</div>";

    }, 1000);
</script>

<!-- Countdown timer -->




<!---->
<div id="wp-extension" data-id="dpadflhmiohjfhhaehelneimpllfbpcg"></div>
