<?php

use backend\models\SakipLkesubkriteria;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var backend\models\search\SakipLkesubkriteriaSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Sakip Lkesubkriterias';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sakip-lkesubkriteria-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Sakip Lkesubkriteria', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'reflkesubkriteria_id',
            'reflkekomponen_id',
            'reflkesubkomponen_id',
            'uraian_lkesubkriteria:ntext',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, SakipLkesubkriteria $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'reflkesubkriteria_id' => $model->reflkesubkriteria_id]);
                 }
            ],
        ],
    ]); ?>


</div>
