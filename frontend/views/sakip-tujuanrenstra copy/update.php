<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var frontend\models\SakipTujuanrenstra $model */

$this->title = 'Update Sakip Tujuanrenstra: ' . $model->reftujuanrenstra_id;
$this->params['breadcrumbs'][] = ['label' => 'Sakip Tujuanrenstras', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->reftujuanrenstra_id, 'url' => ['view', 'reftujuanrenstra_id' => $model->reftujuanrenstra_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="sakip-tujuanrenstra-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_formupdate', [
        'model' => $model,
    ]) ?>

</div>