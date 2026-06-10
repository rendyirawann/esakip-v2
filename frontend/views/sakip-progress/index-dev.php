<style>
    .text-justify {
        text-align: justify !important;
    }
</style>
<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

$this->title = 'Progress Isian SKPD';
$this->params['breadcrumbs'][] = $this->title;

$this->registerCss("
    /* Class utama untuk tabel di modal */
    .table-modal-detail {
        table-layout: fixed;
        width: 100%;
        border-collapse: collapse; /* Border lebih rapi */
    }

    /* Styling untuk Header Tabel */
    .table-modal-detail thead th {
        background-color: #f8f9fa; /* Warna abu-abu muda */
        color: #333;
        font-weight: 600; /* Sedikit lebih tebal */
        padding: 0.8rem;
        border-bottom: 2px solid #dee2e6;
        white-space: normal !important;
        word-wrap: break-word;
        vertical-align: middle;
    }

    /* Styling untuk Body Tabel */
    .table-modal-detail tbody td {
        padding: 0.8rem;
        vertical-align: top;
        white-space: normal !important;
        word-wrap: break-word;
    }
    
    /* Efek hover pada baris tabel */
    .table-modal-detail tbody tr:hover {
        background-color: #f1f3f5; /* Warna hover yang lembut */
    }

    /* Styling untuk daftar di dalam sel (jika ada) */
    .table-modal-detail td ul {
        margin: 0;
        padding-left: 1.2rem;
    }
");

$this->registerJs("
// --- Event Handler untuk Tombol Detail ---
$(document).on('click', '.show-detail-btn', function() {
    var button = $(this);
    var modal = $('#detailModal');
    var modalTitle = modal.find('.modal-title');
    var modalBody = modal.find('.modal-body');

    // Ambil data dari atribut tombol
    var type = button.data('type');
    var skpdId = button.data('skpd-id');
    var skpdNama = button.data('skpd-nama');
    var periodeId = button.data('periode-id');
    var title = button.data('title');
    
    // Set judul modal & tampilkan loading
    modalTitle.text(title + ' - ' + skpdNama);
    modalBody.html('<p class=\"text-center\">Mengambil data...</p>');
    modal.modal('show');

    // Panggil AJAX ke controller
    $.ajax({
        url: '" . yii\helpers\Url::to(['get-monitoring-detail']) . "',
        type: 'GET',
        data: { type: type, refskpd_id: skpdId, refperiode_id: periodeId },
        success: function(response) {
            
            // --- PERUBAHAN 1: Tambahkan div pembungkus dengan kelas padding 'p-3' ---
            var tableHtml = '<div class=\"p-3\"><div class=\"table-responsive\"><table class=\"table table-sm table-bordered table-striped table-hover table-modal-detail\">';
            
            if (response.data.length === 0) {
                tableHtml = '<div class=\"alert alert-warning\">Tidak ada data detail untuk ditampilkan.</div>';
            } else {
                // Bangun Header Tabel berdasarkan tipe, tambahkan kolom 'No.'
                tableHtml += '<thead><tr><th width=\"5%\">No.</th>';
                if (type === 'sasaran') {
                    tableHtml += '<th width=\"45%\">Sasaran Renstra</th><th>Indikator Terkait</th>';
                } else if (type === 'program') {
                    tableHtml += '<th width=\"45%\">Sasaran Renstra Terkait</th><th>Daftar Program</th>';
                } else if (type === 'kegiatan') {
                    tableHtml += '<th width=\"45%\">Program Terkait</th><th>Daftar Kegiatan</th>';
                } else if (type === 'subkegiatan') {
                    tableHtml += '<th width=\"45%\">Kegiatan Terkait</th><th>Daftar Sub Kegiatan</th>';
                }
                tableHtml += '</tr></thead>';
                
                // Bangun Body Tabel
                tableHtml += '<tbody>';
                $.each(response.data, function(index, item) {
                    tableHtml += '<tr><td>' + (index + 1) + '</td><td>' + item.kolom1 + '</td><td>' + item.kolom2 + '</td></tr>';
                });
                tableHtml += '</tbody>';
            }
            
            // --- PERUBAHAN 2: Tambahkan penutup untuk div pembungkus ---
            tableHtml += '</table></div></div>';
            
            modalBody.html(tableHtml);
        },
        error: function() {
            modalBody.html('<div class=\"alert alert-danger\">Terjadi kesalahan.</div>');
        }
    });
});
");
?>

<div class="pc-container">
    <div class="pc-content">
        <!-- [ breadcrumb ] start -->
        <div class="page-header">
            <div class="page-block">
                <div class="row align-items-center">
                    <div class="col-md-12">
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?= Url::to(['/site/index']) ?>">Home</a></li>
                            <li class="breadcrumb-item" aria-current="page">Progress SKPD</li>
                        </ul>
                    </div>
                    <div class="col-md-12">
                        <div class="page-header-title">
                            <h2 class="mb-0">Progress SKPD</h2>
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
                    <div class="card-header" style="background-color: #04A9F5; padding: 8px;">
                        <h6 style="color: white; margin: 0; cursor: pointer;" id="toggleAll">
                            <i class="fas fa-pen-fancy"></i>Periode Progress SKPD - <?= Html::encode(ucwords(strtolower($nama_skpd))) ?>
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <!-- Dropdown filter berdasarkan refperiode_id -->
                                <?= \yii\helpers\Html::beginForm(['index-dev'], 'get', ['class' => 'form-inline']); ?>
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
                                <?= \yii\helpers\Html::endForm(); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End Card -->

                <!-- Table Start -->
                <div class="card">
                    <div class="card-header" style="background-color: #04A9F5; padding: 8px;">
                        <h6 style="color: white; margin: 0; cursor: pointer;" id="toggleAll">
                            <i class="fas fa-pen-fancy"></i> Data SAKIP Progress SKPD - <?= Html::encode(ucwords(strtolower($nama_skpd))) ?> (Periode <?= $selectedPeriodValue ?>)
                        </h6>
                    </div>
                    <div class="card-body">
                        <?php if (Yii::$app->session->hasFlash('success')) : ?>
                            <div class="alert alert-success">
                                <?= Yii::$app->session->getFlash('success') ?>
                            </div>
                        <?php endif; ?>

                        <?php if (Yii::$app->session->hasFlash('error')) : ?>
                            <div class="alert alert-danger">
                                <?= Yii::$app->session->getFlash('error') ?>
                            </div>
                        <?php endif; ?>


                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-light text-center">
                                    <tr>
                                        <th>No.</th>
                                        <th>Nama SKPD / OPD</th>
                                        <th>Jml. Sasaran</th>
                                        <th>Jml. Program</th>
                                        <th>Jml. Kegiatan</th>
                                        <th>Jml. Sub Kegiatan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($monitoringData)): ?>
                                        <tr>
                                            <td colspan="6" class="text-center"><i>Tidak ada data untuk ditampilkan.</i></td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($monitoringData as $index => $data): ?>
                                            <tr>
                                                <td class="text-center"><?= $index + 1 ?></td>
                                                <td><?= Html::encode($data['nama_skpd']) ?></td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-link show-detail-btn" data-type="sasaran" data-skpd-id="<?= $data['skpd_id'] ?>" data-skpd-nama="<?= Html::encode($data['nama_skpd']) ?>" data-periode-id="<?= $selectedPeriodId ?>" data-title="Detail Sasaran Renstra"><?= $data['jumlah_sasaran'] ?></button>
                                                </td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-link show-detail-btn" data-type="program" data-skpd-id="<?= $data['skpd_id'] ?>" data-skpd-nama="<?= Html::encode($data['nama_skpd']) ?>" data-periode-id="<?= $selectedPeriodId ?>" data-title="Detail Program"><?= $data['jumlah_program'] ?></button>
                                                </td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-link show-detail-btn" data-type="kegiatan" data-skpd-id="<?= $data['skpd_id'] ?>" data-skpd-nama="<?= Html::encode($data['nama_skpd']) ?>" data-periode-id="<?= $selectedPeriodId ?>" data-title="Detail Kegiatan"><?= $data['jumlah_kegiatan'] ?></button>
                                                </td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-link show-detail-btn" data-type="subkegiatan" data-skpd-id="<?= $data['skpd_id'] ?>" data-skpd-nama="<?= Html::encode($data['nama_skpd']) ?>" data-periode-id="<?= $selectedPeriodId ?>" data-title="Detail Sub Kegiatan"><?= $data['jumlah_subkegiatan'] ?></button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        <!--  -->
                        <div class="modal fade" id="detailModal" tabindex="-1" role="dialog" aria-labelledby="detailModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-xl" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="detailModalLabel">Detail</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--  -->

                    </div>
                </div>

                <!-- Table end -->
            </div>
            <!-- end row -->
        </div>


        <!-- [ Main Content ] end -->
    </div>
</div>