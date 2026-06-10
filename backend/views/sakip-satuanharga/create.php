<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\SakipSatuanharga $model */

$this->title = 'Create Sakip Satuanharga';
$this->params['breadcrumbs'][] = ['label' => 'Sakip Satuanhargas', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sakip-satuanharga-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
