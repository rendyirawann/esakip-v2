<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\SakipLkekomponen $model */

$this->title = 'Create Sakip Lkekomponen';
$this->params['breadcrumbs'][] = ['label' => 'Sakip Lkekomponens', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sakip-lkekomponen-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
