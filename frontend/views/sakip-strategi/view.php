<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var frontend\models\SakipStrategi $model */

$this->title = $model->refstrategi_id;
$this->params['breadcrumbs'][] = ['label' => 'Sakip Strategis', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="sakip-strategi-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'refstrategi_id' => $model->refstrategi_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'refstrategi_id' => $model->refstrategi_id], [
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
            'refstrategi_id',
            'uraian_strategi:ntext',
            'refskpd_id',
            'refmisi_id',
            'refsasaranrenstra_id',
            'refsasaran_id',
            'reftujuan_id',
            'refperiode_id',
            'user_create',
            'date_create',
            'user_edit',
            'date_edit',
            'user_delete',
            'date_delete',
        ],
    ]) ?>

</div>
