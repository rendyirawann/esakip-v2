<?php

use backend\models\SakipLkekomponen;
use backend\models\SakipLkesubkomponen;
use backend\models\SakipLkesubkriteria;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var backend\models\search\SakipLkekomponenSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'e-Sakip (Admin) - Data LKE';
$this->params['breadcrumbs'][] = $this->title;

$this->registerJs("
$('#createModal').on('show.bs.modal', function (event) {
    var modal = $(this);
    $.ajax({
        url: '" . Url::to(['sakip-lkekomponen/create']) . "',
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
$('#createModalLkesubkomponen').on('show.bs.modal', function (event) {
    var modal = $(this);
    var button = $(event.relatedTarget);  // Button that triggered the modal
    var reflkekomponen_id = button.data('reflkekomponen_id');  // Extract value from data-* attributes

    // Set the hidden inputs in the modal with the correct values
    modal.find('#reflkekomponen_id').val(reflkekomponen_id);

    // Send the data to load the form
    $.ajax({
        url: '" . Url::to(['sakip-lkesubkomponen/create']) . "',
        type: 'GET',
        data: { reflkekomponen_id: reflkekomponen_id }, // Include the data
        success: function(data) {
            modal.find('#modalFormContentLkesubkomponen').html(data);
        }
    });
});
");

$this->registerJs("
$('#updateModalLkesubkomponen').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget); // Button that triggered the modal
    var url = button.data('url'); // Extract info from data-url attributes

    var modal = $(this);
    $.ajax({
        url: url,
        type: 'GET',
        success: function(data) {
            modal.find('#modalUpdateFormContentLkesubkomponen').html(data);
        }
    });
});
");

$this->registerJs("
$('#createModalLkesubkriteria').on('show.bs.modal', function (event) {
    var modal = $(this);
    var button = $(event.relatedTarget);  // Button that triggered the modal
    var reflkekomponen_id = button.data('reflkekomponen_id');  // Extract value from data-* attributes
    var reflkesubkomponen_id = button.data('reflkesubkomponen_id');  // Extract value from data-* attributes

    // Set the hidden inputs in the modal with the correct values
    modal.find('#reflkekomponen_id').val(reflkekomponen_id);
    modal.find('#reflkesubkomponen_id').val(reflkesubkomponen_id);

    // Send the data to load the form
    $.ajax({
        url: '" . Url::to(['sakip-lkesubkriteria/create']) . "',
        type: 'GET',
        data: { reflkekomponen_id: reflkekomponen_id, reflkesubkomponen_id: reflkesubkomponen_id }, // Include the data
        success: function(data) {
            modal.find('#modalFormContentLkesubkriteria').html(data);
        }
    });
});
");

$this->registerJs("
$('#updateModalLkesubkriteria').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget); // Button that triggered the modal
    var url = button.data('url'); // Extract info from data-url attributes

    var modal = $(this);
    $.ajax({
        url: url,
        type: 'GET',
        success: function(data) {
            modal.find('#modalUpdateFormContentLkesubkriteria').html(data);
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
                            <li class="breadcrumb-item" aria-current="page">Data SAKIP LKE</li>
                        </ul>
                    </div>
                    <div class="col-md-12">
                        <div class="page-header-title">
                            <h2 class="mb-0">Data SAKIP LKE</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- [ breadcrumb ] end -->


        <!-- [ Main Content ] start -->
        <div class="row">
            <!-- Base style - Hover table start -->
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Data SAKIP LKE</h5>
                        <small>List Data</small>
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
                        <?= Html::button('Tambah Data SAKIP LKE Komponen', [
                            'class' => 'btn btn-success',
                            'data-bs-toggle' => 'modal',
                            'data-bs-target' => '#createModal',
                        ]) ?>

                        <div class="dt-responsive table-responsive">
                            <table id="table-style-hover" class="table table-striped table-hover table-bordered nowrap" style="font-size:xx-small;">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Komponen</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    <?php $no = 1; ?>
                                    <?php foreach ($dataProvider->models as $model) : ?>
                                        <tr>
                                            <td><?= Html::encode($no++) ?></td>
                                            <td>
                                                <?= Html::button('<i class="fas fa-edit"></i>', [
                                                    'class' => 'btn btn-success btn-sm',
                                                    'title' => 'Update',
                                                    'data-bs-toggle' => 'modal',
                                                    'data-bs-target' => '#updateModal',
                                                    'data-url' => Url::to(['update', 'reflkekomponen_id' => $model->reflkekomponen_id])
                                                ]) ?>
                                                <?= Html::a('<i class="fas fa-trash-alt"></i>', ['delete', 'reflkekomponen_id' => $model->reflkekomponen_id], [
                                                    'class' => 'btn btn-danger btn-sm',
                                                    'title' => 'Delete',
                                                    'data' => [
                                                        'confirm' => 'Are you sure you want to delete this item?',
                                                        'method' => 'post',
                                                    ],
                                                ]) ?>
                                                <?= Html::button('<i class="fas fa-check"></i>', [
                                                    'class' => 'btn btn-success btn-sm mx-3',
                                                    'title' => 'Tambah Sub Komponen',
                                                    'data-bs-toggle' => 'modal',
                                                    'data-bs-target' => '#createModalLkesubkomponen',
                                                    'data-reflkekomponen_id' => $model->reflkekomponen_id,
                                                ]) ?>
                                                <?php
                                                // Hitung total bobot untuk komponen ini
                                                $totalBobot = SakipLkesubkomponen::find()
                                                    ->where(['reflkekomponen_id' => $model->reflkekomponen_id])
                                                    ->sum('bobot_lkesubkomponen');
                                                ?>
                                                <?= Html::encode($model->uraian_lkekomponen) ?> <b> (Total Bobot: <?= Html::encode($totalBobot) ?>)</b>

                                                <!-- Accordion -->
                                                <div class="accordion mt-2" id="accordion-<?= $model->reflkekomponen_id ?>">
                                                    <div class="accordion-item">
                                                        <h2 class="accordion-header" id="heading-<?= $model->reflkekomponen_id ?>">
                                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-<?= $model->reflkekomponen_id ?>" aria-expanded="false" aria-controls="collapse-<?= $model->reflkekomponen_id ?>">
                                                                Lihat Detail Sub Komponen
                                                            </button>
                                                        </h2>
                                                        <div id="collapse-<?= $model->reflkekomponen_id ?>" class="accordion-collapse collapse" aria-labelledby="heading-<?= $model->reflkekomponen_id ?>" data-bs-parent="#accordion-<?= $model->reflkekomponen_id ?>">
                                                            <div class="accordion-body">
                                                                <?php
                                                                $subkomponenModels = SakipLkesubkomponen::findAll(['reflkekomponen_id' => $model->reflkekomponen_id]);
                                                                if (!empty($subkomponenModels)) {
                                                                ?>
                                                                    <div class="table-responsive">
                                                                        <table class="table table-bordered table-striped" style="font-size:small;">
                                                                            <thead>
                                                                                <tr>
                                                                                    <th>No</th>
                                                                                    <th>Uraian Sub Komponen</th>
                                                                                    <th>Bobot Sub Komponen</th>

                                                                                </tr>
                                                                            </thead>
                                                                            <tbody>
                                                                                <?php $no = 1; ?>
                                                                                <?php foreach ($subkomponenModels as $subkomponenModel) : ?>
                                                                                    <tr>
                                                                                        <td><?= Html::encode($no++) ?></td>
                                                                                        <td>
                                                                                            <?= Html::button('<i class="fas fa-eye"></i>', [
                                                                                                'class' => 'btn btn-primary btn-sm',
                                                                                                'data-bs-toggle' => 'modal',
                                                                                                'data-bs-target' => '#modalKriteria-' . $subkomponenModel->reflkesubkomponen_id,
                                                                                                'data-bs-backdrop' => 'static',
                                                                                                'data-bs-focus' => 'false',
                                                                                            ]) ?>
                                                                                            <?= Html::button('<i class="fas fa-edit"></i>', [
                                                                                                'class' => 'btn btn-success btn-sm',
                                                                                                'title' => 'Update',
                                                                                                'data-bs-toggle' => 'modal',
                                                                                                'data-bs-target' => '#updateModalLkesubkomponen',
                                                                                                'data-url' => Url::to(['sakip-lkesubkomponen/update', 'reflkesubkomponen_id' => $subkomponenModel->reflkesubkomponen_id])
                                                                                            ]) ?>
                                                                                            <?= Html::a('<i class="fas fa-trash-alt"></i>', [
                                                                                                'sakip-lkesubkomponen/delete',
                                                                                                'reflkesubkomponen_id' => $subkomponenModel->reflkesubkomponen_id,
                                                                                                'reflkekomponen_id' => $model->reflkekomponen_id,
                                                                                            ], [
                                                                                                'class' => 'btn btn-danger btn-sm',
                                                                                                'title' => 'Delete',
                                                                                                'data' => [
                                                                                                    'confirm' => 'Are you sure you want to delete this item?',
                                                                                                    'method' => 'post',
                                                                                                ],
                                                                                            ]) ?>
                                                                                            <?= Html::button('<i class="fas fa-check"></i>', [
                                                                                                'class' => 'btn btn-success btn-sm mx-3',
                                                                                                'title' => 'Tambah Kriteria Sub Komponen',
                                                                                                'data-bs-toggle' => 'modal',
                                                                                                'data-bs-target' => '#createModalLkesubkriteria',
                                                                                                'data-reflkekomponen_id' => $model->reflkekomponen_id,
                                                                                                'data-reflkesubkomponen_id' => $subkomponenModel->reflkesubkomponen_id,
                                                                                            ]) ?>
                                                                                            <?= Html::encode($subkomponenModel->uraian_lkesubkomponen) ?></td>
                                                                                        <td><?= Html::encode($subkomponenModel->bobot_lkesubkomponen) ?></td>

                                                                                    </tr>
                                                                                <?php endforeach; ?>
                                                                            </tbody>
                                                                        </table>
                                                                    </div>
                                                                <?php
                                                                } else {
                                                                    echo '<p class="text-muted">Tidak ada sub komponen untuk komponen ini.</p>';
                                                                }
                                                                ?>
                                                                <!-- Modal untuk Detail Kriteria -->
                                                                <?php foreach ($subkomponenModels as $subkomponenModel) : ?>
                                                                    <div class="modal fade" id="modalKriteria-<?= $subkomponenModel->reflkesubkomponen_id ?>" tabindex="-1" aria-labelledby="modalKriteriaLabel-<?= $subkomponenModel->reflkesubkomponen_id ?>" aria-hidden="true" data-bs-backdrop="static" data-bs-focus="false">
                                                                        <div class="modal-dialog modal-lg">
                                                                            <div class="modal-content">
                                                                                <div class="modal-header">
                                                                                    <h5 class="modal-title" id="modalKriteriaLabel-<?= $subkomponenModel->reflkesubkomponen_id ?>">Detail Kriteria: <?= Html::encode($subkomponenModel->uraian_lkesubkomponen) ?></h5>
                                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                                </div>
                                                                                <div class="modal-body">
                                                                                    <?php
                                                                                    $subkriteriaModels = SakipLkesubkriteria::findAll(['reflkesubkomponen_id' => $subkomponenModel->reflkesubkomponen_id]);
                                                                                    if (!empty($subkriteriaModels)) {
                                                                                    ?>
                                                                                        <div class="table-responsive">
                                                                                            <table class="table table-bordered table-striped" style="font-size:small;">
                                                                                                <thead>
                                                                                                    <tr>
                                                                                                        <th>No</th>
                                                                                                        <th>Uraian Kriteria</th>
                                                                                                        <th>Aksi</th>
                                                                                                    </tr>
                                                                                                </thead>
                                                                                                <tbody>
                                                                                                    <?php $no = 1; ?>
                                                                                                    <?php foreach ($subkriteriaModels as $subkriteriaModel) : ?>
                                                                                                        <tr>
                                                                                                            <td><?= Html::encode($no++) ?></td>
                                                                                                            <td style="white-space: normal;"><?= Html::encode($subkriteriaModel->uraian_lkesubkriteria) ?></td>
                                                                                                            <td>
                                                                                                                <?= Html::button('<i class="fas fa-edit"></i>', [
                                                                                                                    'class' => 'btn btn-success btn-sm',
                                                                                                                    'title' => 'Update',
                                                                                                                    'data-bs-toggle' => 'modal',
                                                                                                                    'data-bs-target' => '#updateModalLkesubkriteria',
                                                                                                                    'data-bs-backdrop' => 'static',
                                                                                                                    'data-bs-focus' => 'false',
                                                                                                                    'data-url' => Url::to(['sakip-lkesubkriteria/update', 'reflkesubkriteria_id' => $subkriteriaModel->reflkesubkriteria_id])
                                                                                                                ]) ?>

                                                                                                                <?= Html::a('<i class="fas fa-trash-alt"></i>', [
                                                                                                                    'sakip-lkesubkomponen/delete',
                                                                                                                    'reflkesubkomponen_id' => $subkomponenModel->reflkesubkomponen_id,
                                                                                                                    'reflkekomponen_id' => $model->reflkekomponen_id,
                                                                                                                ], [
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
                                                                                                </tbody>
                                                                                            </table>
                                                                                        </div>
                                                                                    <?php
                                                                                    } else {
                                                                                        echo '<p class="text-muted">Tidak ada Kriteria untuk Sub Komponen ini.</p>';
                                                                                    }
                                                                                    ?>
                                                                                </div>
                                                                                <div class="modal-footer">
                                                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                <?php endforeach; ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- End Accordion -->
                                            </td>

                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>


                        <div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="createModalLabel">Tambah Data SAKIP LKE</h5>
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
                                        <h5 class="modal-title" id="updateModalLabel">Update Data SAKIP LKE</h5>
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
                        <div class="modal fade" id="createModalLkesubkomponen" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="createModalLabel">Tambah Data SAKIP LKE Sub Komponen</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <!-- The form will be loaded here -->
                                        <div id="modalFormContentLkesubkomponen">
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
                        <div class="modal fade" id="updateModalLkesubkomponen" tabindex="-1" aria-labelledby="updateModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="updateModalLabel">Update Data SAKIP LKE Sub Komponen</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <!-- The form will be loaded here -->
                                        <div id="modalUpdateFormContentLkesubkomponen">
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
                        <div class="modal fade" id="createModalLkesubkriteria" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="createModalLabel">Tambah Data SAKIP LKE Kriteria</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <!-- The form will be loaded here -->
                                        <div id="modalFormContentLkesubkriteria">
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
                        <div class="modal fade" id="updateModalLkesubkriteria" tabindex="-1" aria-labelledby="updateModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-focus="false">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="updateModalLabel">Update Data SAKIP LKE Kriteria</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <!-- The form will be loaded here -->
                                        <div id="modalUpdateFormContentLkesubkriteria">
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
            </div>
            <!-- Base style - Hover table end -->
        </div>
        <!-- [ Main Content ] end -->
    </div>
</div>