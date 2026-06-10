<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var frontend\models\SakipKebijakan $model */

$this->title = 'Update Sakip Kebijakan: ' . $model->refkebijakan_id;
$this->params['breadcrumbs'][] = ['label' => 'Sakip Kebijakans', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->refkebijakan_id, 'url' => ['view', 'refkebijakan_id' => $model->refkebijakan_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="sakip-kebijakan-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_formupdate', [
        'model' => $model,
    ]) ?>

</div>