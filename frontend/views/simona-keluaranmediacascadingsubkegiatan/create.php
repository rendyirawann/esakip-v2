<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var frontend\models\SimonaKeluaranmediacascadingsubkegiatan $model */

$this->title = 'Create Simona Keluaranmediacascadingsubkegiatan';
$this->params['breadcrumbs'][] = ['label' => 'Simona Keluaranmediacascadingsubkegiatans', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="simona-keluaranmediacascadingsubkegiatan-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
