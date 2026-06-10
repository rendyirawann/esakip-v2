<style>
  .text-justify {
    text-align: justify !important;
  }
</style>
<?php

use frontend\models\SakipVisi;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var frontend\models\search\SakipVisiSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'e-Sakip - Data Visi & Misi';
$this->params['breadcrumbs'][] = $this->title;
$this->registerJs("
    // Event handler untuk tombol toggle. Logika ini sudah benar.
    $('.visimisi-toggle-btn').on('click', function() {
        // Hapus kelas aktif dari semua tombol, lalu tambahkan ke yang diklik
        $('.visimisi-toggle-btn').removeClass('btn-primary active').addClass('btn-secondary');
        $(this).removeClass('btn-secondary').addClass('btn-primary active');

        var target = $(this).data('target');
        
        // Sembunyikan semua container konten
        $('.visimisi-content').hide();
        
        // Tampilkan hanya container yang menjadi target
        $(target).show();
    });

    // JavaScript untuk mengubah ikon + menjadi - saat accordion dibuka/tutup
    $('.accordion .collapse').on('show.bs.collapse', function () {
        $(this).parent().find('.fas').removeClass('fa-plus').addClass('fa-minus');
    }).on('hide.bs.collapse', function () {
        $(this).parent().find('.fas').removeClass('fa-minus').addClass('fa-plus');
    });
");

$this->registerJs("
    // 1. Definisikan semua elemen accordion yang ada di halaman
    var allAccordionItems = document.querySelectorAll('.accordion .collapse');

    // 2. Dengarkan event 'show.bs.collapse' dari Bootstrap
    // Event ini terjadi TEPAT SEBELUM sebuah item accordion mulai terbuka.
    $(allAccordionItems).on('show.bs.collapse', function () {
        // Cari ikon di dalam header yang memicu event ini, lalu ubah jadi 'minus'
        $(this).prev('.card-header').find('.fas').removeClass('fa-plus').addClass('fa-minus');
    });

    // 3. Dengarkan event 'hide.bs.collapse' dari Bootstrap
    // Event ini terjadi TEPAT SEBELUM sebuah item accordion mulai tertutup.
    $(allAccordionItems).on('hide.bs.collapse', function () {
        // Cari ikon di dalam header yang memicu event ini, lalu ubah kembali jadi 'plus'
        $(this).prev('.card-header').find('.fas').removeClass('fa-minus').addClass('fa-plus');
    });


    // 4. (Opsional tapi Penting) Perbaiki logika untuk tombol 'toggleAll'
    // Tombol ini harusnya membuka semua yang tertutup, atau menutup semua yang terbuka.
    $('#toggleAll').off('click').on('click', function() {
        var allCollapses = $('.accordion .collapse');
        var openCollapses = allCollapses.filter('.show');

        if (openCollapses.length > 0) {
            // Jika ada yang terbuka, tutup semuanya
            openCollapses.collapse('hide');
        } else {
            // Jika semua tertutup, buka semuanya
            allCollapses.collapse('show');
        }
    });

");

$this->registerJs("
$('#createModal').on('show.bs.modal', function (event) {
    var modal = $(this);
    $.ajax({
        url: '" . Url::to(['sakip-visi/create']) . "',
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




?>
<div class="pc-container">
  <div class="pc-content">
    <!-- [ breadcrumb ] start -->
    <div class="page-header">
      <div class="page-block">
        <div class="row align-items-center">
          <div class="col-md-12">
            <ul class="breadcrumb">
              <li class="breadcrumb-item"><a href="<?= Url::to(['/site/index']) ?>">Home</a></li>
              <li class="breadcrumb-item" aria-current="page">VISI RPJMD</li>
            </ul>
          </div>
          <div class="col-md-12">
            <div class="page-header-title">
              <h2 class="mb-0">Visi</h2>
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
          <div class="card-header" style="background-color: #04A9F5; padding: 8px;"> <!-- Sesuaikan padding di sini -->
            <h6 style="color: white; margin: 0; cursor: pointer;" id="toggleAll"> <!-- Mengatur margin menjadi 0 untuk mengurangi ruang -->
              <i class="fas fa-pen-fancy"></i> VISI dan Misi RPJMD - <?= Html::encode($nama_skpd) ?>
            </h6>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-sm-12">
                <?= \yii\helpers\Html::beginForm(['index'], 'get', ['class' => 'form-inline']); ?>
                <div class="form-group">
                  <?= \yii\helpers\Html::label('Pilih Periode:', 'refperiode_id', ['class' => 'mr-2']); ?>
                  <?= \yii\helpers\Html::dropDownList(
                    'refperiode_id',
                    $selectedPeriodId,
                    \yii\helpers\ArrayHelper::map($periodeList, 'refperiode_id', 'periode'), // Mapping periodeList
                    [
                      'class' => 'form-control',
                      'prompt' => 'Pilih Periode',
                      'onchange' => 'this.form.submit()' // Submit form saat pilihan berubah
                    ]
                  ); ?>
                </div>
                <?= \yii\helpers\Html::endForm(); ?>
                <div class="mb-3">
                  <div class="btn-group" role="group">
                    <button type="button" class="btn btn-primary active visimisi-toggle-btn" data-target="#content-murni">
                      Visi & Misi (Induk)
                    </button>
                    <button type="button" class="btn btn-secondary visimisi-toggle-btn" data-target="#content-perubahan">
                      Visi & Misi (Perubahan)
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <div id="content-murni" class="visimisi-content">
              <div class="accordion" id="accordionMurni">
                <div class="card">
                  <div class="card-header" id="headingVisiMurni">
                    <h5 class="mb-0"><button class="btn btn-link" data-bs-toggle="collapse" data-bs-target="#collapseVisiMurni"><i class="fas fa-plus"></i> Uraian Visi</button></h5>
                  </div>
                  <div id="collapseVisiMurni" class="collapse" data-bs-parent="#accordionMurni">
                    <div class="card-body"><?= $visi ? Html::decode($visi->uraian_visi) : '<i>Tidak ada data visi untuk periode ini.</i>' ?></div>
                  </div>
                </div>
                <div class="card">
                  <div class="card-header" id="headingPenjabaranVisiMurni">
                    <h5 class="mb-0"><button class="btn btn-link" data-bs-toggle="collapse" data-bs-target="#collapsePenjabaranVisiMurni"><i class="fas fa-plus"></i> Penjabaran Visi</button></h5>
                  </div>
                  <div id="collapsePenjabaranVisiMurni" class="collapse" data-bs-parent="#accordionMurni">
                    <div class="card-body text-justify"><?= $visi ? Html::decode($visi->penjabaran_visi) : '<i>Tidak ada data penjabaran visi untuk periode ini.</i>' ?></div>
                  </div>
                </div>
                <div class="card">
                  <div class="card-header" id="headingMisiMurni">
                    <h5 class="mb-0"><button class="btn btn-link" data-bs-toggle="collapse" data-bs-target="#collapseMisiMurni"><i class="fas fa-plus"></i> Uraian Misi</button></h5>
                  </div>
                  <div id="collapseMisiMurni" class="collapse" data-bs-parent="#accordionMurni">
                    <div class="card-body">
                      <?php if (!empty($misiData)): ?>
                        <?php foreach ($misiData as $misi): ?><p><?= Html::decode($misi->uraian_misi) ?></p><?php endforeach; ?>
                      <?php else: ?><p><i>Tidak ada data misi untuk periode ini.</i></p><?php endif; ?>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div id="content-perubahan" class="visimisi-content" style="display:none;">
              <div class="accordion" id="accordionPerubahan">
                <div class="card">
                  <div class="card-header" id="headingVisiPerubahan">
                    <h5 class="mb-0"><button class="btn btn-link" data-bs-toggle="collapse" data-bs-target="#collapseVisiPerubahan"><i class="fas fa-plus"></i> Uraian Visi Perubahan</button></h5>
                  </div>
                  <div id="collapseVisiPerubahan" class="collapse" data-bs-parent="#accordionPerubahan">
                    <div class="card-body"><?= $visiP ? Html::decode($visiP->uraian_visi_p) : '<i>Tidak ada data visi perubahan untuk periode ini.</i>' ?></div>
                  </div>
                </div>
                <div class="card">
                  <div class="card-header" id="headingPenjabaranVisiPerubahan">
                    <h5 class="mb-0"><button class="btn btn-link" data-bs-toggle="collapse" data-bs-target="#collapsePenjabaranVisiPerubahan"><i class="fas fa-plus"></i> Penjabaran Visi Perubahan</button></h5>
                  </div>
                  <div id="collapsePenjabaranVisiPerubahan" class="collapse" data-bs-parent="#accordionPerubahan">
                    <div class="card-body text-justify"><?= $visiP ? Html::decode($visiP->penjabaran_visi_p) : '<i>Tidak ada data penjabaran visi perubahan untuk periode ini.</i>' ?></div>
                  </div>
                </div>
                <div class="card">
                  <div class="card-header" id="headingMisiPerubahan">
                    <h5 class="mb-0"><button class="btn btn-link" data-bs-toggle="collapse" data-bs-target="#collapseMisiPerubahan"><i class="fas fa-plus"></i> Uraian Misi Perubahan</button></h5>
                  </div>
                  <div id="collapseMisiPerubahan" class="collapse" data-bs-parent="#accordionPerubahan">
                    <div class="card-body">
                      <?php if (!empty($misiPData)): ?>
                        <?php foreach ($misiPData as $misiP): ?><p><?= Html::decode($misiP->uraian_misi_p) ?></p><?php endforeach; ?>
                      <?php else: ?><p><i>Tidak ada data misi perubahan untuk periode ini.</i></p><?php endif; ?>
                    </div>
                  </div>
                </div>
              </div>
            </div>

          </div>
          <!-- end card -->
        </div>

        <!-- end col -->
      </div>
      <!-- end row -->
    </div>


    <!-- [ Main Content ] end -->
  </div>
</div>

<!-- Include jQuery and Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>