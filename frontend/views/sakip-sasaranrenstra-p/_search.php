<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var frontend\models\search\SakipSasaranrenstraPSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="sakip-sasaranrenstra-p-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'refsasaranrenstra_p_id') ?>

    <?= $form->field($model, 'refsasaranrenstra_id') ?>

    <?= $form->field($model, 'uraian_sasaranrenstra_p') ?>

    <?= $form->field($model, 'refskpd_id') ?>

    <?= $form->field($model, 'refsasaran_p_id') ?>

    <?php // echo $form->field($model, 'refvisi_p_id') ?>

    <?php // echo $form->field($model, 'refmisi_p_id') ?>

    <?php // echo $form->field($model, 'reftujuan_p_id') ?>

    <?php // echo $form->field($model, 'refperiode_id') ?>

    <?php // echo $form->field($model, 'reftujuanrenstra_p_id') ?>

    <?php // echo $form->field($model, 'sasaranrenstra_p_isaktif') ?>

    <?php // echo $form->field($model, 'alasan_sasaranrenstra_p') ?>

    <?php // echo $form->field($model, 'formulasi_sasaranrenstra_p') ?>

    <?php // echo $form->field($model, 'kriteria_sasaranrenstra_p') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
