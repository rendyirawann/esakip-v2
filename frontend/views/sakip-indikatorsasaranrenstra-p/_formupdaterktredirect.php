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
/** @var backend\models\SakipVisi $model */
/** @var yii\widgets\ActiveForm $form */

$this->registerJsFile('@web/lightapp/assets/js/plugins/choices.min.js', ['depends' => [\yii\web\JqueryAsset::class]]);
$this->registerCssFile('@web/css/choices.min.css');

// Tambahkan script JavaScript untuk menangani perubahan pada refsasaranrenstra_id
$this->registerJs("
    $('#dataindikatorsasaranrenstra').on('change', '#refsasaranrenstra_id', function() {
        var selectedId = $(this).val(); // Ambil nilai yang dipilih

        // Lakukan AJAX untuk mendapatkan refperiode_id dan periode
        $.ajax({
            url: '" . \yii\helpers\Url::to(['get-refperiode']) . "', // Ganti dengan URL controller yang sesuai
            type: 'GET',
            data: { id: selectedId },
            success: function(data) {
                if (data.success) {
                    $('#refperiode_id').val(data.periode); // Update input text dengan periode
                    $('#refperiode_id_hidden').val(data.refperiode_id); // Update hidden input dengan refperiode_id
                } else {
                    $('#refperiode_id').val('Periode tidak ditemukan'); // Jika tidak ditemukan
                    $('#refperiode_id_hidden').val(''); // Kosongkan hidden input
                }
            },
            error: function() {
                $('#refperiode_id').val('Terjadi kesalahan'); // Jika terjadi kesalahan
                $('#refperiode_id_hidden').val(''); // Kosongkan hidden input
            }
        });
    });
");

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
        $('form#dataindikatorsasaranrenstra').off('submit').on('submit', function(e) {
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

// Ambil refskpd_id dari user yang sedang login
$userRefSkpdId = Yii::$app->user->identity->refskpd_id;

// Query untuk mendapatkan nama_skpd berdasarkan refskpd_id
$namaSkpd = SakipSkpd::find()->select('nama_skpd')->where(['refskpd_id' => $userRefSkpdId])->scalar();

?>

<div class="loading-overlay" id="loading-overlay"></div>

<div class="sakip-indikatorsasaranrenstra-form">

    <?php
    $form = ActiveForm::begin([
        'id' => 'dataindikatorsasaranrenstra',
        'options' => ['enctype' => 'multipart/form-data'],
        // 'enableAjaxValidation' => true,
    ]);
    ?>

    <?= $form->field($model, 'uraian_indikatorsasaranrenstra')->hiddenInput(['rows' => 6])->label(false) ?>

    <div class="form-group">
        <label for="uraian_indikatorsasaranrenstra">Indikator Sasaran Renstra</label>
        <input type="text" id="uraian_indikatorsasaranrenstra" class="form-control" value="<?= $model->uraian_indikatorsasaranrenstra ?>" readonly>
    </div>

    <?= $form->field($model, 'refskpd_id')->hiddenInput(['value' => $userRefSkpdId])->label(false) ?>

    <?= $form->field($model, 'refsasaranrenstra_id')->hiddenInput(['maxlength' => true])->label(false) ?>

    <div class="form-group">
        <label for="refsasaranrenstra_id">Uraian Sasaran Renstra</label>
        <input type="text" id="refsasaranrenstra_id" class="form-control" value="<?= $model->refSasaranrenstra->uraian_sasaranrenstra ?>" readonly>
    </div>

    <?= $form->field($model, 'refperiode_id')->hiddenInput(['id' => 'refperiode_id_hidden'])->label(false) ?>

    <?= $form->field($model, 'indikatorsasaranrenstra_satuan')->hiddenInput(['maxlength' => true])->label(false) ?>

    <?= $form->field($model, 'target_rkt')->textInput(['maxlength' => true])->label('Target RKT') ?>

    <?= $form->field($model, 'indikatorsasaranrenstra_target')->hiddenInput(['maxlength' => true])->label(false) ?>

    <?= $form->field($model, 'target_rkt_p')->hiddenInput(['maxlength' => true])->label(false) ?>

    <?= $form->field($model, 'target_pk')->hiddenInput(['maxlength' => true])->label(false) ?>

    <?= $form->field($model, 'target_pk_p')->hiddenInput(['maxlength' => true])->label(false) ?>

    <?= $form->field($model, 'realisasi')->hiddenInput(['maxlength' => true])->label(false) ?>

    <?= $form->field($model, 'capaian')->hiddenInput(['maxlength' => true])->label(false) ?>

    <?= $form->field($model, 'analisis')->hiddenInput(['maxlength' => true])->label(false) ?>

    <?= $form->field($model, 'keterangan')->textInput(['rows' => 2]) ?>

    <?= $form->field($model, 'indikatorsasaranrenstra_isaktif')->hiddenInput(['maxlength' => true])->label(false) ?>

    <?= $form->field($model, 'iku_isaktif')->hiddenInput(['maxlength' => true])->label(false) ?>

    <?= $form->field($model, 'pk_isaktif')->hiddenInput(['maxlength' => true])->label(false) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>