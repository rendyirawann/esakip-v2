<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var frontend\models\search\SimonaKeluaranmediacascadingsubkegiatanSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="simona-keluaranmediacascadingsubkegiatan-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'refsimonakeluaranmediacascadingsubkegiatan_id') ?>

    <?= $form->field($model, 'refsimonacascadingsubkegiatan_id') ?>

    <?= $form->field($model, 'refsimonarincianbelanjacascadingsubkegiatan_id') ?>

    <?= $form->field($model, 'file') ?>

    <?= $form->field($model, 'nama_file') ?>

    <?php // echo $form->field($model, 'refuser_id') 
    ?>

    <?php // echo $form->field($model, 'refskpd_id') 
    ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>