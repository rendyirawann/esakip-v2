<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var frontend\models\SimonaMediacascadingsubkegiatan $model */

$this->title = $model->refsimonamediacascadingsubkegiatan_id;
$this->params['breadcrumbs'][] = ['label' => 'Simona Mediacascadingsubkegiatans', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="simona-mediacascadingsubkegiatan-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'refsimonamediacascadingsubkegiatan_id' => $model->refsimonamediacascadingsubkegiatan_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'refsimonamediacascadingsubkegiatan_id' => $model->refsimonamediacascadingsubkegiatan_id], [
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
            'refsimonamediacascadingsubkegiatan_id',
            'refsimonacascadingsubkegiatan_id',
            'file',
            'nama_file:ntext',
            'refuser_id',
            'refskpd_id',
        ],
    ]) ?>

</div>
