<?php

/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */
/** @var \common\models\LoginForm $model */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
use yii\helpers\Url;

$this->title = 'Login';
?>
<div class="auth-main v2">
    <div class="bg-overlay bg-dark"></div>
    <div class="auth-wrapper">
        <div class="auth-sidecontent">
            <div class="auth-sidefooter">
                <img src="<?= Url::base(true) ?>/lightapp/assets/images/bappeda.png" width="32px;" alt="images" />
                <hr class="mb-3 mt-4" />
                <div class="row">
                    <div class="col my-1">
                        <p class="m-0">eSakip. Sistem Akuntabilitas Kinerja Instansi Pemerintah &copy; <?= date('Y') ?> <a href="https://bappedalitbang.deliserdangkab.go.id" target="_blank">
                                Bappedalitbang</a></p>
                    </div>
                    <div class="col-auto my-1">
                        <!-- <ul class="list-inline footer-link mb-0">
                            <li class="list-inline-item"><a href="../index.html">Home</a></li>
                            <li class="list-inline-item"><a href="https://pcoded.gitbook.io/light-able/" target="_blank">Documentation</a></li>
                            <li class="list-inline-item"><a href="https://phoenixcoded.support-hub.io/" target="_blank">Support</a>
                            </li>
                        </ul> -->
                    </div>
                </div>
            </div>

        </div>
        <form class="auth-form" method="post" action="<?= Url::to(['/site/register']) ?>">
            <div class="card my-5 mx-3">
                <?php if (Yii::$app->session->hasFlash('success')) : ?>
                    <div class="alert alert-success">
                        <?= Yii::$app->session->getFlash('success') ?>
                    </div>
                <?php endif; ?>

                <?php if (Yii::$app->session->hasFlash('error')) : ?>
                    <div class="alert alert-danger">
                        <?= Yii::$app->session->getFlash('error') ?>
                    </div>
                <?php endif; ?>

                <div class="card-body">
                    <h4 class="f-w-500 mb-1">Register Aplikasi eSakip</h4>
                    <p class="mb-3">Register Akun Publik <a href="../pages/register-v2.html" class="link-primary ms-1"></a></p>
                    <?php $form = ActiveForm::begin(['id' => 'signup-form']); ?>

                    <?= $form->field($model, 'username')->textInput(['autofocus' => true])->label('Username') ?>

                    <?= $form->field($model, 'email')->textInput()->label('Email') ?>

                    <?= $form->field($model, 'no_hp')->textInput()->label('Nomor Handphone') ?>

                    <?= $form->field($model, 'password')->passwordInput()->label('Password') ?>

                    <div class="form-group">
                        <?= Html::submitButton('Buat Akun', ['class' => 'btn btn-az-primary btn-block', 'name' => 'signup-button']) ?>
                    </div>

                    <?php ActiveForm::end(); ?>
                    <div class="d-flex mt-1 justify-content-between align-items-center">
                        <!-- <div class="form-check">
                            <input class="form-check-input input-primary" type="checkbox" id="customCheckc1" checked="">
                            <label class="form-check-label text-muted" for="customCheckc1">Remember me</label>
                        </div> -->
                        <!-- <a href="../pages/forgot-password-v2.html">
                            <h6 class="text-secondary f-w-400 mb-0">Forgot Password?</h6>
                        </a> -->
                    </div>
                    <div class="saprator my-3">
                        <!-- <span>Or continue with</span> -->
                    </div>
                    <div class="text-center">
                        <ul class="list-inline mx-auto mt-3 mb-0">
                            <!-- <li class="list-inline-item">
                                <a href="https://www.facebook.com/" class="avtar avtar-s rounded-circle bg-facebook" target="_blank">
                                    <i class="fab fa-facebook-f text-white"></i>
                                </a>
                            </li>
                            <li class="list-inline-item">
                                <a href="https://twitter.com/" class="avtar avtar-s rounded-circle bg-twitter" target="_blank">
                                    <i class="fab fa-twitter text-white"></i>
                                </a>
                            </li>
                            <li class="list-inline-item">
                                <a href="https://myaccount.google.com/" class="avtar avtar-s rounded-circle bg-googleplus" target="_blank">
                                    <i class="fab fa-google text-white"></i>
                                </a>
                            </li> -->
                        </ul>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<!-- [ Main Content ] end -->