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

$this->registerCss(<<<'CSS'
.esk-auth-row{display:flex;justify-content:center;margin-top:14px}
.esk-auth-row form{margin:0}
.esk-login-btn{background:transparent !important;border:2px solid #2dd4bf !important;color:#5eead4 !important}
.esk-login-btn:hover{background:rgba(45,212,191,.14) !important;color:#fff !important}
.esk-logout-btn{background:linear-gradient(135deg,#ef4444,#dc2626) !important;border:none !important;color:#fff !important}
.esk-logout-btn:hover{filter:brightness(1.08)}
CSS);
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

        <?php
        // Kartu EsakipStorage — hanya untuk role yang berhak (lihat-semua: superadmin/
        // admin/developer; serta skpd yang melihat foldernya sendiri).
        if (isset($currentUser) && (
            isset($assignments['superadmin']) || isset($assignments['admin'])
            || isset($assignments['developer']) || isset($assignments['skpd'])
        )): ?>
            <div class="profile-card">
                <a href="<?= Url::to(['/storage/index']) ?>">
                    <div class="profile-avatar">
                        <svg width="80" height="80" viewBox="0 0 64 64" style="border-radius:10px 10px 0 0">
                            <rect width="64" height="64" rx="14" fill="#0d9488"/>
                            <path fill="#fff" d="M13 21h13l4 4h21a3 3 0 0 1 3 3v18a3 3 0 0 1-3 3H13a3 3 0 0 1-3-3V24a3 3 0 0 1 3-3z" opacity=".96"/>
                            <path fill="#0d9488" d="M41 45a6 6 0 0 0 0-12 7 7 0 0 0-13-2 5 5 0 0 0 1 14h12z"/>
                        </svg>
                    </div>
                </a>
                <a href="<?= Url::to(['/storage/index']) ?>">
                    <p>EsakipStorage</p>
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

    <!-- Tombol Login (jika belum login) / Logout (jika sudah login) -->
    <div class="esk-auth-row">
        <?php if (Yii::$app->user->isGuest): ?>
            <a href="<?= Url::to(['/site/login']) ?>">
                <button class="manage-profiles-btn esk-login-btn">&#128274; Login</button>
            </a>
        <?php else: ?>
            <?= Html::beginForm(['/site/logout'], 'post', ['style' => 'display:inline']) ?>
                <button type="submit" class="manage-profiles-btn esk-logout-btn">&#9099; Logout</button>
            <?= Html::endForm() ?>
        <?php endif; ?>
    </div>
</div>