<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\SakipSubkegiatan $model */

$this->title = 'Create Sakip Subkegiatan';
$this->params['breadcrumbs'][] = ['label' => 'Sakip Subkegiatans', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sakip-subkegiatan-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
