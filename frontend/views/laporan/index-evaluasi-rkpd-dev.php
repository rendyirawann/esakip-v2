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

    printWindow.document.write('</style></head><body>');
    printWindow.document.write(content); // Tulis konten ke printWindow
    printWindow.document.write('</body></html>');
    printWindow.document.close();
    printWindow.print(); // Panggil print
});



");

$this->registerCss("
    /* --- Gaya untuk Laporan Evaluasi RKPD --- */

    /* Container utama laporan */
    .laporan-container {
        font-family: 'Public Sans', sans-serif;
        font-size: 11px;
    }

    /* Judul Laporan */
    .laporan-title {
        text-align: center;
        margin-bottom: 1rem;
    }
    
    /* Tabel Laporan Utama */
    .table-laporan {
        width: 100%;
        border-collapse: collapse;
    }

    .table-laporan th,
    .table-laporan td {
        border: 1px solid #dee2e6; /* Warna border abu-abu standar */
        padding: 0.4rem;
        vertical-align: top;
        /* Properti Kunci untuk Responsif */
        white-space: normal !important; /* Memaksa teks untuk turun (wrap) */
        word-break: break-word;     /* Memecah kata yang sangat panjang */
    }

    .table-laporan thead th {
        background-color: #0d6efd; /* Warna biru primary bootstrap */
        color: white;
        text-align: center;
        vertical-align: middle;
        font-weight: 600;
    }

    /* Class untuk teks tebal */
    .text-tebal {
        font-weight: bold;
    }
");

// Perbarui JavaScript untuk menangani collapse bertingkat

// JavaScript BARU untuk menangani ikon +/-
$this->registerJs("
    // Dengarkan event 'click' pada tombol collapse
    $('button[data-bs-toggle=\"collapse\"]').on('click', function() {
        var icon = $(this).find('i.fas');
        
        // Cek status SEBELUM Bootstrap mengubahnya
        var targetSelector = $(this).data('bs-target');
        var isCollapsing = $(targetSelector).hasClass('show');

        // Toggle ikon berdasarkan state saat ini
        if (isCollapsing) {
            icon.removeClass('fa-minus').addClass('fa-plus');
        } else {
            icon.removeClass('fa-plus').addClass('fa-minus');
        }
    });

    // Event tambahan untuk memastikan ikon kembali ke '+' saat induknya ditutup
    $('.collapse').on('hide.bs.collapse', function () {
        // Cari SEMUA tombol collapse di dalam item yang ditutup ini
        $(this).find('button[data-bs-toggle=\"collapse\"]').each(function() {
            // Dan reset ikonnya ke '+'
            $(this).find('i.fas').removeClass('fa-minus').addClass('fa-plus');
        });
    });
");
?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
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
                                <?= \yii\helpers\Html::beginForm(['index-evaluasi-rkpd-dev'], 'get', ['class' => 'form-inline']); ?>
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
                                    <div class="btn-group">
                                        <?= Html::a(
                                            '<i class="fas fa-file-pdf"></i> Cetak PDF',
                                            ['cetak-evaluasi-rkpd-dev', 'refperiode_id' => $selectedPeriodId, 'refskpd_id' => $selectedSkpdId],
                                            ['class' => 'btn btn-danger', 'target' => '_blank', 'data-pjax' => 0]
                                        ) ?>

                                        <?= Html::a(
                                            '<i class="fas fa-file-excel"></i> Cetak Excel',
                                            ['cetak-evaluasi-rkpd-excel-dev', 'refperiode_id' => $selectedPeriodId, 'refskpd_id' => $selectedSkpdId],
                                            ['class' => 'btn btn-success', 'target' => '_blank', 'data-pjax' => 0]
                                        ) ?>
                                    </div>
                                </div>

                                <?php if (!empty($sasaranRenstra) && !empty($strategiList) && !empty($kebijakanList)): ?>

                                    <div class="border p-3 text-center">
                                        <p class="text-muted">Data untuk periode yang dipilih tidak tersedia.</p>
                                    </div>

                                <?php else: ?>

                                    <div id="previewContent" class="border p-3" style="min-height: 400px;">
                                        <div class="title text-center"> <img src="<?= Url::base(true) ?>/lightapp/assets/images/bappeda.png" alt="logo image" height="60" width="60" class="logo-lg" />
                                            <h5 class="mt-2"> Rencana Strategis <?= Html::encode(ucwords(strtolower($nama_skpd))) ?><br>Periode <?= $selectedPeriodValue ?>
                                            </h5>
                                        </div>
                                        <hr>

                                        <div class="table-responsive">
                                            <table class="table table-bordered text-center">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th rowspan="3" class="align-middle">#</th>
                                                        <th rowspan="3" class="align-middle">Kode</th>
                                                        <th rowspan="3" class="align-middle">Program / Kegiatan / Sub Kegiatan</th>
                                                        <th rowspan="3" class="align-middle">Indikator</th>
                                                        <th rowspan="3" class="align-middle">Satuan</th>
                                                        <th rowspan="3" class="align-middle">Target</th>
                                                        <th rowspan="3" class="align-middle">Anggaran</th>
                                                        <th colspan="8">Realisasi Kinerja Pada Triwulan</th>
                                                        <th colspan="2" rowspan="2">Total Realisasi dan Anggaran Tiap Triwulan</th>
                                                    </tr>
                                                    <tr>
                                                        <th colspan="2">I</th>
                                                        <th colspan="2">II</th>
                                                        <th colspan="2">III</th>
                                                        <th colspan="2">IV</th>
                                                    </tr>
                                                    <tr>
                                                        <th>Realisasi</th>
                                                        <th>Penyerapan Anggaran</th>
                                                        <th>Realisasi</th>
                                                        <th>Penyerapan Anggaran</th>
                                                        <th>Realisasi</th>
                                                        <th>Penyerapan Anggaran</th>
                                                        <th>Realisasi</th>
                                                        <th>Penyerapan Anggaran</th>
                                                        <th>Total Realisasi</th>
                                                        <th>Total Penyerapan Anggaran</th>
                                                    </tr>

                                                </thead>
                                                <tbody>
                                                    <?php if (empty($laporanData)): ?>
                                                        <tr>
                                                            <td colspan="19" class="text-center p-4"><i>Tidak ada data untuk ditampilkan.</i></td>
                                                        </tr>
                                                    <?php else: ?>
                                                        <?php foreach ($laporanData as $s_index => $sasaran): ?>
                                                            <tr class="table-primary text-start">
                                                                <td class="text-center align-middle">
                                                                    <button class="btn btn-sm btn-link p-0 text-white" type="button" data-bs-toggle="collapse" data-bs-target=".program-for-sasaran-<?= $s_index ?>">
                                                                        <i class="fas fa-plus accordion-icon"></i>
                                                                    </button>
                                                                </td>
                                                                <td colspan="18" class="fw-bold align-middle">
                                                                    SASARAN: <?= Html::encode($sasaran['uraian_sasaranrenstra']) ?>
                                                                </td>
                                                            </tr>

                                                            <?php if (!empty($sasaran['programs'])): ?>
                                                                <?php foreach ($sasaran['programs'] as $p_index => $program): ?>

                                                                    <tr class="collapse program-for-sasaran-<?= $s_index ?> table-light text-start">
                                                                        <td class="text-center align-middle"></td>
                                                                        <td class="align-middle ps-4">
                                                                            <button class="btn btn-sm btn-link p-0" type="button" data-bs-toggle="collapse" data-bs-target=".kegiatan-for-program-<?= $s_index ?>-<?= $p_index ?>">
                                                                                <i class="fas fa-plus accordion-icon"></i>
                                                                            </button>
                                                                            <?= Html::encode($program['kode_program']) ?>
                                                                        </td>
                                                                        <td class="fw-bold align-middle"><?= Html::encode($program['nama_program']) ?></td>
                                                                        <td class="align-middle"><?= Html::encode($program['uraian_indikator']) ?></td>
                                                                        <td class="text-center align-middle"><?= Html::encode($program['satuan']) ?></td>
                                                                        <td class="text-center align-middle"><?= Html::encode($program['target']) ?></td>
                                                                        <td class="text-end fw-bold align-middle"><?= Yii::$app->formatter->asDecimal($program['total_anggaran'], 0) ?></td>

                                                                        <td class="text-center align-middle"><?= Html::encode($program['realisasi'][1]) ?></td>
                                                                        <td class="text-end align-middle"><?= Yii::$app->formatter->asDecimal($program['triwulan_penyerapan_anggaran'][1], 0) ?></td>
                                                                        <td class="text-center align-middle"><?= Html::encode($program['realisasi'][2]) ?></td>
                                                                        <td class="text-end align-middle"><?= Yii::$app->formatter->asDecimal($program['triwulan_penyerapan_anggaran'][2], 0) ?></td>
                                                                        <td class="text-center align-middle"><?= Html::encode($program['realisasi'][3]) ?></td>
                                                                        <td class="text-end align-middle"><?= Yii::$app->formatter->asDecimal($program['triwulan_penyerapan_anggaran'][3], 0) ?></td>
                                                                        <td class="text-center align-middle"><?= Html::encode($program['realisasi'][4]) ?></td>
                                                                        <td class="text-end align-middle"><?= Yii::$app->formatter->asDecimal($program['triwulan_penyerapan_anggaran'][4], 0) ?></td>
                                                                        <td class="text-center fw-bold align-middle"><?= Html::encode($program['total_realisasi']) ?></td>
                                                                        <td class="text-end fw-bold align-middle"><?= Yii::$app->formatter->asDecimal($program['total_penyerapan'], 0) ?></td>
                                                                    </tr>

                                                                    <?php if (!empty($program['kegiatans'])): ?>
                                                                        <?php foreach ($program['kegiatans'] as $k_index => $kegiatan): ?>

                                                                            <tr class="collapse kegiatan-for-program-<?= $s_index ?>-<?= $p_index ?> text-start" style="background-color: #f8f9fa;">
                                                                                <td class="text-center align-middle"></td>
                                                                                <td class="ps-5 align-middle">
                                                                                    <button class="btn btn-sm btn-link py-0 px-1" type="button" data-bs-toggle="collapse" data-bs-target=".subkegiatan-for-kegiatan-<?= $s_index ?>-<?= $p_index ?>-<?= $k_index ?>">
                                                                                        <i class="fas fa-plus accordion-icon"></i>
                                                                                    </button>
                                                                                    <?= Html::encode($kegiatan['kode_kegiatan']) ?>
                                                                                </td>
                                                                                <td class="ps-4 align-middle"><?= Html::encode($kegiatan['nama_kegiatan']) ?></td>
                                                                                <td class="align-middle"><?= Html::encode($kegiatan['uraian_indikator']) ?></td>
                                                                                <td class="text-center align-middle"><?= Html::encode($kegiatan['satuan']) ?></td>
                                                                                <td class="text-center align-middle"><?= Html::encode($kegiatan['target']) ?></td>
                                                                                <td class="text-end align-middle"><?= Yii::$app->formatter->asDecimal($kegiatan['total_anggaran'], 0) ?></td>

                                                                                <td class="text-center align-middle"><?= Html::encode($kegiatan['realisasi'][1]) ?></td>
                                                                                <td class="text-end align-middle"><?= Yii::$app->formatter->asDecimal($kegiatan['triwulan_penyerapan_anggaran'][1], 0) ?></td>
                                                                                <td class="text-center align-middle"><?= Html::encode($kegiatan['realisasi'][2]) ?></td>
                                                                                <td class="text-end align-middle"><?= Yii::$app->formatter->asDecimal($kegiatan['triwulan_penyerapan_anggaran'][2], 0) ?></td>
                                                                                <td class="text-center align-middle"><?= Html::encode($kegiatan['realisasi'][3]) ?></td>
                                                                                <td class="text-end align-middle"><?= Yii::$app->formatter->asDecimal($kegiatan['triwulan_penyerapan_anggaran'][3], 0) ?></td>
                                                                                <td class="text-center align-middle"><?= Html::encode($kegiatan['realisasi'][4]) ?></td>
                                                                                <td class="text-end align-middle"><?= Yii::$app->formatter->asDecimal($kegiatan['triwulan_penyerapan_anggaran'][4], 0) ?></td>
                                                                                <td class="text-center fw-bold align-middle"><?= Html::encode($kegiatan['total_realisasi']) ?></td>
                                                                                <td class="text-end fw-bold align-middle"><?= Yii::$app->formatter->asDecimal($kegiatan['total_penyerapan'], 0) ?></td>
                                                                            </tr>

                                                                            <?php if (!empty($kegiatan['subkegiatans'])): ?>
                                                                                <?php foreach ($kegiatan['subkegiatans'] as $subkegiatan): ?>

                                                                                    <tr class="collapse subkegiatan-for-kegiatan-<?= $s_index ?>-<?= $p_index ?>-<?= $k_index ?> text-start">
                                                                                        <td></td>
                                                                                        <td class="ps-5 align-middle"><?= Html::encode($subkegiatan['kode_subkegiatan']) ?></td>
                                                                                        <td class="ps-5 align-middle"><?= Html::encode($subkegiatan['nama_subkegiatan']) ?></td>
                                                                                        <td class="align-middle"><?= Html::encode($subkegiatan['uraian_indikator']) ?></td>
                                                                                        <td class="text-center align-middle"><?= Html::encode($subkegiatan['satuan']) ?></td>
                                                                                        <td class="text-center align-middle"><?= Html::encode($subkegiatan['target']) ?></td>
                                                                                        <td class="text-end align-middle"><?= Yii::$app->formatter->asDecimal($subkegiatan['total_anggaran'], 0) ?></td>

                                                                                        <td class="text-center align-middle"><?= Html::encode($subkegiatan['realisasi'][1]) ?></td>
                                                                                        <td class="text-end align-middle"><?= Yii::$app->formatter->asDecimal($subkegiatan['triwulan_penyerapan_anggaran'][1], 0) ?></td>
                                                                                        <td class="text-center align-middle"><?= Html::encode($subkegiatan['realisasi'][2]) ?></td>
                                                                                        <td class="text-end align-middle"><?= Yii::$app->formatter->asDecimal($subkegiatan['triwulan_penyerapan_anggaran'][2], 0) ?></td>
                                                                                        <td class="text-center align-middle"><?= Html::encode($subkegiatan['realisasi'][3]) ?></td>
                                                                                        <td class="text-end align-middle"><?= Yii::$app->formatter->asDecimal($subkegiatan['triwulan_penyerapan_anggaran'][3], 0) ?></td>
                                                                                        <td class="text-center align-middle"><?= Html::encode($subkegiatan['realisasi'][4]) ?></td>
                                                                                        <td class="text-end align-middle"><?= Yii::$app->formatter->asDecimal($subkegiatan['triwulan_penyerapan_anggaran'][4], 0) ?></td>
                                                                                        <td class="text-center fw-bold align-middle"><?= array_sum(array_filter($subkegiatan['realisasi'], 'is_numeric')) ?></td>
                                                                                        <td class="text-end fw-bold align-middle"><?= Yii::$app->formatter->asDecimal(array_sum($subkegiatan['triwulan_penyerapan_anggaran']), 0) ?></td>
                                                                                    </tr>
                                                                                <?php endforeach; ?>
                                                                            <?php endif; ?>

                                                                        <?php endforeach; ?>
                                                                    <?php endif; ?>

                                                                <?php endforeach; ?>
                                                            <?php endif; ?>

                                                        <?php endforeach; ?>
                                                    <?php endif; ?>
                                                </tbody>


                                            </table>
                                        </div>
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

<!-- [ Main Content ] end -->