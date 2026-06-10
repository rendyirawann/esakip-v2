<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var frontend\models\SakipCascadingsubkegiatan $model */

$this->title = $model->refcascadingsubkegiatan_id;
$this->params['breadcrumbs'][] = ['label' => 'Sakip Cascadingsubkegiatans', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="sakip-cascadingsubkegiatan-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'refcascadingsubkegiatan_id' => $model->refcascadingsubkegiatan_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'refcascadingsubkegiatan_id' => $model->refcascadingsubkegiatan_id], [
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
            'refcascadingsubkegiatan_id',
            'refcascadingkegiatan_id',
            'refcascadingprogram_id',
            'refprogram_id',
            'refkegiatan_id',
            'refsubkegiatan_id',
            'uraian_sasaransubkegiatan:ntext',
            'uraian_indikatorsubkegiatan:ntext',
            'refperiode_id',
            'refskpd_id',
            'subkegiatan_target',
            'subkegiatan_satuan',
            'subkegiatan_anggaran',
        ],
    ]) ?>

</div>
