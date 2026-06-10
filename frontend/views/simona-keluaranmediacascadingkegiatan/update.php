<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var frontend\models\SimonaKeluaranmediacascadingkegiatan $model */

$this->title = 'Update Simona Keluaranmediacascadingkegiatan: ' . $model->refsimonakeluaranmediacascadingkegiatan_id;
$this->params['breadcrumbs'][] = ['label' => 'Simona Keluaranmediacascadingkegiatans', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->refsimonakeluaranmediacascadingkegiatan_id, 'url' => ['view', 'refsimonakeluaranmediacascadingkegiatan_id' => $model->refsimonakeluaranmediacascadingkegiatan_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="simona-keluaranmediacascadingkegiatan-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
