<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\SakipProgram $model */

$this->title = 'Update Sakip Program: ' . $model->refprogram_id;
$this->params['breadcrumbs'][] = ['label' => 'Sakip Programs', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->refprogram_id, 'url' => ['view', 'refprogram_id' => $model->refprogram_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="sakip-program-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
