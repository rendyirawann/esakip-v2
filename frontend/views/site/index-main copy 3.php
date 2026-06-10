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

    .profile-container {
        text-align: center;
        /* Pusatkan konten */
    }

    .profile-list {
        display: flex;
        justify-content: center;
        /* Pusatkan kartu profil secara horizontal */
        flex-wrap: wrap;
        /* Bungkus kartu ke baris berikutnya jika ada overflow */
        gap: 20px;
        /* Jarak antar kartu */
        margin-bottom: 20px;
        /* Tambahkan jarak di bawah profil */
    }


    .portal-button-container {
        margin-top: 20px;
        /* Tambahkan jarak di atas tombol */
        text-align: center;
    }

    .manage-profiles-btn {
        padding: 10px 20px;
        background-color: #007bff;
        color: #fff;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }

    .manage-profiles-btn:hover {
        background-color: #0056b3;
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
$currentUser = Yii::$app->user->identity;
?>
<div class="loading-overlay" id="loadingOverlay">
    <div>
        <div class="progress-bar" id="progressBar"></div>
        <p id="loadingText">Loading...</p>
    </div>
</div>
<div class="profile-container">
    <h1 class="profile-title">Pilih Dashboard</h1>
    <div class="profile-list">
        <?php if ($currentUser->refskpd_id === null): ?>
            <div class="profile-card">
                <a href="<?= Url::to(['/site/index-dokrenbang']) ?>">
                    <div class="profile-avatar">
                        <img src="<?= Url::base(true) ?>/lightapp/assets/images/dokrenbang-2.gif" alt="Dokumen" style="border-radius: 10px 10px 0 0;" width="80px;">
                    </div>
                </a>
                <a href="<?= Url::to(['/site/index-dokrenbang']) ?>">
                    <p>Dokumen</p>
                </a>
            </div>
        <?php else: ?>
            <div class="profile-card">
                <a href="<?= Url::to(['/site/index-esakip']) ?>">
                    <div class="profile-avatar">
                        <img src="<?= Url::base(true) ?>/lightapp/assets/images/esakip.gif" alt="e-Sakip" style="border-radius: 10px 10px 0 0;" width="80px;">
                    </div>
                </a>
                <a href="<?= Url::to(['/site/index-esakip']) ?>">
                    <p>Esakip</p>
                </a>
            </div>
            <div class="profile-card">
                <a href="<?= Url::to(['/site/index-simona']) ?>">
                    <div class="profile-avatar">
                        <img src="<?= Url::base(true) ?>/lightapp/assets/images/perencanaan.gif" alt="Perencanaan" style="border-radius: 10px 10px 0 0;" width="80px;">
                    </div>
                </a>
                <a href="<?= Url::to(['/site/index-simona']) ?>">
                    <p>Perencanaan</p>
                </a>
            </div>
            <div class="profile-card">
                <a href="<?= Url::to(['/site/index-dokrenbang']) ?>">
                    <div class="profile-avatar">
                        <img src="<?= Url::base(true) ?>/lightapp/assets/images/dokrenbang-2.gif" alt="Dokumen" style="border-radius: 10px 10px 0 0;" width="80px;">
                    </div>
                </a>
                <a href="<?= Url::to(['/site/index-dokrenbang']) ?>">
                    <p>Dokumen</p>
                </a>
            </div>
        <?php endif; ?>
    </div>
    <!-- Tombol Portal -->
    <div class="portal-button-container">
        <a href="<?= Url::to(['/site/portal']) ?>">
            <button class="manage-profiles-btn">Portal</button>
        </a>
    </div>
</div>