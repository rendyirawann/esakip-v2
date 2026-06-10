<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var frontend\models\SimonaKeluaranmediacascadingsubkegiatan $model */

$this->title = 'Update Simona Keluaranmediacascadingsubkegiatan: ' . $model->refsimonakeluaranmediacascadingsubkegiatan_id;
$this->params['breadcrumbs'][] = ['label' => 'Simona Keluaranmediacascadingsubkegiatans', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->refsimonakeluaranmediacascadingsubkegiatan_id, 'url' => ['view', 'refsimonakeluaranmediacascadingsubkegiatan_id' => $model->refsimonakeluaranmediacascadingsubkegiatan_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="simona-keluaranmediacascadingsubkegiatan-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
