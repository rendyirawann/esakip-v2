<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var frontend\models\SimonaKeluaranmediacascadingkegiatan $model */

$this->title = 'Create Simona Keluaranmediacascadingkegiatan';
$this->params['breadcrumbs'][] = ['label' => 'Simona Keluaranmediacascadingkegiatans', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="simona-keluaranmediacascadingkegiatan-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
