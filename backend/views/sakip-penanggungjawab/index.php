<?php

use backend\models\SakipPenanggungjawab;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var backend\models\search\SakipPenanggungjawabSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Sakip Penanggungjawabs';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sakip-penanggungjawab-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Sakip Penanggungjawab', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); 
    ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'refpenanggungjawab_id',
            'refpegawai_id',
            'refbidangbappeda_id',
            'refuser_id',
            'refskpd_id',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, SakipPenanggungjawab $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'refpenanggungjawab_id' => $model->refpenanggungjawab_id]);
                }
            ],
        ],
    ]); ?>


</div>