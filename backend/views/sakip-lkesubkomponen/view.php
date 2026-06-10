<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var backend\models\SakipLkesubkomponen $model */

$this->title = $model->reflkesubkomponen_id;
$this->params['breadcrumbs'][] = ['label' => 'Sakip Lkesubkomponens', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="sakip-lkesubkomponen-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'reflkesubkomponen_id' => $model->reflkesubkomponen_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'reflkesubkomponen_id' => $model->reflkesubkomponen_id], [
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
            'reflkesubkomponen_id',
            'reflkekomponen_id',
            'uraian_lkesubkomponen:ntext',
            'bobot_lkesubkomponen',
        ],
    ]) ?>

</div>
