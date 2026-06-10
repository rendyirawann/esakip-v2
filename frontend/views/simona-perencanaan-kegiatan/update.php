<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var frontend\models\SakipCascadingprogram $model */

$this->title = 'Update Sakip Cascadingprogram: ' . $model->refcascadingprogram_id;
$this->params['breadcrumbs'][] = ['label' => 'Sakip Cascadingprograms', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->refcascadingprogram_id, 'url' => ['view', 'refcascadingprogram_id' => $model->refcascadingprogram_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="sakip-cascadingprogram-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_formupdate', [
        'model' => $model,
    ]) ?>

</div>