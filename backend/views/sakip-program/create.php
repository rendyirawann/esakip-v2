<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\SakipProgram $model */

$this->title = 'Create Sakip Program';
$this->params['breadcrumbs'][] = ['label' => 'Sakip Programs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sakip-program-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
