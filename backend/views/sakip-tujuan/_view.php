<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\TblSampah */

$this->title = 'Detail Data Tujuan';
$this->params['breadcrumbs'][] = ['label' => 'Detail Data Tujuan', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sakip-tujuan-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'reftujuan_id',
            [
                'attribute' => 'uraian_tujuan',
                'format' => 'raw', // Mengizinkan rendering HTML
                'contentOptions' => [
                    'style' => 'white-space: pre-wrap;', // Mengizinkan word wrap
                ],
                'value' => function ($model) {
                    return $model->uraian_tujuan;
                },
            ],
            [
                'attribute' => 'indikator_tujuan',
                'format' => 'raw', // Mengizinkan rendering HTML
                'contentOptions' => [
                    'style' => 'white-space: pre-wrap;', // Mengizinkan word wrap
                ],
                'value' => function ($model) {
                    return $model->indikator_tujuan;
                },
            ],
            [
                'attribute' => 'refvisi_id',
                'label' => 'Visi Terkait',
                'format' => 'raw', // Mengizinkan rendering HTML
                'contentOptions' => [
                    'style' => 'white-space: pre-wrap;', // Mengizinkan word wrap
                ],
                'value' => function ($model) {
                    return $model->visi ? $model->visi->uraian_visi : 'Tidak ada visi';
                },
            ],
            [
                'attribute' => 'refmisi_id',
                'label' => 'Misi Terkait',
                'format' => 'raw', // Mengizinkan rendering HTML
                'contentOptions' => [
                    'style' => 'white-space: pre-wrap;', // Mengizinkan word wrap
                ],
                'value' => function ($model) {
                    return $model->misi ? $model->misi->uraian_misi : 'Tidak ada misi';
                },
            ],
            [
                'attribute' => 'refperiode_id',
                'label' => 'Periode Tahun',
                'value' => function ($model) {
                    return $model->periodeLabel();
                },
            ],
            [
                'attribute' => 'tujuan_isaktif',
                'format' => 'raw', // Mengizinkan HTML rendering
                'value' => function ($model) {
                    if ($model->tujuan_isaktif === 'T') {
                        return Html::tag('span', 'Aktif', ['class' => 'btn btn-success']);
                    } else if ($model->tujuan_isaktif === 'F') {
                        return Html::tag('span', 'Tidak Aktif', ['class' => 'btn btn-warning']);
                    }
                },
            ],
        ],
    ]) ?>




</div>