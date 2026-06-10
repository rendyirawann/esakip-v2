<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var backend\models\SakipEselon $model */

$this->title = $model->refeselon_id;
$this->params['breadcrumbs'][] = ['label' => 'Sakip Eselons', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="sakip-eselon-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'refeselon_id' => $model->refeselon_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'refeselon_id' => $model->refeselon_id], [
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
            'refeselon_id',
            'title_eselon',
        ],
    ]) ?>

</div>