<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var frontend\models\SakipPenjabatskpdCascadingkegiatan $model */

$this->title = 'Update Sakip Penjabatskpd Cascadingkegiatan: ' . $model->refpenjabatcascadingkegiatan_id;
$this->params['breadcrumbs'][] = ['label' => 'Sakip Penjabatskpd Cascadingkegiatans', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->refpenjabatcascadingkegiatan_id, 'url' => ['view', 'refpenjabatcascadingkegiatan_id' => $model->refpenjabatcascadingkegiatan_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="sakip-penjabatskpd-cascadingkegiatan-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
