<style>
    #loading-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        /* Dark semi-transparent background */
        z-index: 9999;
        /* Make sure it's on top of everything */
    }

    /* Style the loading spinner (optional) */
    #loading-overlay:after {
        content: "";
        position: absolute;
        top: 50%;
        left: 50%;
        width: 40px;
        height: 40px;
        margin-top: -20px;
        margin-left: -20px;
        border: 4px solid #f3f3f3;
        border-top: 4px solid #3498db;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }
</style>
<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\UploadForm */

$this->title = 'Upload CSV to Database';
$this->params['breadcrumbs'][] = $this->title;
$this->registerJs("
    $(document).on('beforeSubmit', 'form#sakipsubkegiatan', function(){
        $('#loading-overlay').show();
    });

    $(document).on('ajaxComplete', function(){
        $('#loading-overlay').hide();
    });

");
?>

<div class="loading-overlay" id="loading-overlay"></div>

<div class="sakip-subkegiatan-upload">

    <div class="sakip-subkegiatan-form">

        <?php $form = ActiveForm::begin([
            'id' => 'sakipsubkegiatan',
            'options' => ['enctype' => 'multipart/form-data'],
        ]); ?>

        <?= $form->field($model, 'file')->fileInput()->label('Upload CSV')->hint('Maksimal Ukuran File Upload: 10MB') ?>

        <div class="form-group">
            <?= Html::submitButton('Upload', ['class' => 'btn btn-primary']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>