<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\SakipPimpinan $model */

$this->title = 'Update Sakip Pimpinan: ' . $model->refpimpinan_id;
$this->params['breadcrumbs'][] = ['label' => 'Sakip Pimpinans', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->refpimpinan_id, 'url' => ['view', 'refpimpinan_id' => $model->refpimpinan_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="sakip-pimpinan-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
