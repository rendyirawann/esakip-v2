<?php

use frontend\models\SakipPenjabatskpdCascadingsubkegiatan;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var frontend\models\search\SakipPenjabatskpdCascadingsubkegiatanSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Sakip Penjabatskpd Cascadingsubkegiatans';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sakip-penjabatskpd-cascadingsubkegiatan-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Sakip Penjabatskpd Cascadingsubkegiatan', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'refpenjabatcascadingsubkegiatan_id',
            'refpenjabatskpd_id',
            'refeselon_id',
            'refcascadingprogram_id',
            'refcascadingkegiatan_id',
            //'refcascadingsubkegiatan_id',
            //'refindikatorsubkegiatan_id',
            //'refskpd_id',
            //'refperiode_id',
            //'refsasaranrenstra_id',
            //'refindikatorsasaranrenstra_id',
            //'refprogram_id',
            //'refkegiatan_id',
            //'refsubkegiatan_id',
            //'uraian_sasaransubkegiatan:ntext',
            //'uraian_indikatorsubkegiatan:ntext',
            //'subkegiatan_target',
            //'subkegiatan_satuan',
            //'target_rkt',
            //'target_rkt_p',
            //'target_pk',
            //'target_pk_p',
            //'realisasi',
            //'capaian',
            //'keterangan:ntext',
            //'analisis:ntext',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, SakipPenjabatskpdCascadingsubkegiatan $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'refpenjabatcascadingsubkegiatan_id' => $model->refpenjabatcascadingsubkegiatan_id]);
                 }
            ],
        ],
    ]); ?>


</div>
