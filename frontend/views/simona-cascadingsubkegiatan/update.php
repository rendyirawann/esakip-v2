<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var frontend\models\SimonaCascadingsubkegiatan $model */

$this->title = 'Update Simona Cascadingsubkegiatan: ' . $model->refsimonacascadingsubkegiatan_id;
$this->params['breadcrumbs'][] = ['label' => 'Simona Cascadingsubkegiatans', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->refsimonacascadingsubkegiatan_id, 'url' => ['view', 'refsimonacascadingsubkegiatan_id' => $model->refsimonacascadingsubkegiatan_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="simona-cascadingsubkegiatan-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
