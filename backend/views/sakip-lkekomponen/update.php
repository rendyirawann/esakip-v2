<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\SakipLkekomponen $model */

$this->title = 'Update Sakip Lkekomponen: ' . $model->reflkekomponen_id;
$this->params['breadcrumbs'][] = ['label' => 'Sakip Lkekomponens', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->reflkekomponen_id, 'url' => ['view', 'reflkekomponen_id' => $model->reflkekomponen_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="sakip-lkekomponen-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
