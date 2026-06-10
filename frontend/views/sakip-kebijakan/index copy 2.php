<?php

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
/** @var frontend\models\search\SakipKebijakanSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Sakip Kebijakan';
$this->params['breadcrumbs'][] = $this->title;

$this->registerJs("
    $('#createModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget); // Tombol yang diklik
        var refsasaranrenstraId = button.data('refsasaranrenstra'); // Ambil refsasaranrenstra_id dari tombol
        var refstrategiId = button.data('refstrategi'); // Ambil refstrategi_id dari tombol
        var refperiodeId = button.data('refperiode'); // Ambil refperiode_id dari tombol (opsional)

        var modal = $(this);
        $.ajax({
            url: '" . Url::to(['sakip-kebijakan/create']) . "',
            type: 'GET',
            data: {
                refsasaranrenstra_id: refsasaranrenstraId,
                refstrategi_id: refstrategiId, // Kirim refstrategi_id
                refperiode_id: refperiodeId
            },
            success: function(data) {
                modal.find('#modalFormContent').html(data);
                // Set value refsasaranrenstra_id dan refstrategi_id pada input hidden
                $('#refsasaranrenstra_id_hidden').val(refsasaranrenstraId);
                $('#refstrategi_id_hidden').val(refstrategiId); // Set refstrategi_id juga
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
                            <li class="breadcrumb-item" aria-current="page">Kebijakan Renstra</li>
                        </ul>
                    </div>
                    <div class="col-md-12">
                        <div class="page-header-title">
                            <h2 class="mb-0">Kebijakan Renstra</h2>
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
                            <i class="fas fa-pen-fancy"></i> Kebijakan Renstra - <?= Html::decode($nama_skpd) ?>
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
                        <h5>Data SAKIP Kebijakan Renstra</h5>
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

                        <?php if ($dataEmpty): ?>
                            <div class="alert alert-warning mt-4">
                                Data tidak ada untuk periode yang dipilih.
                            </div>
                        <?php else: ?>
                            <?php foreach ($data as $sasaran): ?>
                                <div class="card mt-3">
                                    <div class="card-header" style="background-color: #04A9F5; padding: 8px; font-size: 0.8rem;">
                                        <h6 style="color: white; margin: 0; cursor: pointer;" id="toggleAll">
                                            <!-- Tampilkan uraian misi -->
                                            <?= Html::decode($sasaran->misi->uraian_misi) ?>
                                        </h6>
                                    </div>
                                    <div class="card-body" style="margin-top: -3em; margin-right:-2em; font-size: 0.8rem;">
                                        <div class="card">
                                            <div class="card-header" style="background-color: #e23c3c; padding: 8px; font-size: 0.8rem;">
                                                <h6 style="color: white; margin: 0; cursor: pointer;" id="toggleAll">
                                                    (Tujuan)<?= Html::decode($sasaran->tujuan->uraian_tujuan) ?>
                                                </h6>
                                            </div>
                                            <div class="card-body" style="margin-top: -3em; margin-right:-2em; font-size: 0.8rem;">
                                                <div class="card">
                                                    <div class="card-header" style="background-color: #e23c3c; padding: 8px; font-size: 0.8rem;">
                                                        <?php
                                                        // Fetch sasaran renstra
                                                        $sasaranRenstraList = SakipSasaranRenstra::find()
                                                            ->where(['refsasaranrenstra_id' => $sasaran->refsasaranrenstra_id])
                                                            ->andWhere(['reftujuan_id' => $sasaran->reftujuan_id])
                                                            ->andWhere(['refmisi_id' => $sasaran->refmisi_id])
                                                            ->all();
                                                        foreach ($sasaranRenstraList as $renstra): ?>
                                                            <h6 style="color: white; margin: 0; cursor: pointer;" id="toggleAll">
                                                                <?= $renstra->uraian_sasaranrenstra ?> <!-- Tampilkan setiap uraian sasaran renstra -->
                                                            </h6>
                                                        <?php endforeach; ?>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="card mb-3">
                                                            <div class="card-header">
                                                                <?php
                                                                // Fetch strategies
                                                                $strategies = SakipStrategi::find()
                                                                    ->where(['refsasaranrenstra_id' => $sasaran->refsasaranrenstra_id])
                                                                    ->all();
                                                                foreach ($strategies as $strategy): ?>
                                                                    <div>
                                                                        <?= $strategy->uraian_strategi ?> <!-- Tampilkan setiap uraian strategi -->
                                                                    </div>
                                                                    <div class="ml-3"> <!-- Add some indentation for kebijakan -->
                                                                        <?php
                                                                        // Fetch policies for the current strategy
                                                                        $policies = SakipKebijakan::find()
                                                                            ->where(['refsasaranrenstra_id' => $sasaran->refsasaranrenstra_id])
                                                                            ->andWhere(['refstrategi_id' => $strategy->refstrategi_id])
                                                                            ->all();

                                                                        if (empty($policies)): ?>
                                                                            <div>Kebijakan strategi belum ada.</div>
                                                                            <?= Html::button('Tambah Data Kebijakan', [
                                                                                'class' => 'btn btn-success mb-2',
                                                                                'data-bs-toggle' => 'modal',
                                                                                'data-bs-target' => '#createModal',
                                                                                'data-refsasaranrenstra' => $renstra->refsasaranrenstra_id, // Menggunakan $renstra
                                                                                'data-refstrategi' => $strategy->refstrategi_id, // Mengambil refstrategi_id
                                                                                'data-refperiode' => $renstra->refperiode_id // Menggunakan $renstra
                                                                            ]) ?>

                                                                        <?php else: ?>
                                                                            <?php foreach ($policies as $policy): ?>
                                                                                <div>
                                                                                    <?= $policy->uraian_kebijakan ?> <!-- Tampilkan setiap uraian kebijakan -->
                                                                                </div>
                                                                                <?= Html::button('Tambah Data Kebijakan', [
                                                                                    'class' => 'btn btn-success mb-2',
                                                                                    'data-bs-toggle' => 'modal',
                                                                                    'data-bs-target' => '#createModal',
                                                                                    'data-refsasaranrenstra' => $renstra->refsasaranrenstra_id, // Menggunakan $renstra
                                                                                    'data-refstrategi' => $strategy->refstrategi_id, // Mengambil refstrategi_id
                                                                                    'data-refperiode' => $renstra->refperiode_id // Menggunakan $renstra
                                                                                ]) ?>
                                                                                <?= Html::button('<i class="fas fa-edit"></i>', [
                                                                                    'class' => 'btn btn-success btn-sm',
                                                                                    'title' => 'Update',
                                                                                    'data-bs-toggle' => 'modal',
                                                                                    'data-bs-target' => '#updateModal',
                                                                                    'data-url' => Url::to(['update', 'refkebijakan_id' => $policy->refkebijakan_id])
                                                                                ]) ?>
                                                                                <?= Html::a('<i class="fas fa-trash-alt"></i>', ['delete', 'refkebijakan_id' => $policy->refkebijakan_id], [
                                                                                    'class' => 'btn btn-danger btn-sm',
                                                                                    'title' => 'Delete',
                                                                                    'data' => [
                                                                                        'confirm' => 'Are you sure you want to delete this item?',
                                                                                        'method' => 'post',
                                                                                    ],
                                                                                ]) ?>
                                                                            <?php endforeach; ?>
                                                                        <?php endif; ?>
                                                                    </div>
                                                                <?php endforeach; ?>
                                                            </div>
                                                        </div>
                                                    </div>
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
                                        <h5 class="modal-title" id="createModalLabel">Tambah Data SAKIP Kebijakan Renstra</h5>
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
                                        <h5 class="modal-title" id="updateModalLabel">Update Data SAKIP Kebijakan Renstra</h5>
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


                    </div>
                </div>

                <!-- Table end -->
            </div>
            <!-- end row -->
        </div>


        <!-- [ Main Content ] end -->
    </div>
</div>