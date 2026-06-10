<?php

/** @var yii\web\View $this */

$this->title = 'My Yii Application';
?>
<div class="site-index">
    <div class="p-5 mb-4 bg-transparent rounded-3">
        <div class="container-fluid py-5 text-center">
            <h1 class="display-4">Congratulations!</h1>
            <p class="fs-5 fw-light">You have successfully created your Yii-powered application.</p>
            <p><a class="btn btn-lg btn-success" href="https://www.yiiframework.com">Get started with Yii</a></p>
        </div>
    </div>

    <div class="body-content">
        <!--  -->
        <div class="row">
            <!-- start row -->
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header" style="background-color: #04A9F5; padding: 8px;"> <!-- Sesuaikan padding di sini -->
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
                                    <?= \yii\helpers\Html::submitButton('Tampilkan', ['class' => 'btn btn-primary']); ?>
                                </div>

                                <?= \yii\helpers\Html::endForm(); ?>
                            </div>
                        </div>
                    </div>

                    <!--  -->
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-body">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <td rowspan="2" style='white-space: normal;'>nama_skpd</td>
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
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!--  -->

                </div>
            </div>
        </div>
        <!--  -->
    </div>
</div>