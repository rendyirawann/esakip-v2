<?php

use frontend\models\SimonaMediacascadingsubkegiatan;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var frontend\models\search\SimonaMediacascadingsubkegiatanSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Simona Mediacascadingsubkegiatans';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="simona-mediacascadingsubkegiatan-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Simona Mediacascadingsubkegiatan', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'refsimonamediacascadingsubkegiatan_id',
            'refsimonacascadingsubkegiatan_id',
            'file',
            'nama_file:ntext',
            'refuser_id',
            //'refskpd_id',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, SimonaMediacascadingsubkegiatan $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'refsimonamediacascadingsubkegiatan_id' => $model->refsimonamediacascadingsubkegiatan_id]);
                 }
            ],
        ],
    ]); ?>


</div>
