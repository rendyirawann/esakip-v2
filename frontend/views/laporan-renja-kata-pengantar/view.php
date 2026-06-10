<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var frontend\models\LaporanRenjaKataPengantar $model */

$this->title = $model->laporan_renja_kata_pengantar_id;
$this->params['breadcrumbs'][] = ['label' => 'Laporan Renja Kata Pengantars', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="laporan-renja-kata-pengantar-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'laporan_renja_kata_pengantar_id' => $model->laporan_renja_kata_pengantar_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'laporan_renja_kata_pengantar_id' => $model->laporan_renja_kata_pengantar_id], [
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
            'laporan_renja_kata_pengantar_id',
            'uraian_katapengantar:ntext',
            'refperiode_id',
            'refskpd_id',
            'halaman_renja',
        ],
    ]) ?>

</div>
