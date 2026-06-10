<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var backend\models\search\SakipProgramSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="sakip-program-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'refprogram_id') ?>

    <?= $form->field($model, 'kode_program') ?>

    <?= $form->field($model, 'nama_program') ?>

    <?= $form->field($model, 'refurusan_id') ?>

    <?= $form->field($model, 'refbidang_id') ?>

    <?php // echo $form->field($model, 'program_isaktif') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
