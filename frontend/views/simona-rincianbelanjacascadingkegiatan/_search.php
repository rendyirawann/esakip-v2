<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var frontend\models\search\SimonaRincianbelanjacascadingkegiatanSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="simona-rincianbelanjacascadingkegiatan-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'refsimonarincianbelanjacascadingkegiatan_id') ?>

    <?= $form->field($model, 'refsimonacascadingkegiatan_id') ?>

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
