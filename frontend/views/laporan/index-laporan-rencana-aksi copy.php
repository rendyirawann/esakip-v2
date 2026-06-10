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

    .judultabel {
        margin-top: 0px;
        font-family: 'Bookman Old Style', 'Verdana';
        font-size: 12px;
        font-weight: bold;
    }

    .tbdata {
        border-collapse: collapse;
        font-family: 'Bookman Old Style', 'Verdana';
        font-size: 10px;
        border: 1px solid #5d5d5d;
    }

    .tbdata th {
        height: 40px;
        background: rgb(0, 160, 221);
        text-align: center;
        border: 1px solid #5d5d5d;
        color: #ffffff;
        padding: 3px 5px;
    }

    .tbdata td {
        padding: 3px 5px;
        border: 1px solid #5d5d5d;
        vertical-align: top;
    }

    .tbdata td.tdisi {
        border-bottom: 0px;
    }

    .tbdata td.tdkosong {
        border-bottom: 0px;
        border-top: 0px;
        visibility: hidden;
    }

    .tengah {
        text-align: center;
    }

    .kanan {
        text-align: right;
    }

    thead {
        display: table-header-group;
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

$pdfUrl = Url::to(['laporan/download-pdf-realisasi-anggaran']); // Endpoint to generate and download PDF
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

    .judultabel {
        margin-top: 0px;
        font-family: 'Bookman Old Style', 'Verdana';
        font-size: 12px;
        font-weight: bold;
    }

    .tbdata {
        border-collapse: collapse;
        font-family: 'Bookman Old Style', 'Verdana';
        font-size: 10px;
        border: 1px solid #5d5d5d;
    }

    .tbdata th {
        height: 40px;
        background: rgb(0, 160, 221);
        text-align: center;
        border: 1px solid #5d5d5d;
        color: #ffffff;
        padding: 3px 5px;
    }

    .tbdata td {
        padding: 3px 5px;
        border: 1px solid #5d5d5d;
        vertical-align: top;
    }

    .tbdata td.tdisi {
        border-bottom: 0px;
    }

    .tbdata td.tdkosong {
        border-bottom: 0px;
        border-top: 0px;
        visibility: hidden;
    }

    .tengah {
        text-align: center;
    }

    .kanan {
        text-align: right;
    }

    thead {
        display: table-header-group;
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

    // Download PDF with loading overlay
    $('#btnPdf').on('click', function() {
        var content = $('#previewBox').html(); // Get content from previewBox
        var title = 'Laporan Rencana Kinerja Tahun " . $selectedPeriodValue . " - ' + toSentenceCase(" . json_encode($nama_skpd) . "); // Dynamic title based on period and SKPD
        $('#loadingOverlay').show(); // Show loading overlay

        $.ajax({
            url: '{$pdfUrl}',
            type: 'POST',
            data: {
                content: content,
                title: title // Send dynamic title
            },
            success: function(data) {
                $('#loadingOverlay').hide(); // Hide loading overlay
                window.location.href = data; // Redirect to download URL
            },
            error: function() {
                $('#loadingOverlay').hide(); // Hide loading overlay on error
            }
        });
    });

    // Download Excel with loading overlay
    $('#btnExcel').on('click', function() {
        var content = $('#previewBox').html(); // Ambil konten dari previewBox
        $('#loadingOverlay').show(); // Tampilkan overlay loading
        $.ajax({
            url: '{$excelUrl}', // Endpoint untuk mengunduh Excel
            type: 'POST',
            data: {
                content: content,
                title: '{$this->title}' // Kirimkan judul untuk nama file
            },
            success: function(data) {
                // Anda dapat menambahkan logika tambahan di sini jika diperlukan
                $('#loadingOverlay').hide(); // Sembunyikan overlay loading
                window.location.href = data; // Arahkan ke URL untuk mengunduh PDF
            },
            error: function() {
                $('#loadingOverlay').hide(); // Sembunyikan overlay loading
            }
        });
    });

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
                                <?= \yii\helpers\Html::beginForm(['index-laporan-renja-tahunan'], 'get', ['class' => 'form-inline']); ?>
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
                                    <button id="btnPdf" class="btn btn-danger">PDF</button>
                                    <button id="btnExcel" class="btn btn-success">Excel</button>
                                </div>
                                <?php if (empty($sasaranRenstra)): ?>
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
                                        <div id='content'>
                                            <h3 class='judultabel'>
                                                <center>Rencana Aksi <?php echo $this->crud->get_nama_skpd($idskpd); ?><br>Periode <?= $periode['tahunmulai'] . ' - ' . $periode['tahunakhir'] ?><br>Tahun <?= $tahun ?></center>
                                            </h3>
                                            <table width='100%' border=0 cellpadding=0 cellspacing=0 class='tbdata' align='center'>
                                                <thead>
                                                    <tr>
                                                        <th width='3%'>No</th>
                                                        <th width='8%'>Sasaran Strategis</th>
                                                        <th width='8%'>Indikator Kinerja</th>
                                                        <th width='5%'>Satuan</th>
                                                        <th width='8%'>Target</th>
                                                        <th width='8%'>Program</th>
                                                        <th width='8%'>Anggaran</th>
                                                        <th width='8%'>Kegiatan</th>
                                                        <th width='8%'>Anggaran</th>
                                                        <th width='8%'>Sub Kegiatan</th>
                                                        <th width='8%'>Anggaran</th>
                                                        <th width='8%'>Output Sub Kegiatan</th>
                                                        <th width='8%'>Target</th>
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
                                                                    <td>tampilkan triwulan_target_rkt dari sakip_indikatorsasaranrenstra_triwulan dan tampilkan secara br Tw1=triwulan_target_rkt(reftriwulan_id=1)<br>Tw2=triwulan_target_rkt(reftriwulan_id=2)<br>Tw3=triwulan_target_rkt(reftriwulan_id=3)<br>Tw4=triwulan_target_rkt(reftriwulan_id=4) sesuai refperiode_id dan refskpd_id dan refindikatorsasaranrenstra_id terkait<br></td>
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

                                                    <tr>

                                                        <td class='text-center' rowspan='" . $renstra . "'>nomor++</td>
                                                        <td rowspan='" . $renstra . "'>tampilkan uraian_sasaranrenstra dari sakip_sasaranrenstra</td>
                                                        <td rowspan='" . $renstraIND . "'>tampilkan seluruhan refindikatorsasaranrenstra_id refIndikatorSasaranRenstra->uraian_indikatorsasaranrenstra yang memiliki refsasaranrenstra_id terkait</td>
                                                        <td class='text-center' rowspan='" . $renstraIND . "'>" . $v->satuan . "</td>
                                                        <td rowspan='" . $renstraIND . "'>
                                                            " . $targetRenstra . "
                                                        </td>
                                                        <td rowspan='" . $program . "'>" . $v->refprogram_nama . "</td>
                                                        <td class='text-right' rowspan='" . $program . "'>" . $this->mycustom->formatnomor($aProgram) . "</td>
                                                        <td rowspan='" . $kegiatan . "'>" . $v->refkegiatan_nama . "</td>
                                                        <td class='text-right' rowspan='" . $kegiatan . "'>" . $this->mycustom->formatnomor($aKegiatan) . "</td>
                                                        <td rowspan='" . $subkegiatan . "'>" . $v->refsubkegiatan_nama . "</td>
                                                        <td class='text-right' rowspan='" . $subkegiatan . "'>" . $this->mycustom->formatnomor($aSubkegiatan) . "</td>

                                                        <td width='10%'><?= $v->permen90refsubkegiatan_indikator ?></td>
                                                        <td width='10%'><?= $targetSubkegiatan; ?></td>

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