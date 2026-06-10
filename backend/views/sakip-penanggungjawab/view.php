<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var backend\models\SakipPenanggungjawab $model */

$this->title = $model->refpenanggungjawab_id;
$this->params['breadcrumbs'][] = ['label' => 'Sakip Penanggungjawabs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="sakip-penanggungjawab-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'refpenanggungjawab_id' => $model->refpenanggungjawab_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'refpenanggungjawab_id' => $model->refpenanggungjawab_id], [
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
            'refpenanggungjawab_id',
            'refpegawai_id',
            'refbidangbappeda_id',
            'refuser_id',
            'refskpd_id',
        ],
    ]) ?>

</div>
