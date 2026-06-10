<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var backend\models\SakipPegawaibappeda $model */

$this->title = 'View Data SAKIP Pegawai Bappeda - ' . $model->nama_pegawai;
$this->params['breadcrumbs'][] = ['label' => 'Data Bidang', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
$this->registerJs("
    $('#createModal').on('show.bs.modal', function (event) {
        var modal = $(this);
        $.ajax({
            url: '" . Url::to(['sakip-pegawaibappeda/create']) . "',
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
$('#createModalPenanggungJawab').on('show.bs.modal', function (event) {
    var modal = $(this);
    var button = $(event.relatedTarget);  // Button that triggered the modal
    var refpegawai_id = button.data('refpegawai_id');  // Extract value from data-* attributes
    var refbidangbappeda_id = button.data('refbidangbappeda_id');  // Extract value from data-* attributes

    // Set the hidden inputs in the modal with the correct values
    modal.find('#refpegawai_id').val(refpegawai_id);
    modal.find('#refbidangbappeda_id').val(refbidangbappeda_id);

    // Send the data to load the form
    $.ajax({
        url: '" . Url::to(['sakip-penanggungjawab/create']) . "',
        type: 'GET',
        data: { refpegawai_id: refpegawai_id, refbidangbappeda_id: refbidangbappeda_id }, // Include the data
        success: function(data) {
            modal.find('#modalFormContentPenanggungJawab').html(data);
        }
    });
});
");

$this->registerJs("
$('#updateModalPenanggungJawab').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget); // Button that triggered the modal
    var url = button.data('url'); // Extract info from data-url attributes

    var modal = $(this);
    $.ajax({
        url: url,
        type: 'GET',
        success: function(data) {
            modal.find('#modalUpdateFormContentPenanggungJawab').html(data);
        }
    });
});
");
?>

<!-- [ Main Content ] start -->
<div class="pc-container">
    <div class="pc-content">
        <!-- [ breadcrumb ] start -->
        <div class="page-header">
            <div class="page-block">
                <div class="row align-items-center">
                    <div class="col-md-12">
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?= Url::to(['/site/index']) ?>">Home</a></li>
                            <li class="breadcrumb-item"><a href="<?= Url::to(['/sakip-pegawaibappeda/index']) ?>">Data SAKIP Pegawai Bappeda</a></li>
                            <li class="breadcrumb-item" aria-current="page">View Data SAKIP Pegawai Bappeda</li>
                        </ul>
                    </div>
                    <div class="col-md-12">
                        <div class="page-header-title">
                            <h2 class="mb-0">View Data SAKIP Pegawai Bappeda</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- [ breadcrumb ] end -->


        <!-- [ Main Content ] start -->
        <div class="row">
            <!-- [ form-element ] start -->
            <div class="col-lg-12">
                <!-- Basic Inputs -->
                <div class="card">
                    <div class="card-body">
                        <h1>View Data SAKIP Pegawai Bappeda - <?= Html::encode($model->refpegawai_id) ?></h1>

                        <p>
                            <?= Html::button('Update', [
                                'class' => 'btn btn-primary',
                                'title' => 'Update',
                                'data-bs-toggle' => 'modal',
                                'data-bs-target' => '#updateModal',
                                'data-url' => Url::to(['update', 'refpegawai_id' => $model->refpegawai_id])
                            ]) ?>
                            <?= Html::a('Delete', ['delete', 'refpegawai_id' => $model->refpegawai_id], [
                                'class' => 'btn btn-danger',
                                'data' => [
                                    'confirm' => 'Are you sure you want to delete this item?',
                                    'method' => 'post',
                                ],
                            ]) ?>
                            <?= Html::button('<i class="fa fa-plus"></i>', [
                                'class' => 'btn btn-success',
                                'data-bs-toggle' => 'modal',
                                'data-bs-target' => '#createModal',
                            ]) ?>
                        </p>


                        <?= DetailView::widget([
                            'model' => $model,
                            'attributes' => [
                                'refpegawai_id',
                                [
                                    'attribute' => 'statusAparatur',
                                    'filter' => ['1' => 'ASN', '2' => 'Non ASN'],
                                    'label' => 'status',
                                    'value' => function ($model) {
                                        return ($model->statusAparatur == 1) ? 'ASN' : 'Non ASN';
                                    }
                                ],
                                'nama_pegawai:ntext',
                                [
                                    'attribute' => 'nip',
                                    'label' => 'NIP',
                                    'value' => function ($model) {
                                        return $model->nip ? $model->nip : 'Tidak ada NIP';
                                    },
                                ],
                                [
                                    'attribute' => 'refeselon_id',
                                    'label' => 'Nama Eselon',
                                    'value' => function ($model) {
                                        return $model->refEselon ? $model->refEselon->title_eselon : 'Tidak ada Data Eselon';
                                    },
                                ],
                                [
                                    'attribute' => 'reftitle_id',
                                    'label' => 'Nama Title',
                                    'value' => function ($model) {
                                        return $model->refTitle ? $model->refTitle->nama_title : 'Tidak ada Data Title';
                                    },
                                ],
                                [
                                    'attribute' => 'refbidangbappeda_id',
                                    'label' => 'Nama Bidang Bappeda',
                                    'value' => function ($model) {
                                        return $model->refBidangbappeda ? $model->refBidangbappeda->nama_bidangbappeda : 'Tidak ada Data Bidang Bappeda';
                                    },
                                ],
                                [
                                    'attribute' => 'no_hp',
                                    'label' => 'Nomor Handphone',
                                    'value' => function ($model) {
                                        return $model->no_hp ? $model->no_hp : 'Tidak ada Nomor Handphone';
                                    },
                                ],
                            ],
                        ]) ?>



                    </div>

                    <div class="row">
                        <div class="col-lg-12">
                            <!-- Start Card Penjabat SKPD -->
                            <div class="card mx-4">
                                <div class="card-body">
                                    <h1>Data Penanggung Jawab - <?= Html::encode($model->nama_pegawai) ?></h1>

                                    <!-- Check if there is no data for the selected period -->
                                    <?php if (empty($penanggungjawabData)): ?>
                                        <p><strong>Belum ada data untuk penanggung jawab ini.</strong></p>
                                        <?= Html::button('<i class="fas fa-plus"></i> Tambah Data Penanggung Jawab', [
                                            'class' => 'btn btn-success btn-sm mt-4',
                                            'data-bs-toggle' => 'modal',
                                            'data-bs-target' => '#createModalPenanggungJawab',
                                            'data-refpegawai_id' => $model->refpegawai_id,
                                            'data-refbidangbappeda_id' => $model->refbidangbappeda_id,
                                        ]) ?>
                                    <?php else: ?>
                                        <!-- Loop through all penjabat data based on refskpd_id and refperiode_id -->
                                        <div class="row">
                                            <?php foreach ($penanggungjawabData as $penanggungjawab) : ?>
                                                <div class="col-sm-11">
                                                    <div class="card">
                                                        <div class="card-header">
                                                            <h5><?= Html::encode($penanggungjawab->refPegawai->nama_pegawai) ?></h5> <!-- Displaying refperiode_id -->
                                                        </div>
                                                        <div class="card-body">
                                                            <p><strong>SKPD:</strong> <?= Html::encode($penanggungjawab->refSkpd->nama_skpd) ?></p>
                                                            <p><strong>Bidang Bappeda:</strong> <?= Html::encode($penanggungjawab->refBidangbappeda->nama_bidangbappeda ?? 'Bidang not available') ?></p>
                                                            <br>
                                                            <?= Html::button('<i class="fas fa-edit"></i>', [
                                                                'class' => 'btn btn-success btn-sm',
                                                                'title' => 'Update',
                                                                'data-bs-toggle' => 'modal',
                                                                'data-bs-target' => '#updateModalPenanggungJawab',
                                                                'data-url' => Url::to(['sakip-penanggungjawab/update', 'refpenanggungjawab_id' => $penanggungjawab->refpenanggungjawab_id])
                                                            ]) ?>
                                                        </div>
                                                        <?= Html::button('<i class="fas fa-plus"></i> Tambah Data Penanggung Jawab', [
                                                            'class' => 'btn btn-success btn-sm mt-4',
                                                            'data-bs-toggle' => 'modal',
                                                            'data-bs-target' => '#createModalPenanggungJawab',
                                                            'data-refpegawai_id' => $model->refpegawai_id,
                                                            'data-refbidangbappeda_id' => $model->refbidangbappeda_id,
                                                        ]) ?>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>


                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="createModalLabel">Tambah Data SAKIP Pegawai Bappeda</h5>
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
                            <h5 class="modal-title" id="updateModalLabel">Update Data SAKIP Pegawai Bappeda</h5>
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
            <div class="modal fade" id="createModalPenanggungJawab" tabindex="-1" aria-labelledby="createModalLabelPenanggungJawab" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="createModalLabelPenanggungJawab">Lengkapi Data Penanggung Jawab</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <!-- The form will be loaded here -->
                            <div id="modalFormContentPenanggungJawab">
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
            <div class="modal fade" id="updateModalPenanggungJawab" tabindex="-1" aria-labelledby="updateModalLabelPenanggungJawab" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="updateModalLabelPenanggungJawab">Update Data SAKIP Penanggung Jawab</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <!-- The form will be loaded here -->
                            <div id="modalUpdateFormContentPenanggungJawab">
                                <!-- AJAX-loaded content will be injected here -->
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>


            <!-- [ form-element ] end -->
        </div>
        <!-- [ Main Content ] end -->
    </div>
</div>