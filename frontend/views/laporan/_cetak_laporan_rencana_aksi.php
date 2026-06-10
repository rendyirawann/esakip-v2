<?php

use yii\helpers\Html;
use yii\helpers\Url;
?>
<div class="title">
    <img src="<?= Url::base(true) ?>/lightapp/assets/images/bappeda.png" alt="logo image" height="60" width="60" class="logo-lg" />
    <h5 class="tbdata">
        <center>Rencana Aksi - <?= Html::encode(ucwords(strtolower($nama_skpd))) ?><br>Periode <?= $selectedPeriodValue ?></center>
    </h5>
</div>
<hr>
<br>
<table width='100%' class='tbdata'>
    <thead>
        <tr>
            <th width='3%'>No</th>
            <th width='8%'>Program</th>
            <th width='8%'>Anggaran Program</th>
            <th width='8%'>Kegiatan</th>
            <th width='8%'>Anggaran Kegiatan</th>
            <th width='8%'>Sub Kegiatan</th>
            <th width='8%'>Anggaran Sub Kegiatan</th>
            <th width='8%'>Indikator Sub Kegiatan</th>
            <th width='8%'>Target Sub Kegiatan</th>
        </tr>
    </thead>
    <tbody>
        <?php $nomor = 1; ?>
        <?php foreach ($groupedSubkegiatan as $refcascadingkegiatan_id => $subkegiatanGroup): ?>
            <?php
            $rowspan = count($subkegiatanGroup);
            $firstItem = reset($subkegiatanGroup);
            $programName = $firstItem->refCascadingKegiatan->refCascadingProgram->refProgram->nama_program ?? '-';
            $programAnggaran = $anggaranPerProgram[$firstItem->refCascadingKegiatan->refCascadingProgram->refProgram->refprogram_id] ?? 0;
            $kegiatanName = $firstItem->refCascadingKegiatan->refKegiatan->nama_kegiatan ?? '-';
            $anggaranKegiatan = $anggaranPerKegiatan[$refcascadingkegiatan_id] ?? 0;
            $isFirstRow = true;
            ?>
            <?php foreach ($subkegiatanGroup as $item): ?>
                <tr>
                    <?php if ($isFirstRow): ?>
                        <td class="tengah" rowspan="<?= $rowspan ?>"><?= $nomor++; ?></td>
                        <td rowspan="<?= $rowspan ?>"><?= Html::encode($programName) ?></td>
                        <td rowspan="<?= $rowspan ?>"><?= number_format($programAnggaran, 0, ',', '.') ?></td>
                        <td rowspan="<?= $rowspan ?>"><?= Html::encode($kegiatanName) ?></td>
                        <td rowspan="<?= $rowspan ?>"><?= number_format($anggaranKegiatan, 0, ',', '.') ?></td>
                    <?php endif; ?>
                    <td><?= $item->refSubkegiatan ? Html::encode($item->refSubkegiatan->nama_subkegiatan) : '-' ?></td>
                    <td><?= $item->subkegiatan_anggaran ? number_format($item->subkegiatan_anggaran, 0, ',', '.') : '-' ?></td>
                    <td><?= $item->uraian_indikatorsubkegiatan ? Html::encode($item->uraian_indikatorsubkegiatan) : '-' ?></td>
                    <td>
                        <?php
                        if ($item->indikatorTriwulan) {
                            foreach ($item->indikatorTriwulan as $triwulan) {
                                echo "Trw ({$triwulan->reftriwulan_id}) = {$triwulan->triwulan_target_rkt} <br>";
                            }
                        } else {
                            echo '-';
                        }
                        ?>
                    </td>
                </tr>
            <?php
                $isFirstRow = false;
            endforeach; ?>
        <?php endforeach; ?>
    </tbody>
</table>