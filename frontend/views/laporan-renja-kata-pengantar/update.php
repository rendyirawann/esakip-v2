<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var frontend\models\LaporanRenjaKataPengantar $model */

$this->title = 'Update Laporan Renja Kata Pengantar: ' . $model->laporan_renja_kata_pengantar_id;
$this->params['breadcrumbs'][] = ['label' => 'Laporan Renja Kata Pengantars', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->laporan_renja_kata_pengantar_id, 'url' => ['view', 'laporan_renja_kata_pengantar_id' => $model->laporan_renja_kata_pengantar_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="laporan-renja-kata-pengantar-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
