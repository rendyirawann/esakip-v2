<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\TblSampah */

$this->title = 'Detail Data Visi Perubahan';
$this->params['breadcrumbs'][] = ['label' => 'Detail Data Visi Perubahan', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sakip-visi-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'refvisi_p_id',
            [
                'attribute' => 'uraian_visi_p',
                'format' => 'raw', // Mengizinkan rendering HTML
                'contentOptions' => [
                    'style' => 'white-space: pre-wrap;', // Mengizinkan word wrap
                ],
                'value' => function ($model) {
                    return $model->uraian_visi_p;
                },
            ],
            [
                'attribute' => 'penjabaran_visi_p',
                'format' => 'raw', // Mengizinkan rendering HTML
                'contentOptions' => [
                    'style' => 'white-space: pre-wrap;', // Mengizinkan word wrap
                ],
                'value' => function ($model) {
                    return $model->penjabaran_visi_p;
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
                'attribute' => 'visi_p_isaktif',
                'format' => 'raw', // Mengizinkan HTML rendering
                'value' => function ($model) {
                    if ($model->visi_p_isaktif === 'T') {
                        return Html::tag('span', 'Aktif', ['class' => 'btn btn-success']);
                    } else if ($model->visi_p_isaktif === 'F') {
                        return Html::tag('span', 'Tidak Aktif', ['class' => 'btn btn-warning']);
                    }
                },
            ],
        ],
    ]) ?>




</div>