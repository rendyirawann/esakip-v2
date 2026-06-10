<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\SakipTitle */

$this->title = 'Detail Data Title';
$this->params['breadcrumbs'][] = ['label' => 'Detail Data Title', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sakip-title-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'reftitle_id',
            'nama_title:ntext',
        ],
    ]) ?>




</div>