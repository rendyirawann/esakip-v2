<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\SakipSubunit $model */

$this->title = 'Update Sakip Subunit: ' . $model->refsubunit_id;
$this->params['breadcrumbs'][] = ['label' => 'Sakip Subunits', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->refsubunit_id, 'url' => ['view', 'refsubunit_id' => $model->refsubunit_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="sakip-subunit-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
