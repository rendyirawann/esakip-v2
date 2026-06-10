<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\TblSampah */

$this->title = 'Detail Data Sub Unit';
$this->params['breadcrumbs'][] = ['label' => 'Detail Data Sub Unit', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sakip-subunit-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
    'model' => $model,
    'attributes' => [
        'refsubunit_id',
        'kode_subunit',
        'nama_subunit:ntext',
        [
            'attribute' => 'subunit_isaktif',
            'format' => 'raw', // Mengizinkan HTML rendering
            'value' => function ($model) {
                if ($model->subunit_isaktif === 'T') {
                    return Html::tag('span', 'Aktif', ['class' => 'btn btn-success']);
                } else if ($model->subunit_isaktif === 'F') {
                    return Html::tag('span', 'Tidak Aktif', ['class' => 'btn btn-warning']);
                }
            },
        ],
    ],
]) ?>




</div>
