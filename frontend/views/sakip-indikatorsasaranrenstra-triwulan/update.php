<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var frontend\models\SakipIndikatorsasaranrenstraTriwulan $model */

$this->title = 'Update Sakip Indikatorsasaranrenstra Triwulan: ' . $model->refindikatorsasaranrenstratriwulan_id;
$this->params['breadcrumbs'][] = ['label' => 'Sakip Indikatorsasaranrenstra Triwulans', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->refindikatorsasaranrenstratriwulan_id, 'url' => ['view', 'refindikatorsasaranrenstratriwulan_id' => $model->refindikatorsasaranrenstratriwulan_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="sakip-indikatorsasaranrenstra-triwulan-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
