<?php

use backend\models\SakipKoordinasi;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var backend\models\search\SakipKoordinasiSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Sakip Koordinasis';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sakip-koordinasi-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Sakip Koordinasi', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'refkoordinasi_id',
            'refuser_id',
            'refskpd_id',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, SakipKoordinasi $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'refkoordinasi_id' => $model->refkoordinasi_id]);
                 }
            ],
        ],
    ]); ?>


</div>
