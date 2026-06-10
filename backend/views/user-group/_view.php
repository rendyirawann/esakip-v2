<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\TblSampah */

$this->title = 'Detail Data User Group';
$this->params['breadcrumbs'][] = ['label' => 'Detail Data User Group', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-group-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
    'model' => $model,
    'attributes' => [
        'kode_group',
        'nama_group',
    ],
]) ?>




</div>
