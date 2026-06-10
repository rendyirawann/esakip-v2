<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var backend\models\SakipPenjabatSkpd $model */

$this->title = $model->refpenjabatskpd_id;
$this->params['breadcrumbs'][] = ['label' => 'Sakip Penjabat Skpds', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="sakip-penjabat-skpd-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'refpenjabatskpd_id' => $model->refpenjabatskpd_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'refpenjabatskpd_id' => $model->refpenjabatskpd_id], [
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
            'refpenjabatskpd_id',
            'refskpd_id',
            'refperiode_id',
            'nama_penjabat:ntext',
            'nip_penjabat',
            'jabatan_eselon:ntext',
            'pangkat_eselon',
        ],
    ]) ?>

</div>
