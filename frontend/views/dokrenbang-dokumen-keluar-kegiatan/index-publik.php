<?php

use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var frontend\models\search\SakipCascadingkegiatanSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Dokrenbang Dokumen Kegiatan';
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
                            <li class="breadcrumb-item"><a href="<?= Url::to(['/site/index-dokrenbang']) ?>">Home</a></li>
                            <li class="breadcrumb-item" aria-current="page">DOKRENBANG - Dokumen Keluar Kegiatan</li>
                        </ul>
                    </div>
                    <div class="col-md-12">
                        <div class="page-header-title">
                            <h2 class="mb-0">DOKRENBANG - Dokumen Keluar Kegiatan</h2>
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
                            <i class="fas fa-pen-fancy"></i> DOKRENBANG - Dokumen Keluar Kegiatan - <?= Html::decode($nama_skpd) ?>
                        </h6>
                    </div>
                </div>
                <!-- End Card -->

                <!-- Table Start -->
                <div class="card">
                    <div class="card-header">
                        <h5>Data Dokumen Keluar Kegiatan</h5>
                        <small>List Data</small>
                        <br>


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


                        <div class="row">
                            <div class="col-sm-12">
                                <!-- Dropdown filter berdasarkan refperiode_id -->
                                <?= \yii\helpers\Html::beginForm(['index-publik'], 'get', ['class' => 'form-inline']); ?>
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



                        <?php if (empty($cascadingKegiatanList)): ?>
                            <div class="alert alert-warning mt-4">
                                Data tidak ada untuk periode yang dipilih.
                            </div>
                        <?php else: ?>
                            <div class="dt-responsive table-responsive">
                                <table id="table-style-hover" class="table table-striped table-hover table-bordered nowrap" style="font-size:xx-small;">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Cascading Kegiatan</th>
                                            <th>Dokumen</th>
                                            <th>Download</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($cascadingKegiatanList as $index => $kegiatan): ?>
                                            <?php if (!empty($kegiatan->simonaKeluaranmediacascadingkegiatan)): ?>
                                                <tr>
                                                    <td rowspan="<?= count($kegiatan->simonaKeluaranmediacascadingkegiatan) ?>">
                                                        <?= $index + 1 ?>
                                                    </td>
                                                    <td rowspan="<?= count($kegiatan->simonaKeluaranmediacascadingkegiatan) ?>">
                                                        <?= Html::encode($kegiatan->refKegiatan->nama_kegiatan) ?>
                                                    </td>
                                                    <?php foreach ($kegiatan->simonaKeluaranmediacascadingkegiatan as $key => $dokumen): ?>
                                                        <?php if ($key > 0): ?>
                                                <tr><?php endif; ?>
                                                <td>
                                                    <?= Html::encode($dokumen->nama_file) ?>
                                                </td>
                                                <td>
                                                    <?= Html::a(
                                                            'Download',
                                                            ['simona-keluaranmediacascadingkegiatan/download', 'refsimonakeluaranmediacascadingkegiatan_id' => $dokumen->refsimonakeluaranmediacascadingkegiatan_id],
                                                            ['class' => 'btn btn-success btn-sm', 'target' => '_blank']
                                                        ) ?>
                                                </td>
                                                </tr>
                                            <?php endforeach; ?>
                                            </tr>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>


                    </div>
                </div>

                <!-- Table end -->
            </div>
            <!-- end row -->
        </div>


        <!-- [ Main Content ] end -->
    </div>
</div>