<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var frontend\models\SakipIndikatorcascadingsubkegiatanTriwulan $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="sakip-indikatorcascadingsubkegiatan-triwulan-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'refindikatorsubkegiatan_id')->textInput() ?>

    <?= $form->field($model, 'refcascadingprogram_id')->textInput() ?>

    <?= $form->field($model, 'refcascadingkegiatan_id')->textInput() ?>

    <?= $form->field($model, 'refcascadingsubkegiatan_id')->textInput() ?>

    <?= $form->field($model, 'refsasaranrenstra_id')->textInput() ?>

    <?= $form->field($model, 'refindikatorsasaranrenstra_id')->textInput() ?>

    <?= $form->field($model, 'refskpd_id')->textInput() ?>

    <?= $form->field($model, 'refperiode_id')->textInput() ?>

    <?= $form->field($model, 'refprogram_id')->textInput() ?>

    <?= $form->field($model, 'refkegiatan_id')->textInput() ?>

    <?= $form->field($model, 'refsubkegiatan_id')->textInput() ?>

    <?= $form->field($model, 'triwulan_target_rkt')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'triwulan_target_rkt_p')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'triwulan_target_pk')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'triwulan_target_pk_p')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'triwulan_realisasi')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'triwulan_capaian')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'triwulan_keterangan')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'triwulan_analisis')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'triwulan_penyerapan_anggaran')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>