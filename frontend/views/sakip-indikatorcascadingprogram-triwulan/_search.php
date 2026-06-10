<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var frontend\models\search\SakipIndikatorcascadingprogramTriwulanSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="sakip-indikatorcascadingprogram-triwulan-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'refindikatorprogramtriwulan_id') ?>

    <?= $form->field($model, 'refindikatorprogram_id') ?>

    <?= $form->field($model, 'refcascadingprogram_id') ?>

    <?= $form->field($model, 'refsasaranrenstra_id') ?>

    <?= $form->field($model, 'refskpd_id') ?>

    <?php // echo $form->field($model, 'refperiode_id') ?>

    <?php // echo $form->field($model, 'reftriwulan_id') ?>

    <?php // echo $form->field($model, 'refbidang_id') ?>

    <?php // echo $form->field($model, 'refprogram_id') ?>

    <?php // echo $form->field($model, 'triwulan_target_rkt') ?>

    <?php // echo $form->field($model, 'triwulan_target_rkt_p') ?>

    <?php // echo $form->field($model, 'triwulan_target_pk') ?>

    <?php // echo $form->field($model, 'triwulan_target_pk_p') ?>

    <?php // echo $form->field($model, 'triwulan_realisasi') ?>

    <?php // echo $form->field($model, 'triwulan_capaian') ?>

    <?php // echo $form->field($model, 'triwulan_keterangan') ?>

    <?php // echo $form->field($model, 'triwulan_analisis') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
