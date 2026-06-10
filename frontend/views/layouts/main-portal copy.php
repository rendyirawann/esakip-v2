<link href="https://portal.deliserdangkab.go.id/wp-content/berkas/1718002763.png" rel="icon">

<style>
    :root {
        --bs-primary: #0d6efd;
        --bs-secondary: #6c757d;
        --bs-dark: #212529;
        --bs-success: #198754;
        --bs-info: #0dcaf0;
        --bs-warning: #ffc107;
        --bs-danger: #dc3545;
        --bs-light: #f8f9fa;
        --bs-purple: #6f42c1;
        --bs-pink: #d63384;
        --bs-orange: #fd7e14;
        --bs-teal: #20c997;
        --bs-indigo: #6610f2;
        --bs-black: #000;
        --bs-kominfo: #094A8A;
    }

    body {
        margin: 0;
        padding: 0;
    }

    body,
    .modal-display {
        background-image: linear-gradient(90deg, #e3ffe7 0%, #d9e7ff 100%);
        height: 100%;
        background-position: center;
        background-repeat: no-repeat;
        background-size: cover;
    }

    .modal-display ul li {
        transition: all 500ms;
    }

    .modal-display ul li:hover {
        transform: scale(1.25);
        transform: translateX(100 0);
    }

    header {
        font-family: "IBM Plex Sans", sans-serif;
    }

    .card-layanan {
        height: 16rem;
        width: 15rem;
        transition: all 500ms;
    }

    .card-layanan:hover {
        transform: scale(1.05);
        transform: translateX(100 0);
    }

    .card-layanan img {
        min-height: 100px;
        max-height: 100px;
        width: 100%;
        max-width: fit-content;
        max-width: -moz-fit-content;
        object-fit: contain;
    }

    .card-opd {
        height: 500px;
        transition: all 500ms;
    }

    .card-opd:hover {
        transform: scale(1.05);
        transform: translateX(100 0);
    }

    .card-opd .card-body-opd img {
        transition: all 500ms;
    }

    .card-opd .card-body-opd img:hover {
        transform: scale(1.25);
        transform: translateX(100 0);
    }

    .card-opd img {
        max-height: 100px;
        max-width: fit-content;
        object-fit: fill;
    }

    .card-body-opd {
        height: 200px;
        object-fit: cover;
        font-family: "IBM Plex Sans", sans-serif;
    }

    .menu-icon {
        overflow: hidden;
        transition: all 500ms;
    }

    .menu-icon:hover {
        transform: scale(1.15);
        transform: translateX(100 0);
    }

    .menu-icon .cpns {
        overflow: hidden;
        animation: left-right 2s ease-in-out infinite alternate-reverse both;
    }

    .menu-icon .pon-xxi {
        overflow: hidden;
        animation: up-down 4s ease-in-out infinite alternate-reverse both;
    }

    .menu-icon .pilkada-2024 {
        overflow: hidden;
        animation: up-down 3s ease-in-out infinite alternate-reverse both;
    }

    /* medsos icon */
    .bi-facebook {
        color: rgb(31, 86, 224);
    }

    .bi-instagram {
        color: rgb(255, 111, 79);
    }

    .bi-youtube {
        color: rgb(255, 0, 0);
    }

    .bi-twitter {
        color: rgb(0, 157, 255);
    }

    .bi-tiktok {
        color: rgb(31, 31, 31);
    }

    .card-footer .btn {
        transition: all 300ms;
    }

    .card-footer .btn:hover {
        transform: scale(1.3);
    }

    /* small font */
    .bkpsdm small {
        font-size: 16px;
    }

    .dinas-kb small {
        font-size: 12px;
    }

    .rsud small {
        font-size: 12px;
    }

    .wrapper .button {
        /* background-color: var(--bs-light);
  color: var(--bs-kominfo); */
        display: inline-block;
        height: 60px;
        width: 60px;
        float: left;
        margin: 0 5px;
        overflow: hidden;
        border-radius: 50px;
        cursor: pointer;
        box-shadow: 0px 10px 10px rgba(0, 0, 0, 0.1);
        transition: all 300ms ease-out;
    }

    .wrapper a .b1 {
        background-color: var(--bs-light);
        color: var(--bs-pink);
    }

    .wrapper a .b2 {
        background-color: var(--bs-light);
        color: var(--bs-danger);
    }

    .wrapper a .b3 {
        background-color: var(--bs-light);
        color: var(--bs-info);
    }

    .wrapper a .b4 {
        background-color: var(--bs-light);
        color: var(--bs-kominfo);
    }

    .wrapper a .b5 {
        background-color: var(--bs-light);
        color: var(--bs-dark);
    }

    .wrapper .button:hover {
        width: 200px;
    }

    .wrapper .button .socicon {
        display: inline-block;
        height: 60px;
        width: 60px;
        text-align: center;
        border-radius: 50px;
        box-sizing: border-box;
        line-height: 60px;
    }

    .wrapper .button .socicon i {
        font-size: 25px;
        line-height: 60px;
        transition: all 300ms ease-out;
    }

    .wrapper .button span {
        font-size: 20px;
        font-weight: 500;
        line-height: 60px;
        margin-left: 10px;
        transition: all 300ms ease-out;
    }

    /* Countdown Section */
    .countdown {
        width: 650px;
        height: 480px;
    }

    #timer {
        color: var(--bs-light);
        text-align: center;
        text-transform: uppercase;
        font-family: 'Livvic', sans-serif;
        font-size: .7em;
        letter-spacing: 2px;
    }

    .days,
    .hours,
    .minutes,
    .seconds {
        display: inline-block;
        padding: 10px;
        width: 85px;
        border-radius: 10px;
    }

    .days {
        background: var(--bs-danger);
    }

    .hours {
        background: var(--bs-light);
        color: var(--bs-dark);
    }

    .minutes {
        background: var(--bs-kominfo);
    }

    .seconds {
        background: var(--bs-warning);
    }

    .numbers {
        /* font-family: 'Covered By Your Grace', cursive; */
        font-family: "New Amsterdam", sans-serif;
        text-align: center;
        font-size: 4em;
    }

    /*--------------------------------------------------------------
# Animation Section
--------------------------------------------------------------*/
    @keyframes up-down {
        0% {
            transform: translateY(5px);
        }

        100% {
            transform: translateY(-5px);
        }
    }

    @keyframes left-right {
        0% {
            transform: translateX(4px);
        }

        100% {
            transform: translateX(-4px);
        }
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
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">

<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <link rel="icon" href="<?= Url::base(true) ?>/lightapp/assets/images/bappeda.png" type="image/x-icon" />
    <?php $this->head() ?>
</head>

<body data-aos-easing="ease" data-aos-duration="400" data-aos-delay="0">
    <?php $this->beginBody() ?>
    <!-- Navbar-->
    <?= $this->render('navbar-portal', ['assetDir' => '@web']) ?>
    <!-- /.navbar-->

    <?= $content ?>


    <?php $this->endBody() ?>
    <!-- Footer -->
    <?= $this->render('footer-portal') ?>
    <!-- /.footer-->
    <!-- Scroll Top -->

</body>

</html>
<?php $this->endPage();
