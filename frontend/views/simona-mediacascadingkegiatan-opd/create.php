<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var frontend\models\SimonaMediacascadingkegiatanOpd $model */

$this->title = 'Create Simona Mediacascadingkegiatan Opd';
$this->params['breadcrumbs'][] = ['label' => 'Simona Mediacascadingkegiatan Opds', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="simona-mediacascadingkegiatan-opd-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
