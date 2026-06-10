<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var frontend\models\SakipIndikatorcascadingkegiatan $model */

$this->title = 'Create Sakip Indikatorcascadingkegiatan';
$this->params['breadcrumbs'][] = ['label' => 'Sakip Indikatorcascadingkegiatans', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sakip-indikatorcascadingkegiatan-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
