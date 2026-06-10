<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var frontend\models\SakipCascadingsubkegiatan $model */

$this->title = 'Create Sakip Cascadingsubkegiatan';
$this->params['breadcrumbs'][] = ['label' => 'Sakip Cascadingsubkegiatans', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sakip-cascadingsubkegiatan-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
