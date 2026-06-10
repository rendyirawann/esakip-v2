<!-- <style>
  .card-header {
    height: 120px;
  }

  .position-relative {
    position: relative;
  }

  .arrow-icon {
    position: absolute;
    right: -10px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 1.5em;
    color: #555;
  }

  /* Kustomisasi khusus untuk panah bawah pada card ke-4 */
  .arrow-icon-bottom {
    position: absolute;
    bottom: -20px;
    /* Jarak dari bawah card */
    left: 50%;
    transform: translateX(-50%);
    /* Rotasi panah ke bawah */
    font-size: 1.5em;
    color: #555;
  }

  .arrow-icon-bottom-2 {
    position: absolute;
    bottom: -20px;
    /* Jarak dari bawah card */
    right: 50%;
    transform: translateX(-50%);
    /* Rotasi panah ke bawah */
    font-size: 1.5em;
    color: #555;
  }
</style> -->
<?php

use yii\helpers\Url;
use yii\helpers\Html;
use frontend\models\User;
use frontend\models\SakipSasaranrenstra;
use frontend\models\SakipIndikatorsasaranrenstra;
use frontend\models\SakipTujuanrenstra;
use frontend\models\SakipIndikatortujuanrenstra;
use frontend\models\SakipStrategi;
use frontend\models\SakipKebijakan;
use frontend\models\SakipCascadingprogram;
use frontend\models\SakipCascadingkegiatan;
use frontend\models\SakipCascadingsubkegiatan;

/** @var yii\web\View $this */
$this->registerJsFile('https://cdn.jsdelivr.net/npm/echarts/dist/echarts.min.js', ['depends' => [\yii\web\JqueryAsset::class]]);

$this->title = 'Aplikasi ESAKIP';

$this->registerJs("
    $('#createModal').on('show.bs.modal', function (event) {
        var modal = $(this);
        $.ajax({
            url: '" . Url::to(['site/create']) . "',
            type: 'GET',
            success: function(data) {
                modal.find('#modalFormContent').html(data);
            }
        });
    });
");



$this->registerJs("
$('#updateModal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget); // Button that triggered the modal
    var url = button.data('url'); // Extract info from data-url attributes

    var modal = $(this);
    $.ajax({
        url: url,
        type: 'GET',
        success: function(data) {
            modal.find('#modalUpdateFormContent').html(data);
        }
    });
});
");

$dataChartTerendah = json_encode($triwulanTerendah);
$dataChartTertinggi = json_encode($triwulanTertinggi);
$chartIdTerendah = 'triwulan-terendah-pie';
$chartIdTertinggi = 'triwulan-tertinggi-pie';
$namaSkpdTerendah = count($triwulanTerendah) > 0 ? $triwulanTerendah[0]['nama'] : 'Tidak ada data';
$namaSkpdTertinggi = count($triwulanTertinggi) > 0 ? $triwulanTertinggi[0]['nama'] : 'Tidak ada data';

$this->registerJs("
    var chartDom1 = document.getElementById('$chartIdTerendah');
    var myChart1 = echarts.init(chartDom1);
    var option1 = {
        title: {
            text: 'Capaian Terendah Semua Triwulan',
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
            left: 'left'
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

    var chartDom2 = document.getElementById('$chartIdTertinggi');
    var myChart2 = echarts.init(chartDom2);
    var option2 = {
        title: {
            text: 'Capaian Tertinggi Semua Triwulan',
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
            left: 'left'
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
");

$dataChartProgramTerendah = json_encode($triwulanTerendahProgram);
$dataChartProgramTertinggi = json_encode($triwulanTertinggiProgram);
$chartIdProgramTerendah = 'triwulan-terendah-program-pie';
$chartIdProgramTertinggi = 'triwulan-tertinggi-program-pie';

$namaProgramTerendah = count($triwulanTerendahProgram) > 0 ? $triwulanTerendahProgram[0]['nama'] : 'Tidak ada data';
$namaProgramTertinggi = count($triwulanTertinggiProgram) > 0 ? $triwulanTertinggiProgram[0]['nama'] : 'Tidak ada data';

$this->registerJs("
    var chartDomProgram1 = document.getElementById('$chartIdProgramTerendah');
    var myChartProgram1 = echarts.init(chartDomProgram1);
    var optionProgram1 = {
        title: {
            text: 'Capaian Terendah Semua Triwulan',
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

    var chartDomProgram2 = document.getElementById('$chartIdProgramTertinggi');
    var myChartProgram2 = echarts.init(chartDomProgram2);
    var optionProgram2 = {
        title: {
            text: 'Capaian Tertinggi Semua Triwulan',
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
            text: 'Capaian Terendah Semua Triwulan',
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
            text: 'Capaian Tertinggi Semua Triwulan',
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
            text: 'Capaian Terendah Semua Triwulan',
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
            text: 'Capaian Tertinggi Semua Triwulan',
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
<div class="pc-container">
  <div class="pc-content">
    <!-- [ breadcrumb ] start -->
    <div class="page-header">
      <div class="page-block">
        <div class="row align-items-center">
          <div class="col-md-12">
            <ul class="breadcrumb">
              <li class="breadcrumb-item"><a href="<?= Url::to(['/site/index-esakip']) ?>">Home</a></li>
              <li class="breadcrumb-item" aria-current="page">Dashboard</li>
            </ul>
          </div>
          <div class="col-md-12">
            <div class="page-header-title">
              <h2 class="mb-0">Dashboard</h2>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- [ breadcrumb ] end -->



    <div class="row">
      <div class="col-lg-12">
        <div class="row">

          <!-- Card 1: Total Users -->
          <div class="col-md-4">
            <div class="card">
              <div class="card-body">
                <h5 class="card-title">
                  <i class="fas fa-users"></i> Total Users
                </h5>
                <p class="card-text">
                  <?php
                  $totalUsers = User::find()->count();
                  echo $totalUsers;
                  ?>
                </p>
              </div>
            </div>
          </div>

          <!-- Card 2: Users Attempted Login Today -->
          <div class="col-md-4">
            <div class="card">
              <div class="card-body">
                <h5 class="card-title">
                  <i class="fas fa-sign-in-alt"></i> Users Attempted Login Today
                </h5>
                <p class="card-text">
                  <?php
                  $today = date('Y-m-d');
                  $usersAttemptedLoginToday = User::find()
                    ->joinWith('userAttemptlogin')
                    ->where(['DATE(user.user_lastlogin)' => $today, 'user.user_isonline' => 'T'])
                    ->groupBy('user.id')
                    ->count();
                  echo $usersAttemptedLoginToday;
                  ?>
                </p>
              </div>
            </div>
          </div>

          <!-- Card 3: Users Currently Online -->
          <div class="col-md-4">
            <div class="card">
              <div class="card-body">
                <h5 class="card-title">
                  <i class="fas fa-user-check"></i> Users Currently Online
                </h5>
                <p class="card-text">
                  <?php
                  $usersCurrentlyOnline = User::find()
                    ->joinWith('userAttemptlogin')
                    ->where(['user.user_isonline' => 'T'])
                    ->groupBy('user.id')
                    ->count();
                  echo $usersCurrentlyOnline;
                  ?>
                </p>
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>




    <div class="row">

      <!-- start row -->
      <div class="col-lg-12 text-center">
        <h1>Dashboard Aplikasi eSakip</h1>
        <img src="<?= Url::base(true) ?>/lightapp/assets/images/bappeda.png" alt="" width="auto">
        <h3>Bappedalitbang Deli Sedang</h3>
      </div>
      <!-- end row -->
    </div>




    <!-- [ Main Content ] end -->
  </div>
</div>