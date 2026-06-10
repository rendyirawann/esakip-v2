<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var frontend\models\SakipIndikatorcascadingprogram $model */

$this->title = $model->refindikatorprogram_id;
$this->params['breadcrumbs'][] = ['label' => 'Sakip Indikatorcascadingprograms', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="sakip-indikatorcascadingprogram-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'refindikatorprogram_id' => $model->refindikatorprogram_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'refindikatorprogram_id' => $model->refindikatorprogram_id], [
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
            'refindikatorprogram_id',
            'refcascadingprogram_id',
            'refsasaranrenstra_id',
            'refskpd_id',
            'refperiode_id',
            'refbidang_id',
            'refprogram_id',
            'target_rkt',
            'target_rkt_p',
            'target_pk',
            'target_pk_p',
            'realisasi',
            'capaian',
            'keterangan:ntext',
            'analisis:ntext',
        ],
    ]) ?>

</div>
