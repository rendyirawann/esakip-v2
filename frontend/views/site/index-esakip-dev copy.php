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
        $.ajax({
            url: '" . Url::to(['site/create']) . "',
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
              <li class="breadcrumb-item"><a href="<?= Url::to(['/site/index-esakip']) ?>">Home</a></li>
              <li class="breadcrumb-item" aria-current="page">Dashboard</li>
            </ul>
          </div>
          <div class="col-md-12">
            <div class="page-header-title">
              <h2 class="mb-0">Dashboard</h2>
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
                <?= \yii\helpers\Html::beginForm(['index-esakip'], 'get', ['class' => 'form-inline']); ?>
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
                      <th colspan="8">Lembar Kerja Evaluasi Gabungan</th>
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
                    <?php $noKomponen = 1; ?>
                    <?php foreach ($lkekomponenModels as $komponen): ?>
                      <tr>
                        <td><?= Html::encode($noKomponen++) ?></td>
                        <td colspan="5"><?= Html::encode($komponen->uraian_lkekomponen) ?></td>
                        <td>
                          <?= Html::encode(
                            SakipLkesubkomponen::find()
                              ->where(['reflkekomponen_id' => $komponen->reflkekomponen_id])
                              ->sum('bobot_lkesubkomponen')
                          ) ?>
                        </td>
                        <td colspan="2"></td>
                        <td></td>
                      </tr>
                      <?php
                      $subKomponenModels = SakipLkesubkomponen::findAll(['reflkekomponen_id' => $komponen->reflkekomponen_id]);
                      $noSubKomponen = 1;
                      ?>
                      <?php foreach ($subKomponenModels as $subKomponen): ?>
                        <tr>
                          <td></td>
                          <td><?= Html::encode($noKomponen - 1) . '.' . $noSubKomponen++ ?></td>
                          <td colspan="4"><?= Html::encode($subKomponen->uraian_lkesubkomponen) ?></td>
                          <td><?= Html::encode($subKomponen->bobot_lkesubkomponen) ?></td>
                          <td>
                            <?= Html::encode(
                              SakipLke::findOne([
                                'reflkesubkomponen_id' => $subKomponen->reflkesubkomponen_id
                              ])->unit_jawaban ?? '-'
                            ) ?>
                          </td>
                          <td>
                            <?= Html::encode(
                              SakipLke::findOne([
                                'reflkesubkomponen_id' => $subKomponen->reflkesubkomponen_id
                              ])->unit_nilai ?? '-'
                            ) ?>
                          </td>
                          <td>
                            <?= Html::a('Tambah', ['sakip-lke/create', 'reflkesubkomponen_id' => $subKomponen->reflkesubkomponen_id], [
                              'class' => 'btn btn-primary btn-sm'
                            ]) ?>
                          </td>
                        </tr>
                        <?php
                        $subKriteriaModels = SakipLkesubkriteria::findAll(['reflkesubkomponen_id' => $subKomponen->reflkesubkomponen_id]);
                        $noKriteria = 1;
                        ?>
                        <?php foreach ($subKriteriaModels as $kriteria): ?>
                          <tr>
                            <td></td>
                            <td></td>
                            <td><?= Html::encode($noKomponen - 1) . '.' . ($noSubKomponen - 1) . '.' . $noKriteria++ ?></td>
                            <td colspan="3"><?= Html::encode($kriteria->uraian_lkesubkriteria) ?></td>
                            <td></td>
                            <td colspan="3"></td>
                          </tr>
                        <?php endforeach; ?>
                      <?php endforeach; ?>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>

              <div class="col-lg-12 text-center">
                <h1>Dashboard Aplikasi eSakip</h1>
                <img src="<?= Url::base(true) ?>/lightapp/assets/images/bappeda.png" alt="" width="auto">
                <h3>Bappedalitbang Deli Sedang</h3>
              </div>
            </div>
            <!--  -->
            <div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
              <div class="modal-dialog modal-lg">
                <div class="modal-content" style="border-radius: 20px;">
                  <div class="modal-header">
                    <h5 class="modal-title" id="createModalLabel">Tambah Data SAKIP Cascading Kegiatan</h5>
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
                    <h5 class="modal-title" id="updateModalLabel">Update Data SAKIP Cascading Kegiatan</h5>
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