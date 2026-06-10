<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use frontend\models\SakipCascadingkegiatan;
use frontend\models\SimonaCascadingkegiatan;
use frontend\models\SimonaRincianbelanjacascadingkegiatan;
use frontend\models\SimonaMediacascadingkegiatan;
use frontend\models\SimonaKeluaranmediacascadingkegiatan;
use frontend\models\SimonaMediacascadingkegiatanOpd;
use frontend\models\SakipCascadingprogram;
use frontend\models\SakipCascadingsubkegiatan;
use frontend\models\SakipPenjabatskpdCascadingkegiatan;
use frontend\models\SakipStrategi;
use frontend\models\SakipKebijakan;
use frontend\models\SakipTujuanrenstra;
use frontend\models\SakipSasaranrenstra;
use frontend\models\SakipIndikatorsasaranrenstra;
use frontend\models\SakipSasaran;
use frontend\models\SakipPeriode;
use frontend\models\SakipTujuan;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use frontend\models\SakipIndikatortujuanrenstra;

/** @var yii\web\View $this */
/** @var frontend\models\SakipCascadingprogram $model */

$this->title = 'Perencanaan Kegiatan - ' . $model->refProgram->nama_program;
$this->params['breadcrumbs'][] = ['label' => 'Sakip Cascadingprograms', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);

$this->registerJs("
$('#createModal').on('show.bs.modal', function (event) {
    var modal = $(this);
    var button = $(event.relatedTarget);  // Button that triggered the modal

    // Extract values from the button's data attributes
    var refcascadingprogram_id = button.data('refcascadingprogram_id');
    var refcascadingkegiatan_id = button.data('refcascadingkegiatan_id');
    var refperiode_id = button.data('refperiode_id');
    var refsasaranrenstra_id = button.data('refsasaranrenstra_id');
    var refindikatorsasaranrenstra_id = button.data('refindikatorsasaranrenstra_id');
    var refprogram_id = button.data('refprogram_id');
    var refkegiatan_id = button.data('refkegiatan_id');
    var uraian_sasarankegiatan = button.data('uraian_sasarankegiatan');
    var uraian_indikatorkegiatan = button.data('uraian_indikatorkegiatan');
    var kegiatan_target = button.data('kegiatan_target');
    var kegiatan_satuan = button.data('kegiatan_satuan');

    // Set the hidden inputs in the modal with the correct values
    modal.find('#refcascadingprogram_id').val(refcascadingprogram_id);
    modal.find('#refcascadingkegiatan_id').val(refcascadingkegiatan_id);
    modal.find('#refperiode_id').val(refperiode_id);
    modal.find('#refsasaranrenstra_id').val(refsasaranrenstra_id);
    modal.find('#refindikatorsasaranrenstra_id').val(refindikatorsasaranrenstra_id);
    modal.find('#refprogram_id').val(refprogram_id);
    modal.find('#refkegiatan_id').val(refkegiatan_id);
    modal.find('#uraian_sasarankegiatan').val(uraian_sasarankegiatan);
    modal.find('#uraian_indikatorkegiatan').val(uraian_indikatorkegiatan);
    modal.find('#kegiatan_target').val(kegiatan_target);
    modal.find('#kegiatan_satuan').val(kegiatan_satuan);

    // Send the data to load the form dynamically (optional)
    $.ajax({
        url: '" . Url::to(['simona-cascadingkegiatan/create']) . "',
        type: 'GET',
        data: {
            refcascadingprogram_id: refcascadingprogram_id,
            refcascadingkegiatan_id: refcascadingkegiatan_id,
            refperiode_id: refperiode_id,
            refsasaranrenstra_id: refsasaranrenstra_id,
            refindikatorsasaranrenstra_id: refindikatorsasaranrenstra_id,
            refprogram_id: refprogram_id,
            refkegiatan_id: refkegiatan_id,
            uraian_sasarankegiatan: uraian_sasarankegiatan, 
            uraian_indikatorkegiatan: uraian_indikatorkegiatan,
            kegiatan_target: kegiatan_target, 
            kegiatan_satuan: kegiatan_satuan 
        },
        success: function(data) {
            modal.find('#modalFormContent').html(data);
        }
    });

});

");



$this->registerJs("
$('#updateModal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget); // Button that triggered the modal
    var url = button.data('url'); // Extract info from data-url attributes

    var modal = $(this);
    $.ajax({
        url: url,
        type: 'GET',
        success: function(data) {
            modal.find('#modalUpdateFormContent').html(data);
        }
    });
});
");

$this->registerJs("
$('#createModalRincianBelanja').on('show.bs.modal', function (event) {
    var modal = $(this);
    var button = $(event.relatedTarget);  // Button yang memicu modal

    // Ambil refsimonacascadingkegiatan_id dari tombol
    var refsimonacascadingkegiatan_id = button.data('refsimonacascadingkegiatan_id');
    var refcascadingkegiatan_id = button.data('refcascadingkegiatan_id');
    var refkegiatan_id = button.data('refkegiatan_id');
    var refcascadingprogram_id = button.data('refcascadingprogram_id');
    var refprogram_id = button.data('refprogram_id');
    var refperiode_id = button.data('refperiode_id');

    // Set nilai ke input field di form modal
    modal.find('#refsimonacascadingkegiatan_id').val(refsimonacascadingkegiatan_id);
    modal.find('#refcascadingkegiatan_id').val(refcascadingkegiatan_id);
    modal.find('#refkegiatan_id').val(refkegiatan_id);
    modal.find('#refcascadingprogram_id').val(refcascadingprogram_id);
    modal.find('#refprogram_id').val(refprogram_id);
    modal.find('#refperiode_id').val(refperiode_id);

    // Load konten form (opsional, jika ingin dinamis)
$.ajax({
    url: '" . Url::to(['simona-rincianbelanjacascadingkegiatan/create']) . "',
    type: 'GET',
    data: {
        refsimonacascadingkegiatan_id: refsimonacascadingkegiatan_id,
        refcascadingkegiatan_id: refcascadingkegiatan_id,
        refkegiatan_id: refkegiatan_id,
        refcascadingprogram_id: refcascadingprogram_id,
        refprogram_id: refprogram_id,
        refperiode_id: refperiode_id
    },
    success: function(data) {
        console.log('Form loaded successfully');
        modal.find('#modalFormContentRincianBelanja').html(data);
    },
    error: function(err) {
        console.log('Error loading form:', err);
    }
});

});
");




$this->registerJs("
$('#updateModalRincianBelanja').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget); // Button that triggered the modal
    var url = button.data('url'); // Extract info from data-url attributes

    var modal = $(this);
    $.ajax({
        url: url,
        type: 'GET',
        success: function(data) {
            modal.find('#modalUpdateFormContentRincianBelanja').html(data);
        }
    });
});
");

$this->registerJs("
    $('.btn-upload-keluaran').on('click', function () {
        var modalId = $(this).data('bs-target');
        var refsimonacascadingkegiatan_id = $(this).data('refsimonacascadingkegiatan_id');
        var refcascadingkegiatan_id = $(this).data('refcascadingkegiatan_id');
        var refkegiatan_id = $(this).data('refkegiatan_id');
        var refcascadingprogram_id = $(this).data('refcascadingprogram_id');
        var refprogram_id = $(this).data('refprogram_id');

        // Isi nilai hidden input di modal
        $(modalId).find('#refsimonacascadingkegiatan_id').val(refsimonacascadingkegiatan_id);
        $(modalId).find('#refcascadingkegiatan_id').val(refcascadingkegiatan_id);
        $(modalId).find('#refkegiatan_id').val(refkegiatan_id);
        $(modalId).find('#refcascadingprogram_id').val(refcascadingprogram_id);
        $(modalId).find('#refprogram_id').val(refprogram_id);
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
                            <li class="breadcrumb-item" aria-current="page">Perencanaan Cascading Kegiatan</li>
                        </ul>
                    </div>
                    <div class="col-md-12">
                        <div class="page-header-title">
                            <h2 class="mb-0">Perencanaan Cascading Kegiatan</h2>
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
                            <i class="fas fa-pen-fancy"></i>Periode SAKIP Cascading Kegiatan - <?= Html::encode(ucwords(strtolower($nama_skpd))) ?>
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <!-- Dropdown filter berdasarkan refperiode_id -->
                                <?= \yii\helpers\Html::beginForm(['view-kegiatan'], 'get', ['class' => 'form-inline']); ?>
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
                    </div>
                </div>
                <!-- End Card -->

                <!-- Table Start -->
                <div class="card">
                    <div class="card-header">
                        <h5>Data Perencanaan Kegiatan</h5>
                        <small>List Data</small>
                        <br>


                    </div>
                    <div class="card-body">
                        <?php if (Yii::$app->session->hasFlash('success')): ?>
                            <div class="alert alert-success">
                                <?= Yii::$app->session->getFlash('success') ?>
                            </div>
                        <?php endif; ?>

                        <?php if (Yii::$app->session->hasFlash('error')): ?>
                            <div class="alert alert-danger">
                                <?= Yii::$app->session->getFlash('error') ?>
                            </div>
                        <?php endif; ?>


                        <?php if (empty($dataProvider->getModels())): ?>
                            <div class="alert alert-warning mt-4">
                                Data tidak ada untuk periode yang dipilih.
                            </div>
                        <?php else: ?>
                            <div class="dt-responsive table-responsive">
                                <table id="table-style-hover" class="table table-striped table-hover table-bordered nowrap" style="font-size:xx-small;">
                                    <?php
                                    $currentRefsasaranrenstraId = null; // Variable to track current refsasaranrenstra_id
                                    $currentRefcascadingprogramId = null; // Variable to track current refcascadingprogram_id
                                    $currentRefkegiatanId = null; // Variable to track current refkegiatan_id
                                    $totalAnggaran = 0; // Variable to track total anggaran for the current kegiatan

                                    foreach ($dataProvider->getModels() as $model): ?>
                                        <!-- Check if the refsasaranrenstra_id has changed to display the mission, goal, and target -->
                                        <?php if ($currentRefsasaranrenstraId !== $model->refCascadingProgram->refsasaranrenstra_id): ?>
                                            <tr>
                                                <th colspan="6" style="background-color: #04A9F5; color:white; white-space: normal;"><?= $model->refCascadingProgram->refmisi->uraian_misi ?></th>
                                            </tr>
                                            <tr>
                                                <th colspan="6" style="background-color: #e23c3c; color:white; white-space: normal;">(Tujuan)<?= $model->refCascadingProgram->reftujuan->uraian_tujuan ?></th>
                                            </tr>
                                            <tr>
                                                <th colspan="6" style="background-color: #6ee15b; color:white; white-space: normal;">(Sasaran)<?= $model->refCascadingProgram->refsasaran->uraian_sasaran ?></th>
                                            </tr>
                                            <?php $currentRefsasaranrenstraId = $model->refCascadingProgram->refsasaranrenstra_id; ?>
                                        <?php endif; ?>

                                        <!-- Check if the cascading program has changed to display the program -->
                                        <?php if ($currentRefcascadingprogramId !== $model->refcascadingprogram_id): ?>
                                            <tr>
                                                <th colspan="6" style="background-color: #16537e; color:white; white-space: normal;"><?= $model->refCascadingProgram->refProgram->kode_program ?> - <?= $model->refCascadingProgram->refProgram->nama_program ?></th>
                                            </tr>
                                            <?php $currentRefcascadingprogramId = $model->refcascadingprogram_id; ?>
                                        <?php endif; ?>

                                        <!-- Display Kegiatan and group by kegiatan_id -->
                                        <?php if ($currentRefkegiatanId !== $model->refkegiatan_id): ?>
                                            <tr>
                                                <th style="background-color: #1DE9B6; color:white;"><?= $model->refKegiatan->kode_kegiatan ?></th>
                                                <th colspan="2" style="background-color: #1DE9B6; color:white; white-space: normal;"><?= $model->refKegiatan->nama_kegiatan ?></th>
                                                <th style="background-color: #1DE9B6; color:white;">Satuan</th>
                                                <th style="background-color: #1DE9B6; color:white;"><?= $model->refPeriode->periode ?></th>
                                                <th style="background-color: #1DE9B6; color:white;">Anggaran</th>
                                            </tr>
                                            <?php
                                            $currentRefkegiatanId = $model->refkegiatan_id;
                                            $totalAnggaran = 0; // Reset total for new kegiatan
                                            ?>
                                        <?php endif; ?>

                                        <tr>
                                            <?php
                                            $assignments = Yii::$app->authManager->getAssignments(Yii::$app->user->getId());
                                            if (isset($assignments['pegawai'])) {

                                            ?>
                                                <th colspan="2">Sasaran/Indikator</th>
                                            <?php } elseif (isset($assignments['skpd'])) { ?>
                                                <th>
                                                    <?= Html::a('<i class="fa fa-eye"></i>', ['simona-perencanaan-kegiatan/view-subkegiatan', 'refcascadingkegiatan_id' => $model->refcascadingkegiatan_id], ['class' => 'btn btn-primary btn-sm', 'title' => 'View']) ?>
                                                    <?= Html::button('<i class="fas fa-plus"></i>', [
                                                        'class' => 'btn btn-success btn-sm mx-2',
                                                        'title' => 'Tambah Rincian',
                                                        'data-bs-toggle' => 'modal',
                                                        'data-bs-target' => '#createModal',
                                                        'data-refcascadingprogram_id' => $model->refcascadingprogram_id,
                                                        'data-refcascadingkegiatan_id' => $model->refcascadingkegiatan_id,
                                                        'data-refperiode_id' => $model->refperiode_id,
                                                        'data-refsasaranrenstra_id' => $model->refsasaranrenstra_id,
                                                        'data-refindikatorsasaranrenstra_id' => $model->refindikatorsasaranrenstra_id,
                                                        'data-refprogram_id' => $model->refprogram_id,
                                                        'data-refkegiatan_id' => $model->refkegiatan_id,
                                                        'data-uraian_sasarankegiatan' => $model->uraian_sasarankegiatan,
                                                        'data-uraian_indikatorkegiatan' => $model->uraian_indikatorkegiatan,
                                                        'data-kegiatan_target' => $model->kegiatan_target,
                                                        'data-kegiatan_satuan' => $model->kegiatan_satuan,
                                                    ]) ?>

                                                </th>

                                                <th>Sasaran/Indikator</th>
                                            <?php } ?>

                                            <th style="white-space: normal;"><?= $model->uraian_sasarankegiatan ?> - <?= $model->uraian_indikatorkegiatan ?></th>
                                            <th><?= $model->kegiatan_satuan ?></th>
                                            <th><?= $model->kegiatan_target ?></th>

                                            <?php
                                            $subkegiatanAnggaran = SakipCascadingsubkegiatan::find()
                                                ->where([
                                                    'refkegiatan_id' => $model->refkegiatan_id,
                                                    'refcascadingkegiatan_id' => $model->refcascadingkegiatan_id,
                                                ])
                                                ->sum('CAST(subkegiatan_anggaran AS UNSIGNED)');

                                            $totalAnggaran += $subkegiatanAnggaran;
                                            ?>

                                            <th><?= 'Rp. ' . number_format($totalAnggaran, 0, ',', '.'); ?></th>
                                        </tr>

                                        <!-- Accordion row -->
                                        <tr>
                                            <td colspan="6">
                                                <div class="accordion" id="accordion-<?= $model->refkegiatan_id ?>">
                                                    <div class="accordion-item">
                                                        <h2 class="accordion-header" id="heading-<?= $model->refkegiatan_id ?>">
                                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-<?= $model->refkegiatan_id ?>" aria-expanded="false" aria-controls="collapse-<?= $model->refkegiatan_id ?>">
                                                                Detail Kegiatan <?= $model->refKegiatan->nama_kegiatan ?>
                                                            </button>
                                                        </h2>
                                                        <div id="collapse-<?= $model->refkegiatan_id ?>" class="accordion-collapse collapse" aria-labelledby="heading-<?= $model->refkegiatan_id ?>" data-bs-parent="#accordion-<?= $model->refkegiatan_id ?>">
                                                            <div class="accordion-body">
                                                                <?php
                                                                $totalAnggaranRincianKeseluruhan = 0; // Variable to track overall total of all rincian belanja

                                                                $details = SimonaCascadingKegiatan::find()->where(['refkegiatan_id' => $model->refkegiatan_id])->all();
                                                                foreach ($details as $detail):
                                                                ?>
                                                                    <h5><?= $detail->nama_tahapankegiatan ?></h5>
                                                                    <div class="dt-responsive table-responsive">
                                                                        <table id="table-style-hover" class="table table-striped table-hover table-bordered nowrap" style="font-size:xx-small;">
                                                                            <tr>
                                                                                <th><b>Kegiatan</b></th>
                                                                                <td><b><?= $model->refKegiatan->nama_kegiatan ?></b></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <th>Tanggal Mulai</th>
                                                                                <td><?= $detail->date_start ?></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <th>Tanggal Selesai</th>
                                                                                <td><?= $detail->expired_date ?></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <th>Dokumen (Bappeda)</th>
                                                                                <th>Dokumen (OPD)</th>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>
                                                                                    <?php
                                                                                    $assignments = Yii::$app->authManager->getAssignments(Yii::$app->user->getId());
                                                                                    if (isset($assignments['pegawai'])) {
                                                                                    ?>
                                                                                        <!-- Tombol untuk membuka modal upload dokumen -->
                                                                                        <?= Html::button('Tambah Upload Dokumen (BAPPEDA)', [
                                                                                            'class' => 'btn btn-primary btn-sm',
                                                                                            'data-bs-toggle' => 'modal',
                                                                                            'data-bs-target' => '#uploadModal-' . $detail->refsimonacascadingkegiatan_id
                                                                                        ]) ?>
                                                                                    <?php } ?>

                                                                                    <!-- Button to open the modal to view the file -->
                                                                                    <?= Html::button('View Document', [
                                                                                        'class' => 'btn btn-info btn-sm',
                                                                                        'data-bs-toggle' => 'modal',
                                                                                        'data-bs-target' => '#viewModal-' . $detail->refsimonacascadingkegiatan_id
                                                                                    ]) ?>
                                                                                </td>
                                                                                <td>
                                                                                    <?php
                                                                                    $assignments = Yii::$app->authManager->getAssignments(Yii::$app->user->getId());
                                                                                    if (isset($assignments['skpd'])) {
                                                                                    ?>
                                                                                        <!-- Tombol untuk membuka modal upload dokumen -->
                                                                                        <?= Html::button('Tambah Upload Dokumen', [
                                                                                            'class' => 'btn btn-primary btn-sm',
                                                                                            'data-bs-toggle' => 'modal',
                                                                                            'data-bs-target' => '#uploadModalOpd-' . $detail->refsimonacascadingkegiatan_id
                                                                                        ]) ?>
                                                                                    <?php } ?>

                                                                                    <!-- Button to open the modal to view the file -->
                                                                                    <?= Html::button('View Document', [
                                                                                        'class' => 'btn btn-info btn-sm',
                                                                                        'data-bs-toggle' => 'modal',
                                                                                        'data-bs-target' => '#viewModalOpd-' . $detail->refsimonacascadingkegiatan_id
                                                                                    ]) ?>
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td colspan="2">
                                                                                    <?= Html::button('<i class="fas fa-edit">Update Tahapan Kegiatan</i>', [
                                                                                        'class' => 'btn btn-success btn-sm',
                                                                                        'title' => 'Update',
                                                                                        'data-bs-toggle' => 'modal',
                                                                                        'data-bs-target' => '#updateModal',
                                                                                        'data-url' => Url::to(['simona-cascadingkegiatan/update', 'refsimonacascadingkegiatan_id' => $detail->refsimonacascadingkegiatan_id])
                                                                                    ]) ?>
                                                                                </td>
                                                                            </tr>
                                                                        </table>
                                                                    </div>

                                                                    <!--  -->
                                                                    <!-- Modal to View the Document -->
                                                                    <div class="modal fade" id="viewModal-<?= $detail->refsimonacascadingkegiatan_id ?>" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
                                                                        <div class="modal-dialog">
                                                                            <div class="modal-content" style="border-radius: 20px;">
                                                                                <div class="modal-header">
                                                                                    <h5 class="modal-title" id="viewModalLabel">View Document (BAPPEDA)</h5>
                                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                                </div>
                                                                                <div class="modal-body" style="padding-bottom:20px; padding-right:15px; padding-left:15px;">
                                                                                    <?php
                                                                                    // Fetch all documents associated with the refsimonacascadingkegiatan_id
                                                                                    $mediaFiles = SimonaMediacascadingkegiatan::findAll(['refsimonacascadingkegiatan_id' => $detail->refsimonacascadingkegiatan_id]);

                                                                                    if ($mediaFiles) {
                                                                                        // Table to display file details for each file
                                                                                        echo '<table class="table table-bordered">';
                                                                                        foreach ($mediaFiles as $media) {
                                                                                            if ($media->file) {
                                                                                                echo '<tr>';
                                                                                                echo '<td><b>File Name</b></td>';
                                                                                                echo '<td>' . Html::encode($media->file) . '</td>';
                                                                                                echo '</tr>';
                                                                                                echo '<tr>';
                                                                                                echo '<td><b>Download</b></td>';
                                                                                                echo '<td>' . Html::a('Download ' . $media->file, ['simona-mediacascadingkegiatan/download', 'refsimonamediacascadingkegiatan_id' => $media->refsimonamediacascadingkegiatan_id], ['class' => 'btn btn-success', 'target' => '_blank']) . '</td>';
                                                                                                echo '</tr>';
                                                                                            }
                                                                                        }
                                                                                        echo '</table>';
                                                                                    } else {
                                                                                        // If no documents are available
                                                                                        echo 'No document available.';
                                                                                    }
                                                                                    ?>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>


                                                                    <!--  -->
                                                                    <div class="modal fade" id="uploadModal-<?= $detail->refsimonacascadingkegiatan_id ?>" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
                                                                        <div class="modal-dialog">
                                                                            <div class="modal-content" style="border-radius: 20px;">
                                                                                <div class="modal-header">
                                                                                    <h5 class="modal-title" id="uploadModalLabel">Upload Dokumen (BAPPEDA)</h5>
                                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                                </div>
                                                                                <div class="modal-body" style="padding-bottom:20px; padding-right:15px; padding-left:15px;">
                                                                                    <?php $form = ActiveForm::begin([
                                                                                        'action' => ['simona-mediacascadingkegiatan/create'],
                                                                                        'options' => ['enctype' => 'multipart/form-data']
                                                                                    ]); ?>

                                                                                    <?= $form->field($uploadModel, 'refsimonacascadingkegiatan_id')->hiddenInput(['value' => $detail->refsimonacascadingkegiatan_id])->label(false) ?>
                                                                                    <?= $form->field($uploadModel, 'file_docs[]')->fileInput(['multiple' => true]) ?> <!-- Multiple file input -->
                                                                                    <?= $form->field($uploadModel, 'nama_file')->textInput(['maxlength' => true]) ?>
                                                                                    <?= $form->field($uploadModel, 'refuser_id')->hiddenInput(['value' => Yii::$app->user->id])->label(false) ?>
                                                                                    <?= $form->field($uploadModel, 'refskpd_id')->hiddenInput(['value' => Yii::$app->user->identity->refskpd_id])->label(false) ?>

                                                                                    <div class="form-group">
                                                                                        <?= Html::submitButton('Upload', ['class' => 'btn btn-success']) ?>
                                                                                    </div>

                                                                                    <?php ActiveForm::end(); ?>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <!--  -->
                                                                    <!-- Modal to View the Document -->
                                                                    <div class="modal fade" id="viewModalOpd-<?= $detail->refsimonacascadingkegiatan_id ?>" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
                                                                        <div class="modal-dialog">
                                                                            <div class="modal-content" style="border-radius: 20px;">
                                                                                <div class="modal-header">
                                                                                    <h5 class="modal-title" id="viewModalLabel">View Document</h5>
                                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                                </div>
                                                                                <div class="modal-body" style="padding-bottom:20px; padding-right:15px; padding-left:15px;">
                                                                                    <?php
                                                                                    // Fetch all documents associated with the refsimonacascadingkegiatan_id
                                                                                    $mediaFiles = SimonaMediacascadingkegiatanOpd::findAll(['refsimonacascadingkegiatan_id' => $detail->refsimonacascadingkegiatan_id]);

                                                                                    if ($mediaFiles) {
                                                                                        // Table to display file details for each file
                                                                                        echo '<table class="table table-bordered">';
                                                                                        foreach ($mediaFiles as $media) {
                                                                                            if ($media->file) {
                                                                                                echo '<tr>';
                                                                                                echo '<td><b>File Name</b></td>';
                                                                                                echo '<td>' . Html::encode($media->file) . '</td>';
                                                                                                echo '</tr>';
                                                                                                echo '<tr>';
                                                                                                echo '<td><b>Download</b></td>';
                                                                                                echo '<td>' . Html::a('Download ' . $media->file, ['simona-mediacascadingkegiatan-opd/download', 'refsimonamediacascadingkegiatanopd_id' => $media->refsimonamediacascadingkegiatanopd_id], ['class' => 'btn btn-success', 'target' => '_blank']) . '</td>';
                                                                                                echo '</tr>';
                                                                                            }
                                                                                        }
                                                                                        echo '</table>';
                                                                                    } else {
                                                                                        // If no documents are available
                                                                                        echo 'No document available.';
                                                                                    }
                                                                                    ?>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>


                                                                    <!--  -->
                                                                    <div class="modal fade" id="uploadModalOpd-<?= $detail->refsimonacascadingkegiatan_id ?>" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
                                                                        <div class="modal-dialog">
                                                                            <div class="modal-content" style="border-radius: 20px;">
                                                                                <div class="modal-header">
                                                                                    <h5 class="modal-title" id="uploadModalLabel">Upload Dokumen</h5>
                                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                                </div>
                                                                                <div class="modal-body" style="padding-bottom:20px; padding-right:15px; padding-left:15px;">
                                                                                    <?php $form = ActiveForm::begin([
                                                                                        'action' => ['simona-mediacascadingkegiatan-opd/create'],
                                                                                        'options' => ['enctype' => 'multipart/form-data']
                                                                                    ]); ?>

                                                                                    <?= $form->field($uploadModelOpd, 'refsimonacascadingkegiatan_id')->hiddenInput(['value' => $detail->refsimonacascadingkegiatan_id])->label(false) ?>
                                                                                    <?= $form->field($uploadModelOpd, 'file_docs[]')->fileInput(['multiple' => true]) ?> <!-- Multiple file input -->
                                                                                    <?= $form->field($uploadModelOpd, 'nama_file')->textInput(['maxlength' => true]) ?>
                                                                                    <?= $form->field($uploadModelOpd, 'refuser_id')->hiddenInput(['value' => Yii::$app->user->id])->label(false) ?>
                                                                                    <?= $form->field($uploadModelOpd, 'refskpd_id')->hiddenInput(['value' => Yii::$app->user->identity->refskpd_id])->label(false) ?>

                                                                                    <div class="form-group">
                                                                                        <?= Html::submitButton('Upload', ['class' => 'btn btn-success']) ?>
                                                                                    </div>

                                                                                    <?php ActiveForm::end(); ?>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <!--  -->


                                                                    <div class="accordion mb-5" id="sub-accordion-<?= $detail->refsimonacascadingkegiatan_id ?>">
                                                                        <div class="accordion-item">
                                                                            <h2 class="accordion-header" id="sub-heading-<?= $detail->refsimonacascadingkegiatan_id ?>">
                                                                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#sub-collapse-<?= $detail->refsimonacascadingkegiatan_id ?>" aria-expanded="false" aria-controls="sub-collapse-<?= $detail->refsimonacascadingkegiatan_id ?>">
                                                                                    Rincian Belanja <?= $detail->nama_tahapankegiatan ?>
                                                                                </button>
                                                                            </h2>
                                                                            <div id="sub-collapse-<?= $detail->refsimonacascadingkegiatan_id ?>" class="accordion-collapse collapse" aria-labelledby="sub-heading-<?= $detail->refsimonacascadingkegiatan_id ?>" data-bs-parent="#sub-accordion-<?= $detail->refsimonacascadingkegiatan_id ?>">
                                                                                <div class="accordion-body">
                                                                                    <!-- Isi detail tingkat kedua -->
                                                                                    <?php
                                                                                    $totalAnggaranRincian = 0;
                                                                                    $expenses = SimonaRincianbelanjacascadingkegiatan::find()->where(['refsimonacascadingkegiatan_id' => $detail->refsimonacascadingkegiatan_id])->all();
                                                                                    if (empty($expenses)): ?>
                                                                                        <p><i>Belum Ada Rincian Belanja</i></p>
                                                                                        <?php else:

                                                                                        foreach ($expenses as $expense):
                                                                                            $totalAnggaranRincian += (int) $expense->anggaran_rincianbelanja; // Summing the anggaran_rincianbelanja
                                                                                        ?>
                                                                                            <h5>Rincian Belanja <?= Html::encode($detail->nama_tahapankegiatan) ?></h5>
                                                                                            <div class="dt-responsive table-responsive">
                                                                                                <table id="table-style-hover" class="table table-striped table-hover table-bordered nowrap" style="font-size:xx-small;">
                                                                                                    <tr>
                                                                                                        <th>Detail Belanja</th>
                                                                                                        <td colspan="2"><?= Html::encode($expense->detail_rincianbelanja) ?></td>
                                                                                                    </tr>
                                                                                                    <tr>
                                                                                                        <th>Satuan Belanja</th>
                                                                                                        <td><?= Html::encode($expense->jumlah_rincianbelanja) ?></td>
                                                                                                    </tr>
                                                                                                    <tr>
                                                                                                        <th>Jumlah Belanja</th>
                                                                                                        <td><?= Html::encode($expense->satuan_rincianbelanja) ?></td>
                                                                                                    </tr>
                                                                                                    <tr>
                                                                                                        <th>Anggaran Belanja</th>
                                                                                                        <td><?= 'Rp. ' . number_format($expense->anggaran_rincianbelanja, 0, ',', '.'); ?></td>
                                                                                                    </tr>
                                                                                                    <tr>
                                                                                                        <th>Keluaran Dokumen</th>
                                                                                                        <?php
                                                                                                        $assignments = Yii::$app->authManager->getAssignments(Yii::$app->user->getId());
                                                                                                        if (isset($assignments['skpd'])) {
                                                                                                        ?>
                                                                                                            <td colspan="2"><?= Html::button('Tambah Keluaran Dokumen', [
                                                                                                                                'class' => 'btn btn-primary btn-sm btn-upload-keluaran',
                                                                                                                                'data-bs-toggle' => 'modal',
                                                                                                                                'data-bs-target' => '#uploadModalKeluaran-' . $expense->refsimonarincianbelanjacascadingkegiatan_id,
                                                                                                                                'data-refsimonacascadingkegiatan_id' => $expense->refsimonacascadingkegiatan_id,
                                                                                                                                'data-refcascadingkegiatan_id' => $detail->refcascadingkegiatan_id,
                                                                                                                                'data-refkegiatan_id' => $detail->refkegiatan_id,
                                                                                                                                'data-refcascadingprogram_id' => $detail->refcascadingprogram_id,
                                                                                                                                'data-refprogram_id' => $detail->refprogram_id,
                                                                                                                            ]) ?>
                                                                                                                <!-- Button to open the modal to view the file -->
                                                                                                                <?= Html::button('View Document', [
                                                                                                                    'class' => 'btn btn-info btn-sm',
                                                                                                                    'data-bs-toggle' => 'modal',
                                                                                                                    'data-bs-target' => '#viewModalKeluaran-' . $expense->refsimonarincianbelanjacascadingkegiatan_id
                                                                                                                ]) ?>

                                                                                                            </td>
                                                                                                        <?php } elseif (isset($assignments['pegawai'])) { ?>
                                                                                                            <td colspan="2">
                                                                                                                <!-- Button to open the modal to view the file -->
                                                                                                                <?= Html::button('View Document', [
                                                                                                                    'class' => 'btn btn-info btn-sm',
                                                                                                                    'data-bs-toggle' => 'modal',
                                                                                                                    'data-bs-target' => '#viewModalKeluaran-' . $expense->refsimonarincianbelanjacascadingkegiatan_id
                                                                                                                ]) ?>

                                                                                                            </td>
                                                                                                        <?php } ?>

                                                                                                    </tr>
                                                                                                    <tr>
                                                                                                        <td colspan="2">
                                                                                                            <?= Html::button('<i class="fas fa-edit">Update Rincian Belanja</i>', [
                                                                                                                'class' => 'btn btn-success btn-sm',
                                                                                                                'title' => 'Update',
                                                                                                                'data-bs-toggle' => 'modal',
                                                                                                                'data-bs-target' => '#updateModalRincianBelanja',
                                                                                                                'data-url' => Url::to(['simona-rincianbelanjacascadingkegiatan/update', 'refsimonarincianbelanjacascadingkegiatan_id' => $expense->refsimonarincianbelanjacascadingkegiatan_id])
                                                                                                            ]) ?>
                                                                                                        </td>
                                                                                                    </tr>

                                                                                                </table>
                                                                                            </div>

                                                                                            <!-- Modal to View the Document -->
                                                                                            <div class="modal fade" id="viewModalKeluaran-<?= $expense->refsimonarincianbelanjacascadingkegiatan_id ?>" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
                                                                                                <div class="modal-dialog">
                                                                                                    <div class="modal-content" style="border-radius: 20px;">
                                                                                                        <div class="modal-header">
                                                                                                            <h5 class="modal-title" id="viewModalLabel">View Document Keluaran</h5>
                                                                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                                                        </div>
                                                                                                        <div class="modal-body" style="padding-bottom:20px; padding-right:15px; padding-left:15px;">
                                                                                                            <?php
                                                                                                            // Fetch all documents associated with the refsimonakeluaranmediacascadingkegiatan_id
                                                                                                            $mediaFiles = SimonaKeluaranmediacascadingkegiatan::findAll(['refsimonarincianbelanjacascadingkegiatan_id' => $expense->refsimonarincianbelanjacascadingkegiatan_id]);

                                                                                                            if ($mediaFiles) {
                                                                                                                // Table to display file details for each file
                                                                                                                echo '<table class="table table-bordered">';
                                                                                                                foreach ($mediaFiles as $media) {
                                                                                                                    if ($media->file) {
                                                                                                                        echo '<tr>';
                                                                                                                        echo '<td><b>File Name</b></td>';
                                                                                                                        echo '<td>' . Html::encode($media->file) . '</td>';
                                                                                                                        echo '</tr>';
                                                                                                                        echo '<tr>';
                                                                                                                        echo '<td><b>Download</b></td>';
                                                                                                                        echo '<td>' . Html::a('Download ' . $media->file, ['simona-keluaranmediacascadingkegiatan/download', 'refsimonakeluaranmediacascadingkegiatan_id' => $media->refsimonakeluaranmediacascadingkegiatan_id], ['class' => 'btn btn-success', 'target' => '_blank']) . '</td>';
                                                                                                                        echo '</tr>';
                                                                                                                    }
                                                                                                                }
                                                                                                                echo '</table>';
                                                                                                            } else {
                                                                                                                // If no documents are available
                                                                                                                echo 'No document available.';
                                                                                                            }
                                                                                                            ?>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>


                                                                                            <!--  -->
                                                                                            <div class="modal fade" id="uploadModalKeluaran-<?= $expense->refsimonarincianbelanjacascadingkegiatan_id ?>" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
                                                                                                <div class="modal-dialog">
                                                                                                    <div class="modal-content" style="border-radius: 20px;">
                                                                                                        <div class="modal-header">
                                                                                                            <h5 class="modal-title" id="uploadModalLabel">Upload Dokumen Keluaran</h5>
                                                                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                                                        </div>
                                                                                                        <div class="modal-body" style="padding-bottom:20px; padding-right:15px; padding-left:15px;">
                                                                                                            <?php $form = ActiveForm::begin([
                                                                                                                'action' => ['simona-keluaranmediacascadingkegiatan/create'],
                                                                                                                'options' => ['enctype' => 'multipart/form-data']
                                                                                                            ]); ?>

                                                                                                            <?= $form->field($uploadModelKeluaran, 'refsimonarincianbelanjacascadingkegiatan_id')->hiddenInput(['value' => $expense->refsimonarincianbelanjacascadingkegiatan_id])->label(false) ?>
                                                                                                            <?= $form->field($uploadModelKeluaran, 'refsimonacascadingkegiatan_id')->hiddenInput(['id' => 'refsimonacascadingkegiatan_id'])->label(false) ?>
                                                                                                            <?= $form->field($uploadModelKeluaran, 'refcascadingkegiatan_id')->hiddenInput(['id' => 'refcascadingkegiatan_id'])->label(false) ?>
                                                                                                            <?= $form->field($uploadModelKeluaran, 'refkegiatan_id')->hiddenInput(['id' => 'refkegiatan_id'])->label(false) ?>
                                                                                                            <?= $form->field($uploadModelKeluaran, 'refcascadingprogram_id')->hiddenInput(['id' => 'refcascadingprogram_id'])->label(false) ?>
                                                                                                            <?= $form->field($uploadModelKeluaran, 'refprogram_id')->hiddenInput(['id' => 'refprogram_id'])->label(false) ?>
                                                                                                            <?= $form->field($uploadModelKeluaran, 'file_docs[]')->fileInput(['multiple' => true]) ?> <!-- Multiple file input -->
                                                                                                            <?= $form->field($uploadModelKeluaran, 'nama_file')->textInput(['maxlength' => true]) ?>
                                                                                                            <?= $form->field($uploadModelKeluaran, 'refuser_id')->hiddenInput(['value' => Yii::$app->user->id])->label(false) ?>
                                                                                                            <?= $form->field($uploadModelKeluaran, 'refskpd_id')->hiddenInput(['value' => Yii::$app->user->identity->refskpd_id])->label(false) ?>

                                                                                                            <div class="form-group">
                                                                                                                <?= Html::submitButton('Upload', ['class' => 'btn btn-success']) ?>
                                                                                                            </div>

                                                                                                            <?php ActiveForm::end(); ?>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                            <!--  -->
                                                                                    <?php endforeach;
                                                                                    endif;
                                                                                    // Add the total for this tahap to the overall total
                                                                                    $totalAnggaranRincianKeseluruhan += $totalAnggaranRincian;  ?>
                                                                                    <?php

                                                                                    $subkegiatanAnggaran = SakipCascadingsubkegiatan::find()
                                                                                        ->where([
                                                                                            'refcascadingkegiatan_id' => $detail->refcascadingkegiatan_id,
                                                                                            'refkegiatan_id' => $detail->refkegiatan_id,
                                                                                        ])
                                                                                        ->sum('CAST(subkegiatan_anggaran AS UNSIGNED)');

                                                                                    $remainingAnggaran = $subkegiatanAnggaran - $totalAnggaranRincianKeseluruhan;
                                                                                    ?>
                                                                                    <div class="alert alert-info mt-3">
                                                                                        <strong>Total Rincian Belanja Tahap Ini:</strong> <?= 'Rp. ' . number_format($totalAnggaranRincian, 0, ',', '.'); ?>
                                                                                    </div>

                                                                                    <!-- Conditional display for the button or message -->
                                                                                    <?php if ($totalAnggaranRincianKeseluruhan > $subkegiatanAnggaran): ?>
                                                                                        <div class="alert alert-danger mt-3">
                                                                                            <strong>Sisa Anggaran Kegiatan Ini Sudah Habis</strong>
                                                                                        </div>
                                                                                    <?php else: ?>
                                                                                        <?= Html::button('<i class="fas fa-plus"> Tambah Rincian Belanja</i>', [
                                                                                            'class' => 'btn btn-success btn-sm mx-2',
                                                                                            'title' => 'Tambah Rincian Belanja',
                                                                                            'data-bs-toggle' => 'modal',
                                                                                            'data-bs-target' => '#createModalRincianBelanja',
                                                                                            'data-refsimonacascadingkegiatan_id' => $detail->refsimonacascadingkegiatan_id,
                                                                                            'data-refcascadingkegiatan_id' => $detail->refcascadingkegiatan_id,
                                                                                            'data-refcascadingprogram_id' => $detail->refcascadingprogram_id,
                                                                                            'data-refkegiatan_id' => $detail->refkegiatan_id,
                                                                                            'data-refprogram_id' => $detail->refprogram_id,
                                                                                            'data-refperiode_id' => $detail->refperiode_id,
                                                                                        ]) ?>
                                                                                    <?php endif; ?>



                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <hr>
                                                                <?php endforeach; ?>
                                                                <!-- Display the overall total at the bottom -->
                                                                <div class="alert alert-warning mt-3">
                                                                    <strong>Total Keseluruhan Rincian Belanja:</strong> <?= 'Rp. ' . number_format($totalAnggaranRincianKeseluruhan, 0, ',', '.'); ?>
                                                                </div>
                                                                <!-- Display the remaining budget -->
                                                                <div class="alert alert-danger mt-3">
                                                                    <strong>Sisa Anggaran:</strong> <?= 'Rp. ' . number_format($subkegiatanAnggaran, 0, ',', '.'); ?> - <?= 'Rp. ' . number_format($totalAnggaranRincianKeseluruhan, 0, ',', '.'); ?> = <?= 'Rp. ' . number_format($remainingAnggaran, 0, ',', '.'); ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>


                                    <?php endforeach; ?>
                                </table>



                            </div>
                        <?php endif; ?>

                        <!--  -->
                        <div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content" style="border-radius: 20px;">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="createModalLabel">Tambah Tahapan Kegiatan</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <!-- The form will be loaded here -->
                                        <div id="modalFormContent" style="padding-bottom:20px; padding-right:15px; padding-left:15px;">
                                            <!-- AJAX-loaded content will be injected here -->
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--  -->
                        <div class="modal fade" id="updateModal" tabindex="-1" aria-labelledby="updateModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content" style="border-radius: 20px;">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="updateModalLabel">Update Rincian Tahapan</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <!-- The form will be loaded here -->
                                        <div id="modalUpdateFormContent" style="padding-bottom:20px; padding-right:15px; padding-left:15px;">
                                            <!-- AJAX-loaded content will be injected here -->
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--  -->

                        <!--  -->
                        <div class="modal fade" id="createModalRincianBelanja" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content" style="border-radius: 20px;">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="createModalLabel">Tambah Rincian Belanja</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <!-- The form will be loaded here -->
                                        <div id="modalFormContentRincianBelanja" style="padding-bottom:20px; padding-right:15px; padding-left:15px;">
                                            <!-- AJAX-loaded content will be injected here -->
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--  -->
                        <div class="modal fade" id="updateModalRincianBelanja" tabindex="-1" aria-labelledby="updateModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content" style="border-radius: 20px;">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="updateModalLabel">Update Rincian Belanja</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <!-- The form will be loaded here -->
                                        <div id="modalUpdateFormContentRincianBelanja" style="padding-bottom:20px; padding-right:15px; padding-left:15px;">
                                            <!-- AJAX-loaded content will be injected here -->
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
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