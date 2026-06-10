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

                        <?php if ($dataEmpty): ?>
                            <div class="alert alert-warning mt-4">
                                Data tidak ada untuk periode yang dipilih.
                            </div>
                        <?php else: ?>
                            <?php foreach ($data as $sasaranRenstra): ?>
                                <div class="card mt-3">
                                    <div class="card-header" style="background-color: #04A9F5; padding: 8px; font-size: 0.8rem;">
                                        <h6 style="color: white; margin: 0; cursor: pointer;" id="toggleAll">
                                            <!-- Tampilkan uraian misi -->
                                            <?= Html::decode($sasaranRenstra->misi->uraian_misi) ?>
                                        </h6>
                                    </div>

                                    <div class="card-body" style="margin-top: -3em; margin-right:-2em; font-size: 0.8rem;">
                                        <div class="card mb-3">
                                            <div class="card-header" style="background-color: #e23c3c; padding: 8px; font-size: 0.8rem;">
                                                <h6 style="color: white; margin: 0; cursor: pointer;" id="toggleAll">
                                                    (Tujuan)<?= Html::decode($sasaranRenstra->tujuan->uraian_tujuan) ?>
                                                </h6>
                                            </div>
                                            <div class="card-body" style="margin-top: -3em; margin-right:-2em; font-size: 0.8rem;">
                                                <div class="card mb-3">
                                                    <div class="card-header" style="background-color: #e23c3c; padding: 8px; font-size: 0.8rem;">
                                                        <!-- Tampilkan uraian indikator tujuan renstra -->
                                                        <?php
                                                        // Fetching the indicators for the current refsasaranrenstra_id
                                                        $tujuanRenstra = SakipTujuanrenstra::find()
                                                            ->where(['refsasaranrenstra_id' => $sasaranRenstra->refsasaranrenstra_id])
                                                            ->all();

                                                        if (!empty($tujuanRenstra)):
                                                            foreach ($tujuanRenstra as $tujuan): ?>
                                                                <h6 style="color: white; margin: 0; cursor: pointer;" id="toggleAll">
                                                                    (Tujuan Renstra) <br><?= Html::decode($tujuan->uraian_tujuanrenstra) ?>
                                                                </h6>
                                                    </div>

                                                    <!-- Fetching the indicators for the current reftujuanrenstra_id -->
                                                    <?php
                                                                $indicators = SakipIndikatortujuanrenstra::find()
                                                                    ->where(['reftujuanrenstra_id' => $tujuan->reftujuanrenstra_id])
                                                                    ->all();
                                                    ?>
                                                    <div class="card-body" style="margin-top: -2.2em; margin-right:-2em; font-size: 0.8rem;">
                                                        <div class="card mb-3">
                                                            <div class="card-header" style="background-color: #e23c3c; padding: 8px; font-size: 0.8rem;">
                                                                <h6 style="color: white; margin: 0; cursor: pointer;" id="toggleAll">
                                                                    Indikator untuk Tujuan: <?= Html::decode($tujuan->uraian_tujuanrenstra) ?>
                                                                </h6>
                                                            </div>
                                                            <div class="card-body" style="font-size: 0.8rem;">
                                                                <table class="table table-sm" style="border: none; width: 100%; font-size: 0.7rem;">

                                                                    <tbody>
                                                                        <?php if (!empty($indicators)): ?>
                                                                            <?php foreach ($indicators as $indicator): ?>
                                                                                <tr>
                                                                                    <td><?= Html::decode($indicator->uraian_indikatortujuanrenstra) ?></td>
                                                                                    <td><?= Html::button('<i class="fas fa-edit"></i>', [
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
                                                                                        ]) ?></td>
                                                                                </tr>

                                                                            <?php endforeach; ?>
                                                                        <?php else: ?>
                                                                            <tr>
                                                                                <td colspan="2"><b><i>Indikator belum ada.</i></b></td>
                                                                            </tr>
                                                                        <?php endif; ?>
                                                                    </tbody>
                                                                </table>
                                                                <?= Html::button('Tambah Data Indikator Tujuan Renstra', [
                                                                    'class' => 'btn btn-success mb-2',
                                                                    'data-bs-toggle' => 'modal',
                                                                    'data-bs-target' => '#createModal',
                                                                    'data-refsasaranrenstra' => $sasaranRenstra->refsasaranrenstra_id, // Mengambil refsasaranrenstra_id
                                                                    'data-reftujuanrenstra' => $tujuan->reftujuanrenstra_id, // Mengambil reftujuanrenstra_id
                                                                    'data-refperiode' => $sasaranRenstra->refperiode_id // Mengambil refperiode_id
                                                                ]) ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <p>Tujuan Renstra belum ada.</p>
                                        <?php endif; ?>

                                            </div>
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