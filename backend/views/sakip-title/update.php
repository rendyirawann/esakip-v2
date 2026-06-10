<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\SakipTitle $model */

$this->title = 'Update Sakip Title: ' . $model->reftitle_id;
$this->params['breadcrumbs'][] = ['label' => 'Sakip Titles', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->reftitle_id, 'url' => ['view', 'reftitle_id' => $model->reftitle_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="sakip-title-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
