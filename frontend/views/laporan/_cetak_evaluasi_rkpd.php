<?php

use yii\helpers\Html;
?>


<h3 style="text-align: center;">Laporan Evaluasi Hasil RKPD Terhadap RPJMD</h3>
<h4 style="text-align: center;"><?= Html::encode($nama_skpd) ?></h4>
<h5 style="text-align: center;">Periode <?= $selectedPeriodValue ?></h5>
<br>

<table class="report-table">
    <thead class="table-dark">
        <tr>
            <th rowspan="3" class="align-middle">#</th>
            <th rowspan="3" class="align-middle">Kode</th>
            <th rowspan="3" class="align-middle">Program / Kegiatan / Sub Kegiatan</th>
            <th rowspan="3" class="align-middle">Indikator</th>
            <th rowspan="3" class="align-middle">Satuan</th>
            <th rowspan="3" class="align-middle">Target</th>
            <th rowspan="3" class="align-middle">Anggaran</th>
            <th colspan="8">Realisasi Kinerja Pada Triwulan</th>
            <th colspan="2" rowspan="2">Total Realisasi dan Anggaran Tiap Triwulan</th>
        </tr>
        <tr>
            <th colspan="2">I</th>
            <th colspan="2">II</th>
            <th colspan="2">III</th>
            <th colspan="2">IV</th>
        </tr>
        <tr>
            <th>Realisasi</th>
            <th>Penyerapan Anggaran</th>
            <th>Realisasi</th>
            <th>Penyerapan Anggaran</th>
            <th>Realisasi</th>
            <th>Penyerapan Anggaran</th>
            <th>Realisasi</th>
            <th>Penyerapan Anggaran</th>
            <th>Total Realisasi</th>
            <th>Total Penyerapan Anggaran</th>
        </tr>

    </thead>
    <tbody>
        <?php if (empty($laporanData)): ?>
            <tr>
                <td colspan="19" class="text-center p-4"><i>Tidak ada data untuk ditampilkan.</i></td>
            </tr>
        <?php else: ?>
            <?php foreach ($laporanData as $s_index => $sasaran): ?>
                <tr class="table-primary text-start">
                    <td class="text-center align-middle">
                        <button class="btn btn-sm btn-link p-0 text-white" type="button" data-bs-toggle="collapse" data-bs-target=".program-for-sasaran-<?= $s_index ?>">
                            <i class="fas fa-plus accordion-icon"></i>
                        </button>
                    </td>
                    <td colspan="18" class="fw-bold align-middle">
                        SASARAN: <?= Html::encode($sasaran['uraian_sasaranrenstra']) ?>
                    </td>
                </tr>

                <?php if (!empty($sasaran['programs'])): ?>
                    <?php foreach ($sasaran['programs'] as $p_index => $program): ?>

                        <tr class="collapse program-for-sasaran-<?= $s_index ?> table-light text-start">
                            <td class="text-center align-middle"></td>
                            <td class="align-middle ps-4">
                                <button class="btn btn-sm btn-link p-0" type="button" data-bs-toggle="collapse" data-bs-target=".kegiatan-for-program-<?= $s_index ?>-<?= $p_index ?>">
                                    <i class="fas fa-plus accordion-icon"></i>
                                </button>
                                <?= Html::encode($program['kode_program']) ?>
                            </td>
                            <td class="fw-bold align-middle"><?= Html::encode($program['nama_program']) ?></td>
                            <td class="align-middle"><?= Html::encode($program['uraian_indikator']) ?></td>
                            <td class="text-center align-middle"><?= Html::encode($program['satuan']) ?></td>
                            <td class="text-center align-middle"><?= Html::encode($program['target']) ?></td>
                            <td class="text-end fw-bold align-middle"><?= Yii::$app->formatter->asDecimal($program['total_anggaran'], 0) ?></td>

                            <td class="text-center align-middle"><?= Html::encode($program['realisasi'][1]) ?></td>
                            <td class="text-end align-middle"><?= Yii::$app->formatter->asDecimal($program['triwulan_penyerapan_anggaran'][1], 0) ?></td>
                            <td class="text-center align-middle"><?= Html::encode($program['realisasi'][2]) ?></td>
                            <td class="text-end align-middle"><?= Yii::$app->formatter->asDecimal($program['triwulan_penyerapan_anggaran'][2], 0) ?></td>
                            <td class="text-center align-middle"><?= Html::encode($program['realisasi'][3]) ?></td>
                            <td class="text-end align-middle"><?= Yii::$app->formatter->asDecimal($program['triwulan_penyerapan_anggaran'][3], 0) ?></td>
                            <td class="text-center align-middle"><?= Html::encode($program['realisasi'][4]) ?></td>
                            <td class="text-end align-middle"><?= Yii::$app->formatter->asDecimal($program['triwulan_penyerapan_anggaran'][4], 0) ?></td>
                            <td class="text-center fw-bold align-middle"><?= Html::encode($program['total_realisasi']) ?></td>
                            <td class="text-end fw-bold align-middle"><?= Yii::$app->formatter->asDecimal($program['total_penyerapan'], 0) ?></td>
                        </tr>

                        <?php if (!empty($program['kegiatans'])): ?>
                            <?php foreach ($program['kegiatans'] as $k_index => $kegiatan): ?>

                                <tr class="collapse kegiatan-for-program-<?= $s_index ?>-<?= $p_index ?> text-start" style="background-color: #f8f9fa;">
                                    <td class="text-center align-middle"></td>
                                    <td class="ps-5 align-middle">
                                        <button class="btn btn-sm btn-link py-0 px-1" type="button" data-bs-toggle="collapse" data-bs-target=".subkegiatan-for-kegiatan-<?= $s_index ?>-<?= $p_index ?>-<?= $k_index ?>">
                                            <i class="fas fa-plus accordion-icon"></i>
                                        </button>
                                        <?= Html::encode($kegiatan['kode_kegiatan']) ?>
                                    </td>
                                    <td class="ps-4 align-middle"><?= Html::encode($kegiatan['nama_kegiatan']) ?></td>
                                    <td class="align-middle"><?= Html::encode($kegiatan['uraian_indikator']) ?></td>
                                    <td class="text-center align-middle"><?= Html::encode($kegiatan['satuan']) ?></td>
                                    <td class="text-center align-middle"><?= Html::encode($kegiatan['target']) ?></td>
                                    <td class="text-end align-middle"><?= Yii::$app->formatter->asDecimal($kegiatan['total_anggaran'], 0) ?></td>

                                    <td class="text-center align-middle"><?= Html::encode($kegiatan['realisasi'][1]) ?></td>
                                    <td class="text-end align-middle"><?= Yii::$app->formatter->asDecimal($kegiatan['triwulan_penyerapan_anggaran'][1], 0) ?></td>
                                    <td class="text-center align-middle"><?= Html::encode($kegiatan['realisasi'][2]) ?></td>
                                    <td class="text-end align-middle"><?= Yii::$app->formatter->asDecimal($kegiatan['triwulan_penyerapan_anggaran'][2], 0) ?></td>
                                    <td class="text-center align-middle"><?= Html::encode($kegiatan['realisasi'][3]) ?></td>
                                    <td class="text-end align-middle"><?= Yii::$app->formatter->asDecimal($kegiatan['triwulan_penyerapan_anggaran'][3], 0) ?></td>
                                    <td class="text-center align-middle"><?= Html::encode($kegiatan['realisasi'][4]) ?></td>
                                    <td class="text-end align-middle"><?= Yii::$app->formatter->asDecimal($kegiatan['triwulan_penyerapan_anggaran'][4], 0) ?></td>
                                    <td class="text-center fw-bold align-middle"><?= Html::encode($kegiatan['total_realisasi']) ?></td>
                                    <td class="text-end fw-bold align-middle"><?= Yii::$app->formatter->asDecimal($kegiatan['total_penyerapan'], 0) ?></td>
                                </tr>

                                <?php if (!empty($kegiatan['subkegiatans'])): ?>
                                    <?php foreach ($kegiatan['subkegiatans'] as $subkegiatan): ?>

                                        <tr class="collapse subkegiatan-for-kegiatan-<?= $s_index ?>-<?= $p_index ?>-<?= $k_index ?> text-start">
                                            <td></td>
                                            <td class="ps-5 align-middle"><?= Html::encode($subkegiatan['kode_subkegiatan']) ?></td>
                                            <td class="ps-5 align-middle"><?= Html::encode($subkegiatan['nama_subkegiatan']) ?></td>
                                            <td class="align-middle"><?= Html::encode($subkegiatan['uraian_indikator']) ?></td>
                                            <td class="text-center align-middle"><?= Html::encode($subkegiatan['satuan']) ?></td>
                                            <td class="text-center align-middle"><?= Html::encode($subkegiatan['target']) ?></td>
                                            <td class="text-end align-middle"><?= Yii::$app->formatter->asDecimal($subkegiatan['total_anggaran'], 0) ?></td>

                                            <td class="text-center align-middle"><?= Html::encode($subkegiatan['realisasi'][1]) ?></td>
                                            <td class="text-end align-middle"><?= Yii::$app->formatter->asDecimal($subkegiatan['triwulan_penyerapan_anggaran'][1], 0) ?></td>
                                            <td class="text-center align-middle"><?= Html::encode($subkegiatan['realisasi'][2]) ?></td>
                                            <td class="text-end align-middle"><?= Yii::$app->formatter->asDecimal($subkegiatan['triwulan_penyerapan_anggaran'][2], 0) ?></td>
                                            <td class="text-center align-middle"><?= Html::encode($subkegiatan['realisasi'][3]) ?></td>
                                            <td class="text-end align-middle"><?= Yii::$app->formatter->asDecimal($subkegiatan['triwulan_penyerapan_anggaran'][3], 0) ?></td>
                                            <td class="text-center align-middle"><?= Html::encode($subkegiatan['realisasi'][4]) ?></td>
                                            <td class="text-end align-middle"><?= Yii::$app->formatter->asDecimal($subkegiatan['triwulan_penyerapan_anggaran'][4], 0) ?></td>
                                            <td class="text-center fw-bold align-middle"><?= array_sum(array_filter($subkegiatan['realisasi'], 'is_numeric')) ?></td>
                                            <td class="text-end fw-bold align-middle"><?= Yii::$app->formatter->asDecimal(array_sum($subkegiatan['triwulan_penyerapan_anggaran']), 0) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>

                            <?php endforeach; ?>
                        <?php endif; ?>

                    <?php endforeach; ?>
                <?php endif; ?>

            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>


</table>