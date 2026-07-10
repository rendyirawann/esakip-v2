<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\TblSampah */

$this->title = 'Detail Data Visi';
$this->params['breadcrumbs'][] = ['label' => 'Detail Data Visi', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sakip-visi-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
    'model' => $model,
    'attributes' => [
        'refvisi_id',
        [
            'attribute' => 'uraian_visi',
            'format' => 'raw', // Mengizinkan rendering HTML
            'contentOptions' => [
                'style' => 'white-space: pre-wrap;', // Mengizinkan word wrap
            ],
            'value' => function ($model) {
                return $model->uraian_visi;
            },
        ],   
        [
            'attribute' => 'penjabaran_visi',
            'format' => 'raw', // Mengizinkan rendering HTML
            'contentOptions' => [
                'style' => 'white-space: pre-wrap;', // Mengizinkan word wrap
            ],
            'value' => function ($model) {
                return $model->penjabaran_visi;
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
            'attribute' => 'visi_isaktif',
            'format' => 'raw', // Mengizinkan HTML rendering
            'value' => function ($model) {
                if ($model->visi_isaktif === 'T') {
                    return Html::tag('span', 'Aktif', ['class' => 'btn btn-success']);
                } else if ($model->visi_isaktif === 'F') {
                    return Html::tag('span', 'Tidak Aktif', ['class' => 'btn btn-warning']);
                }
            },
        ],
    ],
]) ?>




</div>
