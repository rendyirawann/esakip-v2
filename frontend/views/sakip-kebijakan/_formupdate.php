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
use frontend\models\SakipKebijakan;

/** @var yii\web\View $this */
/** @var frontend\models\SakipTujuanrenstra $model */
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
        $('form#datakebijakan').off('submit').on('submit', function(e) {
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


// Ambil refskpd_id dari model, default ke user yang sedang login jika null
$userRefSkpdId = $model->refskpd_id ?: Yii::$app->user->identity->refskpd_id;

// Query untuk mendapatkan nama_skpd berdasarkan refskpd_id
$namaSkpd = SakipSkpd::find()->select('nama_skpd')->where(['refskpd_id' => $userRefSkpdId])->scalar();


?>

<div class="loading-overlay" id="loading-overlay"></div>

<div class="sakip-stretegi-form">

    <?php
    $form = ActiveForm::begin([
        'id' => 'datakebijakan',
        'options' => ['enctype' => 'multipart/form-data'],
    ]);
    ?>

    <?= $form->field($model, 'uraian_kebijakan')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'refskpd_id')->hiddenInput(['value' => $userRefSkpdId])->label(false) ?>

    <div class="form-group">
        <label for="nama_skpd">Nama SKPD</label>
        <input type="text" id="nama_skpd" class="form-control" value="<?= Html::decode($namaSkpd) ?>" readonly>
    </div>

    <!-- Hidden input untuk refstrategi_id -->
    <?= $form->field($model, 'refstrategi_id')->hiddenInput(['id' => 'refstrategi_id_hidden'])->label(false) ?>

    <!-- Hidden input untuk refsasaranrenstra_id -->
    <?= $form->field($model, 'refsasaranrenstra_id')->hiddenInput(['id' => 'refsasaranrenstra_id_hidden'])->label(false) ?>

    <!-- Hidden input untuk refsasaran_id -->
    <?= $form->field($model, 'refsasaran_id')->hiddenInput(['id' => 'refsasaran_id_hidden'])->label(false) ?>

    <!-- Hidden input untuk refmisi_id -->
    <?= $form->field($model, 'refmisi_id')->hiddenInput(['id' => 'refmisi_id_hidden'])->label(false) ?>

    <!-- Hidden input untuk reftujuan_id -->
    <?= $form->field($model, 'reftujuan_id')->hiddenInput(['id' => 'reftujuan_id_hidden'])->label(false) ?>

    <!-- Hidden input untuk refperiode_id -->
    <?= $form->field($model, 'refperiode_id')->hiddenInput(['id' => 'refperiode_id_hidden'])->label(false) ?>

    <?= $form->field($model, 'user_create')->hiddenInput(['maxlength' => true])->label(false) ?>
    <?= $form->field($model, 'date_create')->hiddenInput()->label(false) ?>
    <?= $form->field($model, 'user_edit')->hiddenInput(['maxlength' => true])->label(false) ?>
    <?= $form->field($model, 'date_edit')->hiddenInput()->label(false) ?>
    <?= $form->field($model, 'user_delete')->hiddenInput(['maxlength' => true])->label(false) ?>
    <?= $form->field($model, 'date_delete')->hiddenInput()->label(false) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>