<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\SakipEselon $model */

$this->title = 'Create Sakip Eselon';
$this->params['breadcrumbs'][] = ['label' => 'Sakip Eselons', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sakip-eselon-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
