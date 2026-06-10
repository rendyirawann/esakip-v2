<?php

/** @var \yii\web\View $this */
/** @var string $content */

use common\widgets\Alert;
use frontend\assets\AppAsset;
use yii\bootstrap5\Breadcrumbs;
use yii\bootstrap5\Html;
use yii\helpers\Url;
use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">

<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <?php $this->registerCsrfMetaTags() ?>
    <title>Swagger UI</title>
    <link rel="icon" type="image/png" href="<?= Url::base(true) ?>/swagger/favicon-32x32.png" sizes="32x32" />
    <link rel="icon" type="image/png" href="<?= Url::base(true) ?>/swagger/favicon-16x16.png" sizes="16x16" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="description" content="DOC API ESAKIP - PEMERINTAHAN KABUPATEN DELI SERDANG" />
    <meta name="author" content="rendyirawan" />
    <?php $this->head() ?>
</head>

<body class="d-flex flex-column h-100">
    <?php $this->beginBody() ?>

    <div id="swagger-ui"></div>

    <?php $this->endBody() ?>
</body>

</html>
<?php $this->endPage();
