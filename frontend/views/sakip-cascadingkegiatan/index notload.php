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

$this->title = 'e-Sakip - Data Cascading Kegiatan';
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
                            <i class="fas fa-pen-fancy"></i>Periode SAKIP Cascading Kegiatan - <?= Html::encode(ucwords(strtolower($nama_skpd))) ?>
                        </h6>
                    </div>
                    <div class="card-body">
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
                    </div>
                </div>
                <!-- End Card -->

                <!-- Table Start -->
                <div class="card">
                    <div class="card-header" style="background-color: #04A9F5; padding: 8px;">
                        <h6 style="color: white; margin: 0; cursor: pointer;" id="toggleAll">
                            <i class="fas fa-pen-fancy"></i>Data SAKIP Cascading Kegiatan - <?= Html::encode(ucwords(strtolower($nama_skpd))) ?> (Periode <?= $selectedPeriodValue ?>)
                        </h6>
                    </div>
                    <div class="card-body" id="refresh">
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

                        <?= Html::button('<i class="fas fa-plus-square"></i> Data Cascading Kegiatan', [
                            'class' => 'btn btn-success mb-3',
                            'data-bs-toggle' => 'modal',
                            'data-bs-target' => '#createModal',
                        ]) ?>

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
                                    $currentRefprogramId = null;
                                    $totalAnggaran = 0; // Variable to track total anggaran for the current kegiatan

                                    foreach ($dataProvider->getModels() as $model): ?>
                                        <!-- Check if the refsasaranrenstra_id has changed to display the mission, goal, and target -->
                                        <?php if ($currentRefsasaranrenstraId !== $model->refCascadingProgram->refsasaranrenstra_id): ?>
                                            <tr>
                                                <th colspan="6" style="background-color: #04A9F5; color:white; white-space: normal;"><?= $model->refCascadingProgram->refmisi->uraian_misi ?></th>
                                            </tr>
                                            <tr>
                                                <th colspan="6" style="background-color: #e23c3c; color:white; white-space: normal;">(Tujuan)<?= $model->refCascadingProgram->reftujuan ? $model->refCascadingProgram->reftujuan->uraian_tujuan : 'Tidak Ada Tujuan' ?></th>
                                            </tr>
                                            <tr>
                                                <th colspan="6" style="background-color: #6ee15b; color:white; white-space: normal;">(Sasaran)<br><?= $model->refCascadingProgram->sasaranRenstra->uraian_sasaranrenstra ?></th>
                                            </tr>
                                            <?php $currentRefsasaranrenstraId = $model->refCascadingProgram->refsasaranrenstra_id; ?>
                                        <?php endif; ?>

                                        <!-- Check if the cascading program has changed to display the program -->
                                        <?php if ($currentRefprogramId !== $model->refprogram_id): ?>
                                            <tr>
                                                <th colspan="6" style="background-color: #16537e; color:white; white-space: normal;"><?= $model->refCascadingProgram->refProgram->kode_program ?> - <?= $model->refCascadingProgram->refProgram->nama_program ?></th>
                                            </tr>
                                            <?php $currentRefprogramId = $model->refprogram_id; ?>
                                        <?php endif; ?>

                                        <!-- Display Kegiatan and group by kegiatan_id -->
                                        <?php if ($currentRefkegiatanId !== $model->refkegiatan_id): ?>
                                            <tr>
                                                <th style="background-color: #1DE9B6; color:white;"><?= $model->refKegiatan ? $model->refKegiatan->kode_kegiatan : '-' ?></th>
                                                <th colspan="2" style="background-color: #1DE9B6; color:white; white-space: normal;"><?= $model->refKegiatan ? $model->refKegiatan->nama_kegiatan : '-' ?></th>
                                                <th style="background-color: #1DE9B6; color:white;">Satuan</th>
                                                <th style="background-color: #1DE9B6; color:white;">Target <?= $model->refPeriode->periode ?></th>
                                                <th style="background-color: #1DE9B6; color:white;">Anggaran</th>
                                            </tr>
                                            <?php
                                            $currentRefkegiatanId = $model->refkegiatan_id;
                                            // Calculate total anggaran for the current refkegiatan_id
                                            $totalAnggaran = SakipCascadingsubkegiatan::find()
                                                ->where(['refkegiatan_id' => $model->refkegiatan_id])
                                                ->andWhere(['refskpd_id' => $model->refskpd_id])
                                                ->sum('CAST(subkegiatan_anggaran AS UNSIGNED)') ?? 0;
                                            ?>
                                        <?php endif; ?>

                                        <tr>
                                            <th>
                                                <?= Html::button('<i class="fas fa-edit"></i>', [
                                                    'class' => 'btn btn-success btn-sm',
                                                    'title' => 'Update',
                                                    'data-bs-toggle' => 'modal',
                                                    'data-bs-target' => '#updateModal',
                                                    'data-url' => Url::to(['update', 'refcascadingkegiatan_id' => $model->refcascadingkegiatan_id])
                                                ]) ?>
                                                <?= Html::a('<i class="fas fa-trash-alt"></i>', ['delete', 'refcascadingkegiatan_id' => $model->refcascadingkegiatan_id], [
                                                    'class' => 'btn btn-danger btn-sm',
                                                    'title' => 'Delete',
                                                    'data' => [
                                                        'confirm' => 'Are you sure you want to delete this item?',
                                                        'method' => 'post',
                                                    ],
                                                ]) ?>
                                                <?= Html::button('<i class="fas fa-check"></i>', [
                                                    'class' => 'btn btn-success btn-sm mx-2',
                                                    'title' => 'Pilih Pengampu Penjabat',
                                                    'data-bs-toggle' => 'modal',
                                                    'data-bs-target' => '#createModalPenjabatSkpd',
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
                                            <th style="white-space: normal;"><?= $model->uraian_sasarankegiatan ?> - <?= $model->uraian_indikatorkegiatan ?></th>
                                            <th><?= $model->kegiatan_satuan ?></th>
                                            <th><?= $model->kegiatan_target ?></th>

                                            <!-- Sum anggaran from the subkegiatan -->


                                            <th><?= 'Rp. ' . number_format($totalAnggaran, 0, ',', '.'); ?></th>
                                        </tr>
                                        <tr>
                                            <td colspan="6">
                                                <div class="accordion" id="accordion<?= $model->refcascadingkegiatan_id ?>">
                                                    <div class="accordion-item">
                                                        <h2 class="accordion-header" id="heading<?= $model->refcascadingkegiatan_id ?>">
                                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?= $model->refcascadingkegiatan_id ?>" aria-expanded="false" aria-controls="collapse<?= $model->refcascadingkegiatan_id ?>">
                                                                Lihat Penjabat SKPD
                                                            </button>
                                                        </h2>
                                                        <div id="collapse<?= $model->refcascadingkegiatan_id ?>" class="accordion-collapse collapse" aria-labelledby="heading<?= $model->refcascadingkegiatan_id ?>" data-bs-parent="#accordion<?= $model->refcascadingkegiatan_id ?>">
                                                            <div class="accordion-body">
                                                                <?php
                                                                $penjabatModels = SakipPenjabatskpdCascadingkegiatan::findAll(['refcascadingkegiatan_id' => $model->refcascadingkegiatan_id]);
                                                                foreach ($penjabatModels as $penjabatModel): ?>
                                                                    <?php $penjabat = $penjabatModel->refPenjabatskpd ?? null; ?>

                                                                    <p><strong>Penjabat:</strong> <?= $penjabat ? $penjabat->nama_penjabat : '-' ?></p>
                                                                    <p><strong>NIP:</strong> <?= $penjabat ? $penjabat->nip_penjabat : '-' ?></p>
                                                                    <p><strong>Jabatan:</strong> <?= $penjabat ? $penjabat->jabatan_eselon : '-' ?></p>
                                                                    <p><strong>Pangkat:</strong> <?= $penjabat ? $penjabat->pangkat_eselon : '-' ?></p>
                                                                    <?= Html::a('<i class="fas fa-trash-alt"></i> Hapus Penjabat SKPD', [
                                                                        'sakip-penjabatskpd-cascadingkegiatan/delete',
                                                                        'refpenjabatcascadingkegiatan_id' => $penjabatModel->refpenjabatcascadingkegiatan_id,
                                                                        'refperiode_id' => $model->refperiode_id,
                                                                    ], [
                                                                        'class' => 'btn btn-danger btn-sm',
                                                                        'title' => 'Delete',
                                                                        'data' => [
                                                                            'confirm' => 'Are you sure you want to delete this item?',
                                                                            'method' => 'post',
                                                                        ],
                                                                    ]) ?>
                                                                    <hr>
                                                                <?php endforeach; ?>
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
                                        <h5 class="modal-title" id="createModalLabel">Tambah Data SAKIP Cascading Kegiatan</h5>
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
                                        <h5 class="modal-title" id="updateModalLabel">Update Data SAKIP Cascading Kegiatan</h5>
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
                        <div class="modal fade" id="createModalPenjabatSkpd" tabindex="-1" aria-labelledby="createModalLabelPenjabatSkpd" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content" style="border-radius: 20px;">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="createModalLabelPenjabatSkpd">Tambah Data SAKIP Penjabat SKPD</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <!-- The form will be loaded here -->
                                        <div id="modalFormContentPenjabatSkpd" style="padding-bottom:20px; padding-right:15px; padding-left:15px;">
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
                        <div class="modal fade" id="updateModalPenjabatSkpd" tabindex="-1" aria-labelledby="updateModalLabelPenjabat Skpd" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content" style="border-radius: 20px;">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="updateModalLabelPenjabat Skpd">Update Data SAKIP Penjabat Skpd</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <!-- The form will be loaded here -->
                                        <div id="modalUpdateFormContentPenjabatSkpd" style="padding-bottom:20px; padding-right:15px; padding-left:15px;">
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