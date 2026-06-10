<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\SakipCascadingprogram */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="sakip-cascadingprogram-export-excel">

    <h1>Export Excel</h1>

    <?php $form = ActiveForm::begin([
        'action' => ['sakip-cascadingprogram/action-export-excel'],
        'method' => 'post',
    ]); ?>

    <div class="form-group">
        <?= Html::submitButton('Export to Excel', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>