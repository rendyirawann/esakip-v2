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

/** @var yii\web\View $this */
/** @var frontend\models\SakipCascadingprogram $model */
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
        $('form#cascadingprogram').off('submit').on('submit', function(e) {
            e.preventDefault(); // Mencegah form submit biasa

            var \$form = $(this);
            var \$submitBtn = \$form.find(':submit'); // Temukan tombol submit

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

$this->registerJs("
    function showLoading() {
        $('#loading-overlay').show();
    }

    function hideLoading() {
        $('#loading-overlay').hide();
    }

   $('#ref-periode-dropdown').change(function() {
        showLoading();

        var refperiode_id = $(this).val();
        $('#sakipcascadingprogram-refsasaranrenstra_id').empty().append('<option value=\"\">Select Sasaran Renstra</option>');
                // Hancurkan Selectize sebelum menghapus opsi lama
        if ($('#sakipcascadingprogram-refsasaranrenstra_id')[0].selectize) {
            $('#sakipcascadingprogram-refsasaranrenstra_id')[0].selectize.destroy();
        }
            
        if (refperiode_id) {
            $.ajax({
                url: '" . Url::to(['sakip-cascadingprogram/get-sasaran-renstra']) . "',
                type: 'GET',
                data: { refperiode_id: refperiode_id },
                success: function(data) {
                    $.each(data, function(id, name) {
                        $('#sakipcascadingprogram-refsasaranrenstra_id').append('<option value=\"' + id + '\">' + name + '</option>');
                    });
                                    // Reinitialize Selectize with search enabled
                $('#sakipcascadingprogram-refsasaranrenstra_id').selectize({
                    create: true,
                    sortField: 'text',
                    searchField: ['text'],
                    maxItems: 1
                });
                    $('#additional-fields').show();
                      // Re-initialize selectize for the refprogram dropdown
                    $('#refbidang-dropdown').selectize();
                },
                error: function() {
                    alert('Error retrieving data.');
                },
                complete: function() {
                    hideLoading();
                }
            });
        } else {
            $('#additional-fields').hide();
            hideLoading();
        }
    });

    $('#sakipcascadingprogram-refsasaranrenstra_id').change(function() {
    showLoading();
    
    var refsasaranrenstra_id = $(this).val();
    
    if (refsasaranrenstra_id) {
        $.ajax({
            url: '" . Url::to(['sakip-cascadingprogram/get-associated-values']) . "',
            type: 'GET',
            data: { refsasaranrenstra_id: refsasaranrenstra_id },
            success: function(data) {
                // Assuming data is returned in the format { refsasaran_id: ..., reftujuan_id: ..., refmisi_id: ... }
                $('#sakipcascadingprogram-refsasaran_id').val(data.refsasaran_id);
                $('#sakipcascadingprogram-reftujuan_id').val(data.reftujuan_id);
                $('#sakipcascadingprogram-refmisi_id').val(data.refmisi_id);
                
                // Update display fields
                $('#refsasaran_id_display').val(data.refsasaran_uraian);
                $('#reftujuan_id_display').val(data.reftujuan_uraian);
                $('#refmisi_id_display').val(data.refmisi_uraian);
            },
            error: function() {
                alert('Error retrieving associated values.');
            },
            complete: function() {
                hideLoading();
            }
        });
    } else {
        // Clear fields if no refsasaranrenstra_id is selected
        $('#sakipcascadingprogram-refsasaran_id').val('');
        $('#sakipcascadingprogram-reftujuan_id').val('');
        $('#sakipcascadingprogram-refmisi_id').val('');
        $('#refsasaran_id_display').val('');
        $('#reftujuan_id_display').val('');
        $('#refmisi_id_display').val('');
    }
});

  $('#sakipcascadingprogram-refsasaranrenstra_id').change(function() {
        showLoading();

        var refsasaranrenstra_id = $(this).val();
         // Menghancurkan Selectize sebelum menghapus opsi dan menambahkan opsi baru
    if ($('#sakipcascadingprogram-refindikatorsasaranrenstra_id')[0].selectize) {
        $('#sakipcascadingprogram-refindikatorsasaranrenstra_id')[0].selectize.destroy();
    }
        $('#sakipcascadingprogram-refindikatorsasaranrenstra_id').empty().append('<option value=\"\">Select Indikator Sasaran Renstra</option>');
        
        if (refsasaranrenstra_id) {
            $.ajax({
                url: '" . Url::to(['sakip-cascadingprogram/get-indikator-sasaran-renstra']) . "',
                type: 'GET',
                data: { refsasaranrenstra_id: refsasaranrenstra_id },
                success: function(data) {
                    $.each(data, function(id, name) {
                        $('#sakipcascadingprogram-refindikatorsasaranrenstra_id').append('<option value=\"' + id + '\">' + name + '</option>');
                    });
                    // Reinitialize Selectize with search enabled
                $('#sakipcascadingprogram-refindikatorsasaranrenstra_id').selectize({
                    create: true,
                    sortField: 'text',
                    searchField: ['text'],
                    maxItems: 1
                });
                },
                error: function() {
                    alert('Error retrieving data.');
                },
                complete: function() {
                    hideLoading();
                }
            });
        } else {
            hideLoading();
        }
    });


// AJAX for refprogram_id based on selected refbidang_id
 $('#refbidang-dropdown').change(function() {  // Changed here to the updated ID
    var bidangId = $(this).val();
    var programDropdown = $('#" . Html::getInputId($model, 'refprogram_id') . "');

    // Reset value of refprogram_id
    programDropdown.val(''); // Reset the selected value
    programDropdown.trigger('change'); // Trigger change event for selectize

    // Destroy the existing Selectize instance before reinitializing
    if (programDropdown[0].selectize) {
        programDropdown[0].selectize.destroy();
    }

    if (bidangId) {
        showLoading(); // Tampilkan loading overlay
        $.ajax({
            url: '" . Url::to(['sakip-cascadingprogram/get-programs']) . "',
            type: 'GET',
            data: { bidang_id: bidangId },
            success: function(data) {
                programDropdown.empty(); // Clear previous options
                programDropdown.append('<option value=\"\">Pilih Program</option>'); // Add prompt option
                $.each(data, function(key, value) {
                    programDropdown.append('<option value=\"' + key + '\">' + value + '</option>');
                });
                
                // Reinitialize selectize.js after updating options
                programDropdown.selectize({
                    create: true,
                    sortField: 'text'
                });
            },
            complete: function() {
                hideLoading(); // Sembunyikan loading overlay
            }
        });
    } else {
        // Clear the options and reset
        programDropdown.empty().append('<option value=\"\">Pilih Program</option>');
        programDropdown.trigger('change'); // Trigger change event to reset selectize
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

<div class="sakip-cascadingprogram-form">

    <?php
    $form = ActiveForm::begin([
        'id' => 'cascadingprogram',
        'options' => ['enctype' => 'multipart/form-data'],
    ]);
    ?>

    <?= $form->field($model, 'refperiode_id')->dropDownList(
        ArrayHelper::map($periodeList, 'refperiode_id', 'periode'),
        ['prompt' => 'Select Periode', 'id' => 'ref-periode-dropdown']
    )->label('Pilih Periode') ?>


    <div id="additional-fields" style="display:none;">


        <?= $form->field($model, 'refsasaranrenstra_id')->dropDownList([], ['prompt' => 'Pilih Sasaran Renstra', 'id' => 'sakipcascadingprogram-refsasaranrenstra_id'])->label('Sasaran Renstra') ?>

        <?= $form->field($model, 'refindikatorsasaranrenstra_id')->dropDownList([], [
            'prompt' => 'Select Indikator Sasaran Renstra',
            'id' => 'sakipcascadingprogram-refindikatorsasaranrenstra_id'
        ])->label('Indikator Sasaran Renstra') ?>


        <!-- Dropdown for refbidang_id -->
        <?= $form->field($model, 'refbidang_id')->dropDownList(
            ArrayHelper::map($bidangList, 'refbidang_id', function ($model) {
                return $model['kode_bidang'] . ' - ' . $model['nama_bidang'];
            }),
            ['prompt' => 'Pilih Bidang', 'id' => 'refbidang-dropdown']
        )->label('Bidang') ?>


        <!-- Dropdown for refprogram_id -->
        <?= $form->field($model, 'refprogram_id')->dropDownList([], ['prompt' => 'Pilih Program'])->label('Program') ?>

        <!-- // Hidden input for refsasaran_id -->
        <?= $form->field($model, 'refsasaran_id')->hiddenInput()->label(false) ?>

        <!-- // Hidden input for reftujuan_id -->
        <?= $form->field($model, 'reftujuan_id')->hiddenInput()->label(false) ?>

        <!-- // Hidden input for refmisi_id -->
        <?= $form->field($model, 'refmisi_id')->hiddenInput()->label(false) ?>

        <?= $form->field($model, 'refskpd_id')->hiddenInput(['value' => $userRefSkpdId])->label(false) ?>

        <?= $form->field($model, 'uraian_sasaranprogram')->textarea(['rows' => 2])->label('Uraian Sasaran Program') ?>

        <?= $form->field($model, 'uraian_indikatorprogram')->textarea(['rows' => 2])->label('Uraian Indikator Program') ?>

        <div class="row">
            <div class="col-lg-6">
                <?= $form->field($model, 'program_target')->textInput(['maxlength' => true])->label('Target') ?>
            </div>
            <div class="col-lg-6">
                <?= $form->field($model, 'program_satuan')->textInput(['maxlength' => true])->label('Satuan') ?>
            </div>
        </div>

    </div>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>