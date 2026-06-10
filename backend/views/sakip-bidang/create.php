<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\SakipBidang $model */

$this->title = 'Create Sakip Bidang';
$this->params['breadcrumbs'][] = ['label' => 'Sakip Bidangs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sakip-bidang-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
