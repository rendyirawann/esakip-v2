<?php

use yii\helpers\Html;
use yii\helpers\Url;
?>
<div class="title">
    <img src="<?= Url::base(true) ?>/lightapp/assets/images/bappeda.png" alt="logo image" height="60" width="60" class="logo-lg" />
    <h5 class="tbdata">
        <center>Tingkat Efisiensi & Efektifitas Kinerja Terhadap Realisasi Anggaran - <?= Html::encode(ucwords(strtolower($nama_skpd))) ?><br>Periode <?= $selectedPeriodValue ?></center>
    </h5>
</div>
<hr><br>
<table class='tbdata' width="100%">
    <thead>
        <tr>
            <th rowspan='2' class='head1'>No</th>
            <th rowspan='2' class='head1'>Sasaran</th>
            <th rowspan='2' class='head1'>Kode</th>
            <th rowspan='2' class='head1'>Indikator</th>
            <th rowspan='2' class='head1'>Satuan</th>
            <th colspan='3' class='head2'>Kinerja</th>
            <th colspan='4' class='head3'>Keuangan</th>
        </tr>
        <tr>
            <th class='head2a'>Target</th>
            <th class='head2a'>Realisasi</th>
            <th class='head2a'>(%)</th>
            <th class="head3a">Program</th>
            <th class='head3a'>Pagu</th>
            <th class='head3a'>Realisasi</th>
            <th class='head3a'>(%)</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $no = 1;
        foreach ($sasaranRenstra as $sasaran) {
            $sasaranRowSpan = 0;
            if (!empty($sasaran->indikatorSasaran)) {
                foreach ($sasaran->indikatorSasaran as $indikator) {
                    $sasaranRowSpan += count($indikator->cascadingPrograms) > 0 ? count($indikator->cascadingPrograms) : 1;
                }
            } else {
                $sasaranRowSpan = 1;
            }

            $firstIndicator = true;
            $indicatorIndex = 1;
            if (!empty($sasaran->indikatorSasaran)) {
                foreach ($sasaran->indikatorSasaran as $indikator) {
                    $programCount = count($indikator->cascadingPrograms);
                    $indicatorRowSpan = $programCount > 0 ? $programCount : 1;
        ?>
                    <tr>
                        <?php if ($firstIndicator): ?>
                            <td rowspan="<?= $sasaranRowSpan ?>" class="tengah"><?= $no ?></td>
                            <td rowspan="<?= $sasaranRowSpan ?>"><?= Html::encode($sasaran->uraian_sasaranrenstra) ?></td>
                        <?php endif; ?>

                        <td rowspan="<?= $indicatorRowSpan ?>" class="tengah"><?= $no . '.' . $indicatorIndex ?></td>
                        <td rowspan="<?= $indicatorRowSpan ?>"><?= Html::encode($indikator->uraian_indikatorsasaranrenstra) ?></td>
                        <td rowspan="<?= $indicatorRowSpan ?>" class="tengah"><?= Html::encode($indikator->indikatorsasaranrenstra_satuan) ?></td>
                        <td rowspan="<?= $indicatorRowSpan ?>" class="tengah"><?= Html::encode($indikator->indikatorsasaranrenstra_target) ?></td>
                        <td rowspan="<?= $indicatorRowSpan ?>" class="tengah"><?= Html::encode($indikator->realisasi) ?></td>
                        <td rowspan="<?= $indicatorRowSpan ?>" class="tengah"><?= Html::encode($indikator->capaian) ?></td>

                        <?php
                        if (!empty($indikator->cascadingPrograms)) {
                            $firstProgram = true;
                            foreach ($indikator->cascadingPrograms as $cascadingProgram) {
                                if (!$firstProgram) echo "</tr><tr>";
                                $programData = $programDataMap[$cascadingProgram->refcascadingprogram_id] ?? null;
                        ?>
                                <td><?= Html::encode($cascadingProgram->refProgram->nama_program ?? '-') ?></td>
                                <td><?= 'Rp. ' . number_format($programData['totalPagu'] ?? 0, 0, ',', '.') ?></td>
                                <td><?= 'Rp. ' . number_format($programData['totalRealisasiAnggaran'] ?? 0, 0, ',', '.') ?></td>
                                <td><?= number_format($programData['totalCapaianAnggaran'] ?? 0, 2, ',', '.') ?></td>
                        <?php
                                $firstProgram = false;
                            }
                        } else {
                            echo "<td>Tidak ada program terkait</td><td>0</td><td>0</td><td>0</td>";
                        }
                        ?>
                    </tr>
        <?php
                    $firstIndicator = false;
                    $indicatorIndex++;
                }
            } else {
                // Baris jika tidak ada indikator
                echo "<tr>";
                echo "<td class='tengah'>$no</td>";
                echo "<td>" . Html::encode($sasaran->uraian_sasaranrenstra) . "</td>";
                echo "<td colspan='9' class='tengah'>Tidak ada indikator untuk sasaran ini</td>";
                echo "</tr>";
            }
            $no++;
        }
        ?>
    </tbody>
</table>