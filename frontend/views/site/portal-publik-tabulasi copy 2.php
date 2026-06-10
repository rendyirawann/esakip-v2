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

$this->registerCss(<<<CSS
.table th,
.table td {
    padding: 0.35rem 0.5rem;
    font-size: 0.78rem;
    white-space: nowrap;
}

.table thead th {
    font-weight: 600;
}

.card-title {
    font-size: 1rem;
    max-width: 100%;
    word-break: break-word;
}

.card-body > .row {
    margin-left: 0;
    margin-right: 0;
}

.table-responsive {
    overflow-x: auto;
}

@media (min-width: 768px) {
    .card .card-body {
        padding: 1rem;
    }
}
CSS);

$this->registerJs("
    let currentIndex = 0;
    const tables = document.querySelectorAll('.skpd-table');

    function showTable(index) {
        tables.forEach((table, i) => {
            table.style.display = i === index ? '' : 'none';
        });
    }

    document.getElementById('prevBtn').addEventListener('click', () => {
        if (currentIndex > 0) {
            currentIndex--;
            showTable(currentIndex);
        }
    });

    document.getElementById('nextBtn').addEventListener('click', () => {
        if (currentIndex < tables.length - 1) {
            currentIndex++;
            showTable(currentIndex);
        }
    });
");
?>


<div class="container p-2 mt-5">
    <div class="row">

        <div class="col-md-12">

            <!-- Menu list -->

            <div class="d-flex justify-content-center">
                <ul class="list-unstyled d-flex gap-3">

                    <li class="menu-icon card shadow p-1" style="max-width: 150px;">
                        <a href="<?= Url::to(['/site/index-esakip']) ?>" target="_blank" class="text-decoration-none">
                            <img src="<?= Url::base(true) ?>/lightapp/assets/images/esakip.gif" class="card-img-top" alt="e-Sakip">
                        </a>
                        <span class="p-2 text-center"><b>e-SAKIP</b></span>
                    </li>

                    <li class="menu-icon card shadow p-1" style="max-width: 150px;">
                        <a href="<?= Url::to(['/site/index-simona']) ?>" target="_blank" class="text-decoration-none">
                            <img src="<?= Url::base(true) ?>/lightapp/assets/images/perencanaan.gif" class="card-img-top" alt="Perencanaan">
                        </a>
                        <span class="p-2 text-center"><b>Monitoring<br>Perencanaan</b></span>
                    </li>

                    <li class="menu-icon card shadow p-1" style="max-width: 150px;">
                        <a href="<?= Url::to(['/site/index-dokrenbang']) ?>" target="_blank" class="text-decoration-none">
                            <img src="<?= Url::base(true) ?>/lightapp/assets/images/dokrenbang-2.gif" class="card-img-top" alt="Dokumen">
                        </a>
                        <span class="p-2 text-center"><b>Dokumen<br>Perencanaan</b></span>
                    </li>

                </ul>
            </div>

            <!-- Menu list -->

        </div>
    </div>

</div>


<div class="container mt-1">
    <div class="row">

        <div class="col-md-12">

            <!-- Menu list -->

            <div class="d-flex justify-content-center">
                <ul class="list-unstyled d-flex gap-3">

                    <li class="menu-icon card shadow p-1" style="max-width: 150px;">
                        <a href="https://portal.deliserdangkab.go.id/" target="_blank" class="text-decoration-none">
                            <img src="<?= Url::base(true) ?>/lightapp/assets/images/bappeda.png" class="cpns card-img-top py-1 px-2" alt="Portal DS">
                        </a>
                        <span class="p-2 text-center"><b>Portal<br>Deli Serdang</b></span>
                    </li>

                    <li class="menu-icon card shadow p-1" style="max-width: 150px;">
                        <a href="https://bappedalitbang.deliserdangkab.go.id/" target="_blank" class="text-decoration-none">
                            <img src="<?= Url::base(true) ?>/lightapp/assets/images/bappeda.png" class="pilkada-2024 card-img-top pt-1 pb-0 px-2" alt="Bappeda DS">
                        </a>
                        <span class="p-2 text-center"><b>Website<br>Bappedalitbang</b></span>
                    </li>

                </ul>
            </div>

            <!-- Menu list -->

        </div>
    </div>

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
                    <div class="row">
                        <div id="skpd-container">
                            <?php if (!empty($skpdList)): ?>
                                <?php foreach ($skpdList as $index => $skpd): ?>
                                    <div class="skpd-table" style="<?= $index === 0 ? '' : 'display: none;' ?>">
                                        <div class="col-lg-12 mb-3">
                                            <div class="card shadow-sm border-0">
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-md-12 mb-3">
                                                            <h5 class="card-title mb-0"><?= Html::encode($skpd->nama_skpd) ?></h5>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-12">
                                                        <div class="table-responsive">
                                                            <table class="table table-bordered table-sm table-striped align-middle text-center">
                                                                <thead class="table-secondary">
                                                                    <tr>
                                                                        <th colspan="10" class="text-center"><?= Html::encode($skpd->nama_skpd) ?></th>
                                                                    </tr>
                                                                    <tr>
                                                                        <th>No</th>
                                                                        <th>Indikator Sasaran Renstra</th>
                                                                        <th>Realisasi TW 1</th>
                                                                        <th>Capaian TW 1</th>
                                                                        <th>Realisasi TW 2</th>
                                                                        <th>Capaian TW 2</th>
                                                                        <th>Realisasi TW 3</th>
                                                                        <th>Capaian TW 3</th>
                                                                        <th>Realisasi TW 4</th>
                                                                        <th>Capaian TW 4</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <?php
                                                                    $renstraData = $dataRenstra[$skpd->refskpd_id] ?? [];
                                                                    $no = 1;

                                                                    foreach ($renstraData as $sasaranItem):
                                                                        $indikatorList = $sasaranItem['indikator'];
                                                                        $indikatorCount = count($indikatorList);

                                                                        foreach ($indikatorList as $index => $indikatorItem):
                                                                            echo '<tr>';

                                                                            if ($index === 0) {
                                                                                echo '<td rowspan="' . $indikatorCount . '">' . $no++ . '</td>';
                                                                            }

                                                                            echo '<td style="white-space: normal;">' . Html::encode($indikatorItem['uraian_indikator']) . '</td>';

                                                                            foreach ($indikatorItem['triwulan'] as $tw) {
                                                                                $realisasi = Html::encode($tw->triwulan_realisasi);
                                                                                $capaian = Html::encode($tw->triwulan_capaian);
                                                                                $satuan = Html::encode($tw->refIndikatorsasaranrenstra->indikatorsasaranrenstra_satuan ?? '');

                                                                                echo '<td>' . ($realisasi !== '' ? $realisasi : '-') . '</td>';

                                                                                if ($realisasi === '' || $realisasi === null || $realisasi == 0) {
                                                                                    echo '<td><span class="badge bg-danger">Belum ada capaian</span></td>';
                                                                                } else {
                                                                                    echo '<td>' . $capaian . ' ' . $satuan . '</td>';
                                                                                }
                                                                            }

                                                                            echo '</tr>';
                                                                        endforeach;
                                                                    endforeach;
                                                                    ?>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <?php for ($i = 1; $i <= 3; $i++): ?>
                                    <div class="skpd-table" style="<?= $i === 1 ? '' : 'display: none;' ?>">
                                        <div class="col-lg-12 mb-3">
                                            <div class="card shadow-sm border-0">
                                                <div class="card-body">
                                                    <h5 class="card-title mb-3">Contoh Tabel SKPD <?= $i ?></h5>
                                                    <table class="table table-bordered text-center">
                                                        <tr>
                                                            <td colspan="10">Tabel kosong - SKPD <?= $i ?></td>
                                                        </tr>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endfor; ?>
                            <?php endif; ?>
                        </div>

                        <!-- Navigation Buttons -->
                        <div class="text-center">
                            <button class="btn btn-outline-primary me-2" id="prevBtn">← Sebelumnya</button>
                            <button class="btn btn-outline-primary" id="nextBtn">Berikutnya →</button>
                        </div>
                    </div>
                </div>


                <!-- CSS Styling -->
                <style>
                    .table-sm th,
                    .table-sm td {
                        font-size: 12px;
                        /* Memperkecil ukuran font */
                        padding: 4px 8px;
                        /* Memperkecil padding untuk membuat tabel lebih kecil */
                    }

                    /* Membuat konten dalam tabel dapat melakukan word wrapping */
                    .table td,
                    .table th {
                        white-space: normal !important;
                        word-wrap: break-word;
                    }

                    /* Responsif untuk ukuran layar kecil */
                    @media (max-width: 768px) {
                        .table-responsive {
                            overflow-x: auto;
                        }

                        .table-sm th,
                        .table-sm td {
                            font-size: 10px;
                            /* Ukuran font lebih kecil untuk perangkat mobile */
                            padding: 3px 5px;
                            /* Padding lebih kecil pada perangkat mobile */
                        }
                    }
                </style>



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