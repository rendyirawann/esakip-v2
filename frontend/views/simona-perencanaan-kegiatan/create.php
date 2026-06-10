<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var frontend\models\SakipCascadingprogram $model */

$this->title = 'Create Sakip Cascadingprogram';
$this->params['breadcrumbs'][] = ['label' => 'Sakip Cascadingprograms', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sakip-cascadingprogram-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>