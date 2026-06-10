<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var frontend\models\SimonaMediacascadingkegiatan $model */

$this->title = 'Update Simona Mediacascadingkegiatan: ' . $model->refsimonamediacascadingkegiatan_id;
$this->params['breadcrumbs'][] = ['label' => 'Simona Mediacascadingkegiatans', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->refsimonamediacascadingkegiatan_id, 'url' => ['view', 'refsimonamediacascadingkegiatan_id' => $model->refsimonamediacascadingkegiatan_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="simona-mediacascadingkegiatan-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
