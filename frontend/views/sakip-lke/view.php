<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var frontend\models\SakipLke $model */

$this->title = $model->reflke_id;
$this->params['breadcrumbs'][] = ['label' => 'Sakip Lkes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="sakip-lke-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'reflke_id' => $model->reflke_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'reflke_id' => $model->reflke_id], [
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
            'reflke_id',
            'refperiode_id',
            'refskpd_id',
            'reflkekomponen_id',
            'reflkesubkomponen_id',
            'unit_jawaban:ntext',
            'unit_nilai',
        ],
    ]) ?>

</div>
