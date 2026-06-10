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

$pdfUrl = Url::to(['laporan/download-pdf-rkpd']); // Endpoint to generate and download PDF
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
        var title = 'Laporan Evaluasi RKPD " . $selectedPeriodValue . " - ' + toSentenceCase(" . json_encode($nama_skpd) . "); // Dynamic title based on period and SKPD
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
                            <i class="fas fa-pen-fancy"></i> Laporan Evaluasi RKPD - <?= Html::encode(ucwords(strtolower($nama_skpd))) ?>
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <!-- Dropdown filter berdasarkan refperiode_id -->
                                <?= \yii\helpers\Html::beginForm(['index-laporan-evaluasi-rkpd'], 'get', ['class' => 'form-inline']); ?>
                                <div class="form-group">
                                    <?= \yii\helpers\Html::label('Pilih Periode:', 'refperiode_id', ['class' => 'mr-2']); ?>
                                    <?= \yii\helpers\Html::dropDownList(
                                        'refperiode_id',
                                        $selectedPeriodId,
                                        \yii\helpers\ArrayHelper::map($periodeList, 'refperiode_id', 'periode'), // Mapping periodeList
                                        [
                                            'class' => 'form-control',
                                            'prompt' => 'Pilih Periode',
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
                                    <!-- <button id="btnExcel" class="btn btn-success">Excel</button> -->
                                </div>

                                <!-- <div id="previewBox" class="border p-3" style="min-height: 400px;">
                                    <p class="text-muted">Data Periode Ini Tidak Tersedia</p>
                                </div> -->
                                <!-- else empty -->
                                <!-- Preview box -->
                                <div id="previewBox" class="border p-3" style="min-height: 400px;">
                                    <div class="title">
                                        <img src="<?= Url::base(true) ?>/lightapp/assets/images/bappeda.png" alt="logo image" height="60" width="60" class="logo-lg" />
                                        <h5 class="tbdata">
                                            <center>Evaluasi RKPD - <?= Html::encode(ucwords(strtolower($nama_skpd))) ?><br>Periode <?= $selectedPeriodValue ?></center>
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
                                                            <td width='25px' class='header' style="white-space: normal;" rowspan="2">No</td>
                                                            <td width='150px' class='header' style="white-space: normal;" rowspan="2">Sasaran Renstra</td>
                                                            <td width='150px' class='header' style="white-space: normal;" rowspan="2">Kode</td>
                                                            <td width='150px' class='header' style="white-space: normal;" rowspan="2">Urusan/Bidang Urusan/Program/Kegiatan</td>
                                                            <td width='150px' class='header' style="white-space: normal;" rowspan="2">Indikator Program/Indikator Kegiatan</td>
                                                            <td width='150px' class='header' style="white-space: normal;" rowspan="2" colspan="2">Target Renstra/Anggaran Renstra</td>
                                                            <td width='150px' class='header' style="white-space: normal;" rowspan="2" colspan="2">Realisasi Capaian(refperiode_id - 1)</td>
                                                            <td width='150px' class='header' style="white-space: normal;" rowspan="2" colspan="2">Target Renstra/Anggaran Renstra</td>
                                                            <td width='150px' class='header' style="white-space: normal;" colspan="8">Realisasi Capaian Triwulan</td>
                                                            <td width='150px' class='header' style="white-space: normal;" rowspan="2" colspan="2">Realisasi Kinerja dan Anggaran yang di Evaluasi</td>
                                                            <td width='150px' class='header' style="white-space: normal;" rowspan="2" colspan="2">Realisasi Kinerja dan Anggaran Sampai Tahun</td>
                                                            <td width='150px' class='header' style="white-space: normal;" rowspan="2" colspan="2">Tingkat Capaian KInerja dan Realisasi Anggaran</td>
                                                            <td width='150px' class='header' style="white-space: normal;" rowspan="2" colspan="2">SKPD</td>
                                                        </tr>
                                                        <tr>
                                                            <td width='25px' class='header' style="white-space: normal;" colspan="2">I</td>
                                                            <td width='25px' class='header' style="white-space: normal;" colspan="2">II</td>
                                                            <td width='25px' class='header' style="white-space: normal;" colspan="2">III</td>
                                                            <td width='25px' class='header' style="white-space: normal;" colspan="2">IV</td>
                                                        </tr>
                                                        <tr>
                                                            <td width='25px' class='header' style="white-space: normal;" rowspan="2">1</td>
                                                            <td width='25px' class='header' style="white-space: normal;" rowspan="2">2</td>
                                                            <td width='25px' class='header' style="white-space: normal;" rowspan="2">3</td>
                                                            <td width='25px' class='header' style="white-space: normal;" rowspan="2">4</td>
                                                            <td width='25px' class='header' style="white-space: normal;" rowspan="2">5</td>
                                                            <td width='25px' class='header' style="white-space: normal;" colspan="2">6</td>
                                                            <td width='25px' class='header' style="white-space: normal;" colspan="2">7</td>
                                                            <td width='25px' class='header' style="white-space: normal;" colspan="2">8</td>
                                                            <td width='25px' class='header' style="white-space: normal;" colspan="2">9</td>
                                                            <td width='25px' class='header' style="white-space: normal;" colspan="2">10</td>
                                                            <td width='25px' class='header' style="white-space: normal;" colspan="2">11</td>
                                                            <td width='25px' class='header' style="white-space: normal;" colspan="2">12</td>
                                                            <td width='25px' class='header' style="white-space: normal;" colspan="2">13</td>
                                                            <td width='25px' class='header' style="white-space: normal;" colspan="2">14</td>
                                                            <td width='25px' class='header' style="white-space: normal;" colspan="2">15</td>
                                                            <td width='25px' class='header' style="white-space: normal;" rowspan="2">16</td>
                                                        </tr>
                                                        <tr>
                                                            <td width='25px' class='header' style="white-space: normal;">K</td>
                                                            <td width='25px' class='header' style="white-space: normal;">Rp</td>
                                                            <td width='25px' class='header' style="white-space: normal;">K</td>
                                                            <td width='25px' class='header' style="white-space: normal;">Rp</td>
                                                            <td width='25px' class='header' style="white-space: normal;">K</td>
                                                            <td width='25px' class='header' style="white-space: normal;">Rp</td>
                                                            <td width='25px' class='header' style="white-space: normal;">K</td>
                                                            <td width='25px' class='header' style="white-space: normal;">Rp</td>
                                                            <td width='25px' class='header' style="white-space: normal;">K</td>
                                                            <td width='25px' class='header' style="white-space: normal;">Rp</td>
                                                            <td width='25px' class='header' style="white-space: normal;">K</td>
                                                            <td width='25px' class='header' style="white-space: normal;">Rp</td>
                                                            <td width='25px' class='header' style="white-space: normal;">K</td>
                                                            <td width='25px' class='header' style="white-space: normal;">Rp</td>
                                                            <td width='25px' class='header' style="white-space: normal;">K</td>
                                                            <td width='25px' class='header' style="white-space: normal;">Rp</td>
                                                            <td width='25px' class='header' style="white-space: normal;">K</td>
                                                            <td width='25px' class='header' style="white-space: normal;">Rp</td>
                                                            <td width='25px' class='header' style="white-space: normal;">K</td>
                                                            <td width='25px' class='header' style="white-space: normal;">Rp</td>
                                                        </tr>
                                                    </thead>
                                                    <tbody>

                                                    </tbody>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>

                                    <!--  -->
                                </div>
                                <!-- end if empty -->
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