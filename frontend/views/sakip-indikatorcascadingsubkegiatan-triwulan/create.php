<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var frontend\models\SakipIndikatorcascadingsubkegiatanTriwulan $model */

$this->title = 'Create Sakip Indikatorcascadingsubkegiatan Triwulan';
$this->params['breadcrumbs'][] = ['label' => 'Sakip Indikatorcascadingsubkegiatan Triwulans', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sakip-indikatorcascadingsubkegiatan-triwulan-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
