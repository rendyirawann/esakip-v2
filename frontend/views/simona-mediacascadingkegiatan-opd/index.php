<?php

use frontend\models\SimonaMediacascadingkegiatanOpd;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var frontend\models\search\SimonaMediacascadingkegiatanOpdSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Simona Mediacascadingkegiatan Opds';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="simona-mediacascadingkegiatan-opd-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Simona Mediacascadingkegiatan Opd', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'refsimonamediacascadingkegiatanopd_id',
            'refsimonacascadingkegiatan_id',
            'file',
            'nama_file:ntext',
            'refuser_id',
            //'refskpd_id',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, SimonaMediacascadingkegiatanOpd $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'refsimonamediacascadingkegiatanopd_id' => $model->refsimonamediacascadingkegiatanopd_id]);
                 }
            ],
        ],
    ]); ?>


</div>
