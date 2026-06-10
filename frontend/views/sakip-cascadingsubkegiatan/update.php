<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var frontend\models\SakipCascadingsubkegiatan $model */

$this->title = 'Update Sakip Cascadingsubkegiatan: ' . $model->refcascadingsubkegiatan_id;
$this->params['breadcrumbs'][] = ['label' => 'Sakip Cascadingsubkegiatans', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->refcascadingsubkegiatan_id, 'url' => ['view', 'refcascadingsubkegiatan_id' => $model->refcascadingsubkegiatan_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="sakip-cascadingsubkegiatan-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
