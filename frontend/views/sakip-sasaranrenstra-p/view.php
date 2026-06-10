<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var frontend\models\SakipSasaranrenstraP $model */

$this->title = $model->refsasaranrenstra_p_id;
$this->params['breadcrumbs'][] = ['label' => 'Sakip Sasaranrenstra Ps', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="sakip-sasaranrenstra-p-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'refsasaranrenstra_p_id' => $model->refsasaranrenstra_p_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'refsasaranrenstra_p_id' => $model->refsasaranrenstra_p_id], [
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
            'refsasaranrenstra_p_id',
            'refsasaranrenstra_id',
            'uraian_sasaranrenstra_p:ntext',
            'refskpd_id',
            'refsasaran_p_id',
            'refvisi_p_id',
            'refmisi_p_id',
            'reftujuan_p_id',
            'refperiode_id',
            'reftujuanrenstra_p_id',
            'sasaranrenstra_p_isaktif',
            'alasan_sasaranrenstra_p:ntext',
            'formulasi_sasaranrenstra_p',
            'kriteria_sasaranrenstra_p:ntext',
        ],
    ]) ?>

</div>
