<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var backend\models\SakipMisi $model */

$this->title = 'View Data SAKIP Tujuan - ' . $model->uraian_tujuan;
$this->params['breadcrumbs'][] = ['label' => 'Sakip Misis', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
$this->registerJs("
$('#createModal').on('show.bs.modal', function (event) {
    var modal = $(this);
    $.ajax({
        url: '" . Url::to(['sakip-tujuan/create']) . "',
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
              <li class="breadcrumb-item"><a href="<?= Url::to(['/sakip-tujuan/index']) ?>">Data SAKIP Tujuan</a></li>
              <li class="breadcrumb-item" aria-current="page">View Data SAKIP Tujuan</li>
            </ul>
          </div>
          <div class="col-md-12">
            <div class="page-header-title">
              <h2 class="mb-0">View Data SAKIP Tujuan</h2>
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
            <h1>View Data SAKIP Tujuan - <?= Html::encode($model->reftujuan_id) ?></h1>

            <p>
              <?= Html::button('Update', [
                'class' => 'btn btn-primary',
                'title' => 'Update',
                'data-bs-toggle' => 'modal',
                'data-bs-target' => '#updateModal',
                'data-url' => Url::to(['update', 'reftujuan_id' => $model->reftujuan_id])
              ]) ?>
              <?= Html::a('Delete', ['delete', 'reftujuan_id' => $model->reftujuan_id], [
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
                'reftujuan_id',
                [
                  'attribute' => 'uraian_tujuan',
                  'format' => 'raw', // Mengizinkan rendering HTML
                  'contentOptions' => [
                    'style' => 'white-space: pre-wrap;', // Mengizinkan word wrap
                  ],
                  'value' => function ($model) {
                    return $model->uraian_tujuan;
                  },
                ],
                [
                  'attribute' => 'indikator_tujuan',
                  'format' => 'raw', // Mengizinkan rendering HTML
                  'contentOptions' => [
                    'style' => 'white-space: pre-wrap;', // Mengizinkan word wrap
                  ],
                  'value' => function ($model) {
                    return $model->indikator_tujuan;
                  },
                ],
                [
                  'attribute' => 'refvisi_id',
                  'label' => 'Visi Terkait',
                  'format' => 'raw', // Mengizinkan rendering HTML
                  'contentOptions' => [
                    'style' => 'white-space: pre-wrap;', // Mengizinkan word wrap
                  ],
                  'value' => function ($model) {
                    return $model->visi ? $model->visi->uraian_visi : 'Tidak ada visi';
                  },
                ],
                [
                  'attribute' => 'refmisi_id',
                  'label' => 'Misi Terkait',
                  'format' => 'raw', // Mengizinkan rendering HTML
                  'contentOptions' => [
                    'style' => 'white-space: pre-wrap;', // Mengizinkan word wrap
                  ],
                  'value' => function ($model) {
                    return $model->misi ? $model->misi->uraian_misi : 'Tidak ada misi';
                  },
                ],
                [
                  'attribute' => 'refperiode_id',
                  'label' => 'Periode Tahun',
                  'value' => function ($model) {
                    return $model->periodeLabel();
                  },
                ],
                [
                  'attribute' => 'tujuan_isaktif',
                  'format' => 'raw', // Mengizinkan HTML rendering
                  'value' => function ($model) {
                    if ($model->tujuan_isaktif === 'T') {
                      return Html::tag('span', 'Aktif', ['class' => 'btn btn-success']);
                    } else if ($model->tujuan_isaktif === 'F') {
                      return Html::tag('span', 'Tidak Aktif', ['class' => 'btn btn-warning']);
                    }
                  },
                ],
              ],
            ]) ?>

          </div>
        </div>

      </div>
      <div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="createModalLabel">Tambah Data SAKIP Tujuan</h5>
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
              <h5 class="modal-title" id="updateModalLabel">Update Data SAKIP Tujuan</h5>
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


      <!-- [ form-element ] end -->
    </div>
    <!-- [ Main Content ] end -->
  </div>
</div>