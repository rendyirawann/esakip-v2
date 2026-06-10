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
        border: 1px solid #cfcfcf;
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
</style>
<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

?>
<div class="title">
    <img src="<?= Url::base(true) ?>/lightapp/assets/images/bappeda.png" alt="logo image" height="60" width="60" class="logo-lg" />
    <h5 class="tbdata">
        <center>Perjanjian Kinerja Tahunan - <?= Html::encode(ucwords(strtolower($nama_skpd))) ?><br>Periode <?= $selectedPeriodValue ?></center>
    </h5>
</div>
<hr>
<br>
<table border=0 cellpadding=1 cellspacing=1 border=0 class="tbdata" width="100%">
    <tr>
        <td>
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
                    $currentNumber = 0; // Primary number for refsasaranrenstra_id (increments for each new goal)
                    $lastSasaran = ''; // Variable to track the last goal (sasaran) code
                    foreach ($sasaranRenstra as $index => $item): ?>
                        <?php
                        // Fetch indicators associated with the current goal
                        $indikators = $item->indikatorSasaran; // Fetch indicator data

                        // Get the code for the current goal
                        $sasaranCode = $item->uraian_sasaranrenstra; // Assume this is used for comparison

                        // Check if the current goal code is different from the last one
                        if ($lastSasaran !== $sasaranCode) {
                            $currentNumber++; // Increment primary number for a new goal
                            $lastSasaran = $sasaranCode; // Update the last goal code
                            $indicatorIndex = 1; // Reset secondary number for the new goal
                        }

                        if (!empty($indikators)): // Check if there are indicators found
                        ?>
                            <?php foreach ($indikators as $i => $indikator): // Loop through each indicator 
                            ?>
                                <tr>
                                    <?php if ($i === 0): // If this is the first indicator for the current goal 
                                    ?>
                                        <td style='text-align:center;'><?= $index + 1 ?></td>
                                        <td><?= Html::encode(strip_tags($item->uraian_sasaranrenstra)) ?></td>
                                        <td style='text-align:center;'><?= $currentNumber . '.' . $indicatorIndex ?></td> <!-- Display goal with format -->
                                        <?php $indicatorIndex++; // Increment secondary number for the next indicator 
                                        ?>
                                    <?php else: // For additional indicators, leave unnecessary columns blank 
                                    ?>
                                        <td></td>
                                        <td></td>
                                        <td style='text-align:center;'><?= $currentNumber . '.' . $indicatorIndex ?></td> <!-- Display incremental indicator number -->
                                        <?php $indicatorIndex++; // Increment secondary number 
                                        ?>
                                    <?php endif; ?>
                                    <td><?= Html::encode(strip_tags($indikator->uraian_indikatorsasaranrenstra)) ?></td>
                                    <td style='text-align:center;'><?= Html::encode(strip_tags($indikator->indikatorsasaranrenstra_satuan)) ?></td>
                                    <td style='text-align:center;'><?= Html::encode(strip_tags($indikator->target_pk)) ?></td>
                                </tr>
                            <?php endforeach; // End of indicators loop 
                            ?>
                        <?php else: // If no indicators found 
                        ?>
                            <tr>
                                <td colspan="7" class="text-center">Tidak ada indikator</td>
                            </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>

                </tbody>
            </table>
        </td>
    </tr>
</table>