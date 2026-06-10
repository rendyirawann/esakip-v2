<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var frontend\models\search\SimonaRincianbelanjacascadingsubkegiatanSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="simona-rincianbelanjacascadingsubkegiatan-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'refsimonarincianbelanjacascadingsubkegiatan_id') ?>

    <?= $form->field($model, 'refsimonacascadingsubkegiatan_id') ?>

    <?= $form->field($model, 'detail_rincianbelanja') ?>

    <?= $form->field($model, 'satuan_rincianbelanja') ?>

    <?= $form->field($model, 'jumlah_rincianbelanja') ?>

    <?php // echo $form->field($model, 'anggaran_rincianbelanja') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
