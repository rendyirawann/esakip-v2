<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var frontend\models\SakipPenjabatskpdCascadingkegiatan $model */

$this->title = 'Create Sakip Penjabatskpd Cascadingkegiatan';
$this->params['breadcrumbs'][] = ['label' => 'Sakip Penjabatskpd Cascadingkegiatans', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sakip-penjabatskpd-cascadingkegiatan-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
