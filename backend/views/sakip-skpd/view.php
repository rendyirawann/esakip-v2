<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var backend\models\SakipSkpd $model */

$this->title = 'View Data SAKIP SKPD - ' . $model->nama_skpd;
$this->params['breadcrumbs'][] = ['label' => 'Data SKPD', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
$this->registerJs("
    $('#createModal').on('show.bs.modal', function (event) {
        var modal = $(this);
        $.ajax({
            url: '" . Url::to(['sakip-skpd/create']) . "',
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
$('#createModalPenjabatSkpd').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget); // Button that triggered the modal
    var refskpdId = button.data('refskpd-id'); // Extract refskpd_id from data-* attribute
    var modal = $(this);

    $.ajax({
        url: '" . Url::to(['sakip-penjabat-skpd/create']) . "',
        type: 'GET',
        data: { refskpd_id: refskpdId }, // Pass refskpd_id as data
        success: function(data) {
            modal.find('#modalFormContentPenjabatSkpd').html(data);
        }
    });
});
");

$this->registerJs("
$('#updateModalPenjabatSkpd').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget); // Button that triggered the modal
    var url = button.data('url'); // Extract info from data-url attributes

    var modal = $(this);
    $.ajax({
        url: url,
        type: 'GET',
        success: function(data) {
            modal.find('#modalUpdateFormContentPenjabatSkpd').html(data);
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
              <li class="breadcrumb-item"><a href="<?= Url::to(['/sakip-skpd/index']) ?>">Data SAKIP SKPD</a></li>
              <li class="breadcrumb-item" aria-current="page">View Data SAKIP SKPD</li>
            </ul>
          </div>
          <div class="col-md-12">
            <div class="page-header-title">
              <h2 class="mb-0">View Data SAKIP SKPD</h2>
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
            <h1>View Data SAKIP SKPD - <?= Html::encode($model->refskpd_id) ?></h1>

            <p>
              <?= Html::button('Update', [
                'class' => 'btn btn-primary',
                'title' => 'Update',
                'data-bs-toggle' => 'modal',
                'data-bs-target' => '#updateModal',
                'data-url' => Url::to(['update', 'refskpd_id' => $model->refskpd_id])
              ]) ?>
              <?= Html::a('Delete', ['delete', 'refskpd_id' => $model->refskpd_id], [
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
                'refskpd_id',
                'kode_skpd',
                [
                  'attribute' => 'nama_skpd',
                  'format' => 'ntext', // Menampilkan teks dalam format paragraf
                  'contentOptions' => [
                    'style' => 'white-space: pre-wrap;', // Mengizinkan word wrap
                  ],
                ],
                'kepala_skpd:ntext',
                'nip_kepala',
                'jabatan_kepala',
                'pangkat_kepala',
                [
                  'attribute' => 'refurusan_id',
                  'label' => 'Nama Urusan',
                  'value' => function ($model) {
                    return $model->urusan ? $model->urusan->nama_urusan : 'Tidak ada urusan';
                  },
                ],
                [
                  'attribute' => 'refbidang_id',
                  'label' => 'Nama Bidang',
                  'value' => function ($model) {
                    return $model->bidang ? $model->bidang->nama_bidang : 'Tidak ada Bidang';
                  },
                ],
                [
                  'attribute' => 'refskpd_unit',
                  'format' => 'raw', // Mengizinkan HTML rendering
                  'value' => function ($model) {
                    if ($model->refskpd_unit === 'I') {
                      return Html::tag('span', 'Instansi', ['class' => 'btn btn-success']);
                    } else if ($model->refskpd_unit === 'U') {
                      return Html::tag('span', 'Utama', ['class' => 'btn btn-warning']);
                    } else if ($model->refskpd_unit === 'P') {
                      return Html::tag('span', 'Pendukung', ['class' => 'btn btn-warning']);
                    } else if ($model->refskpd_unit === 'T') {
                      return Html::tag('span', 'Tambahan', ['class' => 'btn btn-warning']);
                    }
                  },
                ],
                'refskpd_keterangan',
                [
                  'attribute' => 'skpd_isaktif',
                  'format' => 'raw', // Mengizinkan HTML rendering
                  'value' => function ($model) {
                    if ($model->skpd_isaktif === 'T') {
                      return Html::tag('span', 'Aktif', ['class' => 'btn btn-success']);
                    } else if ($model->skpd_isaktif === 'F') {
                      return Html::tag('span', 'Tidak Aktif', ['class' => 'btn btn-warning']);
                    }
                  },
                ],
              ],
            ]) ?>
          </div>
          <!-- start wor penjabat skpd -->
          <!-- Start Penjabat SKPD Section -->
          <div class="row">

            <div class="col-lg-6">
              <!-- Dropdown for selecting period -->
              <?= Html::beginForm(['view', 'refskpd_id' => $model->refskpd_id], 'get') ?>
              <div class="form-group mx-4">
                <?= Html::label('Select Period', 'refperiode_id') ?>
                <?= Html::dropDownList(
                  'refperiode_id',
                  $selectedPeriodId,
                  yii\helpers\ArrayHelper::map($periodeList, 'refperiode_id', 'periode'),
                  ['class' => 'form-control', 'prompt' => 'Select a Period', 'onchange' => 'this.form.submit()']
                ) ?>
              </div>
              <?= Html::endForm() ?>
            </div>

          </div>

          <div class="row">
            <div class="col-lg-12">
              <!-- Start Card Penjabat SKPD -->
              <div class="card mx-4">
                <div class="card-body">
                  <h1>Data Penjabat SKPD - <?= Html::encode($model->nama_skpd) ?></h1>
                  <?= Html::button('<i class="fas fa-plus"></i> Tambah Data Penjabat', [
                    'class' => 'btn btn-success btn mb-4',
                    'data-bs-toggle' => 'modal',
                    'data-bs-target' => '#createModalPenjabatSkpd',
                    'data-refskpd-id' => $model->refskpd_id,
                  ]) ?>
                  <!-- Check if there is no data for the selected period -->
                  <?php if (empty($penjabatData)): ?>
                    <p><strong>Belum ada data penjabat untuk periode ini.</strong></p>
                    <?= Html::button('<i class="fas fa-plus"></i> Tambah Data Penjabat', [
                      'class' => 'btn btn-success btn-sm mt-4',
                      'data-bs-toggle' => 'modal',
                      'data-bs-target' => '#createModalPenjabatSkpd',
                      'data-refskpd-id' => $model->refskpd_id,
                    ]) ?>
                  <?php else: ?>
                    <!-- Loop through all penjabat data based on refskpd_id and refperiode_id -->
                    <div class="row">
                      <?php foreach ($penjabatData as $penjabat) : ?>
                        <div class="col-sm-11">
                          <div class="card">
                            <div class="card-header">
                              <h5><?= Html::encode($penjabat->refPeriode->periode) ?></h5> <!-- Displaying refperiode_id -->
                            </div>
                            <div class="card-body">
                              <p><strong>Nama Penjabat:</strong> <?= Html::encode($penjabat->nama_penjabat) ?></p>
                              <p><strong>NIP Penjabat:</strong> <?= Html::encode($penjabat->nip_penjabat) ?></p>
                              <p><strong>Jabatan Eselon:</strong> <?= Html::encode($penjabat->jabatan_eselon) ?></p>
                              <p><strong>Pangkat Eselon:</strong> <?= Html::encode($penjabat->pangkat_eselon) ?></p>
                              <p><strong>Eselon:</strong> <?= Html::encode($penjabat->refEselon ? $penjabat->refEselon->title_eselon : '') ?></p>
                              <br>
                              <?= Html::button('<i class="fas fa-edit"></i> Edit Data', [
                                'class' => 'btn btn-success btn-sm',
                                'title' => 'Update',
                                'data-bs-toggle' => 'modal',
                                'data-bs-target' => '#updateModalPenjabatSkpd',
                                'data-url' => Url::to(['sakip-penjabat-skpd/update', 'refpenjabatskpd_id' => $penjabat->refpenjabatskpd_id])
                              ]) ?>
                            </div>

                          </div>
                        </div>
                      <?php endforeach; ?>
                      <?= Html::button('<i class="fas fa-plus"></i> Tambah Data Penjabat', [
                        'class' => 'btn btn-success btn-sm mt-4',
                        'data-bs-toggle' => 'modal',
                        'data-bs-target' => '#createModalPenjabatSkpd',
                        'data-refskpd-id' => $model->refskpd_id,
                      ]) ?>
                    </div>
                  <?php endif; ?>


                </div>
              </div>
            </div>
          </div>


          <!-- End Penjabat SKPD Section -->


          <!-- end row -->
          <!-- End Card SKPD -->
        </div>

      </div>
      <div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="createModalLabel">Tambah Data SAKIP SKPD</h5>
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
              <h5 class="modal-title" id="updateModalLabel">Update Data SAKIP SKPD</h5>
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
      <div class="modal fade" id="createModalPenjabatSkpd" tabindex="-1" aria-labelledby="createModalLabelPenjabatSkpd" aria-hidden="true">
        <div class="modal-dialog modal-lg">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="createModalLabelPenjabatSkpd">Lengkapi Data SKPD</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <!-- The form will be loaded here -->
              <div id="modalFormContentPenjabatSkpd">
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
      <div class="modal fade" id="updateModalPenjabatSkpd" tabindex="-1" aria-labelledby="updateModalLabelPenjabatSkpd" aria-hidden="true">
        <div class="modal-dialog modal-lg">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="updateModalLabelPenjabatSkpd">Update Data SAKIP Penjabat SKPD</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <!-- The form will be loaded here -->
              <div id="modalUpdateFormContentPenjabatSkpd">
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

      <!-- [ form-element ] end -->
    </div>
    <!-- [ Main Content ] end -->
  </div>
</div>