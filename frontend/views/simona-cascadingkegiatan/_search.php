<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var frontend\models\search\SimonaCascadingkegiatanSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="simona-cascadingkegiatan-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'refsimonacascadingkegiatan_id') ?>

    <?= $form->field($model, 'refcascadingprogram_id') ?>

    <?= $form->field($model, 'refcascadingkegiatan_id') ?>

    <?= $form->field($model, 'refskpd_id') ?>

    <?= $form->field($model, 'refsasaranrenstra_id') ?>

    <?php // echo $form->field($model, 'refindikatorsasaranrenstra_id') ?>

    <?php // echo $form->field($model, 'refprogram_id') ?>

    <?php // echo $form->field($model, 'refkegiatan_id') ?>

    <?php // echo $form->field($model, 'uraian_sasarankegiatan') ?>

    <?php // echo $form->field($model, 'uraian_indikatorkegiatan') ?>

    <?php // echo $form->field($model, 'refperiode_id') ?>

    <?php // echo $form->field($model, 'kegiatan_target') ?>

    <?php // echo $form->field($model, 'kegiatan_satuan') ?>

    <?php // echo $form->field($model, 'refpegawaibappeda_id') ?>

    <?php // echo $form->field($model, 'date_start') ?>

    <?php // echo $form->field($model, 'expired_date') ?>

    <?php // echo $form->field($model, 'status_simonacascadingkegiatan') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
