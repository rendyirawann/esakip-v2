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
use backend\models\SakipProgram;

/** @var yii\web\View $this */
/** @var backend\models\SakipKegiatan $model */
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

    function saveFormData() {
        $('#datakegiatan').find(':input').each(function() {
            var input = $(this);
            var name = input.attr('name');
            if (input.is(':radio')) {
                if (input.is(':checked')) {
                    localStorage.setItem(name, input.val());
                }
            } else {
                localStorage.setItem(name, input.val());
            }
        });
    }

    function loadFormData() {
        $('#datakegiatan').find(':input').each(function() {
            var input = $(this);
            var name = input.attr('name');
            if (input.is(':radio')) {
                var savedValue = localStorage.getItem(name);
                if (savedValue) {
                    input.prop('checked', input.val() === savedValue);
                }
            } else {
                if (localStorage.getItem(name)) {
                    input.val(localStorage.getItem(name));
                }
            }
        });
    }

    $(document).ready(function() {
        initializeChoices();
        loadFormData();

        $(document).on('shown.bs.modal', '#createModal', function() {
            initializeChoices();
            loadFormData(); // Load form data when modal is opened
            $('#datakegiatan').find(':radio').each(function() {
                var input = $(this);
                var savedValue = localStorage.getItem(input.attr('name'));
                if (savedValue) {
                    input.prop('checked', input.val() === savedValue);
                }
            });
        });

        $('#datakegiatan').on('input', function() {
            saveFormData(); // Save form data on input change
        });

        $('#datakegiatan').off('submit').on('submit', function(e) {
            e.preventDefault();

            var \$form = $(this);
            var \$submitBtn = \$form.find(':submit');
            \$submitBtn.prop('disabled', true);

            $('#loading-overlay').show();

            $.ajax({
                type: \$form.attr('method'),
                url: \$form.attr('action'),
                data: \$form.serialize(),
                success: function(response) {
                    if (response.success) {
                        localStorage.clear(); // Clear local storage on successful submit
                        window.location.href = response.redirect;
                    } else {
                        console.log(response.errors);
                    }
                },
                error: function() {
                    console.log('Terjadi kesalahan saat mengirim data.');
                },
                complete: function() {
                    \$submitBtn.prop('disabled', false);
                    $('#loading-overlay').hide();
                }
            });
        });
    });
");
?>
<div class="loading-overlay" id="loading-overlay"></div>

<div class="sakip-kegiatan-form">

    <?php
    $form = ActiveForm::begin([
        'id' => 'datakegiatan',
        'options' => ['enctype' => 'multipart/form-data'],
    ]);
    ?>

    <?= $form->field($model, 'kode_kegiatan')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'nama_kegiatan')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'refurusan_id')->dropDownList(
        ArrayHelper::map(
            SakipUrusan::find()->orderBy(['urusan_id' => SORT_ASC])->all(),
            'urusan_id',
            function ($model) {
                return $model->kode_urusan . ' - ' . $model->nama_urusan;
            }
        ),
        [
            'prompt' => 'Pilih Urusan',
            'data-trigger' => '',
            'onchange' => '
            var urusanId = $(this).val();
            $.post("' . Url::to(['sakip-kegiatan/lists', 'id' => '']) . '"+urusanId, function(data) {
                var bidangDropdown = $("select#sakipkegiatan-refbidang_id");
                bidangDropdown.html(data).val(""); // Reset bidang dengan data baru
                bidangDropdown.prepend("<option value=\"\">Pilih Bidang</option>"); // Tambahkan prompt
                
                // Reset program dropdown
                $("select#sakipkegiatan-refprogram_id").html("<option>Pilih Program</option>").val(""); // Tambahkan prompt
            });'
        ]
    )->label('Urusan Terkait') ?>


    <?= $form->field($model, 'refbidang_id')->dropDownList(
        ArrayHelper::map(
            SakipBidang::find()->where(['refurusan_id' => $model->refurusan_id])->orderBy('kode_bidang ASC')->all(),
            'refbidang_id',
            function ($model) {
                return $model->nama_bidang . ' - ' . $model->kode_bidang;
            }
        ),
        [
            'prompt' => 'Pilih Bidang',
            'onchange' => '
            var bidangId = $(this).val();
            $.post("' . Url::to(['sakip-kegiatan/list-program', 'id' => '']) . '"+bidangId, function(data) {
                var programDropdown = $("select#sakipkegiatan-refprogram_id");
                programDropdown.html(data).val(""); // Reset program dengan data baru
                programDropdown.prepend("<option value=\"\">Pilih Program</option>"); // Tambahkan prompt
            });'
        ]
    )->label('Bidang Terkait') ?>


    <?= $form->field($model, 'refprogram_id')->dropDownList(
        ArrayHelper::map(
            SakipProgram::find()->where(['refbidang_id' => $model->refbidang_id])->orderBy('kode_program ASC')->all(),
            'refprogram_id',
            function ($model) {
                return $model->nama_program . ' - ' . $model->kode_program;
            }
        ),
        [
            'prompt' => 'Pilih Program'
        ]
    )->label('Program Terkait') ?>


    <?= $form->field($model, 'kegiatan_isaktif')->radioList(
        ['T' => 'Aktif', 'F' => 'Tidak Aktif'],
        [
            'item' => function ($index, $label, $name, $checked, $value) {
                return '<label class="radio-inline">' . Html::radio($name, $checked, ['value' => $value]) . $label . '</label>';
            }
        ]
    )->label('Status Kegiatan') ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>