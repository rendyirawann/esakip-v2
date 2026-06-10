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

use backend\models\SakipEselon;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use dosamigos\ckeditor\CKEditor;
use backend\models\SakipSkpd;

/** @var yii\web\View $this */
/** @var backend\models\SakipPenjabatSkpd $model */
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
        $('form#penjabatskpd').off('submit').on('submit', function(e) {
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
    // Function to show loading overlay
    function showLoading() {
        $('#loading-overlay').show();
    }

    // Function to hide loading overlay
    function hideLoading() {
        $('#loading-overlay').hide();
    }

    // When period is selected, show additional fields and Add More button
    $('#ref-periode-dropdown').change(function() {
        var selectedPeriod = $(this).val();
        if (selectedPeriod) {
            showLoading();
            setTimeout(function() {
                $('#additional-fields').show();  // Show additional fields once period is selected
                $('#add-more-btn').show();  // Show the Add More button
                hideLoading();  // Hide loading overlay
            }, 500);
        } else {
            // If no period is selected, hide the add more button
            $('#add-more-btn').hide();
            $('#additional-fields').hide();  // Hide additional fields
        }
    });

    // Add new form fields dynamically
    function addAdditionalFields() {
        var newFieldHtml = $('#additional-fields .additional-field-set').first().clone(); // Clone the first set
        $('#additional-fields-container').append(newFieldHtml);
        updateRemoveButtonVisibility();  // Update remove button visibility
    }

    // Remove a form set if needed
    $(document).on('click', '.remove-field', function() {
        $(this).closest('.additional-field-set').remove();
        updateRemoveButtonVisibility();  // Update remove button visibility
    });

    // Function to toggle visibility of remove buttons based on the number of form sets
    function updateRemoveButtonVisibility() {
        var totalFields = $('#additional-fields-container .additional-field-set').length;
        $('#additional-fields-container .remove-field').each(function() {
            if (totalFields > 1) {
                $(this).show();  // Show remove button only if more than 1 form set
            } else {
                $(this).hide();  // Hide remove button if only 1 form set
            }
        });
    }

    // Initial call to hide remove button if only 1 form set
    updateRemoveButtonVisibility();

    // Initially hide the Add More button
    $('#add-more-btn').hide();
");


?>

<div class="loading-overlay" id="loading-overlay"></div>

<div class="sakip-penjabat-skpd-form">

    <?php
    $form = ActiveForm::begin([
        'id' => 'penjabatskpd',
        'options' => ['enctype' => 'multipart/form-data'],
    ]);
    ?>

    <?= $form->field($model, 'refskpd_id')->hiddenInput()->label(false) ?>

    <div class="form-group">
        <label for="nama_skpd">Nama SKPD</label>
        <input type="text" id="nama_skpd" class="form-control" value="<?= Html::decode($namaSkpd) ?>" readonly>
    </div>

    <?= $form->field($model, 'refperiode_id')->dropDownList(
        ArrayHelper::map($periodeList, 'refperiode_id', 'periode'),
        ['prompt' => 'Select Periode', 'id' => 'ref-periode-dropdown']
    ) ?>

    <div id="additional-fields" style="display:none;">

        <div id="additional-fields-container">
            <div class="additional-field-set">
                <?= $form->field($model, 'nama_penjabat[]')->textarea(['rows' => 6]) ?>
                <?= $form->field($model, 'nip_penjabat[]')->textInput(['maxlength' => true]) ?>
                <?= $form->field($model, 'jabatan_eselon[]')->textarea(['rows' => 6]) ?>
                <?= $form->field($model, 'pangkat_eselon[]')->textInput(['maxlength' => true]) ?>
                <?= $form->field($model, 'refeselon_id')->dropDownList(
                    ArrayHelper::map(SakipEselon::find()->orderBy(['refeselon_id' => SORT_ASC])->all(), 'refeselon_id', 'title_eselon'),
                    ['prompt' => 'Pilih Pangkat Eselon', 'data-trigger' => '']
                )->label('Pangkat Eselon') ?>
                <button type="button" class="btn btn-danger remove-field mb-2" style="display:none;">Remove</button>
            </div>
        </div>
    </div>

    <!-- The "Add More" button -->
    <button type="button" class="btn btn-primary" id="add-more-btn mt-3" onclick="addAdditionalFields()">Add More</button>

    <hr>
    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success mt-5']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>