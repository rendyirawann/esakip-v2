<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var frontend\models\SakipKebijakan $model */

$this->title = 'Create Sakip Kebijakan';
$this->params['breadcrumbs'][] = ['label' => 'Sakip Kebijakans', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sakip-kebijakan-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
