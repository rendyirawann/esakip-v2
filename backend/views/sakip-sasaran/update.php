<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\SakipSasaran $model */

$this->title = 'Update Sakip Sasaran: ' . $model->refsasaran_id;
$this->params['breadcrumbs'][] = ['label' => 'Sakip Sasarans', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->refsasaran_id, 'url' => ['view', 'refsasaran_id' => $model->refsasaran_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="sakip-sasaran-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
