<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\SakipKegiatan $model */

$this->title = 'Create Sakip Kegiatan';
$this->params['breadcrumbs'][] = ['label' => 'Sakip Kegiatans', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sakip-kegiatan-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
