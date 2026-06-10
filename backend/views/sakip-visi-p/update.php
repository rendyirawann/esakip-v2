<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\SakipVisi $model */

$this->title = 'Update Sakip Visi: ' . $model->refvisi_id;
$this->params['breadcrumbs'][] = ['label' => 'Sakip Visis', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->refvisi_id, 'url' => ['view', 'refvisi_id' => $model->refvisi_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="sakip-visi-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
