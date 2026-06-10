<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var backend\models\SakipUrusan $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="sakip-urusan-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'kode_urusan')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'nama_urusan')->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
