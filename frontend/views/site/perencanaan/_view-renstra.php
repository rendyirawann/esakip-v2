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

    table.tbdata,
    table.tblAtas {
        border: none;
    }

    table.tbdata td,
    table.tbdata th {
        border: none !important;
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
        <center>Rencana Strategis <?= Html::encode(ucwords(strtolower($nama_skpd))) ?><br>Periode <?= $selectedPeriodValue ?></center>
    </h5>
</div>
<hr>
<br>
<table border=0 cellpadding=1 cellspacing=1 border=0 class="tbdata" width="100%">
    <tr>
        <td><span class="tebal">Visi RPJMD</span><br><br>
            <table class='tblAtas'>
                <?php $visiSeen = [];
                foreach ($sasaranRenstra as $item):
                    if (!isset($item->refVisi)) continue;
                    $visiId = $item->refVisi->refvisi_id;
                    if (in_array($visiId, $visiSeen)) continue;
                    $visiSeen[] = $visiId;
                ?>
                    <tr>
                        <td>
                            <ul>
                                <li><?= Html::encode(strip_tags($item->refVisi->uraian_visi)) ?></li>
                            </ul>
                        </td>
                    </tr>
                <?php endforeach; ?>

            </table>
        </td>
    </tr>
    <tr>
        <td><span class="tebal">Misi RPJMD</span><br><br>
            <table class='tblAtas'>
                <?php $misiSeen = [];
                foreach ($sasaranRenstra as $item):
                    if (!isset($item->refMisi)) continue;
                    $misiId = $item->refMisi->refmisi_id;
                    if (in_array($misiId, $misiSeen)) continue;
                    $misiSeen[] = $misiId;
                ?>
                    <tr>
                        <td>
                            <ul>
                                <li><?= Html::encode(strip_tags($item->refMisi->uraian_misi)) ?></li>
                            </ul>
                        </td>
                    </tr>
                <?php endforeach; ?>

            </table>
        </td>
    </tr>
    <tr>
        <td><span class="tebal">Tujuan RPJMD</span><br><br>
            <table class='tblAtas'>
                <?php $tujuanSeen = [];
                foreach ($sasaranRenstra as $item):
                    if (!isset($item->refTujuan)) continue;
                    $tujuanId = $item->refTujuan->reftujuan_id;
                    if (in_array($tujuanId, $tujuanSeen)) continue;
                    $tujuanSeen[] = $tujuanId;
                ?>
                    <tr>
                        <td>
                            <ul>
                                <li><?= Html::encode(strip_tags($item->refTujuan->uraian_tujuan)) ?></li>
                            </ul>
                        </td>
                    </tr>
                <?php endforeach; ?>

            </table>
        </td>
    </tr>
    <tr>
        <td><span class="tebal">Sasaran RPJMD</span><br><br>
            <table class='tblAtas'>
                <?php $sasaranSeen = [];
                foreach ($sasaranRenstra as $item):
                    if (!isset($item->refSasaran)) continue;
                    $sasaranId = $item->refSasaran->refsasaran_id;
                    if (in_array($sasaranId, $sasaranSeen)) continue;
                    $sasaranSeen[] = $sasaranId;
                ?>
                    <tr>
                        <td>
                            <ul>
                                <li><?= Html::encode(strip_tags($item->refSasaran->uraian_sasaran)) ?></li>
                            </ul>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </td>
    </tr>
    <tr>
        <td><span class="tebal">Strategi Renstra</span><br><br>
            <table class='tblAtas'>
                <?php
                $strategiSeen = [];
                foreach ($strategiList as $strategi):
                    $id = $strategi->refstrategi_id;
                    if (in_array($id, $strategiSeen)) continue;
                    $strategiSeen[] = $id;
                ?>
                    <tr>
                        <td>
                            <ul>
                                <li><?= Html::encode(strip_tags($strategi->uraian_strategi)) ?></li>
                            </ul>
                        </td>
                    </tr>
                <?php endforeach; ?>

            </table>
        </td>
    </tr>

    <tr>
        <td>
            <table class='tblRenstra'>
                <thead>
                    <tr>
                        <td rowspan='2' width='25px' class='header'>No</td>
                        <td rowspan='2' width='150px' class='header'>Tujuan Renstra</td>
                        <td rowspan='2' width='25px' class='header'></td>
                        <td rowspan='2' width='150px' class='header'>Sasaran Renstra</td>
                        <td rowspan='2' width='30px' class='header'></td>
                        <td rowspan='2' width='150px' class='header'>Indikator Kinerja</td>
                        <td rowspan='2' width='80px' class='header'>Satuan</td>
                        <td colspan="2" class='header'>Target Tahun</td>
                    </tr>
                    <tr>
                        <td width='80px' class='header'><?= $selectedPeriodValue ?></td>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($sasaranRenstra)): ?>
                        <?php
                        $currentNumber = 0; // Variabel untuk menyimpan angka depan (sebelum titik)
                        $lastSasaran = ''; // Variabel untuk menyimpan sasaran terakhir
                        foreach ($sasaranRenstra as $index => $item): ?>
                            <?php
                            $indikators = $item->indikatorSasaran; // Mengambil data indikator
                            $sasaranCode = $item->uraian_sasaranrenstra; // Kode sasaran

                            // Cek apakah kode sasaran saat ini berbeda dari kode sasaran terakhir
                            if ($lastSasaran !== $sasaranCode) {
                                $currentNumber++; // Increment jika berbeda
                                $lastSasaran = $sasaranCode; // Update kode sasaran terakhir
                            }

                            $indicatorIndex = 1; // Reset untuk setiap sasaran baru

                            if (!empty($indikators)): // Cek jika ada indikator
                            ?>
                                <?php foreach ($indikators as $i => $indikator): ?>
                                    <tr>
                                        <?php if ($i === 0): // Jika ini adalah indikator pertama 
                                        ?>
                                            <td style='text-align:center;'><?= $index + 1 ?></td>
                                            <td><?= Html::encode(strip_tags($item->refTujuan->uraian_tujuan)) ?></td>
                                            <td style='text-align:center;'><?= $currentNumber . '.' . ($indicatorIndex++) ?></td>
                                            <td><?= Html::encode(strip_tags($item->uraian_sasaranrenstra)) ?></td>
                                            <td></td>
                                        <?php else: // Jika bukan indikator pertama, kosongkan kolom yang tidak perlu 
                                        ?>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        <?php endif; ?>
                                        <td><?= Html::encode(strip_tags($indikator->uraian_indikatorsasaranrenstra)) ?></td>
                                        <td style='text-align:center;'><?= Html::encode(strip_tags($indikator->indikatorsasaranrenstra_satuan)) ?></td>
                                        <td style='text-align:center;'><?= Html::encode(strip_tags($indikator->indikatorsasaranrenstra_target)) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center">Tidak ada indikator</td>
                                </tr>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center">Belum ada Data</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

        </td>
    </tr>
</table>