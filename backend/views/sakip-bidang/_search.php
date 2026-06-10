<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var backend\models\search\SakipBidangSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="sakip-bidang-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'refbidang_id') ?>

    <?= $form->field($model, 'kode_bidang') ?>

    <?= $form->field($model, 'nama_bidang') ?>

    <?= $form->field($model, 'bidang_isaktif') ?>

    <?= $form->field($model, 'refurusan_id') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
