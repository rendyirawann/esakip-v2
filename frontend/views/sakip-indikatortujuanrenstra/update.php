<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var frontend\models\SakipIndikatortujuanrenstra $model */

$this->title = 'Update Sakip Indikatortujuanrenstra: ' . $model->refindikatortujuanrenstra_id;
$this->params['breadcrumbs'][] = ['label' => 'Sakip Indikatortujuanrenstras', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->refindikatortujuanrenstra_id, 'url' => ['view', 'refindikatortujuanrenstra_id' => $model->refindikatortujuanrenstra_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="sakip-indikatortujuanrenstra-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_formupdate', [
        'model' => $model,
    ]) ?>

</div>