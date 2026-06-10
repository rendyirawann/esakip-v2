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
use frontend\models\SakipSkpd;

/** @var yii\web\View $this */
/** @var backend\models\SimonaCascadingkegiatan$model */
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
        $('form#datasimonacascadingkegiatan').off('submit').on('submit', function(e) {
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

<div class="data-simona-cascadingkegiatan-form">

    <?php
    $form = ActiveForm::begin([
        'id' => 'datasimonacascadingkegiatan',
        'options' => ['enctype' => 'multipart/form-data'],
    ]);
    ?>

    <?= $form->field($model, 'refcascadingkegiatan_id')->hiddenInput()->label(false) ?>

    <?= $form->field($model, 'refcascadingprogram_id')->hiddenInput()->label(false) ?>

    <?= $form->field($model, 'refskpd_id')->hiddenInput(['value' => $userRefSkpdId])->label(false) ?>

    <!-- refperiode_id: Periode -->
    <?= $form->field($model, 'refperiode_id')->hiddenInput()->label(false) ?>

    <?= $form->field($model, 'refsasaranrenstra_id')->hiddenInput()->label(false) ?>

    <?= $form->field($model, 'refindikatorsasaranrenstra_id')->hiddenInput()->label(false) ?>

    <?= $form->field($model, 'refprogram_id')->hiddenInput()->label(false) ?>

    <?= $form->field($model, 'refkegiatan_id')->hiddenInput()->label(false) ?>

    <?= $form->field($model, 'uraian_sasarankegiatan')->hiddenInput()->label(false) ?>

    <?= $form->field($model, 'uraian_indikatorkegiatan')->hiddenInput()->label(false) ?>

    <?= $form->field($model, 'kegiatan_target')->hiddenInput()->label(false) ?>

    <?= $form->field($model, 'kegiatan_satuan')->hiddenInput()->label(false) ?>

    <?= $form->field($model, 'refpegawaibappeda_id')->hiddenInput()->label(false) ?>

    <?= $form->field($model, 'nama_tahapankegiatan')->textarea()->label('Nama Tahapan') ?>

    <?= $form->field($model, 'date_start')->textInput(['class' => 'form-control', 'id' => 'datetime-local', 'type' => 'date', 'placeholder' => 'YY/MM/DD'])->label('Tanggal Mulai') ?>

    <?= $form->field($model, 'expired_date')->textInput(['class' => 'form-control', 'id' => 'datetime-local', 'type' => 'date', 'placeholder' => 'YY/MM/DD'])->label('Tanggal Selesai') ?>

    <?= $form->field($model, 'status_simonacascadingkegiatan')->radioList(
        ['T' => 'Aktif', 'F' => 'Tidak Aktif', 'P' => 'Pending', 'S' => 'Selesai'],
        [
            'item' => function ($index, $label, $name, $checked, $value) {
                return '<label class="radio-inline">' . Html::radio($name, $checked, ['value' => $value]) . $label . '</label>';
            }
        ]
    )->label('Status Tahapan') ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>


</div>