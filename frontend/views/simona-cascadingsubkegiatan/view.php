<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var frontend\models\SimonaCascadingsubkegiatan $model */

$this->title = $model->refsimonacascadingsubkegiatan_id;
$this->params['breadcrumbs'][] = ['label' => 'Simona Cascadingsubkegiatans', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="simona-cascadingsubkegiatan-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'refsimonacascadingsubkegiatan_id' => $model->refsimonacascadingsubkegiatan_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'refsimonacascadingsubkegiatan_id' => $model->refsimonacascadingsubkegiatan_id], [
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
            'refsimonacascadingsubkegiatan_id',
            'refcascadingprogram_id',
            'refcascadingkegiatan_id',
            'refcascadingsubkegiatan_id',
            'refskpd_id',
            'refsasaranrenstra_id',
            'refindikatorsasaranrenstra_id',
            'refprogram_id',
            'refkegiatan_id',
            'refsubkegiatan_id',
            'uraian_sasaransubkegiatan:ntext',
            'uraian_indikatorsubkegiatan:ntext',
            'refperiode_id',
            'subkegiatan_target',
            'subkegiatan_satuan',
            'refpegawaibappeda_id',
            'date_start',
            'expired_date',
            'status_simonacascadingsubkegiatan',
        ],
    ]) ?>

</div>
