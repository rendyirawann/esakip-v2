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
        border-collapse: collapse;
        font-family: "Bookman Old Style", "Verdana";
        font-size: 11px;
    }

    .tbdata th {
        height: 40px;
        background: rgb(0, 160, 221);
        text-align: center;
        border: 0.2px solid #cfcfcf;
        color: #ffffff;
        padding: 2px;
    }

    .tbdata td {
        padding: 2px;
        border: 0.2px solid #cfcfcf;
        vertical-align: top;
    }

    .tengah {
        text-align: center;
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

    .tbdata {
        border-collapse: collapse;
        font-family: 'Public Sans', sans-serif;
        font-size: 11px;
    }

    .tbdata th {
        height: 40px;
        background: rgb(0, 160, 221);
        text-align: center;
        border: 0.2px solid #cfcfcf;
        color: #ffffff;
        padding: 2px;
    }

    .tbdata td {
        padding: 2px;
        border: 0.2px solid #cfcfcf;
        vertical-align: top;
    }

    .tengah {
        text-align: center;
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
                            <i class="fas fa-pen-fancy"></i> Laporan Pagu dan Realisasi Anggaran - <?= Html::encode(ucwords(strtolower($nama_skpd))) ?>
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
                                                <center>Pagu dan Realisasi Anggaran - <?= Html::encode(ucwords(strtolower($nama_skpd))) ?><br>Periode <?= $selectedPeriodValue ?></center>
                                            </h5>
                                        </div>
                                        <hr>
                                        <br>
                                        <div id="content">
                                            <h3 class="tbdata">
                                                <center><b>Pagu dan Realisasi Anggaran Tahun <?= $selectedPeriodValue ?> <?= Html::encode(ucwords(strtolower($nama_skpd))) ?></b></center>
                                            </h3>
                                            <table width="100%" border=0 cellpadding=0 cellspacing=0 class="tbdata">
                                                <thead>
                                                    <tr>
                                                        <th rowspan=2 width="10%">No</th>
                                                        <th rowspan=2 width="30%">Program</th>
                                                        <th rowspan=2 width="10%">Pagu Anggaran Tahun <?= $selectedPeriodValue ?></th>
                                                        <th colspan=2>Triwulan 1</th>
                                                        <th colspan=2>Triwulan 2</th>
                                                        <th colspan=2>Triwulan 3</th>
                                                        <th colspan=2>Triwulan 4</th>
                                                    </tr>
                                                    <tr>
                                                        <th height="20">Realisasi</th>
                                                        <th>%</th>
                                                        <th>Realisasi</th>
                                                        <th>%</th>
                                                        <th>Realisasi</th>
                                                        <th>%</th>
                                                        <th>Realisasi</th>
                                                        <th>%</th>
                                                    </tr>
                                                    <tr>
                                                        <th>1</th>
                                                        <th>2</th>
                                                        <th>3</th>
                                                        <th>4</th>
                                                        <th>5</th>
                                                        <th>6</th>
                                                        <th>7</th>
                                                        <th>8</th>
                                                        <th>9</th>
                                                        <th>10</th>
                                                        <th>11</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td colspan="11">ambil refsasaranrenstra_id dari sakip_indikatorcascadingsubkegiatan terkait refSasaranrenstra->uraian_sasaranrenstra</td>
                                                    </tr>
                                                    <tr>
                                                        <td align='center'>Nomor</td>
                                                        <td>tampilkan list dari refcascadingprogram_id pada sakip_indikatorcascadingsubkegiatan lalu lihat ada refprogram_id apa saja refProgram->nama_program </td>
                                                        <td align='right'>anggaran_pk_p</td>
                                                        <td align='right'>penyerapan_anggaran reftriwulan_id = 1 dari sakip_indikatorcascadingsubkegiatan_triwulan</td>
                                                        <td align='center'>triwulan_realisasi reftriwulan_id = 1 dari sakip_indikatorcascadingsubkegiatan_triwulan</td>
                                                        <td align='right'>penyerapan_anggaran reftriwulan_id = 2 dari sakip_indikatorcascadingsubkegiatan_triwulan</td>
                                                        <td align='center'>triwulan_realisasi reftriwulan_id = 2 dari sakip_indikatorcascadingsubkegiatan_triwulan</td>
                                                        <td align='right'>penyerapan_anggaran reftriwulan_id = 3 dari sakip_indikatorcascadingsubkegiatan_triwulan</td>
                                                        <td align='center'>triwulan_realisasi reftriwulan_id = 3 dari sakip_indikatorcascadingsubkegiatan_triwulan</td>
                                                        <td align='right'>penyerapan_anggaran reftriwulan_id = 4 dari sakip_indikatorcascadingsubkegiatan_triwulan</td>
                                                        <td align='center'>triwulan_realisasi reftriwulan_id = 4 dari sakip_indikatorcascadingsubkegiatan_triwulan</td>

                                                    </tr>

                                                    <tr>
                                                        <td colspan=2 align='center'><b>Total</b></td>
                                                        <td align='right'><b>a</b></td>
                                                        <td align='right'><b>b</b></td>
                                                        <td align='center'><b>c</b></td>
                                                        <td align='right'><b>d</b></td>
                                                        <td align='center'><b>e</b></td>
                                                        <td align='right'><b>f</b></td>
                                                        <td align='center'><b>g</b></td>
                                                        <td align='right'><b>h</b></td>
                                                        <td align='center'><b>i</b></td>
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