<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\SakipKoordinasi $model */

$this->title = 'Update Sakip Koordinasi: ' . $model->refkoordinasi_id;
$this->params['breadcrumbs'][] = ['label' => 'Sakip Koordinasis', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->refkoordinasi_id, 'url' => ['view', 'refkoordinasi_id' => $model->refkoordinasi_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="sakip-koordinasi-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
