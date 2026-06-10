<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\SakipSkpd $model */

$this->title = 'Update Sakip Skpd: ' . $model->refskpd_id;
$this->params['breadcrumbs'][] = ['label' => 'Sakip Skpds', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->refskpd_id, 'url' => ['view', 'refskpd_id' => $model->refskpd_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="sakip-skpd-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
