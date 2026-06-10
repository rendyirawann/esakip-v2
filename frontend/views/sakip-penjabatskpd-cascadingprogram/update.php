<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var frontend\models\SakipPenjabatskpdCascadingprogram $model */

$this->title = 'Update Sakip Penjabatskpd Cascadingprogram: ' . $model->refpenjabatcascadingprogram_id;
$this->params['breadcrumbs'][] = ['label' => 'Sakip Penjabatskpd Cascadingprograms', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->refpenjabatcascadingprogram_id, 'url' => ['view', 'refpenjabatcascadingprogram_id' => $model->refpenjabatcascadingprogram_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="sakip-penjabatskpd-cascadingprogram-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_formupdate', [
        'model' => $model,
    ]) ?>

</div>