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
use frontend\models\SakipCascadingsubkegiatan;
use frontend\models\SimonaRincianbelanjacascadingkegiatan;

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
        $('form#datarincianbelanjasimonacascadingkegiatan').off('submit').on('submit', function(e) {
            e.preventDefault(); // Mencegah form submit biasa

            var \$form = $(this);
            var \$submitBtn = \$form.find(':submit'); // Temukan tombol submit

                           // Format anggaran_rincianbelanja before submission
            var anggaranField = $('#simonarincianbelanjacascadingkegiatan-anggaran_rincianbelanja');
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
        $('#simonarincianbelanjacascadingkegiatan-anggaran_rincianbelanja').on('input', function() {
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

// Calculate remaining budget
$subkegiatanAnggaran = SakipCascadingsubkegiatan::find()
    ->where([
        'refcascadingkegiatan_id' => $detail->refcascadingkegiatan_id,
        'refkegiatan_id' => $detail->refkegiatan_id,
    ])
    ->sum('CAST(subkegiatan_anggaran AS UNSIGNED)');

$totalAnggaranRincian = 0;
$expenses = SimonaRincianbelanjacascadingkegiatan::find()->where(['refsimonacascadingkegiatan_id' => $detail->refsimonacascadingkegiatan_id])->all();
foreach ($expenses as $expense) {
    $totalAnggaranRincian += (int) $expense->anggaran_rincianbelanja; // Summing the anggaran_rincianbelanja
}

$remainingAnggaran = $subkegiatanAnggaran - $totalAnggaranRincian;

// Ambil refskpd_id dari user yang sedang login
$userRefSkpdId = Yii::$app->user->identity->refskpd_id;

// Query untuk mendapatkan nama_skpd berdasarkan refskpd_id
$namaSkpd = SakipSkpd::find()->select('nama_skpd')->where(['refskpd_id' => $userRefSkpdId])->scalar();


?>

<div class="loading-overlay" id="loading-overlay"></div>

<div class="data-simona-rincianbelanjacascadingkegiatan-form">

    <?php
    $form = ActiveForm::begin([
        'id' => 'datarincianbelanjasimonacascadingkegiatan',
        'options' => ['enctype' => 'multipart/form-data'],
    ]);
    ?>


    <?= $form->field($model, 'refsimonacascadingkegiatan_id')->hiddenInput(['maxlength' => true])->label(false) ?>

    <?= $form->field($model, 'refcascadingkegiatan_id')->hiddenInput()->label(false) ?>
    <?= $form->field($model, 'refkegiatan_id')->hiddenInput()->label(false) ?>

    <?= $form->field($model, 'refcascadingkegiatan_id')->hiddenInput()->label(false) ?>
    <?= $form->field($model, 'refkegiatan_id')->hiddenInput()->label(false) ?>

    <?= $form->field($model, 'refcascadingprogram_id')->hiddenInput()->label(false) ?>
    <?= $form->field($model, 'refprogram_id')->hiddenInput()->label(false) ?>

    <?= $form->field($model, 'refskpd_id')->hiddenInput()->label(false) ?>
    <?= $form->field($model, 'refperiode_id')->hiddenInput()->label(false) ?>

    <?= $form->field($model, 'detail_rincianbelanja')->textarea(['rows' => 6]) ?>

    <div class="row">
        <div class="col-lg-3">
            <?= $form->field($model, 'satuan_rincianbelanja')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-lg-3">
            <?= $form->field($model, 'jumlah_rincianbelanja')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-lg-3">
            <?= $form->field($model, 'anggaran_rincianbelanja')->textInput(['maxlength' => true]) ?>
        </div>
    </div>

    <?php if (isset($detail)): ?>
        <div class="alert alert-info mt-3">
            <strong>Sisa Anggaran Subkegiatan:</strong>
            <?= 'Rp. ' . number_format($remainingAnggaran, 0, ',', '.'); ?>
        </div>
    <?php else: ?>
        <p><i>Detail tidak ditemukan.</i></p>
    <?php endif; ?>

    <!-- Rest of the form content goes here -->


    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>


</div>