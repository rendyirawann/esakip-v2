<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var frontend\models\SimonaCascadingkegiatan $model */

$this->title = $model->refsimonacascadingkegiatan_id;
$this->params['breadcrumbs'][] = ['label' => 'Simona Cascadingkegiatans', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="simona-cascadingkegiatan-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'refsimonacascadingkegiatan_id' => $model->refsimonacascadingkegiatan_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'refsimonacascadingkegiatan_id' => $model->refsimonacascadingkegiatan_id], [
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
            'refsimonacascadingkegiatan_id',
            'refcascadingprogram_id',
            'refcascadingkegiatan_id',
            'refskpd_id',
            'refsasaranrenstra_id',
            'refindikatorsasaranrenstra_id',
            'refprogram_id',
            'refkegiatan_id',
            'uraian_sasarankegiatan:ntext',
            'uraian_indikatorkegiatan:ntext',
            'refperiode_id',
            'kegiatan_target',
            'kegiatan_satuan',
            'refpegawaibappeda_id',
            'date_start',
            'expired_date',
            'status_simonacascadingkegiatan',
        ],
    ]) ?>

</div>
