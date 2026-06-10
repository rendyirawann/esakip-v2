<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var frontend\models\SimonaKeluaranmediacascadingkegiatan $model */

$this->title = $model->refsimonakeluaranmediacascadingkegiatan_id;
$this->params['breadcrumbs'][] = ['label' => 'Simona Keluaranmediacascadingkegiatans', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="simona-keluaranmediacascadingkegiatan-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'refsimonakeluaranmediacascadingkegiatan_id' => $model->refsimonakeluaranmediacascadingkegiatan_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'refsimonakeluaranmediacascadingkegiatan_id' => $model->refsimonakeluaranmediacascadingkegiatan_id], [
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
            'refsimonakeluaranmediacascadingkegiatan_id',
            'refsimonacascadingkegiatan_id',
            'file',
            'nama_file:ntext',
            'refuser_id',
            'refskpd_id',
        ],
    ]) ?>

</div>
