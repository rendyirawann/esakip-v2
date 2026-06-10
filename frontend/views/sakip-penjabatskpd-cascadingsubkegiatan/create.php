<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var frontend\models\SakipPenjabatskpdCascadingsubkegiatan $model */

$this->title = 'Create Sakip Penjabatskpd Cascadingsubkegiatan';
$this->params['breadcrumbs'][] = ['label' => 'Sakip Penjabatskpd Cascadingsubkegiatans', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sakip-penjabatskpd-cascadingsubkegiatan-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
