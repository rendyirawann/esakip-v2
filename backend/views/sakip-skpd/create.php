<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\SakipSkpd $model */

$this->title = 'Create Sakip Skpd';
$this->params['breadcrumbs'][] = ['label' => 'Sakip Skpds', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sakip-skpd-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
