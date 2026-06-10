<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\SakipUrusan $model */

$this->title = 'Create Sakip Urusan';
$this->params['breadcrumbs'][] = ['label' => 'Sakip Urusans', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sakip-urusan-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
