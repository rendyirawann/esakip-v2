<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var frontend\models\SakipSasaranrenstra $model */

$this->title = $model->refsasaranrenstra_id;
$this->params['breadcrumbs'][] = ['label' => 'Sakip Sasaranrenstras', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="sakip-sasaranrenstra-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'refsasaranrenstra_id' => $model->refsasaranrenstra_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'refsasaranrenstra_id' => $model->refsasaranrenstra_id], [
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
            'refsasaranrenstra_id',
            'uraian_sasaranrenstra:ntext',
            'refskpd_id',
            'refsasaran_id',
            'reftujuanrenstra_id',
            'sasaranrenstra_isaktif',
        ],
    ]) ?>

</div>
