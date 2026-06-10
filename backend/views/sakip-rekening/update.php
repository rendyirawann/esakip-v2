<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\SakipRekening $model */

$this->title = 'Update Sakip Rekening: ' . $model->refrekening_id;
$this->params['breadcrumbs'][] = ['label' => 'Sakip Rekenings', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->refrekening_id, 'url' => ['view', 'refrekening_id' => $model->refrekening_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="sakip-rekening-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
