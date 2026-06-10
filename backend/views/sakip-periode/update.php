<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\SakipPeriode $model */

$this->title = 'Update Sakip Periode: ' . $model->refperiode_id;
$this->params['breadcrumbs'][] = ['label' => 'Sakip Periodes', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->refperiode_id, 'url' => ['view', 'refperiode_id' => $model->refperiode_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="sakip-periode-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
