<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var frontend\models\SakipIndikatorcascadingsubkegiatan $model */

$this->title = 'Create Sakip Indikatorcascadingsubkegiatan';
$this->params['breadcrumbs'][] = ['label' => 'Sakip Indikatorcascadingsubkegiatans', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sakip-indikatorcascadingsubkegiatan-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
