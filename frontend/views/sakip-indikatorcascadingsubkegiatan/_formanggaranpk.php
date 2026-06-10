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

use frontend\models\SakipSasaran;
use frontend\models\SakipSasaranrenstra;
use frontend\models\SakipPeriode;
use frontend\models\SakipSkpd;
use frontend\models\SakipTujuanrenstra;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use dosamigos\ckeditor\CKEditor;

/** @var yii\web\View $this */
/** @var frontend\models\SakipIndikatorcascadingsubkegiatan $model */
/** @var yii\widgets\ActiveForm $form */

$this->registerJsFile('@web/lightapp/assets/js/plugins/choices.min.js', ['depends' => [\yii\web\JqueryAsset::class]]);
$this->registerCssFile('@web/css/choices.min.css');

// Tambahkan script untuk menginisialisasi choices.js
$this->registerJs("
    function initializeChoices() {
        const elements = document.querySelectorAll('[data-trigger]');
        elements.forEach(el => {
            if (!el.classList.contains('choices-initialized')) {
                new Choices(el, {
                    searchEnabled: true,
                    shouldSort: false,
                });
                el.classList.add('choices-initialized');
            }
        });
    }

    $(document).ready(function() {
        initializeChoices(); // Inisialisasi choices pertama kali

        $(document).on('shown.bs.modal', '#createModal', function() {
            initializeChoices();
        });

        // Pastikan event handler hanya terdaftar sekali
        $('form#dataindikatorcascadingsubkegiatan').off('submit').on('submit', function(e) {
            e.preventDefault(); // Mencegah form submit biasa

            var \$form = $(this);
            var \$submitBtn = \$form.find(':submit'); // Temukan tombol submit

               // Format anggaran_pk before submission
            var anggaranField = $('#sakipindikatorcascadingsubkegiatan-anggaran_pk');
            var anggaranValue = anggaranField.val();

             // Validate the input
    if (!anggaranValue || isNaN(anggaranValue.replace(/\./g, ''))) {
        alert('Anggaran harus diisi dengan angka.');
        anggaranField.focus(); // Focus the field
        return; // Stop form submission
    }
        
            // Remove dots for the submission
            var rawValue = anggaranValue.replace(/\./g, '');

            // Set the cleaned value back to the input before sending
            anggaranField.val(rawValue);

            // Nonaktifkan tombol submit untuk mencegah klik berulang
            \$submitBtn.prop('disabled', true);

            $('#loading-overlay').show();

            $.ajax({
                type: \$form.attr('method'),
                url: \$form.attr('action'),
                data: \$form.serialize(),
                success: function(response) {
                 if (response.success) {
        $('#updateModal').modal('hide');
        $('#refresh').load(location.href + ' #refresh > *');
                    } else {
                        console.log(response.errors);
                    }
                },
                error: function() {
                    console.log('Terjadi kesalahan saat mengirim data.');
                },
                complete: function() {
                    \$submitBtn.prop('disabled', false); // Aktifkan kembali tombol submit
                    $('#loading-overlay').hide();
                }
            });
        });
    });
");

// Add new JavaScript for formatting the anggaran input
$this->registerJs("
   // Add new JavaScript for formatting the anggaran input
    $(document).ready(function() {
        $('#sakipindikatorcascadingsubkegiatan-anggaran_pk').on('input', function() {
            let value = $(this).val();
            // Remove all non-digit characters
            value = value.replace(/[^0-9]/g, '');
            // Format the number with dots as thousand separators
            if (value) {
                value = Number(value).toLocaleString('id-ID');
            }
            // Display the formatted value
            $(this).val(value);
        });
    });
");

// Ambil refskpd_id dari model, default ke user yang sedang login jika null
$userRefSkpdId = $model->refskpd_id ?: Yii::$app->user->identity->refskpd_id;

// Query untuk mendapatkan nama_skpd berdasarkan refskpd_id
$namaSkpd = SakipSkpd::find()->select('nama_skpd')->where(['refskpd_id' => $userRefSkpdId])->scalar();

?>

<div class="loading-overlay" id="loading-overlay"></div>

<div class="sakip-indikatorcascadingsubkegiatan-form">

    <?php
    $form = ActiveForm::begin([
        'id' => 'dataindikatorcascadingsubkegiatan',
        'options' => ['enctype' => 'multipart/form-data'],
        // 'enableAjaxValidation' => true,
    ]);
    ?>

    <?= $form->field($model, 'refcascadingprogram_id')->hiddenInput()->label(false) ?>

    <?= $form->field($model, 'refcascadingkegiatan_id')->hiddenInput()->label(false) ?>

    <?= $form->field($model, 'refcascadingsubkegiatan_id')->hiddenInput()->label(false) ?>

    <?= $form->field($model, 'refsasaranrenstra_id')->hiddenInput()->label(false) ?>

    <?= $form->field($model, 'refindikatorsasaranrenstra_id')->hiddenInput()->label(false) ?>

    <?= $form->field($model, 'refskpd_id')->hiddenInput(['value' => $userRefSkpdId])->label(false) ?>

    <?= $form->field($model, 'refperiode_id')->hiddenInput(['id' => 'refperiode_id_hidden'])->label(false) ?>

    <?= $form->field($model, 'refprogram_id')->hiddenInput()->label(false) ?>

    <?= $form->field($model, 'refkegiatan_id')->hiddenInput()->label(false) ?>

    <?= $form->field($model, 'refsubkegiatan_id')->hiddenInput()->label(false) ?>

    <div class="form-group">
        <label for="refsubkegiatan_id">Sub Kegiatan</label>
        <input type="text" id="refsubkegiatan_id" class="form-control" value="<?= $model->refSubkegiatan->nama_subkegiatan ?>" readonly>
    </div>

    <?= $form->field($model, 'target_rkt')->hiddenInput(['maxlength' => true])->label(false) ?>

    <?= $form->field($model, 'anggaran_rkt')->hiddenInput(['maxlength' => true])->label(false) ?>

    <?= $form->field($model, 'target_rkt_p')->hiddenInput(['maxlength' => true])->label(false) ?>

    <?= $form->field($model, 'anggaran_rkt_p')->hiddenInput(['maxlength' => true])->label(false) ?>

    <?= $form->field($model, 'target_pk')->hiddenInput(['maxlength' => true])->label(false) ?>

    <?= $form->field($model, 'anggaran_pk')->textInput(['maxlength' => true, 'value' => $model->anggaran_pk ? number_format($model->anggaran_pk, 0, ',', '.') : '']) ?>

    <?= $form->field($model, 'target_pk_p')->hiddenInput(['maxlength' => true])->label(false) ?>

    <?= $form->field($model, 'anggaran_pk_p')->hiddenInput(['maxlength' => true])->label(false) ?>

    <?= $form->field($model, 'realisasi')->hiddenInput(['maxlength' => true])->label(false) ?>

    <?= $form->field($model, 'capaian')->hiddenInput(['maxlength' => true])->label(false) ?>

    <?= $form->field($model, 'keterangan')->hiddenInput(['rows' => 2])->label(false) ?>

    <?= $form->field($model, 'analisis')->hiddenInput(['rows' => 2])->label(false) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>