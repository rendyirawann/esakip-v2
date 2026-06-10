<?php

use frontend\models\SakipStrategi;
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
/** @var frontend\models\search\SakipTujuanrenstraSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'e-Sakip - Data Tujuan Renstra';
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
        var button = $(event.relatedTarget); // Tombol yang diklik
        var refsasaranrenstraId = button.data('refsasaranrenstra'); // Ambil refsasaranrenstra_id dari tombol
        var refperiodeId = button.data('refperiode'); // Ambil refperiode_id dari tombol (opsional)

        var modal = $(this);
        $.ajax({
            url: '" . Url::to(['sakip-tujuanrenstra/create']) . "',
            type: 'GET',
            data: {
                refsasaranrenstra_id: refsasaranrenstraId,
                refperiode_id: refperiodeId
            },
            success: function(data) {
                modal.find('#modalFormContent').html(data);
                // Set value refsasaranrenstra_id pada input hidden
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
                            <li class="breadcrumb-item" aria-current="page">Tujuan Renstra</li>
                        </ul>
                    </div>
                    <div class="col-md-12">
                        <div class="page-header-title">
                            <h2 class="mb-0">Tujuan Renstra</h2>
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
                            <i class="fas fa-pen-fancy"></i>Periode Tujuan Renstra - <?= Html::encode(ucwords(strtolower($nama_skpd))) ?>
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
                                            'prompt' => 'Pilih Periode',
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
                            <i class="fas fa-pen-fancy"></i> Data SAKIP Tujuan Renstra - <?= Html::encode(ucwords(strtolower($nama_skpd))) ?> (Periode <?= $selectedPeriodValue ?>)
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
                                Belum Ada Data untuk SKPD / Periode yang dipilih.
                            </div>
                        <?php else: ?>
                            <?php foreach ($sasaranRenstraList as $sasaranRenstra): ?>
                                <div class="card mt-3 shadow-sm">
                                    <div class="card-header bg-primary text-white p-2 d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0" style="cursor: pointer;" data-bs-toggle="collapse" data-bs-target="#misiCollapse">
                                            <?= Html::decode($sasaranRenstra->misi->uraian_misi) ?>
                                        </h6>
                                        <i class="fas fa-chevron-down"></i>
                                    </div>
                                    <div id="misiCollapse" class="collapse show">
                                        <div class="card-body p-2">
                                            <!-- TUJUAN RENSTRA -->
                                            <div class="card mb-2 border-0">
                                                <div class="card-header bg-danger text-white p-2 d-flex justify-content-between align-items-center">
                                                    <h6 class="mb-0" style="cursor: pointer;" data-bs-toggle="collapse" data-bs-target="#tujuanCollapse">
                                                        <?= Html::decode($sasaranRenstra->tujuan->uraian_tujuan) ?>
                                                    </h6>
                                                    <i class="fas fa-chevron-down"></i>
                                                </div>
                                                <div id="tujuanCollapse" class="collapse show">
                                                    <div class="card-body p-2">
                                                        <!-- SASARAN RENSTRA -->
                                                        <div class="card mb-2 border-0">
                                                            <div class="card-header bg-success text-white p-2 d-flex justify-content-between align-items-center">
                                                                <h6 class="mb-0" style="cursor: pointer;" data-bs-toggle="collapse" data-bs-target="#sasaranCollapse">
                                                                    <?= Html::decode($sasaranRenstra->uraian_sasaranrenstra) ?>
                                                                </h6>
                                                                <i class="fas fa-chevron-down"></i>
                                                            </div>
                                                            <div id="sasaranCollapse" class="collapse show">
                                                                <div class="card-body p-2">
                                                                    <!-- TABLE Tujuan Renstra -->
                                                                    <table class="table table-bordered table-hover table-sm">
                                                                        <thead class="table-light">
                                                                            <tr>
                                                                                <th>Tujuan Renstra</th>
                                                                                <?php if ($isAdmin): ?>
                                                                                    <th class="text-center">Aksi</th>
                                                                                <?php endif; ?>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            <?php if (!empty($sasaranRenstra->tujuanRenstra)): ?>
                                                                                <?php foreach ($sasaranRenstra->tujuanRenstra as $tujuanRenstra): ?>
                                                                                    <tr>
                                                                                        <td style="white-space: normal;"><?= Html::decode($tujuanRenstra->uraian_tujuanrenstra) ?></td>
                                                                                        <?php if ($isAdmin): ?>
                                                                                            <td class="text-center">
                                                                                                <?= Html::button('<i class="fas fa-edit"></i>', [
                                                                                                    'class' => 'btn btn-outline-warning btn-sm',
                                                                                                    'title' => 'Update',
                                                                                                    'data-bs-toggle' => 'modal',
                                                                                                    'data-bs-target' => '#updateModal',
                                                                                                    'data-url' => Url::to(['update', 'reftujuanrenstra_id' => $tujuanRenstra->reftujuanrenstra_id])
                                                                                                ]) ?>
                                                                                            </td>
                                                                                        <?php endif; ?>
                                                                                    </tr>
                                                                                <?php endforeach; ?>
                                                                            <?php else: ?>
                                                                                <tr>
                                                                                    <td colspan="<?= $isAdmin ? 2 : 1 ?>" class="text-center"><b><i>Belum ada Tujuan Renstra.</i></b></td>
                                                                                </tr>
                                                                            <?php endif; ?>
                                                                        </tbody>
                                                                    </table>


                                                                </div> <!-- END card-body SASARAN -->
                                                            </div>
                                                        </div> <!-- END card SASARAN -->
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


                    </div>
                </div>

                <!-- Table end -->
            </div>
            <!-- end row -->
        </div>


        <!-- [ Main Content ] end -->
    </div>
</div>