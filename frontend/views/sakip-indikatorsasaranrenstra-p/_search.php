<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var frontend\models\search\SakipIndikatorsasaranrenstraSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="sakip-indikatorsasaranrenstra-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'refindikatorsasaranrenstra_id') ?>

    <?= $form->field($model, 'uraian_indikatorsasaranrenstra') ?>

    <?= $form->field($model, 'refsasaranrenstra_id') ?>

    <?= $form->field($model, 'refskpd_id') ?>

    <?= $form->field($model, 'refperiode_id') ?>

    <?php // echo $form->field($model, 'satuan_target') 
    ?>

    <?php // echo $form->field($model, 'target_rkt') 
    ?>

    <?php // echo $form->field($model, 'target_rkt_p') 
    ?>

    <?php // echo $form->field($model, 'target_pk') 
    ?>

    <?php // echo $form->field($model, 'target_pk_p') 
    ?>

    <?php // echo $form->field($model, 'realisasi') 
    ?>

    <?php // echo $form->field($model, 'capaian') 
    ?>

    <?php // echo $form->field($model, 'analisis') 
    ?>

    <?php // echo $form->field($model, 'keterangan') 
    ?>

    <?php // echo $form->field($model, 'indikatorsasaranrenstra_isaktif') 
    ?>

    <?php // echo $form->field($model, 'iku_isaktif') 
    ?>

    <?php // echo $form->field($model, 'pk_isaktif') 
    ?>

    <?php // echo $form->field($model, 'alasan_sasaranrenstra') 
    ?>

    <?php // echo $form->field($model, 'formulasi_sasaranrenstra') 
    ?>

    <?php // echo $form->field($model, 'kriteria_sasaranrenstra') 
    ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>