<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var frontend\models\SakipIndikatorsasaranrenstra $model */

$this->title = 'Create Sakip Indikatorsasaranrenstra';
$this->params['breadcrumbs'][] = ['label' => 'Sakip Indikatorsasaranrenstras', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sakip-indikatorsasaranrenstra-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
