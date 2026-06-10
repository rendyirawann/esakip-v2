<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var backend\models\search\SakipSumberdanaSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="sakip-sumberdana-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'refsumberdana_id') ?>

    <?= $form->field($model, 'kode_sumberdana') ?>

    <?= $form->field($model, 'nama_sumberdana') ?>

    <?= $form->field($model, 'sumberdana_isaktif') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
