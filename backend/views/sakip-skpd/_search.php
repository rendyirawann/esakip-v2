<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var backend\models\search\SakipSkpdSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="sakip-skpd-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'refskpd_id') ?>

    <?= $form->field($model, 'kode_skpd') ?>

    <?= $form->field($model, 'nama_skpd') ?>

    <?= $form->field($model, 'kepala_skpd') ?>

    <?= $form->field($model, 'nip_kepala') ?>

    <?php // echo $form->field($model, 'jabatan_kepala') 
    ?>

    <?php // echo $form->field($model, 'pangkat_kepala') 
    ?>

    <?php // echo $form->field($model, 'refurusan_id') 
    ?>

    <?php // echo $form->field($model, 'refbidang_id') 
    ?>

    <?php // echo $form->field($model, 'refskpd_unit') 
    ?>

    <?php // echo $form->field($model, 'refskpd_keterangan') 
    ?>

    <?php // echo $form->field($model, 'skpd_isaktif') 
    ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>