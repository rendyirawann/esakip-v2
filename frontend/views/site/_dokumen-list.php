<?php

/** @var \yii\web\View $this */
/** @var string $content */

use common\widgets\Alert;
use frontend\assets\MainPortalAsset;
use yii\bootstrap5\Breadcrumbs;
use yii\bootstrap5\Html;
use yii\helpers\Url;
use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;

MainPortalAsset::register($this);

$this->title = 'My Yii Application';
?>

<?php if (!empty($dokumenList)): ?>
    <table class="table table-striped table-bordered mt-4">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama File</th>
                <th>Download</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($dokumenList as $index => $dokumen): ?>
                <tr>
                    <td><?= $index + 1 ?></td>
                    <td><?= Html::encode($dokumen->nama_file) ?></td>
                    <td>
                        <?= Html::a('Download', ['simona-keluaranmediacascadingkegiatan/download', 'refsimonakeluaranmediacascadingkegiatan_id' => $dokumen->refsimonakeluaranmediacascadingkegiatan_id], ['class' => 'btn btn-success', 'target' => '_blank']) ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <div class="alert alert-warning mt-4">
        Tidak ada dokumen yang ditemukan untuk kata kunci "<?= Html::encode($searchKeyword) ?>" .
    </div>
<?php endif; ?>