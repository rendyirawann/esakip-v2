<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var frontend\models\SakipCascadingkegiatan $model */

$this->title = 'Update Sakip Cascadingkegiatan: ' . $model->refcascadingkegiatan_id;
$this->params['breadcrumbs'][] = ['label' => 'Sakip Cascadingkegiatans', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->refcascadingkegiatan_id, 'url' => ['view', 'refcascadingkegiatan_id' => $model->refcascadingkegiatan_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="sakip-cascadingkegiatan-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_formupdate', [
        'model' => $model,
    ]) ?>

</div>