<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var frontend\models\SakipIndikatorcascadingsubkegiatan $model */

$this->title = $model->refindikatorsubkegiatan_id;
$this->params['breadcrumbs'][] = ['label' => 'Sakip Indikatorcascadingsubkegiatans', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="sakip-indikatorcascadingsubkegiatan-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'refindikatorsubkegiatan_id' => $model->refindikatorsubkegiatan_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'refindikatorsubkegiatan_id' => $model->refindikatorsubkegiatan_id], [
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
            'refindikatorsubkegiatan_id',
            'refcascadingrenstraprogram_id',
            'refcascadingrenstrakegiatan_id',
            'refcascadingrenstrasubkegiatan_id',
            'refsasaranrenstra_id',
            'refskpd_id',
            'refperiode_id',
            'refprogram_id',
            'refkegiatan_id',
            'refsubkegiatan_id',
            'target_rkt',
            'anggaran_rkt',
            'target_rkt_p',
            'anggaran_rkt_p',
            'target_pk',
            'anggaran_pk',
            'target_pk_p',
            'anggaran_pk_p',
            'realisasi',
            'capaian',
            'keterangan:ntext',
            'analisis:ntext',
        ],
    ]) ?>

</div>
