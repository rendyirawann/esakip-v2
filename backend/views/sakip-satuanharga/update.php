<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\SakipSatuanharga $model */

$this->title = 'Update Sakip Satuanharga: ' . $model->refsatuanharga_id;
$this->params['breadcrumbs'][] = ['label' => 'Sakip Satuanhargas', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->refsatuanharga_id, 'url' => ['view', 'refsatuanharga_id' => $model->refsatuanharga_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="sakip-satuanharga-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
