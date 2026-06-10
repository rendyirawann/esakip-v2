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

$pdfUrl = Url::to(['laporan/download-pdf']); // Endpoint to generate and download PDF
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
                            <i class="fas fa-pen-fancy"></i> Laporan Renstra - <?= Html::encode(ucwords(strtolower($nama_skpd))) ?>
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <!-- Dropdown filter berdasarkan refperiode_id -->
                                <?= \yii\helpers\Html::beginForm(['index-laporan-renstra'], 'get', ['class' => 'form-inline']); ?>
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
                                        ['cetak-laporan-renstra', 'refperiode_id' => $selectedPeriodId, 'refskpd_id' => $selectedSkpdId],
                                        ['class' => 'btn btn-danger', 'target' => '_blank', 'data-pjax' => 0]
                                    ) ?>
                                    <?= Html::a(
                                        '<i class="fas fa-file-excel"></i> Excel',
                                        ['cetak-laporan-renstra-excel', 'refperiode_id' => $selectedPeriodId, 'refskpd_id' => $selectedSkpdId],
                                        ['class' => 'btn btn-success', 'target' => '_blank', 'data-pjax' => 0]
                                    ) ?>
                                </div>
                                <?php if (empty($sasaranRenstra) && empty($strategiList) && empty($kebijakanList)): ?>
                                    <div id="previewBox" class="border p-3" style="min-height: 400px;">
                                        <p class="text-muted">Data Periode Ini Tidak Tersedia</p>
                                    </div>
                                <?php else: ?>
                                    <!-- Preview box -->
                                    <div id="previewBox" class="border p-3" style="min-height: 400px;">
                                        <div class="title">
                                            <img src="<?= Url::base(true) ?>/lightapp/assets/images/bappeda.png" alt="logo image" height="60" width="60" class="logo-lg" />
                                            <h5 class="tbdata">
                                                <center>Rencana Strategis <?= Html::encode(ucwords(strtolower($nama_skpd))) ?><br>Periode <?= $selectedPeriodValue ?></center>
                                            </h5>
                                        </div>
                                        <hr>
                                        <br>
                                        <table border=0 cellpadding=1 cellspacing=1 border=0 class="tbdata" width="100%">
                                            <tr>
                                                <td><span class="tebal">Visi RPJMD</span><br><br>
                                                    <table class='tblAtas'>
                                                        <?php $visiSeen = [];
                                                        foreach ($sasaranRenstra as $item):
                                                            if (!isset($item->refVisi)) continue;
                                                            $visiId = $item->refVisi->refvisi_id;
                                                            if (in_array($visiId, $visiSeen)) continue;
                                                            $visiSeen[] = $visiId;
                                                        ?>
                                                            <tr>
                                                                <td>
                                                                    <ul>
                                                                        <li><?= Html::encode(strip_tags($item->refVisi->uraian_visi)) ?></li>
                                                                    </ul>
                                                                </td>
                                                            </tr>
                                                        <?php endforeach; ?>

                                                    </table>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><span class="tebal">Misi RPJMD</span><br><br>
                                                    <table class='tblAtas'>
                                                        <?php $misiSeen = [];
                                                        foreach ($sasaranRenstra as $item):
                                                            if (!isset($item->refMisi)) continue;
                                                            $misiId = $item->refMisi->refmisi_id;
                                                            if (in_array($misiId, $misiSeen)) continue;
                                                            $misiSeen[] = $misiId;
                                                        ?>
                                                            <tr>
                                                                <td>
                                                                    <ul>
                                                                        <li><?= Html::encode(strip_tags($item->refMisi->uraian_misi)) ?></li>
                                                                    </ul>
                                                                </td>
                                                            </tr>
                                                        <?php endforeach; ?>

                                                    </table>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><span class="tebal">Tujuan RPJMD</span><br><br>
                                                    <table class='tblAtas'>
                                                        <?php $tujuanSeen = [];
                                                        foreach ($sasaranRenstra as $item):
                                                            if (!isset($item->refTujuan)) continue;
                                                            $tujuanId = $item->refTujuan->reftujuan_id;
                                                            if (in_array($tujuanId, $tujuanSeen)) continue;
                                                            $tujuanSeen[] = $tujuanId;
                                                        ?>
                                                            <tr>
                                                                <td>
                                                                    <ul>
                                                                        <li><?= Html::encode(strip_tags($item->refTujuan->uraian_tujuan)) ?></li>
                                                                    </ul>
                                                                </td>
                                                            </tr>
                                                        <?php endforeach; ?>

                                                    </table>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><span class="tebal">Sasaran RPJMD</span><br><br>
                                                    <table class='tblAtas'>
                                                        <?php $sasaranSeen = [];
                                                        foreach ($sasaranRenstra as $item):
                                                            if (!isset($item->refSasaran)) continue;
                                                            $sasaranId = $item->refSasaran->refsasaran_id;
                                                            if (in_array($sasaranId, $sasaranSeen)) continue;
                                                            $sasaranSeen[] = $sasaranId;
                                                        ?>
                                                            <tr>
                                                                <td>
                                                                    <ul>
                                                                        <li><?= Html::encode(strip_tags($item->refSasaran->uraian_sasaran)) ?></li>
                                                                    </ul>
                                                                </td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    </table>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><span class="tebal">Strategi Renstra</span><br><br>
                                                    <table class='tblAtas'>
                                                        <?php
                                                        $strategiSeen = [];
                                                        foreach ($strategiList as $strategi):
                                                            $id = $strategi->refstrategi_id;
                                                            if (in_array($id, $strategiSeen)) continue;
                                                            $strategiSeen[] = $id;
                                                        ?>
                                                            <tr>
                                                                <td>
                                                                    <ul>
                                                                        <li><?= Html::encode(strip_tags($strategi->uraian_strategi)) ?></li>
                                                                    </ul>
                                                                </td>
                                                            </tr>
                                                        <?php endforeach; ?>

                                                    </table>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><span class="tebal">Kebijakan Renstra</span><br><br>
                                                    <table class='tblAtas'>
                                                        <?php
                                                        $kebijakanSeen = [];
                                                        foreach ($kebijakanList as $kebijakan):
                                                            $id = $kebijakan->refkebijakan_id;
                                                            if (in_array($id, $kebijakanSeen)) continue;
                                                            $kebijakanSeen[] = $id;
                                                        ?>
                                                            <tr>
                                                                <td>
                                                                    <ul>
                                                                        <li><?= Html::encode(strip_tags($kebijakan->uraian_kebijakan)) ?></li>
                                                                    </ul>
                                                                </td>
                                                            </tr>
                                                        <?php endforeach; ?>

                                                    </table>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <table class='tblRenstra'>
                                                        <thead>
                                                            <tr>
                                                                <td rowspan='2' width='25px' class='header'>No</td>
                                                                <td rowspan='2' width='150px' class='header'>Tujuan Renstra</td>
                                                                <td rowspan='2' width='25px' class='header'></td>
                                                                <td rowspan='2' width='150px' class='header'>Sasaran Renstra</td>
                                                                <td rowspan='2' width='30px' class='header'></td>
                                                                <td rowspan='2' width='150px' class='header'>Indikator Kinerja</td>
                                                                <td rowspan='2' width='80px' class='header'>Satuan</td>
                                                                <td colspan="2" class='header'>Target Tahun</td>
                                                            </tr>
                                                            <tr>
                                                                <td width='80px' class='header'><?= $selectedPeriodValue ?></td>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php
                                                            $currentNumber = 0; // Variabel untuk menyimpan angka depan (sebelum titik)
                                                            $lastSasaran = ''; // Variabel untuk menyimpan sasaran terakhir
                                                            foreach ($sasaranRenstra as $index => $item): ?>
                                                                <?php
                                                                // Ambil indikator yang sesuai dengan refsasaranrenstra_id
                                                                $indikators = $item->indikatorSasaran; // Mengambil data indikator

                                                                // Dapatkan kode sasaran renstra, misalnya 1.1, 1.2
                                                                $sasaranCode = $item->uraian_sasaranrenstra; // Misalkan ini adalah yang Anda gunakan untuk membandingkan

                                                                // Cek apakah kode sasaran saat ini berbeda dari kode sasaran terakhir
                                                                if ($lastSasaran !== $sasaranCode) {
                                                                    $currentNumber++; // Increment jika berbeda
                                                                    $lastSasaran = $sasaranCode; // Update kode sasaran terakhir
                                                                }

                                                                // Ambil angka indeks untuk indikator
                                                                $indicatorIndex = 1; // Reset untuk setiap sasaran baru

                                                                if (!empty($indikators)): // Cek jika ada indikator yang ditemukan 
                                                                ?>
                                                                    <?php foreach ($indikators as $i => $indikator): // Loop melalui setiap indikator 
                                                                    ?>
                                                                        <tr>
                                                                            <?php if ($i === 0): // Jika ini adalah indikator pertama, tampilkan informasi sasaran sekali 
                                                                            ?>
                                                                                <td style='text-align:center;'><?= $index + 1 ?></td>
                                                                                <td><?= Html::encode(strip_tags($item->refTujuan->uraian_tujuan)) ?></td>
                                                                                <td style='text-align:center;'><?= $currentNumber . '.' . ($indicatorIndex++) ?></td> <!-- Menampilkan sasaran dengan format -->
                                                                                <td><?= Html::encode(strip_tags($item->uraian_sasaranrenstra)) ?></td>
                                                                                <td></td>
                                                                            <?php else: // Jika bukan indikator pertama, kosongkan kolom yang tidak perlu 
                                                                            ?>
                                                                                <td></td>
                                                                                <td></td>
                                                                                <td></td>
                                                                                <td></td>
                                                                                <td></td>
                                                                            <?php endif; ?>
                                                                            <td><?= Html::encode(strip_tags($indikator->uraian_indikatorsasaranrenstra)) ?></td>
                                                                            <td style='text-align:center;'><?= Html::encode(strip_tags($indikator->indikatorsasaranrenstra_satuan)) ?></td>
                                                                            <td style='text-align:center;'><?= Html::encode(strip_tags($indikator->indikatorsasaranrenstra_target)) ?></td>
                                                                        </tr>
                                                                    <?php endforeach; // Akhir loop indikator 
                                                                    ?>
                                                                <?php else: // Jika tidak ada indikator 
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