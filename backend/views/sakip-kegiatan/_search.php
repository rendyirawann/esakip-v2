<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var backend\models\search\SakipKegiatanSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="sakip-kegiatan-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'refkegiatan_id') ?>

    <?= $form->field($model, 'kode_kegiatan') ?>

    <?= $form->field($model, 'nama_kegiatan') ?>

    <?= $form->field($model, 'refurusan_id') ?>

    <?= $form->field($model, 'refbidang_id') ?>

    <?php // echo $form->field($model, 'refprogram_id') ?>

    <?php // echo $form->field($model, 'kegiatan_isaktif') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
