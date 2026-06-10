<?php

use frontend\models\SimonaRincianbelanjacascadingkegiatan;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var frontend\models\search\SimonaRincianbelanjacascadingkegiatanSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Simona Rincianbelanjacascadingkegiatans';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="simona-rincianbelanjacascadingkegiatan-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Simona Rincianbelanjacascadingkegiatan', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'refsimonarincianbelanjacascadingkegiatan_id',
            'refsimonacascadingkegiatan_id',
            'detail_rincianbelanja:ntext',
            'satuan_rincianbelanja',
            'jumlah_rincianbelanja',
            //'anggaran_rincianbelanja',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, SimonaRincianbelanjacascadingkegiatan $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'refsimonarincianbelanjacascadingkegiatan_id' => $model->refsimonarincianbelanjacascadingkegiatan_id]);
                 }
            ],
        ],
    ]); ?>


</div>
