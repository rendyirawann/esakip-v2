<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var frontend\models\search\SakipIndikatorcascadingsubkegiatanTriwulanSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="sakip-indikatorcascadingsubkegiatan-triwulan-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'refindikatorsubkegiatantriwulan_id') ?>

    <?= $form->field($model, 'refindikatorsubkegiatan_id') ?>

    <?= $form->field($model, 'refcascadingprogram_id') ?>

    <?= $form->field($model, 'refcascadingkegiatan_id') ?>

    <?= $form->field($model, 'refcascadingsubkegiatan_id') ?>

    <?php // echo $form->field($model, 'refsasaranrenstra_id') 
    ?>

    <?php // echo $form->field($model, 'refskpd_id') 
    ?>

    <?php // echo $form->field($model, 'refperiode_id') 
    ?>

    <?php // echo $form->field($model, 'refprogram_id') 
    ?>

    <?php // echo $form->field($model, 'refkegiatan_id') 
    ?>

    <?php // echo $form->field($model, 'refsubkegiatan_id') 
    ?>

    <?php // echo $form->field($model, 'triwulan_target_rkt') 
    ?>

    <?php // echo $form->field($model, 'triwulan_target_rkt_p') 
    ?>

    <?php // echo $form->field($model, 'triwulan_target_pk') 
    ?>

    <?php // echo $form->field($model, 'triwulan_target_pk_p') 
    ?>

    <?php // echo $form->field($model, 'triwulan_realisasi') 
    ?>

    <?php // echo $form->field($model, 'triwulan_capaian') 
    ?>

    <?php // echo $form->field($model, 'triwulan_keterangan') 
    ?>

    <?php // echo $form->field($model, 'triwulan_analisis') 
    ?>

    <?php // echo $form->field($model, 'triwulan_penyerapan_anggaran') 
    ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>