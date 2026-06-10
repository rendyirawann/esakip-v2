<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var frontend\models\SakipSasaranrenstra $model */

$this->title = 'Create Sakip Sasaranrenstra';
$this->params['breadcrumbs'][] = ['label' => 'Sakip Sasaranrenstras', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sakip-sasaranrenstra-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
