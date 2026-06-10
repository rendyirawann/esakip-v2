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
use backend\models\SakipEselon;
use backend\models\SakipTitle;
use backend\models\SakipBidangbappeda;

/** @var yii\web\View $this */
/** @var backend\models\SakipPegawaibappeda $model */
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
        $('form#datapegawaibappeda').off('submit').on('submit', function(e) {
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

?>

<div class="loading-overlay" id="loading-overlay"></div>

<div class="data-pegawaibappeda-form">

    <?php
    $form = ActiveForm::begin([
        'id' => 'datapegawaibappeda',
        'options' => ['enctype' => 'multipart/form-data'],
    ]);
    ?>

    <?= $form->field($model, 'statusAparatur')->radioList(array(1 => 'ASN', 2 => 'Non ASN')); ?>

    <?= $form->field($model, 'nama_pegawai')->textInput(['maxlength' => true])->label('Nama Pegawai') ?>

    <?= $form->field($model, 'nip')->textarea(['rows' => 6])->label('NIP') ?>

    <?= $form->field($model, 'refeselon_id')->dropDownList(
        ArrayHelper::map(SakipEselon::find()->orderBy(['refeselon_id' => SORT_ASC])->all(), 'refeselon_id', 'title_eselon'),
        ['prompt' => 'Pilih Jabatan Eselon', 'data-trigger' => '']
    )->label('Jabatan Eselon') ?>

    <?= $form->field($model, 'reftitle_id')->dropDownList(
        ArrayHelper::map(SakipTitle::find()->orderBy(['reftitle_id' => SORT_ASC])->all(), 'reftitle_id', 'nama_title'),
        ['prompt' => 'Pilih Title', 'data-trigger' => '']
    )->label('Title Jabatan') ?>

    <?= $form->field($model, 'refbidangbappeda_id')->dropDownList(
        ArrayHelper::map(SakipBidangbappeda::find()->orderBy(['refbidangbappeda_id' => SORT_ASC])->all(), 'refbidangbappeda_id', 'nama_bidangbappeda'),
        ['prompt' => 'Pilih Bidang Bappeda', 'data-trigger' => '']
    )->label('Bidang Bappeda') ?>

    <?= $form->field($model, 'no_hp')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>


</div>