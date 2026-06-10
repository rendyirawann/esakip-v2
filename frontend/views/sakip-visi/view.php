<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var frontend\models\SakipVisi $model */

$this->title = $model->refvisi_id;
$this->params['breadcrumbs'][] = ['label' => 'Sakip Visis', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="sakip-visi-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'refvisi_id' => $model->refvisi_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'refvisi_id' => $model->refvisi_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'refvisi_id',
            'uraian_visi:ntext',
            'penjabaran_visi:ntext',
            'refperiode_id',
            'visi_isaktif',
        ],
    ]) ?>

</div>
