<?php

use yii\helpers\Html;
use yii\helpers\Url;

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
                foreach ($sasaranRenstra as $item): if (!isset($item->refVisi)) continue;
                    $visiId = $item->refVisi->refvisi_id;
                    if (in_array($visiId, $visiSeen)) continue;
                    $visiSeen[] = $visiId; ?>
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
                foreach ($sasaranRenstra as $item): if (!isset($item->refMisi)) continue;
                    $misiId = $item->refMisi->refmisi_id;
                    if (in_array($misiId, $misiSeen)) continue;
                    $misiSeen[] = $misiId; ?>
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
                foreach ($sasaranRenstra as $item): if (!isset($item->refTujuan)) continue;
                    $tujuanId = $item->refTujuan->reftujuan_id;
                    if (in_array($tujuanId, $tujuanSeen)) continue;
                    $tujuanSeen[] = $tujuanId; ?>
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
                foreach ($sasaranRenstra as $item): if (!isset($item->refSasaran)) continue;
                    $sasaranId = $item->refSasaran->refsasaran_id;
                    if (in_array($sasaranId, $sasaranSeen)) continue;
                    $sasaranSeen[] = $sasaranId; ?>
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
                <?php $strategiSeen = [];
                foreach ($strategiList as $strategi): $id = $strategi->refstrategi_id;
                    if (in_array($id, $strategiSeen)) continue;
                    $strategiSeen[] = $id; ?>
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
        <td><span class="tebal">Kebijakan Renstra</span><br><br>
            <table class='tblAtas'>
                <?php $kebijakanSeen = [];
                foreach ($kebijakanList as $kebijakan): $id = $kebijakan->refkebijakan_id;
                    if (in_array($id, $kebijakanSeen)) continue;
                    $kebijakanSeen[] = $id; ?>
                    <tr>
                        <td>
                            <ul>
                                <li><?= Html::encode(strip_tags($kebijakan->uraian_kebijakan)) ?></li>
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
                        <td colspan="1" class='header'>Target Tahun</td>
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
                        }

                        $indicatorIndex = 1;

                        if (!empty($indikators)):
                            foreach ($indikators as $i => $indikator):
                    ?>
                                <tr>
                                    <?php if ($i === 0): ?>
                                        <td style='text-align:center;' rowspan="<?= count($indikators) ?>"><?= $index + 1 ?></td>
                                        <td rowspan="<?= count($indikators) ?>"><?= Html::encode(strip_tags($item->refTujuan->uraian_tujuan)) ?></td>
                                        <td style='text-align:center;' rowspan="<?= count($indikators) ?>"><?= $currentNumber ?></td>
                                        <td rowspan="<?= count($indikators) ?>"><?= Html::encode(strip_tags($item->uraian_sasaranrenstra)) ?></td>
                                        <td style='text-align:center;' rowspan="<?= count($indikators) ?>"><?= $currentNumber . '.' . $indicatorIndex ?></td>
                                    <?php endif; ?>

                                    <td><?= Html::encode(strip_tags($indikator->uraian_indikatorsasaranrenstra)) ?></td>
                                    <td style='text-align:center;'><?= Html::encode(strip_tags($indikator->indikatorsasaranrenstra_satuan)) ?></td>
                                    <td style='text-align:center;'><?= Html::encode(strip_tags($indikator->indikatorsasaranrenstra_target)) ?></td>
                                </tr>
                    <?php
                                $indicatorIndex++;
                            endforeach;
                        endif;
                    endforeach;
                    ?>
                </tbody>
            </table>
        </td>
    </tr>
</table>