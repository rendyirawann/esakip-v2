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
use yii\helpers\ArrayHelper;
use yii\helpers\Url; // Untuk membuat URL AJAX
use yii\widgets\ActiveForm;
use backend\models\User;
use backend\models\SakipSkpd;


/** @var yii\web\View $this */
/** @var backend\models\SakipKoordinasi $model */
/** @var yii\widgets\ActiveForm $form */
$this->registerJsFile('@web/lightapp/assets/js/plugins/choices.min.js', ['depends' => [\yii\web\JqueryAsset::class]]);
$this->registerCssFile('@web/css/choices.min.css');

$this->registerJs("

    // 1. Fungsi helper ini sudah bagus, tidak perlu diubah.
    function initializeSelectize(selector) {
        if ($(selector).length > 0 && $(selector)[0].selectize) {
            $(selector)[0].selectize.destroy();
        }
        $(selector).selectize({
            create: false,
            sortField: 'text',
            placeholder: 'Ketik untuk mencari SKPD...'
        });
    }

    // --- INTI PERBAIKAN ---
    // 2. Panggil fungsi inisialisasi secara langsung, tanpa dibungkus event apapun.
    // Script ini akan dieksekusi oleh browser setiap kali konten _form.php
    // berhasil dimuat ke dalam modal oleh AJAX. Ini adalah cara yang paling andal.
    initializeSelectize('#koordinasi-refskpd_id');

    // 3. Handler untuk submit form. Dibiarkan seperti ini sudah cukup aman.
    // Menggunakan event delegation dari document dan .off() adalah praktik yang baik.
    $(document).off('submit', 'form#koordinasi-form').on('submit', 'form#koordinasi-form', function(e) {
        e.preventDefault();
        var form = $(this);
        form.find(':submit').prop('disabled', true); // Nonaktifkan tombol
        $('#loading-overlay').show();

        $.ajax({
            type: form.attr('method'),
            url: form.attr('action'),
            data: form.serialize(),
            success: function(response) {
                if (response.success) {
                    // Redirect setelah berhasil
                    window.location.href = response.redirect;
                } else {
                    alert('Gagal menyimpan: ' + JSON.stringify(response.errors));
                    form.find(':submit').prop('disabled', false); // Aktifkan lagi jika gagal
                }
            },
            error: function() {
                alert('Terjadi kesalahan saat mengirim data.');
                form.find(':submit').prop('disabled', false); // Aktifkan lagi jika error
            },
            complete: function() {
                // Sembunyikan loading overlay di sini, bukan di error/success
                // agar selalu berjalan.
                $('#loading-overlay').hide();
            }
        });
    });

");
?>

<link href="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/css/selectize.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/js/standalone/selectize.min.js"></script>

<div class="loading-overlay" id="loading-overlay" style="display:none;"></div>

<div class="sakip-koordinasi-form">
    <?php
    $form = ActiveForm::begin([
        'id' => 'koordinasi-form',
        'options' => ['enctype' => 'multipart/form-data'],
    ]);
    ?>

    <?= $form->field($model, 'refuser_id')->hiddenInput()->label(false) ?>

    <?= $form->field($model, 'refskpd_id')->dropDownList(
        ArrayHelper::map(
            SakipSkpd::find()->where(['skpd_isaktif' => 'T'])->orderBy('refskpd_id ASC')->all(),
            'refskpd_id',
            function ($refSkpd) {
                return "{$refSkpd->kode_skpd} - {$refSkpd->nama_skpd}";
            }
        ),
        [
            'prompt' => 'Pilih SKPD',
            // Beri ID yang spesifik agar mudah ditargetkan oleh JavaScript
            'id' => 'koordinasi-refskpd_id'
        ]
    ) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>