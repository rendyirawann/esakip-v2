<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var frontend\models\SakipIndikatorcascadingsubkegiatanTriwulan $model */

$this->title = $model->refindikatorsubkegiatantriwulan_id;
$this->params['breadcrumbs'][] = ['label' => 'Sakip Indikatorcascadingsubkegiatan Triwulans', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="sakip-indikatorcascadingsubkegiatan-triwulan-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'refindikatorsubkegiatantriwulan_id' => $model->refindikatorsubkegiatantriwulan_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'refindikatorsubkegiatantriwulan_id' => $model->refindikatorsubkegiatantriwulan_id], [
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
            'refindikatorsubkegiatantriwulan_id',
            'refindikatorsubkegiatan_id',
            'refcascadingrestraprogram_id',
            'refcascadingrestrakegiatan_id',
            'refcascadingrestrasubkegiatan_id',
            'refsasaranrenstra_id',
            'refskpd_id',
            'refperiode_id',
            'refprogram_id',
            'refkegiatan_id',
            'refsubkegiatan_id',
            'triwulan_target_rkt',
            'triwulan_target_rkt_p',
            'triwulan_target_pk',
            'triwulan_target_pk_p',
            'triwulan_realisasi',
            'triwulan_capaian',
            'triwulan_keterangan:ntext',
            'triwulan_analisis:ntext',
            'triwulan_penyerapan_anggaran',
        ],
    ]) ?>

</div>
