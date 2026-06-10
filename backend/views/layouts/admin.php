<?php

/**
 * Layout khusus untuk modul RBAC (mdmsoft/yii2-admin).
 * Tujuannya hanya membungkus konten RBAC ke dalam ".pc-container .pc-content"
 * agar posisinya benar (tidak tertutup sidebar/header) dan konsisten dengan
 * halaman admin lainnya. Tidak mengubah logika/fungsi modul.
 *
 * @var \yii\web\View $this
 * @var string $content
 */

use backend\assets\AppAsset;
use yii\bootstrap5\Html;
use yii\helpers\Url;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">

<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="author" content="Bappedalitbang" />
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <!-- [Favicon] icon -->
    <link rel="icon" href="<?= Url::base(true) ?>/lightapp/assets/images/bappeda.png" type="image/x-icon" />
    <?php $this->head() ?>
</head>

<body data-pc-preset="preset-1" data-pc-sidebar-theme="light" data-pc-sidebar-caption="true" data-pc-direction="ltr" data-pc-theme="light">
    <?php $this->beginBody() ?>

    <!-- Navbar (sidebar + header) -->
    <?= $this->render('navbar', ['assetDir' => '@web']) ?>

    <div class="pc-container">
        <div class="pc-content">
            <?php if (Yii::$app->session->hasFlash('success')) : ?>
                <div class="alert alert-success"><?= Yii::$app->session->getFlash('success') ?></div>
            <?php endif; ?>
            <?php if (Yii::$app->session->hasFlash('error')) : ?>
                <div class="alert alert-danger"><?= Yii::$app->session->getFlash('error') ?></div>
            <?php endif; ?>

            <?= $content ?>
        </div>
    </div>

    <?php $this->endBody() ?>
    <!-- Footer -->
    <?= $this->render('footer') ?>
</body>

</html>
<?php $this->endPage();
