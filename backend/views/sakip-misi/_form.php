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
use backend\models\SakipPeriode;
use backend\models\SakipVisi;
use dosamigos\ckeditor\CKEditor;

/** @var yii\web\View $this */
/** @var backend\models\SakipMisi $model */
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
        $('form#datamisi').off('submit').on('submit', function(e) {
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
    $(document).ready(function() {
        var selectedOption = $('#sakipmisi-refvisi_id option:selected');
        selectedOption.html(selectedOption.html().replace(/&lt;/g, '<').replace(/&gt;/g, '>')); // Decode HTML
    });
");


?>

<div class="loading-overlay" id="loading-overlay"></div>

<div class="data-misi-form">

    <?php
    $form = ActiveForm::begin([
        'id' => 'datamisi',
        'options' => ['enctype' => 'multipart/form-data'],
    ]);
    ?>

    <?= $form->field($model, 'uraian_misi')->widget(CKEditor::className(), [
        'options' => ['rows' => 5],
        'preset' => 'full'
    ]); ?>

    <?= $form->field($model, 'refperiode_id')->dropDownList(
        ArrayHelper::map(
            SakipPeriode::find()->orderBy(['refperiode_id' => SORT_ASC])->all(),
            'refperiode_id',
            'periode'
        ),
        [
            'prompt' => 'Pilih Periode',
            'data-trigger' => '',
            'onchange' => '
            var periodeId = $(this).val();
            $.post("' . Url::to(['sakip-misi/lists', 'id' => '']) . '"+periodeId, function(data) {
                var visiDropdown = $("select#sakipmisi-refvisi_id");
                visiDropdown.html(data).val(""); // Reset visi dengan data baru
                visiDropdown.prepend("<option value=\"\">Pilih Uraian Visi Terkait</option>"); // Tambahkan prompt
            });'
        ]
    )->label('Pilih Periode') ?>

    <?= $form->field($model, 'refvisi_id')->dropDownList(
        ArrayHelper::map(
            SakipVisi::find()->where(['refperiode_5tahun_id' => $model->refperiode_5tahun_id])->all(),
            'refvisi_id',
            function ($model) {
                return Html::decode($model->uraian_visi) . " - " . Html::encode($model->refperiode_id); // Decode & encode data
            }
        ),
        [
            'prompt' => 'Pilih Uraian Visi Terkait',
            'options' => [
                $model->refvisi_id => ['Selected' => true], // Menandai yang terpilih
            ],
        ]
    )->label('Visi Terkait') ?>




    <?= $form->field($model, 'misi_isaktif')->radioList(
        ['T' => 'Aktif', 'F' => 'Tidak Aktif'],
        [
            'item' => function ($index, $label, $name, $checked, $value) {
                return '<label class="radio-inline">' . Html::radio($name, $checked, ['value' => $value]) . $label . '</label>';
            }
        ]
    )->label('Status Misi') ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>



</div>