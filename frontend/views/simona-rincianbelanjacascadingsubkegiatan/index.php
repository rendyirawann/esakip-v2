<?php

use frontend\models\SimonaRincianbelanjacascadingsubkegiatan;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var frontend\models\search\SimonaRincianbelanjacascadingsubkegiatanSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Simona Rincianbelanjacascadingsubkegiatans';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="simona-rincianbelanjacascadingsubkegiatan-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Simona Rincianbelanjacascadingsubkegiatan', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'refsimonarincianbelanjacascadingsubkegiatan_id',
            'refsimonacascadingsubkegiatan_id',
            'detail_rincianbelanja:ntext',
            'satuan_rincianbelanja',
            'jumlah_rincianbelanja',
            //'anggaran_rincianbelanja',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, SimonaRincianbelanjacascadingsubkegiatan $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'refsimonarincianbelanjacascadingsubkegiatan_id' => $model->refsimonarincianbelanjacascadingsubkegiatan_id]);
                 }
            ],
        ],
    ]); ?>


</div>
