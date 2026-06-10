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
          <div class="card-header" style="background-color: #04A9F5; padding: 8px;">
            <h6 style="color: white; margin: 0; cursor: pointer;" id="toggleAll">
              <i class="fas fa-pen-fancy"></i> VISI RPJMD
            </h6>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-sm-12">
                <?= \yii\helpers\Html::beginForm(['index-dev'], 'get', ['class' => 'form-inline']); ?>
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
                <div class="form-group ml-3">
                  <?= \yii\helpers\Html::label('Pilih SKPD:', 'refskpd_id', ['class' => 'mr-2']); ?>
                  <?= \yii\helpers\Html::dropDownList(
                    'refskpd_id',
                    $selectedSkpdId,
                    $skpdList,
                    [
                      'class' => 'form-control',
                      'prompt' => 'Pilih SKPD',
                      'onchange' => 'this.form.submit()'
                    ]
                  ); ?>
                </div>
                <?= \yii\helpers\Html::endForm(); ?>
              </div>
            </div>

            <!-- Accordion Section -->
            <div class="accordion" id="accordionVisi">
              <div class="card">
                <div class="card-header" style="background-color:whitesmoke;" id="headingVisiOne">
                  <h5 class="mb-0">
                    <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseVisiOne" aria-expanded="true" aria-controls="collapseVisiOne">
                      <i class="fas fa-plus" id="plusIcon"></i> Uraian Visi
                    </button>
                  </h5>
                </div>
                <div id="collapseVisiOne" class="collapse" aria-labelledby="headingVisiOne" data-parent="#accordionVisi">
                  <div class="card-body">
                    <div class="row">
                      <div class="col-sm-12">
                        <?= $dataProvider->models ? Html::decode($dataProvider->models[0]->visi->uraian_visi) : 'Tidak ada data' ?>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="card">
                <div class="card-header" style="background-color:whitesmoke;" id="headingVisiTwo">
                  <h5 class="mb-0">
                    <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseVisiTwo" aria-expanded="false" aria-controls="collapseVisiTwo">
                      <i class="fas fa-plus" id="plusIcon2"></i> Penjabaran Visi
                    </button>
                  </h5>
                </div>
                <div id="collapseVisiTwo" class="collapse" aria-labelledby="headingVisiTwo" data-parent="#accordionVisi">
                  <div class="card-body">
                    <div class="row">
                      <div class="col-sm-12 text-justify">
                        <?= $dataProvider->models ? Html::decode($dataProvider->models[0]->visi->penjabaran_visi) : 'Tidak ada data' ?>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- End Accordion Section -->

          </div>
        </div>
      </div>
      <!-- end row -->
    </div>


    <!-- start misi -->
    <div class="row">
      <!-- start row -->
      <div class="col-sm-12">
        <div class="card">
          <div class="card-header" style="background-color: #04A9F5; padding: 8px;">
            <h6 style="color: white; margin: 0; cursor: pointer;" id="toggleAll">
              <i class="fas fa-pen-fancy"></i> MISI RPJMD
            </h6>
          </div>
          <div class="card-body">


            <!-- Accordion Section -->
            <div class="accordion" id="accordionMisi">
              <div class="card">
                <div class="card-header" style="background-color:whitesmoke;" id="headingMisiOne">
                  <h5 class="mb-0">
                    <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseMisiOne" aria-expanded="true" aria-controls="collapseMisiOne">
                      <i class="fas fa-plus" id="plusIcon"></i> Uraian Misi
                    </button>
                  </h5>
                </div>
                <div id="collapseMisiOne" class="collapse" aria-labelledby="headingMisiOne" data-parent="#accordionMisi">
                  <div class="card-body">
                    <div class="row">
                      <div class="col-sm-12">
                        <?php if (!empty($misiData)): ?>
                          <?php foreach ($misiData as $misi): ?>
                            <p><?= Html::decode($misi->uraian_misi) ?></p>
                          <?php endforeach; ?>
                        <?php else: ?>
                          <p>Tidak ada data misi untuk periode ini.</p>
                        <?php endif; ?>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- End Accordion Section -->
          </div>
        </div>
      </div>

      <!-- end row -->
    </div>
    <!-- end misi -->
    <!-- [ Main Content ] end -->
  </div>
</div>

<!-- Include jQuery and Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script>
  $(document).ready(function() {
    // Toggle accordion sections with card header
    $('#toggleAll').click(function() {
      $('.collapse').collapse('toggle'); // Toggle all collapsible elements
      $('#plusIcon, #plusIcon2').toggleClass('fas fa-plus fas fa-minus'); // Toggle icons
    });

    // Toggle individual accordion buttons
    $('.btn-link').click(function() {
      $(this).find('i').toggleClass('fas fa-plus fas fa-minus');
    });
  });
</script>