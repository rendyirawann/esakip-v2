<?php

use yii\helpers\Html;
use yii\helpers\Url;
?>
<div class="title">
    <img src="<?= Url::base(true) ?>/lightapp/assets/images/bappeda.png" alt="logo image" height="60" width="60" class="logo-lg" />
    <h5 class="tbdata">
        <center>Rencana Kinerja Tahunan - <?= Html::encode(ucwords(strtolower($nama_skpd))) ?><br>Periode <?= $selectedPeriodValue ?></center>
    </h5>
</div>
<hr>
<br>
<table class='tblRenstra'>
    <thead>
        <tr>
            <td rowspan='2' width='25px' class='header'>No</td>
            <td rowspan='2' width='150px' class='header'>Sasaran Renstra</td>
            <td rowspan='2' width='30px' class='header'></td>
            <td rowspan='2' width='150px' class='header'>Indikator Kinerja</td>
            <td rowspan='2' width='80px' class='header'>Satuan</td>
            <td class='header'>Target Tahun</td>
        </tr>
        <tr>
            <td width='80px' class='header'><?= $selectedPeriodValue ?></td>
        </tr>
    </thead>
    <tbody>
        <?php
        $currentNumber = 0;
        $lastSasaran = '';
        foreach ($sasaranRenstra as $index => $item):
            $indikators = $item->indikatorSasaran;
            $sasaranCode = $item->uraian_sasaranrenstra;

            if ($lastSasaran !== $sasaranCode) {
                $currentNumber++;
                $lastSasaran = $sasaranCode;
                $indicatorIndex = 1;
            }

            if (!empty($indikators)):
                foreach ($indikators as $i => $indikator):
        ?>
                    <tr>
                        <?php if ($i === 0): ?>
                            <td style='text-align:center;' rowspan="<?= count($indikators) ?>"><?= $index + 1 ?></td>
                            <td rowspan="<?= count($indikators) ?>"><?= Html::encode(strip_tags($item->uraian_sasaranrenstra)) ?></td>
                        <?php endif; ?>

                        <td style='text-align:center;'><?= $currentNumber . '.' . $indicatorIndex++ ?></td>
                        <td><?= Html::encode(strip_tags($indikator->uraian_indikatorsasaranrenstra)) ?></td>
                        <td style='text-align:center;'><?= Html::encode(strip_tags($indikator->indikatorsasaranrenstra_satuan)) ?></td>
                        <td style='text-align:center;'><?= Html::encode(strip_tags($indikator->indikatorsasaranrenstra_target)) ?></td>
                    </tr>
        <?php
                endforeach;
            endif;
        endforeach;
        ?>
    </tbody>
</table>