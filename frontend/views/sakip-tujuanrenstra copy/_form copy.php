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
use frontend\models\SakipSasaranrenstra;

/** @var yii\web\View $this */
/** @var frontend\models\SakipTujuanrenstra $model */
/** @var yii\widgets\ActiveForm $form */

$this->registerJsFile('@web/lightapp/assets/js/plugins/choices.min.js', ['depends' => [\yii\web\JqueryAsset::class]]);
$this->registerCssFile('@web/css/choices.min.css');

$this->registerJs("
    $('#datatujuanrenstra').on('change', '#refsasaranrenstra_id', function() {
        var selectedId = $(this).val(); // Ambil nilai yang dipilih

        // Lakukan AJAX untuk mendapatkan misi, tujuan, dan periode
        $.ajax({
            url: '" . \yii\helpers\Url::to(['get-ref-data']) . "', // Ganti dengan URL controller yang sesuai
            type: 'GET',
            data: { id: selectedId },
            success: function(data) {
    if (data.success) {
        // Update input hidden dan text dengan data yang didapat
        $('#refmisi_id_hidden').val(data.refmisi_id);
        $('#refmisi_id').val(data.uraian_misi ? data.uraian_misi : 'Misi tidak ditemukan'); // Update with the actual misi data

        $('#reftujuan_id_hidden').val(data.reftujuan_id);
        $('#reftujuan_id').val(data.uraian_tujuan ? data.uraian_tujuan : 'Tujuan tidak ditemukan'); // Update with the actual tujuan data

        $('#refperiode_id_hidden').val(data.refperiode_id);
        $('#refperiode_id').val(data.periode ? data.periode : 'Periode tidak ditemukan');
    } else {
        $('#refmisi_id').val('Misi tidak ditemukan');
        $('#reftujuan_id').val('Tujuan tidak ditemukan');
        $('#refperiode_id').val('Periode tidak ditemukan');
    }
},
            error: function() {
                $('#refmisi_id').val('Misi tidak ditemukan');
                $('#reftujuan_id').val('Tujuan tidak ditemukan');
                $('#refperiode_id').val('Periode tidak ditemukan');
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
        $('form#datatujuanrenstra').off('submit').on('submit', function(e) {
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

<div class="sakip-tujuanrenstra-form">

    <?php
    $form = ActiveForm::begin([
        'id' => 'datatujuanrenstra',
        'options' => ['enctype' => 'multipart/form-data'],
    ]);
    ?>

    <?= $form->field($model, 'uraian_tujuanrenstra')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'refskpd_id')->hiddenInput(['value' => $userRefSkpdId])->label(false) ?>

    <div class="form-group">
        <label for="nama_skpd">Nama SKPD</label>
        <input type="text" id="nama_skpd" class="form-control" value="<?= Html::encode($namaSkpd) ?>" readonly>
    </div>

    <!-- Dropdown untuk refsasaran_id -->
    <?= $form->field($model, 'refsasaranrenstra_id')->dropDownList(
        ArrayHelper::map($sasaranList, 'refsasaranrenstra_id', 'uraian_sasaranrenstra'), // Mengambil data dari tabel
        ['prompt' => 'Pilih Sasaran...', 'id' => 'refsasaranrenstra_id'] // Tambahkan id untuk JavaScript
    ) ?>

    <?= $form->field($model, 'refmisi_id')->hiddenInput(['id' => 'refmisi_id_hidden'])->label(false) ?>
    <div class="form-group">
        <label for="refmisi_id">Misi Terkait</label>
        <input type="text" id="refmisi_id" class="form-control" value="<?= Html::encode($model->misi ? $model->misi->uraian_misi : 'Misi tidak ditemukan') ?>" disabled>
    </div>

    <?= $form->field($model, 'reftujuan_id')->hiddenInput(['id' => 'reftujuan_id_hidden'])->label(false) ?>
    <div class="form-group">
        <label for="reftujuan_id">Tujuan Terkait</label>
        <input type="text" id="reftujuan_id" class="form-control" value="<?= Html::encode($model->tujuan ? $model->tujuan->uraian_tujuan : 'Tujuan tidak ditemukan') ?>" disabled>
    </div>


    <?= $form->field($model, 'refperiode_id')->hiddenInput(['id' => 'refperiode_id_hidden'])->label(false) ?>
    <div class="form-group">
        <label for="refperiode_id">Periode Tahun</label>
        <input type="text" id="refperiode_id" class="form-control" value="<?= Html::encode($model->refperiode_id ? $model->refPeriode->periode : 'Periode tidak ditemukan') ?>" disabled>
    </div>


    <?= $form->field($model, 'user_create')->hiddenInput(['maxlength' => true])->label(false) ?>
    <?= $form->field($model, 'date_create')->hiddenInput()->label(false) ?>
    <?= $form->field($model, 'user_edit')->hiddenInput(['maxlength' => true])->label(false) ?>
    <?= $form->field($model, 'date_edit')->hiddenInput()->label(false) ?>
    <?= $form->field($model, 'user_delete')->hiddenInput(['maxlength' => true])->label(false) ?>
    <?= $form->field($model, 'date_delete')->hiddenInput()->label(false) ?>

    <?= $form->field($model, 'tujuanrenstra_isaktif')->radioList(
        ['T' => 'Aktif', 'F' => 'Tidak Aktif'],
        [
            'item' => function ($index, $label, $name, $checked, $value) {
                return '<label class="radio-inline">' . Html::radio($name, $checked, ['value' => $value]) . $label . '</label>';
            }
        ]
    )->label('Status Tujuan Renstra') ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>