<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\SakipPeriode5tahun $model */

$this->title = 'Update Sakip Periode 5 Tahun: ' . $model->nama_periode;
$this->params['breadcrumbs'][] = ['label' => 'Sakip Periode 5 Tahuns', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->nama_periode, 'url' => ['view', 'refperiode_5tahun_id' => $model->refperiode_5tahun_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="sakip-periode-5tahun-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
