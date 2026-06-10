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
use yii\widgets\ActiveForm;
use dosamigos\ckeditor\CKEditor;
use frontend\models\SakipSkpd;
use frontend\models\SakipTujuanrenstra;

/** @var yii\web\View $this */
/** @var frontend\models\SakipIndikatoryujuanrenstra $model */
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
        $('form#dataindikatortujuanrenstra').off('submit').on('submit', function(e) {
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

<div class="sakip-indikatortujuanrenstra-form">

    <?php
    $form = ActiveForm::begin([
        'id' => 'dataindikatortujuanrenstra',
        'options' => ['enctype' => 'multipart/form-data'],
    ]);
    ?>

    <?= $form->field($model, 'uraian_indikatortujuanrenstra')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'refskpd_id')->hiddenInput(['value' => $userRefSkpdId])->label(false) ?>

    <div class="form-group">
        <label for="nama_skpd">Nama SKPD</label>
        <input type="text" id="nama_skpd" class="form-control" value="<?= Html::encode($namaSkpd) ?>" readonly>
    </div>

    <!-- Hidden input untuk refsasaranrenstra_id -->
    <?= $form->field($model, 'refsasaranrenstra_id')->hiddenInput(['id' => 'refsasaranrenstra_id_hidden'])->label(false) ?>

    <!-- Tampilan input disabled untuk refsasaranrenstra_id -->
    <div class="form-group">
        <label for="refsasaranrenstra_id_display">Sasaran Renstra ID</label>
        <input type="text" id="refsasaranrenstra_id_display" class="form-control" value="<?= Html::encode($model->refsasaranrenstra_id) ?>" disabled>
    </div>


    <!-- Hidden input untuk reftujuanrenstra_id -->
    <?= $form->field($model, 'reftujuanrenstra_id')->hiddenInput(['id' => 'reftujuanrenstra_id_hidden'])->label(false) ?>

    <!-- Tampilan input disabled untuk reftujuanrenstra_id -->
    <div class="form-group">
        <label for="uraian_tujuanrenstra_display">Uraian Tujuan Renstra</label>
        <input type="text" id="uraian_tujuanrenstra_display" class="form-control" value="<?= Html::encode($model->tujuanRenstra ? $model->tujuanRenstra->uraian_tujuanrenstra : 'Uraian tidak ditemukan') ?>" disabled>
    </div>

    <!-- Hidden input untuk refperiode_id -->
    <?= $form->field($model, 'refperiode_id')->hiddenInput(['id' => 'refperiode_id_hidden'])->label(false) ?>

    <!-- Tampilan input disabled untuk Periode -->
    <div class="form-group">
        <label for="refperiode_id">Periode Tahun</label>
        <input type="text" id="refperiode_id" class="form-control" value="<?= Html::encode($model->refPeriode ? $model->refPeriode->periode : 'Periode tidak ditemukan') ?>" disabled>
    </div>



    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>