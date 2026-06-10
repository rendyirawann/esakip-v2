<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var frontend\models\SakipSasaranrenstra $model */

$this->title = 'Update Sakip Sasaranrenstra: ' . $model->refsasaranrenstra_id;
$this->params['breadcrumbs'][] = ['label' => 'Sakip Sasaranrenstras', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->refsasaranrenstra_id, 'url' => ['view', 'refsasaranrenstra_id' => $model->refsasaranrenstra_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="sakip-sasaranrenstra-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_formupdatesasaranrenstra', [
        'model' => $model,
    ]) ?>

</div>