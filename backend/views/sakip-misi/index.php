<?php

use backend\models\SakipMisi;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var backend\models\search\SakipMisiSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Sakip Misi';
$this->params['breadcrumbs'][] = $this->title;
$this->registerJs("
$('#createModal').on('show.bs.modal', function (event) {
    var modal = $(this);
    $.ajax({
        url: '" . Url::to(['sakip-misi/create']) . "',
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
              <li class="breadcrumb-item" aria-current="page">Data SAKIP Misi</li>
            </ul>
          </div>
          <div class="col-md-12">
            <div class="page-header-title">
              <h2 class="mb-0">Data SAKIP Misi</h2>
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
            <h5>Data SAKIP Misi</h5>
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
            <?= Html::button('Tambah Data SAKIP Misi', [
              'class' => 'btn btn-success',
              'data-bs-toggle' => 'modal',
              'data-bs-target' => '#createModal',
            ]) ?>

            <div class="dt-responsive table-responsive">
              <table id="table-style-hover" class="table table-striped table-hover table-bordered nowrap" style="font-size:xx-small;">
                <thead>
                  <tr>
                    <th>No</th>
                    <th>Uraian Misi</th>
                    <th>Periode Tahun</th>
                    <th>Visi Terkait</th>
                    <th>Status Misi</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>

                  <?php $no = 1; ?>
                  <?php foreach ($dataProvider->models as $model) : ?>
                    <tr>
                      <td><?= Html::encode($no++) ?></td>
                      <td style="white-space: pre-wrap;"><?= Html::decode($model->uraian_misi) ?></td>
                      <td><?= Html::encode($model->periodeLabel()) ?></td>
                      <td style="white-space: normal;"><?= Html::decode($model->visi->uraian_visi) ?></td>
                      <td>
                        <?php if ($model->misi_isaktif === 'T'): ?>
                          <span class="btn btn-success btn-sm">Aktif</span>
                        <?php elseif ($model->misi_isaktif === 'F'): ?>
                          <span class="btn btn-warning btn-sm">Tidak Aktif</span>
                        <?php endif; ?>
                      </td>
                      <td style="width: 150px;">
                        <button type="button" class="btn btn-primary btn-sm" title="View" data-bs-toggle="modal" data-bs-target="#myModal<?= $model->refmisi_id ?>">
                          <i class="fas fa-eye"></i>
                        </button>
                        <?= Html::button('<i class="fas fa-edit"></i>', [
                          'class' => 'btn btn-success btn-sm',
                          'title' => 'Update',
                          'data-bs-toggle' => 'modal',
                          'data-bs-target' => '#updateModal',
                          'data-url' => Url::to(['update', 'refmisi_id' => $model->refmisi_id])
                        ]) ?>
                        <?= Html::a('<i class="fas fa-trash-alt"></i>', ['delete', 'refmisi_id' => $model->refmisi_id], [
                          'class' => 'btn btn-danger btn-sm',
                          'title' => 'Delete',
                          'data' => [
                            'confirm' => 'Are you sure you want to delete this item?',
                            'method' => 'post',
                          ],
                        ]) ?>
                        <?= Html::a('<i class="fas fa-copy"></i>', ['duplicate', 'refmisi_id' => $model->refmisi_id], [
                          'class' => 'btn btn-secondary btn-sm',
                          'title' => 'Duplicate',
                          'data' => [
                            'confirm' => 'Are you sure you want to duplicate this item?',
                            'method' => 'post',
                          ],
                        ]) ?>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
                <tfoot>
                  <tr>
                    <th>No</th>
                    <th>Uraian Misi</th>
                    <th>Periode Tahun</th>
                    <th>Visi Terkait</th>
                    <th>Status Misi</th>
                    <th>Action</th>
                  </tr>
                </tfoot>
              </table>
            </div>
            <!-- Modal -->
            <?php foreach ($dataProvider->models as $model) : ?>
              <div class="modal fade" id="myModal<?= $model->refmisi_id ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-xl" role="document">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title" id="exampleModalLabel">Detail Data Misi</h5>
                    </div>
                    <div class="modal-body">
                      <!-- Render view.php di sini -->
                      <?= $this->render('_view', ['model' => $model]) ?>
                    </div>
                    <div class="modal-footer">
                      <?= Html::a('<i class="fas fa-eye"> </i>', ['view', 'refmisi_id' => $model->refmisi_id], ['class' => 'btn btn-info ml-2', 'title' => 'View']) ?>
                      <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                    </div>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
            <div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
              <div class="modal-dialog modal-lg">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="createModalLabel">Tambah Data SAKIP Misi</h5>
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
                    <h5 class="modal-title" id="updateModalLabel">Update Data SAKIP Misi</h5>
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



          </div>
        </div>
      </div>
      <!-- Base style - Hover table end -->
    </div>
    <!-- [ Main Content ] end -->
  </div>
</div>