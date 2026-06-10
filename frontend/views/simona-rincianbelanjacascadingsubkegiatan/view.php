<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var frontend\models\SimonaRincianbelanjacascadingsubkegiatan $model */

$this->title = $model->refsimonarincianbelanjacascadingsubkegiatan_id;
$this->params['breadcrumbs'][] = ['label' => 'Simona Rincianbelanjacascadingsubkegiatans', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="simona-rincianbelanjacascadingsubkegiatan-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'refsimonarincianbelanjacascadingsubkegiatan_id' => $model->refsimonarincianbelanjacascadingsubkegiatan_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'refsimonarincianbelanjacascadingsubkegiatan_id' => $model->refsimonarincianbelanjacascadingsubkegiatan_id], [
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
            'refsimonarincianbelanjacascadingsubkegiatan_id',
            'refsimonacascadingsubkegiatan_id',
            'detail_rincianbelanja:ntext',
            'satuan_rincianbelanja',
            'jumlah_rincianbelanja',
            'anggaran_rincianbelanja',
        ],
    ]) ?>

</div>
