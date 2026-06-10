<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\SakipSumberdana $model */

$this->title = 'Update Sakip Sumberdana: ' . $model->refsumberdana_id;
$this->params['breadcrumbs'][] = ['label' => 'Sakip Sumberdanas', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->refsumberdana_id, 'url' => ['view', 'refsumberdana_id' => $model->refsumberdana_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="sakip-sumberdana-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
