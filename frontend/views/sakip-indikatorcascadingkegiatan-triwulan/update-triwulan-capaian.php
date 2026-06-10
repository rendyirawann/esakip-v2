<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var frontend\models\SakipIndikatorcascadingkegiatanTriwulan $model */

$this->title = 'Update Sakip Indikatorcascadingkegiatan Triwulan: ' . $model->refindikatorkegiatantriwulan_id;
$this->params['breadcrumbs'][] = ['label' => 'Sakip Indikatorcascadingkegiatan Triwulans', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->refindikatorkegiatantriwulan_id, 'url' => ['view', 'refindikatorkegiatantriwulan_id' => $model->refindikatorkegiatantriwulan_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="sakip-indikatorcascadingkegiatan-triwulan-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_formupdatetriwulancapaian', [
        'model' => $model,
    ]) ?>

</div>