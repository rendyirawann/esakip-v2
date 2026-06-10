<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var frontend\models\SakipSasaranrenstraP $model */

$this->title = 'Create Sakip Sasaranrenstra P';
$this->params['breadcrumbs'][] = ['label' => 'Sakip Sasaranrenstra Ps', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sakip-sasaranrenstra-p-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
