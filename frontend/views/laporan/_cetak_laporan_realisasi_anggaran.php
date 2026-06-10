<?php

use yii\helpers\Html;
use yii\helpers\Url;
?>
<div class="title">
    <img src="<?= Url::base(true) ?>/lightapp/assets/images/bappeda.png" alt="logo image" height="60" width="60" class="logo-lg" />
    <h5 class="tbdata">
        <center>Pagu dan Realisasi Anggaran - <?= Html::encode(ucwords(strtolower($nama_skpd))) ?><br>Periode <?= $selectedPeriodValue ?></center>
    </h5>
</div>
<hr>
<br>
<h3 class="tbdata">
    <center><b>Pagu dan Realisasi Anggaran Tahun <?= $selectedPeriodValue ?> <?= Html::encode(ucwords(strtolower($nama_skpd))) ?></b></center>
</h3>
<table width="100%" class="tbdata">
    <thead>
        <tr>
            <th rowspan="2" width="5%">No</th>
            <th rowspan="2" width="25%">Program</th>
            <th rowspan="2" width="15%">Pagu Anggaran Tahun <?= $selectedPeriodValue ?></th>
            <th colspan="2">Triwulan 1</th>
            <th colspan="2">Triwulan 2</th>
            <th colspan="2">Triwulan 3</th>
            <th colspan="2">Triwulan 4</th>
        </tr>
        <tr>
            <th>Penyerapan</th>
            <th>Realisasi (%)</th>
            <th>Penyerapan</th>
            <th>Realisasi (%)</th>
            <th>Penyerapan</th>
            <th>Realisasi (%)</th>
            <th>Penyerapan</th>
            <th>Realisasi (%)</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $no = 1;
        foreach ($data as $refsasaranrenstra_id => $entry): ?>
            <tr style="background-color: #f2f2f2;">
                <td colspan="11"><b>Sasaran: <?= Html::encode($entry['uraian_sasaran']) ?></b></td>
            </tr>
            <?php foreach ($entry['programs'] as $programId => $program): ?>
                <tr>
                    <td class="tengah"><?= $no++ ?></td>
                    <td><?= Html::encode($program['nama_program']) ?></td>
                    <td class="kanan">Rp. <?= Html::encode(number_format((float) ($program['anggaran_pk_p'] ?? 0), 2, ',', '.')) ?></td>

                    <?php for ($i = 0; $i < 4; $i++): ?>
                        <td class="kanan">
                            Rp. <?= isset($program['quarterly'][$i]) ? Html::encode(number_format((float) $program['quarterly'][$i]['triwulan_penyerapan_anggaran'], 2, ',', '.')) : '0.00' ?>
                        </td>
                        <td class="tengah">
                            <?php 
                                $penyerapan = isset($program['quarterly'][$i]) ? (float)$program['quarterly'][$i]['triwulan_penyerapan_anggaran'] : 0;
                                $pagu = (float)($program['anggaran_pk_p'] ?? 0);
                                $persen = ($pagu > 0) ? ($penyerapan / $pagu) * 100 : 0;
                                echo Html::encode(number_format($persen, 2, ',', '.'));
                            ?>
                        </td>
                    <?php endfor; ?>
                </tr>
            <?php endforeach; ?>
            <tr style="font-weight: bold; background-color: #e3e3e3;">
                <td colspan="2" class="kanan"><strong>Total Anggaran:</strong></td>
                <td class="kanan">Rp. <?= Html::encode(number_format($entry['total_anggaran'], 2, ',', '.')) ?></td>
                <?php for ($i = 0; $i < 4; $i++): ?>
                    <td class="kanan">
                        Rp. <?= Html::encode(number_format((float) ($entry['total_quarterly_penyerapan_anggaran'][$i] ?? 0), 2, ',', '.')) ?>
                    </td>
                    <td class="tengah">
                        <?php 
                            $penyerapanTotal = (float)($entry['total_quarterly_penyerapan_anggaran'][$i] ?? 0);
                            $paguTotal = (float)($entry['total_anggaran'] ?? 0);
                            $persenTotal = ($paguTotal > 0) ? ($penyerapanTotal / $paguTotal) * 100 : 0;
                            echo Html::encode(number_format($persenTotal, 2, ',', '.'));
                        ?>
                    </td>
                <?php endfor; ?>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>