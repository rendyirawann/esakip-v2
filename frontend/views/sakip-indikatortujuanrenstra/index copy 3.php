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

$this->title = 'e-Sakip - Data Indikator Tujuan Renstra';
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
                            <i class="fas fa-pen-fancy"></i>Periode Indikator Tujuan Renstra - <?= Html::encode(ucwords(strtolower($nama_skpd))) ?>
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
                            <i class="fas fa-pen-fancy"></i> Data SAKIP Indikator Tujuan Renstra - <?= Html::encode(ucwords(strtolower($nama_skpd))) ?> (Periode <?= $selectedPeriodValue ?>)
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



                        <?php if ($dataEmpty): ?>
                            <div class="alert alert-warning mt-4">
                                Data tidak ada untuk periode yang dipilih.
                            </div>
                        <?php else: ?>
                            <?php foreach ($data as $sasaranRenstra): ?>
                                <div class="card mt-3 shadow-sm">
                                    <div class="card-header bg-info text-white p-2 d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0" style="cursor: pointer;" data-bs-toggle="collapse" data-bs-target="#misiCollapse">
                                            <?= Html::decode($sasaranRenstra->misi->uraian_misi) ?>
                                        </h6>
                                        <i class="fas fa-chevron-down"></i>
                                    </div>
                                    <div id="misiCollapse" class="collapse show">
                                        <div class="card-body p-2">
                                            <!-- TUJUAN  -->
                                            <div class="card mb-2 border-0" id="refresh">
                                                <div class="card-header bg-danger text-white p-2 d-flex justify-content-between align-items-center">
                                                    <h6 class="mb-0" style="cursor: pointer;" data-bs-toggle="collapse" data-bs-target="#tujuanCollapse">
                                                        (Tujuan) <?= Html::decode($sasaranRenstra->tujuan->uraian_tujuan) ?>
                                                    </h6>
                                                    <i class="fas fa-chevron-down"></i>
                                                </div>
                                                <div id="tujuanCollapse" class="collapse show">
                                                    <div class="card-body p-2">
                                                        <?php
                                                        $tujuanRenstra = SakipTujuanrenstra::find()
                                                            ->where(['refsasaranrenstra_id' => $sasaranRenstra->refsasaranrenstra_id])
                                                            ->all();

                                                        if (!empty($tujuanRenstra)):
                                                            foreach ($tujuanRenstra as $tujuan): ?>
                                                                <!-- Tujuan RENSTRA -->
                                                                <div class="card mb-2 border-0">
                                                                    <div class="card-header bg-success text-white p-2 d-flex justify-content-between align-items-center">
                                                                        <h6 class="mb-0" style="cursor: pointer;" data-bs-toggle="collapse" data-bs-target="#indikatorUntukTujuanCollapse">
                                                                            (Tujuan Renstra) <br><?= Html::decode($tujuan->uraian_tujuanrenstra) ?>
                                                                        </h6>
                                                                        <i class="fas fa-chevron-down"></i>
                                                                    </div>
                                                                    <div id="indikatorUntukTujuanCollapse" class="collapse show">
                                                                        <div class="card-body p-2">
                                                                            <?php
                                                                            $indicators = SakipIndikatortujuanrenstra::find()
                                                                                ->where(['reftujuanrenstra_id' => $tujuan->reftujuanrenstra_id])
                                                                                ->all();
                                                                            ?>
                                                                            <!-- INDIKATOR UNTUK TUJUAN RENSTRA -->
                                                                            <div class="card mb-2 border-0">
                                                                                <div class="card-header bg-primary text-white p-2 d-flex justify-content-between align-items-center">
                                                                                    <h6 class="mb-0" style="cursor: pointer;" data-bs-toggle="collapse" data-bs-target="#indikatorTujuanCollapse">
                                                                                        Indikator untuk Tujuan: <br><?= Html::decode($tujuan->uraian_tujuanrenstra) ?>
                                                                                    </h6>
                                                                                    <i class="fas fa-chevron-down"></i>
                                                                                </div>
                                                                                <div id="indikatorTujuanCollapse" class="collapse show">
                                                                                    <div class="card-body p-2">
                                                                                        <!-- TABLE INDIKATOR TUJUAN RENSTRA -->
                                                                                        <table class="table table-bordered table-hover table-sm">
                                                                                            <thead class="table-light">
                                                                                                <tr>
                                                                                                    <th>Indikator Tujuan Renstra</th>
                                                                                                    <th class="text-center">Aksi</th>
                                                                                                </tr>
                                                                                            </thead>
                                                                                            <tbody>
                                                                                                <?php if (!empty($indicators)): ?>
                                                                                                    <?php foreach ($indicators as $indicator): ?>
                                                                                                        <tr>
                                                                                                            <td style="white-space: normal;"><?= Html::decode($indicator->uraian_indikatortujuanrenstra) ?></td>
                                                                                                            <td class="text-center">
                                                                                                                <?= Html::button('<i class="fas fa-edit"></i>', [
                                                                                                                    'class' => 'btn btn-success btn-sm',
                                                                                                                    'title' => 'Update',
                                                                                                                    'data-bs-toggle' => 'modal',
                                                                                                                    'data-bs-target' => '#updateModal',
                                                                                                                    'data-url' => Url::to(['update', 'refindikatortujuanrenstra_id' => $indicator->refindikatortujuanrenstra_id])
                                                                                                                ]) ?>
                                                                                                                <?= Html::a('<i class="fas fa-trash-alt"></i>', ['delete', 'refindikatortujuanrenstra_id' => $indicator->refindikatortujuanrenstra_id], [
                                                                                                                    'class' => 'btn btn-danger btn-sm',
                                                                                                                    'title' => 'Delete',
                                                                                                                    'data' => [
                                                                                                                        'confirm' => 'Are you sure you want to delete this item?',
                                                                                                                        'method' => 'post',
                                                                                                                    ],
                                                                                                                ]) ?>
                                                                                                            </td>
                                                                                                        </tr>
                                                                                                    <?php endforeach; ?>
                                                                                                <?php else: ?>
                                                                                                    <tr>
                                                                                                        <td colspan="2"><b><i>Indikator belum ada.</i></b></td>
                                                                                                    </tr>
                                                                                                <?php endif; ?>
                                                                                            </tbody>
                                                                                        </table>

                                                                                        <!-- TOMBOL TAMBAH -->
                                                                                        <div class="text-end mt-2">
                                                                                            <!-- Tombol tambah data tujuan renstra -->
                                                                                            <?= Html::button('Tambah Data Indikator Tujuan Renstra', [
                                                                                                'class' => 'btn btn-success mb-2',
                                                                                                'data-bs-toggle' => 'modal',
                                                                                                'data-bs-target' => '#createModal',
                                                                                                'data-refsasaranrenstra' => $sasaranRenstra->refsasaranrenstra_id, // Mengambil refsasaranrenstra_id
                                                                                                'data-reftujuanrenstra' => $tujuan->reftujuanrenstra_id, // Mengambil reftujuanrenstra_id
                                                                                                'data-refperiode' => $sasaranRenstra->refperiode_id // Mengambil refperiode_id
                                                                                            ]) ?>
                                                                                        </div>

                                                                                    </div> <!-- END card-body SASARAN -->
                                                                                </div>

                                                                            </div> <!-- END card SASARAN -->
                                                                        </div>
                                                                    </div> <!-- END card-body TUJUAN -->

                                                                </div> <!-- END card SASARAN -->
                                                            <?php endforeach; ?>
                                                        <?php else: ?>
                                                            <p><b><i>Tujuan Renstra belum ada.</i></b></p>
                                                        <?php endif; ?>
                                                    </div>
                                                </div> <!-- END card-body TUJUAN -->
                                            </div>
                                        </div>
                                    </div> <!-- END card-body MISI -->
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>


                        <!--  -->
                        <div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content" style="border-radius: 20px;">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="createModalLabel">Tambah Data SAKIP Tujuan Renstra</h5>
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
                                        <h5 class="modal-title" id="updateModalLabel">Update Data SAKIP Tujuan Renstra</h5>
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
                        <div class="modal fade" id="createModalTujuanRenstra" tabindex="-1" aria-labelledby="createModalLabelTujuanRenstra" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content" style="border-radius: 20px;">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="createModalLabelTujuanRenstra">Tambah Data SAKIP Tujuan Renstra</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <!-- The form will be loaded here -->
                                        <div id="modalFormContentTujuanRenstra" style="padding-bottom:20px; padding-right:15px; padding-left:15px;">
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
                                <div class="modal-content" style="border-radius: 20px;">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="updateModalLabelIndikator">Update Data SAKIP Tujuan Renstra</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <!-- The form will be loaded here -->
                                        <div id="modalUpdateFormContentTujuanRenstra" style="padding-bottom:20px; padding-right:15px; padding-left:15px;">
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