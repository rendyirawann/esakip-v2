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
                                <?= \yii\helpers\Html::beginForm(['index'], 'get', ['class' => 'form-inline']); ?>
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



                        <div class="card mt-4">
                            <div class="card-header">
                                <h5><i class="fas fa-folder-open me-2"></i>Daftar Kegiatan dengan Dokumen Keluaran</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th style="width: 5%;">No.</th>
                                                <th>Nama Kegiatan</th>
                                                <th class="text-center">Jumlah Dokumen</th>
                                                <th class="text-center" style="width: 15%;">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if ($dataProvider->getTotalCount() == 0): ?>
                                                <tr>
                                                    <td colspan="4" class="text-center font-italic p-4">
                                                        Tidak ada dokumen keluaran yang ditemukan untuk periode dan SKPD yang dipilih.
                                                    </td>
                                                </tr>
                                            <?php else: ?>
                                                <?php foreach ($dataProvider->getModels() as $index => $kegiatan): ?>
                                                    <tr>
                                                        <td class="text-center"><?= $dataProvider->pagination->page * $dataProvider->pagination->pageSize + $index + 1 ?></td>
                                                        <td>
                                                            <?php // Pastikan ada relasi 'refKegiatan' di model SakipCascadingkegiatan 
                                                            ?>
                                                            <?= Html::encode($kegiatan->refKegiatan->nama_kegiatan ?? 'Nama Kegiatan Tidak Ditemukan') ?>
                                                        </td>
                                                        <td class="text-center">
                                                            <span class="badge bg-info"><?= count($kegiatan->simonaKeluaranmediacascadingkegiatans) ?> Dokumen</span>
                                                        </td>
                                                        <td class="text-center">
                                                            <?= Html::button('<i class="fas fa-eye"></i> Lihat Dokumen', [
                                                                'class' => 'btn btn-primary btn-sm',
                                                                'data-bs-toggle' => 'modal',
                                                                // Target modal dibuat unik untuk setiap kegiatan
                                                                'data-bs-target' => '#viewModal-' . $kegiatan->refcascadingkegiatan_id,
                                                            ]) ?>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Tampilkan Paginasi -->
                                <div class="d-flex justify-content-end mt-3">
                                    <?= \yii\widgets\LinkPager::widget(['pagination' => $dataProvider->pagination]) ?>
                                </div>
                            </div>
                        </div>


                        <!-- ======================================================================= -->
                        <!-- MODAL DIHASILKAN DI DALAM LOOP UNTUK SETIAP KEGIATAN -->
                        <!-- ======================================================================= -->
                        <?php foreach ($dataProvider->getModels() as $kegiatan): ?>
                            <div class="modal fade" id="viewModal-<?= $kegiatan->refcascadingkegiatan_id ?>" tabindex="-1" aria-labelledby="viewModalLabel-<?= $kegiatan->refcascadingkegiatan_id ?>" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="viewModalLabel-<?= $kegiatan->refcascadingkegiatan_id ?>">
                                                Dokumen untuk: <?= Html::encode($kegiatan->refKegiatan->nama_kegiatan ?? '') ?>
                                            </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <table class="table table-bordered table-striped">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Nama File</th>
                                                        <th class="text-center">Aksi</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($kegiatan->simonaKeluaranmediacascadingkegiatans as $dokumen): ?>
                                                        <tr>
                                                            <td><?= Html::encode($dokumen->nama_file) ?></td>
                                                            <td class="text-center">
                                                                <?= Html::a(
                                                                    '<i class="fas fa-download"></i> Download',
                                                                    ['simona-keluaranmediacascadingkegiatan/download', 'refsimonakeluaranmediacascadingkegiatan_id' => $dokumen->refsimonakeluaranmediacascadingkegiatan_id],
                                                                    ['class' => 'btn btn-success btn-sm', 'target' => '_blank']
                                                                ) ?>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>

                    </div>
                </div>

                <!-- Table end -->
            </div>
            <!-- end row -->
        </div>


        <!-- [ Main Content ] end -->
    </div>
</div>