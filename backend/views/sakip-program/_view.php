<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\TblSampah */

$this->title = 'Detail Data Program';
$this->params['breadcrumbs'][] = ['label' => 'Detail Data Program', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sakip-program-view">

    <h1><?= Html::encode($this->title) ?></h1>

<?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'refprogram_id',
            'kode_program',
            [
                'attribute' => 'nama_program',
                'format' => 'ntext', // Menampilkan teks dalam format paragraf
                'contentOptions' => [
                    'style' => 'white-space: pre-wrap;', // Mengizinkan word wrap
                ],
            ],      
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
                'attribute' => 'program_isaktif',
                'format' => 'raw', // Mengizinkan HTML rendering
                'value' => function ($model) {
                    if ($model->program_isaktif === 'T') {
                        return Html::tag('span', 'Aktif', ['class' => 'btn btn-success']);
                    } else if ($model->program_isaktif === 'F') {
                        return Html::tag('span', 'Tidak Aktif', ['class' => 'btn btn-warning']);
                    }
                },
            ],
        ],
    ]) ?>


</div>
