<?php

use yii\helpers\Html;
use yii\widgets\DetailView;


$this->title = 'Detail Data Eselon';
$this->params['breadcrumbs'][] = ['label' => 'Detail Data Eselon', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sakip-eselon-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'refeselon_id',
            'title_eselon',
        ],
    ]) ?>




</div>