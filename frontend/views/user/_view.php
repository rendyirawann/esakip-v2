<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\TblSampah */

$this->title = 'Detail Data User';
$this->params['breadcrumbs'][] = ['label' => 'Detail Data User', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$this->registerJs("
    function togglePasswordVisibility(inputId, button) {
        var input = document.getElementById(inputId);
        if (input.type === 'password') {
            input.type = 'text';
            button.textContent = 'Hide';
        } else {
            input.type = 'password';
            button.textContent = 'Show';
        }
    }
", \yii\web\View::POS_END);

?>
<div class="user-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'username',
            'auth_key',
            'password_hash',
            [
                'label' => 'Password Asli',
                'format' => 'raw',
                'value' => function ($model) {
                    return Html::textInput('real_password', $model->password_real, [
                        'id' => 'real-password-' . $model->id,
                        'readonly' => true,
                        'type' => 'password',
                        'class' => 'form-control d-inline-block',
                        'style' => 'width:200px; margin-right:10px; color:black; background-color:#f0f0f0;'
                    ]) .
                        Html::button('Show', [
                            'class' => 'btn btn-sm btn-warning',
                            'onclick' => "togglePasswordVisibility('real-password-{$model->id}', this)"
                        ]);
                }
            ],


            'password_reset_token',
            'email:email',
            'nama_user:ntext',
            [
                'attribute' => 'refskpd_id',
                'label' => 'SKPD User',
                'value' => function ($model) {
                    return $model->skpd ? $model->skpd->nama_skpd : 'Tidak ada SKPD';
                },
            ],
            [
                'attribute' => 'kode_group',
                'label' => 'Group User',
                'value' => function ($model) {
                    return $model->group ? $model->group->nama_group : 'Tidak ada Group User';
                },
            ],
            [
                'attribute' => 'status',
                'format' => 'raw', // Mengizinkan HTML rendering
                'value' => function ($model) {
                    if ($model->status === 10) {
                        return Html::tag('span', 'Aktif', ['class' => 'btn btn-success']);
                    } else if ($model->status === 9) {
                        return Html::tag('span', 'Tidak Aktif', ['class' => 'btn btn-warning']);
                    } else if ($model->status === 0) {
                        return Html::tag('span', 'Deleted', ['class' => 'btn btn-warning']);
                    }
                },
            ],
            'created_at',
            'updated_at',
            'user_lastlogin',
            'user_lastloginip',
            [
                'attribute' => 'user_isonline',
                'format' => 'raw', // Mengizinkan HTML rendering
                'value' => function ($model) {
                    if ($model->user_isonline === 'T') {
                        return Html::tag('span', 'Online', ['class' => 'btn btn-success']);
                    } else if ($model->user_isonline === 'F') {
                        return Html::tag('span', 'Offline', ['class' => 'btn btn-warning']);
                    }
                },
            ],
        ],
    ]) ?>


</div>