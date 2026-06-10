<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var frontend\models\search\SakipVisiSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="sakip-visi-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'refvisi_id') ?>

    <?= $form->field($model, 'uraian_visi') ?>

    <?= $form->field($model, 'penjabaran_visi') ?>

    <?= $form->field($model, 'refperiode_id') ?>

    <?= $form->field($model, 'visi_isaktif') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
