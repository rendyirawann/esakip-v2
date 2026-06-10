<?php

use frontend\models\SimonaMediacascadingkegiatan;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var frontend\models\search\SimonaMediacascadingkegiatanSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Simona Mediacascadingkegiatans';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="simona-mediacascadingkegiatan-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Simona Mediacascadingkegiatan', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'refsimonamediacascadingkegiatan_id',
            'refsimonacascadingkegiatan_id',
            'file',
            'nama_file:ntext',
            'refuser_id',
            //'refskpd_id',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, SimonaMediacascadingkegiatan $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'refsimonamediacascadingkegiatan_id' => $model->refsimonamediacascadingkegiatan_id]);
                 }
            ],
        ],
    ]); ?>


</div>
