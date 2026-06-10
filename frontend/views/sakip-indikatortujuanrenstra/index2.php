<?php

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
/** @var frontend\models\search\SakipIndikatortujuanrenstraSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Sakip Indikatortujuanrenstras';
$this->params['breadcrumbs'][] = $this->title;

$this->registerJs("
    $('#createModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget); // Tombol yang diklik
        var refsasaranrenstraId = button.data('refsasaranrenstra'); // Ambil refsasaranrenstra_id dari tombol
        var reftujuanrenstraId = button.data('reftujuanrenstra'); // Ambil reftujuanrenstra_id dari tombol
        var refperiodeId = button.data('refperiode'); // Ambil refperiode_id dari tombol (opsional)

        var modal = $(this);
        $.ajax({
            url: '" . Url::to(['sakip-indikatortujuanrenstra/create']) . "',
            type: 'GET',
            data: {
                refsasaranrenstra_id: refsasaranrenstraId,
                reftujuanrenstra_id: reftujuanrenstraId,
                refperiode_id: refperiodeId
            },
            success: function(data) {
                modal.find('#modalFormContent').html(data);
                // Set value refsasaranrenstra_id pada input hidden
                $('#reftujuanrenstra_id_hidden').val(reftujuanrenstraId);
                $('#refsasaranrenstra_id_hidden').val(refsasaranrenstraId);
                $('#refperiode_id_hidden').val(refperiodeId); // Set refperiode_id juga, jika diperlukan
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
$('#createModalIndikatorTujuanRenstra').on('show.bs.modal', function (event) {
    var modal = $(this);
    $.ajax({
        url: '" . Url::to(['sakip-indikatortujuanrenstra/create']) . "',
        type: 'GET',
        success: function(data) {
            modal.find('#modalFormContentIndikatorTujuanRenstra').html(data);
        }
    });
});
");

$this->registerJs("
$('#updateModalIndikatorTujuanRenstra').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget); // Button that triggered the modal
    var url = button.data('url'); // Extract info from data-url attributes

    var modal = $(this);
    $.ajax({
        url: url,
        type: 'GET',
        success: function(data) {
            modal.find('#modalUpdateFormContentIndikatorTujuanRenstra').html(data);
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
                            <li class="breadcrumb-item" aria-current="page">Indikator Tujuan Renstra</li>
                        </ul>
                    </div>
                    <div class="col-md-12">
                        <div class="page-header-title">
                            <h2 class="mb-0">Indikator Tujuan Renstra</h2>
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
                            <i class="fas fa-pen-fancy"></i> Indikator Tujuan Renstra - <?= Html::decode($nama_skpd) ?>
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
                        <h5>Data SAKIP Indikator Tujuan Renstra</h5>
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
                                <!-- Button filter berdasarkan refperiode_id dengan d-flex untuk horizontal layout -->
                                <div class="d-flex flex-wrap">
                                    <?php foreach ($periodeList as $periode): ?>
                                        <a href="<?= \yii\helpers\Url::to(['index', 'refperiode_id' => $periode->refperiode_id]) ?>"
                                            class="btn btn-primary mx-1 <?= ($periode->refperiode_id == $selectedPeriodId) ? 'active' : '' ?>">
                                            <?= $periode->periode ?>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>

                        <?php if (empty($dataProvider->getModels())): ?>
                            <div class="alert alert-warning mt-4">
                                Data tidak ada untuk periode yang dipilih.
                            </div>
                        <?php else: ?>
                            <?php foreach ($dataProvider->getModels() as $refsasaranrenstra_id => $tujuanRenstraGroup): ?>
                                <div class="card mb-3">
                                    <div class="card-header">
                                        <!-- Tampilkan uraian misi -->
                                        <?= $tujuanRenstraGroup[0]->misi->uraian_misi ?>
                                    </div>
                                    <div class="card-body">
                                        <div class="card mb-3">
                                            <div class="card-header">
                                                <!-- Tampilkan uraian tujuan -->
                                                <?= $tujuanRenstraGroup[0]->tujuan->uraian_tujuan ?>
                                            </div>
                                            <div class="card-body">
                                                <?php foreach ($tujuanRenstraGroup as $tujuanRenstra): ?>
                                                    <div class="card mb-3">
                                                        <div class="card-header">
                                                            <!-- Tampilkan uraian tujuan renstra -->
                                                            <?= $tujuanRenstra->uraian_tujuanrenstra ?>
                                                        </div>
                                                        <div class="card-body">
                                                            <div class="card-header">
                                                                <!-- Tampilkan uraian indikator tujuan renstra -->
                                                                <?php
                                                                $indicators = SakipIndikatortujuanrenstra::find()
                                                                    ->where(['reftujuanrenstra_id' => $tujuanRenstra->reftujuanrenstra_id])
                                                                    ->all();
                                                                if (empty($indicators)): ?>
                                                                    <div>Indikator Tujuan Renstra Belum ada</div>
                                                                <?php else: ?>
                                                                    <?php foreach ($indicators as $indicator): ?>
                                                                        <div>
                                                                            <?= $indicator->uraian_indikatortujuanrenstra ?>
                                                                        </div>
                                                                    <?php endforeach; ?>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>











                        <!--  -->
                        <div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="createModalLabel">Tambah Data SAKIP Tujuan Renstra</h5>
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
                                        <h5 class="modal-title" id="updateModalLabel">Update Data SAKIP Tujuan Renstra</h5>
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
                        <div class="modal fade" id="createModalTujuanRenstra" tabindex="-1" aria-labelledby="createModalLabelTujuanRenstra" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="createModalLabelTujuanRenstra">Tambah Data SAKIP Tujuan Renstra</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <!-- The form will be loaded here -->
                                        <div id="modalFormContentTujuanRenstra">
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
                        <div class="modal fade" id="updateModalTujuanRenstra" tabindex="-1" aria-labelledby="updateModalLabelTujuanRenstra" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="updateModalLabelIndikator">Update Data SAKIP Tujuan Renstra</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <!-- The form will be loaded here -->
                                        <div id="modalUpdateFormContentTujuanRenstra">
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