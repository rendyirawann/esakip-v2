<style>
    .card-header {
        height: 120px;
    }

    .position-relative {
        position: relative;
    }

    .arrow-icon {
        position: absolute;
        right: -10px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 1.5em;
        color: #555;
    }

    /* Kustomisasi khusus untuk panah bawah pada card ke-4 */
    .arrow-icon-bottom {
        position: absolute;
        bottom: -20px;
        /* Jarak dari bawah card */
        left: 50%;
        transform: translateX(-50%);
        /* Rotasi panah ke bawah */
        font-size: 1.5em;
        color: #555;
    }

    .arrow-icon-bottom-2 {
        position: absolute;
        bottom: -20px;
        /* Jarak dari bawah card */
        right: 50%;
        transform: translateX(-50%);
        /* Rotasi panah ke bawah */
        font-size: 1.5em;
        color: #555;
    }
</style>
<?php

use yii\helpers\Url;
use yii\helpers\Html;
use frontend\models\User;
use frontend\models\SakipSasaranrenstra;
use frontend\models\SakipIndikatorsasaranrenstra;
use frontend\models\SakipTujuanrenstra;
use frontend\models\SakipIndikatortujuanrenstra;
use frontend\models\SakipStrategi;
use frontend\models\SakipKebijakan;
use frontend\models\SakipCascadingprogram;
use frontend\models\SakipCascadingkegiatan;
use frontend\models\SakipCascadingsubkegiatan;

/** @var yii\web\View $this */

$this->title = 'Aplikasi SIMONA';

?>
<!-- [ Main Content ] start -->
<div class="pc-container">
    <div class="pc-content">
        <!-- [ breadcrumb ] start -->
        <div class="page-header">
            <div class="page-block">
                <div class="row align-items-center">
                    <div class="col-md-12">
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?= Url::to(['/site/index-simona']) ?>">Dashboard</a></li>
                            <li class="breadcrumb-item" aria-current="page">Home</li>
                        </ul>
                    </div>
                    <div class="col-md-12">
                        <div class="page-header-title">
                            <h2 class="mb-0">Home</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- [ breadcrumb ] end -->

        <!-- [ Main Content ] start -->
        <div class="row">
            <div class="col-lg-12 text-center">
                <h1>Dashboard Aplikasi SIMONA</h1>
                <img src="<?= Url::base(true) ?>/lightapp/assets/images/bappeda.png" alt="" width="auto">
                <h3>Bappedalitbang Deli Sedang</h3>
            </div>
        </div>
        <!--  -->

    </div>
    <!-- [ Main Content ] end -->
</div>
</div>
<!-- [ Main Content ] end -->