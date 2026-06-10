<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\SakipLkesubkriteria $model */

$this->title = 'Create Sakip Lkesubkriteria';
$this->params['breadcrumbs'][] = ['label' => 'Sakip Lkesubkriterias', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sakip-lkesubkriteria-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
