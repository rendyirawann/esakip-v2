<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var frontend\models\SimonaRincianbelanjacascadingkegiatan $model */

$this->title = $model->refsimonarincianbelanjacascadingkegiatan_id;
$this->params['breadcrumbs'][] = ['label' => 'Simona Rincianbelanjacascadingkegiatans', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="simona-rincianbelanjacascadingkegiatan-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'refsimonarincianbelanjacascadingkegiatan_id' => $model->refsimonarincianbelanjacascadingkegiatan_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'refsimonarincianbelanjacascadingkegiatan_id' => $model->refsimonarincianbelanjacascadingkegiatan_id], [
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
            'refsimonarincianbelanjacascadingkegiatan_id',
            'refsimonacascadingkegiatan_id',
            'detail_rincianbelanja:ntext',
            'satuan_rincianbelanja',
            'jumlah_rincianbelanja',
            'anggaran_rincianbelanja',
        ],
    ]) ?>

</div>
