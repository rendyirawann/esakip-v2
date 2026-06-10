<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\UserGroup $model */

$this->title = 'Update User Group: ' . $model->kode_group;
$this->params['breadcrumbs'][] = ['label' => 'User Groups', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->kode_group, 'url' => ['view', 'kode_group' => $model->kode_group]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="user-group-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
