<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\SakipPegawaibappeda $model */

$this->title = 'Update Sakip Pegawaibappeda: ' . $model->refpegawai_id;
$this->params['breadcrumbs'][] = ['label' => 'Sakip Pegawaibappedas', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->refpegawai_id, 'url' => ['view', 'refpegawai_id' => $model->refpegawai_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="sakip-pegawaibappeda-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
