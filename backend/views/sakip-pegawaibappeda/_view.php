<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\TblSampah */

$this->title = 'Detail Data Pegawai Bappeda';
$this->params['breadcrumbs'][] = ['label' => 'Detail Data Pegawai Bappeda', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sakip-pegawaibappeda-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'refpegawai_id',
            [
                'attribute' => 'statusAparatur',
                'filter' => ['1' => 'ASN', '2' => 'Non ASN'],
                'label' => 'status',
                'value' => function ($model) {
                    return ($model->statusAparatur == 1) ? 'ASN' : 'Non ASN';
                }
            ],
            'nama_pegawai:ntext',
            [
                'attribute' => 'nip',
                'label' => 'NIP',
                'value' => function ($model) {
                    return $model->nip ? $model->nip : 'Tidak ada NIP';
                },
            ],
            [
                'attribute' => 'refeselon_id',
                'label' => 'Nama Eselon',
                'value' => function ($model) {
                    return $model->refEselon ? $model->refEselon->title_eselon : 'Tidak ada Data Eselon';
                },
            ],
            [
                'attribute' => 'reftitle_id',
                'label' => 'Nama Title',
                'value' => function ($model) {
                    return $model->refTitle ? $model->refTitle->nama_title : 'Tidak ada Data Title';
                },
            ],
            [
                'attribute' => 'refbidangbappeda_id',
                'label' => 'Nama Bidang Bappeda',
                'value' => function ($model) {
                    return $model->refBidangbappeda ? $model->refBidangbappeda->nama_bidangbappeda : 'Tidak ada Data Bidang Bappeda';
                },
            ],
            [
                'attribute' => 'no_hp',
                'label' => 'Nomor Handphone',
                'value' => function ($model) {
                    return $model->no_hp ? $model->no_hp : 'Tidak ada Nomor Handphone';
                },
            ],
        ],
    ]) ?>




</div>