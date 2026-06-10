<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var frontend\models\search\SakipLkeSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="sakip-lke-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'reflke_id') ?>

    <?= $form->field($model, 'refperiode_id') ?>

    <?= $form->field($model, 'refskpd_id') ?>

    <?= $form->field($model, 'reflkekomponen_id') ?>

    <?= $form->field($model, 'reflkesubkomponen_id') ?>

    <?php // echo $form->field($model, 'unit_jawaban') ?>

    <?php // echo $form->field($model, 'unit_nilai') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
