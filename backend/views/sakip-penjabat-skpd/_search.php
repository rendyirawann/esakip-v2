<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var backend\models\search\SakipPenjabatSkpdSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="sakip-penjabat-skpd-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'refpenjabatskpd_id') ?>

    <?= $form->field($model, 'refskpd_id') ?>

    <?= $form->field($model, 'refperiode_id') ?>

    <?= $form->field($model, 'nama_penjabat') ?>

    <?= $form->field($model, 'nip_penjabat') ?>

    <?php // echo $form->field($model, 'jabatan_eselon') ?>

    <?php // echo $form->field($model, 'pangkat_eselon') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
