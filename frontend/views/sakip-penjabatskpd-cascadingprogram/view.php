<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var frontend\models\SakipPenjabatskpdCascadingprogram $model */

$this->title = $model->refpenjabatcascadingprogram_id;
$this->params['breadcrumbs'][] = ['label' => 'Sakip Penjabatskpd Cascadingprograms', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="sakip-penjabatskpd-cascadingprogram-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'refpenjabatcascadingprogram_id' => $model->refpenjabatcascadingprogram_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'refpenjabatcascadingprogram_id' => $model->refpenjabatcascadingprogram_id], [
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
            'refpenjabatcascadingprogram_id',
            'refpenjabatskpd_id',
            'refeselon_id',
            'refcascadingprogram_id',
            'refindikatorprogram_id',
            'refskpd_id',
            'refperiode_id',
            'refsasaranrenstra_id',
            'refindikatorsasaranrenstra_id',
            'refbidang_id',
            'refprogram_id',
            'uraian_sasaranprogram:ntext',
            'uraian_indikatorprogram:ntext',
            'program_target',
            'program_satuan',
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
