<?php
// Tampilan (CSS) diatur oleh layout main-app.php — tema teal eksklusif.

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
    <span class="esk-switch-eyebrow">eSakip &bull; SIMONALISA</span>
    <h1 class="profile-title">Pilih Dashboard</h1>
    <p class="esk-switch-sub">Pilih modul yang ingin Anda akses</p>
    <div class="profile-list">
        <?php
        $assignments = Yii::$app->authManager->getAssignments(Yii::$app->user->getId());

        if (!isset($currentUser)): ?>
            <!-- Jika user belum login, tampilkan semua -->
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

        <?php elseif ($currentUser->refskpd_id === null && isset($assignments['superadmin'])): ?>
            <!-- Jika user login, refskpd_id null, dan assignmentnya adalah superadmin -->
            <div class="profile-card">
                <a href="<?= Url::to(['/site/index-esakip-dev']) ?>">
                    <div class="profile-avatar">
                        <img src="<?= Url::base(true) ?>/lightapp/assets/images/esakip.gif" alt="e-Sakip" style="border-radius: 10px 10px 0 0;" width="80px;">
                    </div>
                </a>
                <a href="<?= Url::to(['/site/index-esakip-dev']) ?>">
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

        <?php elseif ($currentUser->refskpd_id === null && isset($assignments['bidang'])  || isset($assignments['admin'])): ?>

            <!-- Jika user login, refskpd_id null, dan assignmentnya adalah superadmin -->
            <div class="profile-card">
                <a href="<?= Url::to(['/site/index-esakip-dev']) ?>">
                    <div class="profile-avatar">
                        <img src="<?= Url::base(true) ?>/lightapp/assets/images/esakip.gif" alt="e-Sakip" style="border-radius: 10px 10px 0 0;" width="80px;">
                    </div>
                </a>
                <a href="<?= Url::to(['/site/index-esakip-dev']) ?>">
                    <p>Esakip</p>
                </a>
            </div>


        <?php elseif ($currentUser->refskpd_id === null): ?>
            <!-- Jika user login tapi refskpd_id null (bukan superadmin), hanya tampilkan Dokumen -->
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

        <?php elseif (isset($assignments['skpd'])): ?>
            <!-- Jika user login dengan assignment 'skpd', tampilkan Esakip dan Perencanaan -->
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