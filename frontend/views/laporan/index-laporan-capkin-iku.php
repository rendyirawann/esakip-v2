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
    .title {
        border-collapse: collapse;
        font-family: 'Public Sans', sans-serif;
        /* Menggunakan font Public Sans */
        font-size: 11px;
        text-align: center;
    }

    .tbdata {
        font-size: smaller;
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
        font-size: 8px;
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

    .keterangan {
        border-collapse: collapse;
    }

    .keterangan td {
        padding: 5px;
        border: 1px solid #e3e3e3;
    }

    .merah {
        background: #ff0404;
        color: white;
    }

    .hijau {
        background: #006600;
        color: white;
    }

    .biru {
        background: #000266;
        color: white;
    }

    .abu {
        background: #95a5a6;
        color: white;
    }
</style>

<?php

use frontend\assets\AppAsset;
use frontend\models\User;
use common\widgets\Alert;
use yii\bootstrap5\Breadcrumbs;
use yii\bootstrap5\Html;
use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;
use yii\helpers\Url;
use mdm\admin\components\MenuHelper;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use frontend\models\SakipIndikatorsasaranrenstraTriwulan;

$pdfUrl = Url::to(['laporan/download-pdf-capkin-iku']); // Endpoint to generate and download PDF
$excelUrl = Url::to(['laporan/download-excel']); // Endpoint to generate and download Excel
$printUrl = Url::to(['laporan/print-preview']); // Endpoint to load print content
$this->registerJs("

  // Print functionality
 $('#btnPrint').on('click', function() {
    var content = $('#previewBox').html(); // Ambil konten dari previewBox
    var printWindow = window.open('', '_blank');

    // Sertakan styling CSS yang sama pada jendela cetak
    printWindow.document.write('<html><head><title>Print</title>');
    printWindow.document.write('<style>');
    printWindow.document.write(`
        /* Tambahkan styling CSS yang ingin diikutsertakan di jendela cetak */
        #loadingOverlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
        }
    .title {
        border-collapse: collapse;
        font-family: 'Public Sans', sans-serif;
        /* Menggunakan font Public Sans */
        font-size: 11px;
        text-align: center;
    }

    .tbdata {
        border-collapse: collapse;
        font-family: 'Public Sans', sans-serif;
        font-size: 11px;
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
        border: 1px solid #cfcfcf;
        vertical-align: top;
    }

    .tengah {
        text-align: center;
    }

    thead {
        display: table-header-group;
    }

    .keterangan {
        border-collapse: collapse;
    }

    .keterangan td {
        padding: 5px;
        border: 1px solid #e3e3e3;
    }

    .merah {
        background: #ff0404;
        color: white;
    }

    .hijau {
        background: #006600;
        color: white;
    }

    .biru {
        background: #000266;
        color: white;
    }

    .abu {
        background: #95a5a6;
        color: white;
    }
    `);
    printWindow.document.write('</style></head><body>');
    printWindow.document.write(content); // Tulis konten ke printWindow
    printWindow.document.write('</body></html>');
    printWindow.document.close();
    printWindow.print(); // Panggil print
});


// Download PDF with loading overlay
 function toSentenceCase(string) {
        return string.toLowerCase().replace(/(?:^|\s)\S/g, function(a) {
            return a.toUpperCase();
        });
    }

    

");

?>
<div class="pc-container">
    <div class="pc-content">
        <div id="loadingOverlay" style="display:none;">
            <div class="text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p style="color: white;">Downloading Files...</p>
            </div>
        </div>

        <!-- [ breadcrumb ] start -->
        <div class="page-header">
            <div class="page-block">
                <div class="row align-items-center">
                    <div class="col-md-12">
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?= Url::to(['/site/index']) ?>">Dashboard</a></li>
                            <li class="breadcrumb-item" aria-current="page">Home</li>
                        </ul>
                    </div>
                    <div class="col-md-12">
                        <div class="page-header-title">
                            <h2 class="mb-0">Home</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- [ breadcrumb ] end -->

        <div class="row">
            <!-- start row -->
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header" style="background-color: #04A9F5; padding: 8px;"> <!-- Sesuaikan padding di sini -->
                        <h6 style="color: white; margin: 0; cursor: pointer;" id="toggleAll"> <!-- Mengatur margin menjadi 0 untuk mengurangi ruang -->
                            <i class="fas fa-pen-fancy"></i> Laporan Capaian Kinerja Indikator Kinerja Utama - <?= Html::encode(ucwords(strtolower($nama_skpd))) ?>
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <!-- Dropdown filter berdasarkan refperiode_id -->
                                <?= \yii\helpers\Html::beginForm(['index-laporan-capkin-iku'], 'get', ['class' => 'form-inline']); ?>
                                <div class="form-group">
                                    <?= \yii\helpers\Html::label('Pilih Periode:', 'refperiode_id', ['class' => 'mr-2']); ?>
                                    <?= \yii\helpers\Html::dropDownList(
                                        'refperiode_id',
                                        $selectedPeriodId,
                                        \yii\helpers\ArrayHelper::map($periodeList, 'refperiode_id', 'periode'), // Mapping periodeList
                                        [
                                            'class' => 'form-control',
                                            'prompt' => 'Semua Periode',
                                            'onchange' => 'this.form.submit()' // Submit form saat pilihan berubah
                                        ]
                                    ); ?>
                                </div>
                                <?= \yii\helpers\Html::endForm(); ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="btn-group mb-3" role="group">
                                    <button id="btnPrint" class="btn btn-primary">Print</button>
                                    <?= Html::a(
                                        '<i class="fas fa-file-pdf"></i> PDF',
                                        ['cetak-laporan-capkin-iku', 'refperiode_id' => $selectedPeriodId, 'refskpd_id' => $selectedSkpdId],
                                        ['class' => 'btn btn-danger', 'target' => '_blank', 'data-pjax' => 0]
                                    ) ?>
                                    <?= Html::a(
                                        '<i class="fas fa-file-excel"></i> Excel',
                                        ['cetak-laporan-capkin-iku-excel', 'refperiode_id' => $selectedPeriodId, 'refskpd_id' => $selectedSkpdId],
                                        ['class' => 'btn btn-success', 'target' => '_blank', 'data-pjax' => 0]
                                    ) ?>
                                </div>
                                <?php if (empty($indikators)): ?>
                                    <div id="previewBox" class="border p-3" style="min-height: 400px;">
                                        <p class="text-muted">Data Periode Ini Tidak Tersedia</p>
                                    </div>
                                <?php else: ?>
                                    <!-- Preview box -->
                                    <div id="previewBox" class="border p-3" style="min-height: 400px;">
                                        <div class="title">
                                            <img src="<?= Url::base(true) ?>/lightapp/assets/images/bappeda.png" alt="logo image" height="60" width="60" class="logo-lg" />
                                            <h5 class="tbdata">
                                                <center>Capaian Kinerja Indikator Kinerja Utama - <?= Html::encode(ucwords(strtolower($nama_skpd))) ?><br>Periode <?= $selectedPeriodValue ?></center>
                                            </h5>
                                        </div>
                                        <hr>
                                        <br>
                                        <div id="content">
                                            <table border="0" cellpadding="1" cellspacing="1" class="tbdata" width="100%">
                                                <tr>
                                                    <td>
                                                        <table width="100%" border="0" cellpadding="1" cellspacing="1" class="tbdata">
                                                            <thead>
                                                                <tr>
                                                                    <th>Nomor Renstra</th>
                                                                    <th width='150px' class='header'>Sasaran Renstra</th>
                                                                    <th width='30px' class='header'>Nomor Indikator</th>
                                                                    <th width='150px' class='header'>Indikator Sasaran Renstra</th>
                                                                    <th width='80px' class='header'>Satuan</th>
                                                                    <th class='header'>Target Tahun</th>
                                                                    <th class='header'>Triwulan</th>
                                                                    <th class='header'>Target</th>
                                                                    <th class='header'>Realisasi</th>
                                                                    <th class='header'>Capaian %</th>
                                                                    <th class='header'>Keterangan</th>
                                                                </tr>
                                                                <tr>
                                                                    <th>a</th>
                                                                    <th>b</th>
                                                                    <th>c</th>
                                                                    <th>d</th>
                                                                    <th>e</th>
                                                                    <th>f</th>
                                                                    <th>g</th>
                                                                    <th>h</th>
                                                                    <th>i</th>
                                                                    <th>j</th>
                                                                    <th>k</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php
                                                                $lastRefSasaranRenstraId = null; // To track the last displayed refsasaranrenstra_id
                                                                $renstraNomor = 0; // Counter for Nomor Renstra
                                                                $indikatorNomor = 0; // Counter for Indikator per refsasaranrenstra_id

                                                                foreach ($indikators as $index => $indikator):
                                                                    // Fetch related triwulan data
                                                                    $triwulanData = SakipIndikatorSasaranRenstraTriwulan::find()
                                                                        ->where(['refindikatorsasaranrenstra_id' => $indikator->refindikatorsasaranrenstra_id])
                                                                        ->andWhere(['refsasaranrenstra_id' => $indikator->refsasaranrenstra_id])
                                                                        ->andWhere(['refskpd_id' => $refskpd_id])
                                                                        ->all();

                                                                    // Calculate rowspan for this indicator
                                                                    $rowspan = count($triwulanData) + 2; // Add 1 for the "Kondisi Akhir F" row
                                                                ?>
                                                                    <tr>
                                                                        <?php if ($indikator->refsasaranrenstra_id !== $lastRefSasaranRenstraId): ?>
                                                                            <?php
                                                                            $renstraNomor++; // Increment the renstra number
                                                                            $indikatorNomor = 1; // Reset indikator counter for the new refsasaranrenstra_id
                                                                            $lastRefSasaranRenstraId = $indikator->refsasaranrenstra_id; // Update the last displayed ID 
                                                                            ?>
                                                                            <td rowspan="<?= $rowspan ?>" style='text-align:center;'><?= $renstraNomor ?></td> <!-- Display the renstra number -->
                                                                            <td rowspan="<?= $rowspan ?>"><?= Html::encode($indikator->refSasaranrenstra->uraian_sasaranrenstra) ?></td>
                                                                        <?php else: ?>
                                                                            <td rowspan="<?= $rowspan ?>"></td> <!-- Empty cell if it's the same refsasaranrenstra_id -->
                                                                            <td rowspan="<?= $rowspan ?>"></td>
                                                                        <?php endif; ?>

                                                                        <td rowspan="<?= $rowspan ?>"><?= $renstraNomor . '.' . $indikatorNomor ?></td> <!-- Display the current indikator number -->
                                                                        <td rowspan="<?= $rowspan ?>"><?= Html::encode($indikator->uraian_indikatorsasaranrenstra) ?></td>
                                                                        <td rowspan="<?= $rowspan ?>" style='text-align:center;'><?= Html::encode($indikator->indikatorsasaranrenstra_satuan) ?></td>
                                                                        <td rowspan="<?= $rowspan ?>" style='text-align:center;'><?= Html::encode($indikator->indikatorsasaranrenstra_target) ?></td>
                                                                    </tr>
                                                                    <!-- Kondisi Akhir F row -->
                                                                    <tr>
                                                                        <td colspan="2" style='text-align:center;'>Kondisi Akhir F</td>
                                                                        <?php
                                                                        // // Determine the background color for triwulan_target_pk_p
                                                                        // $targetColor = $triwulan->triwulan_target_pk_p === 'n/a' ? '#fff' : ($triwulan->triwulan_target_pk_p < 70 ? '#ff0404' : ($triwulan->triwulan_target_pk_p < 100 ? '#ff6600' : '#006600'));

                                                                        // // Determine the background color for triwulan_realisasi
                                                                        // $realisasiColor = $triwulan->triwulan_realisasi === 'n/a' ? '#fff' : ($triwulan->triwulan_realisasi < 70 ? '#ff0404' : ($triwulan->triwulan_realisasi < 100 ? '#ff6600' : '#006600'));

                                                                        // Determine the background color for triwulan_capaian
                                                                        $capaianColor = $indikator->capaian === 'n/a' ? '#fff' : ($indikator->capaian < 70 ? '#ff0404' : ($indikator->capaian < 100 ? '#ff6600' : '#006600'));
                                                                        ?>
                                                                        <td style='text-align:center;'><?= Html::encode($indikator->realisasi) ?></td>
                                                                        <td style='text-align:center; background-color:<?= $capaianColor ?>; color:white;'><?= Html::encode($indikator->capaian) ?></td>
                                                                        <td>
                                                                            <?php
                                                                            // Logic to determine the keterangan based on the target achievement
                                                                            if ($indikator->capaian === 'n/a') {
                                                                                echo "Tidak Ada Target";
                                                                            } elseif ($indikator->capaian < 70) {
                                                                                echo "Tidak Mencapai 70%";
                                                                            } elseif ($indikator->capaian < 100) {
                                                                                echo "Tidak Mencapai 100%";
                                                                            } else {
                                                                                echo "Tercapai";
                                                                            }
                                                                            ?>
                                                                        </td>
                                                                    </tr>
                                                                    <?php if ($rowspan > 1): ?>
                                                                        <?php foreach ($triwulanData as $i => $triwulan): ?>
                                                                            <?php if ($i > 0): ?>
                                                </tr>
                                                <tr>
                                                <?php endif; ?>
                                                <td>Triwulan <?= $triwulan->reftriwulan_id ?></td>
                                                <?php
                                                                            // // Determine the background color for triwulan_target_pk_p
                                                                            // $targetColor = $triwulan->triwulan_target_pk_p === 'n/a' ? '#fff' : ($triwulan->triwulan_target_pk_p < 70 ? '#ff0404' : ($triwulan->triwulan_target_pk_p < 100 ? '#ff6600' : '#006600'));

                                                                            // // Determine the background color for triwulan_realisasi
                                                                            // $realisasiColor = $triwulan->triwulan_realisasi === 'n/a' ? '#fff' : ($triwulan->triwulan_realisasi < 70 ? '#ff0404' : ($triwulan->triwulan_realisasi < 100 ? '#ff6600' : '#006600'));

                                                                            // Determine the background color for triwulan_capaian
                                                                            $capaianColor = $triwulan->triwulan_capaian === 'n/a' ? '#fff' : ($triwulan->triwulan_capaian < 70 ? '#ff0404' : ($triwulan->triwulan_capaian < 100 ? '#ff6600' : '#006600'));
                                                ?>
                                                <td style="text-align:center;"><?= Html::encode($triwulan->triwulan_target_pk_p) ?></td>
                                                <td style="text-align:center;"><?= Html::encode($triwulan->triwulan_realisasi) ?></td>
                                                <td style="background-color: <?= $capaianColor ?>; text-align:center; color:white;"><?= Html::encode($triwulan->triwulan_capaian) ?></td>
                                                <td>
                                                    <?php
                                                                            // Logic to determine the keterangan based on the target achievement
                                                                            if ($triwulan->triwulan_capaian === 'n/a') {
                                                                                echo "Tidak Ada Target";
                                                                            } elseif ($triwulan->triwulan_capaian < 70) {
                                                                                echo "Tidak Mencapai 70%";
                                                                            } elseif ($triwulan->triwulan_capaian < 100) {
                                                                                echo "Tidak Mencapai 100%";
                                                                            } else {
                                                                                echo "Tercapai";
                                                                            }
                                                    ?>
                                                </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    <?php
                                                                    $indikatorNomor++; // Increment indikator number for each indikator
                                                                endforeach; ?>
                                    </tbody>
                                            </table>






                                            </td>
                                            </tr>
                                            </table>




                                            <h3>Keterangan</h3>
                                            <table class="keterangan">
                                                <tbody>
                                                    <tr class="odd">
                                                        <td>Warna</td>
                                                        <td>Prosentase</td>
                                                        <td>Keterangan</td>
                                                    </tr>
                                                    <tr>
                                                        <td style="background-color:#fff"></td>
                                                        <td>n/a</td>
                                                        <td>Tidak Ada Target</td>
                                                    </tr>
                                                    <tr>
                                                        <td style="background-color:#ff0404"></td>
                                                        <td>x < 70%</td>
                                                        <td>Tidak Mencapai 70%</td>
                                                    </tr>
                                                    <tr>
                                                        <td style="background-color:#ff6600"></td>
                                                        <td> 70 <= x < 100%</td>
                                                        <td>Tidak Mencapai 100%</td>
                                                    </tr>
                                                    <tr class="odd">
                                                        <td style="background-color:#006600"></td>
                                                        <td>x => 100%</td>
                                                        <td>Tercapai</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>

                                        <!--  -->
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- row preview -->
                    </div>
                </div>
            </div>
            <!-- end row -->
        </div>



    </div>
    <!-- [ Main Content ] end -->
</div>
</div>
<!-- [ Main Content ] end -->