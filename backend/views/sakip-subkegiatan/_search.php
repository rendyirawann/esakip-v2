<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var backend\models\search\SakipSubkegiatanSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="sakip-subkegiatan-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'refsubkegiatan_id') ?>

    <?= $form->field($model, 'kode_subkegiatan') ?>

    <?= $form->field($model, 'nama_subkegiatan') ?>

    <?= $form->field($model, 'refurusan_id') ?>

    <?= $form->field($model, 'refbidang_id') ?>

    <?php // echo $form->field($model, 'refprogram_id') ?>

    <?php // echo $form->field($model, 'refkegiatan_id') ?>

    <?php // echo $form->field($model, 'subkegiatan_isaktif') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
