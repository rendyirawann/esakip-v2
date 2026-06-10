<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var frontend\models\SakipIndikatorcascadingprogram $model */

$this->title = 'Update Sakip Indikatorcascadingprogram: ' . $model->refindikatorprogram_id;
$this->params['breadcrumbs'][] = ['label' => 'Sakip Indikatorcascadingprograms', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->refindikatorprogram_id, 'url' => ['view', 'refindikatorprogram_id' => $model->refindikatorprogram_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="sakip-indikatorcascadingprogram-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_formupdatetargetpk', [
        'model' => $model,
    ]) ?>

</div>