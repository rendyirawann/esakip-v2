<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var frontend\models\SimonaCascadingkegiatan $model */

$this->title = 'Create Simona Cascadingkegiatan';
$this->params['breadcrumbs'][] = ['label' => 'Simona Cascadingkegiatans', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="simona-cascadingkegiatan-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
