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
use backend\models\SakipBidang;
use backend\models\SakipUrusan;


/** @var yii\web\View $this */
/** @var backend\models\SakipSkpd $model */
/** @var yii\widgets\ActiveForm $form */
$this->registerJsFile('@web/lightapp/assets/js/plugins/choices.min.js', ['depends' => [\yii\web\JqueryAsset::class]]);
$this->registerCssFile('@web/css/choices.min.css');

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
        $('form#dataskpd').off('submit').on('submit', function(e) {
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

<div class="sakip-skpd-form">

    <?php
    $form = ActiveForm::begin([
        'id' => 'dataskpd',
        'options' => ['enctype' => 'multipart/form-data'],
    ]);
    ?>

    <?= $form->field($model, 'kode_skpd')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'nama_skpd')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'kepala_skpd')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'nip_kepala')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'jabatan_kepala')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'pangkat_kepala')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'refurusan_id')->dropDownList(
        ArrayHelper::map(
            SakipUrusan::find()->orderBy(['urusan_id' => SORT_ASC])->all(),
            'urusan_id',
            'nama_urusan'
        ),
        [
            'prompt' => 'Pilih Urusan',
            'data-trigger' => '',
            'onchange' => '
            var urusanId = $(this).val();
            $.post("' . Url::to(['sakip-skpd/lists', 'id' => '']) . '"+urusanId, function(data) {
                var bidangDropdown = $("select#sakipskpd-refbidang_id");
                bidangDropdown.html(data).val(""); // Reset bidang dengan data baru
                bidangDropdown.prepend("<option value=\"\">Pilih Bidang</option>"); // Tambahkan prompt
            });'
        ]
    )->label('Urusan Terkait') ?>

    <?= $form->field($model, 'refbidang_id')->dropDownList(
        ArrayHelper::map(
            SakipBidang::find()
                ->where(['refurusan_id' => $model->refurusan_id])
                ->orderBy('kode_bidang ASC')
                ->all(),
            'refbidang_id',
            function ($bidang) {
                return "{$bidang->kode_bidang} - {$bidang->nama_bidang}";
            }
        ),
        ['prompt' => 'Pilih Bidang']
    ) ?>


    <?= $form->field($model, 'refskpd_unit')->radioList(
        ['I' => 'Instansi', 'U' => 'Utama', 'P' => 'Pendukung', 'T' => 'Tambahan'],
        [
            'item' => function ($index, $label, $name, $checked, $value) {
                return '<label class="radio-inline">' . Html::radio($name, $checked, ['value' => $value]) . $label . '</label>';
            }
        ]
    )->label('Unit SKPD') ?>

    <?= $form->field($model, 'refskpd_keterangan')->textInput(['value' => 'SOTK Baru', 'readonly' => true]) ?>

    <?= $form->field($model, 'skpd_isaktif')->radioList(
        ['T' => 'Aktif', 'F' => 'Tidak Aktif'],
        [
            'item' => function ($index, $label, $name, $checked, $value) {
                return '<label class="radio-inline">' . Html::radio($name, $checked, ['value' => $value]) . $label . '</label>';
            }
        ]
    )->label('Status SKPD') ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>