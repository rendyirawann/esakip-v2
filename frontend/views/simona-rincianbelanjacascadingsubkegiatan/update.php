<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var frontend\models\SimonaRincianbelanjacascadingsubkegiatan $model */

$this->title = 'Update Simona Rincianbelanjacascadingsubkegiatan: ' . $model->refsimonarincianbelanjacascadingsubkegiatan_id;
$this->params['breadcrumbs'][] = ['label' => 'Simona Rincianbelanjacascadingsubkegiatans', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->refsimonarincianbelanjacascadingsubkegiatan_id, 'url' => ['view', 'refsimonarincianbelanjacascadingsubkegiatan_id' => $model->refsimonarincianbelanjacascadingsubkegiatan_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="simona-rincianbelanjacascadingsubkegiatan-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_formupdate', [
        'model' => $model,
    ]) ?>

</div>