<?php

/** @var yii\web\View $this */

use yii\helpers\Html;
use yii\helpers\Url;
use frontend\assets\WelcomeAsset;

WelcomeAsset::register($this);
$this->title = 'Aplikasi e-SAKIP SIMONALISA Deli Serdang';

?>

<div class="loading-page">
    <img id="svg" class="welcome-logo" src="<?= Url::base(true) ?>/welcomeasset/bappeda.png" alt="eSakip">
    <div class="name-container">
        <div class="logo-name">
            <h1>eSakip <span>SIMONALISA</span></h1>
            <p>Sistem Akuntabilitas Kinerja Instansi Pemerintah</p>
            <small>PEMERINTAH KABUPATEN DELI SERDANG</small>
        </div>
        <div class="welcome-dots"><span></span><span></span><span></span></div>
    </div>
</div>

<style>
    /* Inline & override CSS lama yang ter-cache (anti tampilan jelek/terpotong) */
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

    html, body { margin: 0; padding: 0; height: 100%; overflow: hidden; }
    .fluid-container { display: none !important; }

    .loading-page {
        position: fixed !important;
        inset: 0 !important;
        width: 100% !important;
        height: 100% !important;
        display: flex !important;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        padding: 24px;
        margin: 0;
        color: #e8fffb;
        z-index: 50;
        font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        background: radial-gradient(900px 600px at 18% 12%, rgba(13, 148, 136, .38) 0%, transparent 55%),
                    radial-gradient(900px 600px at 85% 95%, rgba(16, 185, 129, .24) 0%, transparent 55%),
                    linear-gradient(135deg, #03201c 0%, #052b26 50%, #07382f 100%) !important;
    }

    #svg {
        height: 130px !important;
        width: 130px !important;
        object-fit: contain;
        padding: 18px;
        border-radius: 28px;
        background: rgba(255, 255, 255, .08);
        border: 1px solid rgba(255, 255, 255, .18);
        -webkit-backdrop-filter: blur(8px);
        backdrop-filter: blur(8px);
        box-shadow: 0 20px 50px -16px rgba(0, 0, 0, .5);
        stroke: none;
        fill-opacity: 1;
    }

    .name-container {
        height: auto !important;
        overflow: visible !important;
        margin: 30px 0 0 !important;
    }

    .logo-name {
        color: #fff !important;
        font-size: inherit !important;
        letter-spacing: normal !important;
        text-transform: none !important;
        margin: 0 !important;
    }
    .logo-name h1 { font-size: clamp(1.8rem, 5vw, 2.8rem); font-weight: 800; letter-spacing: -.5px; margin: 0; color: #fff; }
    .logo-name h1 span { color: #5eead4; }
    .logo-name p { margin: .55rem 0 0; color: rgba(232, 255, 251, .85); font-size: 1.02rem; font-weight: 500; }
    .logo-name small { display: block; margin-top: .4rem; color: rgba(232, 255, 251, .5); font-size: .8rem; letter-spacing: 1.5px; }

    .welcome-dots { display: flex; gap: 8px; justify-content: center; margin-top: 30px; }
    .welcome-dots span { width: 10px; height: 10px; border-radius: 50%; background: #2dd4bf; animation: welcomeDot 1.2s ease-in-out infinite; }
    .welcome-dots span:nth-child(2) { animation-delay: .2s; }
    .welcome-dots span:nth-child(3) { animation-delay: .4s; }

    @keyframes welcomeDot { 0%, 100% { transform: translateY(0); opacity: .4; } 50% { transform: translateY(-8px); opacity: 1; } }
</style>
