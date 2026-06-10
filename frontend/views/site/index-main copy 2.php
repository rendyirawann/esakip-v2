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
            <ul class="list-unstyled d-flex gap-3">
                <li class="menu-icon card shadow p-1" style="max-width: 150px; border-radius: 10px;">
                    <a href="<?= Url::to(['/site/index-esakip']) ?>" target="_blank" class="text-decoration-none">
                        <img src="<?= Url::base(true) ?>/lightapp/assets/images/esakip.gif" class="card-img-top" alt="e-Sakip" style="border-radius: 10px 10px 0 0;">
                    </a>
                    <span class="p-2 text-center" style="border-radius: 0 0 10px 10px;"><b>e-SAKIP</b></span>
                </li>
            </ul>
        </div>
        <div class="profile-card">
            <ul class="list-unstyled d-flex gap-3">
                <li class="menu-icon card shadow p-1" style="max-width: 150px; border-radius: 10px;">
                    <a href="<?= Url::to(['/site/index-simona']) ?>" target="_blank" class="text-decoration-none">
                        <img src="<?= Url::base(true) ?>/lightapp/assets/images/perencanaan.gif" class="card-img-top" alt="Perencanaan" style="border-radius: 10px 10px 0 0;">
                    </a>
                    <span class="p-2 text-center" style="border-radius: 0 0 10px 10px;"><b>Monitoring<br>Perencanaan</b></span>
                </li>
            </ul>
        </div>
        <div class="profile-card">
            <ul class="list-unstyled d-flex gap-3">
                <li class="menu-icon card shadow p-1" style="max-width: 150px; border-radius: 10px;">
                    <a href="<?= Url::to(['/site/index-dokrenbang']) ?>" target="_blank" class="text-decoration-none">
                        <img src="<?= Url::base(true) ?>/lightapp/assets/images/dokrenbang-2.gif" class="card-img-top" alt="Dokumen" style="border-radius: 10px 10px 0 0;">
                    </a>
                    <span class="p-2 text-center" style="border-radius: 0 0 10px 10px;"><b>Dokumen<br>Perencanaan</b></span>
                </li>
            </ul>
        </div>
    </div>
    <a href="<?= Url::to(['/site/portal']) ?>">
        <button class="manage-profiles-btn">Portal</button>
    </a>
</div>