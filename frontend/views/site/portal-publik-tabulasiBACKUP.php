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

<div class="container p-2 mt-1">
    <div class="row">

        <div class="col-md-12">

            <!-- Menu list -->

            <div class="justify-content-center">
                <div class="card">
                    <div class="card-header" style="background: radial-gradient(circle, rgba(81, 196, 248, 1) 0%, rgba(0, 81, 196, 0.83) 83%, rgba(0, 81, 196, 1) 100%); padding: 8px;"> <!-- Sesuaikan padding di sini -->
                        <h6 style="color: white; margin: 0; cursor: pointer;" id="toggleAll"> <!-- Mengatur margin menjadi 0 untuk mengurangi ruang -->
                            <i class="fas fa-pen-fancy"></i> Sakip Publik
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <!-- Dropdown filter berdasarkan refperiode_id dan halaman tujuan -->
                                <?= \yii\helpers\Html::beginForm(['portal-publik'], 'get', ['class' => 'form-inline']); ?>

                                <!-- Dropdown for selecting Periode -->
                                <div class="form-group mr-2">
                                    <?= \yii\helpers\Html::label('Pilih Periode:', 'refperiode_id', ['class' => 'mr-2']); ?>
                                    <?= \yii\helpers\Html::dropDownList(
                                        'refperiode_id',
                                        $selectedPeriodId,
                                        \yii\helpers\ArrayHelper::map($periodeList, 'refperiode_id', 'periode'), // Mapping periodeList
                                        [
                                            'class' => 'form-control',
                                            'prompt' => 'Semua Periode'
                                        ]
                                    ); ?>
                                </div>

                                <!-- Dropdown for selecting Page -->
                                <div class="form-group mr-2">
                                    <?= \yii\helpers\Html::label('Pilih Halaman:', 'target_page', ['class' => 'mr-2']); ?>
                                    <?= \yii\helpers\Html::dropDownList(
                                        'target_page',
                                        null,
                                        [
                                            'portal-publik-tabulasi' => 'Tabulasi',
                                            'portal-publik-perencanaan' => 'Perencanaan',
                                            'portal-publik-capkin' => 'Capkin'
                                        ],
                                        [
                                            'class' => 'form-control',
                                            'prompt' => 'Pilih Halaman'
                                        ]
                                    ); ?>
                                </div>

                                <!-- Submit Button -->
                                <div class="form-group">
                                    <?= \yii\helpers\Html::submitButton('Tampilkan', [
                                        'class' => 'btn btn-info',
                                        'style' => 'background: radial-gradient(circle, rgba(81, 196, 248, 1) 0%, rgba(0, 81, 196, 0.83) 83%, rgba(0, 81, 196, 1) 100%);'
                                    ]); ?>
                                </div>

                                <?= \yii\helpers\Html::endForm(); ?>
                            </div>
                        </div>
                    </div>

                    <!--  -->
                    <div class="row">
                        <div class="col-lg-12">
                            <?php foreach ($skpdList as $index => $skpd): ?>
                                <div class="card">
                                    <div class="card-body">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <td rowspan="2" style='white-space: normal;'>Nama SKPD</td>
                                                    <td rowspan="2" style='white-space: normal;'>Capaian Kinerja</td>
                                                    <td rowspan="2" style='white-space: normal;'>Tidak Ada Target</td>
                                                    <td colspan="2" style='white-space: normal;'>Tidak Tercapai</td>
                                                    <td rowspan="2" style='white-space: normal;'>Tercapai</td>
                                                    <td rowspan="2" style='white-space: normal;'>Melebihi Target</td>
                                                    <td rowspan="2" style='white-space: normal;'>Jumlah Indikator</td>
                                                </tr>
                                                <tr>
                                                    <td style='white-space: normal;'>Target (00.00 - 69.99%)</td>
                                                    <td style='white-space: normal;'>Target (70.00 - 99.99%)</td>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td rowspan="6" style='white-space: normal;'><?= Html::encode($skpd->nama_skpd); ?></td>
                                                </tr>
                                                <?php
                                                // Label untuk tiap triwulan
                                                $triwulanLabels = ['Tri 1', 'Tri 2', 'Tri 3', 'Tri 4', 'Kondisi Akhir F'];

                                                // Ambil data sakip_indikatorsasaranrenstra_triwulan berdasarkan refskpd_id
                                                $indikatorData = \frontend\models\SakipIndikatorSasaranRenstraTriwulan::find()
                                                    ->where(['refskpd_id' => $skpd->refskpd_id])
                                                    ->orderBy(['reftriwulan_id' => SORT_ASC])
                                                    ->all();

                                                // Track triwulan yang sudah ada capaian
                                                $terisiTriwulan = [];

                                                // Hitung indikator untuk setiap triwulan
                                                $counts = [];
                                                foreach ($indikatorData as $indikator) {
                                                    $triwulanId = $indikator->reftriwulan_id;

                                                    // Jika sudah ada data untuk triwulan ini, skip triwulan sebelumnya
                                                    if (in_array($triwulanId, $terisiTriwulan)) {
                                                        continue;
                                                    }

                                                    // Tandai triwulan yang memiliki data
                                                    $terisiTriwulan[] = $triwulanId;

                                                    // Inisialisasi
                                                    if (!isset($counts[$triwulanId])) {
                                                        $counts[$triwulanId] = [
                                                            'belum_ada_capaian' => 0,
                                                            'capaian_00_69' => 0,
                                                            'capaian_70_99' => 0,
                                                            'capaian_100' => 0,
                                                            'capaian_lebih_100' => 0,
                                                            'total' => 0,
                                                        ];
                                                    }

                                                    // Proses data indikator
                                                    $counts[$triwulanId]['total']++;
                                                    if (empty($indikator->triwulan_capaian)) {
                                                        $counts[$triwulanId]['belum_ada_capaian']++;
                                                    } else {
                                                        $capaian = floatval($indikator->triwulan_capaian);
                                                        if ($capaian < 70) {
                                                            $counts[$triwulanId]['capaian_00_69']++;
                                                        } elseif ($capaian < 100) {
                                                            $counts[$triwulanId]['capaian_70_99']++;
                                                        } elseif ($capaian == 100) {
                                                            $counts[$triwulanId]['capaian_100']++;
                                                        } else {
                                                            $counts[$triwulanId]['capaian_lebih_100']++;
                                                        }
                                                    }
                                                }

                                                // Tampilkan data di tabel
                                                foreach ($triwulanLabels as $index => $label):
                                                    $triwulanId = $index + 1;
                                                ?>
                                                    <tr>
                                                        <td><?= $label ?></td>
                                                        <?php if (isset($counts[$triwulanId])): ?>
                                                            <td><?= $counts[$triwulanId]['belum_ada_capaian']; ?></td>
                                                            <td><?= $counts[$triwulanId]['capaian_00_69']; ?></td>
                                                            <td><?= $counts[$triwulanId]['capaian_70_99']; ?></td>
                                                            <td><?= $counts[$triwulanId]['capaian_100']; ?></td>
                                                            <td><?= $counts[$triwulanId]['capaian_lebih_100']; ?></td>
                                                            <td>
                                                                <span class="badge badge-info">
                                                                    <?= $counts[$triwulanId]['total']; ?>
                                                                </span>
                                                            </td>
                                                        <?php else: ?>
                                                            <td colspan="6"></td> <!-- Tidak menampilkan apa-apa jika tidak ada data -->
                                                        <?php endif; ?>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>

                                        </table>
                                    </div>
                                </div>
                            <?php endforeach; ?>


                        </div>
                    </div>

                    <!--  -->

                </div>
            </div>

            <!-- Menu list -->

        </div>
    </div>

</div>