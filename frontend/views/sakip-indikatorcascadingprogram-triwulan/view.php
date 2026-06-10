<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var frontend\models\SakipIndikatorcascadingprogramTriwulan $model */

$this->title = $model->refindikatorprogramtriwulan_id;
$this->params['breadcrumbs'][] = ['label' => 'Sakip Indikatorcascadingprogram Triwulans', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="sakip-indikatorcascadingprogram-triwulan-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'refindikatorprogramtriwulan_id' => $model->refindikatorprogramtriwulan_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'refindikatorprogramtriwulan_id' => $model->refindikatorprogramtriwulan_id], [
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
            'refindikatorprogramtriwulan_id',
            'refindikatorprogram_id',
            'refcascadingprogram_id',
            'refsasaranrenstra_id',
            'refskpd_id',
            'refperiode_id',
            'reftriwulan_id',
            'refbidang_id',
            'refprogram_id',
            'triwulan_target_rkt',
            'triwulan_target_rkt_p',
            'triwulan_target_pk',
            'triwulan_target_pk_p',
            'triwulan_realisasi',
            'triwulan_capaian',
            'triwulan_keterangan:ntext',
            'triwulan_analisis:ntext',
        ],
    ]) ?>

</div>
