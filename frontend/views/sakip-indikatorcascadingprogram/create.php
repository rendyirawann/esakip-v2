<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var frontend\models\SakipIndikatorcascadingprogram $model */

$this->title = 'Create Sakip Indikatorcascadingprogram';
$this->params['breadcrumbs'][] = ['label' => 'Sakip Indikatorcascadingprograms', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sakip-indikatorcascadingprogram-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
