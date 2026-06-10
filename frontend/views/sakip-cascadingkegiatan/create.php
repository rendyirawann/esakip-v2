<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var frontend\models\SakipCascadingkegiatan $model */

$this->title = 'Create Sakip Cascadingkegiatan';
$this->params['breadcrumbs'][] = ['label' => 'Sakip Cascadingkegiatans', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sakip-cascadingkegiatan-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
