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
    // Reset refsasaranrenstra_id when program is changed
            $('#sakip-cascadingkegiatan-refsasaranrenstra_id').val('');
            $('#refsasaranrenstra_id_display').val('Refsasaranrenstra_id tidak ditemukan');

        showLoading();
        $.ajax({
            url: '" . \yii\helpers\Url::to(['get-cascadingprogram-by-program']) . "',
            type: 'POST',
            data: { refprogram_id: refProgramId },
            success: function(response) {
                if(response.success) {
                    $('#refprogram-indicator-dropdown').html(response.indicatorDropdown);
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

 $('#refprogram-indicator-dropdown').change(function() {
        var refCascadingProgramId = $(this).val();
        
        // Reset refsasaranrenstra_id and display value when refcascadingprogram_id changes
        $('#sakip-cascadingkegiatan-refsasaranrenstra_id').val('');
        $('#refsasaranrenstra_id_display').val('Refsasaranrenstra_id tidak ditemukan');
        $('#sakip-cascadingkegiatan-refindikatorsasaranrenstra_id').val('');
        $('#refindikatorsasaranrenstra_id_display').val('refindikatorsasaranrenstra_id tidak ditemukan');

        if(refCascadingProgramId) {
            showLoading();
            $.ajax({
                url: '" . \yii\helpers\Url::to(['get-sasaranrenstra-by-cascadingprogram']) . "',
                type: 'POST',
                data: { refcascadingprogram_id: refCascadingProgramId },
                success: function(response) {
                    if(response.success) {
                        // Update refsasaranrenstra_id
                        $('#sakip-cascadingkegiatan-refsasaranrenstra_id').val(response.refsasaranrenstra_id);
                        $('#refsasaranrenstra_id_display').val(response.uraian_sasaranrenstra);
                        
                        // Fetch refindikatorsasaranrenstra_id
                        fetchIndikatorsasaranrenstra(refCascadingProgramId);
                    } else {
                        alert('Error: ' + response.message);
                    }
                    hideLoading();
                },
                error: function() {
                    alert('Failed to fetch sasaran renstra data.');
                    hideLoading();
                }
            });
        }
    });

    function fetchIndikatorsasaranrenstra(refCascadingProgramId) {
        $.ajax({
            url: '" . \yii\helpers\Url::to(['get-indikatorsasaranrenstra-by-cascadingprogram']) . "',
            type: 'POST',
            data: { refcascadingprogram_id: refCascadingProgramId },
            success: function(response) {
                if(response.success) {
                    // Update refindikatorsasaranrenstra_id
                    $('#sakip-cascadingkegiatan-refindikatorsasaranrenstra_id').val(response.refindikatorsasaranrenstra_id);
                    $('#refindikatorsasaranrenstra_id_display').val(response.uraian_indikatorsasaranrenstra);
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function() {
                alert('Failed to fetch indikator sasaran renstra data.');
            }
        });
    }


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



// Ambil refskpd_id dari user yang sedang login
$userRefSkpdId = Yii::$app->user->identity->refskpd_id;

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

    <?= $form->field($model, 'refperiode_id')->dropDownList(
        ArrayHelper::map($periodeList, 'refperiode_id', 'periode'),
        ['prompt' => 'Select Periode', 'id' => 'ref-periode-dropdown']
    ) ?>

    <div id="additional-fields" style="display:none;">

        <?= $form->field($model, 'refprogram_id')->dropDownList([], ['prompt' => 'Select Program', 'id' => 'refprogram-dropdown']) ?>

        <?= $form->field($model, 'refcascadingprogram_id')->dropDownList([], ['prompt' => 'Select Indicator', 'id' => 'refprogram-indicator-dropdown']) ?>

        <!-- Hidden input for refsasaranrenstra_id -->
        <?= $form->field($model, 'refsasaranrenstra_id')->hiddenInput(['id' => 'sakip-cascadingkegiatan-refsasaranrenstra_id'])->label(false) ?>


        <div class="form-group">
            <label for="refsasaranrenstra_id_display">Refsasaran Renstra Uraian</label>
            <input type="text" id="refsasaranrenstra_id_display" class="form-control" value="<?= Html::encode($model->refSasaranRenstra->uraian_sasaranrenstra ?? 'Refsasaranrenstra_id tidak ditemukan') ?>" disabled>
        </div>

        <!-- Hidden input for refindikatorsasaranrenstra_id -->
        <?= $form->field($model, 'refindikatorsasaranrenstra_id')->hiddenInput(['id' => 'sakip-cascadingkegiatan-refindikatorsasaranrenstra_id'])->label(false) ?>


        <div class="form-group">
            <label for="refindikatorsasaranrenstra_id_display">Indikator Refsasaran Renstra Uraian</label>
            <input type="text" id="refindikatorsasaranrenstra_id_display" class="form-control" value="<?= Html::encode($model->refIndikatorsasaranRenstra->uraian_indikatorsasaranrenstra ?? 'refindikatorsasaranrenstra_id tidak ditemukan') ?>" disabled>
        </div>


        <?= $form->field($model, 'refkegiatan_id')->dropDownList([], ['prompt' => 'Select Kegiatan', 'id' => 'refkegiatan-dropdown']) ?>


        <?= $form->field($model, 'uraian_sasarankegiatan')->textarea(['rows' => 6]) ?>

        <?= $form->field($model, 'uraian_indikatorkegiatan')->textarea(['rows' => 6]) ?>

        <?= $form->field($model, 'refskpd_id')->hiddenInput(['value' => $userRefSkpdId])->label(false) ?>

        <div class="form-group">
            <label for="nama_skpd">Nama SKPD</label>
            <input type="text" id="nama_skpd" class="form-control" value="<?= Html::decode($namaSkpd) ?>" readonly>
        </div>

        <?= $form->field($model, 'kegiatan_target')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'kegiatan_satuan')->textInput(['maxlength' => true]) ?>

    </div>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>