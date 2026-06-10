<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\SakipTitle */

$this->title = 'Detail Data Bidang Bappeda';
$this->params['breadcrumbs'][] = ['label' => 'Detail Data Bidang Bappeda', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sakip-bidangbappeda-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'refbidangbappeda_id',
            'nama_bidangbappeda:ntext',
        ],
    ]) ?>




</div>