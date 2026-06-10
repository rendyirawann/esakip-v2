<?php

use yii\helpers\Html;
use yii\helpers\Url;
?>
<div class="title">
    <img src="<?= Url::base(true) ?>/lightapp/assets/images/bappeda.png" alt="logo image" height="60" width="60" class="logo-lg" />
    <h5 class="tbdata">
        <center>Indikator Kinerja Utama - <?= Html::encode(ucwords(strtolower($nama_skpd))) ?><br>Periode <?= $selectedPeriodValue ?></center>
    </h5>
</div>
<hr>
<br>
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
                <td colspan="3" style="text-align: center;">Tidak ada data.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>