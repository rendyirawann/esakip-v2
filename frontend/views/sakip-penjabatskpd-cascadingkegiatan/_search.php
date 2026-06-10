<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var frontend\models\search\SakipPenjabatskpdCascadingkegiatanSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="sakip-penjabatskpd-cascadingkegiatan-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'refpenjabatcascadingkegiatan_id') ?>

    <?= $form->field($model, 'refpenjabatskpd_id') ?>

    <?= $form->field($model, 'refeselon_id') ?>

    <?= $form->field($model, 'refcascadingprogram_id') ?>

    <?= $form->field($model, 'refcascadingkegiatan_id') ?>

    <?php // echo $form->field($model, 'refindikatorkegiatan_id') ?>

    <?php // echo $form->field($model, 'refskpd_id') ?>

    <?php // echo $form->field($model, 'refperiode_id') ?>

    <?php // echo $form->field($model, 'refsasaranrenstra_id') ?>

    <?php // echo $form->field($model, 'refindikatorsasaranrenstra_id') ?>

    <?php // echo $form->field($model, 'refprogram_id') ?>

    <?php // echo $form->field($model, 'refkegiatan_id') ?>

    <?php // echo $form->field($model, 'uraian_sasarankegiatan') ?>

    <?php // echo $form->field($model, 'uraian_indikatorkegiatan') ?>

    <?php // echo $form->field($model, 'kegiatan_target') ?>

    <?php // echo $form->field($model, 'kegiatan_satuan') ?>

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
