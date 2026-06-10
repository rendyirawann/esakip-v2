<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\SakipLkesubkomponen $model */

$this->title = 'Create Sakip Lkesubkomponen';
$this->params['breadcrumbs'][] = ['label' => 'Sakip Lkesubkomponens', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sakip-lkesubkomponen-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
