<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var frontend\models\SakipKebijakan $model */

$this->title = $model->refkebijakan_id;
$this->params['breadcrumbs'][] = ['label' => 'Sakip Kebijakans', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="sakip-kebijakan-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'refkebijakan_id' => $model->refkebijakan_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'refkebijakan_id' => $model->refkebijakan_id], [
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
            'refkebijakan_id',
            'uraian_kebijakan:ntext',
            'refskpd_id',
            'refstrategi_id',
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
