<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var frontend\models\SimonaCascadingsubkegiatan $model */

$this->title = 'Create Simona Cascadingsubkegiatan';
$this->params['breadcrumbs'][] = ['label' => 'Simona Cascadingsubkegiatans', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="simona-cascadingsubkegiatan-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
