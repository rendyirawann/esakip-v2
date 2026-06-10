<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\SakipSubkegiatan $model */

$this->title = 'Update Sakip Subkegiatan: ' . $model->refsubkegiatan_id;
$this->params['breadcrumbs'][] = ['label' => 'Sakip Subkegiatans', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->refsubkegiatan_id, 'url' => ['view', 'refsubkegiatan_id' => $model->refsubkegiatan_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="sakip-subkegiatan-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
