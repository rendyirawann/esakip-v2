<?php

/** @var \yii\web\View $this */
/** @var string $content */

use frontend\assets\AppAsset;
use common\widgets\Alert;
use yii\bootstrap5\Breadcrumbs;
use yii\bootstrap5\Html;
use yii\helpers\Url;
use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;

AppAsset::register($this);

$currentUser = Yii::$app->user->identity;
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
  <?php $this->beginBody() ?>

  <!-- Navbar-->
  <?php
  $assignments = Yii::$app->authManager->getAssignments(Yii::$app->user->getId());
  if (isset($assignments['skpd'])) {
  ?>
    <?= $this->render('navbar-dokrenbang', ['assetDir' => '@web']) ?>
  <?php } elseif (isset($assignments['developer'])) {  ?>
    <?= $this->render('navbar-dokrenbang-dev', ['assetDir' => '@web']) ?>
  <?php } elseif ($currentUser->refskpd_id === null) { ?>
    <?= $this->render('navbar-dokrenbang-publik', ['assetDir' => '@web']) ?>
  <?php } ?>
  <?= $content ?>


  <?php $this->endBody() ?>
  <!-- Footer -->
  <?= $this->render('footer-dokrenbang') ?>
  <!-- /.footer-->
</body>

</html>
<?php $this->endPage();
