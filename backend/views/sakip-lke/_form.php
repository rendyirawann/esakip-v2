<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var backend\models\SakipLke $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="sakip-lke-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'refperiode_id')->textInput() ?>

    <?= $form->field($model, 'refskpd_id')->textInput() ?>

    <?= $form->field($model, 'reflkekomponen_id')->textInput() ?>

    <?= $form->field($model, 'reflkesubkomponen_id')->textInput() ?>

    <?= $form->field($model, 'unit_jawaban')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'unit_nilai')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
