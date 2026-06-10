<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var frontend\models\SakipSasaranrenstraP $model */

$this->title = 'Update Sakip Sasaranrenstra P: ' . $model->refsasaranrenstra_p_id;
$this->params['breadcrumbs'][] = ['label' => 'Sakip Sasaranrenstra Ps', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->refsasaranrenstra_p_id, 'url' => ['view', 'refsasaranrenstra_p_id' => $model->refsasaranrenstra_p_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="sakip-sasaranrenstra-p-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_formupdatesasaranrenstra', [
        'model' => $model,
    ]) ?>

</div>