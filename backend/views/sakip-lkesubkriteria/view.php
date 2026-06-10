<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var backend\models\SakipLkesubkriteria $model */

$this->title = $model->reflkesubkriteria_id;
$this->params['breadcrumbs'][] = ['label' => 'Sakip Lkesubkriterias', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="sakip-lkesubkriteria-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'reflkesubkriteria_id' => $model->reflkesubkriteria_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'reflkesubkriteria_id' => $model->reflkesubkriteria_id], [
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
            'reflkesubkriteria_id',
            'reflkekomponen_id',
            'reflkesubkomponen_id',
            'uraian_lkesubkriteria:ntext',
        ],
    ]) ?>

</div>
