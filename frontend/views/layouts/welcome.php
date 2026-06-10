<?php

/** @var \yii\web\View $this */
/** @var string $content */

use common\widgets\Alert;
use frontend\assets\WelcomeAsset;
use yii\helpers\Url;
use yii\helpers\Html;

WelcomeAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="ESAKIP - PEMERINTAHAN KABUPATEN DELI SERDANG" />
    <meta name="author" content="rendyirawan" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <?php $this->registerCsrfMetaTags() ?>
    <link rel="icon" href="/frontend/web/images/bappeda.png" type="image/ico" />
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>

<body>
    <?php $this->beginBody() ?>
    <?= $content ?>


    <?php $this->endBody() ?>
    <!-- Footer -->
    <?= $this->render('footer-welcome') ?>
    <!-- /.footer -->
</body>

</html>
<?php $this->endPage();
