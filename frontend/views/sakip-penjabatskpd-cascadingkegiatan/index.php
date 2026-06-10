<?php

use frontend\models\SakipPenjabatskpdCascadingkegiatan;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var frontend\models\search\SakipPenjabatskpdCascadingkegiatanSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Sakip Penjabatskpd Cascadingkegiatans';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sakip-penjabatskpd-cascadingkegiatan-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Sakip Penjabatskpd Cascadingkegiatan', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'refpenjabatcascadingkegiatan_id',
            'refpenjabatskpd_id',
            'refeselon_id',
            'refcascadingprogram_id',
            'refcascadingkegiatan_id',
            //'refindikatorkegiatan_id',
            //'refskpd_id',
            //'refperiode_id',
            //'refsasaranrenstra_id',
            //'refindikatorsasaranrenstra_id',
            //'refprogram_id',
            //'refkegiatan_id',
            //'uraian_sasarankegiatan:ntext',
            //'uraian_indikatorkegiatan:ntext',
            //'kegiatan_target',
            //'kegiatan_satuan',
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
                'urlCreator' => function ($action, SakipPenjabatskpdCascadingkegiatan $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'refpenjabatcascadingkegiatan_id' => $model->refpenjabatcascadingkegiatan_id]);
                 }
            ],
        ],
    ]); ?>


</div>
