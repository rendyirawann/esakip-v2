<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\SakipTujuan $model */

$this->title = 'Update Sakip Tujuan: ' . $model->reftujuan_id;
$this->params['breadcrumbs'][] = ['label' => 'Sakip Tujuans', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->reftujuan_id, 'url' => ['view', 'reftujuan_id' => $model->reftujuan_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="sakip-tujuan-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
