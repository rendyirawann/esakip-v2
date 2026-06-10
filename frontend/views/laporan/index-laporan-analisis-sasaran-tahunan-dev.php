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

$pdfUrl = Url::to(['laporan/download-pdf-analisis-sasaran-tahunan']); // Endpoint to generate and download PDF
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
                            <i class="fas fa-pen-fancy"></i> Laporan Analisis Pencapaian Sasaran Tahunan - <?= Html::encode(ucwords(strtolower($nama_skpd))) ?>
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <?= \yii\helpers\Html::beginForm(['index-laporan-analisis-sasaran-tahunan-dev'], 'get', ['class' => 'form-inline']); ?>

                            <div class="col-lg-12">
                                <div class="form-group">
                                    <?= \yii\helpers\Html::label('Pilih Periode:', 'refperiode_id', ['class' => 'mr-2']); ?>
                                    <?= \yii\helpers\Html::dropDownList(
                                        'refperiode_id',
                                        $selectedPeriodId,
                                        \yii\helpers\ArrayHelper::map($periodeList, 'refperiode_id', 'periode'),
                                        [
                                            'class' => 'form-control',
                                            'prompt' => 'Semua Periode'
                                        ]
                                    ); ?>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="form-group">
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
                            </div>

                            <div class="col-lg-12">
                                <div class="form-group">
                                    <?= \yii\helpers\Html::label('Pilih Sasaran Renstra:', 'refsasaranrenstra_id', ['class' => 'mr-2']); ?>
                                    <?= \yii\helpers\Html::dropDownList(
                                        'refsasaranrenstra_id',
                                        $selectedSasaranRenstraId,
                                        \yii\helpers\ArrayHelper::map($sasaranRenstraList, 'refsasaranrenstra_id', 'uraian_sasaranrenstra'),
                                        [
                                            'class' => 'form-control',
                                            'prompt' => 'Semua Sasaran Renstra'
                                        ]
                                    ); ?>
                                </div>
                            </div>

                            <div class="col-lg-3">
                                <div class="form-group">
                                    <?= \yii\helpers\Html::submitButton('Tampil', ['class' => 'btn btn-primary']); ?>
                                </div>
                            </div>

                            <?= \yii\helpers\Html::endForm(); ?>
                        </div>


                        <div class="row">
                            <div class="col-sm-12">
                                <div class="btn-group mb-3" role="group">
                                    <button id="btnPrint" class="btn btn-primary">Print</button>
                                    <?= Html::a(
                                        '<i class="fas fa-file-pdf"></i> PDF',
                                        [
                                            'cetak-analisis-sasaran-tahunan',
                                            'refperiode_id' => $selectedPeriodId,
                                            'refskpd_id' => $selectedSkpdId,
                                            'refsasaranrenstra_id' => $selectedSasaranRenstraId
                                        ],
                                        ['class' => 'btn btn-danger', 'target' => '_blank', 'data-pjax' => 0]
                                    ) ?>
                                    <?= Html::a(
                                        '<i class="fas fa-file-excel"></i> Excel',
                                        [
                                            'cetak-analisis-sasaran-tahunan-excel',
                                            'refperiode_id' => $selectedPeriodId,
                                            'refskpd_id' => $selectedSkpdId,
                                            'refsasaranrenstra_id' => $selectedSasaranRenstraId
                                        ],
                                        ['class' => 'btn btn-success', 'target' => '_blank', 'data-pjax' => 0]
                                    ) ?>
                                </div>
                                <?php if (empty($indikators)): ?>
                                    <div id="previewBox" class="border p-3" style="min-height: 400px;">
                                        <p class="text-muted">Data Periode atau SKPD Ini Tidak Tersedia</p>
                                    </div>
                                <?php else: ?>
                                    <!-- Preview box -->
                                    <div id="previewBox" class="border p-3" style="min-height: 400px;">
                                        <div class="title">
                                            <img src="<?= Url::base(true) ?>/lightapp/assets/images/bappeda.png" alt="logo image" height="60" width="60" class="logo-lg" />
                                            <h5 class="tbdata">
                                                <center>
                                                    Analisis Pencapaian <?= Html::encode(ucwords(strtolower($nama_skpd))) ?><br>
                                                    Tahun <?= Html::encode($selectedPeriodValue) ?><br>
                                                    Sasaran <?= Html::encode($selectedSasaranRenstraId) ?> - <?= Html::encode($selectedSasaranRenstraUraian) ?>
                                                </center>
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
                                                                <td rowspan="2" width='25px' class='header'>No</td>
                                                                <td rowspan="2" width='150px' class='header'>Indikator Kinerja Utama</td>
                                                                <td rowspan="2" width='150px' class='header'>Satuan</td>
                                                                <td colspan="2" class='header'>Tahun <?= $selectedPeriodValue ?></td>
                                                                <td rowspan="2" width='150px' class='header'>%</td>
                                                            </tr>
                                                            <tr>
                                                                <td width='150px' class='header'>Target</td>
                                                                <td width='150px' class='header'>Realisasi</td>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php foreach ($indikators as $index => $sasaran): ?>
                                                                <tr>
                                                                    <td style='text-align:center;'><?= $index + 1 ?></td>
                                                                    <td><?= Html::encode($sasaran->uraian_indikatorsasaranrenstra) ?></td>
                                                                    <td><?= Html::encode($sasaran->indikatorsasaranrenstra_satuan) ?></td>
                                                                    <td><?= Html::encode($sasaran->target_rkt) ?></td>
                                                                    <td><?= Html::encode($sasaran->realisasi) ?></td>
                                                                    <td><?= Html::encode($sasaran->capaian) ?></td>
                                                                </tr>
                                                            <?php endforeach; ?>
                                                        </tbody>
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>

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