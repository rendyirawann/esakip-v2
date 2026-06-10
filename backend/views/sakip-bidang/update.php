<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\SakipBidang $model */

$this->title = 'Update Sakip Bidang: ' . $model->refbidang_id;
$this->params['breadcrumbs'][] = ['label' => 'Sakip Bidangs', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->refbidang_id, 'url' => ['view', 'refbidang_id' => $model->refbidang_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="sakip-bidang-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
