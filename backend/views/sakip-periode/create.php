<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\SakipPeriode $model */

$this->title = 'Create Sakip Periode';
$this->params['breadcrumbs'][] = ['label' => 'Sakip Periodes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sakip-periode-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
