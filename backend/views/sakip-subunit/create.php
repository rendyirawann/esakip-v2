<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\SakipSubunit $model */

$this->title = 'Create Sakip Subunit';
$this->params['breadcrumbs'][] = ['label' => 'Sakip Subunits', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sakip-subunit-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
