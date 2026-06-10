<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\SakipMisi $model */

$this->title = 'Create Sakip Misi';
$this->params['breadcrumbs'][] = ['label' => 'Sakip Misis', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sakip-misi-p-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>