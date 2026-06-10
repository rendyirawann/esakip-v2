<?php

use frontend\models\SakipIndikatorcascadingprogramTriwulan;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var frontend\models\search\SakipIndikatorcascadingprogramTriwulanSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Sakip Indikatorcascadingprogram Triwulans';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sakip-indikatorcascadingprogram-triwulan-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Sakip Indikatorcascadingprogram Triwulan', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'refindikatorprogramtriwulan_id',
            'refindikatorprogram_id',
            'refcascadingprogram_id',
            'refsasaranrenstra_id',
            'refskpd_id',
            //'refperiode_id',
            //'reftriwulan_id',
            //'refbidang_id',
            //'refprogram_id',
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
                'urlCreator' => function ($action, SakipIndikatorcascadingprogramTriwulan $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'refindikatorprogramtriwulan_id' => $model->refindikatorprogramtriwulan_id]);
                 }
            ],
        ],
    ]); ?>


</div>
