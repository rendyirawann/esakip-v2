<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\SakipPeriode5tahun */

$this->title = 'Detail Data Periode 5 Tahun';
$this->params['breadcrumbs'][] = ['label' => 'Detail Data Periode 5 Tahun', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sakip-periode-5tahun-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'refperiode_5tahun_id',
            'nama_periode',
            'tahun_mulai',
            'tahun_selesai',
            [
                'attribute' => 'is_aktif',
                'format' => 'raw',
                'value' => function ($model) {
                    if ($model->is_aktif === '1') {
                        return Html::tag('span', 'Aktif', ['class' => 'btn btn-success']);
                    } else {
                        return Html::tag('span', 'Tidak Aktif', ['class' => 'btn btn-warning']);
                    }
                },
            ],
        ],
    ]) ?>

</div>
