<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\SakipEselon $model */

$this->title = 'Update Sakip Eselon: ' . $model->refeselon_id;
$this->params['breadcrumbs'][] = ['label' => 'Sakip Eselons', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->refeselon_id, 'url' => ['view', 'refeselon_id' => $model->refeselon_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="sakip-eselon-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
