<?php

use frontend\models\SakipIndikatorcascadingsubkegiatanTriwulan;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var frontend\models\search\SakipIndikatorcascadingsubkegiatanTriwulanSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Sakip Indikatorcascadingsubkegiatan Triwulans';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sakip-indikatorcascadingsubkegiatan-triwulan-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Sakip Indikatorcascadingsubkegiatan Triwulan', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'refindikatorsubkegiatantriwulan_id',
            'refindikatorsubkegiatan_id',
            'refcascadingrestraprogram_id',
            'refcascadingrestrakegiatan_id',
            'refcascadingrestrasubkegiatan_id',
            //'refsasaranrenstra_id',
            //'refskpd_id',
            //'refperiode_id',
            //'refprogram_id',
            //'refkegiatan_id',
            //'refsubkegiatan_id',
            //'triwulan_target_rkt',
            //'triwulan_target_rkt_p',
            //'triwulan_target_pk',
            //'triwulan_target_pk_p',
            //'triwulan_realisasi',
            //'triwulan_capaian',
            //'triwulan_keterangan:ntext',
            //'triwulan_analisis:ntext',
            //'triwulan_penyerapan_anggaran',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, SakipIndikatorcascadingsubkegiatanTriwulan $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'refindikatorsubkegiatantriwulan_id' => $model->refindikatorsubkegiatantriwulan_id]);
                 }
            ],
        ],
    ]); ?>


</div>
