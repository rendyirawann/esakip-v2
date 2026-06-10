<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var frontend\models\search\SakipTujuanrenstraSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="sakip-tujuanrenstra-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'reftujuanrenstra_id') ?>

    <?= $form->field($model, 'uraian_tujuanrenstra') ?>

    <?= $form->field($model, 'refskpd_id') ?>

    <?= $form->field($model, 'refmisi_id') ?>

    <?= $form->field($model, 'reftujuan_id') ?>

    <?php // echo $form->field($model, 'refsasaranrenstra_id') 
    ?>

    <?php // echo $form->field($model, 'refperiode_id') 
    ?>

    <?php // echo $form->field($model, 'user_create') 
    ?>

    <?php // echo $form->field($model, 'date_create') 
    ?>

    <?php // echo $form->field($model, 'user_edit') 
    ?>

    <?php // echo $form->field($model, 'date_edit') 
    ?>

    <?php // echo $form->field($model, 'user_delete') 
    ?>

    <?php // echo $form->field($model, 'date_delete') 
    ?>

    <?php // echo $form->field($model, 'tujuanrenstra_isaktif') 
    ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>