<?php

use frontend\models\LaporanRenjaKataPengantar;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var frontend\models\search\LaporanRenjaKataPengantarSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Laporan Renja Kata Pengantars';
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
                            <li class="breadcrumb-item"><a href="<?= Url::to(['/buku-laporan/index']) ?>">Dashboard Buku Laporan</a></li>
                            <li class="breadcrumb-item" aria-current="page">Laporan Renja Kata Pengantar</li>
                        </ul>
                    </div>
                    <div class="col-md-12">
                        <div class="page-header-title">
                            <h2 class="mb-0">Data Laporan Renja Kata Pengantar</h2>
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
                        <h5>Data Laporan Renja Kata Pengantar</h5>
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
                        <?= Html::a('Tambah Kata Pengantar', ['create'], ['class' => 'btn btn-success mb-3']) ?>
                        <div class="dt-responsive table-responsive">
                            <table id="table-style-hover" class="table table-striped table-hover table-bordered nowrap" style="font-size:xx-small;">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Periode</th>
                                        <th>SKPD</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    <?php $no = 1; ?>
                                    <?php foreach ($dataProvider->models as $model) : ?>
                                        <tr>
                                            <td><?= Html::encode($no++) ?></td>
                                            <td><?= Html::encode($model->refPeriode->periode) ?></td>
                                            <td><?= Html::encode($model->refSkpd->nama_skpd) ?></td>
                                            <td style="width: 150px;">
                                                <button type="button" class="btn btn-primary btn-sm" title="View" data-toggle="modal" data-target="#myModal<?= $model->laporan_renja_kata_pengantar_id ?>">
                                                    <i class="fa fa-info"></i>
                                                </button>
                                                <?= Html::a('<i class="fa fa-eye"></i>', ['view', 'laporan_renja_kata_pengantar_id' => $model->laporan_renja_kata_pengantar_id], ['class' => 'btn btn-primary btn-sm', 'title' => 'View']) ?>
                                                <?= Html::a('<i class="fa fa-edit"></i>', ['update', 'laporan_renja_kata_pengantar_id' => $model->laporan_renja_kata_pengantar_id], ['class' => 'btn btn-success btn-sm', 'title' => 'Update']) ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>

                            </table>
                        </div>
                        <!-- Modal -->
                        <?php foreach ($dataProvider->models as $model) : ?>
                            <div class="modal fade" id="myModal<?= $model->laporan_renja_kata_pengantar_id ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-xl" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLabel">Detail Data Bidang Bappeda</h5>
                                        </div>
                                        <div class="modal-body">
                                            <!-- Render view.php di sini -->
                                            <?= $this->render('_view', ['model' => $model]) ?>
                                        </div>
                                        <div class="modal-footer">
                                            <?= Html::a('<i class="fas fa-eye"> </i>', ['view', 'laporan_renja_kata_pengantar_id' => $model->laporan_renja_kata_pengantar_id], ['class' => 'btn btn-info ml-2', 'title' => 'View']) ?>
                                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>




                    </div>
                </div>
            </div>
            <!-- Base style - Hover table end -->
        </div>
        <!-- [ Main Content ] end -->
    </div>
</div>