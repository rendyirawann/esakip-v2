<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var frontend\models\SakipPenjabatskpdCascadingkegiatan $model */

$this->title = $model->refpenjabatcascadingkegiatan_id;
$this->params['breadcrumbs'][] = ['label' => 'Sakip Penjabatskpd Cascadingkegiatans', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="sakip-penjabatskpd-cascadingkegiatan-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'refpenjabatcascadingkegiatan_id' => $model->refpenjabatcascadingkegiatan_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'refpenjabatcascadingkegiatan_id' => $model->refpenjabatcascadingkegiatan_id], [
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
            'refpenjabatcascadingkegiatan_id',
            'refpenjabatskpd_id',
            'refeselon_id',
            'refcascadingprogram_id',
            'refcascadingkegiatan_id',
            'refindikatorkegiatan_id',
            'refskpd_id',
            'refperiode_id',
            'refsasaranrenstra_id',
            'refindikatorsasaranrenstra_id',
            'refprogram_id',
            'refkegiatan_id',
            'uraian_sasarankegiatan:ntext',
            'uraian_indikatorkegiatan:ntext',
            'kegiatan_target',
            'kegiatan_satuan',
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
