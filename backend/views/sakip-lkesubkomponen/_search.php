<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var backend\models\search\SakipLkesubkomponenSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="sakip-lkesubkomponen-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'reflkesubkomponen_id') ?>

    <?= $form->field($model, 'reflkekomponen_id') ?>

    <?= $form->field($model, 'uraian_lkesubkomponen') ?>

    <?= $form->field($model, 'bobot_lkesubkomponen') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
