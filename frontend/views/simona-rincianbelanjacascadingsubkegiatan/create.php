<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var frontend\models\SimonaRincianbelanjacascadingsubkegiatan $model */

$this->title = 'Create Simona Rincianbelanjacascadingsubkegiatan';
$this->params['breadcrumbs'][] = ['label' => 'Simona Rincianbelanjacascadingsubkegiatans', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="simona-rincianbelanjacascadingsubkegiatan-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'detail' => $detail,
    ]) ?>

</div>