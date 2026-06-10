<?php

use frontend\models\SakipCascadingkegiatan;
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
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use frontend\models\SakipIndikatortujuanrenstra;

/** @var yii\web\View $this */
/** @var frontend\models\search\SakipCascadingkegiatanSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Sakip Cascading Kegiatan';
$this->params['breadcrumbs'][] = $this->title;

$this->registerJs("
    $('#createModal').on('show.bs.modal', function (event) {
        var modal = $(this);
        $.ajax({
            url: '" . Url::to(['sakip-cascadingkegiatan/create']) . "',
            type: 'GET',
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
$('#createModalPenjabatSkpd').on('show.bs.modal', function (event) {
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
        url: '" . Url::to(['sakip-penjabatskpd-cascadingkegiatan/create']) . "',
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
            modal.find('#modalFormContentPenjabatSkpd').html(data);
  // Fetch filtered `refpenjabatskpd_id` options
            $.ajax({
                url: '" . Url::to(['sakip-penjabatskpd-cascadingkegiatan/fetch-penjabatskpd']) . "',
                type: 'GET',
                data: {
                    refperiode_id: refperiode_id,
                    refcascadingprogram_id: refcascadingprogram_id,  // Pass refcascadingprogram_id to filter out existing entries
                    refcascadingkegiatan_id: refcascadingkegiatan_id  // Pass refcascadingkegiatan_id to filter out existing entries
                },
                success: function(data) {
                    var refpenjabatskpdDropdown = modal.find('#refpenjabatskpd_id');
                    refpenjabatskpdDropdown.empty();  // Clear existing options
                    refpenjabatskpdDropdown.append('<option value=\"\">Pilih Penjabat SKPD</option>');
                    $.each(data, function(index, item) {
                        refpenjabatskpdDropdown.append('<option value=\"' + item.refpenjabatskpd_id + '\">' + item.nama_penjabat + '</option>');
                    });
                }
            });
                 // Send the data to load the form dynamically (optional)
    $.ajax({
        url: '" . Url::to(['sakip-penjabatskpd-cascadingkegiatan/fetch-indikator']) . "',
        type: 'GET',
        data: {
            refcascadingprogram_id: refcascadingprogram_id,
            refcascadingkegiatan_id: refcascadingkegiatan_id
        },
        success: function(data) {
            // Update the refindikatorkegiatan_id field with the fetched value
            modal.find('#refindikatorkegiatan_id').val(data.refindikatorkegiatan_id);
        }
    });
        }
    });

});

");

$this->registerJs("
$('#updateModalPenjabatSkpd').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget); // Button that triggered the modal
    var url = button.data('url'); // Extract info from data-url attributes

    var modal = $(this);
    $.ajax({
        url: url,
        type: 'GET',
        success: function(data) {
            modal.find('#modalUpdateFormContentPenjabatSkpd').html(data);
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
                            <li class="breadcrumb-item" aria-current="page">Cascading Kegiatan</li>
                        </ul>
                    </div>
                    <div class="col-md-12">
                        <div class="page-header-title">
                            <h2 class="mb-0">Cascading Kegiatan</h2>
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
                            <i class="fas fa-pen-fancy"></i> Cascading Kegiatan - <?= Html::decode($nama_skpd) ?>
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-3">

                            </div>
                        </div>
                    </div>
                </div>
                <!-- End Card -->

                <!-- Table Start -->
                <div class="card">
                    <div class="card-header">
                        <h5>Data SAKIP Cascading Kegiatan</h5>
                        <small>List Data</small>
                        <br>


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


                        <div class="row">
                            <div class="col-sm-12">
                                <!-- Dropdown filter berdasarkan refperiode_id -->
                                <?= \yii\helpers\Html::beginForm(['index'], 'get', ['class' => 'form-inline']); ?>
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
                                                <th colspan="6"><?= $model->refCascadingProgram->refmisi->uraian_misi ?></th>
                                            </tr>
                                            <tr>
                                                <th colspan="6"><?= $model->refCascadingProgram->reftujuan->uraian_tujuan ?></th>
                                            </tr>
                                            <tr>
                                                <th colspan="6"><?= $model->refCascadingProgram->refsasaran->uraian_sasaran ?></th>
                                            </tr>
                                            <?php $currentRefsasaranrenstraId = $model->refCascadingProgram->refsasaranrenstra_id; ?>
                                        <?php endif; ?>

                                        <!-- Check if the cascading program has changed to display the program -->
                                        <?php if ($currentRefcascadingprogramId !== $model->refcascadingprogram_id): ?>
                                            <tr>
                                                <th colspan="6"><?= $model->refCascadingProgram->refProgram->kode_program ?> - <?= $model->refCascadingProgram->refProgram->nama_program ?></th>
                                            </tr>
                                            <?php $currentRefcascadingprogramId = $model->refcascadingprogram_id; ?>
                                        <?php endif; ?>

                                        <!-- Display Kegiatan and group by kegiatan_id -->
                                        <?php if ($currentRefkegiatanId !== $model->refkegiatan_id): ?>
                                            <tr>
                                                <th><?= $model->refKegiatan->kode_kegiatan ?></th>
                                                <th colspan="2"><?= $model->refKegiatan->nama_kegiatan ?></th>
                                                <th>Satuan</th>
                                                <th><?= $model->refPeriode->periode ?></th>
                                                <th>Anggaran</th>
                                            </tr>
                                            <?php
                                            $currentRefkegiatanId = $model->refkegiatan_id;
                                            $totalAnggaran = 0; // Reset total for new kegiatan
                                            ?>
                                        <?php endif; ?>

                                        <tr>
                                            <td>
                                                <?= Html::a('<i class="fa fa-eye"></i>', ['view-subkegiatan', 'refcascadingkegiatan_id' => $model->refcascadingkegiatan_id], ['class' => 'btn btn-primary btn-sm', 'title' => 'View']) ?>

                                            </td>

                                            <th>Sasaran/Indikator</th>
                                            <th><?= $model->uraian_sasarankegiatan ?> - <?= $model->uraian_indikatorkegiatan ?></th>
                                            <th><?= $model->kegiatan_satuan ?></th>
                                            <th><?= $model->kegiatan_target ?></th>

                                            <!-- Sum anggaran from the subkegiatan -->
                                            <?php
                                            // Fetch subkegiatan_anggaran from sakip_cascadingsubkegiatan for the current kegiatan
                                            $subkegiatanAnggaran = SakipCascadingsubkegiatan::find()
                                                ->where([
                                                    'refkegiatan_id' => $model->refkegiatan_id,
                                                    'refcascadingkegiatan_id' => $model->refcascadingkegiatan_id, // Ensure both IDs match
                                                ])
                                                ->sum('CAST(subkegiatan_anggaran AS UNSIGNED)'); // Ensure proper sum of numeric values

                                            $totalAnggaran += $subkegiatanAnggaran; // Accumulate anggaran
                                            ?>

                                            <th><?= 'Rp. ' . number_format($totalAnggaran, 0, ',', '.'); ?></th>
                                        </tr>

                                    <?php endforeach; ?>
                                </table>
                            </div>
                        <?php endif; ?>










                        <!--  -->
                        <div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="createModalLabel">Tambah Data SAKIP Cascading Kegiatan</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <!-- The form will be loaded here -->
                                        <div id="modalFormContent">
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
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="updateModalLabel">Update Data SAKIP Cascading Kegiatan</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <!-- The form will be loaded here -->
                                        <div id="modalUpdateFormContent">
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
                        <div class="modal fade" id="createModalPenjabatSkpd" tabindex="-1" aria-labelledby="createModalLabelPenjabatSkpd" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="createModalLabelPenjabatSkpd">Tambah Data SAKIP Penjabat SKPD</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <!-- The form will be loaded here -->
                                        <div id="modalFormContentPenjabatSkpd">
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
                        <div class="modal fade" id="updateModalPenjabat Skpd" tabindex="-1" aria-labelledby="updateModalLabelPenjabat Skpd" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="updateModalLabelPenjabat Skpd">Update Data SAKIP Penjabat Skpd</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <!-- The form will be loaded here -->
                                        <div id="modalUpdateFormContentPenjabat Skpd">
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