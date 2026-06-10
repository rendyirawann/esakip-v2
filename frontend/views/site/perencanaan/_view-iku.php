<style>
    #loadingOverlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        /* semi-transparent background */
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        /* Make sure it overlays above everything */
    }

    /*  */
    .tbdata {
        border-collapse: collapse;
        font-family: 'Public Sans', sans-serif;
        /* Menggunakan font Public Sans */
        font-size: 11px;
    }

    .title {
        border-collapse: collapse;
        font-family: 'Public Sans', sans-serif;
        /* Menggunakan font Public Sans */
        font-size: 11px;
        text-align: center;
    }

    .tbdata th {
        height: 40px;
        background: rgb(0, 160, 221);
        text-align: center;
        border: 1px solid #cfcfcf;
        color: #ffffff;
        padding: 2px;
    }

    .tbdata td {
        padding: 2px;
        vertical-align: top;
    }

    .tebal {
        font-weight: bold;
    }

    .tblRenstra {
        width: 100%;
        font-family: 'Public Sans', sans-serif;
        border-collapse: collapse;
        font-size: 11px;
    }

    .tblRenstra td {
        padding: 2px;
        border: 1px solid #f2f2f2;
    }

    .tblRenstra .header {
        text-align: center;
        vertical-align: middle;
        font-weight: bold;
        background: #03b0e2;
        color: #ffffff;
    }

    .trO {
        background: #f2f2f2;
    }

    .trE {
        background: white;
    }

    .tblAtas {
        margin-left: 30px;
        font-family: 'Public Sans', sans-serif;
        border-collapse: collapse;
        font-size: 11px;
    }

    thead {
        display: table-header-group;
    }
</style>
<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

?>
<div class="title">
    <img src="<?= Url::base(true) ?>/lightapp/assets/images/bappeda.png" alt="logo image" height="60" width="60" class="logo-lg" />
    <h5 class="tbdata">
        <center>Rencana Kinerja Utama - <?= Html::encode(ucwords(strtolower($nama_skpd))) ?><br>Periode <?= $selectedPeriodValue ?></center>
    </h5>
</div>
<hr>
<br>
<table border=0 cellpadding=1 cellspacing=1 border=0 class="tbdata" width="100%">
    <tr>
        <td>
            <table class='tblRenstra'>
                <thead>
                    <tr>
                        <td width='25px' class='header'>No</td>
                        <td width='150px' class='header'>Indikator Kinerja Utama</td>
                        <td width='150px' class='header'>Formulasi Sasaran Renstra</td>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($sasaranRenstra as $index => $item): ?>
                        <tr>
                            <td style='text-align:center;'><?= $index + 1 ?></td>
                            <td><?= Html::encode(strip_tags($item['indikator']->uraian_indikatorsasaranrenstra)) ?></td>
                            <td><?= Html::encode(strip_tags($item['formulasi'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($sasaranRenstra)): ?>
                        <tr>
                            <td colspan="3" class="text-center">Tidak ada indikator</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </td>
    </tr>
</table>