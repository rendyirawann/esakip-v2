<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\SakipPenanggungjawab $model */

$this->title = 'Create Sakip Penanggungjawab';
$this->params['breadcrumbs'][] = ['label' => 'Sakip Penanggungjawabs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sakip-penanggungjawab-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
