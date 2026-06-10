<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var frontend\models\search\LaporanRenjaKataPengantarSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="laporan-renja-kata-pengantar-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'laporan_renja_kata_pengantar_id') ?>

    <?= $form->field($model, 'uraian_katapengantar') ?>

    <?= $form->field($model, 'refperiode_id') ?>

    <?= $form->field($model, 'refskpd_id') ?>

    <?= $form->field($model, 'halaman_renja') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
