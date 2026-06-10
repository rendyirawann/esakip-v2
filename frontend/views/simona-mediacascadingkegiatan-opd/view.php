<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var frontend\models\SimonaMediacascadingkegiatanOpd $model */

$this->title = $model->refsimonamediacascadingkegiatanopd_id;
$this->params['breadcrumbs'][] = ['label' => 'Simona Mediacascadingkegiatan Opds', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="simona-mediacascadingkegiatan-opd-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'refsimonamediacascadingkegiatanopd_id' => $model->refsimonamediacascadingkegiatanopd_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'refsimonamediacascadingkegiatanopd_id' => $model->refsimonamediacascadingkegiatanopd_id], [
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
            'refsimonamediacascadingkegiatanopd_id',
            'refsimonacascadingkegiatan_id',
            'file',
            'nama_file:ntext',
            'refuser_id',
            'refskpd_id',
        ],
    ]) ?>

</div>
