<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var frontend\models\SimonaMediacascadingsubkegiatan $model */

$this->title = 'Update Simona Mediacascadingsubkegiatan: ' . $model->refsimonamediacascadingsubkegiatan_id;
$this->params['breadcrumbs'][] = ['label' => 'Simona Mediacascadingsubkegiatans', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->refsimonamediacascadingsubkegiatan_id, 'url' => ['view', 'refsimonamediacascadingsubkegiatan_id' => $model->refsimonamediacascadingsubkegiatan_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="simona-mediacascadingsubkegiatan-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
