<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var frontend\models\SakipIndikatorsasaranrenstraTriwulan $model */

$this->title = 'Create Sakip Indikatorsasaranrenstra Triwulan';
$this->params['breadcrumbs'][] = ['label' => 'Sakip Indikatorsasaranrenstra Triwulans', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sakip-indikatorsasaranrenstra-triwulan-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
