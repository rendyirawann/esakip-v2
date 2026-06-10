<style>
    #modalUpdateFormContent {
        padding-top: 20px;
        padding-right: 15px;
        padding-bottom: 20px;
        padding-left: 15px;
    }
</style>
<?php

use frontend\models\SakipSasaranrenstra;
use frontend\models\SakipTujuanrenstra;
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

/** @var yii\web\View $this */
/** @var frontend\models\search\SakipSasaranrenstraSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'e-Sakip - Data Sasaran Renstra';
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
        url: '" . Url::to(['sakip-sasaranrenstra/create']) . "',
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
$('#updateModalTujuanRenstra').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget); // Button that triggered the modal
    var url = button.data('url'); // Extract info from data-url attributes

    var modal = $(this);
    $.ajax({
        url: url,
        type: 'GET',
        success: function(data) {
            modal.find('#modalUpdateFormContentTujuanRenstra').html(data);
        }
    });
});
");


$this->registerJs("
$('#createModalIndikatorSasaranRenstra').on('show.bs.modal', function (event) {
    var modal = $(this);
    var button = $(event.relatedTarget);  // Button that triggered the modal
    var refperiode_id = button.data('refperiode_id');  // Extract value from data-* attributes
    var refsasaranrenstra_id = button.data('refsasaranrenstra_id');  // Extract value from data-* attributes

    // Set the hidden inputs in the modal with the correct values
    modal.find('#refperiode_id').val(refperiode_id);
    modal.find('#refsasaranrenstra_id').val(refsasaranrenstra_id);

    // Send the data to load the form
    $.ajax({
        url: '" . Url::to(['sakip-indikatorsasaranrenstra/create']) . "',
        type: 'GET',
        data: { refperiode_id: refperiode_id, refsasaranrenstra_id: refsasaranrenstra_id }, // Include the data
        success: function(data) {
            modal.find('#modalFormContentIndikatorSasaranRenstra').html(data);
        }
    });
});
");

$this->registerJs("
$('#updateModalIndikator').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget); // Button that triggered the modal
    var url = button.data('url'); // Extract info from data-url attributes

    var modal = $(this);
    $.ajax({
        url: url,
        type: 'GET',
        success: function(data) {
            modal.find('#modalUpdateFormContentIndikator').html(data);
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
                            <li class="breadcrumb-item" aria-current="page">Sasaran Renstra</li>
                        </ul>
                    </div>
                    <div class="col-md-12">
                        <div class="page-header-title">
                            <h2 class="mb-0">Sasaran Renstra</h2>
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
                            <i class="fas fa-pen-fancy"></i>Periode Sasaran Renstra - <?= Html::encode(ucwords(strtolower($nama_skpd))) ?>
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
                            <i class="fas fa-pen-fancy"></i> Data SAKIP Sasaran Renstra - <?= Html::encode(ucwords(strtolower($nama_skpd))) ?> (Periode <?= $selectedPeriodValue ?>)
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


                        <!-- Tampilkan alert jika tidak ada data -->
                        <?php if (empty($sasaranRenstra)): ?>
                            <div class="alert alert-warning mt-4">
                                Belum Ada Data untuk SKPD / Periode yang dipilih.
                            </div>
                        <?php else: ?>
                            <div class="dt-responsive table-responsive">
                                <?php
                                $no = 0; // Inisialisasi variabel penomoran
                                $currentTujuanId = null; // Variabel untuk melacak tujuan saat ini
                                $currentMisiId = null; // Variabel untuk melacak misi saat ini

                                foreach ($sasaranRenstra as $model) {
                                    $tujuanId = $model->sasaran->reftujuan_id; // Dapatkan reftujuan_id dari relasi sasaran
                                    $misiId = $model->sasaran->refTujuan->refmisi_id; // Dapatkan refmisi_id dari relasi tujuan
                                    $uraianTujuanRenstra = SakipTujuanrenstra::find()
                                        ->select('uraian_tujuanrenstra')
                                        ->where(['reftujuanrenstra_id' => $model->reftujuanrenstra_id]) // Ambil dari reftujuanrenstra_id
                                        ->scalar(); // Ambil nilai scalar

                                    // Cek apakah tujuan dan misi saat ini berbeda dari sebelumnya
                                    if ($tujuanId !== $currentTujuanId || $misiId !== $currentMisiId) {
                                        // Jika berbeda, tutup tabel sebelumnya (jika ada)
                                        if ($currentTujuanId !== null) {
                                            echo '</tbody></table>';
                                        }

                                        // Tampilkan tabel baru untuk tujuan baru
                                        echo '<table id="table-style-hover" class="table table-striped table-hover table-bordered nowrap" style="font-size:xx-small;">';
                                        echo '<thead>';
                                        if ($isAdmin) {
                                            echo '<tr>
                <th style="background-color: #04A9F5; color: white;">Tujuan</th>
                <th style="background-color: #04A9F5; color: white; white-space: normal;" colspan="7">' . Html::decode($model->sasaran->refTujuan->uraian_tujuan) . '</th>
            </tr>';
                                            echo '<tr>
                <th style="background-color: #1DE9B6; color: white;">No</th>
                <th style="background-color: #1DE9B6; color: white;">Indikator Sasaran</th>
                <th style="background-color: #1DE9B6; color: white;">Aksi</th>
                <th style="background-color: #1DE9B6; color: white;">Satuan</th>
                <th style="background-color: #1DE9B6; color: white;">Poin</th>
                <th style="background-color: #1DE9B6; color: white;">Status</th>
                <th style="background-color: #1DE9B6; color: white;">Status IKU</th>
                <th style="background-color: #1DE9B6; color: white;">Status PK</th>
            </tr>';
                                        } else {
                                            echo '<tr>
                <th style="background-color: #04A9F5; color: white;">Tujuan</th>
                <th style="background-color: #04A9F5; color: white; white-space: normal;" colspan="6">' . Html::decode($model->sasaran->refTujuan->uraian_tujuan) . '</th>
            </tr>';
                                            echo '<tr>
                <th style="background-color: #1DE9B6; color: white;">No</th>
                <th style="background-color: #1DE9B6; color: white;">Indikator Sasaran</th>
                <th style="background-color: #1DE9B6; color: white;">Satuan</th>
                <th style="background-color: #1DE9B6; color: white;">Poin</th>
                <th style="background-color: #1DE9B6; color: white;">Status</th>
                <th style="background-color: #1DE9B6; color: white;">Status IKU</th>
                <th style="background-color: #1DE9B6; color: white;">Status PK</th>
            </tr>';
                                        }
                                        echo '</thead>';
                                        echo '<tbody>';

                                        $currentTujuanId = $tujuanId; // Update tujuan saat ini
                                        $currentMisiId = $misiId; // Update misi saat ini
                                        $no = 0; // Reset penomoran untuk tabel baru
                                    }

                                    // Tampilkan Sasaran Renstra terkait tujuan
                                    echo '<tr>';
                                    echo '<td>' . ++$no . '</td>'; // Nomor untuk sasaran
                                    echo '<td style="white-space: normal;">' . 'Sasaran: ' . Html::decode($model->uraian_sasaranrenstra) . '</td>';
                                    if ($isAdmin) {
                                        echo '<td>' .
                                            Html::button('<i class="fas fa-edit"></i>', [
                                                'class' => 'btn btn-success btn-sm mx-2',
                                                'title' => 'Update',
                                                'data-bs-toggle' => 'modal',
                                                'data-bs-target' => '#updateModal',
                                                'data-url' => Url::to(['update', 'refsasaranrenstra_id' => $model->refsasaranrenstra_id])
                                            ]) .
                                            Html::button('<i class="fas fa-check"></i>', [
                                                'class' => 'btn btn-success btn-sm mx-2',
                                                'title' => 'Pilih Tujuan Sasaran Renstra',
                                                'data-bs-toggle' => 'modal',
                                                'data-bs-target' => '#updateModalTujuanRenstra',
                                                'data-url' => Url::to(['update-tujuanrenstra', 'refsasaranrenstra_id' => $model->refsasaranrenstra_id])
                                            ]) .
                                            '</td>';
                                        echo '<td colspan="5"></td>';
                                    } else {
                                        echo '<td colspan="5"></td>';
                                    }
                                    echo '</tr>';

                                    // Tampilkan tujuan di bawah sasaran
                                    echo '<tr>';
                                    echo '<td></td>'; // Nomor kosong untuk tujuan
                                    if (empty($model->reftujuanrenstra_id)) {
                                        if ($isAdmin) {
                                            echo '<td colspan="7">Tujuan Renstra Belum Di Set</td>';
                                        } else {
                                            echo '<td colspan="6">Tujuan Renstra Belum Di Set</td>';
                                        }
                                    } else {
                                        if ($isAdmin) {
                                            echo '<td colspan="7"><b>' . 'Tujuan: ' . Html::decode($uraianTujuanRenstra) . '</b></td>';
                                        } else {
                                            echo '<td colspan="6"><b>' . 'Tujuan: ' . Html::decode($uraianTujuanRenstra) . '</b></td>';
                                        }
                                    }
                                    echo '</tr>';

                                    // Tampilkan Indikator terkait sasaran renstra
                                    foreach ($model->indikators as $indikator) {
                                        echo '<tr>';
                                        echo '<td></td>'; // Nomor kosong untuk indikator
                                        echo '<td style="white-space: normal;">' . 'Indikator: ' . Html::decode($indikator->uraian_indikatorsasaranrenstra) . '</td>';
                                        if ($isAdmin) {
                                            echo '<td>' .
                                                Html::button('<i class="fas fa-edit"></i>', [
                                                    'class' => 'btn btn-success btn-sm mx-2',
                                                    'title' => 'Update Indikator',
                                                    'data-bs-toggle' => 'modal',
                                                    'data-bs-target' => '#updateModalIndikator',
                                                    'data-url' => Url::to(['sakip-indikatorsasaranrenstra/update', 'refindikatorsasaranrenstra_id' => $indikator->refindikatorsasaranrenstra_id])
                                                ]) .
                                                '</td>';
                                        }
                                        echo '<td>' . Html::decode($indikator->indikatorsasaranrenstra_satuan) . '</td>';
                                        echo '<td>' . Html::decode($indikator->indikatorsasaranrenstra_target) . '</td>';
                                        echo '<td class="text-center align-middle">' . ($indikator->indikatorsasaranrenstra_isaktif === 'T'
                                            ? '<span class="badge bg-success">AKTIF</span>'
                                            : '<span class="badge bg-alert">Non Aktif</span>') . '</td>';

                                        echo '<td class="text-center align-middle">' . ($indikator->iku_isaktif === 'T'
                                            ? '<span class="badge bg-success">AKTIF</span>'
                                            : '<span class="badge bg-alert">Non Aktif</span>') . '</td>';

                                        echo '<td class="text-center align-middle">' . ($indikator->pk_isaktif === 'T'
                                            ? '<span class="badge bg-success">AKTIF</span>'
                                            : '<span class="badge bg-alert">Non Aktif</span>') . '</td>';
                                        echo '</tr>';
                                    }
                                }

                                // Tutup tabel terakhir
                                if ($currentTujuanId !== null) {
                                    echo '</tbody></table>';
                                }
                                ?>
                            </div>



                        <?php endif; ?>

                        <!--  -->
                        <div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content" style="border-radius: 20px;">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="createModalLabel">Tambah Data SAKIP Sasaran Renstra</h5>
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
                                        <h5 class="modal-title" id="updateModalLabel">Update Data SAKIP Sasaran Renstra</h5>
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
                        <div class="modal fade" id="updateModalTujuanRenstra" tabindex="-1" aria-labelledby="updateModalTujuanRenstraLabel" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content" style="border-radius: 20px;">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="updateModalTujuanRenstraLabel">Pilih Data SAKIP Tujuan Renstra</h5>
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
                        <div class="modal fade" id="createModalIndikatorSasaranRenstra" tabindex="-1" aria-labelledby="createModalLabelIndikatorSasaranRenstra" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content" style="border-radius: 20px;">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="createModalLabelIndikatorSasaranRenstra">Tambah Data SAKIP Indikator Sasaran Renstra</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <!-- The form will be loaded here -->
                                        <div id="modalFormContentIndikatorSasaranRenstra" style="padding-bottom:20px; padding-right:15px; padding-left:15px;">
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
                        <div class="modal fade" id="updateModalIndikator" tabindex="-1" aria-labelledby="updateModalLabelIndikator" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content" style="border-radius: 20px;">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="updateModalLabelIndikator">Update Data SAKIP Indikator Sasaran Renstra</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <!-- The form will be loaded here -->
                                        <div id="modalUpdateFormContentIndikator" style="padding-bottom:20px; padding-right:15px; padding-left:15px;">
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