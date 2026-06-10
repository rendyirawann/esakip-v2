<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\SakipPenjabatSkpd $model */

$this->title = 'Update Sakip Penjabat Skpd: ' . $model->refpenjabatskpd_id;
$this->params['breadcrumbs'][] = ['label' => 'Sakip Penjabat Skpds', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->refpenjabatskpd_id, 'url' => ['view', 'refpenjabatskpd_id' => $model->refpenjabatskpd_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="sakip-penjabat-skpd-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_formupdate', [
        'model' => $model,
    ]) ?>

</div>