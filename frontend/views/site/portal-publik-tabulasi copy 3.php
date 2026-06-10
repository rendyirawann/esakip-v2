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

$this->registerJsFile('https://cdn.jsdelivr.net/npm/echarts/dist/echarts.min.js', ['depends' => [\yii\web\JqueryAsset::class]]);

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

$dataChart = json_encode($triwulanTerendahPerSkpd);
$chartId = 'triwulan-terendah-pie';

$dataChartTerendah = json_encode($triwulanTerendahPerSkpd);
$dataChartTertinggi = json_encode($triwulanTertinggiPerSkpd);
$chartIdTerendah = 'triwulan-terendah-pie';
$chartIdTertinggi = 'triwulan-tertinggi-pie';
$namaSkpdTerendah = count($triwulanTerendahPerSkpd) > 0 ? $triwulanTerendahPerSkpd[0]['nama'] : 'Tidak ada data';
$namaSkpdTertinggi = count($triwulanTertinggiPerSkpd) > 0 ? $triwulanTertinggiPerSkpd[0]['nama'] : 'Tidak ada data';

$this->registerJs("
function buildProgramList(programs) {
    if (!programs || programs.length === 0) {
        return '<p class=\"mb-0 text-muted\" style=\"font-style: italic;\">Tidak ada program yang terkait.</p>';
    }

    let listHtml = '<ul class=\"mb-0 ps-4\">';
    programs.forEach(function(programName) {
        listHtml += '<li>- ' + programName + '.</li>';
    });
    listHtml += '</ul>';
    return listHtml;
}

// [BARU] Fungsi bantuan untuk membuat daftar HTML dari array kegiatan
function buildKegiatanList(kegiatans) {
    if (!kegiatans || kegiatans.length === 0) {
        return '<p class=\"mb-0 text-muted\" style=\"font-style: italic;\">Tidak ada Kegiatan yang terkait.</p>';
    }

    let listHtml = '<ul class=\"mb-0 ps-4\">';
    kegiatans.forEach(function(kegiatanName) {
        listHtml += '<li>- ' + kegiatanName + '.</li>';
    });
    listHtml += '</ul>';
    return listHtml;
}

// [BARU] Fungsi bantuan untuk membuat daftar HTML dari array sub kegiatan
function buildSubkegiatanList(subkegiatans) {
    if (!subkegiatans || subkegiatans.length === 0) {
        return '<p class=\"mb-0 text-muted\" style=\"font-style: italic;\">Tidak ada Sub Kegiatan yang terkait.</p>';
    }

    let listHtml = '<ul class=\"mb-0 ps-4\">';
    subkegiatans.forEach(function(subkegiatanName) {
        listHtml += '<li>- ' + subkegiatanName + '.</li>';
    });
    listHtml += '</ul>';
    return listHtml;
}

    var chartDom1 = document.getElementById('$chartIdTerendah');
    var myChart1 = echarts.init(chartDom1);
    var option1 = {
        title: {
            text: 'Indikator Sasaran - Capaian Terendah Semua Triwulan',
            left: 'center'
        },
        tooltip: {
            trigger: 'item',
            // Menampilkan nama SKPD dan persen capaian
            formatter: function(params) {
                var namaSkpd = params.data.nama;  // Ambil nama SKPD dari data
                var value = params.value;
                var percent = params.percent;
return namaSkpd + '<br>' + 'Capaian: ' + value + '%';

            }
        },
        legend: {
            orient: 'vertical',
            left: 'left',
            type: 'scroll' // Tambahkan ini jika nama SKPD terlalu panjang
        },
        series: [{
            name: 'Capaian',
            type: 'pie',
            radius: '50%',
            data: $dataChartTerendah,
            label: {
                // Menampilkan nama SKPD dan persen capaian pada label
                formatter: function(params) {
                    var namaSkpd = params.data.nama;
                    var value = params.value;
                    return namaSkpd + ': ' + value + '%';
                }
            },
            emphasis: {
                itemStyle: {
                    shadowBlur: 10,
                    shadowOffsetX: 0,
                    shadowColor: 'rgba(0, 0, 0, 0.5)'
                }
            }
        }]
    };
    myChart1.setOption(option1);

    // [!!!] TAMBAHAN: EVENT LISTENER UNTUK KLIK PADA CHART TERENDAH
    myChart1.on('click', function(params) {
        var detailContainer = document.getElementById('detail-indikator-terendah');
        // 'params.data' berisi objek dari array $dataChartTerendah {name, value, indikator}
        if (params.data && params.data.indikator) {
            var nama = params.data.nama;
            var indikator = params.data.indikator;
            var capaian = params.value;
            var programs = params.data.programs; // Ambil data program
            var kegiatans = params.data.kegiatans; // Ambil data kegiatan
            var subkegiatans = params.data.subkegiatans; // Ambil data sub kegiatan

        var programHtml = buildProgramList(programs); // Buat daftar HTML
        var kegiatanHtml = buildKegiatanList(kegiatans); // Buat daftar HTML
        var subkegiatanHtml = buildSubkegiatanList(subkegiatans); // Buat daftar HTML

            detailContainer.innerHTML = '<h6>' + nama + '</h6>' +
                                        '<p class=\"mb-1\"><strong>Indikator:</strong> ' + indikator + '</p>' +
                                        '<p class=\"mb-0\"><strong>Capaian:</strong> ' + capaian + '.</p>' +
                                        '<strong class=\"d-block\"><strong>Terkait pada Program:</strong> ' + programHtml + '.</strong>' +
                                        '<strong class=\"d-block\"><strong>Terkait pada Kegiatan:</strong> ' + kegiatanHtml + '.</strong>' +
                                        '<strong class=\"d-block\"><strong>Terkait pada Sub Kegiatan:</strong> ' + subkegiatanHtml + '.</strong>'
        }
    });

    var chartDom2 = document.getElementById('$chartIdTertinggi');
    var myChart2 = echarts.init(chartDom2);
    var option2 = {
        title: {
            text: 'Indikator Sasaran - Capaian Tertinggi Semua Triwulan',
            left: 'center'
        },
        tooltip: {
            trigger: 'item',
            // Menampilkan nama SKPD dan persen capaian
            formatter: function(params) {
                var namaSkpd = params.data.nama;  // Ambil nama SKPD dari data
                var value = params.value;
                var percent = params.percent;
return namaSkpd + '<br>' + 'Capaian: ' + value + '%';

            }
        },
        legend: {
            orient: 'vertical',
            left: 'left',
            type: 'scroll' // Tambahkan ini jika nama SKPD terlalu panjang
        },
        series: [{
            name: 'Capaian',
            type: 'pie',
            radius: '50%',
            data: $dataChartTertinggi,
            label: {
                // Menampilkan nama SKPD dan persen capaian pada label
                formatter: function(params) {
                    var namaSkpd = params.data.nama;
                    var value = params.value;
                    return namaSkpd + ': ' + value + '%';
                }
            },
            emphasis: {
                itemStyle: {
                    shadowBlur: 10,
                    shadowOffsetX: 0,
                    shadowColor: 'rgba(0, 0, 0, 0.5)'
                }
            }
        }]
    };
    myChart2.setOption(option2);

    // [!!!] TAMBAHAN: EVENT LISTENER UNTUK KLIK PADA CHART TERTINGGI
    myChart2.on('click', function(params) {
        var detailContainer = document.getElementById('detail-indikator-tertinggi');
        // 'params.data' berisi objek dari array $dataChartTertinggi {name, value, indikator}
        if (params.data && params.data.indikator) {
            var nama = params.data.nama;
            var indikator = params.data.indikator;
            var capaian = params.value;
            var programs = params.data.programs; // Ambil data program
            var kegiatans = params.data.kegiatans; // Ambil data kegiatan
            var subkegiatans = params.data.subkegiatans; // Ambil data subkegiatan

        var programHtml = buildProgramList(programs); // Buat daftar HTML
        var kegiatanHtml = buildKegiatanList(kegiatans); // Buat daftar HTML
        var subkegiatanHtml = buildSubkegiatanList(subkegiatans); // Buat daftar HTML

            detailContainer.innerHTML = '<h6>' + nama + '</h6>' +
                                        '<p class=\"mb-1\"><strong>Indikator:</strong> ' + indikator + '</p>' +
                                        '<p class=\"mb-0\"><strong>Capaian:</strong> ' + capaian + '.</p>' +
                                        '<strong class=\"d-block\"><strong>Terkait pada Program:</strong> ' + programHtml + '.</strong>' +
                                        '<strong class=\"d-block\"><strong>Terkait pada Kegiatan:</strong> ' + kegiatanHtml + '.</strong>' +
                                        '<strong class=\"d-block\"><strong>Terkait pada Sub Kegiatan:</strong> ' + subkegiatanHtml + '.</strong>'
        }
    });

    // Menyesuaikan ukuran chart saat window di-resize
    window.addEventListener('resize', function() {
        myChart1.resize();
        myChart2.resize();
    });
");

$dataChartProgramTerendah = json_encode($triwulanTerendahProgram);
$dataChartProgramTertinggi = json_encode($triwulanTertinggiProgram);
$chartIdProgramTerendah = 'triwulan-terendah-program-pie';
$chartIdProgramTertinggi = 'triwulan-tertinggi-program-pie';

$namaProgramTerendah = count($triwulanTerendahProgram) > 0 ? $triwulanTerendahProgram[0]['nama'] : 'Tidak ada data';
$namaProgramTertinggi = count($triwulanTertinggiProgram) > 0 ? $triwulanTertinggiProgram[0]['nama'] : 'Tidak ada data';

$this->registerJs("
function handleProgramChartClick(params, detailContainerId) {
    var detailContainer = document.getElementById(detailContainerId);
    if (!params.data || !params.data.indikator) {
        return; // Keluar jika tidak ada data valid
    }

    // Ambil semua data dari chart
    var nama = params.data.nama;
    var indikator = params.data.indikator;
    var capaian = params.value;
    var programName = params.data.program_name; // Ambil nama program

    // Buat blok HTML untuk ditampilkan
    detailContainer.innerHTML = '<h6>' + nama + '</h6>' +
        '<p class=\"mb-1\"><strong>Program:</strong> ' + programName + '</p>' +
        '<p class=\"mb-1\"><strong>Indikator:</strong> ' + indikator + '</p>' +
        '<p class=\"mb-1\"><strong>Capaian:</strong> ' + capaian + '%</p>';
}
        
    var chartDomProgram1 = document.getElementById('$chartIdProgramTerendah');
    var myChartProgram1 = echarts.init(chartDomProgram1);
    var optionProgram1 = {
        title: {
            text: 'Indikator Program - Capaian Terendah Semua Triwulan',
            left: 'center'
        },
        tooltip: {
            trigger: 'item',
            formatter: function(params) {
                var nama = params.data.nama;
                var value = params.value;
                return nama + '<br>' + 'Capaian: ' + value + '%';
            }
        },
        legend: {
            orient: 'vertical',
            left: 'left'
        },
        series: [{
            name: 'Capaian',
            type: 'pie',
            radius: '50%',
            data: $dataChartProgramTerendah,
            label: {
                formatter: function(params) {
                    var nama = params.data.nama;
                    var value = params.value;
                    return nama + ': ' + value + '%';
                }
            },
            emphasis: {
                itemStyle: {
                    shadowBlur: 10,
                    shadowOffsetX: 0,
                    shadowColor: 'rgba(0, 0, 0, 0.5)'
                }
            }
        }]
    };
    myChartProgram1.setOption(optionProgram1);
    myChartProgram1.on('click', function(params) { handleProgramChartClick(params, 'detail-indikator-program-terendah'); });


    var chartDomProgram2 = document.getElementById('$chartIdProgramTertinggi');
    var myChartProgram2 = echarts.init(chartDomProgram2);
    var optionProgram2 = {
        title: {
            text: 'Indikator Program - Capaian Tertinggi Semua Triwulan',
            left: 'center'
        },
        tooltip: {
            trigger: 'item',
            formatter: function(params) {
                var nama = params.data.nama;
                var value = params.value;
                return nama + '<br>' + 'Capaian: ' + value + '%';
            }
        },
        legend: {
            orient: 'vertical',
            left: 'left'
        },
        series: [{
            name: 'Capaian',
            type: 'pie',
            radius: '50%',
            data: $dataChartProgramTertinggi,
            label: {
                formatter: function(params) {
                    var nama = params.data.nama;
                    var value = params.value;
                    return nama + ': ' + value + '%';
                }
            },
            emphasis: {
                itemStyle: {
                    shadowBlur: 10,
                    shadowOffsetX: 0,
                    shadowColor: 'rgba(0, 0, 0, 0.5)'
                }
            }
        }]
    };
    myChartProgram2.setOption(optionProgram2);
    myChartProgram2.on('click', function(params) { handleProgramChartClick(params, 'detail-indikator-program-tertinggi'); });

    // --- Event listener untuk resize window ---
window.addEventListener('resize', function() {
    myChartProgram1.resize();
    myChartProgram2.resize();
});
");

$dataChartKegiatanTertinggi = json_encode($triwulanTertinggiKegiatan);
$dataChartKegiatanTerendah = json_encode($triwulanTerendahKegiatan);
$chartIdKegiatanTertinggi = 'triwulan-tertinggi-kegiatan-pie';
$chartIdKegiatanTerendah = 'triwulan-terendah-kegiatan-pie';

$this->registerJs("
    var chartDomKeg1 = document.getElementById('$chartIdKegiatanTerendah');
    var myChartKeg1 = echarts.init(chartDomKeg1);
    var optionKeg1 = {
        title: {
            text: 'Indikator Kegiatan - Capaian Terendah Semua Triwulan',
            left: 'center'
        },
        tooltip: {
            trigger: 'item',
            formatter: function(params) {
                var nama = params.data.nama;
                var value = params.value;
                return nama + '<br>Capaian: ' + value + '%';
            }
        },
        legend: {
            orient: 'vertical',
            left: 'left'
        },
        series: [{
            name: 'Capaian',
            type: 'pie',
            radius: '50%',
            data: $dataChartKegiatanTerendah,
            label: {
                formatter: function(params) {
                    var nama = params.data.nama;
                    var value = params.value;
                    return nama + ': ' + value + '%';
                }
            },
            emphasis: {
                itemStyle: {
                    shadowBlur: 10,
                    shadowOffsetX: 0,
                    shadowColor: 'rgba(0, 0, 0, 0.5)'
                }
            }
        }]
    };
    myChartKeg1.setOption(optionKeg1);

    var chartDomKeg2 = document.getElementById('$chartIdKegiatanTertinggi');
    var myChartKeg2 = echarts.init(chartDomKeg2);
    var optionKeg2 = {
        title: {
            text: 'Indikator Kegiatan - Capaian Tertinggi Semua Triwulan',
            left: 'center'
        },
        tooltip: {
            trigger: 'item',
            formatter: function(params) {
                var nama = params.data.nama;
                var value = params.value;
                return nama + '<br>Capaian: ' + value + '%';
            }
        },
        legend: {
            orient: 'vertical',
            left: 'left'
        },
        series: [{
            name: 'Capaian',
            type: 'pie',
            radius: '50%',
            data: $dataChartKegiatanTertinggi,
            label: {
                formatter: function(params) {
                    var nama = params.data.nama;
                    var value = params.value;
                    return nama + ': ' + value + '%';
                }
            },
            emphasis: {
                itemStyle: {
                    shadowBlur: 10,
                    shadowOffsetX: 0,
                    shadowColor: 'rgba(0, 0, 0, 0.5)'
                }
            }
        }]
    };
    myChartKeg2.setOption(optionKeg2);
");

$dataChartSubKegiatanTertinggi = json_encode($triwulanTertinggiSubkegiatan);
$dataChartSubKegiatanTerendah = json_encode($triwulanTerendahSubkegiatan);
$chartIdSubKegiatanTertinggi = 'triwulan-tertinggi-subkegiatan-pie';
$chartIdSubKegiatanTerendah = 'triwulan-terendah-subkegiatan-pie';

$this->registerJs("
    var chartDomSubKeg1 = document.getElementById('$chartIdSubKegiatanTerendah');
    var myChartSubKeg1 = echarts.init(chartDomSubKeg1);
    var optionSubKeg1 = {
        title: {
            text: 'Indikator Sub Kegiatan - Capaian Terendah Semua Triwulan',
            left: 'center'
        },
        tooltip: {
            trigger: 'item',
            formatter: function(params) {
                var nama = params.data.nama;
                var value = params.value;
                return nama + '<br>Capaian: ' + value + '%';
            }
        },
        legend: {
            orient: 'vertical',
            left: 'left'
        },
        series: [{
            name: 'Capaian',
            type: 'pie',
            radius: '50%',
            data: $dataChartSubKegiatanTerendah,
            label: {
                formatter: function(params) {
                    var nama = params.data.nama;
                    var value = params.value;
                    return nama + ': ' + value + '%';
                }
            },
            emphasis: {
                itemStyle: {
                    shadowBlur: 10,
                    shadowOffsetX: 0,
                    shadowColor: 'rgba(0, 0, 0, 0.5)'
                }
            }
        }]
    };
    myChartSubKeg1.setOption(optionSubKeg1);

    var chartDomSubKeg2 = document.getElementById('$chartIdSubKegiatanTertinggi');
    var myChartSubKeg2 = echarts.init(chartDomSubKeg2);
    var optionSubKeg2 = {
        title: {
            text: 'Indikator Sub Kegiatan - Capaian Tertinggi Semua Triwulan',
            left: 'center'
        },
        tooltip: {
            trigger: 'item',
            formatter: function(params) {
                var nama = params.data.nama;
                var value = params.value;
                return nama + '<br>Capaian: ' + value + '%';
            }
        },
        legend: {
            orient: 'vertical',
            left: 'left'
        },
        series: [{
            name: 'Capaian',
            type: 'pie',
            radius: '50%',
            data: $dataChartSubKegiatanTertinggi,
            label: {
                formatter: function(params) {
                    var nama = params.data.nama;
                    var value = params.value;
                    return nama + ': ' + value + '%';
                }
            },
            emphasis: {
                itemStyle: {
                    shadowBlur: 10,
                    shadowOffsetX: 0,
                    shadowColor: 'rgba(0, 0, 0, 0.5)'
                }
            }
        }]
    };
    myChartSubKeg2.setOption(optionSubKeg2);
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
                        <div class="col-lg-12 mb-3">
                            <div class="card shadow-sm border-0">
                                <div class="card-body">
                                    <!-- <h5 class="card-title mb-3">Visualisasi Capaian Indikator</h5> -->

                                    <div class="tabel-wrapper" id="visualisasi-wrapper">
                                        <!-- Tabel 1: Indikator Sasaran -->
                                        <div class="tabel-content" data-index="0">
                                            <h5 class="fw-bold">Visualisasi Capaian Indikator Sasaran</h5>
                                            <div class="row">
                                                <div class="col-lg-6 mb-4">
                                                    <div class="card shadow-sm border-0">
                                                        <div class="card-body">
                                                            <h6 class="card-title">Capaian Terendah</h6>
                                                            <?php
                                                            // Buat map triwulan_id => data atau null
                                                            $mapTerendah = [];
                                                            foreach ($triwulanTerendahPerSkpd as $item) {
                                                                preg_match('/Triwulan (\d+)/', $item['nama'], $matches);
                                                                if (isset($matches[1])) {
                                                                    $mapTerendah[(int)$matches[1]] = $item;
                                                                }
                                                            }

                                                            foreach (range(1, 4) as $triwulanId): ?>
                                                                <?php $item = $mapTerendah[$triwulanId] ?? null; ?>
                                                                <p><strong>SKPD:</strong> <?= ($item && $item['value'] > 0) ? $item['nama'] : 'Belum ada capaian' ?></p>
                                                                <?php if ($item): ?>
                                                                    <p><strong>Indikator:</strong> <?= $item['indikator'] ?></p>
                                                                <?php endif; ?>
                                                            <?php endforeach; ?>
                                                            <div id="<?= $chartIdTerendah ?>" class="chart-container" style="width:100%; height:400px;"></div>

                                                            <div id="detail-indikator-terendah" class="mt-3 p-2 border rounded bg-light" style="min-height: 80px;">
                                                                <p class="text-muted mb-0" style="font-style: italic;">Klik pada salah satu bagian pie chart untuk melihat detail indikator.</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 mb-4">
                                                    <div class="card shadow-sm border-0">
                                                        <div class="card-body">
                                                            <h6 class="card-title">Capaian Tertinggi</h6>
                                                            <?php
                                                            // Buat map triwulan_id => data atau null
                                                            $mapTertinggi = [];
                                                            foreach ($triwulanTertinggiPerSkpd as $item) {
                                                                preg_match('/Triwulan (\d+)/', $item['nama'], $matches);
                                                                if (isset($matches[1])) {
                                                                    $mapTertinggi[(int)$matches[1]] = $item;
                                                                }
                                                            }

                                                            foreach (range(1, 4) as $triwulanId): ?>
                                                                <?php $item = $mapTertinggi[$triwulanId] ?? null; ?>
                                                                <p><strong>SKPD:</strong> <?= ($item && $item['value'] > 0) ? $item['nama'] : 'Belum ada capaian' ?></p>
                                                                <?php if ($item): ?>
                                                                    <p><strong>Indikator:</strong> <?= $item['indikator'] ?></p>
                                                                <?php endif; ?>
                                                            <?php endforeach; ?>
                                                            <div id="<?= $chartIdTertinggi ?>" class="chart-container" style="width:100%; height:400px;"></div>

                                                            <div id="detail-indikator-tertinggi" class="mt-3 p-2 border rounded bg-light" style="min-height: 80px;">
                                                                <p class="text-muted mb-0" style="font-style: italic;">Klik pada salah satu bagian pie chart untuk melihat detail indikator.</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Tabel 2: Indikator Program -->
                                        <div class="tabel-content d-none" data-index="1">
                                            <h5 class="fw-bold">Visualisasi Capaian Indikator Program</h5>
                                            <div class="row">
                                                <div class="col-md-6 mb-4">
                                                    <div class="card shadow-sm border-0">
                                                        <div class="card-body">
                                                            <h6 class="card-title">Capaian Terendah</h6>
                                                            <?php
                                                            // Buat map triwulan_id => data atau null
                                                            $mapTerendahProgram = [];
                                                            foreach ($triwulanTerendahProgram as $item) {
                                                                preg_match('/Triwulan (\d+)/', $item['nama'], $matches);
                                                                if (isset($matches[1])) {
                                                                    $mapTerendahProgram[(int)$matches[1]] = $item;
                                                                }
                                                            }

                                                            foreach (range(1, 4) as $triwulanId): ?>
                                                                <?php $item = $mapTerendahProgram[$triwulanId] ?? null; ?>
                                                                <p><strong>SKPD:</strong> <?= ($item && $item['value'] > 0) ? $item['nama'] : 'Belum ada capaian' ?></p>
                                                                <?php if ($item): ?>
                                                                    <p><strong>Indikator:</strong> <?= $item['indikator'] ?></p>
                                                                <?php endif; ?>
                                                            <?php endforeach; ?>
                                                            <div id="<?= $chartIdProgramTerendah ?>" class="chart-container" style="width:100%; height:400px;"></div>

                                                            <div id="detail-indikator-program-terendah" class="mt-3 p-2 border rounded bg-light" style="min-height: 80px;">
                                                                <p class="text-muted mb-0" style="font-style: italic;">Klik pada salah satu bagian pie chart untuk melihat detail indikator program.</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 mb-4">
                                                    <div class="card shadow-sm border-0">
                                                        <div class="card-body">
                                                            <h6 class="card-title">Capaian Tertinggi</h6>
                                                            <?php
                                                            // Buat map triwulan_id => data atau null
                                                            $mapTertinggiProgram = [];
                                                            foreach ($triwulanTertinggiProgram as $item) {
                                                                preg_match('/Triwulan (\d+)/', $item['nama'], $matches);
                                                                if (isset($matches[1])) {
                                                                    $mapTertinggiProgram[(int)$matches[1]] = $item;
                                                                }
                                                            }

                                                            foreach (range(1, 4) as $triwulanId): ?>
                                                                <?php $item = $mapTertinggiProgram[$triwulanId] ?? null; ?>
                                                                <p><strong>SKPD:</strong> <?= ($item && $item['value'] > 0) ? $item['nama'] : 'Belum ada capaian' ?></p>
                                                                <?php if ($item): ?>
                                                                    <p><strong>Indikator:</strong> <?= $item['indikator'] ?></p>
                                                                <?php endif; ?>
                                                            <?php endforeach; ?>
                                                            <div id="<?= $chartIdProgramTertinggi ?>" class="chart-container" style="width:100%; height:400px;"></div>

                                                            <div id="detail-indikator-program-tertinggi" class="mt-3 p-2 border rounded bg-light" style="min-height: 80px;">
                                                                <p class="text-muted mb-0" style="font-style: italic;">Klik pada salah satu bagian pie chart untuk melihat detail indikator program.</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Tabel 2: Indikator Kegiatan -->
                                        <div class="tabel-content d-none" data-index="2">
                                            <h5 class="fw-bold">Visualisasi Capaian Indikator Kegiatan</h5>
                                            <div class="row">
                                                <div class="col-md-6 mb-4">
                                                    <div class="card shadow-sm border-0">
                                                        <div class="card-body">
                                                            <h6 class="card-title">Capaian Terendah</h6>
                                                            <?php
                                                            // Buat map triwulan_id => data atau null
                                                            $mapTerendahKegiatan = [];
                                                            foreach ($triwulanTerendahKegiatan as $item) {
                                                                preg_match('/Triwulan (\d+)/', $item['nama'], $matches);
                                                                if (isset($matches[1])) {
                                                                    $mapTerendahKegiatan[(int)$matches[1]] = $item;
                                                                }
                                                            }

                                                            foreach (range(1, 4) as $triwulanId): ?>
                                                                <?php $item = $mapTerendahKegiatan[$triwulanId] ?? null; ?>
                                                                <p><strong>SKPD:</strong> <?= ($item && $item['value'] > 0) ? $item['nama'] : 'Belum ada capaian' ?></p>
                                                                <?php if ($item): ?>
                                                                    <p><strong>Indikator:</strong> <?= $item['indikator'] ?></p>
                                                                <?php endif; ?>
                                                            <?php endforeach; ?>
                                                            <div id="<?= $chartIdKegiatanTerendah ?>" class="chart-container" style="width:100%; height:400px;"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 mb-4">
                                                    <div class="card shadow-sm border-0">
                                                        <div class="card-body">
                                                            <h6 class="card-title">Capaian Tertinggi</h6>
                                                            <?php
                                                            // Buat map triwulan_id => data atau null
                                                            $mapTertinggiKegiatan = [];
                                                            foreach ($triwulanTertinggiKegiatan as $item) {
                                                                preg_match('/Triwulan (\d+)/', $item['nama'], $matches);
                                                                if (isset($matches[1])) {
                                                                    $mapTertinggiKegiatan[(int)$matches[1]] = $item;
                                                                }
                                                            }

                                                            foreach (range(1, 4) as $triwulanId): ?>
                                                                <?php $item = $mapTertinggiKegiatan[$triwulanId] ?? null; ?>
                                                                <p><strong>SKPD:</strong> <?= ($item && $item['value'] > 0) ? $item['nama'] : 'Belum ada capaian' ?></p>
                                                                <?php if ($item): ?>
                                                                    <p><strong>Indikator:</strong> <?= $item['indikator'] ?></p>
                                                                <?php endif; ?>
                                                            <?php endforeach; ?>
                                                            <div id="<?= $chartIdKegiatanTertinggi ?>" class="chart-container" style="width:100%; height:400px;"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Tabel 2: Indikator Sub Kegiatan -->
                                        <div class="tabel-content d-none" data-index="3">
                                            <h5 class="fw-bold">Visualisasi Capaian Indikator Sub Kegiatan</h5>
                                            <div class="row">
                                                <div class="col-md-6 mb-4">
                                                    <div class="card shadow-sm border-0">
                                                        <div class="card-body">
                                                            <h6 class="card-title">Capaian Terendah</h6>
                                                            <?php
                                                            // Buat map triwulan_id => data atau null
                                                            $mapTerendahSubKegiatan = [];
                                                            foreach ($triwulanTerendahSubkegiatan as $item) {
                                                                preg_match('/Triwulan (\d+)/', $item['nama'], $matches);
                                                                if (isset($matches[1])) {
                                                                    $mapTerendahSubKegiatan[(int)$matches[1]] = $item;
                                                                }
                                                            }

                                                            foreach (range(1, 4) as $triwulanId):
                                                                $item = $mapTerendahSubKegiatan[$triwulanId] ?? null;
                                                            ?>
                                                                <p>
                                                                    <strong>Sub Kegiatan dengan capaian terendah:</strong>
                                                                    <?= ($item && $item['value'] > 0) ? $item['nama'] : 'Belum ada capaian' ?>
                                                                </p>
                                                                <?php if ($item): ?>
                                                                    <p><strong>Indikator Terendah:</strong> <?= $item['indikator'] ?></p>
                                                                <?php endif; ?>
                                                            <?php endforeach; ?>
                                                            <div id="<?= $chartIdSubKegiatanTerendah  ?>" class="chart-container" style="width:100%; height:400px;"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 mb-4">
                                                    <div class="card shadow-sm border-0">
                                                        <div class="card-body">
                                                            <h6 class="card-title">Capaian Tertinggi</h6>
                                                            <?php
                                                            // Buat map triwulan_id => data atau null
                                                            $mapTertinggiSubKegiatan = [];
                                                            foreach ($triwulanTertinggiSubkegiatan as $item) {
                                                                preg_match('/Triwulan (\d+)/', $item['nama'], $matches);
                                                                if (isset($matches[1])) {
                                                                    $mapTertinggiSubKegiatan[(int)$matches[1]] = $item;
                                                                }
                                                            }

                                                            foreach (range(1, 4) as $triwulanId):
                                                                $item = $mapTertinggiSubKegiatan[$triwulanId] ?? null;
                                                            ?>
                                                                <p>
                                                                    <strong>Sub Kegiatan dengan capaian tertinggi:</strong>
                                                                    <?= ($item && $item['value'] > 0) ? $item['nama'] : 'Belum ada capaian' ?>
                                                                </p>
                                                                <?php if ($item): ?>
                                                                    <p><strong>Indikator Tertinggi:</strong> <?= $item['indikator'] ?></p>
                                                                <?php endif; ?>
                                                            <?php endforeach; ?>
                                                            <div id="<?= $chartIdSubKegiatanTertinggi  ?>" class="chart-container" style="width:100%; height:400px;"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Tabel 3 & 4: Tambah bagian untuk Kegiatan & Sub Kegiatan jika tersedia -->

                                        <!-- Tombol Navigasi -->
                                        <div class="d-flex justify-content-between mt-2">
                                            <button class="btn btn-outline-primary btn-prev" data-scope="visualisasi-wrapper"><i class="bi bi-arrow-left"></i> Sebelumnya</button>
                                            <button class="btn btn-outline-primary btn-next" data-scope="visualisasi-wrapper">Selanjutnya <i class="bi bi-arrow-right"></i></button>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    document.addEventListener("DOMContentLoaded", function() {
                        function resizeCharts(wrapper) {
                            const charts = wrapper.querySelectorAll(".echart, canvas"); // atau selector chart Anda
                            charts.forEach(chartEl => {
                                if (chartEl._chartInstance) {
                                    chartEl._chartInstance.resize?.(); // untuk Chart.js
                                } else if (chartEl.id && echarts.getInstanceByDom(chartEl)) {
                                    echarts.getInstanceByDom(chartEl).resize(); // untuk ECharts
                                }
                            });
                        }

                        document.querySelectorAll(".btn-next").forEach(btn => {
                            btn.addEventListener("click", function() {
                                const scope = this.dataset.scope;
                                const wrapper = document.getElementById(scope);
                                const current = wrapper.querySelector(".tabel-content:not(.d-none)");
                                const next = current.nextElementSibling;
                                if (next && next.classList.contains("tabel-content")) {
                                    current.classList.add("d-none");
                                    next.classList.remove("d-none");

                                    // Resize semua chart yang ada di dalam elemen yang baru tampil
                                    next.querySelectorAll(".chart-container").forEach(chartEl => {
                                        const chartInstance = echarts.getInstanceByDom(chartEl);
                                        if (chartInstance) chartInstance.resize();
                                    });
                                }
                            });
                        });

                        document.querySelectorAll(".btn-prev").forEach(btn => {
                            btn.addEventListener("click", function() {
                                const scope = this.dataset.scope;
                                const wrapper = document.getElementById(scope);
                                const current = wrapper.querySelector(".tabel-content:not(.d-none)");
                                const prev = current.previousElementSibling;
                                if (prev && prev.classList.contains("tabel-content")) {
                                    current.classList.add("d-none");
                                    prev.classList.remove("d-none");
                                    resizeCharts(prev);
                                }
                            });
                        });
                    });
                </script>






                <div class="card-body p-3">
                    <div class="row">
                        <?php if (!empty($skpdList)): ?>
                            <?php foreach ($skpdList as $index => $skpd): ?>
                                <?php if ($skpd->refskpd_id == 1) continue; ?>
                                <div class="col-lg-12 mb-3">
                                    <div class="card shadow-sm border-0">
                                        <div class="card-body">
                                            <h5 class="card-title mb-3"><?= Html::encode($skpd->nama_skpd) ?></h5>

                                            <div class="tabel-wrapper" id="tabel-wrapper-<?= $index ?>">
                                                <!-- Tabel 1: Sasaran -->
                                                <div class="tabel-content" data-index="0">
                                                    <p class="fw-bold">Tabel Indikator Sasaran</p>
                                                    <div class="mb-2 clearfix">
                                                        <div class="float-end">
                                                            <strong>Keterangan:</strong>
                                                            <div class="d-flex gap-2 mt-1 flex-wrap">
                                                                <span class="badge bg-danger px-3 py-2">&lt; 70%</span>
                                                                <span class="badge bg-warning text-dark px-3 py-2">70% - 99%</span>
                                                                <span class="badge bg-success px-3 py-2">100%</span>
                                                                <span class="badge bg-primary px-3 py-2">&gt; 100%</span>
                                                            </div>
                                                        </div>
                                                    </div>


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
                                                                                $colorClass = '';

                                                                                if ($capaian < 70) {
                                                                                    $colorClass = 'bg-danger text-white';
                                                                                } elseif ($capaian >= 70 && $capaian < 100) {
                                                                                    $colorClass = 'bg-warning text-dark';
                                                                                } elseif ($capaian == 100) {
                                                                                    $colorClass = 'bg-success text-white';
                                                                                } elseif ($capaian > 100) {
                                                                                    $colorClass = 'bg-primary text-white';
                                                                                }

                                                                                echo '<td><span class="badge ' . $colorClass . '"><strong>' . $capaian . '</strong> (' . $satuan . ')</span></td>';
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

                                                <!-- Tabel 2: Program -->
                                                <div class="tabel-content d-none" data-index="1">
                                                    <p class="fw-bold">Tabel Indikator Program</p>
                                                    <div class="mb-2 clearfix">
                                                        <div class="float-end">
                                                            <strong>Keterangan:</strong>
                                                            <div class="d-flex gap-2 mt-1 flex-wrap">
                                                                <span class="badge bg-danger px-3 py-2">&lt; 70%</span>
                                                                <span class="badge bg-warning text-dark px-3 py-2">70% - 99%</span>
                                                                <span class="badge bg-success px-3 py-2">100%</span>
                                                                <span class="badge bg-primary px-3 py-2">&gt; 100%</span>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="table-responsive">
                                                        <table class="table table-bordered table-sm table-striped align-middle text-center">
                                                            <thead class="table-secondary">
                                                                <tr>
                                                                    <th colspan="10" class="text-center"><?= Html::encode($skpd->nama_skpd) ?></th>
                                                                </tr>
                                                                <tr>
                                                                    <th>No</th>
                                                                    <th>Indikator Program</th>
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
                                                                $programData = $dataProgram[$skpd->refskpd_id] ?? [];
                                                                $no = 1;

                                                                foreach ($programData as $programItem):
                                                                    $indikatorList = $programItem['indikator'];
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
                                                                            $satuan = Html::encode($indikatorItem['program_satuan'] ?? '');

                                                                            echo '<td>' . ($realisasi !== '' ? $realisasi : '-') . '</td>';

                                                                            if ($realisasi === '' || $realisasi === null || $realisasi == 0) {
                                                                                echo '<td><span class="badge bg-danger">Belum ada capaian</span></td>';
                                                                            } else {
                                                                                $colorClass = '';

                                                                                if ($capaian < 70) {
                                                                                    $colorClass = 'bg-danger text-white';
                                                                                } elseif ($capaian >= 70 && $capaian < 100) {
                                                                                    $colorClass = 'bg-warning text-dark';
                                                                                } elseif ($capaian == 100) {
                                                                                    $colorClass = 'bg-success text-white';
                                                                                } elseif ($capaian > 100) {
                                                                                    $colorClass = 'bg-primary text-white';
                                                                                }

                                                                                echo '<td><span class="badge ' . $colorClass . '"><strong>' . $capaian . '</strong> (' . $satuan . ')</span></td>';
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

                                                <!-- Tabel 3: Kegiatan -->
                                                <div class="tabel-content d-none" data-index="2">
                                                    <p class="fw-bold">Tabel Indikator Kegiatan</p>
                                                    <div class="mb-2 clearfix">
                                                        <div class="float-end">
                                                            <strong>Keterangan:</strong>
                                                            <div class="d-flex gap-2 mt-1 flex-wrap">
                                                                <span class="badge bg-danger px-3 py-2">&lt; 70%</span>
                                                                <span class="badge bg-warning text-dark px-3 py-2">70% - 99%</span>
                                                                <span class="badge bg-success px-3 py-2">100%</span>
                                                                <span class="badge bg-primary px-3 py-2">&gt; 100%</span>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="table-responsive">
                                                        <table class="table table-bordered table-sm table-striped align-middle text-center">
                                                            <thead class="table-secondary">
                                                                <tr>
                                                                    <th colspan="10" class="text-center"><?= Html::encode($skpd->nama_skpd) ?></th>
                                                                </tr>
                                                                <tr>
                                                                    <th>No</th>
                                                                    <th>Indikator Kegiatan</th>
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
                                                                $kegiatanData = $dataKegiatan[$skpd->refskpd_id] ?? [];

                                                                $no = 1;

                                                                foreach ($kegiatanData as $kegiatanItem):
                                                                    $indikatorList = $kegiatanItem['indikator'];
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
                                                                            $satuan = Html::encode($indikatorItem['kegiatan_satuan'] ?? '');

                                                                            echo '<td>' . ($realisasi !== '' ? $realisasi : '-') . '</td>';

                                                                            if ($realisasi === '' || $realisasi === null || $realisasi == 0) {
                                                                                echo '<td><span class="badge bg-danger">Belum ada capaian</span></td>';
                                                                            } else {
                                                                                $colorClass = '';

                                                                                if ($capaian < 70) {
                                                                                    $colorClass = 'bg-danger text-white';
                                                                                } elseif ($capaian >= 70 && $capaian < 100) {
                                                                                    $colorClass = 'bg-warning text-dark';
                                                                                } elseif ($capaian == 100) {
                                                                                    $colorClass = 'bg-success text-white';
                                                                                } elseif ($capaian > 100) {
                                                                                    $colorClass = 'bg-primary text-white';
                                                                                }

                                                                                echo '<td><span class="badge ' . $colorClass . '"><strong>' . $capaian . '</strong> (' . $satuan . ')</span></td>';
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

                                                <!-- Tabel 4: Sub Kegiatan -->
                                                <div class="tabel-content d-none" data-index="3">
                                                    <p class="fw-bold">Tabel Indikator Sub Kegiatan</p>
                                                    <div class="mb-2 clearfix">
                                                        <div class="float-end">
                                                            <strong>Keterangan:</strong>
                                                            <div class="d-flex gap-2 mt-1 flex-wrap">
                                                                <span class="badge bg-danger px-3 py-2">&lt; 70%</span>
                                                                <span class="badge bg-warning text-dark px-3 py-2">70% - 99%</span>
                                                                <span class="badge bg-success px-3 py-2">100%</span>
                                                                <span class="badge bg-primary px-3 py-2">&gt; 100%</span>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="table-responsive">
                                                        <table class="table table-bordered table-sm table-striped align-middle text-center">
                                                            <thead class="table-secondary">
                                                                <tr>
                                                                    <th colspan="10" class="text-center"><?= Html::encode($skpd->nama_skpd) ?></th>
                                                                </tr>
                                                                <tr>
                                                                    <th>No</th>
                                                                    <th>Indikator Sub Kegiatan</th>
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
                                                                $subkegiatanData = $dataSubkegiatan[$skpd->refskpd_id] ?? [];

                                                                $no = 1;

                                                                foreach ($subkegiatanData as $subkegiatanItem):
                                                                    $indikatorList = $subkegiatanItem['indikator'];
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
                                                                            $satuan = Html::encode($indikatorItem['subkegiatan_satuan'] ?? '');

                                                                            echo '<td>' . ($realisasi !== '' ? $realisasi : '-') . '</td>';

                                                                            if ($realisasi === '' || $realisasi === null || $realisasi == 0) {
                                                                                echo '<td><span class="badge bg-danger">Belum ada capaian</span></td>';
                                                                            } else {
                                                                                $colorClass = '';

                                                                                if ($capaian < 70) {
                                                                                    $colorClass = 'bg-danger text-white';
                                                                                } elseif ($capaian >= 70 && $capaian < 100) {
                                                                                    $colorClass = 'bg-warning text-dark';
                                                                                } elseif ($capaian == 100) {
                                                                                    $colorClass = 'bg-success text-white';
                                                                                } elseif ($capaian > 100) {
                                                                                    $colorClass = 'bg-primary text-white';
                                                                                }

                                                                                echo '<td><span class="badge ' . $colorClass . '"><strong>' . $capaian . '</strong> (' . $satuan . ')</span></td>';
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

                                                <!-- Tombol Navigasi -->
                                                <div class="d-flex justify-content-between mt-2">
                                                    <button class="btn btn-outline-primary btn-prev" data-scope="<?= $index ?>"><i class="bi bi-arrow-left"></i> Sebelumnya</button>
                                                    <button class="btn btn-outline-primary btn-next" data-scope="<?= $index ?>">Selanjutnya <i class="bi bi-arrow-right"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="col-lg-12">
                                <div class="alert alert-warning text-center">Data SKPD tidak tersedia.</div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const wrappers = document.querySelectorAll('.tabel-wrapper');

                        wrappers.forEach(wrapper => {
                            const scopeIndex = wrapper.id.split('-').pop();
                            const contents = wrapper.querySelectorAll('.tabel-content');
                            let currentIndex = 0;

                            wrapper.querySelector(`.btn-prev`).addEventListener('click', () => {
                                contents[currentIndex].classList.add('d-none');
                                currentIndex = (currentIndex - 1 + contents.length) % contents.length;
                                contents[currentIndex].classList.remove('d-none');
                            });

                            wrapper.querySelector(`.btn-next`).addEventListener('click', () => {
                                contents[currentIndex].classList.add('d-none');
                                currentIndex = (currentIndex + 1) % contents.length;
                                contents[currentIndex].classList.remove('d-none');
                            });
                        });
                    });
                </script>


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