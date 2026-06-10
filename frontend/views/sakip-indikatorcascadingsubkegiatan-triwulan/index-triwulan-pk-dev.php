<?php

use frontend\models\SakipIndikatorcascadingkegiatanTriwulan;
use frontend\models\SakipIndikatorcascadingkegiatan;
use frontend\models\SakipIndikatorsasaranrenstra;
use frontend\models\SakipSasaranrenstra;
use frontend\models\SakipSasaran;
use frontend\models\SakipPeriode;
use frontend\models\SakipTujuan;
use frontend\models\SakipIndikatorcascadingsubkegiatan;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use yii\grid\ActionColumn;
use yii\grid\GridView;


/** @var yii\web\View $this */
/** @var frontend\models\search\SakipIndikatorcascadingkegiatanTriwulanSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'e-SAKIP - PK Triwulan Indikator Cascading Sub Kegiatan';
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
        url: '" . Url::to(['sakip-indikatorcascadingsubkegiatan-triwulan/create']) . "',
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
                            <li class="breadcrumb-item" aria-current="page">PK Triwulan Indikator Cascading Sub Kegiatan</li>
                        </ul>
                    </div>
                    <div class="col-md-12">
                        <div class="page-header-title">
                            <h2 class="mb-0">PK Triwulan Indikator Cascading Sub Kegiatan</h2>
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
                            <i class="fas fa-pen-fancy"></i>Periode Data PK Triwulan Indikator Cascading Sub Kegiatan - <?= Html::encode(ucwords(strtolower($nama_skpd))) ?>
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <!-- Dropdown filter berdasarkan refperiode_id -->
                                <?= \yii\helpers\Html::beginForm(['index-triwulan-pk-dev'], 'get', ['class' => 'form-inline']); ?>
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
                            <i class="fas fa-pen-fancy"></i>Data PK Triwulan Indikator Cascading Sub Kegiatan - <?= Html::encode(ucwords(strtolower($nama_skpd))) ?> (Periode <?= $selectedPeriodValue ?>)
                        </h6>
                    </div>
                    <div class="card-body" id="refresh">
                        <!-- Flash messages -->
                        <?php if (Yii::$app->session->hasFlash('success')): ?>
                            <div class="alert alert-success">
                                <?= Yii::$app->session->getFlash('success') ?>
                            </div>
                        <?php endif; ?>

                        <?php if (Yii::$app->session->hasFlash('error')): ?>
                            <div class="alert alert-danger">
                                <?= Yii::$app->session->getFlash('error') ?>
                            </div>
                        <?php endif; ?>
                        <div class="row">
                            <div class="col-sm-2">
                                <!-- Buttons to select reftriwulan_id, arranged vertically -->
                                <div class="d-flex flex-column">
                                    <?php for ($i = 1; $i <= 4; $i++): ?>
                                        <a href="<?= \yii\helpers\Url::to(['index-triwulan-pk-dev', 'refperiode_id' => $selectedPeriodId, 'refskpd_id' => $selectedSkpdId, 'reftriwulan_id' => $i]) ?>"
                                            class="btn btn-secondary my-1 <?= ($selectedTriwulanId == $i) ? 'active' : '' ?>">
                                            Triwulan <?= $i ?>
                                        </a>
                                    <?php endfor; ?>
                                </div>
                            </div>
                            <div class="col-sm-10">
                                <!-- Content -->
                                <?php if ($dataEmpty): ?>
                                    <div class="alert alert-warning mt-4">
                                        Data tidak ada untuk periode yang dipilih.
                                    </div>
                                <?php else: ?>
                                    <div class="dt-responsive table-responsive">
                                        <?php
                                        $lastKegiatanId = null; // Variable to track the previous Kegiatan ID
                                        $subkegiatanCounter = 1; // Counter for numbering within each Kegiatan

                                        foreach ($data as $indikator):
                                            // If refkegiatan_id changes, create a new table for the new kegiatan
                                            if ($lastKegiatanId !== $indikator->refkegiatan_id):
                                                // If not the first iteration, close the previous table
                                                if ($lastKegiatanId !== null): ?>
                                                    </tbody>
                                                    </table>
                                                <?php endif; ?>

                                                <!-- Start a new table for the current program -->
                                                <table id="table-style-hover" class="table table-striped table-hover table-bordered nowrap" style="font-size:xx-small;">
                                                    <thead>
                                                        <tr>
                                                            <th colspan="<?= $isAdmin ? 7 : 6 ?>" style="background-color: #04A9F5; color: white; white-space:normal;">
                                                                Sub Kegiatan <?= $subkegiatanCounter++ ?>: <?= $indikator->refSubkegiatan->nama_subkegiatan ?>
                                                            </th>
                                                        </tr>
                                                        <tr>
                                                            <th style="background-color: #e23c3c; color: white; white-space:normal;">No</th>
                                                            <th style="background-color: #e23c3c; color: white; white-space:normal;">Indikator</th>
                                                            <th style="background-color: #e23c3c; color: white; white-space:normal;">Satuan</th>
                                                            <th style="background-color: #e23c3c; color: white; white-space:normal;">Target PK Tahunan (Periode: <?= $selectedPeriodValue ?>)</th>
                                                            <th style="background-color: #e23c3c; color: white; white-space:normal;">Target PK Triwulan (Triwulan <?= $selectedTriwulanId ?>)</th>
                                                            <th style="background-color: #e23c3c; color: white; white-space:normal;">Sebab Perubahan</th>
                                                             <?php if ($isAdmin): ?>
                                                                 <th style="background-color: #e23c3c; color: white; white-space:normal;">Action</th>
                                                             <?php endif; ?>

                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                    <?php
                                                    // Reset row counter for each kegiatan
                                                    $no = 1;
                                                    $lastSubkegiatanId = $indikator->refsubkegiatan_id; // Update lastSubkegiatanId
                                                endif;
                                                    ?>
                                                    <tr>
                                                        <td><?= $no++ ?></td>
                                                        <td style="white-space:normal;"><?= $indikator->refCascadingSubkegiatan->uraian_sasaransubkegiatan ?> / <?= $indikator->refCascadingSubkegiatan->uraian_indikatorsubkegiatan ?></td>
                                                        <td><?= $indikator->refCascadingSubkegiatan->subkegiatan_satuan ?></td>
                                                        <td><?= $indikator->refIndikatorCascadingSubkegiatan->target_pk ?></td>
                                                        <td><?= $indikator->triwulan_target_pk ?></td>
                                                        <td style="white-space:normal;"><?= $indikator->triwulan_keterangan ?></td>
                                                        <?php if ($isAdmin): ?>
                                                            <td>
                                                                <?= Html::button('<i class="fas fa-edit"></i>', [
                                                                    'class' => 'btn btn-success btn-sm btn-edit-cascadingsubkegiatan-triwulanpk',
                                                                    'title' => 'Update',
                                                                    'data-bs-toggle' => 'modal',
                                                                    'data-bs-target' => '#updateModal',
                                                                    'data-url' => Url::to(['update-triwulan-pk', 'refindikatorsubkegiatantriwulan_id' => $indikator->refindikatorsubkegiatantriwulan_id, 'reftriwulan_id' => $indikator->reftriwulan_id])
                                                                ]) ?>
                                                            </td>
                                                        <?php endif; ?>

                                                    </tr>
                                                    <?php
                                                    // Check if the next entry belongs to a different subkegiatan. If so, close the table.
                                                    $next = next($data);
                                                    if (!$next || $next->refsubkegiatan_id !== $lastSubkegiatanId): ?>
                                                    </tbody>
                                                </table>
                                        <?php endif;
                                                endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!--  -->
                <div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content" style="border-radius: 20px;">
                            <div class="modal-header">
                                <h5 class="modal-title" id="createModalLabel"></h5>
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
                                <h5 class="modal-title" id="updateModalLabel">Update Data PK Triwulan Cascading Sub Kegiatan</h5>
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