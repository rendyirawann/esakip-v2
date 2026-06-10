<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var frontend\models\search\SakipPenjabatskpdCascadingprogramSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="sakip-penjabatskpd-cascadingprogram-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'refpenjabatcascadingprogram_id') ?>

    <?= $form->field($model, 'refpenjabatskpd_id') ?>

    <?= $form->field($model, 'refeselon_id') ?>

    <?= $form->field($model, 'refcascadingprogram_id') ?>

    <?= $form->field($model, 'refindikatorprogram_id') ?>

    <?php // echo $form->field($model, 'refskpd_id') ?>

    <?php // echo $form->field($model, 'refperiode_id') ?>

    <?php // echo $form->field($model, 'refsasaranrenstra_id') ?>

    <?php // echo $form->field($model, 'refindikatorsasaranrenstra_id') ?>

    <?php // echo $form->field($model, 'refbidang_id') ?>

    <?php // echo $form->field($model, 'refprogram_id') ?>

    <?php // echo $form->field($model, 'uraian_sasaranprogram') ?>

    <?php // echo $form->field($model, 'uraian_indikatorprogram') ?>

    <?php // echo $form->field($model, 'program_target') ?>

    <?php // echo $form->field($model, 'program_satuan') ?>

    <?php // echo $form->field($model, 'target_rkt') ?>

    <?php // echo $form->field($model, 'target_rkt_p') ?>

    <?php // echo $form->field($model, 'target_pk') ?>

    <?php // echo $form->field($model, 'target_pk_p') ?>

    <?php // echo $form->field($model, 'realisasi') ?>

    <?php // echo $form->field($model, 'capaian') ?>

    <?php // echo $form->field($model, 'keterangan') ?>

    <?php // echo $form->field($model, 'analisis') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
