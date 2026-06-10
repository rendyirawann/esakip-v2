<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\SakipKoordinasi $model */

$this->title = 'Create Sakip Koordinasi';
$this->params['breadcrumbs'][] = ['label' => 'Sakip Koordinasis', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sakip-koordinasi-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
