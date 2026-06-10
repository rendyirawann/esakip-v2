<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\SakipLkesubkriteria $model */

$this->title = 'Update Sakip Lkesubkriteria: ' . $model->reflkesubkriteria_id;
$this->params['breadcrumbs'][] = ['label' => 'Sakip Lkesubkriterias', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->reflkesubkriteria_id, 'url' => ['view', 'reflkesubkriteria_id' => $model->reflkesubkriteria_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="sakip-lkesubkriteria-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
