<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\SakipKegiatan $model */

$this->title = 'Update Sakip Kegiatan: ' . $model->refkegiatan_id;
$this->params['breadcrumbs'][] = ['label' => 'Sakip Kegiatans', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->refkegiatan_id, 'url' => ['view', 'refkegiatan_id' => $model->refkegiatan_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="sakip-kegiatan-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
