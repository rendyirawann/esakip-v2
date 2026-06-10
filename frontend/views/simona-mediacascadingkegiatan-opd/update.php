<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var frontend\models\SimonaMediacascadingkegiatanOpd $model */

$this->title = 'Update Simona Mediacascadingkegiatan Opd: ' . $model->refsimonamediacascadingkegiatanopd_id;
$this->params['breadcrumbs'][] = ['label' => 'Simona Mediacascadingkegiatan Opds', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->refsimonamediacascadingkegiatanopd_id, 'url' => ['view', 'refsimonamediacascadingkegiatanopd_id' => $model->refsimonamediacascadingkegiatanopd_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="simona-mediacascadingkegiatan-opd-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
