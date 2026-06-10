<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var backend\models\SakipLkekomponen $model */

$this->title = $model->reflkekomponen_id;
$this->params['breadcrumbs'][] = ['label' => 'Sakip Lkekomponens', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="sakip-lkekomponen-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'reflkekomponen_id' => $model->reflkekomponen_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'reflkekomponen_id' => $model->reflkekomponen_id], [
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
            'reflkekomponen_id',
            'uraian_lkekomponen:ntext',
        ],
    ]) ?>

</div>
