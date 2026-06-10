<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var frontend\models\SakipIndikatorcascadingprogramTriwulan $model */

$this->title = 'Update Sakip Indikatorcascadingprogram Triwulan: ' . $model->refindikatorprogramtriwulan_id;
$this->params['breadcrumbs'][] = ['label' => 'Sakip Indikatorcascadingprogram Triwulans', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->refindikatorprogramtriwulan_id, 'url' => ['view', 'refindikatorprogramtriwulan_id' => $model->refindikatorprogramtriwulan_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="sakip-indikatorcascadingprogram-triwulan-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_formupdatetriwulanpkp', [
        'model' => $model,
    ]) ?>

</div>