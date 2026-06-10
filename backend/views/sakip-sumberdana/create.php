<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\SakipSumberdana $model */

$this->title = 'Create Sakip Sumberdana';
$this->params['breadcrumbs'][] = ['label' => 'Sakip Sumberdanas', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sakip-sumberdana-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
