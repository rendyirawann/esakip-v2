<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var frontend\models\SakipTujuanrenstra $model */

$this->title = 'Create Sakip Tujuanrenstra';
$this->params['breadcrumbs'][] = ['label' => 'Sakip Tujuanrenstras', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sakip-tujuanrenstra-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
