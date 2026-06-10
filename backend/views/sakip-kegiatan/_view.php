<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\TblSampah */

$this->title = 'Detail Data Kegiatan';
$this->params['breadcrumbs'][] = ['label' => 'Detail Data Kegiatan', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sakip-kegiatan-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'refkegiatan_id',
            'kode_kegiatan',
            [
                'attribute' => 'nama_kegiatan',
                'format' => 'ntext', // Menampilkan teks dalam format paragraf
                'contentOptions' => [
                    'style' => 'white-space: pre-wrap;', // Mengizinkan word wrap
                ],
            ],              [
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
                'attribute' => 'refprogram_id',
                'label' => 'Nama Program',
                'value' => function ($model) {
                    return $model->program ? $model->program->nama_program : 'Tidak ada Program';
                },
            ],
            [
                'attribute' => 'kegiatan_isaktif',
                'format' => 'raw', // Mengizinkan HTML rendering
                'value' => function ($model) {
                    if ($model->kegiatan_isaktif === 'T') {
                        return Html::tag('span', 'Aktif', ['class' => 'btn btn-success']);
                    } else if ($model->kegiatan_isaktif === 'F') {
                        return Html::tag('span', 'Tidak Aktif', ['class' => 'btn btn-warning']);
                    }
                },
            ],
        ],
    ]) ?>


</div>
