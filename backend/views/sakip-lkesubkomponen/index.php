<?php

use backend\models\SakipLkesubkomponen;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var backend\models\search\SakipLkesubkomponenSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Sakip Lkesubkomponens';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sakip-lkesubkomponen-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Sakip Lkesubkomponen', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'reflkesubkomponen_id',
            'reflkekomponen_id',
            'uraian_lkesubkomponen:ntext',
            'bobot_lkesubkomponen',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, SakipLkesubkomponen $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'reflkesubkomponen_id' => $model->reflkesubkomponen_id]);
                 }
            ],
        ],
    ]); ?>


</div>
