<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var frontend\models\SimonaKeluaranmediacascadingsubkegiatan $model */

$this->title = $model->refsimonakeluaranmediacascadingsubkegiatan_id;
$this->params['breadcrumbs'][] = ['label' => 'Simona Keluaranmediacascadingsubkegiatans', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="simona-keluaranmediacascadingsubkegiatan-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'refsimonakeluaranmediacascadingsubkegiatan_id' => $model->refsimonakeluaranmediacascadingsubkegiatan_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'refsimonakeluaranmediacascadingsubkegiatan_id' => $model->refsimonakeluaranmediacascadingsubkegiatan_id], [
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
            'refsimonakeluaranmediacascadingsubkegiatan_id',
            'refsimonacascadingsubkegiatan_id',
            'refsimonarincianbelanjacascadingkegiatan_id',
            'file',
            'nama_file:ntext',
            'refuser_id',
            'refskpd_id',
        ],
    ]) ?>

</div>
