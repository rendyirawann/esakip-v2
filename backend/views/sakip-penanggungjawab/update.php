<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\SakipPenanggungjawab $model */

$this->title = 'Update Sakip Penanggungjawab: ' . $model->refpenanggungjawab_id;
$this->params['breadcrumbs'][] = ['label' => 'Sakip Penanggungjawabs', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->refpenanggungjawab_id, 'url' => ['view', 'refpenanggungjawab_id' => $model->refpenanggungjawab_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="sakip-penanggungjawab-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
