<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var frontend\models\search\SakipCascadingprogramSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="sakip-cascadingprogram-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'refcascadingprogram_id') ?>

    <?= $form->field($model, 'refsasaran_id') ?>

    <?= $form->field($model, 'refskpd_id') ?>

    <?= $form->field($model, 'reftujuan_id') ?>

    <?= $form->field($model, 'refmisi_id') ?>

    <?php // echo $form->field($model, 'refsasaranrenstra_id') ?>

    <?php // echo $form->field($model, 'refindikatorsasaranrenstra_id') ?>

    <?php // echo $form->field($model, 'refbidang_id') ?>

    <?php // echo $form->field($model, 'refprogram_id') ?>

    <?php // echo $form->field($model, 'uraian_sasaranprogram') ?>

    <?php // echo $form->field($model, 'uraian_indikatorprogram') ?>

    <?php // echo $form->field($model, 'refperiode_id') ?>

    <?php // echo $form->field($model, 'program_target') ?>

    <?php // echo $form->field($model, 'program_satuan') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
