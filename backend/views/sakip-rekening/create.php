<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\SakipRekening $model */

$this->title = 'Create Sakip Rekening';
$this->params['breadcrumbs'][] = ['label' => 'Sakip Rekenings', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sakip-rekening-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
