<!-- <style>
  .card-header {
    height: 120px;
  }

  .position-relative {
    position: relative;
  }

  .arrow-icon {
    position: absolute;
    right: -10px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 1.5em;
    color: #555;
  }

  /* Kustomisasi khusus untuk panah bawah pada card ke-4 */
  .arrow-icon-bottom {
    position: absolute;
    bottom: -20px;
    /* Jarak dari bawah card */
    left: 50%;
    transform: translateX(-50%);
    /* Rotasi panah ke bawah */
    font-size: 1.5em;
    color: #555;
  }

  .arrow-icon-bottom-2 {
    position: absolute;
    bottom: -20px;
    /* Jarak dari bawah card */
    right: 50%;
    transform: translateX(-50%);
    /* Rotasi panah ke bawah */
    font-size: 1.5em;
    color: #555;
  }
</style> -->
<?php

use yii\helpers\Url;
use yii\helpers\Html;
use frontend\models\SakipLke;
use frontend\models\SakipLkekomponen;
use frontend\models\SakipLkesubkomponen;
use frontend\models\SakipLkesubkriteria;

/** @var yii\web\View $this */

$this->title = 'Aplikasi ESAKIP';

$this->registerJs("
$('#createModal').on('show.bs.modal', function (event) {
    var modal = $(this);
    var button = $(event.relatedTarget);  // Button that triggered the modal
    var reflkekomponen_id = button.data('reflkekomponen_id');
    var reflkesubkomponen_id = button.data('reflkesubkomponen_id');
    var refperiode_id = button.data('refperiode_id');
    var refskpd_id = button.data('refskpd_id');

    // Set the hidden inputs in the modal with the correct values
    modal.find('#reflkekomponen_id').val(reflkekomponen_id);
    modal.find('#reflkesubkomponen_id').val(reflkesubkomponen_id);
    modal.find('#refperiode_id').val(refperiode_id);  // Assuming there's an input with this ID
    modal.find('#refskpd_id').val(refskpd_id);        // Assuming there's an input with this ID

    // Send the data to load the form
    $.ajax({
        url: '" . Url::to(['sakip-lke/create-dev']) . "',
        type: 'GET',
        data: {
            reflkekomponen_id: reflkekomponen_id,
            reflkesubkomponen_id: reflkesubkomponen_id,
            refperiode_id: refperiode_id,
            refskpd_id: refskpd_id
        },
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
                            <li class="breadcrumb-item"><a href="<?= Url::to(['/site/index-esakip']) ?>">Home</a></li>
                            <li class="breadcrumb-item" aria-current="page">Sakip LKE</li>
                        </ul>
                    </div>
                    <div class="col-md-12">
                        <div class="page-header-title">
                            <h2 class="mb-0">Sakip LKE</h2>
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
                            <i class="fas fa-pen-fancy"></i>Periode Lembar Kerja Evaluasi Gabungan - <?= Html::encode(ucwords(strtolower($nama_skpd))) ?>
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
                                            'prompt' => 'Semua Periode',
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
                            <i class="fas fa-pen-fancy"></i>Lembar Kerja Evaluasi Gabungan - <?= Html::encode(ucwords(strtolower($nama_skpd))) ?> (Periode <?= $selectedPeriodValue ?>)
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
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


                            <div class="dt-responsive table-responsive">
                                <table id="table-style-hover" class="table table-striped table-hover table-bordered nowrap" style="font-size:xx-small;">
                                    <thead>
                                        <tr>
                                            <th colspan="10">Lembar Kerja Evaluasi Gabungan</th>
                                        </tr>
                                        <tr>
                                            <th rowspan="2">No</th>
                                            <th rowspan="2" colspan="5">Komponen/Sub Komponen/Kriteria</th>
                                            <th rowspan="2">Bobot</th>
                                            <th colspan="2">Unit/Saker</th>
                                            <th rowspan="2">Aksi</th>
                                        </tr>
                                        <tr>
                                            <th>Jawaban</th>
                                            <th>Nilai</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $no = 1;
                                        foreach (SakipLkekomponen::find()->all() as $komponen):
                                            $totalBobot = SakipLkesubkomponen::find()
                                                ->where(['reflkekomponen_id' => $komponen->reflkekomponen_id])
                                                ->sum('bobot_lkesubkomponen');
                                        ?>
                                            <!-- Baris Komponen -->
                                            <tr>
                                                <td><?= $no++ ?></td>
                                                <td colspan="5" style="white-space: normal;"><?= Html::encode($komponen->uraian_lkekomponen) ?></td>
                                                <td><?= Html::encode($totalBobot) ?></td>
                                                <td colspan="2"></td>
                                                <td></td>
                                            </tr>
                                            <!-- Baris Sub Komponen -->
                                            <?php
                                            $subNo = 1;
                                            $subkomponenList = SakipLkesubkomponen::find()
                                                ->where(['reflkekomponen_id' => $komponen->reflkekomponen_id])
                                                ->all();
                                            foreach ($subkomponenList as $subkomponen):
                                                $jawaban = SakipLke::find()
                                                    ->where([
                                                        'reflkekomponen_id' => $komponen->reflkekomponen_id,
                                                        'reflkesubkomponen_id' => $subkomponen->reflkesubkomponen_id,
                                                        'refskpd_id' => $selectedSkpdId,
                                                        'refperiode_id' => $selectedPeriodId,
                                                    ])
                                                    ->select(['unit_jawaban', 'unit_nilai', 'reflke_id'])
                                                    ->one();
                                            ?>
                                                <tr>
                                                    <td></td>
                                                    <td><?= $no - 1 ?>.<?= $subNo++ ?></td>
                                                    <td colspan="4" style="white-space: normal;"><?= Html::encode($subkomponen->uraian_lkesubkomponen) ?></td>
                                                    <td><?= Html::encode($subkomponen->bobot_lkesubkomponen) ?></td>
                                                    <td><?= $jawaban ? Html::encode($jawaban->unit_jawaban) : '-' ?></td>
                                                    <td><?= $jawaban ? Html::encode($jawaban->unit_nilai) : '-' ?></td>
                                                    <td>
                                                        <?php if ($jawaban): ?>
                                                            <?= Html::button('<i class="fas fa-edit"></i> Update', [
                                                                'class' => 'btn btn-warning btn-sm',
                                                                'title' => 'Update',
                                                                'data-bs-toggle' => 'modal',
                                                                'data-bs-target' => '#updateModal',
                                                                'data-url' => Url::to(['update', 'reflke_id' => $jawaban->reflke_id, 'refperiode_id' => $selectedPeriodId, 'refskpd_id' => $selectedSkpdId]),
                                                            ]) ?>
                                                        <?php else: ?>
                                                            <?= Html::button('<i class="fas fa-check"></i> Isi', [
                                                                'class' => 'btn btn-success btn-sm',
                                                                'title' => 'Isi Data LKE',
                                                                'data-bs-toggle' => 'modal',
                                                                'data-bs-target' => '#createModal',
                                                                'data-reflkekomponen_id' => $komponen->reflkekomponen_id,
                                                                'data-reflkesubkomponen_id' => $subkomponen->reflkesubkomponen_id,
                                                                'data-refperiode_id' => $selectedPeriodId,
                                                                'data-refskpd_id' => $selectedSkpdId,
                                                            ]) ?>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                                <!-- Baris Kriteria -->
                                                <?php
                                                $kriteriaNo = 1;
                                                $kriteriaList = SakipLkesubkriteria::find()
                                                    ->where(['reflkesubkomponen_id' => $subkomponen->reflkesubkomponen_id])
                                                    ->all();
                                                foreach ($kriteriaList as $kriteria):
                                                ?>
                                                    <tr>
                                                        <td></td>
                                                        <td></td>
                                                        <td><?= $no - 1 ?>.<?= $subNo - 1 ?>.<?= $kriteriaNo++ ?></td>
                                                        <td colspan="3" style="white-space: normal;"><?= Html::encode($kriteria->uraian_lkesubkriteria) ?></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php endforeach; ?>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>


                        </div>
                        <!--  -->
                        <div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content" style="border-radius: 20px;">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="createModalLabel">Isi Data LKE</h5>
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
                                        <h5 class="modal-title" id="updateModalLabel">Update Data LKE</h5>
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