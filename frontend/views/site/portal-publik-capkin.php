<?php

/** @var \yii\web\View $this */
/** @var string $content */

use common\widgets\Alert;
use frontend\assets\MainPortalAsset;
use yii\bootstrap5\Breadcrumbs;
// use yii\bootstrap5\Html;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;

MainPortalAsset::register($this);

$this->title = 'e-SAKIP Deli Serdang';
?>


<div class="esk-portal">
    <section class="esk-portal-section" style="padding-top: 36px;">
        <div class="esk-section-head" style="margin: 8px 0 22px;">
            <span class="eyebrow">Layanan Publik</span>
            <h2>Capaian Kinerja</h2>
            <p>Realisasi dan capaian indikator kinerja Perangkat Daerah</p>
        </div>

        <div class="esk-pub-grid" style="margin-bottom: 8px;">
            <a class="esk-pub-card" href="<?= Url::to(['/site/portal-publik-tabulasi']) ?>">
                <span class="esk-pub-ic"><i class="bi bi-table"></i></span>
                <span class="t">Tabulasi</span>
                <span class="s">Tabulasi capaian sasaran per SKPD</span>
            </a>
            <a class="esk-pub-card" href="<?= Url::to(['/site/portal-publik-perencanaan']) ?>">
                <span class="esk-pub-ic"><i class="bi bi-diagram-3"></i></span>
                <span class="t">Perencanaan</span>
                <span class="s">Cascading perencanaan kinerja</span>
            </a>
            <a class="esk-pub-card active" href="<?= Url::to(['/site/portal-publik-capkin']) ?>">
                <span class="esk-pub-ic"><i class="bi bi-graph-up-arrow"></i></span>
                <span class="t">Capaian Kinerja</span>
                <span class="s">Realisasi &amp; capaian indikator</span>
            </a>
            <a class="esk-pub-card" href="<?= Url::to(['/site/portal-publik-evaluasi-renja']) ?>">
                <span class="esk-pub-ic"><i class="bi bi-clipboard-data"></i></span>
                <span class="t">Evaluasi Renja</span>
                <span class="s">Hasil evaluasi Rencana Kerja</span>
            </a>
        </div>
    </section>
</div>


<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow-sm border-0">
                <div class="card-header text-white d-flex align-items-center justify-content-between"
                    style="background: radial-gradient(circle, rgba(81, 196, 248, 1) 0%, rgba(0, 81, 196, 0.83) 83%, rgba(0, 81, 196, 1) 100%);">
                    <div>
                        <i class="fas fa-bullseye me-2"></i>
                        <strong>Sakip Publik</strong>
                    </div>
                    <i class="fas fa-chevron-down" id="toggleAll" style="cursor: pointer;"></i>
                </div>

                <div class="card-body pb-0">
                    <?= \yii\helpers\Html::beginForm(['portal-publik'], 'get', ['class' => 'row g-3 align-items-end']); ?>

                    <div class="col-md-4">
                        <?= \yii\helpers\Html::label('<i class="fas fa-calendar-alt me-1"></i> Periode', 'refperiode_id', ['class' => 'form-label']); ?>
                        <?= \yii\helpers\Html::dropDownList(
                            'refperiode_id',
                            $selectedPeriodId,
                            \yii\helpers\ArrayHelper::map($periodeList, 'refperiode_id', 'periode'),
                            ['class' => 'form-control', 'prompt' => 'Semua Periode']
                        ); ?>
                    </div>

                    <div class="col-md-4">
                        <?= \yii\helpers\Html::label('<i class="fas fa-list me-1"></i> Halaman Tujuan', 'target_page', ['class' => 'form-label']); ?>
                        <?= \yii\helpers\Html::dropDownList(
                            'target_page',
                            null,
                            [
                                'portal-publik-tabulasi' => 'Tabulasi',
                                'portal-publik-perencanaan' => 'Perencanaan',
                                'portal-publik-capkin' => 'Capkin'
                            ],
                            ['class' => 'form-control', 'prompt' => 'Pilih Halaman']
                        ); ?>
                    </div>

                    <div class="col-md-4">
                        <?= \yii\helpers\Html::submitButton('<i class="fas fa-search me-1"></i> Tampilkan', [
                            'class' => 'btn btn-success btn-gradient w-100'
                        ]); ?>
                    </div>

                    <?= \yii\helpers\Html::endForm(); ?>
                </div>

                <div class="card-body pt-3">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle text-center">
                            <thead class="table-primary">
                                <tr>
                                    <th rowspan="2">No</th>
                                    <th rowspan="2"><i class="fas fa-building me-1"></i> SKPD</th>
                                    <th colspan="2">Capaian Kinerja Periode Tahun <?= $selectedPeriodValue ?></th>
                                </tr>
                                <tr>
                                    <th>IKU</th>
                                    <th>Strategis</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($skpdList as $index => $skpd): ?>
                                    <tr>
                                        <td><?= $index + 1; ?></td>
                                        <td class="text-start"><?= Html::encode($skpd->nama_skpd); ?></td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-gradient" title="Lihat CAPKIN IKU" data-bs-toggle="modal" data-bs-target="#myModal<?= $skpd->refskpd_id ?>">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-gradient" title="Lihat CAPKIN Strategis" data-bs-toggle="modal" data-bs-target="#myModalIku<?= $skpd->refskpd_id ?>">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div> <!-- table-responsive -->
                    <?php foreach ($skpdList as $skpd): ?>
                        <div class="modal fade" id="myModal<?= $skpd->refskpd_id ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-xl" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">Detail Data Renstra SKPD</h5>
                                    </div>
                                    <div class="modal-body">
                                        <!-- Render _view-renstra with refskpd_id and refperiode_id -->
                                        <?= $this->render('capkin/_view-capkin-iku', [
                                            'indikators' => $indikatorsCapkinUtamaList[$skpd->refskpd_id] ?? [],
                                            'selectedPeriodValue' => $selectedPeriodValue,  // Add this line
                                            'nama_skpd' => $namaSkpdList[$skpd->refskpd_id], // Pass the name
                                            'refskpd_id' => $skpd->refskpd_id, // Pass refskpd_id to the view
                                            'triwulanDataMap' => $triwulanDataMap ?? [], // Pass triwulanDataMap
                                        ]) ?>

                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal fade" id="myModalIku<?= $skpd->refskpd_id ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-xl" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">Detail Data IKU SKPD</h5>
                                    </div>
                                    <div class="modal-body">
                                        <!-- Render _view-iku with refskpd_id and refperiode_id -->
                                        <?= $this->render('capkin/_view-capkin-strategis', [
                                            'indikators' => $indikatorsCapkinStrategiList[$skpd->refskpd_id] ?? [],
                                            'selectedPeriodValue' => $selectedPeriodValue,  // Add this line
                                            'nama_skpd' => $namaSkpdList[$skpd->refskpd_id], // Pass the name
                                            'refskpd_id' => $skpd->refskpd_id, // Pass refskpd_id to the view
                                            'triwulanDataMap' => $triwulanDataMap ?? [], // Pass triwulanDataMap
                                        ]) ?>

                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div> <!-- card-body -->
            </div>
        </div>
    </div>
</div>

<style>
    .btn-gradient {
        background: radial-gradient(circle, rgba(29, 233, 182, 1) 0%, rgba(0, 188, 167, 1) 83%, rgba(0, 125, 98, 1) 100%);
        color: #fff;
        border: none;
        transition: all 0.3s ease;
    }

    .btn-gradient:hover {
        background: radial-gradient(circle, rgba(0, 125, 98, 1) 0%, rgba(0, 188, 167, 1) 83%, rgba(29, 233, 182, 1) 100%);
        color: #fff;
        transform: scale(1.05);
    }

    .form-label i {
        color: #007bff;
    }

    .table th,
    .table td {
        vertical-align: middle;
    }
</style>