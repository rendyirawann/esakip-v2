<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\SakipPeriode5tahun $model */

$this->title = 'Create Sakip Periode 5 Tahun';
$this->params['breadcrumbs'][] = ['label' => 'Sakip Periode 5 Tahuns', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sakip-periode-5tahun-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
