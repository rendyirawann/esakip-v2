<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var frontend\models\SakipIndikatorcascadingkegiatan $model */

$this->title = $model->refindikatorkegiatan_id;
$this->params['breadcrumbs'][] = ['label' => 'Sakip Indikatorcascadingkegiatans', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="sakip-indikatorcascadingkegiatan-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'refindikatorkegiatan_id' => $model->refindikatorkegiatan_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'refindikatorkegiatan_id' => $model->refindikatorkegiatan_id], [
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
            'refindikatorkegiatan_id',
            'refcascadingrenstraprogram_id',
            'refcascadingrenstrakegiatan_id',
            'refsasaranrenstra_id',
            'refskpd_id',
            'refperiode_id',
            'refprogram_id',
            'refkegiatan_id',
            'target_rkt',
            'target_rkt_p',
            'target_pk',
            'target_pk_p',
            'realisasi',
            'capaian',
            'keterangan:ntext',
            'analisis:ntext',
        ],
    ]) ?>

</div>
