<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var frontend\models\SakipTujuanrenstra $model */

$this->title = $model->reftujuanrenstra_id;
$this->params['breadcrumbs'][] = ['label' => 'Sakip Tujuanrenstras', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="sakip-tujuanrenstra-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'reftujuanrenstra_id' => $model->reftujuanrenstra_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'reftujuanrenstra_id' => $model->reftujuanrenstra_id], [
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
            'reftujuanrenstra_id',
            'uraian_tujuanrenstra:ntext',
            'refskpd_id',
            'refmisi_id',
            'reftujuan_id',
            'refsasaranrenstra_id',
            'refperiode_id',
            'user_create',
            'date_create',
            'user_edit',
            'date_edit',
            'user_delete',
            'date_delete',
            'tujuanrenstra_isaktif',
        ],
    ]) ?>

</div>