<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\SakipLke $model */

$this->title = 'Update Sakip Lke: ' . $model->reflke_id;
$this->params['breadcrumbs'][] = ['label' => 'Sakip Lkes', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->reflke_id, 'url' => ['view', 'reflke_id' => $model->reflke_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="sakip-lke-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
