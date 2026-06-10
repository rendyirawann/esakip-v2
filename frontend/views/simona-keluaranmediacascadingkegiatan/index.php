<?php

use frontend\models\SimonaKeluaranmediacascadingkegiatan;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var frontend\models\search\SimonaKeluaranmediacascadingkegiatanSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Simona Keluaranmediacascadingkegiatans';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="simona-keluaranmediacascadingkegiatan-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Simona Keluaranmediacascadingkegiatan', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'refsimonakeluaranmediacascadingkegiatan_id',
            'refsimonacascadingkegiatan_id',
            'file',
            'nama_file:ntext',
            'refuser_id',
            //'refskpd_id',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, SimonaKeluaranmediacascadingkegiatan $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'refsimonakeluaranmediacascadingkegiatan_id' => $model->refsimonakeluaranmediacascadingkegiatan_id]);
                 }
            ],
        ],
    ]); ?>


</div>
