<?php

use frontend\models\SakipIndikatorSasaranRenstra;
use frontend\models\SakipIndikatorSasaranRenstraTriwulan;
use frontend\models\SakipSasaranRenstra;
use yii\helpers\Html;
// Ambil semua indikator dari SKPD dan periode yang belum terisi di sakip_indikatorsasaranrenstra_triwulan
$indikatorQuery = SakipIndikatorSasaranRenstra::find()
    ->alias('i')
    ->joinWith(['refSasaranrenstra s'])
    ->where([
        'i.refskpd_id' => $refskpd_id,
        'i.refperiode_id' => $refperiode_id,
    ])
    ->andWhere([
        'not in',
        'i.refindikatorsasaranrenstra_id',
        SakipIndikatorSasaranRenstraTriwulan::find()
            ->select('refindikatorsasaranrenstra_id')
            ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
    ])
    ->all();
?>

<?php if (!empty($indikatorQuery)): ?>
    <table class="table table-bordered table-hover">
        <thead class="table-secondary">
            <tr>
                <th>No</th>
                <th>Uraian Sasaran Renstra</th>
                <th>Uraian Indikator</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($indikatorQuery as $i => $indikator): ?>
                <tr>
                    <td><?= $i + 1 ?></td>
                    <td><?= Html::encode($indikator->refSasaranrenstra->uraian_sasaranrenstra ?? '-') ?></td>
                    <td><?= Html::encode($indikator->uraian_indikatorsasaranrenstra) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <div class="alert alert-success">Semua indikator sudah terisi realisasi triwulan.</div>
<?php endif; ?>