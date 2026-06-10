<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var frontend\models\SimonaMediacascadingsubkegiatan $model */

$this->title = 'Create Simona Mediacascadingsubkegiatan';
$this->params['breadcrumbs'][] = ['label' => 'Simona Mediacascadingsubkegiatans', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="simona-mediacascadingsubkegiatan-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
