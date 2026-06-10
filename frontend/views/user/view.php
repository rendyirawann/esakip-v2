<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var backend\models\User $model */

$this->title = 'View Data SAKIP User - ' . $model->username;
$this->params['breadcrumbs'][] = ['label' => 'Sakip User', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
$this->registerJs("
$('#createModal').on('show.bs.modal', function (event) {
    var modal = $(this);
    $.ajax({
        url: '" . Url::to(['user/create']) . "',
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
          <li class="breadcrumb-item"><a href="<?= Url::to(['/user/index']) ?>">Data SAKIP User</a></li>
          <li class="breadcrumb-item" aria-current="page">View Data SAKIP User</li>
        </ul>
      </div>
      <div class="col-md-12">
        <div class="page-header-title">
          <h2 class="mb-0">View Data SAKIP User</h2>
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
        <h1>View Data SAKIP User - <?= Html::encode($model->id) ?></h1>

        <p>
        <?= Html::button('Update', [
                'class' => 'btn btn-primary',
                'title' => 'Update',
                'data-bs-toggle' => 'modal',
                'data-bs-target' => '#updateModal',
                'data-url' => Url::to(['update', 'id' => $model->id])
            ]) ?>
          <?= Html::a('Delete', ['delete', 'id' => $model->id], [
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
            'id',
            'username',
            'auth_key',
            'password_hash',
            'password_reset_token',
            'email:email',
            'nama_user:ntext',
            [
                'attribute' => 'refskpd_id',
                'label' => 'SKPD User',
                'value' => function ($model) {
                    return $model->skpd ? $model->skpd->nama_skpd : 'Tidak ada SKPD';
                },
            ],
            [
                'attribute' => 'kode_group',
                'label' => 'Group User',
                'value' => function ($model) {
                    return $model->group ? $model->group->nama_group : 'Tidak ada Group User';
                },
            ],
            [
                'attribute' => 'status',
                'format' => 'raw', // Mengizinkan HTML rendering
                'value' => function ($model) {
                    if ($model->status === 10) {
                        return Html::tag('span', 'Aktif', ['class' => 'btn btn-success']);
                    } else if ($model->status === 9) {
                        return Html::tag('span', 'Tidak Aktif', ['class' => 'btn btn-warning']);
                    } else if ($model->status === 0) {
                        return Html::tag('span', 'Deleted', ['class' => 'btn btn-warning']);
                    }
                },
            ],
            'created_at',
            'updated_at',
            'user_lastlogin',
            'user_lastloginip',
            [
                'attribute' => 'user_isonline',
                'format' => 'raw', // Mengizinkan HTML rendering
                'value' => function ($model) {
                    if ($model->user_isonline === 'T') {
                        return Html::tag('span', 'Online', ['class' => 'btn btn-success']);
                    } else if ($model->user_isonline === 'F') {
                        return Html::tag('span', 'Offline', ['class' => 'btn btn-warning']);
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
          <h5 class="modal-title" id="createModalLabel">Tambah Data SAKIP User Group</h5>
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
            <h5 class="modal-title" id="updateModalLabel">Update Data SAKIP User Group</h5>
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