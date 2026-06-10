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
            <h2>Perencanaan</h2>
            <p>Cascading perencanaan kinerja (Renstra, IKU, RKT, PK, PKP)</p>
        </div>

        <div class="esk-pub-grid" style="margin-bottom: 8px;">
            <a class="esk-pub-card" href="<?= Url::to(['/site/portal-publik-tabulasi']) ?>">
                <span class="esk-pub-ic"><i class="bi bi-table"></i></span>
                <span class="t">Tabulasi</span>
                <span class="s">Tabulasi capaian sasaran per SKPD</span>
            </a>
            <a class="esk-pub-card active" href="<?= Url::to(['/site/portal-publik-perencanaan']) ?>">
                <span class="esk-pub-ic"><i class="bi bi-diagram-3"></i></span>
                <span class="t">Perencanaan</span>
                <span class="s">Cascading perencanaan kinerja</span>
            </a>
            <a class="esk-pub-card" href="<?= Url::to(['/site/portal-publik-capkin']) ?>">
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

<div class="container mt-3">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow-sm border-0">
                <div class="card-header text-white d-flex align-items-center"
                    style="background: radial-gradient(circle, rgba(81, 196, 248, 1) 0%, rgba(0, 81, 196, 0.83) 83%, rgba(0, 81, 196, 1) 100%);">
                    <i class="fas fa-bullseye me-2"></i>
                    <h6 class="mb-0" id="toggleAll" style="cursor: pointer;">Sakip Publik</h6>
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

                <div class="card-body p-3">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped align-middle text-center">
                            <thead class="table-primary">
                                <tr>
                                    <th rowspan="2">No</th>
                                    <th rowspan="2"><i class="fas fa-building me-1"></i> SKPD</th>
                                    <th rowspan="2">Renstra</th>
                                    <th colspan="4">Perencanaan Periode Tahun <?= $selectedPeriodValue ?></th>
                                </tr>
                                <tr>
                                    <th>IKU</th>
                                    <th>RKT</th>
                                    <th>PK</th>
                                    <th>PKP</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($skpdList as $index => $skpd): ?>
                                    <tr>
                                        <td><?= $index + 1; ?></td>
                                        <td style="white-space: normal;"><?= Html::encode($skpd->nama_skpd); ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-gradient" title="View Renstra" data-bs-toggle="modal" data-bs-target="#myModal<?= $skpd->refskpd_id ?>">
                                                <i class="fas fa-book-open"></i>
                                            </button>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-gradient" title="View IKU" data-bs-toggle="modal" data-bs-target="#myModalIku<?= $skpd->refskpd_id ?>">
                                                <i class="fas fa-chart-line"></i>
                                            </button>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-gradient" title="View RKT" data-bs-toggle="modal" data-bs-target="#myModalRkt<?= $skpd->refskpd_id ?>">
                                                <i class="fas fa-calendar-alt"></i>
                                            </button>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-gradient" title="View PK" data-bs-toggle="modal" data-bs-target="#myModalPk<?= $skpd->refskpd_id ?>">
                                                <i class="fas fa-file-alt"></i>
                                            </button>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-gradient" title="View PKP" data-bs-toggle="modal" data-bs-target="#myModalPkp<?= $skpd->refskpd_id ?>">
                                                <i class="fas fa-edit"></i>
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
                                        <?= $this->render('perencanaan/_view-renstra', [
                                            'sasaranRenstra' => $sasaranRenstraList[$skpd->refskpd_id] ?? [],
                                            'strategiList' => $strategiList[$skpd->refskpd_id] ?? [],
                                            'kebijakanList' => $kebijakanList[$skpd->refskpd_id] ?? [],
                                            'selectedPeriodValue' => $selectedPeriodValue,  // Add this line
                                            'nama_skpd' => $namaSkpdList[$skpd->refskpd_id], // Pass the name
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
                                        <?= $this->render('perencanaan/_view-iku', [
                                            'sasaranRenstra' => $sasaranRenstraIkuList[$skpd->refskpd_id] ?? [],
                                            'indikatorsIkuList' => $indikatorsIkuList[$skpd->refskpd_id] ?? [],
                                            'selectedPeriodValue' => $selectedPeriodValue,  // Add this line
                                            'nama_skpd' => $namaSkpdList[$skpd->refskpd_id], // Pass the name
                                        ]) ?>

                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal fade" id="myModalRkt<?= $skpd->refskpd_id ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-xl" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">Detail Data RKT SKPD</h5>
                                    </div>
                                    <div class="modal-body">
                                        <!-- Render _view-iku with refskpd_id and refperiode_id -->
                                        <?= $this->render('perencanaan/_view-rkt', [
                                            'sasaranRenstra' => $sasaranRenstraRktList[$skpd->refskpd_id] ?? [],
                                            'selectedPeriodValue' => $selectedPeriodValue,  // Add this line
                                            'nama_skpd' => $namaSkpdList[$skpd->refskpd_id], // Pass the name
                                        ]) ?>

                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal fade" id="myModalPk<?= $skpd->refskpd_id ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-xl" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">Detail Data PK SKPD</h5>
                                    </div>
                                    <div class="modal-body">
                                        <!-- Render _view-iku with refskpd_id and refperiode_id -->
                                        <?= $this->render('perencanaan/_view-pk', [
                                            'sasaranRenstra' => $sasaranRenstraPkList[$skpd->refskpd_id] ?? [],
                                            'selectedPeriodValue' => $selectedPeriodValue,  // Add this line
                                            'nama_skpd' => $namaSkpdList[$skpd->refskpd_id], // Pass the name
                                        ]) ?>

                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal fade" id="myModalPkp<?= $skpd->refskpd_id ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-xl" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">Detail Data PKP SKPD</h5>
                                    </div>
                                    <div class="modal-body">
                                        <!-- Render _view-iku with refskpd_id and refperiode_id -->
                                        <?= $this->render('perencanaan/_view-pkp', [
                                            'sasaranRenstra' => $sasaranRenstraPkpList[$skpd->refskpd_id] ?? [],
                                            'selectedPeriodValue' => $selectedPeriodValue,  // Add this line
                                            'nama_skpd' => $namaSkpdList[$skpd->refskpd_id], // Pass the name
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
        background: radial-gradient(circle, rgba(81, 196, 248, 1) 0%, rgba(0, 81, 196, 0.83) 83%, rgba(0, 81, 196, 1) 100%);
        color: #fff;
        border: none;
        transition: all 0.3s ease;
    }

    .btn-gradient:hover {
        background: radial-gradient(circle, rgba(0, 81, 196, 1) 0%, rgba(81, 196, 248, 1) 100%);
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