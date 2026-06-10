<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\TblSampah */

$this->title = 'Detail Data Sasaran Perubahan';
$this->params['breadcrumbs'][] = ['label' => 'Detail Data Sasaran Perubahan', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sakip-sasaran-p-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'refsasaran_p_id',
            [
                'attribute' => 'uraian_sasaran_p',
                'format' => 'raw', // Mengizinkan rendering HTML
                'contentOptions' => [
                    'style' => 'white-space: pre-wrap;', // Mengizinkan word wrap
                ],
                'value' => function ($model) {
                    return $model->uraian_sasaran_p;
                },
            ],
            [
                'attribute' => 'refvisi_p_id',
                'label' => 'Visi Perubahan Terkait',
                'format' => 'raw', // Mengizinkan rendering HTML
                'contentOptions' => [
                    'style' => 'white-space: pre-wrap;', // Mengizinkan word wrap
                ],
                'value' => function ($model) {
                    return $model->visi ? $model->visi->uraian_visi_p : 'Tidak ada visi Perubahan';
                },
            ],
            [
                'attribute' => 'refmisi_p_id',
                'label' => 'Misi Perubahan Terkait',
                'format' => 'raw', // Mengizinkan rendering HTML
                'contentOptions' => [
                    'style' => 'white-space: pre-wrap;', // Mengizinkan word wrap
                ],
                'value' => function ($model) {
                    return $model->misi ? $model->misi->uraian_misi_p : 'Tidak ada misi Perubahan';
                },
            ],
            [
                'attribute' => 'reftujuan_p_id',
                'label' => 'Tujuan Perubahan Terkait',
                'format' => 'raw', // Mengizinkan rendering HTML
                'contentOptions' => [
                    'style' => 'white-space: pre-wrap;', // Mengizinkan word wrap
                ],
                'value' => function ($model) {
                    return $model->tujuan ? $model->tujuan->uraian_tujuan_p : 'Tidak ada Tujuan Perubahan';
                },
            ],
            [
                'attribute' => 'refperiode_id',
                'label' => 'Periode Tahun',
                'value' => function ($model) {
                    return $model->periode ? $model->periode->periode : 'Tidak ada periode';
                },
            ],
            [
                'attribute' => 'sasaran_p_isaktif',
                'format' => 'raw', // Mengizinkan HTML rendering
                'value' => function ($model) {
                    if ($model->sasaran_p_isaktif === 'T') {
                        return Html::tag('span', 'Aktif', ['class' => 'btn btn-success']);
                    } else if ($model->sasaran_p_isaktif === 'F') {
                        return Html::tag('span', 'Tidak Aktif', ['class' => 'btn btn-warning']);
                    }
                },
            ],
        ],
    ]) ?>




</div>