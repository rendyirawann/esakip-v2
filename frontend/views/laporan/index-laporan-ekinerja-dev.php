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
        font-family: 'Public Sans', sans-serif;
        font-size: 12px;
        font-weight: bold;
    }

    .tbdata {
        border-collapse: collapse;
        font-family: 'Public Sans', sans-serif;
        font-size: 10px;
        /* border: 1px solid #5d5d5d; */
    }

    .tbdata th {
        height: 40px;
        background: rgb(0, 160, 221);
        text-align: center;
        border: 1px solid #5d5d5d;
        color: #ffffff;
        padding: 2px;
    }

    .tbdata td {
        padding: 2px;
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

    .keterangan {
        border-collapse: collapse;
    }

    .keterangan td {
        padding: 5px;
        border: 1px solid #e3e3e3;
        font-size: 12px;
    }

    .tblprogram {
        width: 100%;
    }

    .tblprogram td {
        padding: 2px;
        border-left: 1px solid #c3c3c3;
        border-bottom: 1px solid #c3c3c3;
        font-size: 10px;
        vertical-align: top;
    }

    .tblprogram th {
        padding: 2px;
        border-left: 1px solid #e3e3e3;
        font-size: 10px;
        text-align: center;
        height: 30px;
    }

    .tblprogram tr.odd {
        background-color: #fafafa;
    }

    .tblprogram tr.evn {
        background-color: #e0e0e0;
    }

    #tblSasaran tr.ganjil {
        background-color: #ffffff;
    }

    #tblSasaran tr.genap {
        background-color: #f0f4f4;
    }

    th.head1 {
        vertical-align: middle;
        background: #2980b9;
        color: #fff;
    }

    th.head2 {
        vertical-align: middle;
        background: #16a085;
        color: #fff;
    }

    th.head2a {
        vertical-align: middle;
        background: #1abc9c;
        color: #fff;
    }

    th.head3 {
        vertical-align: middle;
        background: #27ae60;
        color: #fff;
    }

    th.head3a {
        vertical-align: middle;
        background: #2ecc71;
        color: #fff;
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
use frontend\models\SakipCascadingprogram;
use frontend\models\SakipIndikatorcascadingsubkegiatan;
use frontend\models\SakipProgram;

$pdfUrl = Url::to(['laporan/download-pdf-ekinerja']); // Endpoint to generate and download PDF
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
        font-family: 'Public Sans', sans-serif;
        font-size: 12px;
        font-weight: bold;
    }

    .tbdata {
        border-collapse: collapse;
        font-family: 'Public Sans', sans-serif;
        font-size: 10px;
        /* border: 1px solid #5d5d5d; */
    }

    .tbdata th {
        height: 40px;
        background: rgb(0, 160, 221);
        text-align: center;
        border: 1px solid #5d5d5d;
        color: #ffffff;
        padding: 2px;
    }

    .tbdata td {
        padding: 2px;
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

    .keterangan {
        border-collapse: collapse;
    }

    .keterangan td {
        padding: 5px;
        border: 1px solid #e3e3e3;
        font-size: 12px;
    }

    .tblprogram {
        width: 100%;
    }

    .tblprogram td {
        padding: 2px;
        border-left: 1px solid #c3c3c3;
        border-bottom: 1px solid #c3c3c3;
        font-size: 10px;
        vertical-align: top;
    }

    .tblprogram th {
        padding: 2px;
        border-left: 1px solid #e3e3e3;
        font-size: 10px;
        text-align: center;
        height: 30px;
    }

    .tblprogram tr.odd {
        background-color: #fafafa;
    }

    .tblprogram tr.evn {
        background-color: #e0e0e0;
    }

    #tblSasaran tr.ganjil {
        background-color: #ffffff;
    }

    #tblSasaran tr.genap {
        background-color: #f0f4f4;
    }

    th.head1 {
        vertical-align: middle;
        background: #2980b9;
        color: #fff;
    }

    th.head2 {
        vertical-align: middle;
        background: #16a085;
        color: #fff;
    }

    th.head2a {
        vertical-align: middle;
        background: #1abc9c;
        color: #fff;
    }

    th.head3 {
        vertical-align: middle;
        background: #27ae60;
        color: #fff;
    }

    th.head3a {
        vertical-align: middle;
        background: #2ecc71;
        color: #fff;
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
                            <i class="fas fa-pen-fancy"></i> Laporan Tingkat Efisiensi & Efektifitas Kinerja Terhadap Realisasi Anggaran - <?= Html::encode(ucwords(strtolower($nama_skpd))) ?>
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <!-- Dropdown filter berdasarkan refperiode_id -->
                                <?= \yii\helpers\Html::beginForm(['index-laporan-ekinerja-dev'], 'get', ['class' => 'form-inline']); ?>
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
                                <?= \yii\helpers\Html::endForm(); ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="btn-group mb-3" role="group">
                                    <button id="btnPrint" class="btn btn-primary">Print</button>
                                    <?= Html::a(
                                        '<i class="fas fa-file-pdf"></i> PDF',
                                        ['cetak-laporan-ekinerja', 'refperiode_id' => $selectedPeriodId, 'refskpd_id' => $selectedSkpdId],
                                        ['class' => 'btn btn-danger', 'target' => '_blank', 'data-pjax' => 0]
                                    ) ?>
                                    <?= Html::a(
                                        '<i class="fas fa-file-excel"></i> Excel',
                                        ['cetak-laporan-ekinerja-excel', 'refperiode_id' => $selectedPeriodId, 'refskpd_id' => $selectedSkpdId],
                                        ['class' => 'btn btn-success', 'target' => '_blank', 'data-pjax' => 0]
                                    ) ?>
                                </div>

                                <?php if (empty($sasaranRenstra)): ?>
                                    <div id="previewBox" class="border p-3" style="min-height: 400px;">
                                        <p class="text-muted">Data Periode atau SKPD Ini Tidak Tersedia</p>
                                    </div>
                                <?php else: ?>
                                    <!-- Preview box -->
                                    <div id="previewBox" class="border p-3" style="min-height: 400px;">
                                        <div class="title">
                                            <img src="<?= Url::base(true) ?>/lightapp/assets/images/bappeda.png" alt="logo image" height="60" width="60" class="logo-lg" />
                                            <h5 class="tbdata">
                                                <center>Tingkat Efisiensi & Efektifitas Kinerja Terhadap Realisasi Anggaran - <?= Html::encode(ucwords(strtolower($nama_skpd))) ?><br>Periode <?= $selectedPeriodValue ?></center>
                                            </h5>
                                        </div>
                                        <hr>
                                        <br>
                                        <div id="content">
                                            <table class='table table-bordered' align='center' style="font-size: 10px; table-layout: fixed; width: 100%;" id="tblSasaran">
                                                <tr>
                                                    <th rowspan='2' class='text-center head1' style='width: 4%;'>No</th>
                                                    <th rowspan='2' class='head1' style='width: 12%; white-space: normal;'>Sasaran</th>
                                                    <th rowspan='2' colspan='2' class='head1' style='width: 12%; white-space: normal;'>Indikator</th>
                                                    <th rowspan='2' class='text-center head1' style='width: 6%;'>Satuan</th>
                                                    <th colspan='3' class='text-center head2' style="white-space: normal;">Kinerja</th>
                                                    <th colspan='4' class='text-center head3' style="white-space: normal;">Keuangan</th>
                                                </tr>
                                                <tr>
                                                    <th class='text-center head2a' style='width:5%; white-space: normal;'>Target</th>
                                                    <th class='text-center head2a' style='width:5%; white-space: normal;'>Realisasi</th>
                                                    <th class='text-center head2a' style='width:5%; white-space: normal;'>(%)</th>
                                                    <th class="head3a text-center" style="width: 12%; white-space: normal;">Program</th>
                                                    <th class='text-center head2a' style='width:5%; white-space: normal;'>Pagu</th>
                                                    <th class='text-center head2a' style='width:3%; white-space: normal;'>Realisasi</th>
                                                    <th class='text-center head2a' style='width:3%; white-space: normal;'>(%)</th>
                                                </tr>
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
                                                                $rowspan = $programCount > 0 ? $programCount : 1;
                                                    ?>
                                                                <tr>
                                                                    <?php if ($i === 0): ?>
                                                                        <td rowspan="<?= $rowspan ?>" style='text-align:center;white-space: normal;'><?= $index + 1 ?></td>
                                                                        <td rowspan="<?= $rowspan ?>" style="white-space: normal;"><?= Html::encode(strip_tags($item->uraian_sasaranrenstra)) ?></td>
                                                                    <?php endif; ?>

                                                                    <?php if ($i == 1): // Apply colspan=2 for second row with the same refsasaranrenstra_id 
                                                                    ?>
                                                                        <td></td>
                                                                        <td></td>
                                                                        <td rowspan="<?= $rowspan ?>" style='text-align:center;white-space: normal;'><?= $currentNumber . '.' . $indicatorIndex ?></td>
                                                                    <?php else: ?>
                                                                        <td rowspan="<?= $rowspan ?>" style='text-align:center;white-space: normal;'><?= $currentNumber . '.' . $indicatorIndex ?></td>
                                                                    <?php endif; ?>

                                                                    <td rowspan="<?= $rowspan ?>" style="white-space: normal;"><?= Html::encode(strip_tags($indikator->uraian_indikatorsasaranrenstra)) ?></td>
                                                                    <td rowspan="<?= $rowspan ?>" style='text-align:center;white-space: normal;'><?= Html::encode(strip_tags($indikator->indikatorsasaranrenstra_satuan)) ?></td>
                                                                    <td rowspan="<?= $rowspan ?>" style='text-align:center;white-space: normal;'><?= Html::encode(strip_tags($indikator->indikatorsasaranrenstra_target)) ?></td>
                                                                    <td rowspan="<?= $rowspan ?>" style='text-align:center;white-space: normal;'><?= Html::encode(strip_tags($indikator->realisasi)) ?></td>
                                                                    <td rowspan="<?= $rowspan ?>" style='text-align:center;white-space: normal;'><?= Html::encode(strip_tags($indikator->capaian)) ?></td>

                                                                    <?php if (!empty($indikator->cascadingPrograms)): ?>
                                                                        <?php $firstProgram = true; ?>
                                                                        <?php foreach ($indikator->cascadingPrograms as $cascadingProgram): ?>
                                                                            <?php
                                                                            $programData = SakipIndikatorcascadingsubkegiatan::find()
                                                                                ->where(['refcascadingprogram_id' => $cascadingProgram->refcascadingprogram_id])
                                                                                ->select([
                                                                                    'sum(anggaran_rkt) as totalPagu',
                                                                                    'sum(realisasi) as totalRealisasi',
                                                                                    'sum(capaian) as totalCapaian'
                                                                                ])
                                                                                ->asArray()
                                                                                ->one();
                                                                            ?>
                                                                            <?php if (!$firstProgram): ?>
                                                                </tr>
                                                                <tr>
                                                                <?php endif; ?>
                                                                <td style="white-space: normal;"><?= Html::encode($cascadingProgram->refProgram->nama_program) ?></td>
                                                                <td style='text-align:center;white-space: normal;'><?= 'Rp. ' . number_format($programData['totalPagu'] ?? 0, 0, ',', '.'); ?></td>
                                                                <td style='text-align:center;white-space: normal;'><?= $programData['totalRealisasi'] ?? '0' ?></td>
                                                                <td style='text-align:center;white-space: normal;'><?= $programData['totalCapaian'] ?? '0' ?></td>
                                                                <?php $firstProgram = false; ?>
                                                            <?php endforeach; ?>
                                                        <?php else: ?>
                                                            <td style="white-space: normal;">Tidak ada program terkait</td>
                                                            <td style='text-align:center;white-space: normal;'>0</td>
                                                            <td style='text-align:center;white-space: normal;'>0</td>
                                                            <td style='text-align:center;white-space: normal;'>0</td>
                                                        <?php endif; ?>
                                                                </tr>
                                                                <?php $indicatorIndex++; ?>
                                                            <?php } ?>
                                                        <?php } else { ?>
                                                            <tr>
                                                                <td colspan="10" class="text-center" style="white-space: normal;">Tidak ada indikator</td>
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