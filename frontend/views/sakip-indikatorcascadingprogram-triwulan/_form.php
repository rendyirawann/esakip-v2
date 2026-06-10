<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var frontend\models\SakipIndikatorcascadingprogramTriwulan $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="sakip-indikatorcascadingprogram-triwulan-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'refindikatorprogram_id')->textInput() ?>

    <?= $form->field($model, 'refcascadingprogram_id')->textInput() ?>

    <?= $form->field($model, 'refsasaranrenstra_id')->textInput() ?>

    <?= $form->field($model, 'refindikatorsasaranrenstra_id')->textInput() ?>

    <?= $form->field($model, 'refskpd_id')->textInput() ?>

    <?= $form->field($model, 'refperiode_id')->textInput() ?>

    <?= $form->field($model, 'reftriwulan_id')->textInput() ?>

    <?= $form->field($model, 'refbidang_id')->textInput() ?>

    <?= $form->field($model, 'refprogram_id')->textInput() ?>

    <?= $form->field($model, 'triwulan_target_rkt')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'triwulan_target_rkt_p')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'triwulan_target_pk')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'triwulan_target_pk_p')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'triwulan_realisasi')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'triwulan_capaian')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'triwulan_keterangan')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'triwulan_analisis')->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>