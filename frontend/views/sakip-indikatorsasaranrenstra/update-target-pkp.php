<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var frontend\models\SakipIndikatorsasaranrenstra $model */

$this->title = 'Update Sakip Indikatorsasaranrenstra: ' . $model->refindikatorsasaranrenstra_id;
$this->params['breadcrumbs'][] = ['label' => 'Sakip Indikatorsasaranrenstras', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->refindikatorsasaranrenstra_id, 'url' => ['view', 'refindikatorsasaranrenstra_id' => $model->refindikatorsasaranrenstra_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="sakip-indikatorsasaranrenstra-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_formupdatetargetpkp', [
        'model' => $model,
    ]) ?>

</div>