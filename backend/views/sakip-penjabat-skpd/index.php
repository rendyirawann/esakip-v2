<?php

use backend\models\SakipPenjabatSkpd;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var backend\models\search\SakipPenjabatSkpdSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Sakip Penjabat Skpds';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sakip-penjabat-skpd-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Sakip Penjabat Skpd', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'refpenjabatskpd_id',
            'refskpd_id',
            'refperiode_id',
            'nama_penjabat:ntext',
            'nip_penjabat',
            //'jabatan_eselon:ntext',
            //'pangkat_eselon',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, SakipPenjabatSkpd $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'refpenjabatskpd_id' => $model->refpenjabatskpd_id]);
                 }
            ],
        ],
    ]); ?>


</div>
