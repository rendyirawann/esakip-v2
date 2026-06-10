<?php

use backend\models\SakipLke;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var backend\models\search\SakipLkeSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Sakip Lkes';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sakip-lke-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Sakip Lke', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'reflke_id',
            'refperiode_id',
            'refskpd_id',
            'reflkekomponen_id',
            'reflkesubkomponen_id',
            //'unit_jawaban:ntext',
            //'unit_nilai',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, SakipLke $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'reflke_id' => $model->reflke_id]);
                 }
            ],
        ],
    ]); ?>


</div>
