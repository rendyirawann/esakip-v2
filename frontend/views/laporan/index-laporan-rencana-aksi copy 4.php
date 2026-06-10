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
use frontend\models\SakipIndikatorsasaranrenstraTriwulan;
use frontend\models\SakipIndikatorcascadingsubkegiatanTriwulan;
use frontend\models\SakipIndikatorcascadingsubkegiatan;

$pdfUrl = Url::to(['laporan/download-pdf-rencana-aksi']); // Endpoint to generate and download PDF
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
                            <i class="fas fa-pen-fancy"></i> Laporan CapaiRencana Aksi - <?= Html::encode(ucwords(strtolower($nama_skpd))) ?>
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <!-- Dropdown filter berdasarkan refperiode_id -->
                                <?= \yii\helpers\Html::beginForm(['index-laporan-rencana-aksi'], 'get', ['class' => 'form-inline']); ?>
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
                                <?php if (empty($cascadingSubkegiatan)): ?>
                                    <div id="previewBox" class="border p-3" style="min-height: 400px;">
                                        <p class="text-muted">Data Periode Ini Tidak Tersedia</p>
                                    </div>
                                <?php else: ?>
                                    <!-- Preview box -->
                                    <div id="previewBox" class="border p-3" style="min-height: 400px;">
                                        <div class="title">
                                            <img src="<?= Url::base(true) ?>/lightapp/assets/images/bappeda.png" alt="logo image" height="60" width="60" class="logo-lg" />
                                            <h5 class="tbdata">
                                                <center>Rencana Aksi - <?= Html::encode(ucwords(strtolower($nama_skpd))) ?><br>Periode <?= $selectedPeriodValue ?></center>
                                            </h5>
                                        </div>
                                        <hr>
                                        <br>
                                        <div id='content'>
                                            <h3 class='judultabel'>
                                                <center>Rencana Aksi <?= Html::encode(ucwords(strtolower($nama_skpd))) ?><br>Periode <?= $selectedPeriodValue ?></center>
                                            </h3>
                                            <table width='100%' border='0' cellpadding='0' cellspacing='0' class='tbdata' align='center'>
                                                <thead>
                                                    <tr>
                                                        <th width='3%'>No</th>
                                                        <th width='8%'>Program</th>
                                                        <th width='8%'>Anggaran Program</th> <!-- New column -->
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
                                                        // Count the number of subkegiatan in the group to use as rowspan
                                                        $rowspan = count($subkegiatanGroup);
                                                        $firstItem = reset($subkegiatanGroup); // Get the first item for Kegiatan name
                                                        ?>
                                                        <!-- Render the first row with the Kegiatan name and rowspan -->
                                                        <tr>
                                                            <td style='text-align:center;white-space: normal;' rowspan="<?= $rowspan ?>"><?= $nomor++; ?></td>
                                                            <td style='text-align:center;white-space: normal;' rowspan="<?= $rowspan ?>">
                                                                <?php
                                                                // Display the related program name
                                                                echo $firstItem->refCascadingKegiatan->refCascadingProgram->refProgram->nama_program;
                                                                ?>
                                                            </td>
                                                            <td style='text-align:center;white-space: normal;' rowspan="<?= $rowspan ?>">
                                                                <?php
                                                                // Fetch the related Kegiatan name (this will be the same for all rows in the group)
                                                                echo $firstItem->refCascadingKegiatan && $firstItem->refCascadingKegiatan->refKegiatan
                                                                    ? $firstItem->refCascadingKegiatan->refKegiatan->nama_kegiatan
                                                                    : '-';
                                                                ?>
                                                            </td>
                                                            <!-- Display total anggaran for Kegiatan -->
                                                            <td style='text-align:center;white-space: normal;' rowspan="<?= $rowspan ?>">
                                                                <?= isset($anggaranPerKegiatan[$refcascadingkegiatan_id]) ? number_format($anggaranPerKegiatan[$refcascadingkegiatan_id], 0, ',', '.') : '-' ?>
                                                            </td>
                                                            <td style='text-align:center;white-space: normal;'>
                                                                <?= $firstItem->refSubkegiatan ? $firstItem->refSubkegiatan->nama_subkegiatan : '-'; ?>
                                                            </td>
                                                            <td style='text-align:center;white-space: normal;'>
                                                                <?= $firstItem->subkegiatan_anggaran ? number_format($firstItem->subkegiatan_anggaran, 0, ',', '.') : '-'; ?>
                                                            </td>
                                                            <td style='text-align:center;white-space: normal;'>
                                                                <?= $firstItem->uraian_indikatorsubkegiatan ? $firstItem->uraian_indikatorsubkegiatan : '-'; ?>
                                                            </td>
                                                            <td style='text-align:center;white-space: normal;'>
                                                                <?php
                                                                if ($firstItem->indikatorTriwulan) {
                                                                    foreach ($firstItem->indikatorTriwulan as $triwulan) {
                                                                        echo "Trw ({$triwulan->reftriwulan_id}), = {$triwulan->triwulan_target_rkt} <br>";
                                                                    }
                                                                } else {
                                                                    echo '-';
                                                                }
                                                                ?>
                                                            </td>
                                                        </tr>
                                                        <!-- Render the remaining rows in the group without repeating the Kegiatan name -->
                                                        <?php foreach (array_slice($subkegiatanGroup, 1) as $item): ?>
                                                            <tr>
                                                                <td style='text-align:center;white-space: normal;'>
                                                                    <?= $item->refSubkegiatan ? $item->refSubkegiatan->nama_subkegiatan : '-'; ?>
                                                                </td>
                                                                <td style='text-align:center;white-space: normal;'>
                                                                    <?= $item->subkegiatan_anggaran ? number_format($item->subkegiatan_anggaran, 0, ',', '.') : '-'; ?>
                                                                </td>
                                                                <td style='text-align:center;white-space: normal;'>
                                                                    <?= $item->uraian_indikatorsubkegiatan ? $item->uraian_indikatorsubkegiatan : '-'; ?>
                                                                </td>
                                                                <td style='text-align:center;white-space: normal;'>
                                                                    <?php
                                                                    if ($item->indikatorTriwulan) {
                                                                        foreach ($item->indikatorTriwulan as $triwulan) {
                                                                            echo "Trw ({$triwulan->reftriwulan_id}), = {$triwulan->triwulan_target_rkt} <br>";
                                                                        }
                                                                    } else {
                                                                        echo '-';
                                                                    }
                                                                    ?>
                                                                </td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    <?php endforeach; ?>
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