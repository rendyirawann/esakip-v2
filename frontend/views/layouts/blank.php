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
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">

<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no, user-scalable=0, minimal-ui" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="description" content="ESAKIP - PEMERINTAHAN KABUPATEN DELI SERDANG" />
    <meta name="author" content="rendyirawan" />
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <!-- [Favicon] icon -->
    <link rel="icon" href="<?= Url::base(true) ?>/lightapp/assets/images/bappeda.png" type="image/x-icon" />
    <?php $this->head() ?>
</head>

<body data-pc-preset="preset-1" data-pc-sidebar-theme="light" data-pc-sidebar-caption="true" data-pc-direction="ltr" data-pc-theme="light">
    <!-- [ Pre-loader ] start -->
    <div class="loader-bg">
        <div class="loader-track">
            <div class="loader-fill"></div>
        </div>
    </div>


    <!-- [ Pre-loader ] End -->
    <?php $this->beginBody() ?>
    <!-- Overlay Loader (branded, teal) -->
    <div id="overlaySpinner" class="eskf-loader">
        <div class="eskf-loader-box">
            <div class="eskf-loader-ring">
                <div class="eskf-loader-logo">
                    <img src="<?= Url::base(true) ?>/lightapp/assets/images/bappeda.png" alt="eSakip" />
                </div>
                <svg class="eskf-loader-check" viewBox="0 0 52 52" aria-hidden="true">
                    <circle class="eskf-check-circle" cx="26" cy="26" r="24" fill="none" />
                    <path class="eskf-check-mark" fill="none" d="M14 27 l8 8 l16 -16" />
                </svg>
            </div>
            <h5 class="eskf-loader-title" id="loaderTitle">Memverifikasi akun Anda</h5>
            <p class="eskf-loader-sub" id="loaderSub">Mohon tunggu sebentar...</p>
            <div class="eskf-loader-dots"><span></span><span></span><span></span></div>
        </div>
    </div>

    <!-- CSS Overlay Loader -->
    <style>
        .eskf-loader {
            display: none;
            position: fixed;
            inset: 0;
            width: 100%;
            height: 100%;
            z-index: 10050;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            background: radial-gradient(800px 500px at 50% 35%, rgba(13, 148, 136, .28) 0%, transparent 60%),
                        rgba(4, 28, 25, .85);
            -webkit-backdrop-filter: blur(6px);
            backdrop-filter: blur(6px);
            animation: eskfLoaderFade .25s ease;
        }
        .eskf-loader-box { display: flex; flex-direction: column; align-items: center; text-align: center; font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; }
        .eskf-loader-ring { position: relative; width: 116px; height: 116px; border-radius: 50%; display: flex; align-items: center; justify-content: center; }
        .eskf-loader-ring::before {
            content: ""; position: absolute; inset: 0; border-radius: 50%;
            background: conic-gradient(from 0deg, rgba(20, 184, 166, 0) 0deg, #14b8a6 270deg, #5eead4 360deg);
            -webkit-mask: radial-gradient(farthest-side, transparent calc(100% - 7px), #000 calc(100% - 7px));
                    mask: radial-gradient(farthest-side, transparent calc(100% - 7px), #000 calc(100% - 7px));
            animation: eskfSpin .9s linear infinite;
        }
        .eskf-loader-logo {
            width: 70px; height: 70px; border-radius: 50%; background: #fff;
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 8px 24px rgba(2, 18, 16, .4); animation: eskfPulse 1.6s ease-in-out infinite;
        }
        .eskf-loader-logo img { width: 44px; height: 44px; object-fit: contain; }
        .eskf-loader-check { display: none; width: 78px; height: 78px; }
        .eskf-check-circle { stroke: #22c55e; stroke-width: 3; stroke-dasharray: 151; stroke-dashoffset: 151; animation: eskfCheckCircle .5s ease forwards; }
        .eskf-check-mark { stroke: #22c55e; stroke-width: 4; stroke-linecap: round; stroke-linejoin: round; stroke-dasharray: 40; stroke-dashoffset: 40; animation: eskfCheckMark .35s .45s ease forwards; }
        .eskf-loader-title { color: #fff; font-size: 18px; font-weight: 700; margin: 22px 0 4px; }
        .eskf-loader-sub { color: rgba(226, 240, 237, .78); font-size: 13.5px; margin: 0; }
        .eskf-loader-dots { display: flex; gap: 7px; margin-top: 18px; }
        .eskf-loader-dots span { width: 9px; height: 9px; border-radius: 50%; background: #2dd4bf; animation: eskfDots 1.2s ease-in-out infinite; }
        .eskf-loader-dots span:nth-child(2) { animation-delay: .2s; }
        .eskf-loader-dots span:nth-child(3) { animation-delay: .4s; }
        .eskf-loader.is-success .eskf-loader-ring::before { animation: none; background: conic-gradient(rgba(34, 197, 94, .35), rgba(22, 163, 74, .2)); }
        .eskf-loader.is-success .eskf-loader-logo { display: none; }
        .eskf-loader.is-success .eskf-loader-check { display: block; }
        .eskf-loader.is-success .eskf-loader-dots span { background: #22c55e; }
        @keyframes eskfSpin { to { transform: rotate(360deg); } }
        @keyframes eskfPulse { 0%, 100% { transform: scale(1); } 50% { transform: scale(1.08); } }
        @keyframes eskfDots { 0%, 100% { transform: translateY(0); opacity: .4; } 50% { transform: translateY(-7px); opacity: 1; } }
        @keyframes eskfCheckCircle { to { stroke-dashoffset: 0; } }
        @keyframes eskfCheckMark { to { stroke-dashoffset: 0; } }
        @keyframes eskfLoaderFade { from { opacity: 0; } to { opacity: 1; } }
    </style>
    <?= $content ?>


    <?php $this->endBody() ?>
    <!-- Footer -->
    <?= $this->render('blank-footer') ?>
    <!-- /.footer -->
</body>

</html>
<?php $this->endPage();
