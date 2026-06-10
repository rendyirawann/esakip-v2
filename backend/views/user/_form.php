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
use backend\models\UserGroup;
use backend\models\SakipSkpd;
use backend\models\SakipPegawaibappeda;

/** @var yii\web\View $this */
/** @var backend\models\UserGroup $model */
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
        $('form#user').off('submit').on('submit', function(e) {
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

<div class="user-form">

    <?php
    $form = ActiveForm::begin([
        'id' => 'user',
        'options' => ['enctype' => 'multipart/form-data'],
    ]);
    ?>

    <!-- Radio Buttons -->
    <div class="form-group">
        <label>Pilih Role</label>
        <div>
            <?= Html::radio('user_role', false, ['value' => 'pegawai', 'id' => 'role-pegawai']) ?> <label for="role-pegawai">Pegawai</label>
            <?= Html::radio('user_role', false, ['value' => 'opd', 'id' => 'role-opd']) ?> <label for="role-opd">OPD</label>
            <?= Html::radio('user_role', false, ['value' => 'admin', 'id' => 'role-admin']) ?> <label for="role-admin">Admin</label>
        </div>
    </div>

    <!-- Form (Hidden by Default) -->
    <div id="form-wrapper" style="display: none;">
        <?= $form->field($model, 'username')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'password_hash')->passwordInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'nama_user')->textarea(['rows' => 6]) ?>

        <?= $form->field($model, 'refskpd_id')->dropDownList(
            ArrayHelper::map(SakipSkpd::find()->orderBy(['refskpd_id' => SORT_ASC])->all(), 'refskpd_id', 'nama_skpd'),
            ['prompt' => 'Pilih SKPD User']
        )->label('SKPD User') ?>

        <?= $form->field($model, 'kode_group')->dropDownList(
            ArrayHelper::map(UserGroup::find()->orderBy(['kode_group' => SORT_ASC])->all(), 'kode_group', 'nama_group'),
            ['prompt' => 'Pilih Group User']
        )->label('Group User') ?>

        <div id="pegawai-field">
            <?= $form->field($model, 'refpegawai_id')->dropDownList(
                ArrayHelper::map(SakipPegawaibappeda::find()->orderBy(['refpegawai_id' => SORT_ASC])->all(), 'refpegawai_id', 'nama_pegawai'),
                ['prompt' => 'Pilih Pegawai Bappeda']
            )->label('Pegawai Bappeda') ?>
        </div>

        <?= $form->field($model, 'status')->hiddenInput(['value' => \backend\models\User::STATUS_ACTIVE])->label(false) ?>

        <?= $form->field($model, 'created_at')->hiddenInput(['value' => time()])->label(false) ?>

        <?= $form->field($model, 'updated_at')->hiddenInput(['value' => time()])->label(false) ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

    <!-- JavaScript for Showing/Hiding Form -->
    <?php
    $this->registerJs(<<<JS
    $(document).ready(function() {
        // Handle role selection
        $('input[name="user_role"]').change(function() {
            const role = $(this).val();
            if (role) {
                $('#form-wrapper').show();
                if (role === 'pegawai') {
                    $('#pegawai-field').show();
                } else if (role === 'opd') {
                    $('#pegawai-field').hide();
                } else if (role === 'admin') {
                    $('#pegawai-field').show();
                }
            }
        });
    });
JS);
    ?>



</div>