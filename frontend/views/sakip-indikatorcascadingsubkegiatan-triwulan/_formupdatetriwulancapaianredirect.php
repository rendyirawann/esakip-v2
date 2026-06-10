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
/** @var frontend\models\SakipIndikatorcascadingsubkegiatanTriwulan $model */
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
        $('form#dataindikatorcascadingsubkegiatantriwulan').off('submit').on('submit', function(e) {
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

// Register JavaScript for automatic calculation
$this->registerJs("
    $(document).on('input', '#triwulan_realisasi', function() {
        var target = parseFloat($('#triwulan_target_rkt').val());
        var realisasi = parseFloat($(this).val());

        if (target && realisasi) {
            var capaian = (realisasi / target) * 100;
            $('#triwulan_capaian').val(capaian.toFixed(2)); // Display as a percentage with 2 decimal places
        } else {
            $('#triwulan_capaian').val(''); // Clear if no valid values
        }
    });
");

// Ambil refskpd_id dari user yang sedang login
$userRefSkpdId = Yii::$app->user->identity->refskpd_id;

// Query untuk mendapatkan nama_skpd berdasarkan refskpd_id
$namaSkpd = SakipSkpd::find()->select('nama_skpd')->where(['refskpd_id' => $userRefSkpdId])->scalar();

?>

<div class="loading-overlay" id="loading-overlay"></div>

<div class="sakip-indikatorcascadingsubkegiatan-triwulan-form">

    <?php
    $form = ActiveForm::begin([
        'id' => 'dataindikatorcascadingsubkegiatantriwulan',
        'options' => ['enctype' => 'multipart/form-data'],
        // 'enableAjaxValidation' => true,
    ]);
    ?>

    <?= $form->field($model, 'refindikatorsubkegiatan_id')->hiddenInput()->label(false) ?>

    <?= $form->field($model, 'refcascadingprogram_id')->hiddenInput()->label(false) ?>

    <?= $form->field($model, 'refcascadingkegiatan_id')->hiddenInput()->label(false) ?>

    <?= $form->field($model, 'refcascadingsubkegiatan_id')->hiddenInput()->label(false) ?>

    <?= $form->field($model, 'refskpd_id')->hiddenInput(['value' => $userRefSkpdId])->label(false) ?>

    <?= $form->field($model, 'refsasaranrenstra_id')->hiddenInput(['maxlength' => true])->label(false) ?>

    <?= $form->field($model, 'refindikatorsasaranrenstra_id')->hiddenInput(['maxlength' => true])->label(false) ?>

    <?= $form->field($model, 'refperiode_id')->hiddenInput(['id' => 'refperiode_id_hidden'])->label(false) ?>

    <?= $form->field($model, 'reftriwulan_id')->hiddenInput(['value' => $reftriwulan_id])->label(false) ?>

    <?= $form->field($model, 'reftriwulan_id')->textInput(['value' => $reftriwulan_id, 'disabled' => true])->label('Triwulan Ke-') ?>

    <?= $form->field($model, 'refprogram_id')->hiddenInput()->label(false) ?>

    <?= $form->field($model, 'refkegiatan_id')->hiddenInput()->label(false) ?>

    <?= $form->field($model, 'refsubkegiatan_id')->hiddenInput()->label(false) ?>

    <div class="form-group">
        <label for="refsubkegiatan_id">Sub Kegiatan</label>
        <input type="text" id="refsubkegiatan_id" class="form-control" value="<?= $model->refSubkegiatan->nama_subkegiatan ?>" readonly>
    </div>

    <div class="row">
        <div class="col-lg-4">
            <!-- Triwulan Target RKT: Display existing value and set as read-only -->
            <?= $form->field($model, 'triwulan_target_rkt')->textInput(['id' => 'triwulan_target_rkt', 'disabled' => true, 'maxlength' => true])->label('Target RKT') ?>
        </div>
        <div class="col-lg-4">
            <!-- Triwulan Realisasi Input -->
            <?= $form->field($model, 'triwulan_realisasi')->textInput(['id' => 'triwulan_realisasi', 'maxlength' => true])->label('Realisasi') ?>
        </div>
        <div class="col-lg-4">
            <!-- Triwulan Capaian: Auto-calculated and displayed as percentage -->
            <?= $form->field($model, 'triwulan_capaian')->textInput(['id' => 'triwulan_capaian', 'maxlength' => true, 'readonly' => true])->label('Capaian (%)') ?>
        </div>
    </div>

    <?= $form->field($model, 'triwulan_target_rkt_p')->hiddenInput(['maxlength' => true])->label(false) ?>

    <?= $form->field($model, 'triwulan_target_pk')->hiddenInput(['maxlength' => true])->label(false) ?>

    <?= $form->field($model, 'triwulan_target_pk_p')->hiddenInput(['maxlength' => true])->label(false) ?>

    <?= $form->field($model, 'triwulan_keterangan')->textarea(['rows' => 2])->label('Keterangan') ?>

    <?= $form->field($model, 'triwulan_analisis')->hiddenInput(['rows' => 2])->label(false) ?>

    <?= $form->field($model, 'triwulan_penyerapan_anggaran')->hiddenInput(['maxlength' => true])->label(false) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>