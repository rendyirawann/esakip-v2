<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var backend\models\search\SakipSubunitSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="sakip-subunit-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'refsubunit_id') ?>

    <?= $form->field($model, 'kode_subunit') ?>

    <?= $form->field($model, 'nama_subunit') ?>

    <?= $form->field($model, 'subunit_isaktif') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
