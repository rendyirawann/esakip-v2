<?php

use yii\helpers\Html;
use yii\helpers\Url;
?>
<div class="title">
    <img src="<?= Url::base(true) ?>/lightapp/assets/images/bappeda.png" alt="logo image" height="60" width="60" class="logo-lg" />
    <h5 class="tbdata">
        <center>
            Analisis Pencapaian <?= Html::encode(ucwords(strtolower($nama_skpd))) ?><br>
            Tahun <?= Html::encode($selectedPeriodValue) ?> - Triwulan <?= Html::encode($selectedTriwulanId) ?><br>
            Sasaran: <?= Html::encode($selectedSasaranRenstraUraian) ?>
        </center>
    </h5>
</div>
<hr><br>
<table class='tbdata' width="100%">
    <thead>
        <tr>
            <th rowspan="2">No</th>
            <th rowspan="2">Indikator Kinerja Utama</th>
            <th rowspan="2">Satuan</th>
            <th colspan="2">Triwulan <?= $selectedTriwulanId ?></th>
            <th rowspan="2">%</th>
        </tr>
        <tr>
            <th>Target</th>
            <th>Realisasi</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($indikators as $index => $sasaran): ?>
            <tr>
                <td style='text-align:center;'><?= $index + 1 ?></td>
                <td><?= Html::encode($sasaran->refIndikatorsasaranrenstra->uraian_indikatorsasaranrenstra) ?></td>
                <td><?= Html::encode($sasaran->refIndikatorsasaranrenstra->indikatorsasaranrenstra_satuan) ?></td>
                <td><?= Html::encode($sasaran->triwulan_target_rkt) ?></td>
                <td><?= Html::encode($sasaran->triwulan_realisasi) ?></td>
                <td><?= Html::encode($sasaran->triwulan_capaian) ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>