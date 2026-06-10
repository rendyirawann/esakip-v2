<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var frontend\models\SakipCascadingprogram $model */

$this->title = $model->refcascadingprogram_id;
$this->params['breadcrumbs'][] = ['label' => 'Sakip Cascadingprograms', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="sakip-cascadingprogram-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'refcascadingprogram_id' => $model->refcascadingprogram_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'refcascadingprogram_id' => $model->refcascadingprogram_id], [
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
            'refcascadingprogram_id',
            'refsasaran_id',
            'refskpd_id',
            'reftujuan_id',
            'refmisi_id',
            'refsasaranrenstra_id',
            'refindikatorsasaranrenstra_id',
            'refbidang_id',
            'refprogram_id',
            'uraian_sasaranprogram:ntext',
            'uraian_indikatorprogram:ntext',
            'refperiode_id',
            'program_target',
            'program_satuan',
        ],
    ]) ?>

</div>
