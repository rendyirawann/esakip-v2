<?php

/** @var \yii\web\View $this */
/** @var string $content */

use common\widgets\Alert;
use frontend\assets\MainPortalAsset;
use yii\bootstrap5\Breadcrumbs;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

MainPortalAsset::register($this);

$this->title = 'e-SAKIP Deli Serdang - Evaluasi Renja';
?>


<div class="esk-portal">
    <section class="esk-portal-section" style="padding-top: 36px;">
        <div class="esk-section-head" style="margin: 8px 0 22px;">
            <span class="eyebrow">Layanan Publik</span>
            <h2>Evaluasi Renja</h2>
            <p>Hasil evaluasi Rencana Kerja Perangkat Daerah</p>
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
            <a class="esk-pub-card" href="<?= Url::to(['/site/portal-publik-capkin']) ?>">
                <span class="esk-pub-ic"><i class="bi bi-graph-up-arrow"></i></span>
                <span class="t">Capaian Kinerja</span>
                <span class="s">Realisasi &amp; capaian indikator</span>
            </a>
            <a class="esk-pub-card active" href="<?= Url::to(['/site/portal-publik-evaluasi-renja']) ?>">
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
                        <strong>Evaluasi Renja Publik</strong>
                    </div>
                    <i class="fas fa-chevron-down" id="toggleAll" style="cursor: pointer;"></i>
                </div>

                <div class="card-body pb-0">
                    <?= \yii\helpers\Html::beginForm(['portal-publik'], 'get', ['class' => 'row g-3 align-items-end']); ?>

                    <!-- Pastikan target page tetap disubmit agar routing berfungsi -->
                    <?= Html::hiddenInput('target_page', 'portal-publik-evaluasi-renja') ?>

                    <div class="col-md-4">
                        <?= \yii\helpers\Html::label('<i class="fas fa-calendar-alt me-1"></i> Periode / Tahun', 'refperiode_id', ['class' => 'form-label']); ?>
                        <?= \yii\helpers\Html::dropDownList(
                            'refperiode_id',
                            $refperiode_id,
                            \yii\helpers\ArrayHelper::map($periodeList, 'refperiode_id', 'periode'),
                            ['class' => 'form-control', 'prompt' => 'Pilih Tahun (Default: Sekarang)']
                        ); ?>
                    </div>

                    <div class="col-md-4">
                        <?= \yii\helpers\Html::label('<i class="fas fa-building me-1"></i> SKPD', 'refskpd_id', ['class' => 'form-label']); ?>
                        <?= \yii\helpers\Html::dropDownList(
                            'refskpd_id',
                            $refskpd_id,
                            \yii\helpers\ArrayHelper::map($skpdList, 'refskpd_id', 'nama_skpd'),
                            ['class' => 'form-control', 'prompt' => 'Semua SKPD']
                        ); ?>
                    </div>

                    <div class="col-md-4">
                        <?= \yii\helpers\Html::submitButton('<i class="fas fa-search me-1"></i> Tampilkan', [
                            'class' => 'btn btn-success btn-gradient w-100'
                        ]); ?>
                    </div>

                    <?= \yii\helpers\Html::endForm(); ?>
                </div>

                <div class="card-body pt-4">
                    <?php
                        $skpdName = 'Semua SKPD';
                        if (!empty($refskpd_id)) {
                            foreach ($skpdList as $skpd) {
                                if ($skpd->refskpd_id == $refskpd_id) {
                                    $skpdName = $skpd->nama_skpd;
                                    break;
                                }
                            }
                        }
                    ?>
                    <h5 class="mb-4 text-center text-muted">
                        Detail Pagu Tahun <?= Html::encode($tahun) ?> 
                        <br>
                        <span class="badge bg-secondary mt-2" style="font-size: 0.85em; font-weight: normal;">
                            <?= Html::encode($skpdName) ?>
                        </span>
                    </h5>
                    <div class="row">
                        <!-- Pagu Program -->
                        <div class="col-md-4 mb-3">
                            <div class="card text-center border-0 shadow-sm" style="background:linear-gradient(135deg,#e3f2fd,#bbdefb);">
                                <div class="card-body p-4">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="text-muted mb-0">Total Pagu Program</h6>
                                        <i class="fas fa-briefcase text-primary" style="font-size: 24px;"></i>
                                    </div>
                                    <h3 class="mb-0 text-primary text-start">Rp <?= number_format($totalPaguProgram, 2, ',', '.') ?></h3>
                                </div>
                            </div>
                        </div>

                        <!-- Pagu Kegiatan -->
                        <div class="col-md-4 mb-3">
                            <div class="card text-center border-0 shadow-sm" style="background:linear-gradient(135deg,#fff3e0,#ffe0b2);">
                                <div class="card-body p-4">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="text-muted mb-0">Total Pagu Kegiatan</h6>
                                        <i class="fas fa-list text-warning" style="font-size: 24px; color: #e65100;"></i>
                                    </div>
                                    <h3 class="mb-0 text-start" style="color:#e65100">Rp <?= number_format($totalPaguKegiatan, 2, ',', '.') ?></h3>
                                </div>
                            </div>
                        </div>

                        <!-- Pagu Sub Kegiatan -->
                        <div class="col-md-4 mb-3">
                            <div class="card text-center border-0 shadow-sm" style="background:linear-gradient(135deg,#e8f5e9,#c8e6c9);">
                                <div class="card-body p-4">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="text-muted mb-0">Total Pagu Sub Kegiatan</h6>
                                        <i class="fas fa-check-circle text-success" style="font-size: 24px;"></i>
                                    </div>
                                    <h3 class="mb-0 text-success text-start">Rp <?= number_format($totalPaguSubkegiatan, 2, ',', '.') ?></h3>
                                </div>
                            </div>
                        </div>
                    </div>
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
</style>
