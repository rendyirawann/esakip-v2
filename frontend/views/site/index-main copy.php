<style>
    /* Mengatur gaya link */
    a {
        text-decoration: none;
        /* Hilangkan garis bawah */
        outline: none;
        /* Hilangkan garis biru saat fokus */
        color: inherit;
        /* Ikuti warna teks elemen induk */
    }

    a:hover {
        color: blue;
        /* Jika diperlukan, tambahkan efek hover */
    }
</style>
<?php

/** @var \yii\web\View $this */
/** @var string $content */

use frontend\assets\MainAsset;
use frontend\models\User;
use common\widgets\Alert;
use yii\bootstrap5\Breadcrumbs;
use yii\bootstrap5\Html;
use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;
use yii\helpers\Url;
use mdm\admin\components\MenuHelper;

MainAsset::register($this);

$this->title = 'Home - SIMONALISA';
?>
<div class="loading-overlay" id="loadingOverlay">
    <div>
        <div class="progress-bar" id="progressBar"></div>
        <p id="loadingText">Loading...</p>
    </div>
</div>

<!-- Konten Utama -->
<div class="profile-container">
    <h1 class="profile-title">Pilih Aplikasi</h1>
    <div class="profile-list">
        <div class="profile-card">
            <a href="<?= Url::to(['/site/index-esakip']) ?>">
                <div class="profile-avatar">E</div>
            </a>
            <a href="<?= Url::to(['/site/index-esakip']) ?>">
                <p>Esakip</p>
            </a>
        </div>
        <div class="profile-card">
            <a href="<?= Url::to(['/site/index-simona']) ?>">
                <div class="profile-avatar">P</div>
            </a>
            <a href="<?= Url::to(['/site/index-simona']) ?>">
                <p>Perencanaan</p>
            </a>
        </div>
        <div class="profile-card">
            <a href="<?= Url::to(['/site/index-dokrenbang']) ?>">
                <div class="profile-avatar">D</div>
            </a>
            <a href="<?= Url::to(['/site/index-dokrenbang']) ?>">
                <p>Dokumen</p>
            </a>
        </div>
    </div>
    <a href="<?= Url::to(['/site/portal']) ?>">
        <button class="manage-profiles-btn">Portal</button>
    </a>
</div>