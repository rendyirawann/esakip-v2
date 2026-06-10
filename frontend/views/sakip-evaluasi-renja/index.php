<?php
use yii\helpers\Html;
use yii\helpers\Url;
use frontend\models\SakipEvaluasiRenja;

/** @var yii\web\View $this */
/** @var SakipEvaluasiRenja[] $data */
/** @var string $year */
/** @var string $skpdName */
/** @var int $refskpdId */
/** @var array $importHistory */
/** @var bool $isSuperAdmin */
/** @var array $skpdList */
/** @var int $selectedSkpdId */

$this->title = 'Evaluasi Renja';
$this->registerCssFile("https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css");
$this->registerJsFile("https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js", ['depends' => [\yii\web\JqueryAsset::class]]);
$this->registerJsFile("https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js", ['depends' => [\yii\web\JqueryAsset::class]]);
?>
<style>
.level-unsur { background-color: #558ED5 !important; color: #fff !important; font-weight: 700; }
.level-bidang_urusan { background-color: #8DB4E3 !important; color: #fff !important; font-weight: 700; }
.level-program { background-color: #DBE5F2 !important; font-weight: 600; }
.level-kegiatan { background-color: #FCD5B6 !important; font-weight: 500; }
.level-sub_kegiatan { background-color: #fff !important; }
.table-eval th { font-size: 11px; text-align: center; vertical-align: middle !important; }
.table-eval td { font-size: 11px; vertical-align: middle !important; }
.table-eval td.td-merged { vertical-align: top !important; background: #f0f4ff !important; font-weight: 500; font-size: 10px; padding-top: 6px; }
.table-eval .text-angka { text-align: right; font-family: 'Consolas', monospace; }
.import-zone { border: 2px dashed #90caf9; border-radius: 12px; padding: 30px; text-align: center; background: #f8fbff; transition: all 0.3s; }
.import-zone:hover { border-color: #1976d2; background: #e3f2fd; }
.history-card { transition: transform 0.2s; }
.history-card:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
.col-toggle-menu { max-height: 400px; overflow-y: auto; padding: 8px 12px; min-width: 220px; }
.col-toggle-menu .form-check { padding: 3px 0 3px 24px; }
.col-toggle-menu .form-check-label { font-size: 12px; cursor: pointer; }
</style>
<div class="pc-container">
<div class="pc-content">
    <div class="page-header"><div class="page-block"><div class="row align-items-center">
        <div class="col-md-12"><ul class="breadcrumb"><li class="breadcrumb-item"><a href="<?= Url::to(['/site/index']) ?>">Home</a></li><li class="breadcrumb-item"><?= Html::encode($this->title) ?></li></ul></div>
        <div class="col-md-12"><div class="page-header-title"><h2 class="mb-0"><?= Html::encode($this->title) ?></h2></div></div>
    </div></div></div>

    <?php foreach (Yii::$app->session->getAllFlashes() as $key => $message): ?>
        <?php $alertClass = ($key == 'error') ? 'danger' : (($key == 'success') ? 'success' : 'warning'); ?>
        <div class="alert alert-<?= $alertClass ?> alert-dismissible fade show" role="alert"><?= $message ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    <?php endforeach; ?>

    <?php if ($isSuperAdmin): ?>
    <div class="card mb-3 border-0" style="background: linear-gradient(135deg, #6a1b9a, #8e24aa); border-radius: 12px;">
        <div class="card-body py-3">
            <form method="get" action="<?= Url::to(['index']) ?>" class="row g-2 align-items-center">
                <?php if (!Yii::$app->urlManager->enablePrettyUrl): ?><input type="hidden" name="r" value="sakip-evaluasi-renja/index"><?php endif; ?>
                <div class="col-auto"><span class="badge bg-warning text-dark"><i class="fas fa-shield-alt me-1"></i> SUPERADMIN</span></div>
                <div class="col-md-6">
                    <select name="refskpd_id" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">-- Pilih SKPD --</option>
                        <?php foreach ($skpdList as $sid => $sname): ?>
                            <option value="<?= Html::encode($sid) ?>" <?= $sid == $selectedSkpdId ? 'selected' : '' ?>><?= Html::encode($sname) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-auto"><input type="number" name="year" class="form-control form-control-sm" value="<?= Html::encode($year) ?>" style="width:100px;" placeholder="Tahun"></div>
                <div class="col-auto"><button type="submit" class="btn btn-light btn-sm"><i class="fas fa-search me-1"></i> Tampilkan</button></div>
            </form>
        </div>
    </div>
    <?php endif; ?>

    <?php if (!$refskpdId && !$isSuperAdmin): ?>
        <div class="alert alert-warning d-flex align-items-center"><i class="fas fa-exclamation-triangle me-2 fa-2x"></i><div><strong>Perhatian!</strong> Akun Anda belum terhubung dengan SKPD manapun.</div></div>
    <?php elseif (!empty($refskpdId) || ($isSuperAdmin && !empty($selectedSkpdId))): ?>

    <div class="row">
        <div class="col-lg-5">
            <div class="card mb-3">
                <div class="card-header" style="background: linear-gradient(135deg, #1976d2, #1565c0); padding: 12px 15px;"><h6 class="text-white mb-0"><i class="fas fa-file-import me-2"></i> Import Evaluasi Renja</h6></div>
                <div class="card-body">
                    <form method="post" action="<?= Url::to(['import']) ?>" enctype="multipart/form-data" id="form-import">
                        <input type="hidden" name="<?= Yii::$app->request->csrfParam ?>" value="<?= Yii::$app->request->csrfToken ?>">
                        <?php if (!Yii::$app->urlManager->enablePrettyUrl): ?><input type="hidden" name="r" value="sakip-evaluasi-renja/import"><?php endif; ?>
                        <?php if ($isSuperAdmin): ?>
                        <div class="mb-3">
                            <label class="form-label fw-bold small"><i class="fas fa-building me-1"></i> Import untuk SKPD</label>
                            <select name="refskpd_id" class="form-select form-select-sm">
                                <?php foreach ($skpdList as $sid => $sname): ?>
                                    <option value="<?= Html::encode($sid) ?>" <?= $sid == $selectedSkpdId ? 'selected' : '' ?>><?= Html::encode($sname) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <?php endif; ?>
                        <div class="import-zone mb-3" id="drop-zone">
                            <i class="fas fa-cloud-upload-alt fa-3x text-primary mb-2"></i>
                            <p class="mb-1 fw-bold">Pilih atau drag file Excel (.xlsx)</p>
                            <p class="text-muted small mb-2">Format: Evaluasi Terhadap Hasil RKPD | Maks. 10MB</p>
                            <input type="file" name="excel_file" id="excel_file" accept=".xlsx,.xls" class="form-control" required>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-6"><label class="form-label fw-bold small">Tahun Import</label><input type="number" name="tahun_import" class="form-control" placeholder="Auto dari file" min="2020" max="2040"><small class="text-muted">Kosongkan = otomatis dari file</small></div>
                            <div class="col-6 d-flex align-items-end"><div class="form-check"><input class="form-check-input" type="checkbox" name="replace_existing" value="1" id="replace_existing" checked><label class="form-check-label small" for="replace_existing">Hapus data lama (tahun sama)</label></div></div>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary" id="btn-import"><i class="fas fa-upload me-1"></i> Import Data</button>
                            <a href="<?= Url::to(['download-template']) ?>" class="btn btn-outline-secondary btn-sm"><i class="fas fa-download me-1"></i> Download Template</a>
                        </div>
                    </form>
                </div>
            </div>
            <?php if (!empty($importHistory)): ?>
            <div class="card mb-3">
                <div class="card-header" style="background: linear-gradient(135deg, #388e3c, #2e7d32); padding: 12px 15px;"><h6 class="text-white mb-0"><i class="fas fa-history me-2"></i> Riwayat Import</h6></div>
                <div class="card-body p-2">
                    <?php foreach ($importHistory as $hist): ?>
                    <div class="card history-card mb-2"><div class="card-body p-2 d-flex justify-content-between align-items-center">
                        <div><span class="badge bg-primary"><?= $hist['tahun'] ?></span> <small class="text-muted ms-1"><?= $hist['jumlah'] ?> baris</small><br><small class="text-muted"><?= date('d M Y H:i', strtotime($hist['last_import'])) ?></small></div>
                        <div>
                            <a href="<?= Url::to(['index', 'year' => $hist['tahun'], 'refskpd_id' => $selectedSkpdId]) ?>" class="btn btn-sm btn-outline-primary" title="Lihat"><i class="fas fa-eye"></i></a>
                            <form method="post" action="<?= Url::to(['delete']) ?>" style="display:inline" onsubmit="return confirm('Hapus semua data tahun <?= $hist['tahun'] ?>?')">
                                <input type="hidden" name="<?= Yii::$app->request->csrfParam ?>" value="<?= Yii::$app->request->csrfToken ?>">
                                <input type="hidden" name="tahun" value="<?= $hist['tahun'] ?>">
                                <input type="hidden" name="refskpd_id" value="<?= Html::encode($selectedSkpdId) ?>">
                                <?php if (!Yii::$app->urlManager->enablePrettyUrl): ?><input type="hidden" name="r" value="sakip-evaluasi-renja/delete"><?php endif; ?>
                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus"><i class="fas fa-trash"></i></button>
                            </form>
                        </div>
                    </div></div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
        <div class="col-lg-7">
            <div class="card mb-3">
                <div class="card-header" style="background-color: #04A9F5; padding: 10px 15px;"><h6 class="text-white mb-0"><i class="fas fa-filter me-2"></i> Periode - <?= Html::encode(ucwords(strtolower($skpdName))) ?></h6></div>
                <div class="card-body">
                    <form method="get" action="<?= Url::to(['index']) ?>" class="row g-3 align-items-center">
                        <?php if (!Yii::$app->urlManager->enablePrettyUrl): ?><input type="hidden" name="r" value="sakip-evaluasi-renja/index"><?php endif; ?>
                        <?php if ($isSuperAdmin && !empty($selectedSkpdId)): ?><input type="hidden" name="refskpd_id" value="<?= Html::encode($selectedSkpdId) ?>"><?php endif; ?>
                        <div class="col-auto"><label for="year" class="col-form-label fw-bold">Tahun:</label></div>
                        <div class="col-auto"><input type="number" name="year" id="year" class="form-control" value="<?= Html::encode($year) ?>" style="width:120px;"></div>
                        <div class="col-auto"><button type="submit" class="btn btn-primary"><i class="fas fa-search me-1"></i> Tampilkan</button></div>
                    </form>
                </div>
            </div>
            <?php if (!empty($data)): ?>
            <?php
                $totalProgram = 0; $totalKegiatan = 0; $totalSubKegiatan = 0;
                foreach ($data as $d) {
                    if ($d->level === 'program') $totalProgram++;
                    if ($d->level === 'kegiatan') $totalKegiatan++;
                    if ($d->level === 'sub_kegiatan') $totalSubKegiatan++;
                }
            ?>
            <div class="row g-2 mb-3">
                <div class="col-4"><div class="card text-center border-0" style="background:linear-gradient(135deg,#e3f2fd,#bbdefb);"><div class="card-body p-2"><h4 class="mb-0 text-primary"><?= $totalProgram ?></h4><small class="text-muted">Program</small></div></div></div>
                <div class="col-4"><div class="card text-center border-0" style="background:linear-gradient(135deg,#fff3e0,#ffe0b2);"><div class="card-body p-2"><h4 class="mb-0" style="color:#e65100"><?= $totalKegiatan ?></h4><small class="text-muted">Kegiatan</small></div></div></div>
                <div class="col-4"><div class="card text-center border-0" style="background:linear-gradient(135deg,#e8f5e9,#c8e6c9);"><div class="card-body p-2"><h4 class="mb-0 text-success"><?= $totalSubKegiatan ?></h4><small class="text-muted">Sub Kegiatan</small></div></div></div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Tabel Data -->
    <div class="row"><div class="col-12"><div class="card">
        <div class="card-header d-flex justify-content-between align-items-center" style="background-color: #04A9F5; padding: 10px 15px;">
            <h6 class="text-white mb-0"><i class="fas fa-table me-2"></i> Data Evaluasi Renja - <?= Html::encode(ucwords(strtolower($skpdName))) ?> (Tahun <?= Html::encode($year) ?>)
            <?php if (!empty($data)): ?><span class="badge bg-light text-dark ms-2"><?= count($data) ?> baris</span><?php endif; ?></h6>
            <?php if (!empty($data)): ?>
            <div class="dropdown">
                <button class="btn btn-sm btn-light dropdown-toggle" type="button" id="colToggleBtn" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                    <i class="fas fa-columns me-1"></i> Kolom
                </button>
                <div class="dropdown-menu dropdown-menu-end col-toggle-menu" aria-labelledby="colToggleBtn">
                    <div class="fw-bold small mb-1 text-muted">Tampilkan Kolom:</div>
                    <?php
                    $colGroups = [
                        ['idx' => 0, 'label' => 'No'],
                        ['idx' => 1, 'label' => 'Visi'],
                        ['idx' => 2, 'label' => 'Misi'],
                        ['idx' => 3, 'label' => 'Sasaran RPJMD/NSPK'],
                        ['idx' => 4, 'label' => 'Tujuan Perangkat Daerah'],
                        ['idx' => 5, 'label' => 'Sasaran Perangkat Daerah'],
                        ['idx' => 6, 'label' => 'Urusan/Program/Kegiatan'],
                        ['idx' => 7, 'label' => 'Indikator Kinerja'],
                        ['idx' => 8, 'label' => 'Satuan'],
                        ['idx' => '9,10', 'label' => 'Target Renstra'],
                        ['idx' => '11,12', 'label' => 'Realisasi s/d Thn Lalu'],
                        ['idx' => '13,14', 'label' => 'Target Renja'],
                        ['idx' => '15,16,17', 'label' => 'TW I'],
                        ['idx' => '18,19,20', 'label' => 'TW II'],
                        ['idx' => '21,22,23', 'label' => 'TW III'],
                        ['idx' => '24,25,26', 'label' => 'TW IV'],
                        ['idx' => '27,28', 'label' => 'Realisasi Capaian'],
                        ['idx' => '29,30', 'label' => 'Realisasi Renstra'],
                        ['idx' => '31,32', 'label' => 'Tingkat Capaian (%)'],
                    ];
                    foreach ($colGroups as $cg): ?>
                        <div class="form-check">
                            <input class="form-check-input col-toggle" type="checkbox" checked data-cols="<?= $cg['idx'] ?>" id="col-<?= is_string($cg['idx']) ? str_replace(',','-',$cg['idx']) : $cg['idx'] ?>">
                            <label class="form-check-label" for="col-<?= is_string($cg['idx']) ? str_replace(',','-',$cg['idx']) : $cg['idx'] ?>"><?= $cg['label'] ?></label>
                        </div>
                    <?php endforeach; ?>
                    <hr class="my-1">
                    <button class="btn btn-sm btn-outline-primary w-100" onclick="$('.col-toggle').prop('checked',true).trigger('change')">Tampilkan Semua</button>
                </div>
            </div>
            <?php endif; ?>
        </div>
        <div class="card-body">
            <?php if (empty($data)): ?>
                <div class="text-center py-5"><i class="fas fa-inbox fa-4x text-muted mb-3"></i><p class="text-muted">Belum ada data untuk tahun <?= Html::encode($year) ?>.</p></div>
            <?php else: ?>
            <?php
            // Pre-calculate rowspans for visi, misi, tujuan, sasaran_strategis, sasaran_opd
            $totalRows = count($data);
            $mergeFields = ['visi', 'misi', 'tujuan', 'sasaran_strategis', 'sasaran_opd'];
            $rowspans = [];
            foreach ($mergeFields as $field) {
                $rowspans[$field] = [];
                $i = 0;
                while ($i < $totalRows) {
                    $val = $data[$i]->$field;
                    $span = 1;
                    while ($i + $span < $totalRows && $data[$i + $span]->$field === $val) {
                        $span++;
                    }
                    $rowspans[$field][$i] = $span;
                    for ($j = 1; $j < $span; $j++) {
                        $rowspans[$field][$i + $j] = 0; // skip
                    }
                    $i += $span;
                }
            }
            ?>
            <div class="table-responsive">
                <table class="table table-bordered table-eval w-100" style="font-size:11px;">
                    <thead class="table-light">
                        <tr>
                            <th rowspan="3" style="width:35px">No</th>
                            <th rowspan="3">Visi</th>
                            <th rowspan="3">Misi</th>
                            <th rowspan="3">Sasaran RPJMD/ NSPK</th>
                            <th rowspan="3">Tujuan Perangkat Daerah</th>
                            <th rowspan="3">Sasaran Perangkat Daerah</th>
                            <th rowspan="3">Urusan/Bidang Urusan Pemerintahan Daerah dan Program/Kegiatan</th>
                            <th rowspan="3">Indikator Kinerja Program (Outcome)/ Kegiatan (Output)</th>
                            <th rowspan="3" style="width:45px">Satuan</th>
                            <th colspan="2">Target Renstra Perangkat Daerah Pada Tahun 2030 (Akhir Periode Renstra Perangkat Daerah)</th>
                            <th colspan="2">Realisasi Capaian Kinerja Renstra Perangkat Daerah sampai dengan Renja Perangkat Daerah Tahun <?= $year - 1 ?></th>
                            <th colspan="2">Target Kinerja dan Anggaran Renja Perangkat Daerah Tahun Berjalan (Tahun <?= $year ?>) yang direncanakan</th>
                            <th colspan="12">Realisasi Kinerja Pada Triwulan</th>
                            <th colspan="2">Realisasi Capaian Kinerja dan Anggaran Renja Perangkat Daerah Tahun Berjalan</th>
                            <th colspan="2">Realisasi Kinerja dan Anggaran Renstra Perangkat Daerah s/d Tahun Berjalan</th>
                            <th colspan="2">Tingkat Capaian Kinerja dan Realisasi Anggaran Renstra (%)</th>
                        </tr>
                        <tr>
                            <th rowspan="2">K</th><th rowspan="2">Rp.</th>
                            <th rowspan="2">K</th><th rowspan="2">Rp.</th>
                            <th rowspan="2">K</th><th rowspan="2">Rp.</th>
                            <th colspan="3">TW I</th><th colspan="3">TW II</th><th colspan="3">TW III</th><th colspan="3">TW IV</th>
                            <th rowspan="2">K</th><th rowspan="2">Rp.</th>
                            <th rowspan="2">K</th><th rowspan="2">Rp.</th>
                            <th rowspan="2">K</th><th rowspan="2">Rp.</th>
                        </tr>
                        <tr>
                            <th>K</th><th>%</th><th>Rp.</th>
                            <th>K</th><th>%</th><th>Rp.</th>
                            <th>K</th><th>%</th><th>Rp.</th>
                            <th>K</th><th>%</th><th>Rp.</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($data as $idx => $row): ?>
                        <tr class="level-<?= $row->level ?>">
                            <td class="text-center"><?= $row->no_urut ?? '' ?></td>
                            <?php foreach ($mergeFields as $field): ?>
                                <?php if ($rowspans[$field][$idx] > 0): ?>
                                    <td class="td-merged" rowspan="<?= $rowspans[$field][$idx] ?>"><?= Html::encode($row->$field) ?></td>
                                <?php endif; ?>
                            <?php endforeach; ?>
                            <td>
                                <?php if ($row->level === 'program'): ?><i class="fas fa-folder-open me-1 text-primary"></i>
                                <?php elseif ($row->level === 'kegiatan'): ?><i class="fas fa-tasks me-1 text-secondary"></i>
                                <?php elseif ($row->level === 'sub_kegiatan'): ?>&nbsp;&nbsp;&nbsp;&nbsp;
                                <?php endif; ?>
                                <?= Html::encode($row->getNamaByLevel()) ?>
                            </td>
                            <td><?= Html::encode($row->indikator_kinerja) ?></td>
                            <td class="text-center"><?= Html::encode($row->satuan) ?></td>
                            <td class="text-angka"><?= SakipEvaluasiRenja::formatKinerja($row->target_renstra_kinerja) ?></td>
                            <td class="text-angka"><?= SakipEvaluasiRenja::formatRupiah($row->target_renstra_anggaran) ?></td>
                            <td class="text-angka"><?= SakipEvaluasiRenja::formatKinerja($row->realisasi_sd_lalu_kinerja) ?></td>
                            <td class="text-angka"><?= SakipEvaluasiRenja::formatRupiah($row->realisasi_sd_lalu_anggaran) ?></td>
                            <td class="text-angka"><?= SakipEvaluasiRenja::formatKinerja($row->target_renja_kinerja) ?></td>
                            <td class="text-angka"><?= SakipEvaluasiRenja::formatRupiah($row->target_renja_anggaran) ?></td>
                            <td class="text-angka"><?= SakipEvaluasiRenja::formatKinerja($row->tw1_kinerja) ?></td>
                            <td class="text-angka"><?= SakipEvaluasiRenja::formatPersen($row->tw1_persen) ?></td>
                            <td class="text-angka"><?= SakipEvaluasiRenja::formatRupiah($row->tw1_anggaran) ?></td>
                            <td class="text-angka"><?= SakipEvaluasiRenja::formatKinerja($row->tw2_kinerja) ?></td>
                            <td class="text-angka"><?= SakipEvaluasiRenja::formatPersen($row->tw2_persen) ?></td>
                            <td class="text-angka"><?= SakipEvaluasiRenja::formatRupiah($row->tw2_anggaran) ?></td>
                            <td class="text-angka"><?= SakipEvaluasiRenja::formatKinerja($row->tw3_kinerja) ?></td>
                            <td class="text-angka"><?= SakipEvaluasiRenja::formatPersen($row->tw3_persen) ?></td>
                            <td class="text-angka"><?= SakipEvaluasiRenja::formatRupiah($row->tw3_anggaran) ?></td>
                            <td class="text-angka"><?= SakipEvaluasiRenja::formatKinerja($row->tw4_kinerja) ?></td>
                            <td class="text-angka"><?= SakipEvaluasiRenja::formatPersen($row->tw4_persen) ?></td>
                            <td class="text-angka"><?= SakipEvaluasiRenja::formatRupiah($row->tw4_anggaran) ?></td>
                            <td class="text-angka"><?= SakipEvaluasiRenja::formatKinerja($row->realisasi_capaian_kinerja) ?></td>
                            <td class="text-angka"><?= SakipEvaluasiRenja::formatRupiah($row->realisasi_capaian_anggaran) ?></td>
                            <td class="text-angka"><?= SakipEvaluasiRenja::formatKinerja($row->realisasi_renstra_kinerja) ?></td>
                            <td class="text-angka"><?= SakipEvaluasiRenja::formatRupiah($row->realisasi_renstra_anggaran) ?></td>
                            <td class="text-angka"><?= SakipEvaluasiRenja::formatPersen($row->tingkat_capaian_kinerja) ?></td>
                            <td class="text-angka"><?= SakipEvaluasiRenja::formatPersen($row->tingkat_capaian_anggaran) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
    </div></div></div>
    <?php endif; ?>
</div>
</div>
<?php
$script = <<<JS
$('#form-import').on('submit', function() { $('#btn-import').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Mengimport...'); });
var dz = document.getElementById('drop-zone');
if (dz) {
    ['dragenter','dragover'].forEach(function(e){ dz.addEventListener(e, function(ev){ ev.preventDefault(); dz.style.borderColor='#1976d2'; dz.style.background='#e3f2fd'; }); });
    ['dragleave','drop'].forEach(function(e){ dz.addEventListener(e, function(ev){ ev.preventDefault(); dz.style.borderColor='#90caf9'; dz.style.background='#f8fbff'; }); });
    dz.addEventListener('drop', function(e){ if(e.dataTransfer.files.length>0) document.getElementById('excel_file').files=e.dataTransfer.files; });
}

// Column toggle
$('.col-toggle').on('change', function() {
    var cols = $(this).data('cols').toString().split(',');
    var show = $(this).is(':checked');
    var tbl = $('.table-eval');
    cols.forEach(function(c) {
        var idx = parseInt(c) + 1; // nth-child is 1-indexed
        if (show) {
            tbl.find('th:nth-child('+idx+'), td:nth-child('+idx+')').show();
        } else {
            tbl.find('th:nth-child('+idx+'), td:nth-child('+idx+')').hide();
        }
    });
});
JS;
$this->registerJs($script);
?>
