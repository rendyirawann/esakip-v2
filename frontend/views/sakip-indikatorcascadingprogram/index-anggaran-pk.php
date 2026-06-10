<?php

use frontend\models\SakipIndikatorcascadingprogram;
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
/** @var frontend\models\search\SakipIndikatorcascadingprogramSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'e-SAKIP - PK Anggaran Cascading Program';
$this->params['breadcrumbs'][] = $this->title;

$this->registerJs("
$('#createModal').on('show.bs.modal', function (event) {
    var modal = $(this);
    $.ajax({
        url: '" . Url::to(['sakip-indikatorcascadingprogram/create']) . "',
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
                            <li class="breadcrumb-item" aria-current="page">PK Anggaran Cascading Program</li>
                        </ul>
                    </div>
                    <div class="col-md-12">
                        <div class="page-header-title">
                            <h2 class="mb-0">PK Anggaran Cascading Program</h2>
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
                            <i class="fas fa-pen-fancy"></i>Periode Data PK Anggaran Cascading Program - <?= Html::encode(ucwords(strtolower($nama_skpd))) ?>
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <!-- Dropdown filter berdasarkan refperiode_id -->
                                <?= \yii\helpers\Html::beginForm(['index-anggaran-pk'], 'get', ['class' => 'form-inline']); ?>
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
                            <i class="fas fa-pen-fancy"></i>Data PK Anggaran Cascading Program - <?= Html::encode(ucwords(strtolower($nama_skpd))) ?> (Periode <?= $selectedPeriodValue ?>)
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
                            <div class="dt-responsive table-responsive">
                                <!-- Start a new table for the current program -->
                                <table id="table-style-hover" class="table table-striped table-hover table-bordered nowrap" style="font-size:xx-small;">
                                    <?php
                                    $currentRefsasaranrenstraId = null;
                                    $currentRefprogramId = null;
                                    $no = 1; // Initialize a counter for numbering rows
                                    ?>
                                    <?php foreach ($dataProvider->getModels() as $model): ?>
                                        <?php if ($currentRefsasaranrenstraId !== $model->refsasaranrenstra_id): ?>
                                            <thead>
                                                <tr>
                                                    <th colspan="5" style="background-color: #04A9F5; color:white; white-space: normal;">
                                                        Sasaran Renstra - <?= $model->refSasaranrenstra->uraian_sasaranrenstra ?>
                                                    </th>
                                                </tr>
                                                <tr>
                                                    <th style="background-color: #e23c3c; color:white; white-space: normal;">No</th>
                                                    <th style="background-color: #e23c3c; color:white; white-space: normal;">Program</th>
                                                    <th style="background-color: #e23c3c; color:white; white-space: normal;">Anggaran Renstra</th>
                                                    <th style="background-color: #e23c3c; color:white; white-space: normal;">Anggaran RKT</th>
                                                    <th style="background-color: #e23c3c; color:white; white-space: normal;">Anggaran PK</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            <?php endif; ?>

                                            <?php if ($currentRefprogramId !== $model->refprogram_id): ?>
                                                <?php
                                                // Query to sum subkegiatan_anggaran for the current refprogram_id
                                                $subkegiatanSum = (new \yii\db\Query())
                                                    ->select(['SUM(subkegiatan_anggaran) AS total_anggaran'])
                                                    ->from('sakip_cascadingsubkegiatan')
                                                    ->where(['refprogram_id' => $model->refprogram_id])
                                                    ->andWhere(['refskpd_id' => $model->refskpd_id])
                                                    ->andWhere(['refperiode_id' => $model->refperiode_id])
                                                    ->scalar(); // Use scalar to get a single value

                                                // Handle null values if there are no subkegiatan found
                                                $totalAnggaran = $subkegiatanSum !== null ? $subkegiatanSum : 0;

                                                // Query to sum anggaran_rkt for the current refprogram_id
                                                $subkegiatanRktSum = (new \yii\db\Query())
                                                    ->select(['SUM(anggaran_rkt) AS total_anggaranrkt'])
                                                    ->from('sakip_indikatorcascadingsubkegiatan')
                                                    ->where(['refprogram_id' => $model->refprogram_id])
                                                    ->andWhere(['refskpd_id' => $model->refskpd_id])
                                                    ->andWhere(['refperiode_id' => $model->refperiode_id])
                                                    // ->andWhere(['refcascadingprogram_id' => $model->refcascadingprogram_id])
                                                    ->scalar(); // Use scalar to get a single value

                                                // Handle null values if there are no subkegiatan RKT found
                                                $totalAnggaranrkt = $subkegiatanRktSum !== null ? $subkegiatanRktSum : 0;

                                                // Query to sum anggaran_rkt for the current refprogram_id
                                                $subkegiatanPkSum = (new \yii\db\Query())
                                                    ->select(['SUM(anggaran_pk) AS total_anggaranpk'])
                                                    ->from('sakip_indikatorcascadingsubkegiatan')
                                                    ->where(['refprogram_id' => $model->refprogram_id])
                                                    ->andWhere(['refskpd_id' => $model->refskpd_id])
                                                    ->andWhere(['refperiode_id' => $model->refperiode_id])
                                                    // ->andWhere(['refcascadingprogram_id' => $model->refcascadingprogram_id])
                                                    ->scalar(); // Use scalar to get a single value

                                                // Handle null values if there are no subkegiatan RKT found
                                                $totalAnggaranpk = $subkegiatanPkSum !== null ? $subkegiatanPkSum : 0;
                                                ?>
                                                <tr>
                                                    <td><?= $no++ ?></td> <!-- Increment the counter here -->
                                                    <td style="white-space: normal;"><?= $model->refProgram->nama_program ?></td>
                                                    <td><?= 'Rp. ' . number_format($totalAnggaran, 0, ',', '.'); ?></td> <!-- Format for currency if needed -->
                                                    <td><?= 'Rp. ' . number_format($totalAnggaranrkt, 0, ',', '.'); ?></td> <!-- Format for currency if needed -->
                                                    <td><?= 'Rp. ' . number_format($totalAnggaranpk, 0, ',', '.'); ?></td> <!-- Format for currency if needed -->
                                                </tr>
                                                <?php $currentRefprogramId = $model->refprogram_id; ?>
                                            <?php endif; ?>

                                            <?php if ($currentRefsasaranrenstraId !== $model->refsasaranrenstra_id): ?>
                                            </tbody>
                                            <?php $currentRefsasaranrenstraId = $model->refsasaranrenstra_id; ?>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>



                        <!--  -->
                        <div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="createModalLabel"></h5>
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
                                        <h5 class="modal-title" id="updateModalLabel"></h5>
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

                    </div>
                </div>

                <!-- Table end -->
            </div>
            <!-- end row -->
        </div>


        <!-- [ Main Content ] end -->
    </div>
</div>