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

    .title {
        border-collapse: collapse;
        font-family: 'Public Sans', sans-serif;
        /* Menggunakan font Public Sans */
        font-size: 11px;
        text-align: center;
    }

    #halamanlaporan {
        font-family: 'Public Sans', sans-serif;
        font-size: 0.35cm;
        width: 600px;
        margin: auto;
    }

    #logo {
        width: 100%;
        height: 2.5cm;
        background: url(/backend/web/lightapp/assets/images/bappeda.png) no-repeat;
        background-size: 2.5cm 2cm;
        background-position: center;
    }

    .isilaporan {
        font-size: 0.35cm;
        line-height: 1.3;
    }

    .isilaporan h3 {
        font-weight: normal;
        font-size: 0.35cm;
        text-align: center;
        margin-bottom: 20px;
    }

    .isilaporan h4 {
        font-size: 0.35cm;
        font-weight: bold;
        text-align: center;
    }

    .isilaporan h5 {
        font-size: 0.35cm;
        font-weight: bold;
        text-align: center;
        margin-top: 20px;
        margin-bottom: 20px;
    }

    .isilaporan p {
        text-align: justify;
    }

    .tblPihak {
        width: 100%;
        font-size: 0.35cm;
    }

    .tblPihak td {
        width: 50%;
        text-align: center;
    }

    .tbdata {
        width: 100%;
        font-size: 0.35cm;
        margin-top: 20px;
        border-collapse: collapse;
    }

    .tbdata th {
        padding: 5px;
        text-align: center;
    }

    .tbdata td {
        padding-left: 3px;
        padding-right: 3px;
        vertical-align: top;
    }

    .tengah {
        text-align: center;
    }

    .kanan {
        text-align: right;
    }

    img {
        display: block
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

$pdfUrl = Url::to(['laporan/download-pdf-tapkin']); // Endpoint to generate and download PDF
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

    #halamanlaporan {
        font-family: 'Public Sans', sans-serif;
        font-size: 0.35cm;
        width: 600px;
        margin: auto;
    }

    #logo {
        width: 100%;
        height: 2.5cm;
        background: url(/backend/web/lightapp/assets/images/bappeda.png) no-repeat;
        background-size: 2.5cm 2cm;
        background-position: center;
    }

    .isilaporan {
        font-size: 0.35cm;
        line-height: 1.3;
    }

    .isilaporan h3 {
        font-weight: normal;
        font-size: 0.35cm;
        text-align: center;
        margin-bottom: 20px;
    }

    .isilaporan h4 {
        font-size: 0.35cm;
        font-weight: bold;
        text-align: center;
    }

    .isilaporan h5 {
        font-size: 0.35cm;
        font-weight: bold;
        text-align: center;
        margin-top: 20px;
        margin-bottom: 20px;
    }

    .isilaporan p {
        text-align: justify;
    }

    .tblPihak {
        width: 100%;
        font-size: 0.35cm;
    }

    .tblPihak td {
        width: 50%;
        text-align: center;
    }

    .tbdata {
        width: 100%;
        font-size: 0.35cm;
        margin-top: 20px;
        border-collapse: collapse;
    }

    .tbdata th {
        padding: 5px;
        text-align: center;
    }

    .tbdata td {
        padding-left: 3px;
        padding-right: 3px;
        vertical-align: top;
    }

    .tengah {
        text-align: center;
    }

    .kanan {
        text-align: right;
    }

    img {
        display: block
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
        var title = 'Laporan Perjanjian Kinerja " . $selectedPeriodValue . " - ' + toSentenceCase(" . json_encode($nama_skpd) . "); // Dynamic title based on period and SKPD
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
                            <i class="fas fa-pen-fancy"></i> Laporan Perjanjian Kinerja - <?= Html::encode(ucwords(strtolower($nama_skpd))) ?>
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <!-- Form for selecting period and date -->
                                <?= \yii\helpers\Html::beginForm(['index-laporan-tapkin-dev'], 'get', ['class' => 'form-inline']); ?>
                                <div class="form-group">
                                    <?= \yii\helpers\Html::label('Pilih Periode:', 'refperiode_id', ['class' => 'mr-2']); ?>
                                    <?= \yii\helpers\Html::dropDownList(
                                        'refperiode_id',
                                        $selectedPeriodId,
                                        \yii\helpers\ArrayHelper::map($periodeList, 'refperiode_id', 'periode'),
                                        [
                                            'class' => 'form-control',
                                            'prompt' => 'Pilih Periode',
                                        ]
                                    ); ?>
                                </div>
                                <div class="form-group ml-3">
                                    <?= \yii\helpers\Html::label('Pilih SKPD:', 'refskpd_id', ['class' => 'mr-2']); ?>
                                    <?= \yii\helpers\Html::dropDownList(
                                        'refskpd_id',
                                        $selectedSkpdId,
                                        $skpdList,
                                        [
                                            'class' => 'form-control',
                                            'prompt' => 'Pilih SKPD',
                                            'onchange' => 'this.form.submit()'
                                        ]
                                    ); ?>
                                </div>
                                <div class="form-group ml-3">
                                    <?= \yii\helpers\Html::label('Pilih Penjabat SKPD:', 'refpenjabatskpd_id', ['class' => 'mr-2']); ?>
                                    <?= \yii\helpers\Html::dropDownList(
                                        'refpenjabatskpd_id',
                                        $refpenjabatskpd_id, // Pass the selected ID
                                        \yii\helpers\ArrayHelper::map($penjabatSkpdList, 'refpenjabatskpd_id', 'nama_penjabat'),
                                        [
                                            'class' => 'form-control',
                                            'prompt' => 'Kepala Penjabat',
                                        ]
                                    ); ?>
                                </div>
                                <div class="form-group ml-3">
                                    <?= \yii\helpers\Html::label('Pilih Tanggal:', 'selected_date', ['class' => 'mr-2']); ?>
                                    <?= \yii\helpers\Html::input('date', 'selected_date', null, [
                                        'class' => 'form-control',
                                        'id' => 'selectedDate',
                                        'onchange' => 'updatePreviewDate()' // JS function to update the preview date
                                    ]); ?>
                                </div>
                                <div class="form-group ml-3">
                                    <?= \yii\helpers\Html::submitButton('Tampilkan', ['class' => 'btn btn-primary']); ?>
                                </div>
                                <?= \yii\helpers\Html::endForm(); ?>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-12">
                                <div class="btn-group mb-3" role="group">
                                    <button id="btnPrint" class="btn btn-primary">Print</button>
                                    <!-- <button id="btnPdf" class="btn btn-danger">PDF</button> -->
                                    <!-- <button id="btnExcel" class="btn btn-success">Excel</button> -->
                                </div>
                                <?php if (empty($sasaranRenstra)): ?>
                                    <div id="previewBox" class="border p-3" style="min-height: 400px;">
                                        <p class="text-muted">Data Periode atau SKPD Ini Tidak Tersedia</p>
                                    </div>
                                <?php else: ?>
                                    <!-- Preview box -->
                                    <div id="previewBox" class="border p-3" style="min-height: 400px;">
                                        <?php if ($refpenjabatskpd_id === null || $refpenjabatskpd_id === ''): ?>
                                            <table id="halamanlaporan">
                                                <tr>
                                                    <td>
                                                        <div id="logo" align="center">
                                                            <img src="<?= Url::base(true) ?>/lightapp/assets/images/bappeda.png" alt="logo image" height="60" width="60" class="logo-lg" />
                                                            <hr>
                                                            <br>
                                                        </div>
                                                        <div class="isilaporan">
                                                            <h3>PEMERINTAH KABUPATEN DELI SERDANG</h3>
                                                            <h4>PERNYATAAN PERJANJIAN KINERJA</h4>
                                                            <h4><?= Html::encode($nama_skpd) ?> PEMERINTAH KABUPATEN DELI SERDANG</h4>
                                                            <h5>PERJANJIAN KINERJA TAHUN <?= Html::encode($selectedPeriodValue) ?></h5>
                                                            <p>
                                                                Dalam rangka mewujudkan manajemen pemerintahan yang efektif, transparan, dan akuntabel serta berorientasi pada hasil, kami yang bertanda tangan di bawah ini:
                                                            </p>
                                                            <p>

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
                                                            </p>
                                                            <p>Selanjutnya disebut sebagai PIHAK PERTAMA</p>
                                                            <p>
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
                                                            </p>
                                                            <p>Selaku atasan langsung pihak pertama</p>
                                                            <p>Selanjutnya disebut sebagai PIHAK KEDUA</p>
                                                            <p>
                                                                Pihak pertama berjanji akan mewujudkan target kinerja yang seharusnya sesuai lampiran perjanjian ini, dalam rangka mencapai target kinerja jangka menengah seperti yang telah ditetapkan dalam dokumen perencanaan. Keberhasilan dan kegagalan pencapaian target kinerja tersebut menjadi tanggung jawab kami.
                                                            </p>
                                                            <p>
                                                                Pihak kedua akan melakukan supervisi yang diperlukan serta akan melakukan evaluasi terhadap capaian kinerja dari perjanjian ini dan mengambil tindakan yang diperlukan dalam rangka pemberian penghargaan dan sanksi.
                                                            </p>
                                                            <p>
                                                            <table class="tblPihak">
                                                                <tr>
                                                                    <td></td>
                                                                    <td>Lubuk Pakam, <span id="dynamicDate"><?= date('d F Y') ?></span></td>
                                                                </tr>
                                                                <tr>
                                                                    <td colspan="2" height="15px"></td>
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
                                                            </p>
                                                            <br><br><br><br>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="isilaporan">
                                                            <h5>PERJANJIAN KINERJA</h5>
                                                            <p>
                                                            <table style="font-weight: bold; text-transform: uppercase; font-size: 0.35cm; width: 100%;">
                                                                <tr>
                                                                    <td width="200px"><b>SKPD</b></td>
                                                                    <td><b>: <?= Html::encode($nama_skpd) ?></b></td>
                                                                </tr>
                                                                <tr>
                                                                    <td><b>TAHUN ANGGARAN</b></td>
                                                                    <td><b>: <?= $selectedPeriodValue ?></b></td>
                                                                </tr>
                                                            </table>
                                                            </p>
                                                            <table class="tbdata" border="1" style="border-collapse: collapse; width: 100%; border: 1px solid black;">
                                                                <thead>
                                                                    <tr>
                                                                        <th style="border: 1px solid black;">NO</th>
                                                                        <th style="border: 1px solid black;">SASARAN STRATEGIS</th>
                                                                        <th style="border: 1px solid black;" colspan="2">INDIKATOR KINERJA</th>
                                                                        <th style="border: 1px solid black;">SATUAN</th>
                                                                        <th style="border: 1px solid black;">TARGET</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <tr>
                                                                        <td class="tengah" style="border: 1px solid black;">(1)</td>
                                                                        <td class="tengah" style="border: 1px solid black;">(2)</td>
                                                                        <td class="tengah" style="border: 1px solid black;" colspan="2">(3)</td>
                                                                        <td class="tengah" style="border: 1px solid black;">(4)</td>
                                                                        <td class="tengah" style="border: 1px solid black;">(5)</td>
                                                                    </tr>
                                                                    <?php
                                                                    $sasaranCounter = 1;
                                                                    foreach ($sasaranRenstra as $sasaran):
                                                                        $indikatorCounter = 1;
                                                                        $firstIndikatorRow = true;
                                                                        foreach ($indikators as $indikator):
                                                                            if ($indikator->refsasaranrenstra_id == $sasaran->refsasaranrenstra_id):
                                                                                $formattedNumber = "{$sasaranCounter}.{$indikatorCounter}";
                                                                    ?>
                                                                                <tr>
                                                                                    <td class="tengah" style="border: 1px solid black;"><?= $firstIndikatorRow ? $sasaranCounter : '' ?></td>
                                                                                    <td class="left" style="border: 1px solid black;"><?= $firstIndikatorRow ? Html::encode($sasaran->uraian_sasaranrenstra) : '' ?></td>
                                                                                    <td class="tengah" style="border: 1px solid black;"><?= $formattedNumber ?></td>
                                                                                    <td class="left" style="border: 1px solid black;"><?= Html::encode($indikator->uraian_indikatorsasaranrenstra) ?></td>
                                                                                    <td class="tengah" style="border: 1px solid black;"><?= Html::encode($indikator->indikatorsasaranrenstra_satuan) ?></td>
                                                                                    <td class="tengah" style="border: 1px solid black;"><?= Html::encode($indikator->indikatorsasaranrenstra_target) ?></td>
                                                                                </tr>
                                                                    <?php
                                                                                $indikatorCounter++;
                                                                                $firstIndikatorRow = false;
                                                                            endif;
                                                                        endforeach;
                                                                        $sasaranCounter++;
                                                                    endforeach;
                                                                    ?>
                                                                </tbody>
                                                            </table>

                                                        </div>
                                                        <br><br><br><br>
                                                    </td>
                                                </tr>


                                                <tr>
                                                    <td>
                                                        <table class="tbdata" style="border: 1px solid black; border-collapse: collapse;">
                                                            <thead>
                                                                <tr>
                                                                    <th style="border: 1px solid black;">No</th>
                                                                    <th style="border: 1px solid black;">Program</th>
                                                                    <th style="border: 1px solid black;">Anggaran (Rp)</th>
                                                                    <th style="border: 1px solid black;">Keterangan</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php
                                                                $programCounter = 1;
                                                                $totalAnggaranpkpSum = 0;

                                                                foreach ($programs as $program):
                                                                    // Calculate the total anggaran_pk_p for the program
                                                                    $subkegiatanPkpSum = (new \yii\db\Query())
                                                                        ->select(['SUM(anggaran_pk_p) AS total_anggaranpkp'])
                                                                        ->from('sakip_indikatorcascadingsubkegiatan')
                                                                        ->where(['refprogram_id' => $program->refprogram_id, 'refskpd_id' => $refskpd_id])
                                                                        ->scalar();

                                                                    $totalAnggaranpkp = $subkegiatanPkpSum !== null ? $subkegiatanPkpSum : 0;
                                                                    $totalAnggaranpkpSum += $totalAnggaranpkp;
                                                                ?>
                                                                    <tr>
                                                                        <td class="tengah" style="border: 1px solid black;"><?= $programCounter++ ?></td>
                                                                        <td class="left" style="border: 1px solid black;"><?= Html::encode($program->refProgram->nama_program) ?></td>
                                                                        <td class="text-right" style="border: 1px solid black;"><?= 'Rp. ' . number_format($totalAnggaranpkp, 0, ',', '.') ?></td>
                                                                        <td style="border: 1px solid black;"></td>
                                                                    </tr>
                                                                <?php endforeach; ?>

                                                                <tr>
                                                                    <th class="tengah" colspan="2" style="border: 1px solid black;">Total</th>
                                                                    <th class="text-right" style="border: 1px solid black;"><?= 'Rp. ' . number_format($totalAnggaranpkpSum, 0, ',', '.') ?></th>
                                                                    <th style="border: 1px solid black;"></th>
                                                                </tr>
                                                            </tbody>
                                                        </table>

                                                        <p>
                                                        <table class="tblPihak">
                                                            <tr>
                                                                <td></td>
                                                                <td>Lubuk Pakam, <span id="dynamicDate"><?= date('d F Y') ?></span></td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="2" height="15px"></td>
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
                                                        </p>
                                                    </td>
                                                </tr>
                                            </table>
                                        <?php else: ?>
                                            <table id="halamanlaporan">
                                                <tr>
                                                    <td>
                                                        <div id="logo" align="center">
                                                            <img src="<?= Url::base(true) ?>/lightapp/assets/images/bappeda.png" alt="logo image" height="60" width="60" class="logo-lg" />
                                                            <hr>
                                                            <br>
                                                        </div>
                                                        <div class="isilaporan">
                                                            <h3>PEMERINTAH KABUPATEN DELI SERDANG</h3>
                                                            <h4>PERNYATAAN PERJANJIAN KINERJA</h4>
                                                            <h4><?= Html::encode($nama_skpd) ?> PEMERINTAH KABUPATEN DELI SERDANG</h4>
                                                            <h5>PERJANJIAN KINERJA TAHUN <?= Html::encode($selectedPeriodValue) ?></h5>
                                                            <p>
                                                                Dalam rangka mewujudkan manajemen pemerintahan yang efektif, transparan, dan akuntabel serta berorientasi pada hasil, kami yang bertanda tangan di bawah ini:
                                                            </p>
                                                            <p>

                                                            <table>
                                                                <tr>
                                                                    <td width="125px">Nama</td>
                                                                    <td>: <?= Html::encode($penjabatSkpd->nama_penjabat ?? 'N/A') ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Jabatan</td>
                                                                    <td>: <?= Html::encode($penjabatSkpd->jabatan_eselon ?? 'N/A') ?></td>
                                                                </tr>
                                                            </table>
                                                            </p>
                                                            <p>Selanjutnya disebut sebagai PIHAK PERTAMA</p>
                                                            <p>
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
                                                            </p>
                                                            <p>Selaku atasan langsung pihak pertama</p>
                                                            <p>Selanjutnya disebut sebagai PIHAK KEDUA</p>
                                                            <p>
                                                                Pihak pertama berjanji akan mewujudkan target kinerja yang seharusnya sesuai lampiran perjanjian ini, dalam rangka mencapai target kinerja jangka menengah seperti yang telah ditetapkan dalam dokumen perencanaan. Keberhasilan dan kegagalan pencapaian target kinerja tersebut menjadi tanggung jawab kami.
                                                            </p>
                                                            <p>
                                                                Pihak kedua akan melakukan supervisi yang diperlukan serta akan melakukan evaluasi terhadap capaian kinerja dari perjanjian ini dan mengambil tindakan yang diperlukan dalam rangka pemberian penghargaan dan sanksi.
                                                            </p>
                                                            <p>
                                                            <table class="tblPihak">
                                                                <tr>
                                                                    <td></td>
                                                                    <td>Lubuk Pakam, <span id="dynamicDate"><?= date('d F Y') ?></span></td>
                                                                </tr>
                                                                <tr>
                                                                    <td colspan="2" height="15px"></td>
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
                                                                    <td><?= Html::encode($penjabatSkpd->nama_penjabat ?? 'N/A') ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <td></td>
                                                                    <td>NIP. <?= Html::encode($penjabatSkpd->nip_penjabat ?? 'N/A') ?></td>
                                                                </tr>
                                                            </table>
                                                            </p>
                                                            <br><br><br><br>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="isilaporan">
                                                            <h5>PERJANJIAN KINERJA</h5>
                                                            <p>
                                                            <table style="font-weight: bold; text-transform: uppercase; font-size: 0.35cm; width: 100%;">
                                                                <tr>
                                                                    <td width="200px"><b>SKPD</b></td>
                                                                    <td><b>: <?= Html::encode($nama_skpd) ?></b></td>
                                                                </tr>
                                                                <tr>
                                                                    <td><b>TAHUN ANGGARAN</b></td>
                                                                    <td><b>: <?= $selectedPeriodValue ?></b></td>
                                                                </tr>
                                                            </table>
                                                            </p>
                                                            <table class="tbdata" border="1" style="border-collapse: collapse; width: 100%; border: 1px solid black;">
                                                                <?php if ($refeselonId === 1): ?>
                                                                    <thead>
                                                                        <tr>
                                                                            <th style="border: 1px solid black;">NO</th>
                                                                            <th style="border: 1px solid black;">SASARAN PROGRAM/KEGIATAN</th>
                                                                            <th style="border: 1px solid black;">INDIKATOR KINERJA</th>
                                                                            <th style="border: 1px solid black;">SATUAN</th>
                                                                            <th style="border: 1px solid black;">TARGET</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <tr>
                                                                            <td class="tengah" style="border: 1px solid black;">(1)</td>
                                                                            <td class="tengah" style="border: 1px solid black;">(2)</td>
                                                                            <td class="tengah" style="border: 1px solid black;">(3)</td>
                                                                            <td class="tengah" style="border: 1px solid black;">(4)</td>
                                                                            <td class="tengah" style="border: 1px solid black;">(5)</td>
                                                                        </tr>
                                                                        <?php
                                                                        $programCounter = 1; // Counter untuk program
                                                                        $kegiatanCounter = 1; // Counter untuk kegiatan

                                                                        foreach ($penjabatskpdCascadingProgram as $program) {
                                                                            // Menampilkan data dari sakip_penjabatskpd_cascadingprogram
                                                                            echo "<tr>";
                                                                            echo "<td class='tengah' style='border: 1px solid black;'>{$programCounter}</td>";
                                                                            echo "<td class='left' style='border: 1px solid black;'>{$program['uraian_sasaranprogram']}</td>";
                                                                            echo "<td class='left' style='border: 1px solid black;'>{$program['uraian_indikatorprogram']}</td>";
                                                                            echo "<td class='tengah' style='border: 1px solid black;'>{$program['program_satuan']}</td>";
                                                                            echo "<td class='tengah' style='border: 1px solid black;'>{$program['program_target']}</td>";
                                                                            echo "</tr>";

                                                                            // Menampilkan data terkait kegiatan dari sakip_penjabatskpd_cascadingkegiatan
                                                                            foreach ($penjabatskpdCascadingKegiatan as $kegiatan) {
                                                                                // Jika kegiatan tersebut terkait dengan program yang sedang diproses
                                                                                if ($kegiatan['refcascadingprogram_id'] === $program['refcascadingprogram_id']) {
                                                                                    // Menampilkan kegiatan dengan penomoran yang sesuai
                                                                                    echo "<tr>";
                                                                                    echo "<td class='tengah' style='border: 1px solid black;'></td>";
                                                                                    echo "<td class='left' style='border: 1px solid black;'>{$programCounter}.{$kegiatanCounter}. {$kegiatan['uraian_sasarankegiatan']}</td>"; // Menambahkan penomoran kegiatan
                                                                                    echo "<td class='tengah' style='border: 1px solid black;'>{$kegiatan['uraian_indikatorkegiatan']}</td>";
                                                                                    echo "<td class='tengah' style='border: 1px solid black;'>{$kegiatan['kegiatan_satuan']}</td>";
                                                                                    echo "<td class='tengah' style='border: 1px solid black;'>{$kegiatan['kegiatan_target']}</td>";
                                                                                    echo "</tr>";

                                                                                    // Increment counter untuk kegiatan setelah menampilkan kegiatan
                                                                                    $kegiatanCounter++;
                                                                                }
                                                                            }
                                                                            // Increment counter untuk program setelah menampilkan semua kegiatan untuk program tersebut
                                                                            $programCounter++;
                                                                            $kegiatanCounter = 1; // Reset kegiatan counter untuk program berikutnya
                                                                        }

                                                                        ?>
                                                                    </tbody>
                                                                <?php elseif ($refeselonId === 2): ?>
                                                                    <thead>
                                                                        <tr>
                                                                            <th style="border: 1px solid black;">NO</th>
                                                                            <th style="border: 1px solid black;">SASARAN KEGIATAN/SUB KEGIATAN</th>
                                                                            <th style="border: 1px solid black;">INDIKATOR KINERJA</th>
                                                                            <th style="border: 1px solid black;">SATUAN</th>
                                                                            <th style="border: 1px solid black;">TARGET</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <tr>
                                                                            <td class="tengah" style="border: 1px solid black;">(1)</td>
                                                                            <td class="tengah" style="border: 1px solid black;">(2)</td>
                                                                            <td class="tengah" style="border: 1px solid black;">(3)</td>
                                                                            <td class="tengah" style="border: 1px solid black;">(4)</td>
                                                                            <td class="tengah" style="border: 1px solid black;">(5)</td>
                                                                        </tr>
                                                                        <?php
                                                                        $kegiatanCounter = 1; // Counter untuk kegiatan
                                                                        $subkegiatanCounter = 1; // Counter untuk subkegiatan

                                                                        foreach ($penjabatskpdCascadingKegiatan as $kegiatan) {
                                                                            // Menampilkan data dari sakip_penjabatskpd_cascadingkegiatan
                                                                            echo "<tr>";
                                                                            echo "<td class='tengah' style='border: 1px solid black;'>{$kegiatanCounter}</td>";
                                                                            echo "<td class='left' style='border: 1px solid black;'>{$kegiatan['uraian_sasarankegiatan']}</td>";
                                                                            echo "<td class='left' style='border: 1px solid black;'>{$kegiatan['uraian_indikatorkegiatan']}</td>";
                                                                            echo "<td class='tengah' style='border: 1px solid black;'>{$kegiatan['kegiatan_satuan']}</td>";
                                                                            echo "<td class='tengah' style='border: 1px solid black;'>{$kegiatan['kegiatan_target']}</td>";
                                                                            echo "</tr>";

                                                                            // Menampilkan data terkait subkegiatan dari sakip_penjabatskpd_cascadingsubkegiatan
                                                                            foreach ($penjabatskpdCascadingSubkegiatan as $subkegiatan) {
                                                                                // Jika subkegiatan tersebut terkait dengan kegiatan yang sedang diproses
                                                                                if ($subkegiatan['refcascadingkegiatan_id'] === $kegiatan['refcascadingkegiatan_id']) {
                                                                                    // Menampilkan subkegiatan dengan penomoran yang sesuai
                                                                                    echo "<tr>";
                                                                                    echo "<td class='tengah' style='border: 1px solid black;'></td>";
                                                                                    echo "<td class='left' style='border: 1px solid black;'>{$kegiatanCounter}.{$subkegiatanCounter}. {$subkegiatan['uraian_sasaransubkegiatan']}</td>"; // Menambahkan penomoran subkegiatan
                                                                                    echo "<td class='tengah' style='border: 1px solid black;'>{$subkegiatan['uraian_indikatorsubkegiatan']}</td>";
                                                                                    echo "<td class='tengah' style='border: 1px solid black;'>{$subkegiatan['subkegiatan_satuan']}</td>";
                                                                                    echo "<td class='tengah' style='border: 1px solid black;'>{$subkegiatan['subkegiatan_target']}</td>";
                                                                                    echo "</tr>";

                                                                                    // Increment counter untuk subkegiatan setelah menampilkan subkegiatan
                                                                                    $subkegiatanCounter++;
                                                                                }
                                                                            }
                                                                            // Increment counter untuk kegiatan setelah menampilkan semua subkegiatan untuk kegiatan tersebut
                                                                            $kegiatanCounter++;
                                                                            $subkegiatanCounter = 1; // Reset subkegiatan counter untuk kegiatan berikutnya
                                                                        }

                                                                        ?>
                                                                    </tbody>
                                                                <?php else: ?>
                                                                    <thead>
                                                                        <tr>
                                                                            <th style="border: 1px solid black;">NO</th>
                                                                            <th style="border: 1px solid black;">SASARAN</th>
                                                                            <th style="border: 1px solid black;">INDIKATOR KINERJA</th>
                                                                            <th style="border: 1px solid black;">SATUAN</th>
                                                                            <th style="border: 1px solid black;">TARGET</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <tr>
                                                                            <td class="tengah" style="border: 1px solid black;">(1)</td>
                                                                            <td class="tengah" style="border: 1px solid black;">(2)</td>
                                                                            <td class="tengah" style="border: 1px solid black;" colspan="2">(3)</td>
                                                                            <td class="tengah" style="border: 1px solid black;">(4)</td>
                                                                            <td class="tengah" style="border: 1px solid black;">(5)</td>
                                                                        </tr>
                                                                        <?php
                                                                        $sasaranCounter = 1;
                                                                        foreach ($sasaranRenstra as $sasaran):
                                                                            $indikatorCounter = 1;
                                                                            $firstIndikatorRow = true;
                                                                            foreach ($indikators as $indikator):
                                                                                if ($indikator->refsasaranrenstra_id == $sasaran->refsasaranrenstra_id):
                                                                                    $formattedNumber = "{$sasaranCounter}.{$indikatorCounter}";
                                                                        ?>
                                                                                    <tr>
                                                                                        <td class="tengah" style="border: 1px solid black;"><?= $firstIndikatorRow ? $sasaranCounter : '' ?></td>
                                                                                        <td class="left" style="border: 1px solid black;"><?= $firstIndikatorRow ? Html::encode($sasaran->uraian_sasaranrenstra) : '' ?></td>
                                                                                        <td class="tengah" style="border: 1px solid black;"><?= $formattedNumber ?></td>
                                                                                        <td class="left" style="border: 1px solid black;"><?= Html::encode($indikator->uraian_indikatorsasaranrenstra) ?></td>
                                                                                        <td class="tengah" style="border: 1px solid black;"><?= Html::encode($indikator->indikatorsasaranrenstra_satuan) ?></td>
                                                                                        <td class="tengah" style="border: 1px solid black;"><?= Html::encode($indikator->indikatorsasaranrenstra_target) ?></td>
                                                                                    </tr>
                                                                        <?php
                                                                                    $indikatorCounter++;
                                                                                    $firstIndikatorRow = false;
                                                                                endif;
                                                                            endforeach;
                                                                            $sasaranCounter++;
                                                                        endforeach;
                                                                        ?>
                                                                    </tbody>
                                                                <?php endif; ?>


                                                            </table>

                                                        </div>
                                                        <br><br><br><br>
                                                    </td>
                                                </tr>


                                                <tr>
                                                    <td>
                                                        <table class="tbdata" style="border: 1px solid black; border-collapse: collapse;">
                                                            <?php if ($refeselonId === 1): ?>
                                                                <thead>
                                                                    <tr>
                                                                        <th style="border: 1px solid black;">No</th>
                                                                        <th style="border: 1px solid black;">Program/Kegiatan</th>
                                                                        <th style="border: 1px solid black;">Anggaran (Rp)</th>
                                                                        <th style="border: 1px solid black;">Keterangan</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <?php
                                                                    $noProgram = 0; // Inisialisasi nomor program

                                                                    foreach ($penjabatskpdCascadingProgram as $program) {
                                                                        $noProgram++; // Increment nomor program
                                                                        $noKegiatan = 0; // Reset nomor kegiatan untuk program baru

                                                                        // Tampilkan baris kegiatan
                                                                        echo "<tr>";
                                                                        echo "<td class='tengah' style='border: 1px solid black;'>{$noProgram}</td>"; // Nomor program
                                                                        echo "<td class='left' style='border: 1px solid black;'>{$program->refProgram->nama_program}</td>";
                                                                        echo "<td class='text-right' style='border: 1px solid black;'>";

                                                                        // Ambil anggaran_pk_p dari sakip_indikatorcascadingsubkegiatan berdasarkan refcascadingprogram_id
                                                                        foreach ($indikatorCascadingSubkegiatan as $indikator) {
                                                                            if ($indikator->refcascadingprogram_id == $program->refcascadingprogram_id) {
                                                                                $anggaran_pk_p = $indikator->anggaran_pk_p ?? 0; // Pastikan nilai tidak null
                                                                                echo 'Rp. ' . number_format($anggaran_pk_p, 0, ',', '.'); // Format Rupiah
                                                                                break; // Hanya menampilkan satu anggaran untuk program
                                                                            }
                                                                        }

                                                                        echo "</td>";
                                                                        echo "<td style='border: 1px solid black;'></td>"; // Kolom kosong
                                                                        echo "</tr>";

                                                                        // Menampilkan kegiatan terkait program ini
                                                                        foreach ($penjabatskpdCascadingKegiatan as $kegiatan) {
                                                                            if ($kegiatan->refcascadingprogram_id == $program->refcascadingprogram_id) {
                                                                                $noKegiatan++; // Increment nomor kegiatan

                                                                                echo "<tr>";
                                                                                echo "<td class='tengah' style='border: 1px solid black;'></td>"; // Nomor kegiatan
                                                                                echo "<td class='left' style='border: 1px solid black;'>{$noProgram}.{$noKegiatan}. {$kegiatan->refKegiatan->nama_kegiatan}</td>";

                                                                                echo "<td class='text-right' style='border: 1px solid black;'>";

                                                                                // Ambil anggaran_pk_p dari sakip_indikatorcascadingsubkegiatan berdasarkan refcascadingkegiatan_id
                                                                                foreach ($indikatorCascadingSubkegiatan as $indikator) {
                                                                                    if ($indikator->refcascadingkegiatan_id == $kegiatan->refcascadingkegiatan_id) {
                                                                                        $anggaran_pk_p = $indikator->anggaran_pk_p ?? 0; // Pastikan nilai tidak null
                                                                                        echo 'Rp. ' . number_format($anggaran_pk_p, 0, ',', '.'); // Format Rupiah
                                                                                        break; // Hanya menampilkan satu anggaran untuk kegiatan
                                                                                    }
                                                                                }

                                                                                echo "</td>";
                                                                                echo "<td style='border: 1px solid black;'></td>"; // Kolom kosong
                                                                                echo "</tr>";
                                                                            }
                                                                        }
                                                                    }
                                                                    ?>

                                                                </tbody>
                                                            <?php elseif ($refeselonId === 2): ?>
                                                                <thead>
                                                                    <tr>
                                                                        <th style="border: 1px solid black;">No</th>
                                                                        <th style="border: 1px solid black;">Kegiatan/Subkegiatan</th>
                                                                        <th style="border: 1px solid black;">Anggaran (Rp)</th>
                                                                        <th style="border: 1px solid black;">Keterangan</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <?php
                                                                    $noKegiatan = 0; // Inisialisasi nomor kegiatan

                                                                    foreach ($penjabatskpdCascadingKegiatan as $kegiatan) {
                                                                        $noKegiatan++; // Increment nomor kegiatan
                                                                        $noSubkegiatan = 0; // Reset nomor subkegiatan untuk kegiatan baru

                                                                        // Tampilkan baris kegiatan
                                                                        echo "<tr>";
                                                                        echo "<td class='tengah' style='border: 1px solid black;'>{$noKegiatan}</td>"; // Nomor kegiatan
                                                                        echo "<td class='left' style='border: 1px solid black;'>{$kegiatan->refKegiatan->nama_kegiatan}</td>";
                                                                        echo "<td class='text-right' style='border: 1px solid black;'>";

                                                                        // Ambil anggaran_pk_p dari sakip_indikatorcascadingsubkegiatan berdasarkan refcascadingkegiatan_id
                                                                        foreach ($indikatorCascadingSubkegiatan as $indikator) {
                                                                            if ($indikator->refcascadingkegiatan_id == $kegiatan->refcascadingkegiatan_id) {
                                                                                $anggaran_pk_p = $indikator->anggaran_pk_p ?? 0; // Pastikan nilai tidak null
                                                                                echo 'Rp. ' . number_format($anggaran_pk_p, 0, ',', '.'); // Format Rupiah
                                                                                break; // Hanya menampilkan satu anggaran untuk kegiatan
                                                                            }
                                                                        }

                                                                        echo "</td>";
                                                                        echo "<td style='border: 1px solid black;'></td>"; // Kolom kosong
                                                                        echo "</tr>";

                                                                        // Menampilkan subkegiatan terkait kegiatan ini
                                                                        foreach ($penjabatskpdCascadingSubkegiatan as $subkegiatan) {
                                                                            if ($subkegiatan->refcascadingkegiatan_id == $kegiatan->refcascadingkegiatan_id) {
                                                                                $noSubkegiatan++; // Increment nomor subkegiatan

                                                                                echo "<tr>";
                                                                                echo "<td class='tengah' style='border: 1px solid black;'></td>"; // Nomor subkegiatan
                                                                                echo "<td class='left' style='border: 1px solid black;'>{$noKegiatan}.{$noSubkegiatan}. {$subkegiatan->refSubkegiatan->nama_subkegiatan}</td>";

                                                                                echo "<td class='text-right' style='border: 1px solid black;'>";

                                                                                // Ambil anggaran_pk_p dari sakip_indikatorcascadingsubkegiatan berdasarkan refcascadingsubkegiatan_id
                                                                                foreach ($indikatorCascadingSubkegiatan as $indikator) {
                                                                                    if ($indikator->refcascadingsubkegiatan_id == $subkegiatan->refcascadingsubkegiatan_id) {
                                                                                        $anggaran_pk_p = $indikator->anggaran_pk_p ?? 0; // Pastikan nilai tidak null
                                                                                        echo 'Rp. ' . number_format($anggaran_pk_p, 0, ',', '.'); // Format Rupiah
                                                                                        break; // Hanya menampilkan satu anggaran untuk subkegiatan
                                                                                    }
                                                                                }

                                                                                echo "</td>";
                                                                                echo "<td style='border: 1px solid black;'></td>"; // Kolom kosong
                                                                                echo "</tr>";
                                                                            }
                                                                        }
                                                                    }
                                                                    ?>

                                                                </tbody>
                                                            <?php else: ?>
                                                                <thead>
                                                                    <tr>
                                                                        <th style="border: 1px solid black;">No</th>
                                                                        <th style="border: 1px solid black;">Program</th>
                                                                        <th style="border: 1px solid black;">Anggaran (Rp)</th>
                                                                        <th style="border: 1px solid black;">Keterangan</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <?php
                                                                    $programCounter = 1;
                                                                    $totalAnggaranpkpSum = 0;

                                                                    foreach ($programs as $program):
                                                                        // Calculate the total anggaran_pk_p for the program
                                                                        $subkegiatanPkpSum = (new \yii\db\Query())
                                                                            ->select(['SUM(anggaran_pk_p) AS total_anggaranpkp'])
                                                                            ->from('sakip_indikatorcascadingsubkegiatan')
                                                                            ->where(['refprogram_id' => $program->refprogram_id, 'refskpd_id' => $refskpd_id])
                                                                            ->scalar();

                                                                        $totalAnggaranpkp = $subkegiatanPkpSum !== null ? $subkegiatanPkpSum : 0;
                                                                        $totalAnggaranpkpSum += $totalAnggaranpkp;
                                                                    ?>
                                                                        <tr>
                                                                            <td class="tengah" style="border: 1px solid black;"><?= $programCounter++ ?></td>
                                                                            <td class="left" style="border: 1px solid black;"><?= Html::encode($program->refProgram->nama_program) ?></td>
                                                                            <td class="text-right" style="border: 1px solid black;"><?= 'Rp. ' . number_format($totalAnggaranpkp, 0, ',', '.') ?></td>
                                                                            <td style="border: 1px solid black;"></td>
                                                                        </tr>
                                                                    <?php endforeach; ?>

                                                                    <tr>
                                                                        <th class="tengah" colspan="2" style="border: 1px solid black;">Total</th>
                                                                        <th class="text-right" style="border: 1px solid black;"><?= 'Rp. ' . number_format($totalAnggaranpkpSum, 0, ',', '.') ?></th>
                                                                        <th style="border: 1px solid black;"></th>
                                                                    </tr>
                                                                </tbody>
                                                            <?php endif; ?>

                                                        </table>

                                                        <p>
                                                        <table class="tblPihak">
                                                            <tr>
                                                                <td></td>
                                                                <td>Lubuk Pakam, <span id="dynamicDate"><?= date('d F Y') ?></span></td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="2" height="15px"></td>
                                                            </tr>
                                                            <tr>
                                                                <td><?= Html::encode($leadership->jabatan_pimpinan ?? '') ?></td>
                                                                <td><?= Html::encode($penjabatSkpd->jabatan_eselon ?? 'N/A') ?></td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="2" height="70px">&nbsp;</td>
                                                            </tr>
                                                            <tr>
                                                                <td><?= Html::encode($leadership->nama_pimpinan ?? '') ?></td>
                                                                <td><?= Html::encode($penjabatSkpd->nama_penjabat ?? 'N/A') ?></td>
                                                            </tr>
                                                            <tr>
                                                                <td></td>
                                                                <td>NIP. <?= Html::encode($penjabatSkpd->nip_penjabat ?? 'N/A') ?></td>
                                                            </tr>
                                                        </table>
                                                        </p>
                                                    </td>
                                                </tr>
                                            </table>
                                        <?php endif; ?>


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

<script>
    // JavaScript function to update the date in the preview box in the desired format
    function updatePreviewDate() {
        var selectedDate = document.getElementById('selectedDate').value;
        if (selectedDate) {
            var date = new Date(selectedDate);
            var formattedDate = new Intl.DateTimeFormat('id-ID', {
                day: '2-digit',
                month: 'long',
                year: 'numeric'
            }).format(date);
            document.getElementById('dynamicDate').textContent = formattedDate;
        } else {
            document.getElementById('dynamicDate').textContent = '<?= date('d F Y') ?>';
        }
    }
</script>