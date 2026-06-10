<?php

use frontend\models\SakipCascadingsubkegiatan;
use frontend\models\SakipCascadingkegiatan;
use frontend\models\SakipCascadingprogram;
use frontend\models\SakipPenjabatskpdCascadingsubkegiatan;
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
/** @var frontend\models\search\SakipCascadingsubkegiatanSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'e-Sakip - Data Cascading Sub Kegiatan';
$this->params['breadcrumbs'][] = $this->title;

$isAdmin = false;
if (!Yii::$app->user->isGuest) {
    $assignments = Yii::$app->authManager->getAssignments(Yii::$app->user->id);
    if (isset($assignments['superadmin']) || isset($assignments['admin'])) {
        $isAdmin = true;
    }
}

$this->registerJs("
    $('#createModal').on('show.bs.modal', function (event) {
        var modal = $(this);
        $.ajax({
            url: '" . Url::to(['sakip-cascadingsubkegiatan/create']) . "',
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
    var refcascadingsubkegiatan_id = button.data('refcascadingsubkegiatan_id');
    var refperiode_id = button.data('refperiode_id');
    var refsasaranrenstra_id = button.data('refsasaranrenstra_id');
    var refindikatorsasaranrenstra_id = button.data('refindikatorsasaranrenstra_id');
    var refprogram_id = button.data('refprogram_id');
    var refkegiatan_id = button.data('refkegiatan_id');
    var refsubkegiatan_id = button.data('refsubkegiatan_id');
    var uraian_sasaransubkegiatan = button.data('uraian_sasaransubkegiatan');
    var uraian_indikatorsubkegiatan = button.data('uraian_indikatorsubkegiatan');
    var subkegiatan_target = button.data('subkegiatan_target');
    var subkegiatan_satuan = button.data('subkegiatan_satuan');

    // Set the hidden inputs in the modal with the correct values
    modal.find('#refcascadingprogram_id').val(refcascadingprogram_id);
    modal.find('#refcascadingkegiatan_id').val(refcascadingkegiatan_id);
    modal.find('#refcascadingsubkegiatan_id').val(refcascadingsubkegiatan_id);
    modal.find('#refperiode_id').val(refperiode_id);
    modal.find('#refsasaranrenstra_id').val(refsasaranrenstra_id);
    modal.find('#refindikatorsasaranrenstra_id').val(refindikatorsasaranrenstra_id);
    modal.find('#refprogram_id').val(refprogram_id);
    modal.find('#refkegiatan_id').val(refkegiatan_id);
    modal.find('#refsubkegiatan_id').val(refsubkegiatan_id);
    modal.find('#uraian_sasaransubkegiatan').val(uraian_sasaransubkegiatan);
    modal.find('#uraian_indikatorsubkegiatan').val(uraian_indikatorsubkegiatan);
    modal.find('#subkegiatan_target').val(subkegiatan_target);
    modal.find('#subkegiatan_satuan').val(subkegiatan_satuan);

    // Send the data to load the form dynamically (optional)
    $.ajax({
        url: '" . Url::to(['sakip-penjabatskpd-cascadingsubkegiatan/create']) . "',
        type: 'GET',
        data: {
            refcascadingprogram_id: refcascadingprogram_id,
            refcascadingkegiatan_id: refcascadingkegiatan_id,
            refcascadingsubkegiatan_id: refcascadingsubkegiatan_id,
            refperiode_id: refperiode_id,
            refsasaranrenstra_id: refsasaranrenstra_id,
            refindikatorsasaranrenstra_id: refindikatorsasaranrenstra_id,
            refprogram_id: refprogram_id,
            refkegiatan_id: refkegiatan_id,
            refsubkegiatan_id: refsubkegiatan_id,
            uraian_sasaransubkegiatan: uraian_sasaransubkegiatan, 
            uraian_indikatorsubkegiatan: uraian_indikatorsubkegiatan,
            subkegiatan_target: subkegiatan_target, 
            subkegiatan_satuan: subkegiatan_satuan 
        },
        success: function(data) {
            modal.find('#modalFormContentPenjabatSkpd').html(data);
  // Fetch filtered `refpenjabatskpd_id` options
            $.ajax({
                url: '" . Url::to(['sakip-penjabatskpd-cascadingsubkegiatan/fetch-penjabatskpd']) . "',
                type: 'GET',
                data: {
                    refperiode_id: refperiode_id,
                    refcascadingprogram_id: refcascadingprogram_id,  // Pass refcascadingprogram_id to filter out existing entries
                    refcascadingkegiatan_id: refcascadingkegiatan_id,  // Pass refcascadingkegiatan_id to filter out existing entries
                    refcascadingsubkegiatan_id: refcascadingsubkegiatan_id  // Pass refcascadingsubkegiatan_id to filter out existing entries
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
        url: '" . Url::to(['sakip-penjabatskpd-cascadingsubkegiatan/fetch-indikator']) . "',
        type: 'GET',
        data: {
            refcascadingprogram_id: refcascadingprogram_id,
            refcascadingkegiatan_id: refcascadingkegiatan_id,
            refcascadingsubkegiatan_id: refcascadingsubkegiatan_id
        },
        success: function(data) {
            // Update the refindikatorsubkegiatan_id field with the fetched value
            modal.find('#refindikatorsubkegiatan_id').val(data.refindikatorsubkegiatan_id);
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
                            <li class="breadcrumb-item" aria-current="page">Cascading Sub Kegiatan</li>
                        </ul>
                    </div>
                    <div class="col-md-12">
                        <div class="page-header-title">
                            <h2 class="mb-0">Cascading Sub Kegiatan</h2>
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
                            <i class="fas fa-pen-fancy"></i>Periode SAKIP Cascading Sub Kegiatan - <?= Html::encode(ucwords(strtolower($nama_skpd))) ?>
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
                                            'prompt' => 'Semua Periode',
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
                            <i class="fas fa-pen-fancy"></i>Data SAKIP Cascading Sub Kegiatan - <?= Html::encode(ucwords(strtolower($nama_skpd))) ?> (Periode <?= $selectedPeriodValue ?>)
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

                        <?php if ($dataEmpty): ?>
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
                                    $currentRefsubkegiatanId = null; // Variable to track current refsubkegiatan_id
                                    $currentRefcascadingkegiatanId = null; // Variable to track current refsubkegiatan_id
                                    ?>

                                    <?php foreach ($cascadingSubkegiatans as $cascadingSubkegiatan): ?>
                                        <!-- Check if the refsasaranrenstra_id has changed to display the mission, goal, and target -->
                                        <?php if ($currentRefsasaranrenstraId !== $cascadingSubkegiatan->refCascadingProgram->refsasaranrenstra_id): ?>
                                            <tr>
                                                <th colspan="<?= $isAdmin ? 6 : 5 ?>" style="background-color: #04A9F5; color:white; white-space: normal;"><?= $cascadingSubkegiatan->refCascadingProgram->refmisi->uraian_misi ?></th>
                                            </tr>
                                            <tr>
                                                <th colspan="<?= $isAdmin ? 6 : 5 ?>" style="background-color: #e23c3c; color:white; white-space: normal;"><?= $cascadingSubkegiatan->refCascadingProgram->reftujuan->uraian_tujuan ?></th>
                                            </tr>
                                            <tr>
                                                <th colspan="<?= $isAdmin ? 6 : 5 ?>" style="background-color: #6ee15b; color:white; white-space: normal;"><?= $cascadingSubkegiatan->refCascadingProgram->sasaranRenstra->uraian_sasaranrenstra ?></th>
                                            </tr>
                                            <?php $currentRefsasaranrenstraId = $cascadingSubkegiatan->refCascadingProgram->refsasaranrenstra_id; ?>
                                        <?php endif; ?>

                                        <!-- Check if the cascading program has changed to display the program -->
                                        <?php if ($currentRefcascadingprogramId !== $cascadingSubkegiatan->refcascadingprogram_id): ?>
                                            <tr>
                                                <th colspan="<?= $isAdmin ? 6 : 5 ?>" style="background-color: #16537e; color:white; white-space: normal;"><?= $cascadingSubkegiatan->refCascadingProgram->refProgram->kode_program ?> - <?= $cascadingSubkegiatan->refCascadingProgram->refProgram->nama_program ?></th>
                                            </tr>
                                            <?php $currentRefcascadingprogramId = $cascadingSubkegiatan->refcascadingprogram_id; ?>
                                        <?php endif; ?>

                                        <!-- Check if the cascading program has changed to display the program -->
                                        <?php if ($currentRefcascadingkegiatanId !== $cascadingSubkegiatan->refcascadingkegiatan_id): ?>
                                            <tr>
                                                <th colspan="<?= $isAdmin ? 6 : 5 ?>" style="background-color: #1DE9B6; color:white; white-space: normal;"><?= $cascadingSubkegiatan->refCascadingKegiatan->refKegiatan->kode_kegiatan ?> - <?= $cascadingSubkegiatan->refCascadingKegiatan->refKegiatan->nama_kegiatan ?></th>
                                            </tr>
                                            <?php $currentRefcascadingkegiatanId = $cascadingSubkegiatan->refcascadingkegiatan_id; ?>
                                        <?php endif; ?>

                                        <!-- Display Kegiatan and group by kegiatan_id -->
                                        <?php if ($currentRefsubkegiatanId !== $cascadingSubkegiatan->refsubkegiatan_id): ?>
                                            <tr>
                                                <?php if ($isAdmin): ?>
                                                    <th style="background-color: #f3f4f6;">Aksi</th>
                                                <?php endif; ?>
                                                <th><?= $cascadingSubkegiatan->refSubkegiatan->kode_subkegiatan ?></th>
                                                <th style="white-space: normal;"><?= $cascadingSubkegiatan->refSubkegiatan->nama_subkegiatan ?></th>
                                                <th>Satuan</th>
                                                <th><?= $cascadingSubkegiatan->refPeriode->periode ?></th>
                                                <th>Anggaran</th>
                                            </tr>
                                            <?php $currentRefsubkegiatanId = $cascadingSubkegiatan->refsubkegiatan_id; ?>
                                        <?php endif; ?>

                                        <tr>
                                            <?php if ($isAdmin): ?>
                                                <td class="text-center">
                                                    <?= Html::button('<i class="fas fa-edit"></i>', [
                                                        'class' => 'btn btn-outline-warning btn-sm',
                                                        'title' => 'Update',
                                                        'data-bs-toggle' => 'modal',
                                                        'data-bs-target' => '#updateModal',
                                                        'data-url' => Url::to(['update', 'refcascadingsubkegiatan_id' => $cascadingSubkegiatan->refcascadingsubkegiatan_id])
                                                    ]) ?>
                                                </td>
                                            <?php endif; ?>
                                            <th>Sasaran/Indikator</th>
                                            <th style="white-space: normal;"><?= $cascadingSubkegiatan->uraian_sasaransubkegiatan ?> - <?= $cascadingSubkegiatan->uraian_indikatorsubkegiatan ?></th>
                                            <th><?= $cascadingSubkegiatan->subkegiatan_satuan ?></th>
                                            <th><?= $cascadingSubkegiatan->subkegiatan_target ?></th>
                                            <th><?= 'Rp. ' . number_format((float)$cascadingSubkegiatan->subkegiatan_anggaran, 0, ',', '.') ?></th>
                                        </tr>
                                        <tr>
                                            <td colspan="<?= $isAdmin ? 6 : 5 ?>">
                                                <div class="accordion" id="accordion<?= $cascadingSubkegiatan->refcascadingsubkegiatan_id ?>">
                                                    <div class="accordion-item">
                                                        <h2 class="accordion-header" id="heading<?= $cascadingSubkegiatan->refcascadingsubkegiatan_id ?>">
                                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?= $cascadingSubkegiatan->refcascadingsubkegiatan_id ?>" aria-expanded="false" aria-controls="collapse<?= $cascadingSubkegiatan->refcascadingsubkegiatan_id ?>">
                                                                Lihat Penjabat SKPD
                                                            </button>
                                                        </h2>
                                                        <div id="collapse<?= $cascadingSubkegiatan->refcascadingsubkegiatan_id ?>" class="accordion-collapse collapse" aria-labelledby="heading<?= $cascadingSubkegiatan->refcascadingsubkegiatan_id ?>" data-bs-parent="#accordion<?= $cascadingSubkegiatan->refcascadingsubkegiatan_id ?>">
                                                            <div class="accordion-body">
                                                                <?php
                                                                $penjabatModels = SakipPenjabatskpdCascadingsubkegiatan::findAll(['refcascadingsubkegiatan_id' => $cascadingSubkegiatan->refcascadingsubkegiatan_id]);
                                                                foreach ($penjabatModels as $penjabatModel): ?>
                                                                    <?php $penjabat = $penjabatModel->refPenjabatskpd ?? null; ?>

                                                                    <p><strong>Penjabat:</strong> <?= $penjabat ? $penjabat->nama_penjabat : '-' ?></p>
                                                                    <p><strong>NIP:</strong> <?= $penjabat ? $penjabat->nip_penjabat : '-' ?></p>
                                                                    <p><strong>Jabatan:</strong> <?= $penjabat ? $penjabat->jabatan_eselon : '-' ?></p>
                                                                    <p><strong>Pangkat:</strong> <?= $penjabat ? $penjabat->pangkat_eselon : '-' ?></p>

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
                        <div class="modal fade" id="updateModalPenjabat Skpd" tabindex="-1" aria-labelledby="updateModalLabelPenjabat Skpd" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content" style="border-radius: 20px;">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="updateModalLabelPenjabat Skpd">Update Data SAKIP Penjabat Skpd</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <!-- The form will be loaded here -->
                                        <div id="modalUpdateFormContentPenjabat Skpd" style="padding-bottom:20px; padding-right:15px; padding-left:15px;">
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