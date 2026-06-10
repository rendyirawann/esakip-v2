<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var frontend\models\SakipIndikatortujuanrenstra $model */

$this->title = 'Create Sakip Indikatortujuanrenstra';
$this->params['breadcrumbs'][] = ['label' => 'Sakip Indikatortujuanrenstras', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sakip-indikatortujuanrenstra-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
