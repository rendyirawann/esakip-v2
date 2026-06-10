<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var frontend\models\SakipVisi $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="sakip-visi-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'uraian_visi')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'penjabaran_visi')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'refperiode_id')->textInput() ?>

    <?= $form->field($model, 'visi_isaktif')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
