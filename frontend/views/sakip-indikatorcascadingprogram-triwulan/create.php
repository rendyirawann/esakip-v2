<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var frontend\models\SakipIndikatorcascadingprogramTriwulan $model */

$this->title = 'Create Sakip Indikatorcascadingprogram Triwulan';
$this->params['breadcrumbs'][] = ['label' => 'Sakip Indikatorcascadingprogram Triwulans', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sakip-indikatorcascadingprogram-triwulan-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
