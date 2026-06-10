<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var frontend\models\SakipStrategi $model */

$this->title = 'Create Sakip Strategi';
$this->params['breadcrumbs'][] = ['label' => 'Sakip Strategis', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sakip-strategi-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
