<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var frontend\models\SimonaRincianbelanjacascadingkegiatan $model */

$this->title = 'Create Simona Rincianbelanjacascadingkegiatan';
$this->params['breadcrumbs'][] = ['label' => 'Simona Rincianbelanjacascadingkegiatans', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="simona-rincianbelanjacascadingkegiatan-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
