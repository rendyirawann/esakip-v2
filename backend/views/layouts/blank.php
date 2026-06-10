<?php

/** @var \yii\web\View $this */
/** @var string $content */

use common\widgets\Alert;
use backend\assets\AppAsset;
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
    <meta name="description" content="Light Able admin and dashboard template offer a variety of UI elements and pages, ensuring your admin panel is both fast and effective." />
    <meta name="author" content="phoenixcoded" />
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
    <!-- Overlay Loader (branded) -->
    <div id="overlaySpinner" class="esk-loader">
        <div class="esk-loader-box">
            <div class="esk-loader-ring">
                <div class="esk-loader-logo">
                    <img src="<?= Url::base(true) ?>/lightapp/assets/images/bappeda.png" alt="eSakip" />
                </div>
                <svg class="esk-loader-check" viewBox="0 0 52 52" aria-hidden="true">
                    <circle class="esk-check-circle" cx="26" cy="26" r="24" fill="none" />
                    <path class="esk-check-mark" fill="none" d="M14 27 l8 8 l16 -16" />
                </svg>
            </div>
            <h5 class="esk-loader-title" id="loaderTitle">Memverifikasi akun Anda</h5>
            <p class="esk-loader-sub" id="loaderSub">Mohon tunggu sebentar...</p>
            <div class="esk-loader-dots"><span></span><span></span><span></span></div>
        </div>
    </div>

    <!-- CSS Overlay Loader -->
    <style>
        .esk-loader {
            display: none;
            position: fixed;
            inset: 0;
            width: 100%;
            height: 100%;
            z-index: 10050;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            background: radial-gradient(800px 500px at 50% 35%, rgba(37, 99, 235, .25) 0%, transparent 60%),
                        rgba(8, 12, 28, .82);
            -webkit-backdrop-filter: blur(6px);
            backdrop-filter: blur(6px);
            animation: eskLoaderFade .25s ease;
        }
        .esk-loader-box {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            font-family: 'Public Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        }
        .esk-loader-ring {
            position: relative;
            width: 116px;
            height: 116px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .esk-loader-ring::before {
            content: "";
            position: absolute;
            inset: 0;
            border-radius: 50%;
            background: conic-gradient(from 0deg, rgba(96, 165, 250, 0) 0deg, #3b82f6 270deg, #93c5fd 360deg);
            -webkit-mask: radial-gradient(farthest-side, transparent calc(100% - 7px), #000 calc(100% - 7px));
                    mask: radial-gradient(farthest-side, transparent calc(100% - 7px), #000 calc(100% - 7px));
            animation: eskSpin .9s linear infinite;
        }
        .esk-loader-logo {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 24px rgba(2, 6, 23, .4);
            animation: eskPulse 1.6s ease-in-out infinite;
        }
        .esk-loader-logo img { width: 44px; height: 44px; object-fit: contain; }

        .esk-loader-check { display: none; width: 78px; height: 78px; }
        .esk-check-circle {
            stroke: #22c55e; stroke-width: 3; stroke-dasharray: 151; stroke-dashoffset: 151;
            animation: eskCheckCircle .5s ease forwards;
        }
        .esk-check-mark {
            stroke: #22c55e; stroke-width: 4; stroke-linecap: round; stroke-linejoin: round;
            stroke-dasharray: 40; stroke-dashoffset: 40;
            animation: eskCheckMark .35s .45s ease forwards;
        }

        .esk-loader-title { color: #fff; font-size: 18px; font-weight: 700; margin: 22px 0 4px; }
        .esk-loader-sub { color: rgba(226, 232, 240, .75); font-size: 13.5px; margin: 0; }

        .esk-loader-dots { display: flex; gap: 7px; margin-top: 18px; }
        .esk-loader-dots span {
            width: 9px; height: 9px; border-radius: 50%; background: #60a5fa;
            animation: eskDots 1.2s ease-in-out infinite;
        }
        .esk-loader-dots span:nth-child(2) { animation-delay: .2s; }
        .esk-loader-dots span:nth-child(3) { animation-delay: .4s; }

        /* ---- Success state ---- */
        .esk-loader.is-success .esk-loader-ring::before {
            animation: none;
            background: conic-gradient(rgba(34, 197, 94, .35), rgba(22, 163, 74, .2));
        }
        .esk-loader.is-success .esk-loader-logo { display: none; }
        .esk-loader.is-success .esk-loader-check { display: block; }
        .esk-loader.is-success .esk-loader-dots span { background: #22c55e; }

        @keyframes eskSpin { to { transform: rotate(360deg); } }
        @keyframes eskPulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.08); }
        }
        @keyframes eskDots {
            0%, 100% { transform: translateY(0); opacity: .4; }
            50% { transform: translateY(-7px); opacity: 1; }
        }
        @keyframes eskCheckCircle { to { stroke-dashoffset: 0; } }
        @keyframes eskCheckMark { to { stroke-dashoffset: 0; } }
        @keyframes eskLoaderFade { from { opacity: 0; } to { opacity: 1; } }
    </style>
    <?= $content ?>


    <?php $this->endBody() ?>
    <!-- Footer -->
    <?= $this->render('blank-footer') ?>
    <!-- /.footer -->
</body>

</html>
<?php $this->endPage();
