<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var backend\models\SakipKoordinasi $model */

$this->title = $model->refkoordinasi_id;
$this->params['breadcrumbs'][] = ['label' => 'Sakip Koordinasis', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="sakip-koordinasi-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'refkoordinasi_id' => $model->refkoordinasi_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'refkoordinasi_id' => $model->refkoordinasi_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'refkoordinasi_id',
            'refuser_id',
            'refskpd_id',
        ],
    ]) ?>

</div>
