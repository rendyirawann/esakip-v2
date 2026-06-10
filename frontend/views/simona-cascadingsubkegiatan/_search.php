<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var frontend\models\search\SimonaCascadingsubkegiatanSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="simona-cascadingsubkegiatan-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'refsimonacascadingsubkegiatan_id') ?>

    <?= $form->field($model, 'refcascadingprogram_id') ?>

    <?= $form->field($model, 'refcascadingkegiatan_id') ?>

    <?= $form->field($model, 'refcascadingsubkegiatan_id') ?>

    <?= $form->field($model, 'refskpd_id') ?>

    <?php // echo $form->field($model, 'refsasaranrenstra_id') ?>

    <?php // echo $form->field($model, 'refindikatorsasaranrenstra_id') ?>

    <?php // echo $form->field($model, 'refprogram_id') ?>

    <?php // echo $form->field($model, 'refkegiatan_id') ?>

    <?php // echo $form->field($model, 'refsubkegiatan_id') ?>

    <?php // echo $form->field($model, 'uraian_sasaransubkegiatan') ?>

    <?php // echo $form->field($model, 'uraian_indikatorsubkegiatan') ?>

    <?php // echo $form->field($model, 'refperiode_id') ?>

    <?php // echo $form->field($model, 'subkegiatan_target') ?>

    <?php // echo $form->field($model, 'subkegiatan_satuan') ?>

    <?php // echo $form->field($model, 'refpegawaibappeda_id') ?>

    <?php // echo $form->field($model, 'date_start') ?>

    <?php // echo $form->field($model, 'expired_date') ?>

    <?php // echo $form->field($model, 'status_simonacascadingsubkegiatan') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
