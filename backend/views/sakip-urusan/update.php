<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\SakipUrusan $model */

$this->title = 'Update Sakip Urusan: ' . $model->urusan_id;
$this->params['breadcrumbs'][] = ['label' => 'Sakip Urusans', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->urusan_id, 'url' => ['view', 'urusan_id' => $model->urusan_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="sakip-urusan-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
