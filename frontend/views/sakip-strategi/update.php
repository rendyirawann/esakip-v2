<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var frontend\models\SakipStrategi $model */

$this->title = 'Update Sakip Strategi: ' . $model->refstrategi_id;
$this->params['breadcrumbs'][] = ['label' => 'Sakip Strategis', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->refstrategi_id, 'url' => ['view', 'refstrategi_id' => $model->refstrategi_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="sakip-strategi-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_formupdate', [
        'model' => $model,
    ]) ?>

</div>