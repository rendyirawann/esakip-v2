<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var frontend\models\search\SakipCascadingkegiatanSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="sakip-cascadingkegiatan-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'refcascadingkegiatan_id') ?>

    <?= $form->field($model, 'refcascadingprogram_id') ?>

    <?= $form->field($model, 'refprogram_id') ?>

    <?= $form->field($model, 'refkegiatan_id') ?>

    <?= $form->field($model, 'uraian_sasarankegiatan') ?>

    <?php // echo $form->field($model, 'uraian_indikatorkegiatan') ?>

    <?php // echo $form->field($model, 'refperiode_id') ?>

    <?php // echo $form->field($model, 'refskpd_id') ?>

    <?php // echo $form->field($model, 'kegiatan_target') ?>

    <?php // echo $form->field($model, 'kegiatan_satuan') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
