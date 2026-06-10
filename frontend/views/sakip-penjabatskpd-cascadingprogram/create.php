<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var frontend\models\SakipPenjabatskpdCascadingprogram $model */

$this->title = 'Create Sakip Penjabatskpd Cascadingprogram';
$this->params['breadcrumbs'][] = ['label' => 'Sakip Penjabatskpd Cascadingprograms', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sakip-penjabatskpd-cascadingprogram-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
