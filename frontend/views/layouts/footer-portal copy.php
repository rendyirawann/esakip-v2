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
<footer class="nav border-danger text-light" style="background: rgb(13,202,240); background: radial-gradient(circle, rgba(13,202,240,1) 0%, rgba(0,102,204,1) 83%, rgba(0,25,76,1) 100%);">
    <div class="container mt-4 text-center">
        <p>e-SAKIP. Sistem Akuntabilitas Kinerja Instansi Pemerintah © 2024 Bappedalitbang </p>
    </div>
    <div class="container wrapper justify-content-center d-flex mt-2">
        <a href="#" target="_blank">
            <div class="button b1">
                <div class="socicon"><i class="bi bi-instagram"></i></div>
                <span>Instagram</span>
            </div>
        </a>
        <a href="#" target="_blank">
            <div class="button b2">
                <div class="socicon"><i class="bi bi-youtube"></i></div>
                <span>Youtube</span>
            </div>
        </a>
        <a href="#" target="_blank">
            <div class="button b3">
                <div class="socicon"><i class="bi bi-twitter-x"></i></div>
                <span>Twitter</span>
            </div>
        </a>
        <a href="#" target="_blank">
            <div class="button b4">
                <div class="socicon"><i class="bi bi-facebook"></i></div>
                <span>Facebook</span>
            </div>
        </a>
        <a href="#" target="_blank">
            <div class="button b5">
                <div class="socicon"><i class="bi bi-tiktok"></i></div>
                <span>Tiktok</span>
            </div>
        </a>
    </div>
    <div class="container">
        <div class="row my-4">
            <div class="col-md-12 text-center">
                <!-- <strong class="fw-uppercase">deliserdangkab.go.id<br></strong> -->
                <small>
                    Kawasan Pemerintahan, Jl. Karya Dharma No.2, Perbarakan, Kec. Pagar Merbau, Kabupaten Deli Serdang, Sumatera Utara 205517<br>
                    <p><a href="mailto:bappedalitbang@deliserdangkab.go.id" class="text-warning text-decoration-none"><i class="bi bi-envelope-at-fill"></i> bappedalitbang@deliserdangkab.go.id</a></p>
                </small>
            </div>
        </div>
    </div>
</footer>

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

<!--Start of Tawk.to Script-->
<!--<script type="text/javascript">
    var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
    (function(){
        var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
        s1.async=true;
        s1.src='https://embed.tawk.to/5a5d4c6bd7591465c706c66b/default';
        s1.charset='UTF-8';
        s1.setAttribute('crossorigin','*');
        s0.parentNode.insertBefore(s1,s0);
    })();
    </script>-->
<!--End of Tawk.to Script-->


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