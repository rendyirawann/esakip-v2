<?php

use frontend\models\SakipIndikatorsasaranrenstraTriwulan;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var frontend\models\search\SakipIndikatorsasaranrenstraTriwulanSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Sakip Indikatorsasaranrenstra Triwulans';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sakip-indikatorsasaranrenstra-triwulan-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Sakip Indikatorsasaranrenstra Triwulan', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'refindikatorsasaranrenstratriwulan_id',
            'refindikatorsasaranrenstra_id',
            'refsasaranrenstra_id',
            'refskpd_id',
            'refperiode_id',
            //'reftriwulan_id',
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
                'urlCreator' => function ($action, SakipIndikatorsasaranrenstraTriwulan $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'refindikatorsasaranrenstratriwulan_id' => $model->refindikatorsasaranrenstratriwulan_id]);
                 }
            ],
        ],
    ]); ?>


</div>
