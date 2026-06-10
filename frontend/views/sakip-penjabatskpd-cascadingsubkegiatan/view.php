<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var frontend\models\SakipPenjabatskpdCascadingsubkegiatan $model */

$this->title = $model->refpenjabatcascadingsubkegiatan_id;
$this->params['breadcrumbs'][] = ['label' => 'Sakip Penjabatskpd Cascadingsubkegiatans', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="sakip-penjabatskpd-cascadingsubkegiatan-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'refpenjabatcascadingsubkegiatan_id' => $model->refpenjabatcascadingsubkegiatan_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'refpenjabatcascadingsubkegiatan_id' => $model->refpenjabatcascadingsubkegiatan_id], [
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
            'refpenjabatcascadingsubkegiatan_id',
            'refpenjabatskpd_id',
            'refeselon_id',
            'refcascadingprogram_id',
            'refcascadingkegiatan_id',
            'refcascadingsubkegiatan_id',
            'refindikatorsubkegiatan_id',
            'refskpd_id',
            'refperiode_id',
            'refsasaranrenstra_id',
            'refindikatorsasaranrenstra_id',
            'refprogram_id',
            'refkegiatan_id',
            'refsubkegiatan_id',
            'uraian_sasaransubkegiatan:ntext',
            'uraian_indikatorsubkegiatan:ntext',
            'subkegiatan_target',
            'subkegiatan_satuan',
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
