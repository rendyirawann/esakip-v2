<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var frontend\models\SakipLke $model */

$this->title = 'Create Sakip Lke';
$this->params['breadcrumbs'][] = ['label' => 'Sakip Lkes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sakip-lke-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
