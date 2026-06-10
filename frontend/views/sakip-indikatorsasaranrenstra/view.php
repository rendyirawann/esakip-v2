<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var frontend\models\SakipIndikatorsasaranrenstra $model */

$this->title = $model->refindikatorsasaranrenstra_id;
$this->params['breadcrumbs'][] = ['label' => 'Sakip Indikatorsasaranrenstras', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="sakip-indikatorsasaranrenstra-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'refindikatorsasaranrenstra_id' => $model->refindikatorsasaranrenstra_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'refindikatorsasaranrenstra_id' => $model->refindikatorsasaranrenstra_id], [
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
            'refindikatorsasaranrenstra_id',
            'uraian_indikatorsasaranrenstra:ntext',
            'refsasaranrenstra_id',
            'refskpd_id',
            'refperiode_id',
            'satuan_target',
            'target_rkt',
            'target_rkt_p',
            'target_pk',
            'target_pk_p',
            'realisasi',
            'capaian',
            'analisis',
            'keterangan:ntext',
            'indikatorsasaranrenstra_isaktif',
            'iku_isaktif',
            'pk_isaktif',
        ],
    ]) ?>

</div>