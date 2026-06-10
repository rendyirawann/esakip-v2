<?php

/** @var \yii\web\View $this */
/** @var string $content */

use common\widgets\Alert;
use frontend\assets\ComingSoonAsset;
use yii\bootstrap5\Breadcrumbs;
use yii\bootstrap5\Html;
use yii\helpers\Url;
use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;

ComingSoonAsset::register($this);

$this->title = 'e-SAKIP Deli Serdang';
?>


<!--  -->
<div class="simpleslide100">
    <div class="simpleslide100-item bg-img1" style="background-image: url('images/bg01.jpg');"></div>
    <div class="simpleslide100-item bg-img1" style="background-image: url('images/bg02.jpg');"></div>
    <div class="simpleslide100-item bg-img1" style="background-image: url('images/bg03.jpg');"></div>
</div>

<div class="size1 overlay1">
    <!--  -->
    <div class="size1 flex-col-c-m p-l-15 p-r-15 p-t-50 p-b-50">
        <h5 class="l1-txt1 txt-center p-b-25">
            Current Page's Under Development
        </h5>
        <img src="<?= Url::base(true) ?>/lightapp/assets/images/bappeda.png" alt="logo image" height="60" width="60" class="logo-lg" />

        <p class="m2-txt1 txt-center p-b-48">
            Our website is under development!
        </p>

        <div class="flex-w flex-c-m cd100 p-b-33">
            <div class="flex-col-c-m size2 bor1 m-l-15 m-r-15 m-b-20">
                <span class="l2-txt1 p-b-9 days"></span>
                <span class="s2-txt1">Days</span>
            </div>

            <div class="flex-col-c-m size2 bor1 m-l-15 m-r-15 m-b-20">
                <span class="l2-txt1 p-b-9 hours"></span>
                <span class="s2-txt1">Hours</span>
            </div>

            <div class="flex-col-c-m size2 bor1 m-l-15 m-r-15 m-b-20">
                <span class="l2-txt1 p-b-9 minutes"></span>
                <span class="s2-txt1">Minutes</span>
            </div>

            <div class="flex-col-c-m size2 bor1 m-l-15 m-r-15 m-b-20">
                <span class="l2-txt1 p-b-9 seconds"></span>
                <span class="s2-txt1">Seconds</span>
            </div>
        </div>
        <div class="row flex-w flex-c-m cd100 p-b-33">
            <div class="col-lg-3">
                <a href="<?= Url::to(['/site/portal-publik']) ?>" class="btn btn-danger">Kembali</a>
            </div>
        </div>


    </div>
</div>

<script>
    // Target date for countdown (27 December 2024)
    const targetDate = new Date('2024-12-27T00:00:00').getTime();

    // Function to update countdown
    function updateCountdown() {
        const now = new Date().getTime(); // Current date and time
        const timeDifference = targetDate - now;

        // Calculate days, hours, minutes, and seconds
        const days = Math.floor(timeDifference / (1000 * 60 * 60 * 24));
        const hours = Math.floor((timeDifference % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((timeDifference % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((timeDifference % (1000 * 60)) / 1000);

        // Update HTML content
        document.querySelector('.days').textContent = days;
        document.querySelector('.hours').textContent = hours;
        document.querySelector('.minutes').textContent = minutes;
        document.querySelector('.seconds').textContent = seconds;

        // Stop the countdown if timeDifference reaches zero
        if (timeDifference <= 0) {
            clearInterval(interval);
            document.querySelector('.days').textContent = '0';
            document.querySelector('.hours').textContent = '0';
            document.querySelector('.minutes').textContent = '0';
            document.querySelector('.seconds').textContent = '0';
        }
    }

    // Update the countdown every second
    const interval = setInterval(updateCountdown, 1000);
</script>