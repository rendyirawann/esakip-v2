<?php

use yii\helpers\Url;
use yii\helpers\Html;

/** @var yii\web\View $this */

$this->title = 'Aplikasi ESAKIP - Buku Laporan';

?>
<div class="pc-container">
    <div class="pc-content">
        <!-- [ breadcrumb ] start -->
        <div class="page-header">
            <div class="page-block">
                <div class="row align-items-center">
                    <div class="col-md-12">
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?= Url::to(['/site/index-esakip']) ?>">Home</a></li>
                            <li class="breadcrumb-item" aria-current="page">Dashboard Buku Laporan</li>
                        </ul>
                    </div>
                    <div class="col-md-12">
                        <div class="page-header-title">
                            <h2 class="mb-0">Dashboard Buku Laporan</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- [ breadcrumb ] end -->


        <div class="row">
            <!-- start row -->
            <div class="col-lg-12 text-center">
                <h1>Dashboard Aplikasi eSakip - Buku Laporan</h1>
                <img src="<?= Url::base(true) ?>/lightapp/assets/images/bappeda.png" alt="" width="auto">
                <h3>Bappedalitbang Deli Sedang</h3>
            </div>
            <!-- end row -->
        </div>


        <!-- [ Main Content ] end -->
    </div>
</div>