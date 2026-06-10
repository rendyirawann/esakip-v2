<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var backend\models\search\SakipSatuanhargaSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="sakip-satuanharga-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'refsatuanharga_id') ?>

    <?= $form->field($model, 'kode_satuanharga') ?>

    <?= $form->field($model, 'nama_satuanharga') ?>

    <?= $form->field($model, 'satuanharga_isaktif') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
