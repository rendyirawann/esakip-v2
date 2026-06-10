<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var frontend\models\SakipPenjabatskpdCascadingsubkegiatan $model */

$this->title = 'Update Sakip Penjabatskpd Cascadingsubkegiatan: ' . $model->refpenjabatcascadingsubkegiatan_id;
$this->params['breadcrumbs'][] = ['label' => 'Sakip Penjabatskpd Cascadingsubkegiatans', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->refpenjabatcascadingsubkegiatan_id, 'url' => ['view', 'refpenjabatcascadingsubkegiatan_id' => $model->refpenjabatcascadingsubkegiatan_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="sakip-penjabatskpd-cascadingsubkegiatan-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
