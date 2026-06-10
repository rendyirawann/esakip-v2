<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\SakipPenjabatSkpd $model */

$this->title = 'Create Sakip Penjabat Skpd';
$this->params['breadcrumbs'][] = ['label' => 'Sakip Penjabat Skpds', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sakip-penjabat-skpd-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
