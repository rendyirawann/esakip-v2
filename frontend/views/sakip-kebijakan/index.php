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

$this->title = 'e-Sakip - Data Kebijakan';
$this->params['breadcrumbs'][] = $this->title;

$this->registerJs("
// JavaScript untuk mengubah ikon +/- saat accordion dibuka/tutup
$('.accordion-button').on('click', function() {
    $(this).find('.accordion-icon').toggleClass('fa-plus fa-minus');
});
$('.accordion-collapse').on('show.bs.collapse', function () {
    $(this).prev().find('.accordion-icon').removeClass('fa-plus').addClass('fa-minus');
}).on('hide.bs.collapse', function () {
    $(this).prev().find('.accordion-icon').removeClass('fa-minus').addClass('fa-plus');
});
");

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
                            <i class="fas fa-pen-fancy"></i>Periode Kebijakan Renstra - <?= Html::decode(ucwords(strtolower($nama_skpd))) ?>
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
                            <i class="fas fa-pen-fancy"></i> Data SAKIP Kebijakan Renstra - <?= Html::decode(ucwords(strtolower($nama_skpd))) ?> (Periode <?= $selectedPeriodValue ?>)
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

                        <div class="mt-4">
                            <?php if (empty($groupedData)): ?>
                                <div class="alert alert-warning">Data tidak ditemukan untuk periode ini.</div>
                            <?php else: ?>
                                <div class="accordion" id="accordionMisi">
                                    <?php foreach ($groupedData as $misiId => $misi): ?>
                                        <div class="accordion-item mb-3 shadow-sm">
                                            <h2 class="accordion-header" id="headingMisi<?= $misiId ?>">
                                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseMisi<?= $misiId ?>">
                                                    <i class="fas fa-flag-checkered me-2 accordion-icon"></i>
                                                    <span class="fw-bold text-uppercase">MISI:</span>&nbsp;<?= Html::decode($misi['uraian']) ?>
                                                </button>
                                            </h2>
                                            <div id="collapseMisi<?= $misiId ?>" class="accordion-collapse collapse" data-bs-parent="#accordionMisi">
                                                <div class="accordion-body">
                                                    <?php if (empty($misi['tujuans'])): ?>
                                                        <p class="text-muted fst-italic">Tidak ada tujuan terkait misi ini.</p>
                                                    <?php else: ?>
                                                        <?php foreach ($misi['tujuans'] as $tujuanId => $tujuan): ?>
                                                            <div class="tujuan-block mb-4">
                                                                <h6 class="fw-bold text-info text-uppercase border-bottom pb-2 mb-3">
                                                                    <i class="fas fa-crosshairs me-2"></i>TUJUAN: <?= Html::decode($tujuan['uraian']) ?>
                                                                </h6>
                                                                <?php foreach ($tujuan['sasarans'] as $sasaran): ?>
                                                                    <div class="p-3 border rounded mb-3" style="background-color: #f8f9fa;">
                                                                        <p class="fw-bold"><i class="fas fa-rocket me-2"></i>Sasaran: <?= Html::decode($sasaran->uraian_sasaranrenstra) ?></p>

                                                                        <?php if (empty($sasaran->strategiRenstra)): ?>
                                                                            <div class="alert alert-light text-center p-2"><i>Belum ada Strategi untuk sasaran ini.</i></div>
                                                                        <?php else: ?>
                                                                            <?php foreach ($sasaran->strategiRenstra as $strategi): ?>
                                                                                <div class="list-group-item list-group-item-light p-3 mb-2 rounded">
                                                                                    <div class="d-flex w-100 justify-content-between">
                                                                                        <h6 class="mb-1 fw-bold text-success">Strategi: <?= Html::decode($strategi->uraian_strategi) ?></h6>
                                                                                        <?= Html::button('<i class="fas fa-plus"></i> Tambah Kebijakan', [
                                                                                            'class' => 'btn btn-primary btn-sm',
                                                                                            'data-bs-toggle' => 'modal',
                                                                                            'data-bs-target' => '#createModal',
                                                                                            'data-refstrategi' => $strategi->refstrategi_id,
                                                                                            'data-refsasaranrenstra' => $sasaran->refsasaranrenstra_id,
                                                                                            'data-refperiode' => $selectedPeriodId,
                                                                                        ]) ?>
                                                                                    </div>
                                                                                    <hr class="my-2">
                                                                                    <ul class="list-unstyled ps-3">
                                                                                        <?php if (empty($strategi->kebijakan)): ?>
                                                                                            <li class="font-italic text-muted">Belum ada Kebijakan.</li>
                                                                                        <?php else: ?>
                                                                                            <?php foreach ($strategi->kebijakan as $kebijakan): ?>
                                                                                                <li class="d-flex justify-content-between align-items-center py-1">
                                                                                                    <span><i class="fas fa-caret-right me-2 text-muted"></i><?= Html::decode($kebijakan->uraian_kebijakan) ?></span>
                                                                                                    <div class="btn-group" role="group">
                                                                                                        <?= Html::button('<i class="fas fa-edit"></i>', ['class' => 'btn btn-outline-warning btn-sm', 'title' => 'Update', 'data-bs-toggle' => 'modal', 'data-bs-target' => '#updateModal', 'data-url' => Url::to(['update', 'refkebijakan_id' => $kebijakan->refkebijakan_id])]) ?>
                                                                                                        <?= Html::a('<i class="fas fa-trash-alt"></i>', ['delete', 'refkebijakan_id' => $kebijakan->refkebijakan_id], ['class' => 'btn btn-outline-danger btn-sm', 'title' => 'Hapus', 'data' => ['confirm' => 'Yakin ingin menghapus item ini?', 'method' => 'post']]) ?>
                                                                                                    </div>
                                                                                                </li>
                                                                                            <?php endforeach; ?>
                                                                                        <?php endif; ?>
                                                                                    </ul>
                                                                                </div>
                                                                            <?php endforeach; ?>
                                                                        <?php endif; ?>
                                                                    </div>
                                                                <?php endforeach; ?>
                                                            </div>
                                                        <?php endforeach; ?>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>






                        <!--  -->
                        <div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content" style="border-radius: 20px;">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="createModalLabel">Tambah Data SAKIP Kebijakan Renstra</h5>
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
                                        <h5 class="modal-title" id="updateModalLabel">Update Data SAKIP Kebijakan Renstra</h5>
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


                    </div>
                </div>

                <!-- Table end -->
            </div>
            <!-- end row -->
        </div>


        <!-- [ Main Content ] end -->
    </div>
</div>