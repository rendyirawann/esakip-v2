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
use frontend\models\SakipPenjabatSkpd;
use frontend\models\SakipSkpd;

/** @var yii\web\View $this */
/** @var backend\models\SakipPenjabatskpdCascadingsubkegiatan$model */
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
        $('form#datapenjabatskpdcascadingsubkegiatan').off('submit').on('submit', function(e) {
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
    $('#refpenjabatskpd_id').on('change', function() {
        var refpenjabatskpd_id = $(this).val();

        if (refpenjabatskpd_id) {
            $.ajax({
                url: '" . Url::to(['sakip-penjabatskpd-cascadingsubkegiatan/fetch-eselon']) . "',
                type: 'GET',
                data: { refpenjabatskpd_id: refpenjabatskpd_id },
                success: function(data) {
                    if (data.refeselon_id) {
                        $('#" . Html::getInputId($model, 'refeselon_id') . "').val(data.refeselon_id);  // Set hidden input refeselon_id
                        $('#refeselon_label').val(data.title_eselon);  // Set display field refeselon (title_eselon)
                    } else {
                        $('#" . Html::getInputId($model, 'refeselon_id') . "').val('');  // Clear hidden input if no data found
                        $('#refeselon_label').val('');  // Clear display field if no data found
                    }
                }
            });
        } else {
            $('#" . Html::getInputId($model, 'refeselon_id') . "').val('');
            $('#refeselon_label').val('');
        }
    });
");



// Ambil refskpd_id dari user yang sedang login
$userRefSkpdId = Yii::$app->user->identity->refskpd_id;

// Query untuk mendapatkan nama_skpd berdasarkan refskpd_id
$namaSkpd = SakipSkpd::find()->select('nama_skpd')->where(['refskpd_id' => $userRefSkpdId])->scalar();


?>

<div class="loading-overlay" id="loading-overlay"></div>

<div class="data-penjabatskpd-cascadingsubkegiatan-form">

    <?php
    $form = ActiveForm::begin([
        'id' => 'datapenjabatskpdcascadingsubkegiatan',
        'options' => ['enctype' => 'multipart/form-data'],
    ]);
    ?>

    <?= $form->field($model, 'refpenjabatskpd_id')->dropDownList(
        [],  // Options will be populated by the modal initialization AJAX call
        ['prompt' => 'Pilih Penjabat SKPD', 'id' => 'refpenjabatskpd_id']
    )->label('Penjabat SKPD') ?>

    <!-- Hidden input for refeselon_id -->
    <?= $form->field($model, 'refeselon_id')->hiddenInput()->label(false) ?>

    <!-- Displaying refeselon (pangkat_eselon) -->
    <div class="form-group">
        <label for="refeselon_label">Tingkat Eselon</label>
        <input type="text" id="refeselon_label" class="form-control" value="<?= $model->refEselon ? $model->refEselon->title_eselon : '' ?>" readonly>
    </div>

    <?= $form->field($model, 'refcascadingsubkegiatan_id')->hiddenInput()->label(false) ?>

    <?= $form->field($model, 'refcascadingkegiatan_id')->hiddenInput()->label(false) ?>

    <?= $form->field($model, 'refcascadingprogram_id')->hiddenInput()->label(false) ?>

    <!-- Assuming your modal form includes a hidden input for refindikatorsubkegiatan_id -->
    <?= $form->field($model, 'refindikatorsubkegiatan_id')->hiddenInput(['id' => 'refindikatorsubkegiatan_id'])->label(false) ?>

    <?= $form->field($model, 'refskpd_id')->hiddenInput(['value' => $userRefSkpdId])->label(false) ?>

    <div class="form-group">
        <label for="nama_skpd">Nama SKPD</label>
        <input type="text" id="nama_skpd" class="form-control" value="<?= Html::decode($namaSkpd) ?>" readonly>
    </div>

    <!-- refperiode_id: Periode -->
    <?= $form->field($model, 'refperiode_id')->hiddenInput()->label(false) ?>

    <?= $form->field($model, 'refsasaranrenstra_id')->hiddenInput()->label(false) ?>

    <?= $form->field($model, 'refindikatorsasaranrenstra_id')->hiddenInput()->label(false) ?>

    <?= $form->field($model, 'refprogram_id')->hiddenInput()->label(false) ?>

    <?= $form->field($model, 'refkegiatan_id')->hiddenInput()->label(false) ?>

    <?= $form->field($model, 'refsubkegiatan_id')->hiddenInput()->label(false) ?>

    <div class="form-group">
        <label for="refsubkegiatan_id">Sub Kegiatan</label>
        <input type="text" id="refsubkegiatan_id" class="form-control" value="<?= $model->refSubkegiatan->nama_subkegiatan ?>" readonly>
    </div>

    <?= $form->field($model, 'uraian_sasaransubkegiatan')->hiddenInput()->label(false) ?>

    <?= $form->field($model, 'uraian_indikatorsubkegiatan')->hiddenInput()->label(false) ?>

    <?= $form->field($model, 'subkegiatan_target')->hiddenInput()->label(false) ?>

    <?= $form->field($model, 'subkegiatan_satuan')->hiddenInput()->label(false) ?>

    <?= $form->field($model, 'target_rkt')->hiddenInput(['maxlength' => true])->label(false) ?>

    <?= $form->field($model, 'target_rkt_p')->hiddenInput(['maxlength' => true])->label(false) ?>

    <?= $form->field($model, 'target_pk')->hiddenInput(['maxlength' => true])->label(false) ?>

    <?= $form->field($model, 'target_pk_p')->hiddenInput(['maxlength' => true])->label(false) ?>

    <?= $form->field($model, 'realisasi')->hiddenInput(['maxlength' => true])->label(false) ?>

    <?= $form->field($model, 'capaian')->hiddenInput(['maxlength' => true])->label(false) ?>

    <?= $form->field($model, 'keterangan')->hiddenInput(['rows' => 6])->label(false) ?>

    <?= $form->field($model, 'analisis')->hiddenInput(['rows' => 6])->label(false) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>


</div>