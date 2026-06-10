<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var frontend\models\SimonaCascadingkegiatan $model */

$this->title = 'Update Simona Cascadingkegiatan: ' . $model->refsimonacascadingkegiatan_id;
$this->params['breadcrumbs'][] = ['label' => 'Simona Cascadingkegiatans', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->refsimonacascadingkegiatan_id, 'url' => ['view', 'refsimonacascadingkegiatan_id' => $model->refsimonacascadingkegiatan_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="simona-cascadingkegiatan-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
