<style>
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
</style>
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

$this->title = 'Aplikasi ESAKIP';

?>
<!-- [ Main Content ] start -->
<div class="pc-container">
  <div class="pc-content">
    <!-- [ breadcrumb ] start -->
    <div class="page-header">
      <div class="page-block">
        <div class="row align-items-center">
          <div class="col-md-12">
            <ul class="breadcrumb">
              <li class="breadcrumb-item"><a href="<?= Url::to(['/site/index']) ?>">Dashboard</a></li>
              <li class="breadcrumb-item" aria-current="page">Home</li>
            </ul>
          </div>
          <div class="col-md-12">
            <div class="page-header-title">
              <h2 class="mb-0">Home</h2>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- [ breadcrumb ] end -->

    <!-- [ Main Content ] start -->
    <div class="row">
      <div class="col-lg-12 text-center">
        <h1>Dashboard Aplikasi eSakip</h1>
        <img src="<?= Url::base(true) ?>/lightapp/assets/images/bappeda.png" alt="" width="auto">
        <h3>Bappedalitbang Deli Sedang</h3>
      </div>
    </div>
    <!--  -->

    <div class="row">
      <div class="col-sm-12">
        <!-- Dropdown filter berdasarkan refperiode_id -->
        <?= \yii\helpers\Html::beginForm(['index-esakip'], 'get', ['class' => 'form-inline']); ?>
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

    <div class="row">
      <div class="col-lg-3 position-relative">
        <div class="card">
          <div class="card-header">
            <h5>1 - Sasaran Renstra</h5>
            <?php
            // Hitung jumlah sasaran renstra
            $jumlahSasaran = SakipSasaranrenstra::find()
              ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
              ->count();
            ?>
            <span class="badge bg-info"><?= $jumlahSasaran ?> Sasaran Renstra</span>

            <?php if ($jumlahSasaranBelumIndikator > 0): ?>
              <span class="badge bg-warning"><?= $jumlahSasaranBelumIndikator ?> Sasaran belum memiliki indikator</span>
            <?php endif; ?>
          </div>
          <div class="card-body">
            <p class="<?= $jumlahSasaranBelumIndikator > 0 ? 'text-warning' : ($statusSasaranRenstra ? 'text-success' : 'text-danger') ?>">Status:
              <i class="fas <?= $jumlahSasaranBelumIndikator > 0 ? 'fa-hourglass-half' : ($statusSasaranRenstra ? 'fa-check' : 'fa-times') ?>"></i>
              <?php if ($statusSasaranRenstra): ?>
                <button type="button" class="btn btn-primary btn-sm" title="View" data-bs-toggle="modal" data-bs-target="#sasaranRenstraModal">
                  <i class="fas fa-eye"></i>
                </button>
              <?php endif; ?>
            </p>
            </p>
          </div>
        </div>
        <i class="fas fa-arrow-right arrow-icon"></i>
      </div>



      <!-- Card 2 -->
      <div class="col-lg-3 position-relative">
        <div class="card">
          <div class="card-header">
            <h5>2 - Indikator Sasaran Renstra</h5>
            <?php
            // Hitung jumlah indikator sasaran renstra
            $jumlahIndikator = SakipIndikatorsasaranrenstra::find()
              ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
              ->count();
            ?>
            <span class="badge bg-info"><?= $jumlahIndikator ?> Indikator</span>
            <?php if ($jumlahIndikator > 0): // Tombol hanya muncul jika ada indikator 
            ?>
            <?php endif; ?>
          </div>
          <div class="card-body">
            <p class="<?= $statusIndikatorSasaranRenstra ? 'text-success' : 'text-danger' ?>">Status:
              <i class="fas <?= $statusIndikatorSasaranRenstra ? 'fa-check' : 'fa-times' ?>"></i>
              <?php if ($statusIndikatorSasaranRenstra): ?>
                <button type="button" class="btn btn-primary btn-sm" title="View" data-bs-toggle="modal" data-bs-target="#indikatorSasaranModal">
                  <i class="fas fa-eye"></i>
                </button>
              <?php endif; ?>
            </p>
          </div>
        </div>
        <i class="fas fa-arrow-right arrow-icon"></i>
      </div>
      <!-- Card 3 -->
      <div class="col-lg-3 position-relative">
        <div class="card">
          <div class="card-header">
            <h5>3 - Tujuan Sasaran Renstra</h5>
            <?php
            // Hitung jumlah indikator sasaran renstra
            $jumlahTujuan = SakipTujuanrenstra::find()
              ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
              ->count();
            ?>
            <span class="badge bg-info"><?= $jumlahTujuan ?> Tujuan Renstra</span>
            <?php if ($jumlahTujuan > 0): // Tombol hanya muncul jika ada indikator 
            ?>
            <?php endif; ?>
          </div>
          <div class="card-body">
            <p class="<?= $statusTujuanRenstra ? 'text-success' : 'text-danger' ?>">Status:
              <i class="fas <?= $statusTujuanRenstra ? 'fa-check' : 'fa-times' ?>"></i>
            </p>
            <?php if ($statusTujuanRenstra): ?>
              <button type="button" class="btn btn-primary btn-sm" title="View" data-bs-toggle="modal" data-bs-target="#tujuanSasaranModal">
                <i class="fas fa-eye"></i>
              </button>
            <?php endif; ?>
          </div>
        </div>
        <i class="fas fa-arrow-right arrow-icon"></i>
      </div>
      <!-- Card 4 -->
      <div class="col-lg-3 position-relative">
        <div class="card">
          <div class="card-header">
            <h5>4 - Indikator Tujuan Sasaran Renstra</h5>
          </div>
          <div class="card-body">
            <p class="<?= $statusIndikatorTujuanRenstra ? 'text-success' : 'text-danger' ?>">Status:
              <i class="fas <?= $statusIndikatorTujuanRenstra ? 'fa-check' : 'fa-times' ?>"></i>
            </p>
          </div>
        </div>
        <!-- Panah ke bawah khusus untuk card ke-4 -->
        <i class="fas fa-arrow-down arrow-icon-bottom"></i>
      </div>

      <!--  -->
      <div class="modal fade" id="sasaranRenstraModal" tabindex="-1" role="dialog" aria-labelledby="sasaranRenstraModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="sasaranRenstraModalLabel">Detail Sasaran Renstra</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <?php foreach ($sakipSasaranRenstra as $sasaran) : ?>
                <br>
                <p><strong>Uraian Sasaran Renstra:</strong> <?= $sasaran->uraian_sasaranrenstra ?>
                  <?php
                  // Cek apakah sasaran ini memiliki indikator
                  $indikatorAda = SakipIndikatorsasaranrenstra::find()
                    ->where(['refsasaranrenstra_id' => $sasaran->refsasaranrenstra_id])
                    ->andWhere(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
                    ->exists();
                  ?>
                  <?php if (!$indikatorAda): ?>
                    <span class="badge bg-warning">Belum memiliki indikator</span>
                  <?php endif; ?>
                </p>
                <p><strong>Uraian Misi:</strong> <?= $sasaran->refMisi->uraian_misi ?? 'N/A' ?></p>
                <p><strong>Uraian Tujuan:</strong> <?= $sasaran->refTujuan->uraian_tujuan ?? 'N/A' ?></p>
                <p><strong>Uraian Visi:</strong> <?= $sasaran->refVisi->uraian_visi ?? 'N/A' ?></p>
              <?php endforeach; ?>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
            </div>
          </div>
        </div>
      </div>

      <!--  -->
      <!-- Modal untuk Indikator Sasaran Renstra -->
      <div class="modal fade" id="indikatorSasaranModal" tabindex="-1" role="dialog" aria-labelledby="indikatorSasaranModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="indikatorSasaranModalLabel">Detail Indikator Sasaran Renstra</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <?php
              // Ambil data indikator sasaran renstra
              $indikatorSasaranRenstra = SakipIndikatorsasaranrenstra::find()
                ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
                ->all();
              ?>
              <?php foreach ($indikatorSasaranRenstra as $indikator) : ?>
                <br>
                <p><strong>Uraian Indikator:</strong> <?= $indikator->uraian_indikatorsasaranrenstra ?></p>
                <p><strong>Satuan:</strong> <?= $indikator->indikatorsasaranrenstra_satuan ?></p>
                <p><strong>Target:</strong> <?= $indikator->indikatorsasaranrenstra_target ?></p>
                <p><strong>Realisasi:</strong> <?= $indikator->realisasi ?></p>
                <p><strong>Capaian:</strong> <?= $indikator->capaian ?></p>
                <p><strong>Analisis:</strong> <?= $indikator->analisis ?? 'N/A' ?></p>
                <p><strong>Keterangan:</strong> <?= $indikator->keterangan ?? 'N/A' ?></p>
              <?php endforeach; ?>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
            </div>
          </div>
        </div>
      </div>
      <!--  -->
      <!-- Modal untuk Tujuan Sasaran Renstra -->
      <div class="modal fade" id="tujuanSasaranModal" tabindex="-1" role="dialog" aria-labelledby="tujuanSasaranModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="tujuanSasaranModalLabel">Detail Tujuan Sasaran Renstra</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <?php
              // Ambil data tujuan renstra berdasarkan refskpd_id dan refperiode_id
              $tujuanRenstraList = SakipTujuanrenstra::find()
                ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
                ->with(['refMisi', 'refSasaranrenstra']) // Pastikan relasi ini sesuai dengan model
                ->all();

              foreach ($tujuanRenstraList as $tujuan) : ?>
                <br>
                <p><strong>Uraian Tujuan Renstra:</strong> <?= $tujuan->uraian_tujuanrenstra ?></p>
                <p><strong>Ref Misi:</strong> <?= $tujuan->refMisi->uraian_misi ?? 'N/A' ?></p>
                <p><strong>Ref Sasaran:</strong> <?= $tujuan->refSasaranrenstra->uraian_sasaranrenstra ?? 'N/A' ?></p>
              <?php endforeach; ?>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
            </div>
          </div>
        </div>
      </div>

      <!--  -->
    </div>

    <!--  -->
    <div class="row mt-5">
      <div class="col-lg-3 position-relative">
        <div class="card">
          <div class="card-header">
            <h5>8 - Renstra Kegiatan</h5>
          </div>
          <div class="card-body">
            <p></p>
          </div>
        </div>
        <i class="fas fa-arrow-down arrow-icon-bottom-2"></i>
        <i class="fas fa-arrow-left arrow-icon"></i>
      </div>
      <!--  -->
      <div class="col-lg-3 position-relative">
        <div class="card">
          <div class="card-header">
            <h5>7 - Renstra Program</h5>
          </div>
          <div class="card-body">
            <p></p>
          </div>
        </div>
        <i class="fas fa-arrow-left arrow-icon"></i>
      </div>
      <!--  -->
      <div class="col-lg-3 position-relative">
        <div class="card">
          <div class="card-header">
            <h5>6 - Kebijakan</h5>
          </div>
          <div class="card-body">
            <p></p>
          </div>
        </div>
        <i class="fas fa-arrow-left arrow-icon"></i>
      </div>
      <!--  -->
      <div class="col-lg-3 position-relative">
        <div class="card">
          <div class="card-header">
            <h5>5 - Strategi</h5>
          </div>
          <div class="card-body">
            <p></p>
          </div>
        </div>

      </div>
    </div>

    <!--  -->
    <div class="row mt-5">
      <div class="col-lg-3 position-relative">
        <div class="card">
          <div class="card-header">
            <h5>9 - Renstra Sub Kegiatan</h5>
          </div>
          <div class="card-body">
            <p></p>
          </div>
        </div>
        <i class="fas fa-arrow-right arrow-icon"></i>
      </div>
      <!--  -->
      <div class="col-lg-3 position-relative">
        <div class="card">
          <div class="card-header">
            <h5>10 - Target RKT Sasaran</h5>
          </div>
          <div class="card-body">
            <p></p>
          </div>
        </div>
        <i class="fas fa-arrow-right arrow-icon"></i>
      </div>
      <!--  -->
      <div class="col-lg-3 position-relative">
        <div class="card">
          <div class="card-header">
            <h5>11 - Target RKT Program</h5>
          </div>
          <div class="card-body">
            <p></p>
          </div>
        </div>
        <i class="fas fa-arrow-right arrow-icon"></i>
      </div>
      <!--  -->
      <div class="col-lg-3 position-relative">
        <div class="card">
          <div class="card-header">
            <h5>12 - Target RKT Kegiatan</h5>
          </div>
          <div class="card-body">
            <p></p>
          </div>
        </div>
        <i class="fas fa-arrow-down arrow-icon-bottom"></i>
      </div>
    </div>
    <!--  -->
    <div class="row mt-5">
      <div class="col-lg-3 position-relative">
        <div class="card">
          <div class="card-header">
            <h5>16 - Target PK Program</h5>
          </div>
          <div class="card-body">
            <p></p>
          </div>
        </div>
        <i class="fas fa-arrow-down arrow-icon-bottom-2"></i>
        <i class="fas fa-arrow-left arrow-icon"></i>
      </div>
      <!--  -->
      <div class="col-lg-3 position-relative">
        <div class="card">
          <div class="card-header">
            <h5>15 - Target PK Sasaran</h5>
          </div>
          <div class="card-body">
            <p></p>
          </div>
        </div>
        <i class="fas fa-arrow-left arrow-icon"></i>
      </div>
      <!--  -->
      <div class="col-lg-3 position-relative">
        <div class="card">
          <div class="card-header">
            <h5>14 - Anggaran Sub Kegiatan</h5>
          </div>
          <div class="card-body">
            <p></p>
          </div>
        </div>
        <i class="fas fa-arrow-left arrow-icon"></i>
      </div>
      <!--  -->
      <div class="col-lg-3 position-relative">
        <div class="card">
          <div class="card-header">
            <h5>13 - Target RKT Sub Kegiatan</h5>
          </div>
          <div class="card-body">
            <p></p>
          </div>
        </div>

      </div>
    </div>

    <!--  -->
    <div class="row mt-5">
      <div class="col-lg-3 position-relative">
        <div class="card">
          <div class="card-header">
            <h5>17 - Target PK Kegiatan</h5>
          </div>
          <div class="card-body">
            <p></p>
          </div>
        </div>
        <i class="fas fa-arrow-right arrow-icon"></i>
      </div>
      <!--  -->
      <div class="col-lg-3 position-relative">
        <div class="card">
          <div class="card-header">
            <h5>18 - Target PK Sub Kegiatan</h5>
          </div>
          <div class="card-body">
            <p></p>
          </div>
        </div>
        <i class="fas fa-arrow-right arrow-icon"></i>
      </div>
      <!--  -->
      <div class="col-lg-3 position-relative">
        <div class="card">
          <div class="card-header">
            <h5>19 - Anggaran PK Sub Kegiatan</h5>
          </div>
          <div class="card-body">
            <p></p>
          </div>
        </div>
        <i class="fas fa-arrow-right arrow-icon"></i>
      </div>
      <!--  -->
      <div class="col-lg-3 position-relative">
        <div class="card">
          <div class="card-header">
            <h5>20 - Target PKP Sasaran</h5>
          </div>
          <div class="card-body">
            <p></p>
          </div>
        </div>
        <i class="fas fa-arrow-down arrow-icon-bottom"></i>
      </div>
    </div>

    <!--  -->

    <div class="row mt-5">
      <div class="col-lg-3 position-relative">
        <div class="card">
          <div class="card-header">
            <h5>24 - Anggaran PKP Sub Kegiatan</h5>
          </div>
          <div class="card-body">
            <p></p>
          </div>
        </div>
        <i class="fas fa-arrow-down arrow-icon-bottom-2"></i>
        <i class="fas fa-arrow-left arrow-icon"></i>
      </div>
      <!--  -->
      <div class="col-lg-3 position-relative">
        <div class="card">
          <div class="card-header">
            <h5>23 - Target PKP Sub Kegiatan</h5>
          </div>
          <div class="card-body">
            <p></p>
          </div>
        </div>
        <i class="fas fa-arrow-left arrow-icon"></i>
      </div>
      <!--  -->
      <div class="col-lg-3 position-relative">
        <div class="card">
          <div class="card-header">
            <h5>22 - Target PKP Kegiatan</h5>
          </div>
          <div class="card-body">
            <p></p>
          </div>
        </div>
        <i class="fas fa-arrow-left arrow-icon"></i>
      </div>
      <!--  -->
      <div class="col-lg-3 position-relative">
        <div class="card">
          <div class="card-header">
            <h5>21 - Target PKP Program</h5>
          </div>
          <div class="card-body">
            <p></p>
          </div>
        </div>

      </div>
    </div>

    <!--  -->
    <div class="row mt-5">
      <div class="col-lg-3 position-relative">
        <div class="card">
          <div class="card-header">
            <h5>25 - Capaian Kinerja Sasaran</h5>
          </div>
          <div class="card-body">
            <p></p>
          </div>
        </div>
        <i class="fas fa-arrow-right arrow-icon"></i>
      </div>
      <!--  -->
      <div class="col-lg-3 position-relative">
        <div class="card">
          <div class="card-header">
            <h5>26 - Capaian Kinerja Program</h5>
          </div>
          <div class="card-body">
            <p></p>
          </div>
        </div>
        <i class="fas fa-arrow-right arrow-icon"></i>
      </div>
      <!--  -->
      <div class="col-lg-3 position-relative">
        <div class="card">
          <div class="card-header">
            <h5>27 - Capaian Kinerja Kegiatan</h5>
          </div>
          <div class="card-body">
            <p></p>
          </div>
        </div>
        <i class="fas fa-arrow-right arrow-icon"></i>
      </div>
      <!--  -->
      <div class="col-lg-3 position-relative">
        <div class="card">
          <div class="card-header">
            <h5>28 - Capaian Kinerja Sub Kegiatan</h5>
          </div>
          <div class="card-body">
            <p></p>
          </div>
        </div>
        <i class="fas fa-arrow-down arrow-icon-bottom"></i>
      </div>
    </div>

    <!--  -->

    <div class="row mt-5">
      <div class="col-lg-3 position-relative">

      </div>
      <!--  -->
      <div class="col-lg-3 position-relative">

      </div>
      <!--  -->
      <div class="col-lg-3 position-relative">

      </div>
      <!--  -->
      <div class="col-lg-3 position-relative">
        <div class="card">
          <div class="card-header">
            <h5>29 - Penyerapan Anggaran</h5>
          </div>
          <div class="card-body">
            <p></p>
          </div>
        </div>

      </div>
    </div>





  </div>
  <!-- [ Main Content ] end -->
</div>
</div>
<!-- [ Main Content ] end -->