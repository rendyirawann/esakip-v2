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
                                                <center>Rencana Aksi - <?= Html::encode(ucwords(strtolower($nama_skpd))) ?><br>Periode <?= $selectedPeriodValue ?></center>
                                            </h5>
                                        </div>
                                        <hr>
                                        <br>
                                        <div id='content'>
                                            <h3 class='judultabel'>
                                                <center>Rencana Aksi <?= Html::encode(ucwords(strtolower($nama_skpd))) ?><br>Periode <?= $selectedPeriodValue ?></center>
                                            </h3>
                                            <table width='100%' border=0 cellpadding=0 cellspacing=0 class='tbdata' align='center'>
                                                <thead>
                                                    <tr>
                                                        <th width='3%'>No</th>
                                                        <th width='8%'>Sasaran Strategis</th>
                                                        <th width='8%' colspan="2">Indikator Kinerja</th>
                                                        <th width='5%'>Satuan</th>
                                                        <th width='8%'>Target</th>
                                                        <th width='8%'>Program</th>
                                                        <th width='8%'>Anggaran</th>
                                                        <th width='8%'>Kegiatan</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $currentNumber = 0;
                                                    $lastSasaran = '';

                                                    foreach ($sasaranRenstra as $index => $item) {
                                                        $indikators = $item->indikatorSasaran;
                                                        $sasaranCode = $item->uraian_sasaranrenstra;

                                                        if ($lastSasaran !== $sasaranCode) {
                                                            $currentNumber++;
                                                            $lastSasaran = $sasaranCode;
                                                            $indicatorIndex = 1;
                                                        }

                                                        if (!empty($indikators)) {
                                                            foreach ($indikators as $i => $indikator) {
                                                                $programCount = count($indikator->cascadingPrograms);

                                                    ?>
                                                                <tr>
                                                                    <?php if ($i === 0): ?>
                                                                        <td rowspan="??" style='text-align:center;white-space: normal;'><?= $index + 1 ?></td>
                                                                        <td rowspan="??" style="white-space: normal;"><?= Html::encode(strip_tags($item->uraian_sasaranrenstra)) ?></td>
                                                                    <?php endif; ?>

                                                                    <td rowspan="??" style='text-align:center;white-space: normal;'><?= $currentNumber . '.' . $indicatorIndex ?></td>
                                                                    <td rowspan="??" style="white-space: normal;"><?= Html::encode(strip_tags($indikator->uraian_indikatorsasaranrenstra)) ?></td>
                                                                    <td rowspan="??" style='text-align:center;white-space: normal;'><?= Html::encode(strip_tags($indikator->indikatorsasaranrenstra_satuan)) ?></td>
                                                                    <td rowspan="??">
                                                                        <?php for ($triwulan = 1; $triwulan <= 4; $triwulan++): ?>
                                                                            <?php
                                                                            $triwulanTarget = SakipIndikatorsasaranrenstraTriwulan::find()
                                                                                ->select('triwulan_target_rkt')
                                                                                ->where([
                                                                                    'refindikatorsasaranrenstra_id' => $indikator->refindikatorsasaranrenstra_id,
                                                                                    'refperiode_id' => $refperiode_id,
                                                                                    'refskpd_id' => $refskpd_id,
                                                                                    'reftriwulan_id' => $triwulan
                                                                                ])
                                                                                ->scalar();
                                                                            ?>
                                                                            Tw<?= $triwulan ?>=<?= Html::encode($triwulanTarget) ?><br>
                                                                        <?php endfor; ?>
                                                                    </td>

                                                                    <?php if (!empty($indikator->cascadingPrograms)): ?>
                                                                        <?php foreach ($indikator->cascadingPrograms as $programIndex => $cascadingProgram): ?>
                                                                            <?php
                                                                            $programData = SakipIndikatorcascadingsubkegiatan::find()
                                                                                ->where(['refcascadingprogram_id' => $cascadingProgram->refcascadingprogram_id])
                                                                                ->select(['sum(anggaran_rkt) as totalPagu'])
                                                                                ->asArray()
                                                                                ->one();
                                                                            $kegiatanCount = count($cascadingProgram->cascadingKegiatans);
                                                                            ?>
                                                                            <?php if ($programIndex > 0): ?>
                                                                </tr>
                                                                <tr>
                                                                <?php endif; ?>
                                                                <td rowspan="<?= $kegiatanCount ?>" style="white-space: normal;"><?= Html::encode($cascadingProgram->refProgram->nama_program) ?></td>
                                                                <td rowspan="<?= $kegiatanCount ?>" style='text-align:center;white-space: normal;'><?= $programData['totalPagu'] ?? '0' ?></td>

                                                                <?php foreach ($cascadingProgram->cascadingKegiatans as $kegiatanIndex => $kegiatan): ?>
                                                                    <?php if ($kegiatanIndex > 0): ?>
                                                                </tr>
                                                                <tr>
                                                                <?php endif; ?>
                                                                <td style="white-space: normal;"><?= Html::encode($kegiatan->refKegiatan->nama_kegiatan) ?></td>
                                                            <?php endforeach; ?>
                                                        <?php endforeach; ?>
                                                    <?php else: ?>
                                                        <td colspan="2">Tidak ada program terkait</td>
                                                        <td>0</td>
                                                    <?php endif; ?>
                                                                </tr>
                                                                <?php $indicatorIndex++; ?>
                                                            <?php } ?>
                                                        <?php } else { ?>
                                                            <tr>
                                                                <td colspan="10" class="text-center">Tidak ada indikator</td>
                                                            </tr>
                                                    <?php }
                                                    } ?>
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