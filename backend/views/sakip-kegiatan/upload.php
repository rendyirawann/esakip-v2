<?php

use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var backend\models\SakipKegiatan $model */

$this->title = 'Upload CSV to Database';
$this->params['breadcrumbs'][] = ['label' => 'Sakip Kegiatan', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
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
              <li class="breadcrumb-item" aria-current="page">Data SAKIP Kegiatan</li>
            </ul>
          </div>
          <div class="col-md-12">
            <div class="page-header-title">
              <h2 class="mb-0">Data SAKIP Kegiatan</h2>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- [ breadcrumb ] end -->


    <!-- [ Main Content ] start -->
    <div class="row">
      <div class="col">
        <div class="card">
          <div class="card-header">
            <h5>Upload Data CSV</h5>
          </div>
          <div class="card-body">
            <?php if (Yii::$app->session->hasFlash('success')): ?>
              <div class="alert alert-success">
                <?= Yii::$app->session->getFlash('success') ?>
              </div>
            <?php endif; ?>

            <?php if (Yii::$app->session->hasFlash('error')): ?>
              <div class="alert alert-danger">
                <?= Yii::$app->session->getFlash('error') ?>
              </div>
            <?php endif; ?>
            <?= $this->render('_upload', [
              'model' => $model,
            ]) ?>
          </div>
        </div>
      </div>
    </div>
    <!-- [ Main Content ] end -->
  </div>
</div>