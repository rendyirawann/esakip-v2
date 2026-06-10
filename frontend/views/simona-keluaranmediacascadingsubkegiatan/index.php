<?php

use frontend\models\SimonaKeluaranmediacascadingsubkegiatan;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var frontend\models\search\SimonaKeluaranmediacascadingsubkegiatanSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Simona Keluaranmediacascadingsubkegiatans';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="simona-keluaranmediacascadingsubkegiatan-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Simona Keluaranmediacascadingsubkegiatan', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'refsimonakeluaranmediacascadingsubkegiatan_id',
            'refsimonacascadingsubkegiatan_id',
            'refsimonarincianbelanjacascadingkegiatan_id',
            'file',
            'nama_file:ntext',
            //'refuser_id',
            //'refskpd_id',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, SimonaKeluaranmediacascadingsubkegiatan $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'refsimonakeluaranmediacascadingsubkegiatan_id' => $model->refsimonakeluaranmediacascadingsubkegiatan_id]);
                 }
            ],
        ],
    ]); ?>


</div>
