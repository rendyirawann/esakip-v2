<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\TblSampah */

$this->title = 'Detail Data SKPD';
$this->params['breadcrumbs'][] = ['label' => 'Detail Data SKPD', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sakip-skpd-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'refskpd_id',
            'kode_skpd',
            [
                'attribute' => 'nama_skpd',
                'format' => 'ntext', // Menampilkan teks dalam format paragraf
                'contentOptions' => [
                    'style' => 'white-space: pre-wrap;', // Mengizinkan word wrap
                ],
            ],
            'kepala_skpd:ntext',
            'nip_kepala',
            'jabatan_kepala',
            'pangkat_kepala',
            [
                'attribute' => 'refurusan_id',
                'label' => 'Nama Urusan',
                'value' => function ($model) {
                    return $model->urusan ? $model->urusan->nama_urusan : 'Tidak ada urusan';
                },
            ],
            [
                'attribute' => 'refbidang_id',
                'label' => 'Nama Bidang',
                'value' => function ($model) {
                    return $model->bidang ? $model->bidang->nama_bidang : 'Tidak ada Bidang';
                },
            ],
            [
                'attribute' => 'refskpd_unit',
                'format' => 'raw', // Mengizinkan HTML rendering
                'value' => function ($model) {
                    if ($model->refskpd_unit === 'I') {
                        return Html::tag('span', 'Instansi', ['class' => 'btn btn-success']);
                    } else if ($model->refskpd_unit === 'U') {
                        return Html::tag('span', 'Utama', ['class' => 'btn btn-warning']);
                    } else if ($model->refskpd_unit === 'P') {
                        return Html::tag('span', 'Pendukung', ['class' => 'btn btn-warning']);
                    } else if ($model->refskpd_unit === 'T') {
                        return Html::tag('span', 'Tambahan', ['class' => 'btn btn-warning']);
                    }
                },
            ],
            'refskpd_keterangan',
            [
                'attribute' => 'skpd_isaktif',
                'format' => 'raw', // Mengizinkan HTML rendering
                'value' => function ($model) {
                    if ($model->skpd_isaktif === 'T') {
                        return Html::tag('span', 'Aktif', ['class' => 'btn btn-success']);
                    } else if ($model->skpd_isaktif === 'F') {
                        return Html::tag('span', 'Tidak Aktif', ['class' => 'btn btn-warning']);
                    }
                },
            ],
        ],
    ]) ?>

</div>