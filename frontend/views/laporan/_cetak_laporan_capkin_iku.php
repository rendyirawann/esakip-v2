<?php

use yii\helpers\Html;
use yii\helpers\Url;
use frontend\models\SakipIndikatorsasaranrenstraTriwulan;
use yii\helpers\ArrayHelper;

?>
<div class="title">
    <img src="<?= Url::base(true) ?>/lightapp/assets/images/bappeda.png" alt="logo image" height="60" width="60" class="logo-lg" />
    <h5 class="tbdata">
        <center>Capaian Kinerja Indikator Kinerja Utama - <?= Html::encode(ucwords(strtolower($nama_skpd))) ?><br>Periode <?= $selectedPeriodValue ?></center>
    </h5>
</div>
<hr>
<br>
<table width="100%" class="tbdata">
    <thead>
        <tr>
            <th rowspan="2">Nomor Renstra</th>
            <th rowspan="2">Sasaran Renstra</th>
            <th rowspan="2">Nomor Indikator</th>
            <th rowspan="2">Indikator Sasaran Renstra</th>
            <th rowspan="2">Satuan</th>
            <th rowspan="2">Target Tahun</th>
            <th colspan="5">Rincian Capaian</th>
        </tr>
        <tr>
            <th>Triwulan</th>
            <th>Target</th>
            <th>Realisasi</th>
            <th>Capaian %</th>
            <th>Keterangan</th>
        </tr>
    </thead>
    <tbody>
        <?php
        // 1. Kelompokkan indikator berdasarkan Sasaran Renstra
        $groupedIndikators = ArrayHelper::index($indikators, null, 'refsasaranrenstra_id');
        $renstraNomor = 0;

        foreach ($groupedIndikators as $sasaranId => $indicatorsInGroup):
            $renstraNomor++;

            // 2. Hitung total rowspan untuk grup sasaran ini
            $totalGroupRowspan = 0;
            foreach ($indicatorsInGroup as $indikator) {
                $triwulanCount = SakipIndikatorsasaranrenstraTriwulan::find()->where(['refindikatorsasaranrenstra_id' => $indikator->refindikatorsasaranrenstra_id])->count();
                $totalGroupRowspan += $triwulanCount + 1; // +1 untuk baris "Kondisi Akhir F"
            }

            $firstIndicatorInGroup = true;
            $indikatorNomor = 0;
            foreach ($indicatorsInGroup as $indikator):
                $indikatorNomor++;

                $triwulanData = SakipIndikatorsasaranrenstraTriwulan::find()
                    ->where([
                        'refindikatorsasaranrenstra_id' => $indikator->refindikatorsasaranrenstra_id,
                        'refskpd_id' => $refskpd_id
                    ])
                    ->orderBy(['reftriwulan_id' => SORT_ASC])
                    ->all();

                $indicatorRowspan = count($triwulanData) + 1;
        ?>
                <tr>
                    <?php if ($firstIndicatorInGroup): ?>
                        <td rowspan="<?= $totalGroupRowspan ?>" class="tengah"><?= $renstraNomor ?></td>
                        <td rowspan="<?= $totalGroupRowspan ?>"><?= Html::encode($indikator->refSasaranrenstra->uraian_sasaranrenstra) ?></td>
                    <?php endif; ?>

                    <td rowspan="<?= $indicatorRowspan ?>" class="tengah"><?= $renstraNomor . '.' . $indikatorNomor ?></td>
                    <td rowspan="<?= $indicatorRowspan ?>"><?= Html::encode($indikator->uraian_indikatorsasaranrenstra) ?></td>
                    <td rowspan="<?= $indicatorRowspan ?>" class="tengah"><?= Html::encode($indikator->indikatorsasaranrenstra_satuan) ?></td>
                    <td rowspan="<?= $indicatorRowspan ?>" class="tengah"><?= Html::encode($indikator->indikatorsasaranrenstra_target) ?></td>

                    <td class="tengah">Kondisi Akhir F</td>
                    <td class="tengah"></td>
                    <td class="tengah"><?= Html::encode($indikator->realisasi) ?></td>
                    <?php
                    $capaian = $indikator->capaian;
                    $capaianColor = is_numeric($capaian) ? ($capaian >= 100 ? '#006600' : ($capaian >= 70 ? '#ff6600' : '#ff0404')) : '#fff';
                    $keterangan = is_numeric($capaian) ? ($capaian >= 100 ? 'Tercapai' : ($capaian >= 70 ? 'Tidak Mencapai 100%' : 'Tidak Mencapai 70%')) : 'Tidak Ada Target';
                    ?>
                    <td class="tengah" style="background-color:<?= $capaianColor ?>; color:white;"><?= Html::encode($capaian) ?></td>
                    <td><?= $keterangan ?></td>
                </tr>

                <?php foreach ($triwulanData as $triwulan): ?>
                    <tr>
                        <td class="tengah">Triwulan <?= $triwulan->reftriwulan_id ?></td>
                        <td class="tengah"><?= Html::encode($triwulan->triwulan_target_pk_p) ?></td>
                        <td class="tengah"><?= Html::encode($triwulan->triwulan_realisasi) ?></td>
                        <?php
                        $capaian = $triwulan->triwulan_capaian;
                        $capaianColor = is_numeric($capaian) ? ($capaian >= 100 ? '#006600' : ($capaian >= 70 ? '#ff6600' : '#ff0404')) : '#fff';
                        $keterangan = is_numeric($capaian) ? ($capaian >= 100 ? 'Tercapai' : ($capaian >= 70 ? 'Tidak Mencapai 100%' : 'Tidak Mencapai 70%')) : 'Tidak Ada Target';
                        ?>
                        <td class="tengah" style="background-color: <?= $capaianColor ?>; color:white;"><?= Html::encode($capaian) ?></td>
                        <td><?= $keterangan ?></td>
                    </tr>
                <?php endforeach; ?>
        <?php
                $firstIndicatorInGroup = false;
            endforeach;
        endforeach;
        ?>
    </tbody>
</table>

<br>
<h3>Keterangan</h3>
<table class="keterangan">
</table>