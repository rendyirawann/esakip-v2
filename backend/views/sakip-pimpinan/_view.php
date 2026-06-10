<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */

$this->title = 'Detail Data Pimpinan';
$this->params['breadcrumbs'][] = ['label' => 'Detail Data Pimpinan', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sakip-pimpinan-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'refpimpinan_id',
            [
                'attribute' => 'refperiode_id',
                'label' => 'Periode Pimpinan',
                'value' => function ($model) {
                    return $model->refPeriode ? $model->refPeriode->periode : 'Tidak ada Periode';
                },
            ],
            'nama_pimpinan',
            'jabatan_pimpinan',
            'nama_wpimpinan',
            'jabatan_wpimpinan',
            'user_edit',
            'date_edit',
        ],
    ]) ?>


</div>