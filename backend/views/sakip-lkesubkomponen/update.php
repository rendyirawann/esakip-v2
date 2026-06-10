<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\SakipLkesubkomponen $model */

$this->title = 'Update Sakip Lkesubkomponen: ' . $model->reflkesubkomponen_id;
$this->params['breadcrumbs'][] = ['label' => 'Sakip Lkesubkomponens', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->reflkesubkomponen_id, 'url' => ['view', 'reflkesubkomponen_id' => $model->reflkesubkomponen_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="sakip-lkesubkomponen-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
