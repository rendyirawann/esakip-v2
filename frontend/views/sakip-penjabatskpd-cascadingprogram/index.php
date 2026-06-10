<?php

use frontend\models\SakipPenjabatskpdCascadingprogram;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var frontend\models\search\SakipPenjabatskpdCascadingprogramSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Sakip Penjabatskpd Cascadingprograms';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sakip-penjabatskpd-cascadingprogram-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Sakip Penjabatskpd Cascadingprogram', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'refpenjabatcascadingprogram_id',
            'refpenjabatskpd_id',
            'refeselon_id',
            'refcascadingprogram_id',
            'refindikatorprogram_id',
            //'refskpd_id',
            //'refperiode_id',
            //'refsasaranrenstra_id',
            //'refindikatorsasaranrenstra_id',
            //'refbidang_id',
            //'refprogram_id',
            //'uraian_sasaranprogram:ntext',
            //'uraian_indikatorprogram:ntext',
            //'program_target',
            //'program_satuan',
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
                'urlCreator' => function ($action, SakipPenjabatskpdCascadingprogram $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'refpenjabatcascadingprogram_id' => $model->refpenjabatcascadingprogram_id]);
                 }
            ],
        ],
    ]); ?>


</div>
