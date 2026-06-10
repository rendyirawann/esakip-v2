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
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use dosamigos\ckeditor\CKEditor;
use frontend\models\SakipSkpd;
use frontend\models\SakipCascadingprogram;
use frontend\models\SakipCascadingkegiatan;
use frontend\models\SakipCascadingsubkegiatan;

/** @var yii\web\View $this */
/** @var frontend\models\SakipCascadingkegiatan $model */
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
        $('form#cascadingsubkegiatan').off('submit').on('submit', function(e) {
            e.preventDefault(); // Mencegah form submit biasa

            var \$form = $(this);
            var \$submitBtn = \$form.find(':submit'); // Temukan tombol submit

               // Format subkegiatan_anggaran before submission
            var anggaranField = $('#sakipcascadingsubkegiatan-subkegiatan_anggaran');
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
                        window.location.href = response.redirect; // Redirect ke halaman view
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
        $('#sakipcascadingsubkegiatan-subkegiatan_anggaran').on('input', function() {
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

$this->registerJs("
    function showLoading() {
        $('#loading-overlay').show();
    }

    function hideLoading() {
        $('#loading-overlay').hide();
    }

$('#ref-periode-dropdown').change(function() {
    var refPeriodeId = $(this).val();
    if(refPeriodeId) {
        showLoading();
        $.ajax({
            url: '" . \yii\helpers\Url::to(['get-kegiatan-by-periode']) . "',
            type: 'POST',
            data: { refperiode_id: refPeriodeId },
            success: function(response) {
                if(response.success) {
                    $('#refkegiatan-dropdown').html(response.dropdown);
                     // Re-initialize Selectize for refkegiatan-dropdown
                    $('#refkegiatan-dropdown').selectize({
                        create: false,
                        sortField: 'text',
                        placeholder: 'Pilih Kegiatan',
                    });
                    $('#additional-fields').show();
                } else {
                    alert('Error: ' + response.message);
                }
                hideLoading();
            },
            error: function() {
                alert('Failed to fetch kegiatan data.');
                hideLoading();
            }
        });
    } else {
        $('#additional-fields').hide();
    }
});

$('#refkegiatan-indicator-dropdown').change(function() {
    var refCascadingKegiatanId = $(this).val();
    if (refCascadingKegiatanId) {
        showLoading();
        $.ajax({
            url: '" . \yii\helpers\Url::to(['get-cascadingprogram-by-kegiatan']) . "',
            type: 'POST',
            data: { refcascadingkegiatan_id: refCascadingKegiatanId },
            success: function(response) {
                if (response.success) {
                    // Fill refcascadingprogram_id
                    $('#refcascadingprogram_id').val(response.refcascadingprogram_id);

                    // Fill refsasaranrenstra_id and its display field
                    $('#sakip-cascadingkegiatan-refsasaranrenstra_id').val(response.refsasaranrenstra_id);
                    $('#refsasaranrenstra_id_display').val(response.uraian_sasarankegiatan);

                    // Fill refindikatorsasaranrenstra_id and its display field
                    $('#sakip-cascadingkegiatan-refindikatorsasaranrenstra_id').val(response.refindikatorsasaranrenstra_id);
                    $('#refindikatorsasaranrenstra_id_display').val(response.uraian_indikatorkegiatan);
                } else {
                    alert('Error: ' + response.message);
                }
                hideLoading();
            },
            error: function() {
                alert('Failed to fetch cascading program data.');
                hideLoading();
            }
        });
    }
});



$('#refkegiatan-dropdown').change(function() {
    var refKegiatanId = $(this).val();
     // Reset refcascadingprogram_id and refsasaranrenstra_id when refkegiatan_id changes
    $('#refcascadingprogram_id').val(''); // Reset the cascading program field
    $('#sakip-cascadingkegiatan-refsasaranrenstra_id').val(''); // Reset the hidden refsasaranrenstra_id field
    $('#refsasaranrenstra_id_display').val(''); // Reset the display of uraian_sasaranrenstra
        $('#refsubkegiatan-dropdown').val('').trigger('change'); // Reset the value and trigger change event
    // Destroy and re-initialize selectize on refsubkegiatan-dropdown
    if ($('#refsubkegiatan-dropdown')[0].selectize) {
        $('#refsubkegiatan-dropdown')[0].selectize.destroy(); // Destroy existing selectize
    }

         // Menghancurkan Selectize untuk refkegiatan-dropdown
    if ($('#refkegiatan-indicator-dropdown')[0].selectize) {
        $('#refkegiatan-indicator-dropdown')[0].selectize.destroy();
    }
    
    
    if(refKegiatanId) {
        showLoading();
        
        // AJAX untuk mendapatkan data kegiatan dan program
        $.ajax({
            url: '" . \yii\helpers\Url::to(['get-cascadingkegiatan-by-kegiatan']) . "',
            type: 'POST',
            data: { refkegiatan_id: refKegiatanId },
            success: function(response) {
                if(response.success) {
                    $('#refkegiatan-indicator-dropdown').html(response.indicatorDropdown);
                    // Re-initialize selectize for the refprogram dropdown
                    $('#refkegiatan-indicator-dropdown').selectize();
                    
                    // Mengisi otomatis refprogram_id berdasarkan refkegiatan_id
                    $('#refprogram_id').val(response.refprogram_id);

                     // Initialize Selectize for refkegiatan_id
                    $('#refkegiatan-dropdown').selectize({
                        create: false,
                        sortField: 'text',
                        placeholder: 'Pilih Kegiatan'
                    });
                } else {
                    alert('Error: ' + response.message);
                }
                hideLoading();
            },
            error: function() {
                alert('Failed to fetch cascading kegiatan data.');
                hideLoading();
            }
        });
        
        // AJAX untuk mendapatkan daftar subkegiatan berdasarkan refkegiatan_id
        $.ajax({
            url: '" . \yii\helpers\Url::to(['get-subkegiatan-by-kegiatan']) . "',
            type: 'POST',
            data: { refkegiatan_id: refKegiatanId },
            success: function(response) {
                if(response.success) {
                    $('#refsubkegiatan-dropdown').html(response.dropdown);
                      // Re-initialize Selectize after loading the new options
                    $('#refsubkegiatan-dropdown').selectize({
                        create: false,
                        sortField: 'text',
                        placeholder: 'Pilih Sub Kegiatan',
                    });
                } else {
                    alert('Error: ' + response.message);
                }
                hideLoading();
            },
            error: function() {
                alert('Failed to fetch kegiatan data.');
                hideLoading();
            }
        });
    }
});


");



// Ambil refskpd_id dari user yang sedang login
$userRefSkpdId = Yii::$app->user->identity->refskpd_id;

// Query untuk mendapatkan nama_skpd berdasarkan refskpd_id
$namaSkpd = SakipSkpd::find()->select('nama_skpd')->where(['refskpd_id' => $userRefSkpdId])->scalar();


?>
<!-- Selectize CSS -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/css/selectize.min.css" rel="stylesheet">

<!-- Selectize JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/js/standalone/selectize.min.js"></script>


<div class="loading-overlay" id="loading-overlay"></div>

<div class="sakip-cascadingsubkegiatan-form">

    <?php
    $form = ActiveForm::begin([
        'id' => 'cascadingsubkegiatan',
        'options' => ['enctype' => 'multipart/form-data'],
    ]);
    ?>

    <?= $form->field($model, 'refperiode_id')->dropDownList(
        ArrayHelper::map($periodeList, 'refperiode_id', 'periode'),
        ['prompt' => 'Pilih Periode', 'id' => 'ref-periode-dropdown']
    )->label('Pilih Periode') ?>

    <div id="additional-fields" style="display:none;">

        <?= $form->field($model, 'refkegiatan_id')->dropDownList([], ['prompt' => 'Pilih Kegiatan', 'id' => 'refkegiatan-dropdown'])->label('Kegiatan') ?>

        <?= $form->field($model, 'refcascadingkegiatan_id')->dropDownList([], ['prompt' => 'Pilih Indikator Kegiatan', 'id' => 'refkegiatan-indicator-dropdown'])->label('Indikator Kegiatan') ?>

        <?= $form->field($model, 'refcascadingprogram_id')->hiddenInput([
            'maxlength' => true,
            'id' => 'refcascadingprogram_id', // Pastikan ID sesuai agar bisa diakses lewat JavaScript
            'readonly' => true, // Opsional: agar field ini tidak bisa diedit secara manual
        ])->label(false) ?>

        <!-- Hidden input for refsasaranrenstra_id -->
        <?= $form->field($model, 'refsasaranrenstra_id')->hiddenInput(['id' => 'sakip-cascadingkegiatan-refsasaranrenstra_id'])->label(false) ?>

        <!-- Hidden input for refindikatorsasaranrenstra_id -->
        <?= $form->field($model, 'refindikatorsasaranrenstra_id')->hiddenInput(['id' => 'sakip-cascadingkegiatan-refindikatorsasaranrenstra_id'])->label(false) ?>

        <?= $form->field($model, 'refprogram_id')->hiddenInput(['id' => 'refprogram_id', 'readonly' => true])->label(false) ?>

        <?= $form->field($model, 'refsubkegiatan_id')->dropDownList([], [
            'prompt' => 'Pilih Sub Kegiatan',
            'id' => 'refsubkegiatan-dropdown',
            'data-placeholder' => 'Pilih Sub Kegiatan', // Optional, adds placeholder
        ])->label('Sub Kegiatan') ?>

        <?= $form->field($model, 'uraian_sasaransubkegiatan')->textarea(['rows' => 2])->label('Uraian Sub Kegiatan') ?>

        <?= $form->field($model, 'uraian_indikatorsubkegiatan')->textarea(['rows' => 2])->label('Uraian Indikator Sub Kegiatan') ?>

        <?= $form->field($model, 'refskpd_id')->hiddenInput(['value' => $userRefSkpdId])->label(false) ?>

        <div class="form-group">
            <label for="nama_skpd">Nama SKPD</label>
            <input type="text" id="nama_skpd" class="form-control" value="<?= Html::decode($namaSkpd) ?>" readonly>
        </div>

        <div class="row">
            <div class="col-lg-4">
                <?= $form->field($model, 'subkegiatan_target')->textInput(['maxlength' => true])->label('Target') ?>
            </div>
            <div class="col-lg-4">
                <?= $form->field($model, 'subkegiatan_satuan')->textInput(['maxlength' => true])->label('Satuan') ?>
            </div>
            <div class="col-lg-4">
                <?= $form->field($model, 'subkegiatan_anggaran')->textInput(['maxlength' => true])->label('Anggaran') ?>
            </div>
        </div>

    </div>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>