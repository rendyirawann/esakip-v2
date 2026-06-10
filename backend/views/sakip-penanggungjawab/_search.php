<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var backend\models\search\SakipPenanggungjawabSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="sakip-penanggungjawab-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'refpenanggungjawab_id') ?>

    <?= $form->field($model, 'refpegawai_id') ?>

    <?= $form->field($model, 'refbidangbappeda_id') ?>

    <?= $form->field($model, 'refuser_id') ?>

    <?= $form->field($model, 'refskpd_id') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
