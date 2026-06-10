<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var frontend\models\SakipIndikatorcascadingsubkegiatanTriwulan $model */

$this->title = 'Update Sakip Indikatorcascadingsubkegiatan Triwulan: ' . $model->refindikatorsubkegiatantriwulan_id;
$this->params['breadcrumbs'][] = ['label' => 'Sakip Indikatorcascadingsubkegiatan Triwulans', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->refindikatorsubkegiatantriwulan_id, 'url' => ['view', 'refindikatorsubkegiatantriwulan_id' => $model->refindikatorsubkegiatantriwulan_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="sakip-indikatorcascadingsubkegiatan-triwulan-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_formupdatetriwulanpk', [
        'model' => $model,
    ]) ?>

</div>