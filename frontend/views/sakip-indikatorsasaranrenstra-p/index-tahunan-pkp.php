<?php

use frontend\models\SakipIndikatorsasaranrenstra;
use frontend\models\SakipSasaranrenstra;
use frontend\models\SakipSasaran;
use frontend\models\SakipPeriode;
use frontend\models\SakipTujuan;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var frontend\models\search\SakipIndikatorsasaranrenstraSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'e-SAKIP - PK Perubahan Tahunan Indikator Sasaran Renstra';
$this->params['breadcrumbs'][] = $this->title;

$this->registerJs("
$('#createModal').on('show.bs.modal', function (event) {
    var modal = $(this);
    $.ajax({
        url: '" . Url::to(['sakip-indikatorsasaranrenstra/create']) . "',
        type: 'GET',
        success: function(data) {
            modal.find('#modalFormContent').html(data);
        }
    });
});
");

$this->registerJs("
$(document).ready(function() {

    // Event klik tombol edit
    $(document).on('click', '.btn-edit-pkptahunan', function () {
        var url = $(this).data('url');
        var modal = $('#updateModal');

        $.ajax({
            url: url,
            type: 'GET',
            success: function(data) {
                modal.find('#modalUpdateFormContent').html(data);
                modal.modal('show');
                initializeChoices(); // jika kamu pakai Choices.js
            },
            error: function() {
                console.log('Gagal memuat form update');
            }
        });
    });

    // Bersihkan isi modal saat ditutup
    $('#updateModal').on('hidden.bs.modal', function () {
        $(this).find('#modalUpdateFormContent').html('');
    });

});
");


$this->registerJs("
$('#createModalIndikatorSasaranRenstra').on('show.bs.modal', function (event) {
    var modal = $(this);
    $.ajax({
        url: '" . Url::to(['sakip-indikatorsasaranrenstra/create']) . "',
        type: 'GET',
        success: function(data) {
            modal.find('#modalFormContentIndikatorSasaranRenstra').html(data);
        }
    });
});
");

$this->registerJs("
$('#updateModalIndikator').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget); // Button that triggered the modal
    var url = button.data('url'); // Extract info from data-url attributes

    var modal = $(this);
    $.ajax({
        url: url,
        type: 'GET',
        success: function(data) {
            modal.find('#modalUpdateFormContentIndikator').html(data);
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
                            <li class="breadcrumb-item" aria-current="page">PK Perubahan Tahunan Indikator Sasaran Renstra</li>
                        </ul>
                    </div>
                    <div class="col-md-12">
                        <div class="page-header-title">
                            <h2 class="mb-0">PK Perubahan Tahunan Indikator Sasaran Renstra</h2>
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
                            <i class="fas fa-pen-fancy"></i>Periode Data PK Tahunan Indikator Sasaran Renstra - <?= Html::encode(ucwords(strtolower($nama_skpd))) ?>
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <!-- Dropdown filter berdasarkan refperiode_id -->
                                <?= \yii\helpers\Html::beginForm(['index-tahunan-pkp'], 'get', ['class' => 'form-inline']); ?>
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
                            <i class="fas fa-pen-fancy"></i>Data PK Perubahan Tahunan Indikator Sasaran Renstra - <?= Html::encode(ucwords(strtolower($nama_skpd))) ?> (Periode <?= $selectedPeriodValue ?>)
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
                                <?php
                                $lastSasaranRenstraId = null;
                                $no = 1;
                                foreach ($data as $indikator):
                                    // Jika refsasaranrenstra_id berubah, tampilkan uraian_sasaranrenstra
                                    if ($lastSasaranRenstraId !== $indikator->refsasaranrenstra_id):
                                ?>
                                        <table id="table-style-hover" class="table table-striped table-hover table-bordered nowrap" style="font-size:xx-small;">
                                            <thead>
                                                <tr>
                                                    <th colspan="11" style="background-color: #04A9F5; color: white; white-space:normal;">
                                                        Sasaran <?= $no ?>: <?= $indikator->refSasaranrenstra->uraian_sasaranrenstra ?>
                                                    </th>
                                                </tr>
                                                <tr>
                                                    <th style="background-color: #e23c3c; color: white; white-space:normal;">No</th>
                                                    <th style="background-color: #e23c3c; color: white; white-space:normal;">Indikator</th>
                                                    <th style="background-color: #e23c3c; color: white; white-space:normal;">Satuan</th>
                                                    <th style="background-color: #e23c3c; color: white; white-space:normal;">IKU</th>
                                                    <th style="background-color: #e23c3c; color: white; white-space:normal;">PK</th>
                                                    <th style="background-color: #e23c3c; color: white; white-space:normal;">Target Renstra</th>
                                                    <th style="background-color: #e23c3c; color: white; white-space:normal;">Target RKT</th>
                                                    <th style="background-color: #e23c3c; color: white; white-space:normal;">Target PK</th>
                                                    <th style="background-color: #e23c3c; color: white; white-space:normal;">Target PK Perubahan</th>
                                                    <th style="background-color: #e23c3c; color: white; white-space:normal;">Sebab Perubahan</th>
                                                    <th style="background-color: #e23c3c; color: white; white-space:normal;">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            <?php
                                            // Reset counter for indikator number within the same sasaranrenstra
                                            $no = 1;
                                            $lastSasaranRenstraId = $indikator->refsasaranrenstra_id;
                                        endif;
                                            ?>
                                            <tr>
                                                <td><?= $no++ ?></td>
                                                <td style="white-space:normal;"><?= $indikator->uraian_indikatorsasaranrenstra ?></td>
                                                <td><?= $indikator->indikatorsasaranrenstra_satuan ?></td>
                                                <td class="text-center align-middle">
                                                    <?php if ($indikator->iku_isaktif === 'T'): ?>
                                                        <span class="badge bg-success">AKTIF</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-danger">Non Aktif</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-center align-middle">
                                                    <?php if ($indikator->pk_isaktif === 'T'): ?>
                                                        <span class="badge bg-success">AKTIF</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-danger">Non Aktif</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?= $indikator->indikatorsasaranrenstra_target ?></td>
                                                <td><?= $indikator->target_rkt ?></td>
                                                <td><?= $indikator->target_pk ?></td>
                                                <td><?= $indikator->target_pk_p ?></td>
                                                <td style="white-space:normal;"><?= $indikator->keterangan_pk_p ?></td>
                                                <td>
                                                    <?php if (!empty($indikator->target_pk)): ?>
                                                        <?= Html::button('<i class="fas fa-edit"></i>', [
                                                            'class' => 'btn btn-success btn-sm btn-edit-pkptahunan',
                                                            'title' => 'Update',
                                                            'data-url' => Url::to(['update-target-pkp', 'refindikatorsasaranrenstra_id' => $indikator->refindikatorsasaranrenstra_id])
                                                        ]) ?>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                            <?php
                                            // Tutup table jika ini indikator terakhir dari suatu refsasaranrenstra_id
                                            $next = next($data);
                                            if (!$next || $next->refsasaranrenstra_id !== $lastSasaranRenstraId): ?>
                                            </tbody>
                                        </table>
                                <?php
                                            endif;
                                        endforeach; ?>
                            </div>
                        <?php endif; ?>
                        <!--  -->
                        <div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content" style="border-radius: 20px;">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="createModalLabel">Tambah Data SAKIP Sasaran Renstra</h5>
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
                                        <h5 class="modal-title" id="updateModalLabel">Update Data SAKIP Sasaran Renstra</h5>
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

                    </div>
                </div>

                <!-- Table end -->
            </div>
            <!-- end row -->
        </div>


        <!-- [ Main Content ] end -->
    </div>
</div>