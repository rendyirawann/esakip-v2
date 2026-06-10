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
use frontend\models\SakipPeriode;
use frontend\models\SakipKegiatan;
use frontend\models\SakipCascadingprogram;
use frontend\models\SakipCascadingkegiatan;

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
        $('form#cascadingkegiatan').off('submit').on('submit', function(e) {
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
                url: '" . \yii\helpers\Url::to(['get-program-by-periode']) . "',
                type: 'POST',
                data: { refperiode_id: refPeriodeId },
                success: function(response) {
                    if(response.success) {
                        $('#refprogram-dropdown').html(response.dropdown);
                        $('#additional-fields').show();
                    } else {
                        alert('Error: ' + response.message);
                    }
                    hideLoading();
                },
                error: function() {
                    alert('Failed to fetch program data.');
                    hideLoading();
                }
            });
        } else {
            $('#additional-fields').hide();
        }
    });

    $('#refprogram-dropdown').change(function() {
        var refProgramId = $(this).val();
        if(refProgramId) {
            showLoading();
            $.ajax({
                url: '" . \yii\helpers\Url::to(['get-cascadingprogram-by-program']) . "',
                type: 'POST',
                data: { refprogram_id: refProgramId },
                success: function(response) {
                    if(response.success) {
                        $('#sakipcascadingkegiatan-refcascadingprogram_id').val(response.refcascadingprogram_id);
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

            // AJAX untuk mendapatkan daftar kegiatan berdasarkan refprogram_id
            $.ajax({
                url: '" . \yii\helpers\Url::to(['get-kegiatan-by-program']) . "',
                type: 'POST',
                data: { refprogram_id: refProgramId },
                success: function(response) {
                    if(response.success) {
                        $('#refkegiatan-dropdown').html(response.dropdown);
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



// Ambil refskpd_id dari model, default ke user yang sedang login jika null
$userRefSkpdId = $model->refskpd_id ?: Yii::$app->user->identity->refskpd_id;

// Query untuk mendapatkan nama_skpd berdasarkan refskpd_id
$namaSkpd = SakipSkpd::find()->select('nama_skpd')->where(['refskpd_id' => $userRefSkpdId])->scalar();


?>

<div class="loading-overlay" id="loading-overlay"></div>

<div class="sakip-cascadingkegiatan-form">

    <?php
    $form = ActiveForm::begin([
        'id' => 'cascadingkegiatan',
        'options' => ['enctype' => 'multipart/form-data'],
    ]);
    ?>

    <?= $form->field($model, 'refperiode_id')->hiddenInput(['disabled' => true, 'value' => $model->refPeriode->periode])->label(false) ?>

    <?= $form->field($model, 'refprogram_id')->textInput(['disabled' => true, 'value' => $model->refProgram->nama_program])->label('Program') ?>

    <?= $form->field($model, 'refcascadingprogram_id')->textInput([
        'disabled' => true,
        'value' => ($model->refCascadingProgram ?
            '[' . ($model->refCascadingProgram->sasaranRenstra ? $model->refCascadingProgram->sasaranRenstra->uraian_sasaranrenstra : 'Tidak Ada Sasaran') . '] '
            : '') .
            ($model->refCascadingProgram ? $model->refCascadingProgram->uraian_indikatorprogram : '')
    ])->label('Indikator Cascading Program') ?>


    <?= $form->field($model, 'refsasaranrenstra_id')->hiddenInput(['disabled' => true, 'value' => $model->refSasaranRenstra->uraian_sasaranrenstra])->label(false) ?>

    <?= $form->field($model, 'refindikatorsasaranrenstra_id')->hiddenInput(['disabled' => true, 'value' => $model->refIndikatorsasaranRenstra->uraian_indikatorsasaranrenstra])->label(false) ?>

    <?= $form->field($model, 'refkegiatan_id')->textInput(['disabled' => true, 'value' => $model->refKegiatan->nama_kegiatan])->label('Kegiatan') ?>

    <?= $form->field($model, 'uraian_sasarankegiatan')->textarea(['rows' => 2])->label('Uraian Sasaran Kegiatan') ?>

    <?= $form->field($model, 'uraian_indikatorkegiatan')->textarea(['rows' => 2])->label('Indikator Sasaran Kegiatan') ?>

    <?= $form->field($model, 'refskpd_id')->hiddenInput(['value' => $userRefSkpdId])->label(false) ?>

    <div class="form-group">
        <label for="nama_skpd">Nama SKPD</label>
        <input type="text" id="nama_skpd" class="form-control" value="<?= Html::decode($namaSkpd) ?>" readonly>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <?= $form->field($model, 'kegiatan_target')->textInput(['maxlength' => true])->label('Target') ?>
        </div>
        <div class="col-lg-6">
            <?= $form->field($model, 'kegiatan_satuan')->textInput(['maxlength' => true])->label('Satuan') ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>



</div>