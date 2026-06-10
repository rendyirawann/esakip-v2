<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\TblSampah */

$this->title = 'Detail Data Periode';
$this->params['breadcrumbs'][] = ['label' => 'Detail Data Periode', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sakip-periode-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
    'model' => $model,
    'attributes' => [
        'refperiode_id',
        'periode',
        [
            'attribute' => 'periode_isaktif',
            'format' => 'raw', // Mengizinkan HTML rendering
            'value' => function ($model) {
                if ($model->periode_isaktif === 'T') {
                    return Html::tag('span', 'Aktif', ['class' => 'btn btn-success']);
                } else if ($model->periode_isaktif === 'F') {
                    return Html::tag('span', 'Tidak Aktif', ['class' => 'btn btn-warning']);
                }
            },
        ],
    ],
]) ?>




</div>
