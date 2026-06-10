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
                    $('#refcascadingprogram_id').val(response.refcascadingprogram_id); // Mengisi otomatis refcascadingprogram_id
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
                    
                    // Mengisi otomatis refprogram_id berdasarkan refkegiatan_id
                    $('#refprogram_id').val(response.refprogram_id);
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

<div class="loading-overlay" id="loading-overlay"></div>

<div class="sakip-cascadingsubkegiatan-form">

    <?php
    $form = ActiveForm::begin([
        'id' => 'cascadingsubkegiatan',
        'options' => ['enctype' => 'multipart/form-data'],
    ]);
    ?>
    <?= $form->field($model, 'refcascadingprogram_id')->textInput(['disabled' => true, 'value' => $model->refCascadingProgram->uraian_indikatorprogram])->label('Cascadng Program ID') ?>

    <?= $form->field($model, 'refcascadingkegiatan_id')->textInput(['disabled' => true, 'value' => $model->refCascadingKegiatan->uraian_indikatorkegiatan])->label('Cascadng Kegiatan ID') ?>

    <?= $form->field($model, 'refindikatorsasaranrenstra_id')->textInput(['disabled' => true, 'value' => $model->refIndikatorSasaranRenstra->refindikatorsasaranrenstra_id])->label('Indikator Sasaran Renstra ID') ?>

    <?= $form->field($model, 'refsasaranrenstra_id')->textInput(['disabled' => true, 'value' => $model->refSasaranRenstra->refsasaranrenstra_id])->label('Sasaran Renstra ID') ?>

    <?= $form->field($model, 'refprogram_id')->textInput(['disabled' => true, 'value' => $model->refProgram->nama_program])->label('Program') ?>

    <?= $form->field($model, 'refkegiatan_id')->textInput(['disabled' => true, 'value' => $model->refKegiatan->nama_kegiatan])->label('Kegiatan') ?>

    <?= $form->field($model, 'refsubkegiatan_id')->textInput(['disabled' => true, 'value' => $model->refSubkegiatan->nama_subkegiatan])->label('Sub Kegiatan') ?>

    <!-- Disabled fields to show previous values but prevent editing -->
    <?= $form->field($model, 'refperiode_id')->textInput(['disabled' => true, 'value' => $model->refPeriode->periode])->label('Periode') ?>

    <?= $form->field($model, 'refskpd_id')->hiddenInput(['value' => $userRefSkpdId])->label(false) ?>

    <div class="form-group">
        <label for="nama_skpd">Nama SKPD</label>
        <input type="text" id="nama_skpd" class="form-control" value="<?= Html::decode($namaSkpd) ?>" readonly>
    </div>

    <?= $form->field($model, 'uraian_sasaransubkegiatan')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'uraian_indikatorsubkegiatan')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'subkegiatan_target')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'subkegiatan_satuan')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'subkegiatan_anggaran')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Update', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>



</div>