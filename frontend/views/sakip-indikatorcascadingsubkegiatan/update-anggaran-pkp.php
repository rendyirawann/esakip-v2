<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var frontend\models\SakipIndikatorcascadingsubkegiatan $model */

$this->title = 'Update Sakip Indikatorcascadingsubkegiatan: ' . $model->refindikatorsubkegiatan_id;
$this->params['breadcrumbs'][] = ['label' => 'Sakip Indikatorcascadingsubkegiatans', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->refindikatorsubkegiatan_id, 'url' => ['view', 'refindikatorsubkegiatan_id' => $model->refindikatorsubkegiatan_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="sakip-indikatorcascadingsubkegiatan-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_formanggaranpkp', [
        'model' => $model,
    ]) ?>

</div>