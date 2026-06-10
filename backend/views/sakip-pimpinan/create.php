<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\SakipPimpinan $model */

$this->title = 'Create Sakip Pimpinan';
$this->params['breadcrumbs'][] = ['label' => 'Sakip Pimpinans', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sakip-pimpinan-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
