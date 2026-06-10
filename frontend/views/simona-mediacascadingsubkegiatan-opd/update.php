<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var frontend\models\SimonaMediacascadingsubkegiatanOpd $model */

$this->title = 'Update Simona Mediacascadingsubkegiatan Opd: ' . $model->refsimonamediacascadingsubkegiatanopd_id;
$this->params['breadcrumbs'][] = ['label' => 'Simona Mediacascadingsubkegiatan Opds', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->refsimonamediacascadingsubkegiatanopd_id, 'url' => ['view', 'refsimonamediacascadingsubkegiatanopd_id' => $model->refsimonamediacascadingsubkegiatanopd_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="simona-mediacascadingsubkegiatan-opd-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
