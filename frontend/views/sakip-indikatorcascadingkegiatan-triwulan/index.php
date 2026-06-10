<?php

use frontend\models\SakipIndikatorcascadingkegiatanTriwulan;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var frontend\models\search\SakipIndikatorcascadingkegiatanTriwulanSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Sakip Indikatorcascadingkegiatan Triwulans';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sakip-indikatorcascadingkegiatan-triwulan-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Sakip Indikatorcascadingkegiatan Triwulan', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'refindikatorkegiatantriwulan_id',
            'refindikatorkegiatan_id',
            'refcascadingrestraprogram_id',
            'refcascadingrestrakegiatan_id',
            'refsasaranrenstra_id',
            //'refskpd_id',
            //'refperiode_id',
            //'refprogram_id',
            //'refkegiatan_id',
            //'triwulan_target_rkt',
            //'triwulan_target_rkt_p',
            //'triwulan_target_pk',
            //'triwulan_target_pk_p',
            //'triwulan_realisasi',
            //'triwulan_capaian',
            //'triwulan_keterangan:ntext',
            //'triwulan_analisis:ntext',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, SakipIndikatorcascadingkegiatanTriwulan $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'refindikatorkegiatantriwulan_id' => $model->refindikatorkegiatantriwulan_id]);
                 }
            ],
        ],
    ]); ?>


</div>
