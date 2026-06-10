<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var frontend\models\SimonaMediacascadingsubkegiatanOpd $model */

$this->title = $model->refsimonamediacascadingsubkegiatanopd_id;
$this->params['breadcrumbs'][] = ['label' => 'Simona Mediacascadingsubkegiatan Opds', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="simona-mediacascadingsubkegiatan-opd-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'refsimonamediacascadingsubkegiatanopd_id' => $model->refsimonamediacascadingsubkegiatanopd_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'refsimonamediacascadingsubkegiatanopd_id' => $model->refsimonamediacascadingsubkegiatanopd_id], [
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
            'refsimonamediacascadingsubkegiatanopd_id',
            'refsimonacascadingsubkegiatan_id',
            'file',
            'nama_file:ntext',
            'refuser_id',
            'refskpd_id',
        ],
    ]) ?>

</div>
