<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\SakipMisi $model */

$this->title = 'Update Sakip Misi: ' . $model->refmisi_id;
$this->params['breadcrumbs'][] = ['label' => 'Sakip Misis', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->refmisi_id, 'url' => ['view', 'refmisi_id' => $model->refmisi_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="sakip-misi-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
