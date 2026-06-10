<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\TblSampah */

$this->title = 'Detail Data Sumber Dana';
$this->params['breadcrumbs'][] = ['label' => 'Detail Data Sumber Dana', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sakip-sumberdana-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
    'model' => $model,
    'attributes' => [
        'refsumberdana_id',
        'kode_sumberdana',
        'nama_sumberdana:ntext',
        [
            'attribute' => 'sumberdana_isaktif',
            'format' => 'raw', // Mengizinkan HTML rendering
            'value' => function ($model) {
                if ($model->sumberdana_isaktif === 'T') {
                    return Html::tag('span', 'Aktif', ['class' => 'btn btn-success']);
                } else if ($model->sumberdana_isaktif === 'F') {
                    return Html::tag('span', 'Tidak Aktif', ['class' => 'btn btn-warning']);
                }
            },
        ],
    ],
]) ?>




</div>
