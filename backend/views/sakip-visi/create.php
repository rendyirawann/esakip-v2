<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\SakipVisi $model */

$this->title = 'Create Sakip Visi';
$this->params['breadcrumbs'][] = ['label' => 'Sakip Visis', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sakip-visi-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
