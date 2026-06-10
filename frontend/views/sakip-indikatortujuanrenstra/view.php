<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var frontend\models\SakipIndikatortujuanrenstra $model */

$this->title = $model->refindikatortujuanrenstra_id;
$this->params['breadcrumbs'][] = ['label' => 'Sakip Indikatortujuanrenstras', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="sakip-indikatortujuanrenstra-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'refindikatortujuanrenstra_id' => $model->refindikatortujuanrenstra_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'refindikatortujuanrenstra_id' => $model->refindikatortujuanrenstra_id], [
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
            'refindikatortujuanrenstra_id',
            'uraian_indikatortujuanrenstra:ntext',
            'reftujuanrenstra_id',
            'refsasaranrenstra_id',
            'refskpd_id',
            'refperiode_id',
        ],
    ]) ?>

</div>