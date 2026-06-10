<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var backend\models\search\SakipSasaranPSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="sakip-sasaran-p-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'refsasaran_p_id') ?>

    <?= $form->field($model, 'uraian_sasaran_p') ?>

    <?= $form->field($model, 'refperiode_id') ?>

    <?= $form->field($model, 'refvisi_p_id') ?>

    <?= $form->field($model, 'refmisi_p_id') ?>

    <?php // echo $form->field($model, 'reftujuan_id') 
    ?>

    <?php // echo $form->field($model, 'sasaran_isaktif') 
    ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>