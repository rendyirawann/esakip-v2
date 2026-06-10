<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var frontend\models\SakipRenstratujuan $model */

$this->title = $model->refrenstratujuan_id;
$this->params['breadcrumbs'][] = ['label' => 'Sakip Renstra Tujuan', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="sakip-renstratujuan-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'refrenstratujuan_id' => $model->refrenstratujuan_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'refrenstratujuan_id' => $model->refrenstratujuan_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'refrenstratujuan_id',
            'uraian_renstratujuan:ntext',
            'refskpd_id',
            'refmisi_id',
            'refsasaranrenstra_id',
            'refsasaran_id',
            'reftujuan_id',
            'refperiode_id',
            'user_create',
            'date_create',
            'user_edit',
            'date_edit',
            'user_delete',
            'date_delete',
        ],
    ]) ?>

</div>