<?php

use frontend\models\SimonaCascadingkegiatan;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var frontend\models\search\SimonaCascadingkegiatanSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Simona Cascadingkegiatans';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="simona-cascadingkegiatan-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Simona Cascadingkegiatan', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'refsimonacascadingkegiatan_id',
            'refcascadingprogram_id',
            'refcascadingkegiatan_id',
            'refskpd_id',
            'refsasaranrenstra_id',
            //'refindikatorsasaranrenstra_id',
            //'refprogram_id',
            //'refkegiatan_id',
            //'uraian_sasarankegiatan:ntext',
            //'uraian_indikatorkegiatan:ntext',
            //'refperiode_id',
            //'kegiatan_target',
            //'kegiatan_satuan',
            //'refpegawaibappeda_id',
            //'date_start',
            //'expired_date',
            //'status_simonacascadingkegiatan',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, SimonaCascadingkegiatan $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'refsimonacascadingkegiatan_id' => $model->refsimonacascadingkegiatan_id]);
                 }
            ],
        ],
    ]); ?>


</div>
