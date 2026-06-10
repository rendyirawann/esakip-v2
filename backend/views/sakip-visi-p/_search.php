<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var backend\models\search\SakipVisiPSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="sakip-visi-p-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'refvisi_p_id') ?>

    <?= $form->field($model, 'uraian_visi_p') ?>

    <?= $form->field($model, 'penjabaran_visi_p') ?>

    <?= $form->field($model, 'refperiode_id') ?>

    <?= $form->field($model, 'visi_p_isaktif') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>