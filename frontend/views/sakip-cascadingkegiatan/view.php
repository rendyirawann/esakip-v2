<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var frontend\models\SakipCascadingkegiatan $model */

$this->title = $model->refcascadingkegiatan_id;
$this->params['breadcrumbs'][] = ['label' => 'Sakip Cascadingkegiatans', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="sakip-cascadingkegiatan-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'refcascadingkegiatan_id' => $model->refcascadingkegiatan_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'refcascadingkegiatan_id' => $model->refcascadingkegiatan_id], [
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
            'refcascadingkegiatan_id',
            'refcascadingprogram_id',
            'refprogram_id',
            'refkegiatan_id',
            'uraian_sasarankegiatan:ntext',
            'uraian_indikatorkegiatan:ntext',
            'refperiode_id',
            'refskpd_id',
            'kegiatan_target',
            'kegiatan_satuan',
        ],
    ]) ?>

</div>
