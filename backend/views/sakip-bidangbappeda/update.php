<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\SakipBidangbappeda $model */

$this->title = 'Update Sakip Bidangbappeda: ' . $model->refbidangbappeda_id;
$this->params['breadcrumbs'][] = ['label' => 'Sakip Bidangbappedas', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->refbidangbappeda_id, 'url' => ['view', 'refbidangbappeda_id' => $model->refbidangbappeda_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="sakip-bidangbappeda-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
