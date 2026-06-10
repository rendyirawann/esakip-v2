<style>
    #loading-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 9999;
    }

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

/** @var yii\web\View $this */
/** @var backend\models\SakipPeriode5tahun $model */
/** @var yii\widgets\ActiveForm $form */

$this->registerJsFile('@web/lightapp/assets/js/plugins/choices.min.js', ['depends' => [\yii\web\JqueryAsset::class]]);
$this->registerCssFile('@web/css/choices.min.css');

$this->registerJs("
    $(document).ready(function() {
        $('form#dataperiode5tahun').off('submit').on('submit', function(e) {
            e.preventDefault();

            var $form = $(this);
            var $submitBtn = $form.find(':submit');

            $submitBtn.prop('disabled', true);
            $('#loading-overlay').show();

            $.ajax({
                type: $form.attr('method'),
                url: $form.attr('action'),
                data: $form.serialize(),
                success: function(response) {
                    if (response.success) {
                        window.location.href = response.redirect;
                    } else {
                        console.log(response.errors);
                    }
                },
                error: function() {
                    console.log('Terjadi kesalahan saat mengirim data.');
                },
                complete: function() {
                    $submitBtn.prop('disabled', false);
                    $('#loading-overlay').hide();
                }
            });
        });
    });
");

?>

<div class="loading-overlay" id="loading-overlay"></div>

<div class="data-periode5tahun-form">

    <?php 
    $form = ActiveForm::begin([
        'id' => 'dataperiode5tahun',
        'options' => ['enctype' => 'multipart/form-data'],
    ]); 
    ?>

    <?= $form->field($model, 'nama_periode')->textInput(['maxlength' => true])->label('Nama Periode 5 Tahun') ?>

    <?= $form->field($model, 'tahun_mulai')->textInput()->label('Tahun Mulai') ?>

    <?= $form->field($model, 'tahun_selesai')->textInput()->label('Tahun Selesai') ?>

    <?= $form->field($model, 'is_aktif')->radioList(
        ['1' => 'Aktif', '0' => 'Tidak Aktif'],
        [
            'item' => function($index, $label, $name, $checked, $value) {
                return '<label class="radio-inline">' . Html::radio($name, $checked, ['value' => $value]) . $label . '</label>';
            }
        ]
    )->label('Status') ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
