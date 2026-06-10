<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\TblSampah */

$this->title = 'Detail Data Misi Perubahan';
$this->params['breadcrumbs'][] = ['label' => 'Detail Data Misi Perubahan', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sakip-misi-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'refmisi_p_id',
            [
                'attribute' => 'uraian_misi_p',
                'format' => 'raw', // Mengizinkan rendering HTML
                'contentOptions' => [
                    'style' => 'white-space: pre-wrap;', // Mengizinkan word wrap
                ],
                'value' => function ($model) {
                    return $model->uraian_misi_p;
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
                'attribute' => 'misi_p_isaktif',
                'format' => 'raw', // Mengizinkan HTML rendering
                'value' => function ($model) {
                    if ($model->misi_p_isaktif === 'T') {
                        return Html::tag('span', 'Aktif', ['class' => 'btn btn-success']);
                    } else if ($model->misi_p_isaktif === 'F') {
                        return Html::tag('span', 'Tidak Aktif', ['class' => 'btn btn-warning']);
                    }
                },
            ],
        ],
    ]) ?>



</div>