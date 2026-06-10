<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var frontend\models\SimonaRincianbelanjacascadingkegiatan $model */

$this->title = 'Update Simona Rincianbelanjacascadingkegiatan: ' . $model->refsimonarincianbelanjacascadingkegiatan_id;
$this->params['breadcrumbs'][] = ['label' => 'Simona Rincianbelanjacascadingkegiatans', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->refsimonarincianbelanjacascadingkegiatan_id, 'url' => ['view', 'refsimonarincianbelanjacascadingkegiatan_id' => $model->refsimonarincianbelanjacascadingkegiatan_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="simona-rincianbelanjacascadingkegiatan-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_formupdate', [
        'model' => $model,
    ]) ?>

</div>