<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var frontend\models\search\SakipIndikatortujuanrenstraSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="sakip-indikatortujuanrenstra-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'refindikatortujuanrenstra_id') ?>

    <?= $form->field($model, 'uraian_indikatortujuanrenstra') ?>

    <?= $form->field($model, 'reftujuanrenstra_id') ?>

    <?= $form->field($model, 'refsasaranrenstra_id') ?>

    <?= $form->field($model, 'refskpd_id') ?>

    <?= $form->field($model, 'refperiode_id') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>