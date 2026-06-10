<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var backend\models\search\SakipRekeningSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="sakip-rekening-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'refrekening_id') ?>

    <?= $form->field($model, 'kode_rekening') ?>

    <?= $form->field($model, 'nama_rekening') ?>

    <?= $form->field($model, 'rekening_isaktif') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
