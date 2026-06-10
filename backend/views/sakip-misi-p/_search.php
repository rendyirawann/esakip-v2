<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var backend\models\search\SakipMisiPSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="sakip-misi-p-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'refmisi_p_id') ?>

    <?= $form->field($model, 'uraian_misi_p') ?>

    <?= $form->field($model, 'refperiode_id') ?>

    <?= $form->field($model, 'refvisi_p_id') ?>

    <?= $form->field($model, 'misi_p_isaktif') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>