<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var frontend\models\search\SimonaMediacascadingkegiatanSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="simona-mediacascadingkegiatan-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'refsimonamediacascadingkegiatan_id') ?>

    <?= $form->field($model, 'refsimonacascadingkegiatan_id') ?>

    <?= $form->field($model, 'file') ?>

    <?= $form->field($model, 'nama_file') ?>

    <?= $form->field($model, 'refuser_id') ?>

    <?php // echo $form->field($model, 'refskpd_id') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
