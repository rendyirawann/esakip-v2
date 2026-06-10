<?php

use frontend\models\SakipCascadingprogram;
use frontend\models\SakipPenjabatskpdCascadingprogram;
use frontend\models\SakipCascadingsubkegiatan;
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
/** @var frontend\models\search\SakipCascadingprogramSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Sakip Cascading Program';
$this->params['breadcrumbs'][] = $this->title;

$this->registerJs("
    $('#createModal').on('show.bs.modal', function (event) {
        var modal = $(this);
        $.ajax({
            url: '" . Url::to(['sakip-cascadingprogram/create']) . "',
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
    var refperiode_id = button.data('refperiode_id');
    var refsasaranrenstra_id = button.data('refsasaranrenstra_id');
    var refindikatorsasaranrenstra_id = button.data('refindikatorsasaranrenstra_id');
    var refbidang_id = button.data('refbidang_id');
    var refprogram_id = button.data('refprogram_id');
    var uraian_sasaranprogram = button.data('uraian_sasaranprogram');
    var uraian_indikatorprogram = button.data('uraian_indikatorprogram');
    var program_target = button.data('program_target');
    var program_satuan = button.data('program_satuan');

    // Set the hidden inputs in the modal with the correct values
    modal.find('#refcascadingprogram_id').val(refcascadingprogram_id);
    modal.find('#refperiode_id').val(refperiode_id);
    modal.find('#refsasaranrenstra_id').val(refsasaranrenstra_id);
    modal.find('#refindikatorsasaranrenstra_id').val(refindikatorsasaranrenstra_id);
    modal.find('#refbidang_id').val(refbidang_id);
    modal.find('#refprogram_id').val(refprogram_id);
    modal.find('#uraian_sasaranprogram').val(uraian_sasaranprogram);
    modal.find('#uraian_indikatorprogram').val(uraian_indikatorprogram);
    modal.find('#program_target').val(program_target);
    modal.find('#program_satuan').val(program_satuan);

    // Send the data to load the form dynamically (optional)
    $.ajax({
        url: '" . Url::to(['sakip-penjabatskpd-cascadingprogram/create']) . "',
        type: 'GET',
        data: {
            refcascadingprogram_id: refcascadingprogram_id,
            refperiode_id: refperiode_id,
            refsasaranrenstra_id: refsasaranrenstra_id,
            refindikatorsasaranrenstra_id: refindikatorsasaranrenstra_id,
            refbidang_id: refbidang_id,
            refprogram_id: refprogram_id,
            uraian_sasaranprogram: uraian_sasaranprogram, 
            uraian_indikatorprogram: uraian_indikatorprogram,
            program_target: program_target, 
            program_satuan: program_satuan 
        },
        success: function(data) {
            modal.find('#modalFormContentPenjabatSkpd').html(data);
  // Fetch filtered `refpenjabatskpd_id` options
            $.ajax({
                url: '" . Url::to(['sakip-penjabatskpd-cascadingprogram/fetch-penjabatskpd']) . "',
                type: 'GET',
                data: {
                    refperiode_id: refperiode_id,
                    refcascadingprogram_id: refcascadingprogram_id  // Pass refcascadingprogram_id to filter out existing entries
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
        url: '" . Url::to(['sakip-penjabatskpd-cascadingprogram/fetch-indikator']) . "',
        type: 'GET',
        data: {
            refcascadingprogram_id: refcascadingprogram_id,
        },
        success: function(data) {
            // Update the refindikatorprogram_id field with the fetched value
            modal.find('#refindikatorprogram_id').val(data.refindikatorprogram_id);
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
                            <li class="breadcrumb-item" aria-current="page">Cascading Program</li>
                        </ul>
                    </div>
                    <div class="col-md-12">
                        <div class="page-header-title">
                            <h2 class="mb-0">Cascading Program</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- [ breadcrumb ] end -->


        <div class="row">
            <!-- start row -->
            <div class="col-sm-12">
                <!-- End Card -->
                <div class="card">
                    <div class="card-header" style="background-color: #04A9F5; padding: 8px;">
                        <h6 style="color: white; margin: 0; cursor: pointer;" id="toggleAll">
                            <i class="fas fa-pen-fancy"></i>Periode SAKIP Cascding Program - <?= Html::encode(ucwords(strtolower($nama_skpd))) ?>
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <!-- Dropdown filter berdasarkan refperiode_id -->
                                <?= \yii\helpers\Html::beginForm(['index-pegawai'], 'get', ['class' => 'form-inline']); ?>
                                <div class="form-group">
                                    <?= \yii\helpers\Html::label('Pilih Periode:', 'refperiode_id', ['class' => 'mr-2']); ?>
                                    <?= \yii\helpers\Html::dropDownList(
                                        'refperiode_id',
                                        $selectedPeriodId,
                                        \yii\helpers\ArrayHelper::map($periodeList, 'refperiode_id', 'periode'),
                                        [
                                            'class' => 'form-control',
                                            'prompt' => 'Pilih Periode',
                                            'onchange' => 'this.form.submit()', // Submit form saat pilihan berubah
                                        ]
                                    ); ?>
                                </div>
                                <?php if (!empty($refskpdList)): ?>
                                    <div class="form-group ml-3">
                                        <?= \yii\helpers\Html::label('Pilih SKPD:', 'refskpd_id', ['class' => 'mr-2']); ?>
                                        <?= \yii\helpers\Html::dropDownList(
                                            'refskpd_id',
                                            $selectedSkpdId,
                                            \yii\helpers\ArrayHelper::map($refskpdList, 'refskpd_id', 'nama_skpd'),
                                            [
                                                'class' => 'form-control',
                                                'prompt' => 'Pilih SKPD',
                                                'onchange' => 'this.form.submit()',
                                            ]
                                        ); ?>
                                    </div>
                                <?php endif; ?>
                                <?= \yii\helpers\Html::endForm(); ?>

                            </div>
                        </div>

                    </div>

                </div>

                <!-- Table Start -->
                <div class="card">
                    <div class="card-header" style="background-color: #04A9F5; padding: 8px;">
                        <h6 style="color: white; margin: 0; cursor: pointer;" id="toggleAll">
                            <i class="fas fa-pen-fancy"></i>Data SAKIP Cascading Program - <?= Html::encode(ucwords(strtolower($nama_skpd))) ?> (Periode <?= $selectedPeriodValue ?>)
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

                        <?php if ($dataProvider === null): ?>
                            <div class="alert alert-info mt-4">
                                Silakan pilih Periode dan SKPD untuk menampilkan data.
                            </div>
                        <?php elseif (empty($dataProvider->getModels())): ?>
                            <div class="alert alert-warning mt-4">
                                Data tidak ada untuk periode yang dipilih.
                            </div>
                        <?php else: ?>
                            <div class="dt-responsive table-responsive">
                                <table id="table-style-hover" class="table table-striped table-hover table-bordered nowrap" style="font-size:xx-small;">
                                    <?php
                                    $currentRefsasaranrenstraId = null;
                                    $currentRefprogramId = null;
                                    ?>
                                    <?php foreach ($dataProvider->getModels() as $model): ?>
                                        <?php if ($currentRefsasaranrenstraId !== $model->refsasaranrenstra_id): ?>
                                            <tr>
                                                <th colspan="6" style="background-color: #04A9F5; color:white; white-space: normal;"><?= $model->refmisi->uraian_misi ?></th>
                                            </tr>
                                            <tr>
                                                <th colspan="6" style="background-color: #e23c3c; color:white; white-space: normal;">(Tujuan)<?= $model->reftujuan->uraian_tujuan ?></th>
                                            </tr>
                                            <tr>
                                                <th colspan="6" style="background-color: #6ee15b; color:white; white-space: normal;">(Sasaran)<?= $model->refsasaran->uraian_sasaran ?></th>
                                            </tr>
                                            <?php $currentRefsasaranrenstraId = $model->refsasaranrenstra_id; ?>
                                        <?php endif; ?>

                                        <?php if ($currentRefprogramId !== $model->refprogram_id): ?>
                                            <tr>
                                                <th style="background-color: #16537e; color:white;"><?= $model->refProgram->kode_program ?></th>
                                                <th colspan="2" style="background-color: #16537e; color:white; white-space: normal;"><?= $model->refProgram->nama_program ?></th>
                                                <th style="background-color: #16537e; color:white;">Satuan</th>
                                                <th style="background-color: #16537e; color:white;"><?= $model->refPeriode->periode ?></th>
                                                <th style="background-color: #16537e; color:white;">Anggaran</th>
                                            </tr>
                                            <?php $currentRefprogramId = $model->refprogram_id;
                                            $totalAnggaran = 0;
                                            ?>
                                        <?php endif; ?>

                                        <tr>
                                            <td>
                                                <?= Html::a('<i class="fa fa-eye"></i>', ['view-kegiatan', 'refcascadingprogram_id' => $model->refcascadingprogram_id], ['class' => 'btn btn-primary btn-sm', 'title' => 'View']) ?>
                                            </td>
                                            <td>Sasaran/Indikator</td>
                                            <td style="white-space: normal"><?= $model->uraian_sasaranprogram ?> - <?= $model->uraian_indikatorprogram ?></td>
                                            <td><?= $model->program_satuan ?></td>
                                            <td><?= $model->program_target ?></td>

                                            <?php
                                            $subkegiatanAnggaran = SakipCascadingsubkegiatan::find()
                                                ->where([
                                                    'refprogram_id' => $model->refprogram_id,
                                                    'refcascadingprogram_id' => $model->refcascadingprogram_id,
                                                ])
                                                ->sum('CAST(subkegiatan_anggaran AS UNSIGNED)');

                                            $totalAnggaran += $subkegiatanAnggaran;
                                            ?>
                                            <td><?= 'Rp. ' . number_format($totalAnggaran, 0, ',', '.'); ?></td>
                                        </tr>

                                        <!-- Accordion for refpenjabatskpd_id details -->


                                    <?php endforeach; ?>
                                </table>

                            </div>
                        <?php endif; ?>







                        <!--  -->
                        <div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content" style="border-radius: 20px;">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="createModalLabel">Tambah Data SAKIP Cascading Program</h5>
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
                                        <h5 class="modal-title" id="updateModalLabel">Update Data SAKIP Cascading Program</h5>
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