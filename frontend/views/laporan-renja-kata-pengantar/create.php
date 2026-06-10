<?php

use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var frontend\models\LaporanRenjaKataPengantar $model */

$this->title = 'Create Laporan Renja Kata Pengantar';
$this->params['breadcrumbs'][] = ['label' => 'Laporan Renja Kata Pengantars', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="pc-container">
    <div class="pc-content">
        <!-- [ breadcrumb ] start -->
        <div class="page-header">
            <div class="page-block">
                <div class="row align-items-center">
                    <div class="col-md-12">
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?= Url::to(['/buku-laporan/index']) ?>">Dashboard Buku Laporan</a></li>
                            <li class="breadcrumb-item" aria-current="page">Laporan Renja Kata Pengantar</li>
                        </ul>
                    </div>
                    <div class="col-md-12">
                        <div class="page-header-title">
                            <h2 class="mb-0">Data Laporan Renja Kata Pengantar</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- [ breadcrumb ] end -->


        <!-- [ Main Content ] start -->
        <div class="row">
            <!-- Base style - Hover table start -->
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Data Laporan Renja Kata Pengantar</h5>
                        <small>List Data</small>
                    </div>
                    <div class="card-body">
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

                        <?= $this->render('_form', [
                            'model' => $model,
                        ]) ?>

                    </div>
                </div>
            </div>
            <!-- Base style - Hover table end -->
        </div>
        <!-- [ Main Content ] end -->
    </div>
</div>