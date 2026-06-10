<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\SakipTujuan $model */

$this->title = 'Create Sakip Tujuan';
$this->params['breadcrumbs'][] = ['label' => 'Sakip Tujuans', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sakip-tujuan-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
