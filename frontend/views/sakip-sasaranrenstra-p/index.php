<?php

use frontend\models\SakipSasaranrenstraP;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var frontend\models\search\SakipSasaranrenstraPSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Sakip Sasaranrenstra Ps';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sakip-sasaranrenstra-p-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Sakip Sasaranrenstra P', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'refsasaranrenstra_p_id',
            'refsasaranrenstra_id',
            'uraian_sasaranrenstra_p:ntext',
            'refskpd_id',
            'refsasaran_p_id',
            //'refvisi_p_id',
            //'refmisi_p_id',
            //'reftujuan_p_id',
            //'refperiode_id',
            //'reftujuanrenstra_p_id',
            //'sasaranrenstra_p_isaktif',
            //'alasan_sasaranrenstra_p:ntext',
            //'formulasi_sasaranrenstra_p',
            //'kriteria_sasaranrenstra_p:ntext',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, SakipSasaranrenstraP $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'refsasaranrenstra_p_id' => $model->refsasaranrenstra_p_id]);
                 }
            ],
        ],
    ]); ?>


</div>
