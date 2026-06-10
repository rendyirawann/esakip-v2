<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\TblSampah */

$this->title = 'Detail Data Urusan';
$this->params['breadcrumbs'][] = ['label' => 'Detail Data Urusan', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sakip-urusan-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
    'model' => $model,
    'attributes' => [
        'urusan_id',
        'kode_urusan',
        'nama_urusan:ntext',
        [
            'attribute' => 'urusan_isaktif',
            'format' => 'raw', // Mengizinkan HTML rendering
            'value' => function ($model) {
                if ($model->urusan_isaktif === 'T') {
                    return Html::tag('span', 'Aktif', ['class' => 'btn btn-success']);
                } else if ($model->urusan_isaktif === 'F') {
                    return Html::tag('span', 'Tidak Aktif', ['class' => 'btn btn-warning']);
                }
            },
        ],
    ],
]) ?>




</div>
