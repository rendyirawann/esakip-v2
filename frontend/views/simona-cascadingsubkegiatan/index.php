<?php

use frontend\models\SimonaCascadingsubkegiatan;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var frontend\models\search\SimonaCascadingsubkegiatanSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Simona Cascadingsubkegiatans';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="simona-cascadingsubkegiatan-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Simona Cascadingsubkegiatan', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'refsimonacascadingsubkegiatan_id',
            'refcascadingprogram_id',
            'refcascadingkegiatan_id',
            'refcascadingsubkegiatan_id',
            'refskpd_id',
            //'refsasaranrenstra_id',
            //'refindikatorsasaranrenstra_id',
            //'refprogram_id',
            //'refkegiatan_id',
            //'refsubkegiatan_id',
            //'uraian_sasaransubkegiatan:ntext',
            //'uraian_indikatorsubkegiatan:ntext',
            //'refperiode_id',
            //'subkegiatan_target',
            //'subkegiatan_satuan',
            //'refpegawaibappeda_id',
            //'date_start',
            //'expired_date',
            //'status_simonacascadingsubkegiatan',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, SimonaCascadingsubkegiatan $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'refsimonacascadingsubkegiatan_id' => $model->refsimonacascadingsubkegiatan_id]);
                 }
            ],
        ],
    ]); ?>


</div>
