<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var frontend\models\SimonaMediacascadingsubkegiatan $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="simona-mediacascadingsubkegiatan-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'refsimonacascadingsubkegiatan_id')->textInput() ?>

    <?= $form->field($model, 'file')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'nama_file')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'refuser_id')->textInput() ?>

    <?= $form->field($model, 'refskpd_id')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
