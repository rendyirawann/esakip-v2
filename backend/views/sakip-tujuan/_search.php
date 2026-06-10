<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var backend\models\search\SakipTujuanSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="sakip-tujuan-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'reftujuan_id') ?>

    <?= $form->field($model, 'uraian_tujuan') ?>

    <?= $form->field($model, 'indikator_tujuan') ?>

    <?= $form->field($model, 'refvisi_id') ?>

    <?= $form->field($model, 'refmisi_id') ?>

    <?php // echo $form->field($model, 'refperiode_id') ?>

    <?php // echo $form->field($model, 'tujuan_isaktif') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
