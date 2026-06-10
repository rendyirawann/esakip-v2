<?php

use backend\models\SakipBidang;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var backend\models\search\SakipBidangSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Data SAKIP Bidang';
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="pc-container">
    <div class="pc-content">
        <!-- [ breadcrumb ] start -->
        <div class="page-header">
            <div class="page-block">
                <div class="row align-items-center">
                    <div class="col-md-12">
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?= Url::to(['/site/index']) ?>">Home</a></li>
                            <li class="breadcrumb-item" aria-current="page">Data SAKIP Bidang</li>
                        </ul>
                    </div>
                    <div class="col-md-12">
                        <div class="page-header-title">
                            <h2 class="mb-0">Data SAKIP Bidang</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- [ breadcrumb ] end -->


        <!-- [ Main Content ] start -->
        <div class="row">
            <div class="col">
                <h3>Data Bidang</h3>

                <?php foreach ($models as $model): ?>
                    <table>
                        <td><?= Html::encode($model->kode_program) ?></td>
                        <td><?= Html::encode($model->refprogram_id) ?></td>
                    </table>
                <?php endforeach; ?>
            </div>
        </div>
        <!-- [ Main Content ] end -->
    </div>
</div>