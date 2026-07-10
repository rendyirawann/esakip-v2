<?php

use yii\helpers\Url;
use yii\bootstrap5\Html;

/** @var \yii\web\View $this */

$assignments = Yii::$app->authManager->getAssignments(Yii::$app->user->getId());
$seeAll = isset($assignments['superadmin']) || isset($assignments['admin']) || isset($assignments['developer']);
$identity = Yii::$app->user->identity;

$base = \frontend\models\StorageItem::find()->where(['type' => 'file', 'is_deleted' => 0]);
if (!$seeAll) {
    $base->andWhere(['refskpd_id' => $identity->refskpd_id]);
}
$fileCount = (int) (clone $base)->count();
$totalSize = (float) ((clone $base)->sum('size') ?: 0);
$fmt = function ($b) {
    $u = ['B', 'KB', 'MB', 'GB'];
    $i = 0;
    while ($b >= 1024 && $i < 3) { $b /= 1024; $i++; }
    return ($i == 0 ? (int) $b : number_format($b, 1)) . ' ' . $u[$i];
};

$route = Yii::$app->controller->getRoute();
$onSkpdList = ($route === 'storage/index' && Yii::$app->request->get('skpd') === null);
$onFiles = ($route === 'storage/index' && !$onSkpdList);

$this->registerCss(<<<'CSS'
.pc-sidebar .navbar-wrapper{font-family:"Plus Jakarta Sans",sans-serif}
.esk-ncnav{padding:10px 12px}
.esk-ncnav a,.esk-ncnav .esk-nclogout button{display:flex;align-items:center;gap:12px;width:100%;padding:11px 14px;border-radius:10px;color:#475569;font-weight:600;font-size:.92rem;text-decoration:none;margin-bottom:4px;border:none;background:transparent;text-align:left;cursor:pointer}
.esk-ncnav a:hover,.esk-ncnav .esk-nclogout button:hover{background:#f1f5f9;color:#0f766e}
.esk-ncnav a.active{background:#ccfbf1;color:#0f766e}
.esk-ncnav .ico{width:22px;text-align:center;font-size:1.05rem;flex:0 0 auto}
.esk-ncsep{height:1px;background:#eef0f3;margin:10px 6px}
.esk-nclogout{margin:0}
.esk-nclogout button{color:#dc2626}
.esk-nclogout button:hover{background:#fee2e2;color:#b91c1c}
.esk-storage-used{margin:14px 14px 6px;padding:14px 16px;background:#f8fafc;border:1px solid #eef0f3;border-radius:12px}
.esk-storage-used .t{font-weight:700;color:#0f172a;font-size:.9rem;display:flex;align-items:center;gap:8px}
.esk-storage-used .s{font-size:.8rem;color:#64748b;margin-top:3px}
CSS);
?>
<!-- [ Sidebar Menu ] start -->
<nav class="pc-sidebar">
  <div class="navbar-wrapper">
    <div class="m-header">
      <a href="<?= Url::to(['/storage/index']) ?>" class="b-brand text-primary">
        <h5><img src="<?= Url::base(true) ?>/lightapp/assets/images/bappeda.png" alt="logo" height="46" width="46" class="logo-lg" /> EsakipStorage</h5>
      </a>
    </div>
    <div class="navbar-content">
      <div class="esk-ncnav">
        <?php if ($seeAll): ?>
          <a href="<?= Url::to(['/storage/index']) ?>" class="<?= ($onSkpdList || $onFiles) ? 'active' : '' ?>">
            <span class="ico">&#128193;</span> Semua SKPD
          </a>
        <?php else: ?>
          <a href="<?= Url::to(['/storage/index']) ?>" class="<?= $onFiles ? 'active' : '' ?>">
            <span class="ico">&#128450;</span> File Saya
          </a>
        <?php endif; ?>
        <div class="esk-ncsep"></div>
        <a href="<?= Url::to(['/site/index-main']) ?>">
          <span class="ico">&#8617;</span> Kembali ke Dashboard
        </a>
        <?= Html::beginForm(['/site/logout'], 'post', ['class' => 'esk-nclogout']) ?>
          <button type="submit"><span class="ico">&#9099;</span> Logout</button>
        <?= Html::endForm() ?>
      </div>

      <div class="esk-storage-used">
        <div class="t">&#128190; Penyimpanan</div>
        <div class="s"><?= $fileCount ?> dokumen &bull; <?= $fmt($totalSize) ?> terpakai</div>
      </div>
    </div>
  </div>
</nav>
<!-- [ Sidebar Menu ] end -->
<!-- [ Header Topbar ] start -->
<header class="pc-header">
  <div class="header-wrapper">
    <div class="me-auto pc-mob-drp">
      <ul class="list-unstyled">
        <li class="pc-h-item pc-sidebar-collapse">
          <a href="#" class="pc-head-link ms-0" id="sidebar-hide"><i class="ti ti-menu-2"></i></a>
        </li>
        <li class="pc-h-item pc-sidebar-popup">
          <a href="#" class="pc-head-link ms-0" id="mobile-collapse"><i class="ti ti-menu-2"></i></a>
        </li>
      </ul>
    </div>
    <div class="ms-auto">
      <ul class="list-unstyled d-flex align-items-center mb-0">
        <li class="pc-h-item">
          <span style="font-weight:600;color:#334155;display:inline-flex;align-items:center;gap:8px">
            <i class="ph-duotone ph-user-circle"></i><?= Html::encode($identity->username) ?>
          </span>
        </li>
      </ul>
    </div>
  </div>
</header>
<!-- [ Header Topbar ] end -->
