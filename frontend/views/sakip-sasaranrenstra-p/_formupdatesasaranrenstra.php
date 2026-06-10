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

use frontend\models\SakipSasaranP;
use frontend\models\SakipPeriode;
use frontend\models\SakipSkpd;
use frontend\models\SakipTujuanrenstraP;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use dosamigos\ckeditor\CKEditor;

/** @var yii\web\View $this */
/** @var backend\models\SakipVisi $model */
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

    // Fetch sasaran options when refperiode_id is selected
    $('#ref-periode-dropdown').change(function() {
        showLoading();
        
        var refPeriodeId = $(this).val();
        
        $.ajax({
            url: '" . \yii\helpers\Url::to(['get-sasaran-options']) . "',
            type: 'GET',
            data: { refperiode_id: refPeriodeId },
            success: function(data) {
                $('#sakip-sasaranrenstra-refsasaran_p_id').html(data);
                $('#additional-fields').show();  // Show additional fields once period is selected
                hideLoading();  // Hide loading overlay
            },
            error: function() {
                hideLoading();
                alert('Error loading data');
            }
        });
    });

    // Fetch visi, misi, and tujuan based on selected refsasaran_p_id
    $('#sakip-sasaranrenstra-refsasaran_p_id').change(function() {
        var refsasaranId = $(this).val();
        if (refsasaranId) {
            // Fetch refvisi_p_id
            $.ajax({
                url: '" . \yii\helpers\Url::to(['get-refvisi']) . "',
                type: 'GET',
                data: { id: refsasaranId },
                success: function(data) {
                    if (data.success) {
                        $('#refvisi_p_id_hidden').val(data.refvisi_p_id);
                        $('#refvisi_p_id').val(data.uraian_visi_p);
                    } else {
                        $('#refvisi_p_id_hidden').val('');
                        $('#refvisi_p_id').val('Visi tidak ditemukan');
                    }
                }
            });

            // Fetch refmisi_p_id
            $.ajax({
                url: '" . \yii\helpers\Url::to(['get-refmisi']) . "',
                type: 'GET',
                data: { id: refsasaranId },
                success: function(data) {
                    if (data.success) {
                        $('#refmisi_p_id_hidden').val(data.refmisi_p_id);
                        $('#refmisi_p_id').val(data.uraian_misi);
                    } else {
                        $('#refmisi_p_id_hidden').val('');
                        $('#refmisi_p_id').val('Misi tidak ditemukan');
                    }
                }
            });

            // Fetch reftujuan_p_id
            $.ajax({
                url: '" . \yii\helpers\Url::to(['get-reftujuan']) . "',
                type: 'GET',
                data: { id: refsasaranId },
                success: function(data) {
                    if (data.success) {
                        $('#reftujuan_p_id_hidden').val(data.reftujuan_p_id);
                        $('#reftujuan_p_id').val(data.uraian_tujuan);
                    } else {
                        $('#reftujuan_p_id_hidden').val('');
                        $('#reftujuan_p_id').val('Tujuan tidak ditemukan');
                    }
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

<div class="sakip-sasaranrenstra-form">

    <?php
    $form = ActiveForm::begin([
        'id' => 'datasasaranrenstra',
        'options' => ['enctype' => 'multipart/form-data'],
    ]);
    ?>

    <?= $form->field($model, 'refperiode_id')->hiddenInput(['rows' => 6])->label(false) ?>

    <?= $form->field($model, 'uraian_sasaranrenstra_p')->textarea(['rows' => 2])->label('Uraian Sasaran Renstra Perubahan') ?>

    <?= $form->field($model, 'refskpd_id')->hiddenInput(['value' => $userRefSkpdId])->label(false) ?>

    <div class="form-group">
        <label for="nama_skpd">Nama SKPD</label>
        <input type="text" id="nama_skpd" class="form-control" value="<?= Html::encode($namaSkpd) ?>" readonly>
    </div>

    <?= $form->field($model, 'refsasaran_p_id')->hiddenInput(['rows' => 6])->label(false) ?>

    <?= $form->field($model, 'refvisi_p_id')->hiddenInput(['id' => 'refvisi_p_id_hidden'])->label(false) ?>

    <?= $form->field($model, 'refmisi_p_id')->hiddenInput(['id' => 'refmisi_p_id_hidden'])->label(false) ?>

    <?= $form->field($model, 'reftujuan_p_id')->hiddenInput(['id' => 'reftujuan_p_id_hidden'])->label(false) ?>

    <?= $form->field($model, 'reftujuanrenstra_p_id')->hiddenInput(['value' => null])->label(false) ?>

    <?= $form->field($model, 'sasaranrenstra_p_isaktif')->radioList(
        ['T' => 'Aktif', 'F' => 'Tidak Aktif'],
        [
            'item' => function ($index, $label, $name, $checked, $value) {
                return '<label class="radio-inline">' . Html::radio($name, $checked, ['value' => $value]) . $label . '</label>';
            }
        ]
    )->label('Status Sasaran Renstra Perubahan') ?>

    <?= $form->field($model, 'alasan_sasaranrenstra_p')->hiddenInput()->label(false) ?>

    <?= $form->field($model, 'formulasi_sasaranrenstra_p')->hiddenInput()->label(false) ?>

    <?= $form->field($model, 'kriteria_sasaranrenstra_p')->hiddenInput()->label(false) ?>


    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>