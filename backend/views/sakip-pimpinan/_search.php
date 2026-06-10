<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var backend\models\search\SakipPimpinanSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="sakip-pimpinan-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'refpimpinan_id') ?>

    <?= $form->field($model, 'refperiode_id') ?>

    <?= $form->field($model, 'nama_pimpinan') ?>

    <?= $form->field($model, 'jabatan_pimpinan') ?>

    <?= $form->field($model, 'nama_wpimpinan') ?>

    <?php // echo $form->field($model, 'jabatan_wpimpinan') ?>

    <?php // echo $form->field($model, 'user_edit') ?>

    <?php // echo $form->field($model, 'date_edit') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
