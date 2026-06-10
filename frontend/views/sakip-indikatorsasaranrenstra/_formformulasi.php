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

$this->registerJs("
    $('#datasasaranrenstra').on('change', '#refsasaran_id', function() {
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

        // Lakukan AJAX untuk mendapatkan reftujuan_id
        $.ajax({
            url: '" . \yii\helpers\Url::to(['get-reftujuan']) . "', // Ganti dengan URL controller yang sesuai
            type: 'GET',
            data: { id: selectedId },
            success: function(data) {
                if (data.success) {
                    $('#reftujuan_id').val(data.uraian_tujuan); // Update input text dengan uraian_tujuan
                    $('#reftujuan_id_hidden').val(data.reftujuan_id); // Update hidden input dengan reftujuan_id
                } else {
                    $('#reftujuan_id').val('Tujuan tidak ditemukan'); // Jika tidak ditemukan
                    $('#reftujuan_id_hidden').val(''); // Kosongkan hidden input
                }
            },
            error: function() {
                $('#reftujuan_id').val('Terjadi kesalahan'); // Jika terjadi kesalahan
                $('#reftujuan_id_hidden').val(''); // Kosongkan hidden input
            }
        });

        // Lakukan AJAX untuk mendapatkan refvisi_id
        $.ajax({
            url: '" . \yii\helpers\Url::to(['get-refvisi']) . "', // Ganti dengan URL controller yang sesuai
            type: 'GET',
            data: { id: selectedId },
            success: function(data) {
                if (data.success) {
                    $('#refvisi_id').val(data.uraian_visi); // Update input text dengan uraian_visi
                    $('#refvisi_id_hidden').val(data.refvisi_id); // Update hidden input dengan refvisi_id
                } else {
                    $('#refvisi_id').val('Visi tidak ditemukan'); // Jika tidak ditemukan
                    $('#refvisi_id_hidden').val(''); // Kosongkan hidden input
                }
            },
            error: function() {
                $('#refvisi_id').val('Terjadi kesalahan'); // Jika terjadi kesalahan
                $('#refvisi_id_hidden').val(''); // Kosongkan hidden input
            }
        });

        // Lakukan AJAX untuk mendapatkan refmisi_id
        $.ajax({
            url: '" . \yii\helpers\Url::to(['get-refmisi']) . "', // Ganti dengan URL controller yang sesuai
            type: 'GET',
            data: { id: selectedId },
            success: function(data) {
                if (data.success) {
                    $('#refmisi_id').val(data.uraian_misi); // Update input text dengan uraian_misi
                    $('#refmisi_id_hidden').val(data.refmisi_id); // Update hidden input dengan refmisi_id
                } else {
                    $('#refmisi_id').val('Misi tidak ditemukan'); // Jika tidak ditemukan
                    $('#refmisi_id_hidden').val(''); // Kosongkan hidden input
                }
            },
            error: function() {
                $('#refmisi_id').val('Terjadi kesalahan'); // Jika terjadi kesalahan
                $('#refmisi_id_hidden').val(''); // Kosongkan hidden input
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
        $('form#datasasaranrenstra').off('submit').on('submit', function(e) {
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
        $('#table-style-hover').load(location.href + ' #table-style-hover > *');
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

<div class="sakip-sasaranrenstra-form">

    <?php
    $form = ActiveForm::begin([
        'id' => 'datasasaranrenstra',
        'options' => ['enctype' => 'multipart/form-data'],
    ]);
    ?>

    <?= $form->field($model, 'uraian_indikatorsasaranrenstra')->hiddenInput(['rows' => 6])->label(false) ?>

    <!-- refsasaranrenstra_id: Uraian Sasaran Renstra -->
    <?= $form->field($model, 'refsasaranrenstra_id')->hiddenInput()->label(false) ?>

    <div class="form-group">
        <label for="refsasaranrenstra_id">Uraian Sasaran Renstra</label>
        <input type="text" id="refsasaranrenstra_id" class="form-control" value="<?= $model->refSasaranrenstra->uraian_sasaranrenstra ?>" readonly>
    </div>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'kriteria_sasaranrenstra')->textarea(['rows' => 2])->label('Kriteria') ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'formulasi_sasaranrenstra')->textarea(['rows' => 2])->label('Formulasi') ?>
        </div>
    </div>
    <?= $form->field($model, 'alasan_sasaranrenstra')->textarea(['rows' => 3])->label('Alasan') ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>