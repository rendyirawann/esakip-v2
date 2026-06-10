<?php

use yii\helpers\Html;
use yii\helpers\Url;

// Format tanggal jika ada, jika tidak gunakan tanggal hari ini
$reportDate = !empty($selected_date) ? Yii::$app->formatter->asDate($selected_date, 'long') : date('d F Y');
?>

<div id="halamanlaporan">
    <div class="isilaporan">
        <div style="text-align: center;">
            <img src="<?= Url::to('@web/lightapp/assets/images/bappeda.png', true) ?>" alt="logo" height="60" />
        </div>
        <h3>PEMERINTAH KABUPATEN DELI SERDANG</h3>
        <h4>PERNYATAAN PERJANJIAN KINERJA</h4>
        <h4><?= Html::encode(strtoupper($nama_skpd)) ?></h4>
        <h5>PERJANJIAN KINERJA TAHUN <?= Html::encode($selectedPeriodValue) ?></h5>

        <p>Dalam rangka mewujudkan manajemen pemerintahan yang efektif, transparan, dan akuntabel serta berorientasi pada hasil, kami yang bertanda tangan di bawah ini:</p>

        <table>
            <tr>
                <td width="125px">Nama</td>
                <td>: <?= Html::encode($skpdHead->kepala_skpd) ?></td>
            </tr>
            <tr>
                <td>Jabatan</td>
                <td>: <?= Html::encode($skpdHead->jabatan_kepala) ?></td>
            </tr>
        </table>
        <p>Selanjutnya disebut sebagai PIHAK PERTAMA</p>

        <table>
            <tr>
                <td width="125px">Nama</td>
                <td>: <?= Html::encode($leadership->nama_pimpinan ?? 'N/A') ?></td>
            </tr>
            <tr>
                <td>Jabatan</td>
                <td>: <?= Html::encode($leadership->jabatan_pimpinan ?? 'N/A') ?></td>
            </tr>
        </table>
        <p>Selaku atasan langsung pihak pertama, selanjutnya disebut sebagai PIHAK KEDUA</p>

        <p>Pihak pertama berjanji akan mewujudkan target kinerja yang seharusnya sesuai lampiran perjanjian ini, dalam rangka mencapai target kinerja jangka menengah seperti yang telah ditetapkan dalam dokumen perencanaan. Keberhasilan dan kegagalan pencapaian target kinerja tersebut menjadi tanggung jawab kami.</p>
        <p>Pihak kedua akan melakukan supervisi yang diperlukan serta akan melakukan evaluasi terhadap capaian kinerja dari perjanjian ini dan mengambil tindakan yang diperlukan dalam rangka pemberian penghargaan dan sanksi.</p>

        <table class="tblPihak">
            <tr>
                <td></td>
                <td>Lubuk Pakam, <?= $reportDate ?></td>
            </tr>
            <tr>
                <td>PIHAK KEDUA</td>
                <td>PIHAK PERTAMA</td>
            </tr>
            <tr>
                <td colspan="2" height="70px">&nbsp;</td>
            </tr>
            <tr>
                <td><?= Html::encode($leadership->nama_pimpinan ?? 'N/A') ?></td>
                <td><?= Html::encode($skpdHead->kepala_skpd) ?></td>
            </tr>
            <tr>
                <td></td>
                <td>NIP. <?= Html::encode($skpdHead->nip_kepala) ?></td>
            </tr>
        </table>
    </div>
</div>

<div style="page-break-after: always;"></div>

<div id="halamanlaporan">
    <div class="isilaporan">
        <h5>PERJANJIAN KINERJA</h5>
        <table style="font-weight: bold; text-transform: uppercase; width: 100%;">
            <tr>
                <td width="200px"><b>SKPD</b></td>
                <td><b>: <?= Html::encode($nama_skpd) ?></b></td>
            </tr>
            <tr>
                <td><b>TAHUN ANGGARAN</b></td>
                <td><b>: <?= $selectedPeriodValue ?></b></td>
            </tr>
        </table>

        <table class="tbdata">
            <thead>
                <tr>
                    <th>NO</th>
                    <th>SASARAN STRATEGIS</th>
                    <th colspan="2">INDIKATOR KINERJA</th>
                    <th>SATUAN</th>
                    <th>TARGET</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="tengah">(1)</td>
                    <td class="tengah">(2)</td>
                    <td class="tengah" colspan="2">(3)</td>
                    <td class="tengah">(4)</td>
                    <td class="tengah">(5)</td>
                </tr>
                <?php
                $sasaranCounter = 1;
                foreach ($sasaranRenstra as $sasaran):
                    $indikatorOfSasaran = array_filter($indikators, function ($ind) use ($sasaran) {
                        return $ind->refsasaranrenstra_id == $sasaran->refsasaranrenstra_id;
                    });
                    $rowspan = count($indikatorOfSasaran) > 0 ? count($indikatorOfSasaran) : 1;
                    $firstIndikatorRow = true;
                ?>
                    <?php if (empty($indikatorOfSasaran)): ?>
                        <tr>
                            <td class="tengah"><?= $sasaranCounter ?></td>
                            <td><?= Html::encode($sasaran->uraian_sasaranrenstra) ?></td>
                            <td colspan="4" class="tengah">Tidak ada indikator</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($indikatorOfSasaran as $i => $indikator): ?>
                            <tr>
                                <?php if ($firstIndikatorRow): ?>
                                    <td class="tengah" rowspan="<?= $rowspan ?>"><?= $sasaranCounter ?></td>
                                    <td rowspan="<?= $rowspan ?>"><?= Html::encode($sasaran->uraian_sasaranrenstra) ?></td>
                                <?php endif; ?>
                                <td class="tengah"><?= $sasaranCounter . '.' . ($i + 1) ?></td>
                                <td><?= Html::encode($indikator->uraian_indikatorsasaranrenstra) ?></td>
                                <td class="tengah"><?= Html::encode($indikator->indikatorsasaranrenstra_satuan) ?></td>
                                <td class="tengah"><?= Html::encode($indikator->indikatorsasaranrenstra_target) ?></td>
                            </tr>
                        <?php $firstIndikatorRow = false;
                        endforeach; ?>
                    <?php endif; ?>
                <?php $sasaranCounter++;
                endforeach; ?>
            </tbody>
        </table>

        <br><br>

        <table class="tbdata">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Program</th>
                    <th>Anggaran (Rp)</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $programCounter = 1;
                $totalAnggaranpkpSum = 0;
                foreach ($programs as $program):
                    $totalAnggaranpkp = $programAnggaranMap[$program->refprogram_id] ?? 0;
                    $totalAnggaranpkpSum += $totalAnggaranpkp;
                ?>
                    <tr>
                        <td class="tengah"><?= $programCounter++ ?></td>
                        <td><?= Html::encode($program->refProgram->nama_program) ?></td>
                        <td class="kanan"><?= 'Rp. ' . number_format($totalAnggaranpkp, 0, ',', '.') ?></td>
                        <td></td>
                    </tr>
                <?php endforeach; ?>
                <tr>
                    <th class="tengah" colspan="2">Total</th>
                    <th class="kanan"><?= 'Rp. ' . number_format($totalAnggaranpkpSum, 0, ',', '.') ?></th>
                    <th></th>
                </tr>
            </tbody>
        </table>

        <table class="tblPihak">
            <tr>
                <td></td>
                <td>Lubuk Pakam, <?= $reportDate ?></td>
            </tr>
            <tr>
                <td><?= Html::encode($leadership->jabatan_pimpinan ?? '') ?></td>
                <td><?= Html::encode($skpdHead->jabatan_kepala ?? '') ?></td>
            </tr>
            <tr>
                <td colspan="2" height="70px">&nbsp;</td>
            </tr>
            <tr>
                <td><?= Html::encode($leadership->nama_pimpinan ?? '') ?></td>
                <td><?= Html::encode($skpdHead->kepala_skpd ?? '') ?></td>
            </tr>
            <tr>
                <td></td>
                <td>NIP. <?= Html::encode($skpdHead->nip_kepala ?? '') ?></td>
            </tr>
        </table>
    </div>
</div>