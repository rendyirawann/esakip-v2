<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var frontend\models\SakipIndikatorcascadingkegiatan $model */

$this->title = 'Update Sakip Indikatorcascadingkegiatan: ' . $model->refindikatorkegiatan_id;
$this->params['breadcrumbs'][] = ['label' => 'Sakip Indikatorcascadingkegiatans', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->refindikatorkegiatan_id, 'url' => ['view', 'refindikatorkegiatan_id' => $model->refindikatorkegiatan_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="sakip-indikatorcascadingkegiatan-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_formupdatetargetpkp', [
        'model' => $model,
    ]) ?>

</div>