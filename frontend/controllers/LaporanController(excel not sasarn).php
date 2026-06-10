<?php

namespace frontend\controllers;

use backend\models\SakipPenjabatSkpd;
use Yii;
use frontend\models\SakipSkpd;
use frontend\models\SakipPeriode;
use frontend\models\SakipPimpinan;
use frontend\models\SakipSasaranrenstra;
use frontend\models\SakipIndikatorsasaranrenstra;
use frontend\models\SakipIndikatorsasaranrenstraTriwulan;
use frontend\models\SakipStrategi;
use frontend\models\SakipKebijakan;
use frontend\models\SakipCascadingprogram;
use frontend\models\SakipCascadingsubkegiatan;
use frontend\models\SakipPenjabatskpdCascadingprogram;
use frontend\models\SakipPenjabatskpdCascadingkegiatan;
use frontend\models\SakipPenjabatskpdCascadingsubkegiatan;
use frontend\models\SakipIndikatorcascadingprogram;
use frontend\models\SakipIndikatorcascadingsubkegiatan;
use frontend\models\SakipIndikatorcascadingprogramTriwulan;
use frontend\models\SakipIndikatorcascadingkegiatanTriwulan;
use frontend\models\SakipIndikatorcascadingsubkegiatanTriwulan;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use yii\web\Controller;
use yii\web\Response;
use yii\helpers\Html;
use kartik\mpdf\Pdf;
use kartik\export\ExportMenu; // Import ExportMenu
use yii\web\UploadedFile;
use yii\helpers\Url;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\db\Expression;
use yii\helpers\Json;
use yii\helpers\ArrayHelper;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use frontend\models\SakipKoordinasi;

class LaporanController extends Controller
{

    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'access' => [
                    'class' => AccessControl::class,
                    'rules' => [
                        [
                            'allow' => true,
                            'roles' => ['@'], // Hanya untuk pengguna yang sudah login
                        ],
                    ],
                    'denyCallback' => function ($rule, $action) {
                        return Yii::$app->response->redirect(['site/login']);
                    },
                ],
                'verbs' => [
                    'class' => VerbFilter::class,
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    public function actionIndexEvaluasiRkpd($refperiode_id = null)
    {
        // Get refskpd_id from the current user
        $user = Yii::$app->user->identity;
        $refskpd_id = $user->refskpd_id;

        // Get the name of the SKPD based on refskpd_id
        $nama_skpd = SakipSkpd::find()->select('nama_skpd')->where(['refskpd_id' => $refskpd_id])->scalar();

        // Set default period to this year if not provided
        if ($refperiode_id === null) {
            $currentYear = date('Y');
            $defaultPeriod = SakipPeriode::find()->where(['periode' => $currentYear])->one();
            $refperiode_id = $defaultPeriod ? $defaultPeriod->refperiode_id : null;
        }

        // Fetch all periods
        $periodeList = SakipPeriode::find()->all();

        // =========================================================================
        // BLOK BARU: MENGAMBIL DAN MEMPROSES DATA LAPORAN
        // =========================================================================
        $laporanData = [];
        if ($refskpd_id && $refperiode_id) {
            // 1. Ambil semua program yang di-cascading untuk SKPD dan Periode terpilih
            //    Gunakan with() (Eager Loading) untuk mengambil relasi secara efisien
            $cascadingPrograms = SakipCascadingprogram::find()
                ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
                ->with(['refProgram', 'cascadingSubkegiatans']) // Memuat nama program & semua sub kegiatan terkait
                ->all();

            // 2. Proses setiap program untuk menyusun data sesuai format yang diinginkan
            foreach ($cascadingPrograms as $program) {
                // Hitung total anggaran dengan menjumlahkan 'subkegiatan_anggaran'
                // dari relasi 'cascadingSubkegiatans' yang sudah kita muat.
                $totalAnggaran = 0;
                if (!empty($program->cascadingSubkegiatans)) {
                    $totalAnggaran = array_sum(ArrayHelper::getColumn($program->cascadingSubkegiatans, 'subkegiatan_anggaran'));
                }

                // Tambahkan data yang sudah terstruktur ke array akhir
                $laporanData[] = [
                    'kode_program' => $program->refProgram->kode_program ?? 'N/A',
                    'nama_program' => $program->refProgram->nama_program ?? 'N/A',
                    'uraian_indikator' => $program->uraian_indikatorprogram,
                    'satuan' => $program->program_satuan,
                    'target' => $program->program_target,
                    'total_anggaran' => $totalAnggaran,
                ];
            }
        }
        // =========================================================================
        // AKHIR BLOK BARU
        // =========================================================================

        // Retrieve the periode based on refperiode_id
        $selectedPeriod = SakipPeriode::find()->where(['refperiode_id' => $refperiode_id])->one();
        $selectedPeriodValue = $selectedPeriod ? $selectedPeriod->periode : null; // Get the periode value

        // Set the dynamic title
        $this->view->title = "Laporan Evaluasi RKPD $selectedPeriodValue - " . Html::encode($nama_skpd);

        return $this->render('index-evaluasi-rkpd', [
            'periodeList' => $periodeList,
            'selectedPeriodId' => $refperiode_id,
            'refskpd_id' => $refskpd_id,
            'refperiode_id' => $refperiode_id,
            'nama_skpd' => $nama_skpd,
            'selectedPeriodValue' => $selectedPeriodValue,
            'laporanData' => $laporanData, // <-- Kirim data laporan ke view
        ]);
    }

    public function actionIndexEvaluasiRkpdDev($refperiode_id = null, $refskpd_id = null)
    {
        $user = Yii::$app->user->identity;
        $assignments = Yii::$app->authManager->getAssignments($user->id);

        $skpdList = [];
        $allowedSkpdIds = []; // Daftar ID SKPD yang diizinkan untuk user ini

        if (isset($assignments['superadmin']) || isset($assignments['admin'])) {
            // --- BLOK UNTUK ADMIN ---
            $allSkpd = SakipSkpd::find()->where(['skpd_isaktif' => 'T'])->orderBy('nama_skpd ASC')->all();
            $skpdList = ArrayHelper::map($allSkpd, 'refskpd_id', 'nama_skpd');
            $allowedSkpdIds = array_keys($skpdList);
        } else {
            // --- BLOK UNTUK NON-ADMIN (KOORDINATOR) ---
            $coordinatedSkpdIds = SakipKoordinasi::find()
                ->select('refskpd_id')
                ->where(['refuser_id' => $user->id])
                ->column();

            $allowedSkpdIds = $coordinatedSkpdIds;

            if (!empty($allowedSkpdIds)) {
                $skpdList = ArrayHelper::map(
                    SakipSkpd::find()->where(['refskpd_id' => $allowedSkpdIds, 'skpd_isaktif' => 'T'])->orderBy('nama_skpd ASC')->all(),
                    'refskpd_id',
                    'nama_skpd'
                );
            }
        }

        // --- Validasi Keamanan ---
        // Cek apakah refskpd_id dari URL diizinkan untuk diakses
        if ($refskpd_id !== null && !in_array($refskpd_id, $allowedSkpdIds)) {
            throw new ForbiddenHttpException('Anda tidak memiliki hak akses untuk melihat data SKPD ini.');
        }

        // Tentukan refskpd_id yang akan digunakan jika tidak ada di URL atau tidak valid
        if ($refskpd_id === null || !in_array($refskpd_id, $allowedSkpdIds)) {
            $refskpd_id = !empty($allowedSkpdIds) ? $allowedSkpdIds[0] : null;
        }

        // =========================================================================
        // AKHIR DARI BLOK LOGIKA BARU
        // Kode di bawah ini sekarang menggunakan $refskpd_id yang sudah aman
        // =========================================================================

        // Get the name of the SKPD based on refskpd_id
        $nama_skpd = $refskpd_id ? SakipSkpd::find()->select('nama_skpd')->where(['refskpd_id' => $refskpd_id])->scalar() : 'Tidak ada SKPD dipilih';

        // Set default period to this year if not provided
        if ($refperiode_id === null) {
            $currentYear = date('Y');
            $defaultPeriod = SakipPeriode::find()->where(['periode' => $currentYear])->one();
            $refperiode_id = $defaultPeriod ? $defaultPeriod->refperiode_id : null;
        }

        // Fetch all periods
        $periodeList = SakipPeriode::find()->all();

        $laporanData = [];
        if ($refskpd_id && $refperiode_id) {

            // 1. Query dan pembuatan Peta Triwulan (tetap sama)
            $allProgramTriwulan = SakipIndikatorcascadingprogramTriwulan::find()->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])->all();
            $allKegiatanTriwulan = SakipIndikatorcascadingkegiatanTriwulan::find()->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])->all();
            $allSubkegiatanTriwulan = SakipIndikatorcascadingsubkegiatanTriwulan::find()->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])->all();

            $programTriwulanMap = ArrayHelper::index($allProgramTriwulan, null, 'refcascadingprogram_id');
            $kegiatanTriwulanMap = ArrayHelper::index($allKegiatanTriwulan, null, 'refcascadingkegiatan_id');
            $subkegiatanTriwulanMap = ArrayHelper::index($allSubkegiatanTriwulan, null, 'refcascadingsubkegiatan_id');

            // 2. Query utama, pastikan relasi ke sasaranRenstra ada dan di-load
            $cascadingPrograms = SakipCascadingprogram::find()
                ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
                ->with([
                    'sasaranRenstra', // PENTING: Eager load relasi ke Sasaran Renstra
                    'refProgram',
                    'cascadingKegiatans.refKegiatan',
                    'cascadingKegiatans.cascadingSubkegiatans.refSubkegiatan'
                ])
                ->all();

            // 3. [BARU] Array untuk proses pengelompokan multi-level
            $groupedBySasaran = [];

            foreach ($cascadingPrograms as $cascadingProgram) {
                // Skip jika data utama tidak lengkap
                if (!$cascadingProgram->sasaranRenstra || !$cascadingProgram->refProgram) {
                    continue;
                }

                $sasaranId = $cascadingProgram->refsasaranrenstra_id;
                $programId = $cascadingProgram->refProgram->refprogram_id;

                // Inisialisasi Sasaran jika belum ada
                if (!isset($groupedBySasaran[$sasaranId])) {
                    $groupedBySasaran[$sasaranId] = [
                        'uraian_sasaranrenstra' => $cascadingProgram->sasaranRenstra->uraian_sasaranrenstra,
                        'programs' => [],
                    ];
                }

                // Inisialisasi Program jika belum ada di dalam Sasaran tsb
                if (!isset($groupedBySasaran[$sasaranId]['programs'][$programId])) {
                    $groupedBySasaran[$sasaranId]['programs'][$programId] = [
                        'kode_program' => $cascadingProgram->refProgram->kode_program,
                        'nama_program' => $cascadingProgram->refProgram->nama_program,
                        'uraian_indikator' => $cascadingProgram->uraian_indikatorprogram,
                        'satuan' => $cascadingProgram->program_satuan,
                        'target' => $cascadingProgram->program_target,
                        'realisasi' => [1 => null, 2 => null, 3 => null, 4 => null],
                        'kegiatans' => [],
                    ];
                }

                // Proses realisasi program
                $programTriwulans = $programTriwulanMap[$cascadingProgram->refcascadingprogram_id] ?? [];
                foreach ($programTriwulans as $tw) {
                    $groupedBySasaran[$sasaranId]['programs'][$programId]['realisasi'][$tw->reftriwulan_id] = $tw->triwulan_realisasi;
                }

                // Proses Kegiatan dan Sub Kegiatan
                foreach ($cascadingProgram->cascadingKegiatans as $cascadingKegiatan) {
                    if (!$cascadingKegiatan->refKegiatan) continue;

                    $kegiatanId = $cascadingKegiatan->refKegiatan->refkegiatan_id;

                    // Inisialisasi Kegiatan jika belum ada di dalam Program tsb
                    if (!isset($groupedBySasaran[$sasaranId]['programs'][$programId]['kegiatans'][$kegiatanId])) {
                        $groupedBySasaran[$sasaranId]['programs'][$programId]['kegiatans'][$kegiatanId] = [
                            'kode_kegiatan' => $cascadingKegiatan->refKegiatan->kode_kegiatan,
                            'nama_kegiatan' => $cascadingKegiatan->refKegiatan->nama_kegiatan,
                            'uraian_indikator' => $cascadingKegiatan->uraian_indikatorkegiatan,
                            'satuan' => $cascadingKegiatan->kegiatan_satuan,
                            'target' => $cascadingKegiatan->kegiatan_target,
                            'realisasi' => [1 => null, 2 => null, 3 => null, 4 => null],
                            'subkegiatans' => [],
                        ];
                    }

                    // Proses realisasi kegiatan
                    $kegiatanTriwulans = $kegiatanTriwulanMap[$cascadingKegiatan->refcascadingkegiatan_id] ?? [];
                    foreach ($kegiatanTriwulans as $tw) {
                        $groupedBySasaran[$sasaranId]['programs'][$programId]['kegiatans'][$kegiatanId]['realisasi'][$tw->reftriwulan_id] = $tw->triwulan_realisasi;
                    }

                    // Proses Sub Kegiatan
                    foreach ($cascadingKegiatan->cascadingSubkegiatans as $subkegiatan) {
                        $subkegiatanTriwulans = $subkegiatanTriwulanMap[$subkegiatan->refcascadingsubkegiatan_id] ?? [];
                        $realisasiSubKeg = [1 => null, 2 => null, 3 => null, 4 => null];
                        $penyerapanSubKeg = [1 => 0, 2 => 0, 3 => 0, 4 => 0];
                        foreach ($subkegiatanTriwulans as $tw) {
                            $realisasiSubKeg[$tw->reftriwulan_id] = $tw->triwulan_realisasi;
                            $penyerapanSubKeg[$tw->reftriwulan_id] = (float) $tw->triwulan_penyerapan_anggaran;
                        }

                        // Hitung total penyerapan untuk sub kegiatan ini
                        $totalPenyerapanSubKeg = array_sum($penyerapanSubKeg);

                        // Langsung tambahkan sub kegiatan tanpa grouping lebih lanjut
                        $groupedBySasaran[$sasaranId]['programs'][$programId]['kegiatans'][$kegiatanId]['subkegiatans'][] = [
                            'kode_subkegiatan' => $subkegiatan->refSubkegiatan->kode_subkegiatan ?? 'N/A',
                            'nama_subkegiatan' => $subkegiatan->refSubkegiatan->nama_subkegiatan ?? 'N/A',
                            'uraian_indikator' => $subkegiatan->uraian_indikatorsubkegiatan,
                            'satuan' => $subkegiatan->subkegiatan_satuan,
                            'target' => $subkegiatan->subkegiatan_target,
                            'total_anggaran' => (float) $subkegiatan->subkegiatan_anggaran,
                            'realisasi' => $realisasiSubKeg,
                            'triwulan_penyerapan_anggaran' => $penyerapanSubKeg,
                            'total_penyerapan' => $totalPenyerapanSubKeg, // <-- [TAMBAHKAN BARIS INI]
                        ];
                    }
                }
            }

            // 4. [BARU] Proses agregasi total dan re-indexing array
            foreach ($groupedBySasaran as $s_id => &$sasaran) { // by reference
                foreach ($sasaran['programs'] as $p_id => &$program) { // by reference
                    foreach ($program['kegiatans'] as $k_id => &$kegiatan) { // by reference
                        $kegiatan['total_anggaran'] = array_sum(ArrayHelper::getColumn($kegiatan['subkegiatans'], 'total_anggaran'));
                        $kegiatan['total_penyerapan'] = array_sum(ArrayHelper::getColumn($kegiatan['subkegiatans'], 'total_penyerapan'));
                        $kegiatan['total_realisasi'] = array_sum(array_filter($kegiatan['realisasi'], 'is_numeric'));
                        $kegiatan['triwulan_penyerapan_anggaran'] = [
                            1 => array_sum(ArrayHelper::getColumn($kegiatan['subkegiatans'], ['triwulan_penyerapan_anggaran', 1])),
                            2 => array_sum(ArrayHelper::getColumn($kegiatan['subkegiatans'], ['triwulan_penyerapan_anggaran', 2])),
                            3 => array_sum(ArrayHelper::getColumn($kegiatan['subkegiatans'], ['triwulan_penyerapan_anggaran', 3])),
                            4 => array_sum(ArrayHelper::getColumn($kegiatan['subkegiatans'], ['triwulan_penyerapan_anggaran', 4])),
                        ];
                    }
                    $program['kegiatans'] = array_values($program['kegiatans']); // Re-index kegiatan

                    $program['total_anggaran'] = array_sum(ArrayHelper::getColumn($program['kegiatans'], 'total_anggaran'));
                    $program['total_penyerapan'] = array_sum(ArrayHelper::getColumn($program['kegiatans'], 'total_penyerapan'));
                    $program['total_realisasi'] = array_sum(array_filter($program['realisasi'], 'is_numeric'));
                    $program['triwulan_penyerapan_anggaran'] = [
                        1 => array_sum(ArrayHelper::getColumn($program['kegiatans'], ['triwulan_penyerapan_anggaran', 1])),
                        2 => array_sum(ArrayHelper::getColumn($program['kegiatans'], ['triwulan_penyerapan_anggaran', 2])),
                        3 => array_sum(ArrayHelper::getColumn($program['kegiatans'], ['triwulan_penyerapan_anggaran', 3])),
                        4 => array_sum(ArrayHelper::getColumn($program['kegiatans'], ['triwulan_penyerapan_anggaran', 4])),
                    ];
                }
                $sasaran['programs'] = array_values($sasaran['programs']); // Re-index program
            }

            // Set data final untuk dikirim ke view
            $laporanData = array_values($groupedBySasaran); // Re-index sasaran
        }

        // Retrieve the periode based on refperiode_id
        $selectedPeriod = SakipPeriode::find()->where(['refperiode_id' => $refperiode_id])->one();
        $selectedPeriodValue = $selectedPeriod ? $selectedPeriod->periode : null; // Get the periode value

        // Set the dynamic title
        $this->view->title = "Laporan Renstra $selectedPeriodValue - " . Html::encode($nama_skpd);

        return $this->render('index-evaluasi-rkpd-dev', [
            'periodeList' => $periodeList,
            'selectedPeriodId' => $refperiode_id,
            'refskpd_id' => $refskpd_id,
            'refperiode_id' => $refperiode_id,
            'nama_skpd' => $nama_skpd,
            'selectedPeriodValue' => $selectedPeriodValue, // Include selected period value
            'selectedSkpdId' => $refskpd_id,
            'skpdList' => $skpdList,
            'laporanData' => $laporanData, // <-- Kirim data laporan ke view
        ]);
    }

    public function actionCetakEvaluasiRkpd($refperiode_id = null, $refskpd_id = null)
    {
        $user = Yii::$app->user->identity;
        $assignments = Yii::$app->authManager->getAssignments($user->id);

        $skpdList = [];
        $allowedSkpdIds = []; // Daftar ID SKPD yang diizinkan untuk user ini

        if (isset($assignments['superadmin']) || isset($assignments['admin'])) {
            // --- BLOK UNTUK ADMIN ---
            $allSkpd = SakipSkpd::find()->where(['skpd_isaktif' => 'T'])->orderBy('nama_skpd ASC')->all();
            $skpdList = ArrayHelper::map($allSkpd, 'refskpd_id', 'nama_skpd');
            $allowedSkpdIds = array_keys($skpdList);
        } else {
            // --- BLOK UNTUK NON-ADMIN (KOORDINATOR) ---
            $coordinatedSkpdIds = SakipKoordinasi::find()
                ->select('refskpd_id')
                ->where(['refuser_id' => $user->id])
                ->column();

            $allowedSkpdIds = $coordinatedSkpdIds;

            if (!empty($allowedSkpdIds)) {
                $skpdList = ArrayHelper::map(
                    SakipSkpd::find()->where(['refskpd_id' => $allowedSkpdIds, 'skpd_isaktif' => 'T'])->orderBy('nama_skpd ASC')->all(),
                    'refskpd_id',
                    'nama_skpd'
                );
            }
        }

        // --- Validasi Keamanan ---
        // Cek apakah refskpd_id dari URL diizinkan untuk diakses
        if ($refskpd_id !== null && !in_array($refskpd_id, $allowedSkpdIds)) {
            throw new ForbiddenHttpException('Anda tidak memiliki hak akses untuk melihat data SKPD ini.');
        }

        // Tentukan refskpd_id yang akan digunakan jika tidak ada di URL atau tidak valid
        if ($refskpd_id === null || !in_array($refskpd_id, $allowedSkpdIds)) {
            $refskpd_id = !empty($allowedSkpdIds) ? $allowedSkpdIds[0] : null;
        }

        // =========================================================================
        // AKHIR DARI BLOK LOGIKA BARU
        // Kode di bawah ini sekarang menggunakan $refskpd_id yang sudah aman
        // =========================================================================

        // Get the name of the SKPD based on refskpd_id
        $nama_skpd = $refskpd_id ? SakipSkpd::find()->select('nama_skpd')->where(['refskpd_id' => $refskpd_id])->scalar() : 'Tidak ada SKPD dipilih';

        // Set default period to this year if not provided
        if ($refperiode_id === null) {
            $currentYear = date('Y');
            $defaultPeriod = SakipPeriode::find()->where(['periode' => $currentYear])->one();
            $refperiode_id = $defaultPeriod ? $defaultPeriod->refperiode_id : null;
        }

        // Fetch all periods
        $periodeList = SakipPeriode::find()->all();

        $laporanData = [];
        if ($refskpd_id && $refperiode_id) {

            // 1. Query dan pembuatan Peta Triwulan (tetap sama)
            $allProgramTriwulan = SakipIndikatorcascadingprogramTriwulan::find()->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])->all();
            $allKegiatanTriwulan = SakipIndikatorcascadingkegiatanTriwulan::find()->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])->all();
            $allSubkegiatanTriwulan = SakipIndikatorcascadingsubkegiatanTriwulan::find()->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])->all();

            $programTriwulanMap = ArrayHelper::index($allProgramTriwulan, null, 'refcascadingprogram_id');
            $kegiatanTriwulanMap = ArrayHelper::index($allKegiatanTriwulan, null, 'refcascadingkegiatan_id');
            $subkegiatanTriwulanMap = ArrayHelper::index($allSubkegiatanTriwulan, null, 'refcascadingsubkegiatan_id');

            // 2. Query utama, pastikan relasi ke sasaranRenstra ada dan di-load
            $cascadingPrograms = SakipCascadingprogram::find()
                ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
                ->with([
                    'sasaranRenstra', // PENTING: Eager load relasi ke Sasaran Renstra
                    'refProgram',
                    'cascadingKegiatans.refKegiatan',
                    'cascadingKegiatans.cascadingSubkegiatans.refSubkegiatan'
                ])
                ->all();

            // 3. [BARU] Array untuk proses pengelompokan multi-level
            $groupedBySasaran = [];

            foreach ($cascadingPrograms as $cascadingProgram) {
                // Skip jika data utama tidak lengkap
                if (!$cascadingProgram->sasaranRenstra || !$cascadingProgram->refProgram) {
                    continue;
                }

                $sasaranId = $cascadingProgram->refsasaranrenstra_id;
                $programId = $cascadingProgram->refProgram->refprogram_id;

                // Inisialisasi Sasaran jika belum ada
                if (!isset($groupedBySasaran[$sasaranId])) {
                    $groupedBySasaran[$sasaranId] = [
                        'uraian_sasaranrenstra' => $cascadingProgram->sasaranRenstra->uraian_sasaranrenstra,
                        'programs' => [],
                    ];
                }

                // Inisialisasi Program jika belum ada di dalam Sasaran tsb
                if (!isset($groupedBySasaran[$sasaranId]['programs'][$programId])) {
                    $groupedBySasaran[$sasaranId]['programs'][$programId] = [
                        'kode_program' => $cascadingProgram->refProgram->kode_program,
                        'nama_program' => $cascadingProgram->refProgram->nama_program,
                        'uraian_indikator' => $cascadingProgram->uraian_indikatorprogram,
                        'satuan' => $cascadingProgram->program_satuan,
                        'target' => $cascadingProgram->program_target,
                        'realisasi' => [1 => null, 2 => null, 3 => null, 4 => null],
                        'kegiatans' => [],
                    ];
                }

                // Proses realisasi program
                $programTriwulans = $programTriwulanMap[$cascadingProgram->refcascadingprogram_id] ?? [];
                foreach ($programTriwulans as $tw) {
                    $groupedBySasaran[$sasaranId]['programs'][$programId]['realisasi'][$tw->reftriwulan_id] = $tw->triwulan_realisasi;
                }

                // Proses Kegiatan dan Sub Kegiatan
                foreach ($cascadingProgram->cascadingKegiatans as $cascadingKegiatan) {
                    if (!$cascadingKegiatan->refKegiatan) continue;

                    $kegiatanId = $cascadingKegiatan->refKegiatan->refkegiatan_id;

                    // Inisialisasi Kegiatan jika belum ada di dalam Program tsb
                    if (!isset($groupedBySasaran[$sasaranId]['programs'][$programId]['kegiatans'][$kegiatanId])) {
                        $groupedBySasaran[$sasaranId]['programs'][$programId]['kegiatans'][$kegiatanId] = [
                            'kode_kegiatan' => $cascadingKegiatan->refKegiatan->kode_kegiatan,
                            'nama_kegiatan' => $cascadingKegiatan->refKegiatan->nama_kegiatan,
                            'uraian_indikator' => $cascadingKegiatan->uraian_indikatorkegiatan,
                            'satuan' => $cascadingKegiatan->kegiatan_satuan,
                            'target' => $cascadingKegiatan->kegiatan_target,
                            'realisasi' => [1 => null, 2 => null, 3 => null, 4 => null],
                            'subkegiatans' => [],
                        ];
                    }

                    // Proses realisasi kegiatan
                    $kegiatanTriwulans = $kegiatanTriwulanMap[$cascadingKegiatan->refcascadingkegiatan_id] ?? [];
                    foreach ($kegiatanTriwulans as $tw) {
                        $groupedBySasaran[$sasaranId]['programs'][$programId]['kegiatans'][$kegiatanId]['realisasi'][$tw->reftriwulan_id] = $tw->triwulan_realisasi;
                    }

                    // Proses Sub Kegiatan
                    foreach ($cascadingKegiatan->cascadingSubkegiatans as $subkegiatan) {
                        $subkegiatanTriwulans = $subkegiatanTriwulanMap[$subkegiatan->refcascadingsubkegiatan_id] ?? [];
                        $realisasiSubKeg = [1 => null, 2 => null, 3 => null, 4 => null];
                        $penyerapanSubKeg = [1 => 0, 2 => 0, 3 => 0, 4 => 0];
                        foreach ($subkegiatanTriwulans as $tw) {
                            $realisasiSubKeg[$tw->reftriwulan_id] = $tw->triwulan_realisasi;
                            $penyerapanSubKeg[$tw->reftriwulan_id] = (float) $tw->triwulan_penyerapan_anggaran;
                        }

                        // Hitung total penyerapan untuk sub kegiatan ini
                        $totalPenyerapanSubKeg = array_sum($penyerapanSubKeg);

                        // Langsung tambahkan sub kegiatan tanpa grouping lebih lanjut
                        $groupedBySasaran[$sasaranId]['programs'][$programId]['kegiatans'][$kegiatanId]['subkegiatans'][] = [
                            'kode_subkegiatan' => $subkegiatan->refSubkegiatan->kode_subkegiatan ?? 'N/A',
                            'nama_subkegiatan' => $subkegiatan->refSubkegiatan->nama_subkegiatan ?? 'N/A',
                            'uraian_indikator' => $subkegiatan->uraian_indikatorsubkegiatan,
                            'satuan' => $subkegiatan->subkegiatan_satuan,
                            'target' => $subkegiatan->subkegiatan_target,
                            'total_anggaran' => (float) $subkegiatan->subkegiatan_anggaran,
                            'realisasi' => $realisasiSubKeg,
                            'triwulan_penyerapan_anggaran' => $penyerapanSubKeg,
                            'total_penyerapan' => $totalPenyerapanSubKeg, // <-- [TAMBAHKAN BARIS INI]
                        ];
                    }
                }
            }

            // 4. [BARU] Proses agregasi total dan re-indexing array
            foreach ($groupedBySasaran as $s_id => &$sasaran) { // by reference
                foreach ($sasaran['programs'] as $p_id => &$program) { // by reference
                    foreach ($program['kegiatans'] as $k_id => &$kegiatan) { // by reference
                        $kegiatan['total_anggaran'] = array_sum(ArrayHelper::getColumn($kegiatan['subkegiatans'], 'total_anggaran'));
                        $kegiatan['total_penyerapan'] = array_sum(ArrayHelper::getColumn($kegiatan['subkegiatans'], 'total_penyerapan'));
                        $kegiatan['total_realisasi'] = array_sum(array_filter($kegiatan['realisasi'], 'is_numeric'));
                        $kegiatan['triwulan_penyerapan_anggaran'] = [
                            1 => array_sum(ArrayHelper::getColumn($kegiatan['subkegiatans'], ['triwulan_penyerapan_anggaran', 1])),
                            2 => array_sum(ArrayHelper::getColumn($kegiatan['subkegiatans'], ['triwulan_penyerapan_anggaran', 2])),
                            3 => array_sum(ArrayHelper::getColumn($kegiatan['subkegiatans'], ['triwulan_penyerapan_anggaran', 3])),
                            4 => array_sum(ArrayHelper::getColumn($kegiatan['subkegiatans'], ['triwulan_penyerapan_anggaran', 4])),
                        ];
                    }
                    $program['kegiatans'] = array_values($program['kegiatans']); // Re-index kegiatan

                    $program['total_anggaran'] = array_sum(ArrayHelper::getColumn($program['kegiatans'], 'total_anggaran'));
                    $program['total_penyerapan'] = array_sum(ArrayHelper::getColumn($program['kegiatans'], 'total_penyerapan'));
                    $program['total_realisasi'] = array_sum(array_filter($program['realisasi'], 'is_numeric'));
                    $program['triwulan_penyerapan_anggaran'] = [
                        1 => array_sum(ArrayHelper::getColumn($program['kegiatans'], ['triwulan_penyerapan_anggaran', 1])),
                        2 => array_sum(ArrayHelper::getColumn($program['kegiatans'], ['triwulan_penyerapan_anggaran', 2])),
                        3 => array_sum(ArrayHelper::getColumn($program['kegiatans'], ['triwulan_penyerapan_anggaran', 3])),
                        4 => array_sum(ArrayHelper::getColumn($program['kegiatans'], ['triwulan_penyerapan_anggaran', 4])),
                    ];
                }
                $sasaran['programs'] = array_values($sasaran['programs']); // Re-index program
            }

            // Set data final untuk dikirim ke view
            $laporanData = array_values($groupedBySasaran); // Re-index sasaran
        }


        // Retrieve the periode based on refperiode_id
        $selectedPeriod = SakipPeriode::find()->where(['refperiode_id' => $refperiode_id])->one();
        $selectedPeriodValue = $selectedPeriod ? $selectedPeriod->periode : null; // Get the periode value

        // Set the dynamic title
        $this->view->title = "Laporan Renstra $selectedPeriodValue - " . Html::encode($nama_skpd);

        // 2. Render konten PDF menggunakan partial view
        $content = $this->renderPartial('_cetak_evaluasi_rkpd', [
            'laporanData' => $laporanData,
            'nama_skpd' => $nama_skpd,
            'selectedPeriodValue' => $selectedPeriodValue,
        ]);

        // 1. Definisikan semua CSS Anda sebagai sebuah string PHP
        $css = "
 /* CSS sederhana khusus untuk PDF */
    .report-table { width: 100%; border-collapse: collapse; font-family: sans-serif; font-size: 9px; }
    .report-table th, .report-table td { border: 1px solid #999; padding: 5px; vertical-align: top; }
    .report-table thead th { background-color: #e6e6e6; text-align: center; font-weight: bold; }
    .program-row { background-color: #f2f2f2; font-weight: bold; }
    .kegiatan-row { background-color: #fafafa; }
    .text-end { text-align: right; }
    .text-center { text-align: center; }
    .fw-bold { font-weight: bold; }
    /* Class untuk indentasi */
    .ps-4 { padding-left: 1.5rem !important; }
    .ps-5 { padding-left: 2.5rem !important; }
    ";

        // 2. Setup MPDF component dan masukkan string CSS ke 'cssInline'
        $pdf = new Pdf([
            'mode' => Pdf::MODE_UTF8,
            'format' => Pdf::FORMAT_A4,
            'orientation' => Pdf::ORIENT_LANDSCAPE,
            'destination' => Pdf::DEST_BROWSER,
            'content' => $content,
            'cssFile' => '@vendor/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css',
            'cssInline' => $css, // <-- SEMUA CSS SEKARANG DI SINI
            'options' => ['title' => 'Laporan Evaluasi RKPD'],
            'methods' => [
                'SetHeader' => ["Laporan Evaluasi RKPD - {$nama_skpd}"],
                'SetFooter' => ['{PAGENO}'],
            ]
        ]);

        return $pdf->render();
    }
    public function actionCetakEvaluasiRkpdExcel($refperiode_id = null, $refskpd_id = null)
    {
        $user = Yii::$app->user->identity;
        $assignments = Yii::$app->authManager->getAssignments($user->id);

        $skpdList = [];
        $allowedSkpdIds = []; // Daftar ID SKPD yang diizinkan untuk user ini

        if (isset($assignments['superadmin']) || isset($assignments['admin'])) {
            // --- BLOK UNTUK ADMIN ---
            $allSkpd = SakipSkpd::find()->where(['skpd_isaktif' => 'T'])->orderBy('nama_skpd ASC')->all();
            $skpdList = ArrayHelper::map($allSkpd, 'refskpd_id', 'nama_skpd');
            $allowedSkpdIds = array_keys($skpdList);
        } else {
            // --- BLOK UNTUK NON-ADMIN (KOORDINATOR) ---
            $coordinatedSkpdIds = SakipKoordinasi::find()
                ->select('refskpd_id')
                ->where(['refuser_id' => $user->id])
                ->column();

            $allowedSkpdIds = $coordinatedSkpdIds;

            if (!empty($allowedSkpdIds)) {
                $skpdList = ArrayHelper::map(
                    SakipSkpd::find()->where(['refskpd_id' => $allowedSkpdIds, 'skpd_isaktif' => 'T'])->orderBy('nama_skpd ASC')->all(),
                    'refskpd_id',
                    'nama_skpd'
                );
            }
        }

        // --- Validasi Keamanan ---
        // Cek apakah refskpd_id dari URL diizinkan untuk diakses
        if ($refskpd_id !== null && !in_array($refskpd_id, $allowedSkpdIds)) {
            throw new ForbiddenHttpException('Anda tidak memiliki hak akses untuk melihat data SKPD ini.');
        }

        // Tentukan refskpd_id yang akan digunakan jika tidak ada di URL atau tidak valid
        if ($refskpd_id === null || !in_array($refskpd_id, $allowedSkpdIds)) {
            $refskpd_id = !empty($allowedSkpdIds) ? $allowedSkpdIds[0] : null;
        }

        // =========================================================================
        // AKHIR DARI BLOK LOGIKA BARU
        // Kode di bawah ini sekarang menggunakan $refskpd_id yang sudah aman
        // =========================================================================

        // Get the name of the SKPD based on refskpd_id
        $nama_skpd = $refskpd_id ? SakipSkpd::find()->select('nama_skpd')->where(['refskpd_id' => $refskpd_id])->scalar() : 'Tidak ada SKPD dipilih';

        // Set default period to this year if not provided
        if ($refperiode_id === null) {
            $currentYear = date('Y');
            $defaultPeriod = SakipPeriode::find()->where(['periode' => $currentYear])->one();
            $refperiode_id = $defaultPeriod ? $defaultPeriod->refperiode_id : null;
        }

        // Fetch all periods
        $periodeList = SakipPeriode::find()->all();

        // --- LOGIKA UTAMA PENGAMBILAN & PEMROSESAN DATA (SUDAH DIPERBAIKI) ---
        $laporanData = [];
        if ($refskpd_id && $refperiode_id) {

            // Query dan pembuatan Peta Triwulan tetap sama
            $allProgramTriwulan = SakipIndikatorcascadingprogramTriwulan::find()->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])->all();
            $allKegiatanTriwulan = SakipIndikatorcascadingkegiatanTriwulan::find()->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])->all();
            $allSubkegiatanTriwulan = SakipIndikatorcascadingsubkegiatanTriwulan::find()->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])->all();

            $programTriwulanMap = ArrayHelper::index($allProgramTriwulan, null, 'refcascadingprogram_id');
            $kegiatanTriwulanMap = ArrayHelper::index($allKegiatanTriwulan, null, 'refcascadingkegiatan_id');
            $subkegiatanTriwulanMap = ArrayHelper::index($allSubkegiatanTriwulan, null, 'refcascadingsubkegiatan_id');

            // Query utama tetap sama
            $cascadingPrograms = SakipCascadingprogram::find()
                ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
                ->with([
                    'refProgram', // Pastikan relasi 'refProgram' ada di model SakipCascadingprogram
                    'cascadingKegiatans.refKegiatan',
                    'cascadingKegiatans.cascadingSubkegiatans.refSubkegiatan'
                ])
                ->all();

            // [BARU] Array untuk menampung data yang sudah dikelompokkan
            $groupedData = [];

            foreach ($cascadingPrograms as $cascadingProgram) {
                // Lewati jika tidak ada relasi ke program induk
                if (!$cascadingProgram->refProgram) {
                    continue;
                }

                $programId = $cascadingProgram->refProgram->refprogram_id;

                // Inisialisasi data program jika belum ada
                if (!isset($groupedData[$programId])) {
                    $groupedData[$programId] = [
                        'kode_program' => $cascadingProgram->refProgram->kode_program ?? 'N/A',
                        'nama_program' => $cascadingProgram->refProgram->nama_program ?? 'N/A',
                        // Ambil indikator, satuan, target dari cascading program PERTAMA yang ditemukan
                        'uraian_indikator' => $cascadingProgram->uraian_indikatorprogram,
                        'satuan' => $cascadingProgram->program_satuan,
                        'target' => $cascadingProgram->program_target,
                        'kegiatans' => [],
                        // Inisialisasi data realisasi & anggaran
                        'realisasi' => [1 => null, 2 => null, 3 => null, 4 => null],
                        'triwulan_penyerapan_anggaran' => [1 => 0, 2 => 0, 3 => 0, 4 => 0],
                        'total_anggaran' => 0,
                        'total_realisasi' => 0,
                        'total_penyerapan' => 0,
                    ];
                }

                // Proses realisasi untuk program (jika ada beberapa cascading ke program yang sama)
                $programTriwulans = $programTriwulanMap[$cascadingProgram->refcascadingprogram_id] ?? [];
                foreach ($programTriwulans as $tw) {
                    // Anda bisa tentukan cara agregasi, misal: ambil yang terakhir atau jumlahkan
                    $groupedData[$programId]['realisasi'][$tw->reftriwulan_id] = $tw->triwulan_realisasi;
                }


                // Proses semua kegiatan di dalam cascading program ini
                foreach ($cascadingProgram->cascadingKegiatans as $kegiatan) {
                    $subkegiatanData = [];
                    foreach ($kegiatan->cascadingSubkegiatans as $subkegiatan) {
                        $subkegiatanTriwulans = $subkegiatanTriwulanMap[$subkegiatan->refcascadingsubkegiatan_id] ?? [];
                        $realisasiSubKeg = [1 => null, 2 => null, 3 => null, 4 => null];
                        $penyerapanSubKeg = [1 => 0, 2 => 0, 3 => 0, 4 => 0];
                        foreach ($subkegiatanTriwulans as $tw) {
                            $realisasiSubKeg[$tw->reftriwulan_id] = $tw->triwulan_realisasi;
                            $penyerapanSubKeg[$tw->reftriwulan_id] = (float) $tw->triwulan_penyerapan_anggaran; // Pastikan nama field benar
                        }

                        $subkegiatanData[] = [
                            'kode_subkegiatan' => $subkegiatan->refSubkegiatan->kode_subkegiatan ?? 'N/A',
                            'nama_subkegiatan' => $subkegiatan->refSubkegiatan->nama_subkegiatan ?? 'N/A',
                            'uraian_indikator' => $subkegiatan->uraian_indikatorsubkegiatan,
                            'satuan' => $subkegiatan->subkegiatan_satuan,
                            'target' => $subkegiatan->subkegiatan_target,
                            'total_anggaran' => (float) $subkegiatan->subkegiatan_anggaran,
                            'realisasi' => $realisasiSubKeg,
                            'triwulan_penyerapan_anggaran' => $penyerapanSubKeg,
                            'total_realisasi' => array_sum(array_filter($realisasiSubKeg, 'is_numeric')),
                            'total_penyerapan' => array_sum($penyerapanSubKeg),
                        ];
                    }

                    $totalAnggaranKegiatan = array_sum(ArrayHelper::getColumn($subkegiatanData, 'total_anggaran'));
                    $totalPenyerapanKegiatan = array_sum(ArrayHelper::getColumn($subkegiatanData, 'total_penyerapan'));

                    $kegiatanTriwulans = $kegiatanTriwulanMap[$kegiatan->refcascadingkegiatan_id] ?? [];
                    $realisasiKegiatan = [1 => null, 2 => null, 3 => null, 4 => null];
                    foreach ($kegiatanTriwulans as $tw) {
                        $realisasiKegiatan[$tw->reftriwulan_id] = $tw->triwulan_realisasi;
                    }

                    // Tambahkan kegiatan ke dalam program yang benar
                    $groupedData[$programId]['kegiatans'][] = [
                        'kode_kegiatan' => $kegiatan->refKegiatan->kode_kegiatan ?? 'N/A',
                        'nama_kegiatan' => $kegiatan->refKegiatan->nama_kegiatan ?? 'N/A',
                        'uraian_indikator' => $kegiatan->uraian_indikatorkegiatan,
                        'satuan' => $kegiatan->kegiatan_satuan,
                        'target' => $kegiatan->kegiatan_target,
                        'total_anggaran' => $totalAnggaranKegiatan,
                        'subkegiatans' => $subkegiatanData,
                        'realisasi' => $realisasiKegiatan,
                        'triwulan_penyerapan_anggaran' => [
                            1 => array_sum(ArrayHelper::getColumn($subkegiatanData, ['triwulan_penyerapan_anggaran', 1])),
                            2 => array_sum(ArrayHelper::getColumn($subkegiatanData, ['triwulan_penyerapan_anggaran', 2])),
                            3 => array_sum(ArrayHelper::getColumn($subkegiatanData, ['triwulan_penyerapan_anggaran', 3])),
                            4 => array_sum(ArrayHelper::getColumn($subkegiatanData, ['triwulan_penyerapan_anggaran', 4])),
                        ],
                        'total_realisasi' => array_sum(array_filter($realisasiKegiatan, 'is_numeric')),
                        'total_penyerapan' => $totalPenyerapanKegiatan,
                    ];
                }
            }

            // [BARU] Loop terakhir untuk mengagregasi total anggaran dan penyerapan ke level program
            foreach ($groupedData as $programId => &$programData) { // Gunakan reference (&) untuk mengubah langsung
                $totalAnggaranProgram = array_sum(ArrayHelper::getColumn($programData['kegiatans'], 'total_anggaran'));
                $totalPenyerapanProgram = array_sum(ArrayHelper::getColumn($programData['kegiatans'], 'total_penyerapan'));

                $programData['total_anggaran'] = $totalAnggaranProgram;
                $programData['total_penyerapan'] = $totalPenyerapanProgram;

                // Akumulasi penyerapan anggaran per triwulan dari kegiatan-kegiatannya
                $programData['triwulan_penyerapan_anggaran'] = [
                    1 => array_sum(ArrayHelper::getColumn($programData['kegiatans'], ['triwulan_penyerapan_anggaran', 1])),
                    2 => array_sum(ArrayHelper::getColumn($programData['kegiatans'], ['triwulan_penyerapan_anggaran', 2])),
                    3 => array_sum(ArrayHelper::getColumn($programData['kegiatans'], ['triwulan_penyerapan_anggaran', 3])),
                    4 => array_sum(ArrayHelper::getColumn($programData['kegiatans'], ['triwulan_penyerapan_anggaran', 4])),
                ];

                // Agregasi total realisasi dari semua realisasi triwulan program
                $programData['total_realisasi'] = array_sum(array_filter($programData['realisasi'], 'is_numeric'));
            }
            unset($programData); // Hapus reference setelah loop selesai

            // Kirim data yang sudah dikelompokkan ke view
            $laporanData = array_values($groupedData); // Re-index array agar menjadi non-asosiatif
        }

        // Retrieve the periode based on refperiode_id
        $selectedPeriod = SakipPeriode::find()->where(['refperiode_id' => $refperiode_id])->one();
        $selectedPeriodValue = $selectedPeriod ? $selectedPeriod->periode : null; // Get the periode value

        // Set the dynamic title
        $this->view->title = "Laporan Renstra $selectedPeriodValue - " . Html::encode($nama_skpd);

        // --- MULAI PERUBAHAN DARI SINI ---

        // 2. Buat objek Spreadsheet baru
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Laporan Evaluasi RKPD');

        // 3. Tulis Judul Laporan
        $sheet->mergeCells('A1:P1')->setCellValue('A1', 'LAPORAN EVALUASI HASIL RKPD TERHADAP RPJMD');
        $sheet->mergeCells('A2:P2')->setCellValue('A2', strtoupper($nama_skpd));
        $sheet->mergeCells('A3:P3')->setCellValue('A3', 'PERIODE ' . $selectedPeriodValue);
        $sheet->getStyle('A1:A3')->getFont()->setBold(true);
        $sheet->getStyle('A1:A3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // 4. Tulis Header Tabel yang Kompleks
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrapText' => true],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF4F81BD']]
        ];

        $sheet->mergeCells('A5:A7')->setCellValue('A5', 'Kode');
        $sheet->mergeCells('B5:B7')->setCellValue('B5', 'Program / Kegiatan / Sub Kegiatan');
        $sheet->mergeCells('C5:C7')->setCellValue('C5', 'Indikator');
        $sheet->mergeCells('D5:D7')->setCellValue('D5', 'Satuan');
        $sheet->mergeCells('E5:E7')->setCellValue('E5', 'Target');
        $sheet->mergeCells('F5:F7')->setCellValue('F5', 'Anggaran');

        $sheet->mergeCells('G5:N5')->setCellValue('G5', 'Realisasi Kinerja Pada Triwulan');
        $sheet->mergeCells('G6:H6')->setCellValue('G6', 'I');
        $sheet->mergeCells('I6:J6')->setCellValue('I6', 'II');
        $sheet->mergeCells('K6:L6')->setCellValue('K6', 'III');
        $sheet->mergeCells('M6:N6')->setCellValue('M6', 'IV');

        $sheet->setCellValue('G7', 'Realisasi')->setCellValue('H7', 'Penyerapan Anggaran');
        $sheet->setCellValue('I7', 'Realisasi')->setCellValue('J7', 'Penyerapan Anggaran');
        $sheet->setCellValue('K7', 'Realisasi')->setCellValue('L7', 'Penyerapan Anggaran');
        $sheet->setCellValue('M7', 'Realisasi')->setCellValue('N7', 'Penyerapan Anggaran');

        // [BARU] Header untuk Total
        $sheet->mergeCells('O5:P6')->setCellValue('O5', 'Total Realisasi s/d Triwulan IV');
        $sheet->setCellValue('O7', 'Total Realisasi');
        $sheet->setCellValue('P7', 'Total Penyerapan Anggaran');

        // Terapkan style ke semua header
        $sheet->getStyle('A5:P7')->applyFromArray($headerStyle);

        // 5. Loop data dan tulis ke dalam baris
        $row = 8; // Mulai dari baris ke-8
        foreach ($laporanData as $program) {
            $sheet->fromArray([
                $program['kode_program'],
                $program['nama_program'],
                $program['uraian_indikator'],
                $program['satuan'],
                $program['target'],
                $program['total_anggaran'],
                $program['realisasi'][1],
                $program['triwulan_penyerapan_anggaran'][1],
                $program['realisasi'][2],
                $program['triwulan_penyerapan_anggaran'][2],
                $program['realisasi'][3],
                $program['triwulan_penyerapan_anggaran'][3],
                $program['realisasi'][4],
                $program['triwulan_penyerapan_anggaran'][4],
                $program['total_realisasi'],
                $program['total_penyerapan'] // <-- TAMBAHKAN DATA TOTAL
            ], null, 'A' . $row);
            $sheet->getStyle('A' . $row . ':P' . $row)->getFont()->setBold(true);
            $row++;

            foreach ($program['kegiatans'] as $kegiatan) {
                $sheet->fromArray([
                    $kegiatan['kode_kegiatan'],
                    '  ' . $kegiatan['nama_kegiatan'],
                    $kegiatan['uraian_indikator'],
                    $kegiatan['satuan'],
                    $kegiatan['target'],
                    $kegiatan['total_anggaran'],
                    $kegiatan['realisasi'][1],
                    $kegiatan['triwulan_penyerapan_anggaran'][1],
                    $kegiatan['realisasi'][2],
                    $kegiatan['triwulan_penyerapan_anggaran'][2],
                    $kegiatan['realisasi'][3],
                    $kegiatan['triwulan_penyerapan_anggaran'][3],
                    $kegiatan['realisasi'][4],
                    $kegiatan['triwulan_penyerapan_anggaran'][4],
                    $kegiatan['total_realisasi'],
                    $kegiatan['total_penyerapan'] // <-- TAMBAHKAN DATA TOTAL
                ], null, 'A' . $row);
                $sheet->getStyle('B' . $row)->getAlignment()->setIndent(1);
                $row++;

                foreach ($kegiatan['subkegiatans'] as $subkegiatan) {
                    $sheet->fromArray([
                        $subkegiatan['kode_subkegiatan'],
                        '    ' . $subkegiatan['nama_subkegiatan'],
                        $subkegiatan['uraian_indikator'],
                        $subkegiatan['satuan'],
                        $subkegiatan['target'],
                        $subkegiatan['total_anggaran'],
                        $subkegiatan['realisasi'][1],
                        $subkegiatan['triwulan_penyerapan_anggaran'][1],
                        $subkegiatan['realisasi'][2],
                        $subkegiatan['triwulan_penyerapan_anggaran'][2],
                        $subkegiatan['realisasi'][3],
                        $subkegiatan['triwulan_penyerapan_anggaran'][3],
                        $subkegiatan['realisasi'][4],
                        $subkegiatan['triwulan_penyerapan_anggaran'][4],
                        $subkegiatan['total_realisasi'],
                        $subkegiatan['total_penyerapan'] // <-- TAMBAHKAN DATA TOTAL
                    ], null, 'A' . $row);
                    $sheet->getStyle('B' . $row)->getAlignment()->setIndent(2);
                    $row++;
                }
            }
        }

        // Format Angka
        $numberFormat = '#,##0';
        $sheet->getStyle('F8:F' . $row)->getNumberFormat()->setFormatCode($numberFormat);
        $sheet->getStyle('H8:H' . $row)->getNumberFormat()->setFormatCode($numberFormat);
        $sheet->getStyle('J8:J' . $row)->getNumberFormat()->setFormatCode($numberFormat);
        $sheet->getStyle('L8:L' . $row)->getNumberFormat()->setFormatCode($numberFormat);
        $sheet->getStyle('N8:N' . $row)->getNumberFormat()->setFormatCode($numberFormat);
        $sheet->getStyle('P8:P' . $row)->getNumberFormat()->setFormatCode($numberFormat); // <-- FORMAT KOLOM TOTAL PENYERAPAN

        // Atur lebar kolom otomatis
        foreach (range('A', 'P') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        // Siapkan file untuk di-download
        $fileName = "Laporan_Evaluasi_RKPD_{$nama_skpd}_{$selectedPeriodValue}.xlsx";
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . urlencode($fileName) . '"');
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit();
    }

    public function actionIndexLaporanRenstra($refperiode_id = null)
    {
        // Get refskpd_id from the current user
        $user = Yii::$app->user->identity;
        $refskpd_id = $user->refskpd_id;

        // Get the name of the SKPD based on refskpd_id
        $nama_skpd = SakipSkpd::find()->select('nama_skpd')->where(['refskpd_id' => $refskpd_id])->scalar();

        // Set default period to this year if not provided
        if ($refperiode_id === null) {
            $currentYear = date('Y');
            $defaultPeriod = SakipPeriode::find()->where(['periode' => $currentYear])->one();
            $refperiode_id = $defaultPeriod ? $defaultPeriod->refperiode_id : null;
        }

        // Fetch all periods
        $periodeList = SakipPeriode::find()->all();

        // Fetch data based on refskpd_id and refperiode_id
        $sasaranRenstra = SakipSasaranrenstra::find()
            ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
            ->with(['refVisi', 'refMisi', 'refTujuan', 'refSasaran'])
            ->all();

        // Fetch strategi based on refskpd_id and refperiode_id
        $strategiList = SakipStrategi::find()
            ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
            ->all();

        // Fetch kebijakan based on refskpd_id and refperiode_id
        $kebijakanList = SakipKebijakan::find()
            ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
            ->all();

        // Retrieve the periode based on refperiode_id
        $selectedPeriod = SakipPeriode::find()->where(['refperiode_id' => $refperiode_id])->one();
        $selectedPeriodValue = $selectedPeriod ? $selectedPeriod->periode : null; // Get the periode value

        // Set the dynamic title
        $this->view->title = "Laporan Renstra $selectedPeriodValue - " . Html::encode($nama_skpd);

        return $this->render('index-laporan-renstra', [
            'periodeList' => $periodeList,
            'selectedPeriodId' => $refperiode_id,
            'refskpd_id' => $refskpd_id,
            'refperiode_id' => $refperiode_id,
            'sasaranRenstra' => $sasaranRenstra,
            'strategiList' => $strategiList,
            'kebijakanList' => $kebijakanList,
            'nama_skpd' => $nama_skpd,
            'selectedPeriodValue' => $selectedPeriodValue, // Include selected period value
        ]);
    }

    // public function actionIndexLaporanRenstraDev($refperiode_id = null, $refskpd_id = null)
    // {
    //     // Ambil refskpd_id dari user saat ini jika tidak ada di request
    //     if ($refskpd_id === null) {
    //         $user = Yii::$app->user->identity;
    //         $refskpd_id = $user->refskpd_id;
    //     }


    //     // Get the name of the SKPD based on refskpd_id
    //     $nama_skpd = SakipSkpd::find()->select('nama_skpd')->where(['refskpd_id' => $refskpd_id])->scalar();

    //     // Set default period to this year if not provided
    //     if ($refperiode_id === null) {
    //         $currentYear = date('Y');
    //         $defaultPeriod = SakipPeriode::find()->where(['periode' => $currentYear])->one();
    //         $refperiode_id = $defaultPeriod ? $defaultPeriod->refperiode_id : null;
    //     }

    //     // Fetch all periods
    //     $periodeList = SakipPeriode::find()->all();

    //     // Fetch data based on refskpd_id and refperiode_id
    //     $sasaranRenstra = SakipSasaranrenstra::find()
    //         ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
    //         ->with(['refVisi', 'refMisi', 'refTujuan', 'refSasaran'])
    //         ->all();

    //     // Fetch strategi based on refskpd_id and refperiode_id
    //     $strategiList = SakipStrategi::find()
    //         ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
    //         ->all();

    //     // Fetch kebijakan based on refskpd_id and refperiode_id
    //     $kebijakanList = SakipKebijakan::find()
    //         ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
    //         ->all();

    //     // Ambil daftar SKPD untuk dropdown
    //     $skpdList = ArrayHelper::map(SakipSkpd::find()->all(), 'refskpd_id', 'nama_skpd');


    //     // Retrieve the periode based on refperiode_id
    //     $selectedPeriod = SakipPeriode::find()->where(['refperiode_id' => $refperiode_id])->one();
    //     $selectedPeriodValue = $selectedPeriod ? $selectedPeriod->periode : null; // Get the periode value

    //     // Set the dynamic title
    //     $this->view->title = "Laporan Renstra $selectedPeriodValue - " . Html::encode($nama_skpd);

    //     return $this->render('index-laporan-renstra-dev', [
    //         'periodeList' => $periodeList,
    //         'selectedPeriodId' => $refperiode_id,
    //         'refskpd_id' => $refskpd_id,
    //         'refperiode_id' => $refperiode_id,
    //         'sasaranRenstra' => $sasaranRenstra,
    //         'strategiList' => $strategiList,
    //         'kebijakanList' => $kebijakanList,
    //         'nama_skpd' => $nama_skpd,
    //         'selectedPeriodValue' => $selectedPeriodValue, // Include selected period value
    //         'selectedSkpdId' => $refskpd_id,
    //         'skpdList' => $skpdList,
    //     ]);
    // }

    public function actionIndexLaporanRenstraDev($refperiode_id = null, $refskpd_id = null)
    {
        // =========================================================================
        // BLOK LOGIKA BARU UNTUK KEAMANAN SKPD
        // =========================================================================

        $user = Yii::$app->user->identity;
        $assignments = Yii::$app->authManager->getAssignments($user->id);

        $skpdList = [];
        $allowedSkpdIds = []; // Daftar ID SKPD yang diizinkan untuk user ini

        if (isset($assignments['superadmin']) || isset($assignments['admin'])) {
            // --- BLOK UNTUK ADMIN ---
            $allSkpd = SakipSkpd::find()->where(['skpd_isaktif' => 'T'])->orderBy('nama_skpd ASC')->all();
            $skpdList = ArrayHelper::map($allSkpd, 'refskpd_id', 'nama_skpd');
            $allowedSkpdIds = array_keys($skpdList);
        } else {
            // --- BLOK UNTUK NON-ADMIN (KOORDINATOR) ---
            $coordinatedSkpdIds = SakipKoordinasi::find()
                ->select('refskpd_id')
                ->where(['refuser_id' => $user->id])
                ->column();

            $allowedSkpdIds = $coordinatedSkpdIds;

            if (!empty($allowedSkpdIds)) {
                $skpdList = ArrayHelper::map(
                    SakipSkpd::find()->where(['refskpd_id' => $allowedSkpdIds, 'skpd_isaktif' => 'T'])->orderBy('nama_skpd ASC')->all(),
                    'refskpd_id',
                    'nama_skpd'
                );
            }
        }

        // --- Validasi Keamanan ---
        // Cek apakah refskpd_id dari URL diizinkan untuk diakses
        if ($refskpd_id !== null && !in_array($refskpd_id, $allowedSkpdIds)) {
            throw new ForbiddenHttpException('Anda tidak memiliki hak akses untuk melihat data SKPD ini.');
        }

        // Tentukan refskpd_id yang akan digunakan jika tidak ada di URL atau tidak valid
        if ($refskpd_id === null || !in_array($refskpd_id, $allowedSkpdIds)) {
            $refskpd_id = !empty($allowedSkpdIds) ? $allowedSkpdIds[0] : null;
        }

        // =========================================================================
        // AKHIR DARI BLOK LOGIKA BARU
        // Kode di bawah ini sekarang menggunakan $refskpd_id yang sudah aman
        // =========================================================================

        // Get the name of the SKPD based on refskpd_id
        $nama_skpd = $refskpd_id ? SakipSkpd::find()->select('nama_skpd')->where(['refskpd_id' => $refskpd_id])->scalar() : 'Tidak ada SKPD dipilih';

        // Set default period to this year if not provided
        if ($refperiode_id === null) {
            $currentYear = date('Y');
            $defaultPeriod = SakipPeriode::find()->where(['periode' => $currentYear])->one();
            $refperiode_id = $defaultPeriod ? $defaultPeriod->refperiode_id : null;
        }

        // Fetch all periods
        $periodeList = SakipPeriode::find()->all();

        // Logika query utama Anda tidak diubah
        $sasaranRenstra = [];
        $strategiList = [];
        $kebijakanList = [];
        if ($refskpd_id && $refperiode_id) {
            $sasaranRenstra = SakipSasaranrenstra::find()
                ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
                ->with(['refVisi', 'refMisi', 'refTujuan', 'refSasaran'])
                ->all();

            $strategiList = SakipStrategi::find()
                ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
                ->all();

            $kebijakanList = SakipKebijakan::find()
                ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
                ->all();
        }

        // Retrieve the periode based on refperiode_id
        $selectedPeriod = SakipPeriode::find()->where(['refperiode_id' => $refperiode_id])->one();
        $selectedPeriodValue = $selectedPeriod ? $selectedPeriod->periode : null; // Get the periode value

        // Set the dynamic title
        $this->view->title = "Laporan Renstra $selectedPeriodValue - " . Html::encode($nama_skpd);

        return $this->render('index-laporan-renstra-dev', [
            'periodeList' => $periodeList,
            'selectedPeriodId' => $refperiode_id,
            'refskpd_id' => $refskpd_id,
            'refperiode_id' => $refperiode_id,
            'sasaranRenstra' => $sasaranRenstra,
            'strategiList' => $strategiList,
            'kebijakanList' => $kebijakanList,
            'nama_skpd' => $nama_skpd,
            'selectedPeriodValue' => $selectedPeriodValue, // Include selected period value
            'selectedSkpdId' => $refskpd_id,
            'skpdList' => $skpdList,
        ]);
    }

    public function actionDownloadPdf()
    {
        if (Yii::$app->request->isPost) {
            $content = Yii::$app->request->post('content'); // Konten dari POST
            $title = Yii::$app->request->post('title', 'Laporan'); // Judul untuk nama file

            // Define CSS untuk styling PDF
            $css = <<<CSS
            .tbdata {
                border-collapse: collapse;
                font-family: 'Public Sans', sans-serif;
                font-size: 11px;
            }
            .tbdata th {
                height: 40px;
                background: rgb(0, 160, 221);
                text-align: center;
                border: 1px solid #cfcfcf;
                color: #ffffff;
                padding: 2px;
            }
            .title {
        border-collapse: collapse;
        font-family: 'Public Sans', sans-serif;
        /* Menggunakan font Public Sans */
        font-size: 11px;
        text-align: center;
    }
            .tbdata td {
                padding: 2px;
                vertical-align: top;
            }
            .tebal {
                font-weight: bold;
            }
            .tblRenstra {
                width: 100%;
        font-family: 'Public Sans', sans-serif;
                border-collapse: collapse;
                font-size: 11px;
            }
            .tblRenstra td {
                padding: 2px;
                border: 1px solid #f2f2f2;
            }
            .tblRenstra .header {
                text-align: center;
                vertical-align: middle;
                font-weight: bold;
                background: #03b0e2;
                color: #ffffff;
            }
            .trO {
                background: #f2f2f2;
            }
            .trE {
                background: white;
            }
            .tblAtas {
                margin-left: 30px;
        font-family: 'Public Sans', sans-serif;
                border-collapse: collapse;
                font-size: 11px;
            }
            thead {
                display: table-header-group;
            }
CSS;

            // Setting untuk file PDF dengan CSS
            $pdf = new Pdf([
                'mode' => Pdf::MODE_CORE,
                'format' => Pdf::FORMAT_A4,
                'orientation' => Pdf::ORIENT_PORTRAIT,
                'destination' => Pdf::DEST_DOWNLOAD,
                'filename' => $title . '.pdf',
                'content' => $content, // Konten dari POST
                'cssInline' => $css, // Gunakan CSS inline
                'options' => ['title' => $title],
                'methods' => [
                    'SetHeader' => [$title],
                    'SetFooter' => ['{PAGENO}'],
                ],
            ]);

            return $pdf->render(); // Generate dan kembalikan PDF untuk diunduh
        }
    }

    // Renja Tahunan
    public function actionIndexLaporanRenjaTahunan($refperiode_id = null)
    {
        // Get refskpd_id from the current user
        $user = Yii::$app->user->identity;
        $refskpd_id = $user->refskpd_id;

        // Get the name of the SKPD based on refskpd_id
        $nama_skpd = SakipSkpd::find()->select('nama_skpd')->where(['refskpd_id' => $refskpd_id])->scalar();

        // Set default period to this year if not provided
        if ($refperiode_id === null) {
            $currentYear = date('Y');
            $defaultPeriod = SakipPeriode::find()->where(['periode' => $currentYear])->one();
            $refperiode_id = $defaultPeriod ? $defaultPeriod->refperiode_id : null;
        }

        // Fetch all periods
        $periodeList = SakipPeriode::find()->all();

        // Fetch data based on refskpd_id and refperiode_id
        $sasaranRenstra = SakipSasaranrenstra::find()
            ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
            ->with(['refVisi', 'refMisi', 'refTujuan', 'refSasaran'])
            ->all();

        // Retrieve the periode based on refperiode_id
        $selectedPeriod = SakipPeriode::find()->where(['refperiode_id' => $refperiode_id])->one();
        $selectedPeriodValue = $selectedPeriod ? $selectedPeriod->periode : null; // Get the periode value

        // Set the dynamic title
        $this->view->title = "Laporan Rencana Kinerja Tahun $selectedPeriodValue - " . Html::encode($nama_skpd);

        return $this->render('index-laporan-renja-tahunan', [
            'periodeList' => $periodeList,
            'selectedPeriodId' => $refperiode_id,
            'refskpd_id' => $refskpd_id,
            'refperiode_id' => $refperiode_id,
            'sasaranRenstra' => $sasaranRenstra,
            'nama_skpd' => $nama_skpd,
            'selectedPeriodValue' => $selectedPeriodValue, // Include selected period value
        ]);
    }

    // public function actionIndexLaporanRenjaTahunanDev($refperiode_id = null, $refskpd_id = null)
    // {
    //     // Ambil refskpd_id dari user saat ini jika tidak ada di request
    //     if ($refskpd_id === null) {
    //         $user = Yii::$app->user->identity;
    //         $refskpd_id = $user->refskpd_id;
    //     }

    //     // Get the name of the SKPD based on refskpd_id
    //     $nama_skpd = SakipSkpd::find()->select('nama_skpd')->where(['refskpd_id' => $refskpd_id])->scalar();

    //     // Set default period to this year if not provided
    //     if ($refperiode_id === null) {
    //         $currentYear = date('Y');
    //         $defaultPeriod = SakipPeriode::find()->where(['periode' => $currentYear])->one();
    //         $refperiode_id = $defaultPeriod ? $defaultPeriod->refperiode_id : null;
    //     }

    //     // Fetch all periods
    //     $periodeList = SakipPeriode::find()->all();

    //     // Fetch data based on refskpd_id and refperiode_id
    //     $sasaranRenstra = SakipSasaranrenstra::find()
    //         ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
    //         ->with(['refVisi', 'refMisi', 'refTujuan', 'refSasaran'])
    //         ->all();

    //     // Ambil daftar SKPD untuk dropdown
    //     $skpdList = ArrayHelper::map(SakipSkpd::find()->all(), 'refskpd_id', 'nama_skpd');

    //     // Retrieve the periode based on refperiode_id
    //     $selectedPeriod = SakipPeriode::find()->where(['refperiode_id' => $refperiode_id])->one();
    //     $selectedPeriodValue = $selectedPeriod ? $selectedPeriod->periode : null; // Get the periode value

    //     // Set the dynamic title
    //     $this->view->title = "Laporan Rencana Kinerja Tahun $selectedPeriodValue - " . Html::encode($nama_skpd);

    //     return $this->render('index-laporan-renja-tahunan-dev', [
    //         'periodeList' => $periodeList,
    //         'selectedPeriodId' => $refperiode_id,
    //         'refskpd_id' => $refskpd_id,
    //         'refperiode_id' => $refperiode_id,
    //         'sasaranRenstra' => $sasaranRenstra,
    //         'nama_skpd' => $nama_skpd,
    //         'selectedPeriodValue' => $selectedPeriodValue, // Include selected period value
    //         'selectedSkpdId' => $refskpd_id,
    //         'skpdList' => $skpdList,
    //     ]);
    // }

    public function actionIndexLaporanRenjaTahunanDev($refperiode_id = null, $refskpd_id = null)
    {
        // =========================================================================
        // BLOK LOGIKA BARU UNTUK KEAMANAN SKPD
        // =========================================================================

        $user = Yii::$app->user->identity;
        $assignments = Yii::$app->authManager->getAssignments($user->id);

        $skpdList = [];
        $allowedSkpdIds = []; // Daftar ID SKPD yang diizinkan untuk user ini

        if (isset($assignments['superadmin']) || isset($assignments['admin'])) {
            // --- BLOK UNTUK ADMIN ---
            $allSkpd = SakipSkpd::find()->where(['skpd_isaktif' => 'T'])->orderBy('nama_skpd ASC')->all();
            $skpdList = ArrayHelper::map($allSkpd, 'refskpd_id', 'nama_skpd');
            $allowedSkpdIds = array_keys($skpdList);
        } else {
            // --- BLOK UNTUK NON-ADMIN (KOORDINATOR) ---
            $coordinatedSkpdIds = SakipKoordinasi::find()
                ->select('refskpd_id')
                ->where(['refuser_id' => $user->id])
                ->column();

            $allowedSkpdIds = $coordinatedSkpdIds;

            if (!empty($allowedSkpdIds)) {
                $skpdList = ArrayHelper::map(
                    SakipSkpd::find()->where(['refskpd_id' => $allowedSkpdIds, 'skpd_isaktif' => 'T'])->orderBy('nama_skpd ASC')->all(),
                    'refskpd_id',
                    'nama_skpd'
                );
            }
        }

        // --- Validasi Keamanan ---
        // Cek apakah refskpd_id dari URL diizinkan untuk diakses
        if ($refskpd_id !== null && !in_array($refskpd_id, $allowedSkpdIds)) {
            throw new ForbiddenHttpException('Anda tidak memiliki hak akses untuk melihat data SKPD ini.');
        }

        // Tentukan refskpd_id yang akan digunakan jika tidak ada di URL atau tidak valid
        if ($refskpd_id === null || !in_array($refskpd_id, $allowedSkpdIds)) {
            $refskpd_id = !empty($allowedSkpdIds) ? $allowedSkpdIds[0] : null;
        }

        // =========================================================================
        // AKHIR DARI BLOK LOGIKA BARU
        // Kode di bawah ini sekarang menggunakan $refskpd_id yang sudah aman
        // =========================================================================

        // Get the name of the SKPD based on refskpd_id
        $nama_skpd = $refskpd_id ? SakipSkpd::find()->select('nama_skpd')->where(['refskpd_id' => $refskpd_id])->scalar() : 'Tidak ada SKPD dipilih';

        // Set default period to this year if not provided
        if ($refperiode_id === null) {
            $currentYear = date('Y');
            $defaultPeriod = SakipPeriode::find()->where(['periode' => $currentYear])->one();
            $refperiode_id = $defaultPeriod ? $defaultPeriod->refperiode_id : null;
        }

        // Fetch all periods
        $periodeList = SakipPeriode::find()->all();

        // Logika query utama Anda tidak diubah
        $sasaranRenstra = [];
        if ($refskpd_id && $refperiode_id) {
            $sasaranRenstra = SakipSasaranrenstra::find()
                ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
                ->with(['refVisi', 'refMisi', 'refTujuan', 'refSasaran'])
                ->all();
        }

        // Retrieve the periode based on refperiode_id
        $selectedPeriod = SakipPeriode::find()->where(['refperiode_id' => $refperiode_id])->one();
        $selectedPeriodValue = $selectedPeriod ? $selectedPeriod->periode : null; // Get the periode value

        // Set the dynamic title
        $this->view->title = "Laporan Rencana Kinerja Tahun $selectedPeriodValue - " . Html::encode($nama_skpd);

        return $this->render('index-laporan-renja-tahunan-dev', [
            'periodeList' => $periodeList,
            'selectedPeriodId' => $refperiode_id,
            'refskpd_id' => $refskpd_id,
            'refperiode_id' => $refperiode_id,
            'sasaranRenstra' => $sasaranRenstra,
            'nama_skpd' => $nama_skpd,
            'selectedPeriodValue' => $selectedPeriodValue, // Include selected period value
            'selectedSkpdId' => $refskpd_id,
            'skpdList' => $skpdList,
        ]);
    }

    public function actionDownloadPdfRenja()
    {
        if (Yii::$app->request->isPost) {
            $content = Yii::$app->request->post('content'); // Konten dari POST
            $title = Yii::$app->request->post('title', 'Laporan'); // Judul untuk nama file

            // Define CSS untuk styling PDF
            $css = <<<CSS
            .tbdata {
                border-collapse: collapse;
                font-family: 'Public Sans', sans-serif;
                font-size: 11px;
            }
            .tbdata th {
                height: 40px;
                background: rgb(0, 160, 221);
                text-align: center;
                border: 1px solid #cfcfcf;
                color: #ffffff;
                padding: 2px;
            }
            .title {
        border-collapse: collapse;
        font-family: 'Public Sans', sans-serif;
        /* Menggunakan font Public Sans */
        font-size: 11px;
        text-align: center;
    }
            .tbdata td {
                padding: 2px;
                vertical-align: top;
            }
            .tebal {
                font-weight: bold;
            }
            .tblRenstra {
                width: 100%;
        font-family: 'Public Sans', sans-serif;
                border-collapse: collapse;
                font-size: 11px;
            }
            .tblRenstra td {
                padding: 2px;
                border: 1px solid #f2f2f2;
            }
            .tblRenstra .header {
                text-align: center;
                vertical-align: middle;
                font-weight: bold;
                background: #03b0e2;
                color: #ffffff;
            }
            .trO {
                background: #f2f2f2;
            }
            .trE {
                background: white;
            }
            .tblAtas {
                margin-left: 30px;
        font-family: 'Public Sans', sans-serif;
                border-collapse: collapse;
                font-size: 11px;
            }
            thead {
                display: table-header-group;
            }
CSS;

            // Setting untuk file PDF dengan CSS
            $pdf = new Pdf([
                'mode' => Pdf::MODE_CORE,
                'format' => Pdf::FORMAT_A4,
                'orientation' => Pdf::ORIENT_PORTRAIT,
                'destination' => Pdf::DEST_DOWNLOAD,
                'filename' => $title . '.pdf',
                'content' => $content, // Konten dari POST
                'cssInline' => $css, // Gunakan CSS inline
                'options' => ['title' => $title],
                'methods' => [
                    'SetHeader' => [$title],
                    'SetFooter' => ['{PAGENO}'],
                ],
            ]);

            return $pdf->render(); // Generate dan kembalikan PDF untuk diunduh
        }
    }

    // Laporan IKU
    public function actionIndexLaporanIku($refperiode_id = null)
    {
        // Get refskpd_id from the current user
        $user = Yii::$app->user->identity;
        $refskpd_id = $user->refskpd_id;

        // Get the name of the SKPD based on refskpd_id
        $nama_skpd = SakipSkpd::find()->select('nama_skpd')->where(['refskpd_id' => $refskpd_id])->scalar();

        // Set default period to this year if not provided
        if ($refperiode_id === null) {
            $currentYear = date('Y');
            $defaultPeriod = SakipPeriode::find()->where(['periode' => $currentYear])->one();
            $refperiode_id = $defaultPeriod ? $defaultPeriod->refperiode_id : null;
        }

        // Fetch all periods
        $periodeList = SakipPeriode::find()->all();

        // Fetch indicators based on refskpd_id and refperiode_id
        $indikators = SakipIndikatorsasaranrenstra::find()
            ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
            ->all();

        // Prepare the sasaranRenstra with their formulations
        $sasaranRenstra = [];
        foreach ($indikators as $indikator) {
            $sasaran = SakipSasaranrenstra::find()
                ->where(['refsasaranrenstra_id' => $indikator->refsasaranrenstra_id])
                ->one();

            if ($sasaran) {
                $sasaranRenstra[] = [
                    'indikator' => $indikator,
                    'formulasi' => $sasaran->formulasi_sasaranrenstra,
                ];
            }
        }

        // Retrieve the periode based on refperiode_id
        $selectedPeriod = SakipPeriode::find()->where(['refperiode_id' => $refperiode_id])->one();
        $selectedPeriodValue = $selectedPeriod ? $selectedPeriod->periode : null; // Get the periode value

        // Set the dynamic title
        $this->view->title = "Laporan Indikator Kinerja Utama $selectedPeriodValue - " . Html::encode($nama_skpd);

        return $this->render('index-laporan-iku', [
            'periodeList' => $periodeList,
            'selectedPeriodId' => $refperiode_id,
            'refskpd_id' => $refskpd_id,
            'refperiode_id' => $refperiode_id,
            'sasaranRenstra' => $sasaranRenstra,
            'nama_skpd' => $nama_skpd,
            'selectedPeriodValue' => $selectedPeriodValue, // Include selected period value
        ]);
    }

    // public function actionIndexLaporanIkuDev($refperiode_id = null, $refskpd_id = null)
    // {
    //     // Ambil refskpd_id dari user saat ini jika tidak ada di request
    //     if ($refskpd_id === null) {
    //         $user = Yii::$app->user->identity;
    //         $refskpd_id = $user->refskpd_id;
    //     }


    //     // Get the name of the SKPD based on refskpd_id
    //     $nama_skpd = SakipSkpd::find()->select('nama_skpd')->where(['refskpd_id' => $refskpd_id])->scalar();

    //     // Set default period to this year if not provided
    //     if ($refperiode_id === null) {
    //         $currentYear = date('Y');
    //         $defaultPeriod = SakipPeriode::find()->where(['periode' => $currentYear])->one();
    //         $refperiode_id = $defaultPeriod ? $defaultPeriod->refperiode_id : null;
    //     }

    //     // Fetch all periods
    //     $periodeList = SakipPeriode::find()->all();

    //     // Fetch indicators based on refskpd_id and refperiode_id
    //     $indikators = SakipIndikatorsasaranrenstra::find()
    //         ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
    //         ->all();

    //     // Prepare the sasaranRenstra with their formulations
    //     $sasaranRenstra = [];
    //     foreach ($indikators as $indikator) {
    //         $sasaran = SakipSasaranrenstra::find()
    //             ->where(['refsasaranrenstra_id' => $indikator->refsasaranrenstra_id])
    //             ->one();

    //         if ($sasaran) {
    //             $sasaranRenstra[] = [
    //                 'indikator' => $indikator,
    //                 'formulasi' => $sasaran->formulasi_sasaranrenstra,
    //             ];
    //         }
    //     }

    //     // Ambil daftar SKPD untuk dropdown
    //     $skpdList = ArrayHelper::map(SakipSkpd::find()->all(), 'refskpd_id', 'nama_skpd');

    //     // Retrieve the periode based on refperiode_id
    //     $selectedPeriod = SakipPeriode::find()->where(['refperiode_id' => $refperiode_id])->one();
    //     $selectedPeriodValue = $selectedPeriod ? $selectedPeriod->periode : null; // Get the periode value

    //     // Set the dynamic title
    //     $this->view->title = "Laporan Indikator Kinerja Utama $selectedPeriodValue - " . Html::encode($nama_skpd);

    //     return $this->render('index-laporan-iku-dev', [
    //         'periodeList' => $periodeList,
    //         'selectedPeriodId' => $refperiode_id,
    //         'refskpd_id' => $refskpd_id,
    //         'refperiode_id' => $refperiode_id,
    //         'sasaranRenstra' => $sasaranRenstra,
    //         'nama_skpd' => $nama_skpd,
    //         'selectedPeriodValue' => $selectedPeriodValue, // Include selected period value
    //         'selectedSkpdId' => $refskpd_id,
    //         'skpdList' => $skpdList,
    //     ]);
    // }

    public function actionIndexLaporanIkuDev($refperiode_id = null, $refskpd_id = null)
    {
        // =========================================================================
        // BLOK LOGIKA BARU UNTUK KEAMANAN SKPD
        // =========================================================================

        $user = Yii::$app->user->identity;
        $assignments = Yii::$app->authManager->getAssignments($user->id);

        $skpdList = [];
        $allowedSkpdIds = []; // Daftar ID SKPD yang diizinkan untuk user ini

        if (isset($assignments['superadmin']) || isset($assignments['admin'])) {
            // --- BLOK UNTUK ADMIN ---
            $allSkpd = SakipSkpd::find()->where(['skpd_isaktif' => 'T'])->orderBy('nama_skpd ASC')->all();
            $skpdList = ArrayHelper::map($allSkpd, 'refskpd_id', 'nama_skpd');
            $allowedSkpdIds = array_keys($skpdList);
        } else {
            // --- BLOK UNTUK NON-ADMIN (KOORDINATOR) ---
            $coordinatedSkpdIds = SakipKoordinasi::find()
                ->select('refskpd_id')
                ->where(['refuser_id' => $user->id])
                ->column();

            $allowedSkpdIds = $coordinatedSkpdIds;

            if (!empty($allowedSkpdIds)) {
                $skpdList = ArrayHelper::map(
                    SakipSkpd::find()->where(['refskpd_id' => $allowedSkpdIds, 'skpd_isaktif' => 'T'])->orderBy('nama_skpd ASC')->all(),
                    'refskpd_id',
                    'nama_skpd'
                );
            }
        }

        // --- Validasi Keamanan ---
        // Cek apakah refskpd_id dari URL diizinkan untuk diakses
        if ($refskpd_id !== null && !in_array($refskpd_id, $allowedSkpdIds)) {
            throw new ForbiddenHttpException('Anda tidak memiliki hak akses untuk melihat data SKPD ini.');
        }

        // Tentukan refskpd_id yang akan digunakan jika tidak ada di URL atau tidak valid
        if ($refskpd_id === null || !in_array($refskpd_id, $allowedSkpdIds)) {
            $refskpd_id = !empty($allowedSkpdIds) ? $allowedSkpdIds[0] : null;
        }

        // =========================================================================
        // AKHIR DARI BLOK LOGIKA BARU
        // Kode di bawah ini sekarang menggunakan $refskpd_id yang sudah aman
        // =========================================================================

        // Get the name of the SKPD based on refskpd_id
        $nama_skpd = $refskpd_id ? SakipSkpd::find()->select('nama_skpd')->where(['refskpd_id' => $refskpd_id])->scalar() : 'Tidak ada SKPD dipilih';

        // Set default period to this year if not provided
        if ($refperiode_id === null) {
            $currentYear = date('Y');
            $defaultPeriod = SakipPeriode::find()->where(['periode' => $currentYear])->one();
            $refperiode_id = $defaultPeriod ? $defaultPeriod->refperiode_id : null;
        }

        // Fetch all periods
        $periodeList = SakipPeriode::find()->all();

        // Logika query utama Anda tidak diubah
        $indikators = [];
        if ($refskpd_id && $refperiode_id) {
            $indikators = SakipIndikatorsasaranrenstra::find()
                ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
                ->all();
        }

        // Prepare the sasaranRenstra with their formulations
        $sasaranRenstra = [];
        foreach ($indikators as $indikator) {
            if ($indikator->sasaranRenstra) { // Gunakan relasi yang sudah ada jika memungkinkan
                $sasaranRenstra[] = [
                    'indikator' => $indikator,
                    'formulasi' => $indikator->sasaranRenstra->formulasi_sasaranrenstra,
                ];
            }
        }

        // Retrieve the periode based on refperiode_id
        $selectedPeriod = SakipPeriode::find()->where(['refperiode_id' => $refperiode_id])->one();
        $selectedPeriodValue = $selectedPeriod ? $selectedPeriod->periode : null; // Get the periode value

        // Set the dynamic title
        $this->view->title = "Laporan Indikator Kinerja Utama $selectedPeriodValue - " . Html::encode($nama_skpd);

        return $this->render('index-laporan-iku-dev', [
            'periodeList' => $periodeList,
            'selectedPeriodId' => $refperiode_id,
            'refskpd_id' => $refskpd_id,
            'refperiode_id' => $refperiode_id,
            'sasaranRenstra' => $sasaranRenstra,
            'nama_skpd' => $nama_skpd,
            'selectedPeriodValue' => $selectedPeriodValue, // Include selected period value
            'selectedSkpdId' => $refskpd_id,
            'skpdList' => $skpdList,
        ]);
    }

    public function actionDownloadPdfIku()
    {
        if (Yii::$app->request->isPost) {
            $content = Yii::$app->request->post('content'); // Konten dari POST
            $title = Yii::$app->request->post('title', 'Laporan'); // Judul untuk nama file

            // Define CSS untuk styling PDF
            $css = <<<CSS
                .tbdata {
                    border-collapse: collapse;
                    font-family: 'Public Sans', sans-serif;
                    font-size: 11px;
                }
                .tbdata th {
                    height: 40px;
                    background: rgb(0, 160, 221);
                    text-align: center;
                    border: 1px solid #cfcfcf;
                    color: #ffffff;
                    padding: 2px;
                }
                .title {
            border-collapse: collapse;
            font-family: 'Public Sans', sans-serif;
            /* Menggunakan font Public Sans */
            font-size: 11px;
            text-align: center;
        }
                .tbdata td {
                    padding: 2px;
                    vertical-align: top;
                }
                .tebal {
                    font-weight: bold;
                }
                .tblRenstra {
                    width: 100%;
            font-family: 'Public Sans', sans-serif;
                    border-collapse: collapse;
                    font-size: 11px;
                }
                .tblRenstra td {
                    padding: 2px;
                    border: 1px solid #f2f2f2;
                }
                .tblRenstra .header {
                    text-align: center;
                    vertical-align: middle;
                    font-weight: bold;
                    background: #03b0e2;
                    color: #ffffff;
                }
                .trO {
                    background: #f2f2f2;
                }
                .trE {
                    background: white;
                }
                .tblAtas {
                    margin-left: 30px;
            font-family: 'Public Sans', sans-serif;
                    border-collapse: collapse;
                    font-size: 11px;
                }
                thead {
                    display: table-header-group;
                }
    CSS;

            // Setting untuk file PDF dengan CSS
            $pdf = new Pdf([
                'mode' => Pdf::MODE_CORE,
                'format' => Pdf::FORMAT_A4,
                'orientation' => Pdf::ORIENT_PORTRAIT,
                'destination' => Pdf::DEST_DOWNLOAD,
                'filename' => $title . '.pdf',
                'content' => $content, // Konten dari POST
                'cssInline' => $css, // Gunakan CSS inline
                'options' => ['title' => $title],
                'methods' => [
                    'SetHeader' => [$title],
                    'SetFooter' => ['{PAGENO}'],
                ],
            ]);

            return $pdf->render(); // Generate dan kembalikan PDF untuk diunduh
        }
    }

    // Laporan IKU
    public function actionIndexLaporanTapkin($refperiode_id = null, $refpenjabatskpd_id = null)
    {
        // Get refskpd_id from the current user
        $user = Yii::$app->user->identity;
        $refskpd_id = $user->refskpd_id;

        // Get the name of the SKPD based on refskpd_id
        $nama_skpd = SakipSkpd::find()->select('nama_skpd')->where(['refskpd_id' => $refskpd_id])->scalar();

        // Fetch the head of SKPD information
        $skpdHead = SakipSkpd::find()->where(['refskpd_id' => $refskpd_id])->one();

        // Set default period to this year if not provided
        if ($refperiode_id === null) {
            $currentYear = date('Y');
            $defaultPeriod = SakipPeriode::find()->where(['periode' => $currentYear])->one();
            $refperiode_id = $defaultPeriod ? $defaultPeriod->refperiode_id : null;
        }

        // Fetch the leadership information for the selected period
        if ($refperiode_id !== null) {
            $leadership = SakipPimpinan::find()->where(['refperiode_id' => $refperiode_id])->one();
        } else {
            // If refperiode_id is null, get the leadership data for the current year
            $currentYear = date('Y');
            $defaultPeriod = SakipPeriode::find()->where(['periode' => $currentYear])->one();
            if ($defaultPeriod) {
                $leadership = SakipPimpinan::find()->where(['refperiode_id' => $defaultPeriod->refperiode_id])->one();
            } else {
                $leadership = null; // Handle case where there is no current year period
            }
        }

        // Fetch the selected penjabat SKPD
        $penjabatSkpd = null;
        $refeselonId = null;
        if ($refpenjabatskpd_id) {
            $penjabatSkpd = SakipPenjabatSkpd::find()->where(['refpenjabatskpd_id' => $refpenjabatskpd_id])->one();
            $refeselonId = $penjabatSkpd ? $penjabatSkpd->refeselon_id : null;
        }

        // Fetch all periods
        $periodeList = SakipPeriode::find()->all();

        $penjabatSkpdList = SakipPenjabatSkpd::find()
            ->where(['refskpd_id' => $refskpd_id])
            ->andFilterWhere(['refperiode_id' => $refperiode_id])
            ->all();

        $sasaranRenstra = SakipSasaranRenstra::find()
            ->where(['refskpd_id' => $refskpd_id])
            ->andFilterWhere(['refperiode_id' => $refperiode_id])
            ->all();

        $indikators = SakipIndikatorsasaranrenstra::find()
            ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
            ->all();

        $programs = (new \yii\db\Query())
            ->select(['refprogram_id'])
            ->from('sakip_cascadingprogram')
            ->where(['refskpd_id' => $refskpd_id])
            ->andFilterWhere(['refperiode_id' => $refperiode_id])
            ->groupBy('refprogram_id')
            ->all();


        // Fetch cascading program
        $penjabatskpdCascadingProgram = SakipPenjabatskpdCascadingprogram::find()
            ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
            ->andWhere(['refpenjabatskpd_id' => $refpenjabatskpd_id]) // Tambahkan filter refpenjabatskpd_id
            ->all();

        // Fetch cascading kegiatan
        $penjabatskpdCascadingKegiatan = SakipPenjabatskpdCascadingkegiatan::find()
            ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
            ->andWhere(['refpenjabatskpd_id' => $refpenjabatskpd_id]) // Tambahkan filter refpenjabatskpd_id
            ->all();

        // Fetch cascading sub-kegiatan
        $penjabatskpdCascadingSubkegiatan = SakipPenjabatskpdCascadingsubkegiatan::find()
            ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
            ->andWhere(['refpenjabatskpd_id' => $refpenjabatskpd_id]) // Tambahkan filter refpenjabatskpd_id
            ->all();

        $indikatorCascadingSubkegiatan = SakipIndikatorcascadingsubkegiatan::find()
            ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
            ->all();


        // Retrieve the periode based on refperiode_id
        $selectedPeriod = SakipPeriode::find()->where(['refperiode_id' => $refperiode_id])->one();
        $selectedPeriodValue = $selectedPeriod ? $selectedPeriod->periode : null; // Get the periode value

        // Set the dynamic title
        $this->view->title = "Laporan Perjanjian Kinerja $selectedPeriodValue - " . Html::encode($nama_skpd);

        return $this->render('index-laporan-tapkin', [
            'periodeList' => $periodeList,
            'penjabatSkpdList' => $penjabatSkpdList,
            'selectedPeriodId' => $refperiode_id,
            'refskpd_id' => $refskpd_id,
            'refperiode_id' => $refperiode_id,
            'refpenjabatskpd_id' => $refpenjabatskpd_id,
            'sasaranRenstra' => $sasaranRenstra,
            'indikators' => $indikators,
            'nama_skpd' => $nama_skpd,
            'selectedPeriodValue' => $selectedPeriodValue, // Include selected period value
            'skpdHead' => $skpdHead,
            'penjabatSkpd' => $penjabatSkpd,
            'penjabatskpdCascadingProgram' => $penjabatskpdCascadingProgram,
            'penjabatskpdCascadingKegiatan' => $penjabatskpdCascadingKegiatan,
            'penjabatskpdCascadingSubkegiatan' => $penjabatskpdCascadingSubkegiatan,
            'indikatorCascadingSubkegiatan' => $indikatorCascadingSubkegiatan,
            'refeselonId' => $refeselonId, // Pass refeselon_id to the view
            'leadership' => $leadership, // Pass leadership data to the view
            'programs' => $programs, // Pass programs to the view
        ]);
    }

    // Laporan IKU
    // public function actionIndexLaporanTapkinDev($refperiode_id = null, $refpenjabatskpd_id = null, $refskpd_id = null)
    // {
    //     // Ambil refskpd_id dari user saat ini jika tidak ada di request
    //     if ($refskpd_id === null) {
    //         $user = Yii::$app->user->identity;
    //         $refskpd_id = $user->refskpd_id;
    //     }

    //     // Get the name of the SKPD based on refskpd_id
    //     $nama_skpd = SakipSkpd::find()->select('nama_skpd')->where(['refskpd_id' => $refskpd_id])->scalar();

    //     // Fetch the head of SKPD information
    //     $skpdHead = SakipSkpd::find()->where(['refskpd_id' => $refskpd_id])->one();

    //     // Set default period to this year if not provided
    //     if ($refperiode_id === null) {
    //         $currentYear = date('Y');
    //         $defaultPeriod = SakipPeriode::find()->where(['periode' => $currentYear])->one();
    //         $refperiode_id = $defaultPeriod ? $defaultPeriod->refperiode_id : null;
    //     }

    //     // Fetch the leadership information for the selected period
    //     if ($refperiode_id !== null) {
    //         $leadership = SakipPimpinan::find()->where(['refperiode_id' => $refperiode_id])->one();
    //     } else {
    //         // If refperiode_id is null, get the leadership data for the current year
    //         $currentYear = date('Y');
    //         $defaultPeriod = SakipPeriode::find()->where(['periode' => $currentYear])->one();
    //         if ($defaultPeriod) {
    //             $leadership = SakipPimpinan::find()->where(['refperiode_id' => $defaultPeriod->refperiode_id])->one();
    //         } else {
    //             $leadership = null; // Handle case where there is no current year period
    //         }
    //     }

    //     // Fetch the selected penjabat SKPD
    //     $penjabatSkpd = null;
    //     $refeselonId = null;
    //     if ($refpenjabatskpd_id) {
    //         $penjabatSkpd = SakipPenjabatSkpd::find()->where(['refpenjabatskpd_id' => $refpenjabatskpd_id])->one();
    //         $refeselonId = $penjabatSkpd ? $penjabatSkpd->refeselon_id : null;
    //     }

    //     // Fetch all periods
    //     $periodeList = SakipPeriode::find()->all();

    //     $penjabatSkpdList = SakipPenjabatSkpd::find()
    //         ->where(['refskpd_id' => $refskpd_id])
    //         ->andFilterWhere(['refperiode_id' => $refperiode_id])
    //         ->all();

    //     $sasaranRenstra = SakipSasaranRenstra::find()
    //         ->where(['refskpd_id' => $refskpd_id])
    //         ->andFilterWhere(['refperiode_id' => $refperiode_id])
    //         ->all();

    //     $indikators = SakipIndikatorsasaranrenstra::find()
    //         ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
    //         ->all();

    //     // Retrieve programs for the refskpd_id and refperiode_id
    //     $programs = SakipCascadingprogram::find()
    //         ->where(['refskpd_id' => $refskpd_id])
    //         ->andFilterWhere(['refperiode_id' => $refperiode_id])
    //         ->all();

    //     // Fetch cascading program
    //     $penjabatskpdCascadingProgram = SakipPenjabatskpdCascadingprogram::find()
    //         ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
    //         ->andWhere(['refpenjabatskpd_id' => $refpenjabatskpd_id]) // Tambahkan filter refpenjabatskpd_id
    //         ->all();

    //     // Fetch cascading kegiatan
    //     $penjabatskpdCascadingKegiatan = SakipPenjabatskpdCascadingkegiatan::find()
    //         ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
    //         ->andWhere(['refpenjabatskpd_id' => $refpenjabatskpd_id]) // Tambahkan filter refpenjabatskpd_id
    //         ->all();

    //     // Fetch cascading sub-kegiatan
    //     $penjabatskpdCascadingSubkegiatan = SakipPenjabatskpdCascadingsubkegiatan::find()
    //         ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
    //         ->andWhere(['refpenjabatskpd_id' => $refpenjabatskpd_id]) // Tambahkan filter refpenjabatskpd_id
    //         ->all();

    //     $indikatorCascadingSubkegiatan = SakipIndikatorcascadingsubkegiatan::find()
    //         ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
    //         ->all();

    //     // Ambil daftar SKPD untuk dropdown
    //     $skpdList = ArrayHelper::map(SakipSkpd::find()->all(), 'refskpd_id', 'nama_skpd');

    //     // Retrieve the periode based on refperiode_id
    //     $selectedPeriod = SakipPeriode::find()->where(['refperiode_id' => $refperiode_id])->one();
    //     $selectedPeriodValue = $selectedPeriod ? $selectedPeriod->periode : null; // Get the periode value

    //     // Set the dynamic title
    //     $this->view->title = "Laporan Perjanjian Kinerja $selectedPeriodValue - " . Html::encode($nama_skpd);

    //     return $this->render('index-laporan-tapkin-dev', [
    //         'periodeList' => $periodeList,
    //         'penjabatSkpdList' => $penjabatSkpdList,
    //         'selectedPeriodId' => $refperiode_id,
    //         'refskpd_id' => $refskpd_id,
    //         'refperiode_id' => $refperiode_id,
    //         'refpenjabatskpd_id' => $refpenjabatskpd_id,
    //         'sasaranRenstra' => $sasaranRenstra,
    //         'indikators' => $indikators,
    //         'nama_skpd' => $nama_skpd,
    //         'selectedPeriodValue' => $selectedPeriodValue, // Include selected period value
    //         'skpdHead' => $skpdHead,
    //         'penjabatSkpd' => $penjabatSkpd,
    //         'penjabatskpdCascadingProgram' => $penjabatskpdCascadingProgram,
    //         'penjabatskpdCascadingKegiatan' => $penjabatskpdCascadingKegiatan,
    //         'penjabatskpdCascadingSubkegiatan' => $penjabatskpdCascadingSubkegiatan,
    //         'indikatorCascadingSubkegiatan' => $indikatorCascadingSubkegiatan,
    //         'refeselonId' => $refeselonId, // Pass refeselon_id to the view
    //         'leadership' => $leadership, // Pass leadership data to the view
    //         'programs' => $programs, // Pass programs to the view
    //         'selectedSkpdId' => $refskpd_id,
    //         'skpdList' => $skpdList,
    //     ]);
    // }

    public function actionIndexLaporanTapkinDev($refperiode_id = null, $refpenjabatskpd_id = null, $refskpd_id = null)
    {
        // =========================================================================
        // BLOK LOGIKA BARU UNTUK KEAMANAN SKPD
        // =========================================================================

        $user = Yii::$app->user->identity;
        $assignments = Yii::$app->authManager->getAssignments($user->id);

        $skpdList = [];
        $allowedSkpdIds = []; // Daftar ID SKPD yang diizinkan untuk user ini

        if (isset($assignments['superadmin']) || isset($assignments['admin'])) {
            // --- BLOK UNTUK ADMIN ---
            $allSkpd = SakipSkpd::find()->where(['skpd_isaktif' => 'T'])->orderBy('nama_skpd ASC')->all();
            $skpdList = ArrayHelper::map($allSkpd, 'refskpd_id', 'nama_skpd');
            $allowedSkpdIds = array_keys($skpdList);
        } else {
            // --- BLOK UNTUK NON-ADMIN (KOORDINATOR) ---
            $coordinatedSkpdIds = SakipKoordinasi::find()
                ->select('refskpd_id')
                ->where(['refuser_id' => $user->id])
                ->column();

            $allowedSkpdIds = $coordinatedSkpdIds;

            if (!empty($allowedSkpdIds)) {
                $skpdList = ArrayHelper::map(
                    SakipSkpd::find()->where(['refskpd_id' => $allowedSkpdIds, 'skpd_isaktif' => 'T'])->orderBy('nama_skpd ASC')->all(),
                    'refskpd_id',
                    'nama_skpd'
                );
            }
        }

        // --- Validasi Keamanan ---
        // Cek apakah refskpd_id dari URL diizinkan untuk diakses
        if ($refskpd_id !== null && !in_array($refskpd_id, $allowedSkpdIds)) {
            throw new ForbiddenHttpException('Anda tidak memiliki hak akses untuk melihat data SKPD ini.');
        }

        // Tentukan refskpd_id yang akan digunakan jika tidak ada di URL atau tidak valid
        if ($refskpd_id === null || !in_array($refskpd_id, $allowedSkpdIds)) {
            $refskpd_id = !empty($allowedSkpdIds) ? $allowedSkpdIds[0] : null;
        }

        // =========================================================================
        // AKHIR DARI BLOK LOGIKA BARU
        // Kode di bawah ini sekarang menggunakan $refskpd_id yang sudah aman
        // =========================================================================

        // Get the name of the SKPD based on refskpd_id
        $nama_skpd = $refskpd_id ? SakipSkpd::find()->select('nama_skpd')->where(['refskpd_id' => $refskpd_id])->scalar() : 'Tidak ada SKPD dipilih';

        // Fetch the head of SKPD information
        $skpdHead = SakipSkpd::find()->where(['refskpd_id' => $refskpd_id])->one();

        // Set default period to this year if not provided
        if ($refperiode_id === null) {
            $currentYear = date('Y');
            $defaultPeriod = SakipPeriode::find()->where(['periode' => $currentYear])->one();
            $refperiode_id = $defaultPeriod ? $defaultPeriod->refperiode_id : null;
        }

        // Fetch the leadership information for the selected period
        $leadership = null;
        if ($refperiode_id !== null) {
            $leadership = SakipPimpinan::find()->where(['refperiode_id' => $refperiode_id])->one();
        } else {
            $currentYear = date('Y');
            $defaultPeriod = SakipPeriode::find()->where(['periode' => $currentYear])->one();
            if ($defaultPeriod) {
                $leadership = SakipPimpinan::find()->where(['refperiode_id' => $defaultPeriod->refperiode_id])->one();
            }
        }

        // Fetch the selected penjabat SKPD
        $penjabatSkpd = null;
        $refeselonId = null;
        if ($refpenjabatskpd_id) {
            $penjabatSkpd = SakipPenjabatSkpd::find()->where(['refpenjabatskpd_id' => $refpenjabatskpd_id])->one();
            $refeselonId = $penjabatSkpd ? $penjabatSkpd->refeselon_id : null;
        }

        // Fetch all periods
        $periodeList = SakipPeriode::find()->all();

        // Logika query utama Anda tidak diubah
        $penjabatSkpdList = [];
        $sasaranRenstra = [];
        $indikators = [];
        $programs = [];
        $penjabatskpdCascadingProgram = [];
        $penjabatskpdCascadingKegiatan = [];
        $penjabatskpdCascadingSubkegiatan = [];
        $indikatorCascadingSubkegiatan = [];

        if ($refskpd_id && $refperiode_id) {
            $penjabatSkpdList = SakipPenjabatSkpd::find()
                ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
                ->all();

            $sasaranRenstra = SakipSasaranrenstra::find()
                ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
                ->all();

            $indikators = SakipIndikatorsasaranrenstra::find()
                ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
                ->all();

            $programs = SakipCascadingprogram::find()
                ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
                ->all();

            if ($refpenjabatskpd_id) {
                $penjabatskpdCascadingProgram = SakipPenjabatskpdCascadingprogram::find()
                    ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id, 'refpenjabatskpd_id' => $refpenjabatskpd_id])
                    ->all();

                $penjabatskpdCascadingKegiatan = SakipPenjabatskpdCascadingkegiatan::find()
                    ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id, 'refpenjabatskpd_id' => $refpenjabatskpd_id])
                    ->all();

                $penjabatskpdCascadingSubkegiatan = SakipPenjabatskpdCascadingsubkegiatan::find()
                    ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id, 'refpenjabatskpd_id' => $refpenjabatskpd_id])
                    ->all();
            }

            $indikatorCascadingSubkegiatan = SakipIndikatorcascadingsubkegiatan::find()
                ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
                ->all();
        }

        // Retrieve the periode based on refperiode_id
        $selectedPeriod = SakipPeriode::find()->where(['refperiode_id' => $refperiode_id])->one();
        $selectedPeriodValue = $selectedPeriod ? $selectedPeriod->periode : null; // Get the periode value

        // Set the dynamic title
        $this->view->title = "Laporan Perjanjian Kinerja $selectedPeriodValue - " . Html::encode($nama_skpd);

        return $this->render('index-laporan-tapkin-dev', [
            'periodeList' => $periodeList,
            'penjabatSkpdList' => $penjabatSkpdList,
            'selectedPeriodId' => $refperiode_id,
            'refskpd_id' => $refskpd_id,
            'refperiode_id' => $refperiode_id,
            'refpenjabatskpd_id' => $refpenjabatskpd_id,
            'sasaranRenstra' => $sasaranRenstra,
            'indikators' => $indikators,
            'nama_skpd' => $nama_skpd,
            'selectedPeriodValue' => $selectedPeriodValue, // Include selected period value
            'skpdHead' => $skpdHead,
            'penjabatSkpd' => $penjabatSkpd,
            'penjabatskpdCascadingProgram' => $penjabatskpdCascadingProgram,
            'penjabatskpdCascadingKegiatan' => $penjabatskpdCascadingKegiatan,
            'penjabatskpdCascadingSubkegiatan' => $penjabatskpdCascadingSubkegiatan,
            'indikatorCascadingSubkegiatan' => $indikatorCascadingSubkegiatan,
            'refeselonId' => $refeselonId, // Pass refeselon_id to the view
            'leadership' => $leadership, // Pass leadership data to the view
            'programs' => $programs, // Pass programs to the view
            'selectedSkpdId' => $refskpd_id,
            'skpdList' => $skpdList,
        ]);
    }

    public function actionDownloadPdfTapkin()
    {
        if (Yii::$app->request->isPost) {
            $content = Yii::$app->request->post('content'); // Konten dari POST
            $title = Yii::$app->request->post('title', 'Laporan'); // Judul untuk nama file

            // Define CSS untuk styling PDF
            $css = <<<CSS
    .title {
        border-collapse: collapse;
        font-family: 'Public Sans', sans-serif;
        /* Menggunakan font Public Sans */
        font-size: 11px;
        text-align: center;
    }

    #halamanlaporan {
        font-family: 'Public Sans', sans-serif;
        font-size: 0.35cm;
        width: 600px;
        margin: auto;
    }

    #halamanlaporan .isilaporan {
        font-size: 0.35cm;
        line-height: 1.3;
    }

    #halamanlaporan .isilaporan h3 {
        font-weight: normal;
        font-size: 0.35cm;
        text-align: center;
        margin-bottom: 20px;
    }

    #halamanlaporan .isilaporan h4 {
        font-size: 0.35cm;
        font-weight: bold;
        text-align: center;
    }

    #halamanlaporan .isilaporan h5 {
        font-size: 0.35cm;
        font-weight: bold;
        text-align: center;
        margin-top: 20px;
        margin-bottom: 20px;
    }

    #halamanlaporan .isilaporan p {
        text-align: justify;
    }

    #halamanlaporan .tblPihak {
        width: 100%;
        font-size: 0.35cm;
    }

    #halamanlaporan .tblPihak td {
        width: 50%;
        text-align: center;
    }

    #halamanlaporan .tbdata {
        width: 100%;
        font-size: 0.35cm;
        margin-top: 20px;
        border-collapse: collapse;
    }

    #halamanlaporan .tbdata th {
        padding: 5px;
        text-align: center;
    }

    #halamanlaporan .tbdata td {
        padding-left: 3px;
        padding-right: 3px;
        vertical-align: top;
    }

    #halamanlaporan .tengah {
        text-align: center;
    }

    #halamanlaporan .kanan {
        text-align: right;
    }
    CSS;

            // Setting untuk file PDF dengan CSS
            $pdf = new Pdf([
                'mode' => Pdf::MODE_CORE,
                'format' => Pdf::FORMAT_A4,
                'orientation' => Pdf::ORIENT_PORTRAIT,
                'destination' => Pdf::DEST_DOWNLOAD,
                'filename' => $title . '.pdf',
                'content' => $content, // Konten dari POST
                'cssInline' => $css, // Gunakan CSS inline
                'options' => ['title' => $title],
                'methods' => [
                    'SetHeader' => [$title],
                    'SetFooter' => ['{PAGENO}'],
                ],
            ]);

            return $pdf->render(); // Generate dan kembalikan PDF untuk diunduh
        }
    }

    // Capkin IKu
    public function actionIndexLaporanCapkinIku($refperiode_id = null)
    {
        // Get refskpd_id from the current user
        $user = Yii::$app->user->identity;
        $refskpd_id = $user->refskpd_id;

        // Get the name of the SKPD based on refskpd_id
        $nama_skpd = SakipSkpd::find()->select('nama_skpd')->where(['refskpd_id' => $refskpd_id])->scalar();

        // Set default period to this year if not provided
        if ($refperiode_id === null) {
            $currentYear = date('Y');
            $defaultPeriod = SakipPeriode::find()->where(['periode' => $currentYear])->one();
            $refperiode_id = $defaultPeriod ? $defaultPeriod->refperiode_id : null;
        }

        // Fetch all periods
        $periodeList = SakipPeriode::find()->all();

        // Fetch sasaran renstra with related indicators
        $indikators = SakipIndikatorsasaranrenstra::find()
            ->where(['refskpd_id' => $refskpd_id])
            ->andWhere(['refperiode_id' => $refperiode_id])
            ->with('refSasaranrenstra') // Eager load related sasaran renstra
            ->all();

        // Retrieve the periode based on refperiode_id
        $selectedPeriod = SakipPeriode::find()->where(['refperiode_id' => $refperiode_id])->one();
        $selectedPeriodValue = $selectedPeriod ? $selectedPeriod->periode : null; // Get the periode value

        // Set the dynamic title
        $this->view->title = "Laporan Rencana Kinerja Tahun $selectedPeriodValue - " . Html::encode($nama_skpd);

        return $this->render('index-laporan-capkin-iku', [
            'periodeList' => $periodeList,
            'selectedPeriodId' => $refperiode_id,
            'refskpd_id' => $refskpd_id,
            'refperiode_id' => $refperiode_id,
            'indikators' => $indikators,
            'nama_skpd' => $nama_skpd,
            'selectedPeriodValue' => $selectedPeriodValue, // Include selected period value
        ]);
    }

    // Capkin IKu
    // public function actionIndexLaporanCapkinIkuDev($refperiode_id = null, $refskpd_id = null)
    // {
    //     // Ambil refskpd_id dari user saat ini jika tidak ada di request
    //     if ($refskpd_id === null) {
    //         $user = Yii::$app->user->identity;
    //         $refskpd_id = $user->refskpd_id;
    //     }

    //     // Get the name of the SKPD based on refskpd_id
    //     $nama_skpd = SakipSkpd::find()->select('nama_skpd')->where(['refskpd_id' => $refskpd_id])->scalar();

    //     // Set default period to this year if not provided
    //     if ($refperiode_id === null) {
    //         $currentYear = date('Y');
    //         $defaultPeriod = SakipPeriode::find()->where(['periode' => $currentYear])->one();
    //         $refperiode_id = $defaultPeriod ? $defaultPeriod->refperiode_id : null;
    //     }

    //     // Fetch all periods
    //     $periodeList = SakipPeriode::find()->all();

    //     // Fetch sasaran renstra with related indicators
    //     $indikators = SakipIndikatorsasaranrenstra::find()
    //         ->where(['refskpd_id' => $refskpd_id])
    //         ->andWhere(['refperiode_id' => $refperiode_id])
    //         ->with('refSasaranrenstra') // Eager load related sasaran renstra
    //         ->all();

    //     // Ambil daftar SKPD untuk dropdown
    //     $skpdList = ArrayHelper::map(SakipSkpd::find()->all(), 'refskpd_id', 'nama_skpd');

    //     // Retrieve the periode based on refperiode_id
    //     $selectedPeriod = SakipPeriode::find()->where(['refperiode_id' => $refperiode_id])->one();
    //     $selectedPeriodValue = $selectedPeriod ? $selectedPeriod->periode : null; // Get the periode value

    //     // Set the dynamic title
    //     $this->view->title = "Laporan Rencana Kinerja Tahun $selectedPeriodValue - " . Html::encode($nama_skpd);

    //     return $this->render('index-laporan-capkin-iku-dev', [
    //         'periodeList' => $periodeList,
    //         'selectedPeriodId' => $refperiode_id,
    //         'refskpd_id' => $refskpd_id,
    //         'refperiode_id' => $refperiode_id,
    //         'indikators' => $indikators,
    //         'nama_skpd' => $nama_skpd,
    //         'selectedPeriodValue' => $selectedPeriodValue, // Include selected period value
    //         'selectedSkpdId' => $refskpd_id,
    //         'skpdList' => $skpdList,
    //     ]);
    // }

    public function actionIndexLaporanCapkinIkuDev($refperiode_id = null, $refskpd_id = null)
    {
        // =========================================================================
        // BLOK LOGIKA BARU UNTUK KEAMANAN SKPD
        // =========================================================================

        $user = Yii::$app->user->identity;
        $assignments = Yii::$app->authManager->getAssignments($user->id);

        $skpdList = [];
        $allowedSkpdIds = []; // Daftar ID SKPD yang diizinkan untuk user ini

        if (isset($assignments['superadmin']) || isset($assignments['admin'])) {
            // --- BLOK UNTUK ADMIN ---
            $allSkpd = SakipSkpd::find()->where(['skpd_isaktif' => 'T'])->orderBy('nama_skpd ASC')->all();
            $skpdList = ArrayHelper::map($allSkpd, 'refskpd_id', 'nama_skpd');
            $allowedSkpdIds = array_keys($skpdList);
        } else {
            // --- BLOK UNTUK NON-ADMIN (KOORDINATOR) ---
            $coordinatedSkpdIds = SakipKoordinasi::find()
                ->select('refskpd_id')
                ->where(['refuser_id' => $user->id])
                ->column();

            $allowedSkpdIds = $coordinatedSkpdIds;

            if (!empty($allowedSkpdIds)) {
                $skpdList = ArrayHelper::map(
                    SakipSkpd::find()->where(['refskpd_id' => $allowedSkpdIds, 'skpd_isaktif' => 'T'])->orderBy('nama_skpd ASC')->all(),
                    'refskpd_id',
                    'nama_skpd'
                );
            }
        }

        // --- Validasi Keamanan ---
        // Cek apakah refskpd_id dari URL diizinkan untuk diakses
        if ($refskpd_id !== null && !in_array($refskpd_id, $allowedSkpdIds)) {
            throw new ForbiddenHttpException('Anda tidak memiliki hak akses untuk melihat data SKPD ini.');
        }

        // Tentukan refskpd_id yang akan digunakan jika tidak ada di URL atau tidak valid
        if ($refskpd_id === null || !in_array($refskpd_id, $allowedSkpdIds)) {
            $refskpd_id = !empty($allowedSkpdIds) ? $allowedSkpdIds[0] : null;
        }

        // =========================================================================
        // AKHIR DARI BLOK LOGIKA BARU
        // Kode di bawah ini sekarang menggunakan $refskpd_id yang sudah aman
        // =========================================================================

        // Get the name of the SKPD based on refskpd_id
        $nama_skpd = $refskpd_id ? SakipSkpd::find()->select('nama_skpd')->where(['refskpd_id' => $refskpd_id])->scalar() : 'Tidak ada SKPD dipilih';

        // Set default period to this year if not provided
        if ($refperiode_id === null) {
            $currentYear = date('Y');
            $defaultPeriod = SakipPeriode::find()->where(['periode' => $currentYear])->one();
            $refperiode_id = $defaultPeriod ? $defaultPeriod->refperiode_id : null;
        }

        // Fetch all periods
        $periodeList = SakipPeriode::find()->all();

        // Logika query utama Anda tidak diubah
        $indikators = [];
        if ($refskpd_id && $refperiode_id) {
            $indikators = SakipIndikatorsasaranrenstra::find()
                ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
                ->with('refSasaranrenstra') // Eager load related sasaran renstra
                ->all();
        }

        // Retrieve the periode based on refperiode_id
        $selectedPeriod = SakipPeriode::find()->where(['refperiode_id' => $refperiode_id])->one();
        $selectedPeriodValue = $selectedPeriod ? $selectedPeriod->periode : null; // Get the periode value

        // Set the dynamic title
        $this->view->title = "Laporan Capaian Kinerja IKU $selectedPeriodValue - " . Html::encode($nama_skpd);

        return $this->render('index-laporan-capkin-iku-dev', [
            'periodeList' => $periodeList,
            'selectedPeriodId' => $refperiode_id,
            'refskpd_id' => $refskpd_id,
            'refperiode_id' => $refperiode_id,
            'indikators' => $indikators,
            'nama_skpd' => $nama_skpd,
            'selectedPeriodValue' => $selectedPeriodValue, // Include selected period value
            'selectedSkpdId' => $refskpd_id,
            'skpdList' => $skpdList,
        ]);
    }

    public function actionDownloadPdfCapkinIku()
    {
        if (Yii::$app->request->isPost) {
            $content = Yii::$app->request->post('content'); // Konten dari POST
            $title = Yii::$app->request->post('title', 'Laporan'); // Judul untuk nama file

            // Define CSS untuk styling PDF
            $css = <<<CSS
                    .title {
        border-collapse: collapse;
        font-family: 'Public Sans', sans-serif;
        /* Menggunakan font Public Sans */
        font-size: 11px;
        text-align: center;
    }

    .tbdata {
        border-collapse: collapse;
        font-family: 'Public Sans', sans-serif;
        font-size: 11px;
    }

    .tbdata th {
        height: 40px;
        background: rgb(0, 160, 221);
        text-align: center;
        border: 1px solid #cfcfcf;
        color: #ffffff;
        padding: 2px;
    }

    .tbdata td {
        padding: 2px;
        border: 1px solid #cfcfcf;
        vertical-align: top;
    }

    .tengah {
        text-align: center;
    }

    thead {
        display: table-header-group;
    }

    .keterangan {
        border-collapse: collapse;
    }

    .keterangan td {
        padding: 5px;
        border: 1px solid #e3e3e3;
    }

    .merah {
        background: #ff0404;
        color: white;
    }

    .hijau {
        background: #006600;
        color: white;
    }

    .biru {
        background: #000266;
        color: white;
    }

    .abu {
        background: #95a5a6;
        color: white;
    }
    CSS;

            // Setting untuk file PDF dengan CSS
            $pdf = new Pdf([
                'mode' => Pdf::MODE_CORE,
                'format' => Pdf::FORMAT_A4,
                'orientation' => Pdf::ORIENT_PORTRAIT,
                'destination' => Pdf::DEST_DOWNLOAD,
                'filename' => $title . '.pdf',
                'content' => $content, // Konten dari POST
                'cssInline' => $css, // Gunakan CSS inline
                'options' => ['title' => $title],
                'methods' => [
                    'SetHeader' => [$title],
                    'SetFooter' => ['{PAGENO}'],
                ],
            ]);

            return $pdf->render(); // Generate dan kembalikan PDF untuk diunduh
        }
    }

    public function actionIndexLaporanRealisasiAnggaran($refperiode_id = null)
    {
        // Get refskpd_id from the current user
        $user = Yii::$app->user->identity;
        $refskpd_id = $user->refskpd_id;

        // Get the name of the SKPD based on refskpd_id
        $nama_skpd = SakipSkpd::find()->select('nama_skpd')->where(['refskpd_id' => $refskpd_id])->scalar();

        if ($refperiode_id === null) {
            $currentYear = date('Y');
            $defaultPeriod = SakipPeriode::find()->where(['periode' => $currentYear])->one();
            if ($defaultPeriod) {
                $refperiode_id = $defaultPeriod->refperiode_id;
            } else {
                throw new NotFoundHttpException("Periode tahun $currentYear tidak ditemukan.");
            }
        }


        // Fetch all periods
        $periodeList = SakipPeriode::find()->all();

        // Fetch main indicator data with related programs and SKPD info
        $indikators = SakipIndikatorcascadingsubkegiatan::find()
            ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
            ->with(['refCascadingProgram', 'refProgram', 'refSasaranrenstra'])
            ->all();

        // Fetch quarterly data
        $quarterlyData = SakipIndikatorcascadingsubkegiatanTriwulan::find()
            ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
            ->all();

        // Organize data for the view
        $data = [];
        foreach ($indikators as $indikator) {
            $sasaranId = $indikator->refsasaranrenstra_id;
            if (!isset($data[$sasaranId])) {
                $data[$sasaranId] = [
                    'uraian_sasaran' => $indikator->refSasaranrenstra->uraian_sasaranrenstra,
                    'programs' => [],
                    'total_anggaran' => 0,
                    'total_quarterly_penyerapan_anggaran' => [0, 0, 0, 0],
                    'total_quarterly_realisasi' => [0, 0, 0, 0]
                ];
            }


            // Menambahkan subkegiatan dan anggaran untuk setiap subkegiatan
            $programId = $indikator->refcascadingprogram_id;
            if (!isset($data[$sasaranId]['programs'][$programId])) {
                $data[$sasaranId]['programs'][$programId] = [
                    'nama_program' => $indikator->refProgram->nama_program,
                    'anggaran_pk_p' => (float) ($indikator->anggaran_pk_p ?? 0),
                    'subkegiatan' => []  // Menambahkan array subkegiatan
                ];
                // $data[$sasaranId]['total_anggaran'] += (float) ($indikator->anggaran_pk_p ?? 0);
            }

            // Menambahkan subkegiatan dan anggaran subkegiatan
            $subkegiatanId = $indikator->refsubkegiatan_id;  // Ambil ID subkegiatan
            if (!isset($data[$sasaranId]['programs'][$programId]['subkegiatan'][$subkegiatanId])) {
                $data[$sasaranId]['programs'][$programId]['subkegiatan'][$subkegiatanId] = [
                    'nama_subkegiatan' => $indikator->refSubkegiatan->nama_subkegiatan,
                    'anggaran_subkegiatan' => (float) ($indikator->anggaran_pk_p ?? 0)
                ];
            }
        }

        // **Perhitungan total anggaran untuk setiap program berdasarkan subkegiatan**
        foreach ($data as $sasaranId => $entry) {
            foreach ($entry['programs'] as $programId => $program) {
                $totalProgramAnggaran = 0;
                foreach ($program['subkegiatan'] as $subkegiatan) {
                    $totalProgramAnggaran += $subkegiatan['anggaran_subkegiatan'];
                }

                // Menghitung total anggaran program (subkegiatan ditambahkan)
                $data[$sasaranId]['programs'][$programId]['anggaran_pk_p'] = $totalProgramAnggaran;
                $data[$sasaranId]['total_anggaran'] += $totalProgramAnggaran;
            }
        }


        foreach ($quarterlyData as $quarter) {
            $sasaranId = $quarter->refsasaranrenstra_id;
            $programId = $quarter->refcascadingprogram_id;

            if (isset($data[$sasaranId]['programs'][$programId])) {
                if ($quarter->reftriwulan_id >= 1 && $quarter->reftriwulan_id <= 4) {
                    $quarterIndex = $quarter->reftriwulan_id - 1;

                    if (!isset($data[$sasaranId]['programs'][$programId]['quarterly'][$quarterIndex])) {
                        $data[$sasaranId]['programs'][$programId]['quarterly'][$quarterIndex] = [
                            'triwulan_target_rkt' => 0,
                            'triwulan_realisasi' => 0, // realisasi = capaian indikator subkegiatan
                            'triwulan_penyerapan_anggaran' => 0
                        ];
                    }

                    $data[$sasaranId]['programs'][$programId]['quarterly'][$quarterIndex]['triwulan_target_rkt'] += (float) ($quarter->triwulan_target_rkt ?? 0);
                    $data[$sasaranId]['programs'][$programId]['quarterly'][$quarterIndex]['triwulan_penyerapan_anggaran'] += (float) ($quarter->triwulan_penyerapan_anggaran ?? 0);
                    $data[$sasaranId]['programs'][$programId]['quarterly'][$quarterIndex]['triwulan_realisasi'] += (float) ($quarter->triwulan_capaian ?? 0);

                    // Total per-sasaran (seluruh program)
                    $data[$sasaranId]['total_quarterly_penyerapan_anggaran'][$quarterIndex] += (float) ($quarter->triwulan_penyerapan_anggaran ?? 0);
                    $data[$sasaranId]['total_quarterly_realisasi'][$quarterIndex] += (float) ($quarter->triwulan_capaian ?? 0);
                }
            }
        }


        // Retrieve the periode based on refperiode_id
        $selectedPeriod = SakipPeriode::find()->where(['refperiode_id' => $refperiode_id])->one();
        $selectedPeriodValue = $selectedPeriod ? $selectedPeriod->periode : null; // Get the periode value

        // Set the dynamic title
        $this->view->title = "Laporan Rencana Kinerja Tahun $selectedPeriodValue - " . Html::encode($nama_skpd);

        return $this->render('index-laporan-realisasi-anggaran', [
            'periodeList' => $periodeList,
            'selectedPeriodId' => $refperiode_id,
            'refskpd_id' => $refskpd_id,
            'refperiode_id' => $refperiode_id,
            'indikators' => $indikators,
            'data' => $data,
            'nama_skpd' => $nama_skpd,
            'selectedPeriodValue' => $selectedPeriodValue, // Include selected period value
        ]);
    }

    // public function actionIndexLaporanRealisasiAnggaranDev($refperiode_id = null, $refskpd_id = null)
    // {
    //     // Ambil refskpd_id dari user saat ini jika tidak ada di request
    //     if ($refskpd_id === null) {
    //         $user = Yii::$app->user->identity;
    //         $refskpd_id = $user->refskpd_id;
    //     }

    //     // Get the name of the SKPD based on refskpd_id
    //     $nama_skpd = SakipSkpd::find()->select('nama_skpd')->where(['refskpd_id' => $refskpd_id])->scalar();

    //     // Set default period to this year if not provided
    //     if ($refperiode_id === null) {
    //         $currentYear = date('Y');
    //         $defaultPeriod = SakipPeriode::find()->where(['periode' => $currentYear])->one();
    //         $refperiode_id = $defaultPeriod ? $defaultPeriod->refperiode_id : null;
    //     }

    //     // Fetch all periods
    //     $periodeList = SakipPeriode::find()->all();

    //     // Fetch main indicator data with related programs and SKPD info
    //     $indikators = SakipIndikatorcascadingsubkegiatan::find()
    //         ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
    //         ->with(['refCascadingProgram', 'refProgram', 'refSasaranrenstra'])
    //         ->all();

    //     // Fetch quarterly data
    //     $quarterlyData = SakipIndikatorcascadingsubkegiatanTriwulan::find()
    //         ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
    //         ->all();

    //     // Organize data for the view
    //     $data = [];
    //     foreach ($indikators as $indikator) {
    //         $sasaranId = $indikator->refsasaranrenstra_id;
    //         if (!isset($data[$sasaranId])) {
    //             $data[$sasaranId] = [
    //                 'uraian_sasaran' => $indikator->refSasaranrenstra->uraian_sasaranrenstra,
    //                 'programs' => [],
    //                 'total_anggaran' => 0,
    //                 'total_quarterly_penyerapan_anggaran' => [0, 0, 0, 0],
    //                 'total_quarterly_realisasi' => [0, 0, 0, 0]
    //             ];
    //         }

    //         // Add program details
    //         $programId = $indikator->refcascadingprogram_id;
    //         if (!isset($data[$sasaranId]['programs'][$programId])) {
    //             $data[$sasaranId]['programs'][$programId] = [
    //                 'nama_program' => $indikator->refProgram->nama_program,
    //                 'anggaran_pk_p' => $indikator->anggaran_pk_p,
    //                 'quarterly' => []
    //             ];
    //             $data[$sasaranId]['total_anggaran'] += (float) $indikator->anggaran_pk_p;
    //         }
    //     }

    //     // Populate quarterly data
    //     foreach ($quarterlyData as $quarter) {
    //         $sasaranId = $quarter->refsasaranrenstra_id;
    //         $programId = $quarter->refcascadingprogram_id;

    //         if (isset($data[$sasaranId]['programs'][$programId])) {
    //             $quarterIndex = $quarter->reftriwulan_id - 1; // Assuming reftriwulan_id is 1 to 4 for quarters
    //             $data[$sasaranId]['programs'][$programId]['quarterly'][$quarterIndex] = [
    //                 'triwulan_target_rkt' => $quarter->triwulan_target_rkt,
    //                 'triwulan_realisasi' => $quarter->triwulan_realisasi,
    //                 'triwulan_penyerapan_anggaran' => $quarter->triwulan_penyerapan_anggaran
    //             ];

    //             // Sum totals by quarter
    //             $data[$sasaranId]['total_quarterly_penyerapan_anggaran'][$quarterIndex] += $quarter->triwulan_penyerapan_anggaran;
    //             $data[$sasaranId]['total_quarterly_realisasi'][$quarterIndex] += $quarter->triwulan_realisasi;
    //         }
    //     }

    //     // Ambil daftar SKPD untuk dropdown
    //     $skpdList = ArrayHelper::map(SakipSkpd::find()->all(), 'refskpd_id', 'nama_skpd');

    //     // Retrieve the periode based on refperiode_id
    //     $selectedPeriod = SakipPeriode::find()->where(['refperiode_id' => $refperiode_id])->one();
    //     $selectedPeriodValue = $selectedPeriod ? $selectedPeriod->periode : null; // Get the periode value

    //     // Set the dynamic title
    //     $this->view->title = "Laporan Rencana Kinerja Tahun $selectedPeriodValue - " . Html::encode($nama_skpd);

    //     return $this->render('index-laporan-realisasi-anggaran-dev', [
    //         'periodeList' => $periodeList,
    //         'selectedPeriodId' => $refperiode_id,
    //         'refskpd_id' => $refskpd_id,
    //         'refperiode_id' => $refperiode_id,
    //         'indikators' => $indikators,
    //         'data' => $data,
    //         'nama_skpd' => $nama_skpd,
    //         'selectedPeriodValue' => $selectedPeriodValue, // Include selected period value
    //         'selectedSkpdId' => $refskpd_id,
    //         'skpdList' => $skpdList,
    //     ]);
    // }

    public function actionIndexLaporanRealisasiAnggaranDev($refperiode_id = null, $refskpd_id = null)
    {
        // =========================================================================
        // BLOK LOGIKA BARU UNTUK KEAMANAN SKPD
        // =========================================================================

        $user = Yii::$app->user->identity;
        $assignments = Yii::$app->authManager->getAssignments($user->id);

        $skpdList = [];
        $allowedSkpdIds = []; // Daftar ID SKPD yang diizinkan untuk user ini

        if (isset($assignments['superadmin']) || isset($assignments['admin'])) {
            // --- BLOK UNTUK ADMIN ---
            $allSkpd = SakipSkpd::find()->where(['skpd_isaktif' => 'T'])->orderBy('nama_skpd ASC')->all();
            $skpdList = ArrayHelper::map($allSkpd, 'refskpd_id', 'nama_skpd');
            $allowedSkpdIds = array_keys($skpdList);
        } else {
            // --- BLOK UNTUK NON-ADMIN (KOORDINATOR) ---
            $coordinatedSkpdIds = SakipKoordinasi::find()
                ->select('refskpd_id')
                ->where(['refuser_id' => $user->id])
                ->column();

            $allowedSkpdIds = $coordinatedSkpdIds;

            if (!empty($allowedSkpdIds)) {
                $skpdList = ArrayHelper::map(
                    SakipSkpd::find()->where(['refskpd_id' => $allowedSkpdIds, 'skpd_isaktif' => 'T'])->orderBy('nama_skpd ASC')->all(),
                    'refskpd_id',
                    'nama_skpd'
                );
            }
        }

        // --- Validasi Keamanan ---
        // Cek apakah refskpd_id dari URL diizinkan untuk diakses
        if ($refskpd_id !== null && !in_array($refskpd_id, $allowedSkpdIds)) {
            throw new ForbiddenHttpException('Anda tidak memiliki hak akses untuk melihat data SKPD ini.');
        }

        // Tentukan refskpd_id yang akan digunakan jika tidak ada di URL atau tidak valid
        if ($refskpd_id === null || !in_array($refskpd_id, $allowedSkpdIds)) {
            $refskpd_id = !empty($allowedSkpdIds) ? $allowedSkpdIds[0] : null;
        }

        // =========================================================================
        // AKHIR DARI BLOK LOGIKA BARU
        // Kode di bawah ini sekarang menggunakan $refskpd_id yang sudah aman
        // =========================================================================

        // Get the name of the SKPD based on refskpd_id
        $nama_skpd = $refskpd_id ? SakipSkpd::find()->select('nama_skpd')->where(['refskpd_id' => $refskpd_id])->scalar() : 'Tidak ada SKPD dipilih';

        // Set default period to this year if not provided
        if ($refperiode_id === null) {
            $currentYear = date('Y');
            $defaultPeriod = SakipPeriode::find()->where(['periode' => $currentYear])->one();
            $refperiode_id = $defaultPeriod ? $defaultPeriod->refperiode_id : null;
        }

        // Fetch all periods
        $periodeList = SakipPeriode::find()->all();

        // Logika query utama Anda tidak diubah
        $indikators = [];
        $quarterlyData = [];
        if ($refskpd_id && $refperiode_id) {
            $indikators = SakipIndikatorcascadingsubkegiatan::find()
                ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
                ->with(['refCascadingProgram', 'refProgram', 'refSasaranrenstra'])
                ->all();

            $quarterlyData = SakipIndikatorcascadingsubkegiatanTriwulan::find()
                ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
                ->all();
        }

        // Organize data for the view
        $data = [];
        foreach ($indikators as $indikator) {
            $sasaranId = $indikator->refsasaranrenstra_id;
            if (!isset($data[$sasaranId])) {
                $data[$sasaranId] = [
                    'uraian_sasaran' => $indikator->refSasaranrenstra ? $indikator->refSasaranrenstra->uraian_sasaranrenstra : 'N/A',
                    'programs' => [],
                    'total_anggaran' => 0,
                    'total_quarterly_penyerapan_anggaran' => [0, 0, 0, 0],
                    'total_quarterly_realisasi' => [0, 0, 0, 0]
                ];
            }

            // Add program details
            $programId = $indikator->refcascadingprogram_id;
            if (!isset($data[$sasaranId]['programs'][$programId])) {
                $data[$sasaranId]['programs'][$programId] = [
                    'nama_program' => $indikator->refProgram ? $indikator->refProgram->nama_program : 'N/A',
                    'anggaran_pk_p' => $indikator->anggaran_pk_p,
                    'quarterly' => []
                ];
                $data[$sasaranId]['total_anggaran'] += (float) $indikator->anggaran_pk_p;
            }
        }

        // Populate quarterly data
        foreach ($quarterlyData as $quarter) {
            $sasaranId = $quarter->refsasaranrenstra_id;
            $programId = $quarter->refcascadingprogram_id;

            if (isset($data[$sasaranId]['programs'][$programId])) {
                $quarterIndex = $quarter->reftriwulan_id - 1; // Assuming reftriwulan_id is 1 to 4 for quarters
                if ($quarterIndex >= 0 && $quarterIndex < 4) { // Safety check
                    $data[$sasaranId]['programs'][$programId]['quarterly'][$quarterIndex] = [
                        'triwulan_target_rkt' => $quarter->triwulan_target_rkt,
                        'triwulan_realisasi' => $quarter->triwulan_realisasi,
                        'triwulan_penyerapan_anggaran' => $quarter->triwulan_penyerapan_anggaran
                    ];

                    // Sum totals by quarter
                    $data[$sasaranId]['total_quarterly_penyerapan_anggaran'][$quarterIndex] += $quarter->triwulan_penyerapan_anggaran;
                    $data[$sasaranId]['total_quarterly_realisasi'][$quarterIndex] += $quarter->triwulan_realisasi;
                }
            }
        }

        // Retrieve the periode based on refperiode_id
        $selectedPeriod = SakipPeriode::find()->where(['refperiode_id' => $refperiode_id])->one();
        $selectedPeriodValue = $selectedPeriod ? $selectedPeriod->periode : null; // Get the periode value

        // Set the dynamic title
        $this->view->title = "Laporan Realisasi Anggaran $selectedPeriodValue - " . Html::encode($nama_skpd);

        return $this->render('index-laporan-realisasi-anggaran-dev', [
            'periodeList' => $periodeList,
            'selectedPeriodId' => $refperiode_id,
            'refskpd_id' => $refskpd_id,
            'refperiode_id' => $refperiode_id,
            'data' => $data,
            'nama_skpd' => $nama_skpd,
            'selectedPeriodValue' => $selectedPeriodValue,
            'selectedSkpdId' => $refskpd_id,
            'skpdList' => $skpdList,
        ]);
    }

    public function actionDownloadPdfRealisasiAnggaran()
    {
        if (Yii::$app->request->isPost) {
            $content = Yii::$app->request->post('content'); // Konten dari POST
            $title = Yii::$app->request->post('title', 'Laporan'); // Judul untuk nama file

            // Define CSS untuk styling PDF
            $css = <<<CSS
                    .title {
        border-collapse: collapse;
        font-family: 'Public Sans', sans-serif;
        /* Menggunakan font Public Sans */
        font-size: 11px;
        text-align: center;
    }

    .tbdata {
        border-collapse: collapse;
        font-family: "Bookman Old Style", "Verdana";
        font-size: 11px;
    }

    .tbdata th {
        height: 40px;
        background: rgb(0, 160, 221);
        text-align: center;
        border: 0.2px solid #cfcfcf;
        color: #ffffff;
        padding: 2px;
    }

    .tbdata td {
        padding: 2px;
        border: 0.2px solid #cfcfcf;
        vertical-align: top;
    }

    .tengah {
        text-align: center;
    }

    thead {
        display: table-header-group;
    }
    CSS;

            // Setting untuk file PDF dengan CSS
            $pdf = new Pdf([
                'mode' => Pdf::MODE_CORE,
                'format' => Pdf::FORMAT_A4,
                'orientation' => Pdf::ORIENT_PORTRAIT,
                'destination' => Pdf::DEST_DOWNLOAD,
                'filename' => $title . '.pdf',
                'content' => $content, // Konten dari POST
                'cssInline' => $css, // Gunakan CSS inline
                'options' => ['title' => $title],
                'methods' => [
                    'SetHeader' => [$title],
                    'SetFooter' => ['{PAGENO}'],
                ],
            ]);

            return $pdf->render(); // Generate dan kembalikan PDF untuk diunduh
        }
    }

    public function actionIndexLaporanRencanaAksi($refperiode_id = null)
    {
        // Get refskpd_id from the current user
        $user = Yii::$app->user->identity;
        $refskpd_id = $user->refskpd_id;

        // Get the name of the SKPD based on refskpd_id
        $nama_skpd = SakipSkpd::find()->select('nama_skpd')->where(['refskpd_id' => $refskpd_id])->scalar();

        // Set default period to this year if not provided
        if ($refperiode_id === null) {
            $currentYear = date('Y');
            $defaultPeriod = SakipPeriode::find()->where(['periode' => $currentYear])->one();
            $refperiode_id = $defaultPeriod ? $defaultPeriod->refperiode_id : null;
        }

        // Fetch all periods
        $periodeList = SakipPeriode::find()->all();

        // Fetch cascadingSubkegiatan along with associated data
        $cascadingSubkegiatan = SakipCascadingsubkegiatan::find()
            ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
            ->with([
                'refIndikatorcascadingsubkegiatan',
                'indikatorTriwulan' => function ($query) use ($refperiode_id, $refskpd_id) {
                    $query->andWhere(['refperiode_id' => $refperiode_id, 'refskpd_id' => $refskpd_id]);
                },
                'refCascadingKegiatan.refKegiatan',  // Related Kegiatan data
                'refCascadingKegiatan.refCascadingProgram.refProgram'  // Fetch Program info
            ])
            ->all();

        // Group the cascadingSubkegiatan by refcascadingkegiatan_id
        $groupedSubkegiatan = [];
        foreach ($cascadingSubkegiatan as $item) {
            $groupedSubkegiatan[$item->refCascadingKegiatan->refCascadingProgram->refProgram->refprogram_id][] = $item;
        }


        // Calculate total anggaran for each refcascadingkegiatan_id
        $anggaranPerKegiatan = [];
        foreach ($groupedSubkegiatan as $refcascadingkegiatan_id => $subkegiatanGroup) {
            $totalAnggaran = array_sum(array_column($subkegiatanGroup, 'subkegiatan_anggaran'));
            $anggaranPerKegiatan[$refcascadingkegiatan_id] = $totalAnggaran;
        }

        // Fetch anggaran per cascading program to display totals in the view
        $anggaranPerProgram = [];
        foreach ($cascadingSubkegiatan as $subkegiatan) {
            $programId = $subkegiatan->refCascadingKegiatan->refCascadingProgram->refProgram->refprogram_id;
            if (!isset($anggaranPerProgram[$programId])) {
                $anggaranPerProgram[$programId] = 0;
            }
            $anggaranPerProgram[$programId] += $subkegiatan->subkegiatan_anggaran;
        }


        // Retrieve the periode based on refperiode_id
        $selectedPeriod = SakipPeriode::find()->where(['refperiode_id' => $refperiode_id])->one();
        $selectedPeriodValue = $selectedPeriod ? $selectedPeriod->periode : null; // Get the periode value

        // Set the dynamic title
        $this->view->title = "Laporan Rencana Kinerja Tahun $selectedPeriodValue - " . Html::encode($nama_skpd);

        return $this->render('index-laporan-rencana-aksi', [
            'periodeList' => $periodeList,
            'selectedPeriodId' => $refperiode_id,
            'refskpd_id' => $refskpd_id,
            'refperiode_id' => $refperiode_id,
            'cascadingSubkegiatan' => $cascadingSubkegiatan,
            'groupedSubkegiatan' => $groupedSubkegiatan,
            'anggaranPerKegiatan' => $anggaranPerKegiatan,
            'anggaranPerProgram' => $anggaranPerProgram,
            'nama_skpd' => $nama_skpd,
            'selectedPeriodValue' => $selectedPeriodValue, // Include selected period value
        ]);
    }

    // public function actionIndexLaporanRencanaAksiDev($refperiode_id = null, $refskpd_id = null)
    // {
    //     // Ambil refskpd_id dari user saat ini jika tidak ada di request
    //     if ($refskpd_id === null) {
    //         $user = Yii::$app->user->identity;
    //         $refskpd_id = $user->refskpd_id;
    //     }

    //     // Get the name of the SKPD based on refskpd_id
    //     $nama_skpd = SakipSkpd::find()->select('nama_skpd')->where(['refskpd_id' => $refskpd_id])->scalar();

    //     // Set default period to this year if not provided
    //     if ($refperiode_id === null) {
    //         $currentYear = date('Y');
    //         $defaultPeriod = SakipPeriode::find()->where(['periode' => $currentYear])->one();
    //         $refperiode_id = $defaultPeriod ? $defaultPeriod->refperiode_id : null;
    //     }

    //     // Fetch all periods
    //     $periodeList = SakipPeriode::find()->all();

    //     // Fetch cascadingSubkegiatan along with associated data
    //     $cascadingSubkegiatan = SakipCascadingsubkegiatan::find()
    //         ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
    //         ->with([
    //             'refIndikatorcascadingsubkegiatan',
    //             'indikatorTriwulan' => function ($query) use ($refperiode_id, $refskpd_id) {
    //                 $query->andWhere(['refperiode_id' => $refperiode_id, 'refskpd_id' => $refskpd_id]);
    //             },
    //             'refCascadingKegiatan.refKegiatan',  // Related Kegiatan data
    //             'refCascadingKegiatan.refCascadingProgram.refProgram'  // Fetch Program info
    //         ])
    //         ->all();

    //     // Group the cascadingSubkegiatan by refcascadingkegiatan_id
    //     $groupedSubkegiatan = [];
    //     foreach ($cascadingSubkegiatan as $item) {
    //         $groupedSubkegiatan[$item->refCascadingKegiatan->refCascadingProgram->refProgram->refprogram_id][] = $item;
    //     }


    //     // Calculate total anggaran for each refcascadingkegiatan_id
    //     $anggaranPerKegiatan = [];
    //     foreach ($groupedSubkegiatan as $refcascadingkegiatan_id => $subkegiatanGroup) {
    //         $totalAnggaran = array_sum(array_column($subkegiatanGroup, 'subkegiatan_anggaran'));
    //         $anggaranPerKegiatan[$refcascadingkegiatan_id] = $totalAnggaran;
    //     }

    //     // Fetch anggaran per cascading program to display totals in the view
    //     $anggaranPerProgram = [];
    //     foreach ($cascadingSubkegiatan as $subkegiatan) {
    //         $programId = $subkegiatan->refCascadingKegiatan->refCascadingProgram->refProgram->refprogram_id;
    //         if (!isset($anggaranPerProgram[$programId])) {
    //             $anggaranPerProgram[$programId] = 0;
    //         }
    //         $anggaranPerProgram[$programId] += $subkegiatan->subkegiatan_anggaran;
    //     }

    //     // Ambil daftar SKPD untuk dropdown
    //     $skpdList = ArrayHelper::map(SakipSkpd::find()->all(), 'refskpd_id', 'nama_skpd');

    //     // Retrieve the periode based on refperiode_id
    //     $selectedPeriod = SakipPeriode::find()->where(['refperiode_id' => $refperiode_id])->one();
    //     $selectedPeriodValue = $selectedPeriod ? $selectedPeriod->periode : null; // Get the periode value

    //     // Set the dynamic title
    //     $this->view->title = "Laporan Rencana Kinerja Tahun $selectedPeriodValue - " . Html::encode($nama_skpd);

    //     return $this->render('index-laporan-rencana-aksi-dev', [
    //         'periodeList' => $periodeList,
    //         'selectedPeriodId' => $refperiode_id,
    //         'refskpd_id' => $refskpd_id,
    //         'refperiode_id' => $refperiode_id,
    //         'cascadingSubkegiatan' => $cascadingSubkegiatan,
    //         'groupedSubkegiatan' => $groupedSubkegiatan,
    //         'anggaranPerKegiatan' => $anggaranPerKegiatan,
    //         'anggaranPerProgram' => $anggaranPerProgram,
    //         'nama_skpd' => $nama_skpd,
    //         'selectedPeriodValue' => $selectedPeriodValue, // Include selected period value
    //         'selectedSkpdId' => $refskpd_id,
    //         'skpdList' => $skpdList,
    //     ]);
    // }

    public function actionIndexLaporanRencanaAksiDev($refperiode_id = null, $refskpd_id = null)
    {
        // =========================================================================
        // BLOK LOGIKA BARU UNTUK KEAMANAN SKPD
        // =========================================================================

        $user = Yii::$app->user->identity;
        $assignments = Yii::$app->authManager->getAssignments($user->id);

        $skpdList = [];
        $allowedSkpdIds = []; // Daftar ID SKPD yang diizinkan untuk user ini

        if (isset($assignments['superadmin']) || isset($assignments['admin'])) {
            // --- BLOK UNTUK ADMIN ---
            $allSkpd = SakipSkpd::find()->where(['skpd_isaktif' => 'T'])->orderBy('nama_skpd ASC')->all();
            $skpdList = ArrayHelper::map($allSkpd, 'refskpd_id', 'nama_skpd');
            $allowedSkpdIds = array_keys($skpdList);
        } else {
            // --- BLOK UNTUK NON-ADMIN (KOORDINATOR) ---
            $coordinatedSkpdIds = SakipKoordinasi::find()
                ->select('refskpd_id')
                ->where(['refuser_id' => $user->id])
                ->column();

            $allowedSkpdIds = $coordinatedSkpdIds;

            if (!empty($allowedSkpdIds)) {
                $skpdList = ArrayHelper::map(
                    SakipSkpd::find()->where(['refskpd_id' => $allowedSkpdIds, 'skpd_isaktif' => 'T'])->orderBy('nama_skpd ASC')->all(),
                    'refskpd_id',
                    'nama_skpd'
                );
            }
        }

        // --- Validasi Keamanan ---
        // Cek apakah refskpd_id dari URL diizinkan untuk diakses
        if ($refskpd_id !== null && !in_array($refskpd_id, $allowedSkpdIds)) {
            throw new ForbiddenHttpException('Anda tidak memiliki hak akses untuk melihat data SKPD ini.');
        }

        // Tentukan refskpd_id yang akan digunakan jika tidak ada di URL atau tidak valid
        if ($refskpd_id === null || !in_array($refskpd_id, $allowedSkpdIds)) {
            $refskpd_id = !empty($allowedSkpdIds) ? $allowedSkpdIds[0] : null;
        }

        // =========================================================================
        // AKHIR DARI BLOK LOGIKA BARU
        // Kode di bawah ini sekarang menggunakan $refskpd_id yang sudah aman
        // =========================================================================

        // Get the name of the SKPD based on refskpd_id
        $nama_skpd = $refskpd_id ? SakipSkpd::find()->select('nama_skpd')->where(['refskpd_id' => $refskpd_id])->scalar() : 'Tidak ada SKPD dipilih';

        // Set default period to this year if not provided
        if ($refperiode_id === null) {
            $currentYear = date('Y');
            $defaultPeriod = SakipPeriode::find()->where(['periode' => $currentYear])->one();
            $refperiode_id = $defaultPeriod ? $defaultPeriod->refperiode_id : null;
        }

        // Fetch all periods
        $periodeList = SakipPeriode::find()->all();

        // Logika query utama Anda tidak diubah
        $cascadingSubkegiatan = [];
        if ($refskpd_id && $refperiode_id) {
            $cascadingSubkegiatan = SakipCascadingsubkegiatan::find()
                ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
                ->with([
                    'refIndikatorcascadingsubkegiatan',
                    'indikatorTriwulan' => function ($query) use ($refperiode_id, $refskpd_id) {
                        $query->andWhere(['refperiode_id' => $refperiode_id, 'refskpd_id' => $refskpd_id]);
                    },
                    'refCascadingKegiatan.refKegiatan',  // Related Kegiatan data
                    'refCascadingKegiatan.refCascadingProgram.refProgram'  // Fetch Program info
                ])
                ->all();
        }

        // Group the cascadingSubkegiatan by refcascadingkegiatan_id
        $groupedSubkegiatan = [];
        foreach ($cascadingSubkegiatan as $item) {
            if (isset($item->refCascadingKegiatan->refCascadingProgram->refProgram->refprogram_id)) {
                $groupedSubkegiatan[$item->refCascadingKegiatan->refCascadingProgram->refProgram->refprogram_id][] = $item;
            }
        }


        // Calculate total anggaran for each refcascadingkegiatan_id
        $anggaranPerKegiatan = [];
        foreach ($groupedSubkegiatan as $refcascadingkegiatan_id => $subkegiatanGroup) {
            $totalAnggaran = array_sum(array_column($subkegiatanGroup, 'subkegiatan_anggaran'));
            $anggaranPerKegiatan[$refcascadingkegiatan_id] = $totalAnggaran;
        }

        // Fetch anggaran per cascading program to display totals in the view
        $anggaranPerProgram = [];
        foreach ($cascadingSubkegiatan as $subkegiatan) {
            if (isset($subkegiatan->refCascadingKegiatan->refCascadingProgram->refProgram->refprogram_id)) {
                $programId = $subkegiatan->refCascadingKegiatan->refCascadingProgram->refProgram->refprogram_id;
                if (!isset($anggaranPerProgram[$programId])) {
                    $anggaranPerProgram[$programId] = 0;
                }
                $anggaranPerProgram[$programId] += $subkegiatan->subkegiatan_anggaran;
            }
        }

        // Retrieve the periode based on refperiode_id
        $selectedPeriod = SakipPeriode::find()->where(['refperiode_id' => $refperiode_id])->one();
        $selectedPeriodValue = $selectedPeriod ? $selectedPeriod->periode : null; // Get the periode value

        // Set the dynamic title
        $this->view->title = "Laporan Rencana Aksi $selectedPeriodValue - " . Html::encode($nama_skpd);

        return $this->render('index-laporan-rencana-aksi-dev', [
            'periodeList' => $periodeList,
            'selectedPeriodId' => $refperiode_id,
            'refskpd_id' => $refskpd_id,
            'refperiode_id' => $refperiode_id,
            'cascadingSubkegiatan' => $cascadingSubkegiatan,
            'groupedSubkegiatan' => $groupedSubkegiatan,
            'anggaranPerKegiatan' => $anggaranPerKegiatan,
            'anggaranPerProgram' => $anggaranPerProgram,
            'nama_skpd' => $nama_skpd,
            'selectedPeriodValue' => $selectedPeriodValue, // Include selected period value
            'selectedSkpdId' => $refskpd_id,
            'skpdList' => $skpdList,
        ]);
    }

    public function actionDownloadPdfRencanaAksi()
    {
        if (Yii::$app->request->isPost) {
            $content = Yii::$app->request->post('content'); // Konten dari POST
            $title = Yii::$app->request->post('title', 'Laporan'); // Judul untuk nama file

            // Define CSS untuk styling PDF
            $css = <<<CSS
                             #loadingOverlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        /* semi-transparent background */
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        /* Make sure it overlays above everything */
    }

    /*  */
    .title {
        border-collapse: collapse;
        font-family: 'Public Sans', sans-serif;
        /* Menggunakan font Public Sans */
        font-size: 11px;
        text-align: center;
    }

    .judultabel {
        margin-top: 0px;
        font-family: 'Bookman Old Style', 'Verdana';
        font-size: 12px;
        font-weight: bold;
    }

    .tbdata {
        border-collapse: collapse;
        font-family: 'Bookman Old Style', 'Verdana';
        font-size: 10px;
        /* border: 1px solid #5d5d5d; */
    }

    .tbdata th {
        height: 40px;
        background: rgb(0, 160, 221);
        text-align: center;
        border: 1px solid #5d5d5d;
        color: #ffffff;
        padding: 3px 5px;
    }

    .tbdata td {
        padding: 3px 5px;
        border: 1px solid #5d5d5d;
        vertical-align: top;
    }

    .tbdata td.tdisi {
        border-bottom: 0px;
    }

    .tbdata td.tdkosong {
        border-bottom: 0px;
        border-top: 0px;
        visibility: hidden;
    }

    .tengah {
        text-align: center;
    }

    .kanan {
        text-align: right;
    }

    thead {
        display: table-header-group;
    }
    CSS;

            // Setting untuk file PDF dengan CSS
            $pdf = new Pdf([
                'mode' => Pdf::MODE_CORE,
                'format' => Pdf::FORMAT_A4,
                'orientation' => Pdf::ORIENT_PORTRAIT,
                'destination' => Pdf::DEST_DOWNLOAD,
                'filename' => $title . '.pdf',
                'content' => $content, // Konten dari POST
                'cssInline' => $css, // Gunakan CSS inline
                'options' => ['title' => $title],
                'methods' => [
                    'SetHeader' => [$title],
                    'SetFooter' => ['{PAGENO}'],
                ],
            ]);

            return $pdf->render(); // Generate dan kembalikan PDF untuk diunduh
        }
    }

    public function actionIndexLaporanEkinerja($refperiode_id = null)
    {
        // Get refskpd_id from the current user
        $user = Yii::$app->user->identity;
        $refskpd_id = $user->refskpd_id;

        // Get the name of the SKPD based on refskpd_id
        $nama_skpd = SakipSkpd::find()->select('nama_skpd')->where(['refskpd_id' => $refskpd_id])->scalar();

        // Set default period to this year if not provided
        if ($refperiode_id === null) {
            $currentYear = date('Y');
            $defaultPeriod = SakipPeriode::find()->where(['periode' => $currentYear])->one();
            $refperiode_id = $defaultPeriod ? $defaultPeriod->refperiode_id : null;
        }

        // Fetch all periods
        $periodeList = SakipPeriode::find()->all();

        // Fetch Sasaran Renstra with related indicators
        $sasaranRenstra = SakipSasaranrenstra::find()
            ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
            ->with([
                'indikatorSasaran' => function ($query) {
                    $query->with(['cascadingPrograms.refProgram']); // Load refProgram details for each cascading program
                }
            ])
            ->all();



        // Retrieve the periode based on refperiode_id
        $selectedPeriod = SakipPeriode::find()->where(['refperiode_id' => $refperiode_id])->one();
        $selectedPeriodValue = $selectedPeriod ? $selectedPeriod->periode : null; // Get the periode value

        // Set the dynamic title
        $this->view->title = "Laporan Rencana Kinerja Tahun $selectedPeriodValue - " . Html::encode($nama_skpd);

        return $this->render('index-laporan-ekinerja', [
            'periodeList' => $periodeList,
            'selectedPeriodId' => $refperiode_id,
            'refskpd_id' => $refskpd_id,
            'refperiode_id' => $refperiode_id,
            'sasaranRenstra' => $sasaranRenstra,
            'nama_skpd' => $nama_skpd,
            'selectedPeriodValue' => $selectedPeriodValue, // Include selected period value
        ]);
    }

    // public function actionIndexLaporanEkinerjaDev($refperiode_id = null, $refskpd_id = null)
    // {
    //     // Ambil refskpd_id dari user saat ini jika tidak ada di request
    //     if ($refskpd_id === null) {
    //         $user = Yii::$app->user->identity;
    //         $refskpd_id = $user->refskpd_id;
    //     }

    //     // Get the name of the SKPD based on refskpd_id
    //     $nama_skpd = SakipSkpd::find()->select('nama_skpd')->where(['refskpd_id' => $refskpd_id])->scalar();

    //     // Set default period to this year if not provided
    //     if ($refperiode_id === null) {
    //         $currentYear = date('Y');
    //         $defaultPeriod = SakipPeriode::find()->where(['periode' => $currentYear])->one();
    //         $refperiode_id = $defaultPeriod ? $defaultPeriod->refperiode_id : null;
    //     }

    //     // Fetch all periods
    //     $periodeList = SakipPeriode::find()->all();

    //     // Fetch Sasaran Renstra with related indicators
    //     $sasaranRenstra = SakipSasaranrenstra::find()
    //         ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
    //         ->with([
    //             'indikatorSasaran' => function ($query) {
    //                 $query->with(['cascadingPrograms.refProgram']); // Load refProgram details for each cascading program
    //             }
    //         ])
    //         ->all();

    //     // Ambil daftar SKPD untuk dropdown
    //     $skpdList = ArrayHelper::map(SakipSkpd::find()->all(), 'refskpd_id', 'nama_skpd');

    //     // Retrieve the periode based on refperiode_id
    //     $selectedPeriod = SakipPeriode::find()->where(['refperiode_id' => $refperiode_id])->one();
    //     $selectedPeriodValue = $selectedPeriod ? $selectedPeriod->periode : null; // Get the periode value

    //     // Set the dynamic title
    //     $this->view->title = "Laporan Rencana Kinerja Tahun $selectedPeriodValue - " . Html::encode($nama_skpd);

    //     return $this->render('index-laporan-ekinerja-dev', [
    //         'periodeList' => $periodeList,
    //         'selectedPeriodId' => $refperiode_id,
    //         'refskpd_id' => $refskpd_id,
    //         'refperiode_id' => $refperiode_id,
    //         'sasaranRenstra' => $sasaranRenstra,
    //         'nama_skpd' => $nama_skpd,
    //         'selectedPeriodValue' => $selectedPeriodValue, // Include selected period value
    //         'selectedSkpdId' => $refskpd_id,
    //         'skpdList' => $skpdList,
    //     ]);
    // }

    public function actionIndexLaporanEkinerjaDev($refperiode_id = null, $refskpd_id = null)
    {
        // =========================================================================
        // BLOK LOGIKA BARU UNTUK KEAMANAN SKPD
        // =========================================================================

        $user = Yii::$app->user->identity;
        $assignments = Yii::$app->authManager->getAssignments($user->id);

        $skpdList = [];
        $allowedSkpdIds = []; // Daftar ID SKPD yang diizinkan untuk user ini

        if (isset($assignments['superadmin']) || isset($assignments['admin'])) {
            // --- BLOK UNTUK ADMIN ---
            $allSkpd = SakipSkpd::find()->where(['skpd_isaktif' => 'T'])->orderBy('nama_skpd ASC')->all();
            $skpdList = ArrayHelper::map($allSkpd, 'refskpd_id', 'nama_skpd');
            $allowedSkpdIds = array_keys($skpdList);
        } else {
            // --- BLOK UNTUK NON-ADMIN (KOORDINATOR) ---
            $coordinatedSkpdIds = SakipKoordinasi::find()
                ->select('refskpd_id')
                ->where(['refuser_id' => $user->id])
                ->column();

            $allowedSkpdIds = $coordinatedSkpdIds;

            if (!empty($allowedSkpdIds)) {
                $skpdList = ArrayHelper::map(
                    SakipSkpd::find()->where(['refskpd_id' => $allowedSkpdIds, 'skpd_isaktif' => 'T'])->orderBy('nama_skpd ASC')->all(),
                    'refskpd_id',
                    'nama_skpd'
                );
            }
        }

        // --- Validasi Keamanan ---
        // Cek apakah refskpd_id dari URL diizinkan untuk diakses
        if ($refskpd_id !== null && !in_array($refskpd_id, $allowedSkpdIds)) {
            throw new ForbiddenHttpException('Anda tidak memiliki hak akses untuk melihat data SKPD ini.');
        }

        // Tentukan refskpd_id yang akan digunakan jika tidak ada di URL atau tidak valid
        if ($refskpd_id === null || !in_array($refskpd_id, $allowedSkpdIds)) {
            $refskpd_id = !empty($allowedSkpdIds) ? $allowedSkpdIds[0] : null;
        }

        // =========================================================================
        // AKHIR DARI BLOK LOGIKA BARU
        // Kode di bawah ini sekarang menggunakan $refskpd_id yang sudah aman
        // =========================================================================

        // Get the name of the SKPD based on refskpd_id
        $nama_skpd = $refskpd_id ? SakipSkpd::find()->select('nama_skpd')->where(['refskpd_id' => $refskpd_id])->scalar() : 'Tidak ada SKPD dipilih';

        // Set default period to this year if not provided
        if ($refperiode_id === null) {
            $currentYear = date('Y');
            $defaultPeriod = SakipPeriode::find()->where(['periode' => $currentYear])->one();
            $refperiode_id = $defaultPeriod ? $defaultPeriod->refperiode_id : null;
        }

        // Fetch all periods
        $periodeList = SakipPeriode::find()->all();

        // Logika query utama Anda tidak diubah
        $sasaranRenstra = [];
        if ($refskpd_id && $refperiode_id) {
            $sasaranRenstra = SakipSasaranrenstra::find()
                ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
                ->with([
                    'indikatorSasaran' => function ($query) {
                        $query->with(['cascadingPrograms.refProgram']); // Load refProgram details for each cascading program
                    }
                ])
                ->all();
        }

        // Retrieve the periode based on refperiode_id
        $selectedPeriod = SakipPeriode::find()->where(['refperiode_id' => $refperiode_id])->one();
        $selectedPeriodValue = $selectedPeriod ? $selectedPeriod->periode : null; // Get the periode value

        // Set the dynamic title
        $this->view->title = "Laporan E-Kinerja $selectedPeriodValue - " . Html::encode($nama_skpd);

        return $this->render('index-laporan-ekinerja-dev', [
            'periodeList' => $periodeList,
            'selectedPeriodId' => $refperiode_id,
            'refskpd_id' => $refskpd_id,
            'refperiode_id' => $refperiode_id,
            'sasaranRenstra' => $sasaranRenstra,
            'nama_skpd' => $nama_skpd,
            'selectedPeriodValue' => $selectedPeriodValue, // Include selected period value
            'selectedSkpdId' => $refskpd_id,
            'skpdList' => $skpdList,
        ]);
    }

    public function actionDownloadPdfEkinerja()
    {
        if (Yii::$app->request->isPost) {
            $content = Yii::$app->request->post('content'); // Konten dari POST
            $title = Yii::$app->request->post('title', 'Laporan'); // Judul untuk nama file

            // Define CSS untuk styling PDF
            $css = <<<CSS
                                #loadingOverlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        /* semi-transparent background */
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        /* Make sure it overlays above everything */
    }

    /*  */
    .title {
        border-collapse: collapse;
        font-family: 'Public Sans', sans-serif;
        /* Menggunakan font Public Sans */
        font-size: 11px;
        text-align: center;
    }

    .judultabel {
        margin-top: 0px;
        font-family: 'Public Sans', sans-serif;
        font-size: 12px;
        font-weight: bold;
    }

    .tbdata {
        border-collapse: collapse;
        font-family: 'Public Sans', sans-serif;
        font-size: 10px;
        /* border: 1px solid #5d5d5d; */
    }

    .tbdata th {
        height: 40px;
        background: rgb(0, 160, 221);
        text-align: center;
        border: 1px solid #5d5d5d;
        color: #ffffff;
        padding: 2px;
    }

    .tbdata td {
        padding: 2px;
        border: 1px solid #5d5d5d;
        vertical-align: top;
    }

    .tbdata td.tdisi {
        border-bottom: 0px;
    }

    .tbdata td.tdkosong {
        border-bottom: 0px;
        border-top: 0px;
        visibility: hidden;
    }

    .tengah {
        text-align: center;
    }

    .kanan {
        text-align: right;
    }

    thead {
        display: table-header-group;
    }

    .keterangan {
        border-collapse: collapse;
    }

    .keterangan td {
        padding: 5px;
        border: 1px solid #e3e3e3;
        font-size: 12px;
    }

    .tblprogram {
        width: 100%;
    }

    .tblprogram td {
        padding: 2px;
        border-left: 1px solid #c3c3c3;
        border-bottom: 1px solid #c3c3c3;
        font-size: 10px;
        vertical-align: top;
    }

    .tblprogram th {
        padding: 2px;
        border-left: 1px solid #e3e3e3;
        font-size: 10px;
        text-align: center;
        height: 30px;
    }

    .tblprogram tr.odd {
        background-color: #fafafa;
    }

    .tblprogram tr.evn {
        background-color: #e0e0e0;
    }

    #tblSasaran tr.ganjil {
        background-color: #ffffff;
    }

    #tblSasaran tr.genap {
        background-color: #f0f4f4;
    }

    th.head1 {
        vertical-align: middle;
        background: #2980b9;
        color: #fff;
    }

    th.head2 {
        vertical-align: middle;
        background: #16a085;
        color: #fff;
    }

    th.head2a {
        vertical-align: middle;
        background: #1abc9c;
        color: #fff;
    }

    th.head3 {
        vertical-align: middle;
        background: #27ae60;
        color: #fff;
    }

    th.head3a {
        vertical-align: middle;
        background: #2ecc71;
        color: #fff;
    }
    CSS;

            // Setting untuk file PDF dengan CSS
            $pdf = new Pdf([
                'mode' => Pdf::MODE_CORE,
                'format' => Pdf::FORMAT_A4,
                'orientation' => Pdf::ORIENT_PORTRAIT,
                'destination' => Pdf::DEST_DOWNLOAD,
                'filename' => $title . '.pdf',
                'content' => $content, // Konten dari POST
                'cssInline' => $css, // Gunakan CSS inline
                'options' => ['title' => $title],
                'methods' => [
                    'SetHeader' => [$title],
                    'SetFooter' => ['{PAGENO}'],
                ],
            ]);

            return $pdf->render(); // Generate dan kembalikan PDF untuk diunduh
        }
    }

    public function actionIndexLaporanAnalisisSasaranTriwulan($refperiode_id = null, $reftriwulan_id = null, $refsasaranrenstra_id = null)
    {
        // Get refskpd_id from the current user
        $user = Yii::$app->user->identity;
        $refskpd_id = $user->refskpd_id;

        // Get the name of the SKPD based on refskpd_id
        $nama_skpd = SakipSkpd::find()->select('nama_skpd')->where(['refskpd_id' => $refskpd_id])->scalar();

        // Set default period to this year if not provided
        if ($refperiode_id === null) {
            $currentYear = date('Y');
            $defaultPeriod = SakipPeriode::find()->where(['periode' => $currentYear])->one();
            $refperiode_id = $defaultPeriod ? $defaultPeriod->refperiode_id : null;
        }

        // Set default triwulan to 1 if not provided
        if ($reftriwulan_id === null) {
            $reftriwulan_id = 1;
        }

        // Fetch all periods
        $periodeList = SakipPeriode::find()->all();

        // Indicators list based on SKPD and selected period
        $sasaranRenstraList = SakipSasaranrenstra::find()
            ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
            ->all();

        // Fetch filtered data based on selected indicators and triwulan
        $indikators = SakipIndikatorsasaranrenstraTriwulan::find()
            ->where([
                'refskpd_id' => $refskpd_id,
                'refperiode_id' => $refperiode_id,
                'reftriwulan_id' => $reftriwulan_id,
            ])
            ->andFilterWhere(['refsasaranrenstra_id' => $refsasaranrenstra_id])
            ->all();

        // Retrieve the uraian_sasaranrenstra based on refsasaranrenstra_id
        $selectedSasaranRenstra = SakipSasaranrenstra::find()
            ->where(['refsasaranrenstra_id' => $refsasaranrenstra_id])
            ->select('uraian_sasaranrenstra')
            ->scalar();


        // Retrieve the periode based on refperiode_id
        $selectedPeriod = SakipPeriode::find()->where(['refperiode_id' => $refperiode_id])->one();
        $selectedPeriodValue = $selectedPeriod ? $selectedPeriod->periode : null; // Get the periode value

        // Set the dynamic title
        $this->view->title = "Laporan Laporan Analisis Pencapaian Sasaran per Triwulan $reftriwulan_id -  $selectedPeriodValue - " . Html::encode($nama_skpd);

        return $this->render('index-laporan-analisis-sasaran-triwulan', [
            'periodeList' => $periodeList,
            'selectedPeriodId' => $refperiode_id,
            'refskpd_id' => $refskpd_id,
            'refperiode_id' => $refperiode_id,
            'sasaranRenstraList' => $sasaranRenstraList,
            'indikators' => $indikators,
            'nama_skpd' => $nama_skpd,
            'selectedPeriodValue' => $selectedPeriodValue, // Include selected period value
            'selectedTriwulanId' => $reftriwulan_id, // Include selected triwulan id
            'selectedSasaranRenstraId' => $refsasaranrenstra_id, // Include selected triwulan id
            'selectedSasaranRenstraUraian' => $selectedSasaranRenstra, // Include selected sasaran uraian
        ]);
    }

    // public function actionIndexLaporanAnalisisSasaranTriwulanDev($refperiode_id = null, $reftriwulan_id = null, $refsasaranrenstra_id = null, $refskpd_id = null)
    // {
    //     // Ambil refskpd_id dari user saat ini jika tidak ada di request
    //     if ($refskpd_id === null) {
    //         $user = Yii::$app->user->identity;
    //         $refskpd_id = $user->refskpd_id;
    //     }

    //     // Get the name of the SKPD based on refskpd_id
    //     $nama_skpd = SakipSkpd::find()->select('nama_skpd')->where(['refskpd_id' => $refskpd_id])->scalar();

    //     // Set default period to this year if not provided
    //     if ($refperiode_id === null) {
    //         $currentYear = date('Y');
    //         $defaultPeriod = SakipPeriode::find()->where(['periode' => $currentYear])->one();
    //         $refperiode_id = $defaultPeriod ? $defaultPeriod->refperiode_id : null;
    //     }

    //     // Set default triwulan to 1 if not provided
    //     if ($reftriwulan_id === null) {
    //         $reftriwulan_id = 1;
    //     }

    //     // Fetch all periods
    //     $periodeList = SakipPeriode::find()->all();

    //     // Indicators list based on SKPD and selected period
    //     $sasaranRenstraList = SakipSasaranrenstra::find()
    //         ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
    //         ->all();

    //     // Fetch filtered data based on selected indicators and triwulan
    //     $indikators = SakipIndikatorsasaranrenstraTriwulan::find()
    //         ->where([
    //             'refskpd_id' => $refskpd_id,
    //             'refperiode_id' => $refperiode_id,
    //             'reftriwulan_id' => $reftriwulan_id,
    //         ])
    //         ->andFilterWhere(['refsasaranrenstra_id' => $refsasaranrenstra_id])
    //         ->all();

    //     // Retrieve the uraian_sasaranrenstra based on refsasaranrenstra_id
    //     $selectedSasaranRenstra = SakipSasaranrenstra::find()
    //         ->where(['refsasaranrenstra_id' => $refsasaranrenstra_id])
    //         ->select('uraian_sasaranrenstra')
    //         ->scalar();

    //     // Ambil daftar SKPD untuk dropdown
    //     $skpdList = ArrayHelper::map(SakipSkpd::find()->all(), 'refskpd_id', 'nama_skpd');

    //     // Retrieve the periode based on refperiode_id
    //     $selectedPeriod = SakipPeriode::find()->where(['refperiode_id' => $refperiode_id])->one();
    //     $selectedPeriodValue = $selectedPeriod ? $selectedPeriod->periode : null; // Get the periode value

    //     // Set the dynamic title
    //     $this->view->title = "Laporan Laporan Analisis Pencapaian Sasaran per Triwulan $reftriwulan_id -  $selectedPeriodValue - " . Html::encode($nama_skpd);

    //     return $this->render('index-laporan-analisis-sasaran-triwulan-dev', [
    //         'periodeList' => $periodeList,
    //         'selectedPeriodId' => $refperiode_id,
    //         'refskpd_id' => $refskpd_id,
    //         'refperiode_id' => $refperiode_id,
    //         'sasaranRenstraList' => $sasaranRenstraList,
    //         'indikators' => $indikators,
    //         'nama_skpd' => $nama_skpd,
    //         'selectedPeriodValue' => $selectedPeriodValue, // Include selected period value
    //         'selectedTriwulanId' => $reftriwulan_id, // Include selected triwulan id
    //         'selectedSasaranRenstraId' => $refsasaranrenstra_id, // Include selected triwulan id
    //         'selectedSasaranRenstraUraian' => $selectedSasaranRenstra, // Include selected sasaran uraian
    //         'selectedSkpdId' => $refskpd_id,
    //         'skpdList' => $skpdList,
    //     ]);
    // }

    public function actionIndexLaporanAnalisisSasaranTriwulanDev($refperiode_id = null, $reftriwulan_id = null, $refsasaranrenstra_id = null, $refskpd_id = null)
    {
        // =========================================================================
        // BLOK LOGIKA BARU UNTUK KEAMANAN SKPD
        // =========================================================================

        $user = Yii::$app->user->identity;
        $assignments = Yii::$app->authManager->getAssignments($user->id);

        $skpdList = [];
        $allowedSkpdIds = []; // Daftar ID SKPD yang diizinkan untuk user ini

        if (isset($assignments['superadmin']) || isset($assignments['admin'])) {
            // --- BLOK UNTUK ADMIN ---
            $allSkpd = SakipSkpd::find()->where(['skpd_isaktif' => 'T'])->orderBy('nama_skpd ASC')->all();
            $skpdList = ArrayHelper::map($allSkpd, 'refskpd_id', 'nama_skpd');
            $allowedSkpdIds = array_keys($skpdList);
        } else {
            // --- BLOK UNTUK NON-ADMIN (KOORDINATOR) ---
            $coordinatedSkpdIds = SakipKoordinasi::find()
                ->select('refskpd_id')
                ->where(['refuser_id' => $user->id])
                ->column();

            $allowedSkpdIds = $coordinatedSkpdIds;

            if (!empty($allowedSkpdIds)) {
                $skpdList = ArrayHelper::map(
                    SakipSkpd::find()->where(['refskpd_id' => $allowedSkpdIds, 'skpd_isaktif' => 'T'])->orderBy('nama_skpd ASC')->all(),
                    'refskpd_id',
                    'nama_skpd'
                );
            }
        }

        // --- Validasi Keamanan ---
        // Cek apakah refskpd_id dari URL diizinkan untuk diakses
        if ($refskpd_id !== null && !in_array($refskpd_id, $allowedSkpdIds)) {
            throw new ForbiddenHttpException('Anda tidak memiliki hak akses untuk melihat data SKPD ini.');
        }

        // Tentukan refskpd_id yang akan digunakan jika tidak ada di URL atau tidak valid
        if ($refskpd_id === null || !in_array($refskpd_id, $allowedSkpdIds)) {
            $refskpd_id = !empty($allowedSkpdIds) ? $allowedSkpdIds[0] : null;
        }

        // =========================================================================
        // AKHIR DARI BLOK LOGIKA BARU
        // Kode di bawah ini sekarang menggunakan $refskpd_id yang sudah aman
        // =========================================================================

        // Get the name of the SKPD based on refskpd_id
        $nama_skpd = $refskpd_id ? SakipSkpd::find()->select('nama_skpd')->where(['refskpd_id' => $refskpd_id])->scalar() : 'Tidak ada SKPD dipilih';

        // Set default period to this year if not provided
        if ($refperiode_id === null) {
            $currentYear = date('Y');
            $defaultPeriod = SakipPeriode::find()->where(['periode' => $currentYear])->one();
            $refperiode_id = $defaultPeriod ? $defaultPeriod->refperiode_id : null;
        }

        // Set default triwulan to 1 if not provided
        if ($reftriwulan_id === null) {
            $reftriwulan_id = 1;
        }

        // Fetch all periods
        $periodeList = SakipPeriode::find()->all();

        // Logika query utama Anda tidak diubah, namun ditambahkan pengecekan null
        $sasaranRenstraList = [];
        $indikators = [];

        if ($refskpd_id && $refperiode_id) {
            // Indicators list based on SKPD and selected period
            $sasaranRenstraList = SakipSasaranrenstra::find()
                ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
                ->all();

            // Fetch filtered data based on selected indicators and triwulan
            $indikators = SakipIndikatorsasaranrenstraTriwulan::find()
                ->where([
                    'refskpd_id' => $refskpd_id,
                    'refperiode_id' => $refperiode_id,
                    'reftriwulan_id' => $reftriwulan_id,
                ])
                ->andFilterWhere(['refsasaranrenstra_id' => $refsasaranrenstra_id])
                ->all();
        }

        // Retrieve the uraian_sasaranrenstra based on refsasaranrenstra_id
        $selectedSasaranRenstra = $refsasaranrenstra_id ? SakipSasaranrenstra::find()
            ->where(['refsasaranrenstra_id' => $refsasaranrenstra_id])
            ->select('uraian_sasaranrenstra')
            ->scalar() : null;

        // Retrieve the periode based on refperiode_id
        $selectedPeriod = SakipPeriode::find()->where(['refperiode_id' => $refperiode_id])->one();
        $selectedPeriodValue = $selectedPeriod ? $selectedPeriod->periode : null; // Get the periode value

        // Set the dynamic title
        $this->view->title = "Laporan Analisis Pencapaian Sasaran per Triwulan $reftriwulan_id - $selectedPeriodValue - " . Html::encode($nama_skpd);

        return $this->render('index-laporan-analisis-sasaran-triwulan-dev', [
            'periodeList' => $periodeList,
            'selectedPeriodId' => $refperiode_id,
            'refskpd_id' => $refskpd_id,
            'refperiode_id' => $refperiode_id,
            'sasaranRenstraList' => $sasaranRenstraList,
            'indikators' => $indikators,
            'nama_skpd' => $nama_skpd,
            'selectedPeriodValue' => $selectedPeriodValue, // Include selected period value
            'selectedTriwulanId' => $reftriwulan_id, // Include selected triwulan id
            'selectedSasaranRenstraId' => $refsasaranrenstra_id, // Include selected triwulan id
            'selectedSasaranRenstraUraian' => $selectedSasaranRenstra, // Include selected sasaran uraian
            'selectedSkpdId' => $refskpd_id,
            'skpdList' => $skpdList,
        ]);
    }

    public function actionDownloadPdfAnalisisSasaranTriwulan()
    {
        if (Yii::$app->request->isPost) {
            $content = Yii::$app->request->post('content'); // Konten dari POST
            $title = Yii::$app->request->post('title', 'Laporan'); // Judul untuk nama file

            // Define CSS untuk styling PDF
            $css = <<<CSS
                .tbdata {
                    border-collapse: collapse;
                    font-family: 'Public Sans', sans-serif;
                    font-size: 11px;
                }
                .tbdata th {
                    height: 40px;
                    background: rgb(0, 160, 221);
                    text-align: center;
                    border: 1px solid #cfcfcf;
                    color: #ffffff;
                    padding: 2px;
                }
                .title {
            border-collapse: collapse;
            font-family: 'Public Sans', sans-serif;
            /* Menggunakan font Public Sans */
            font-size: 11px;
            text-align: center;
        }
                .tbdata td {
                    padding: 2px;
                    vertical-align: top;
                }
                .tebal {
                    font-weight: bold;
                }
                .tblRenstra {
                    width: 100%;
            font-family: 'Public Sans', sans-serif;
                    border-collapse: collapse;
                    font-size: 11px;
                }
                .tblRenstra td {
                    padding: 2px;
                    border: 1px solid #f2f2f2;
                }
                .tblRenstra .header {
                    text-align: center;
                    vertical-align: middle;
                    font-weight: bold;
                    background: #03b0e2;
                    color: #ffffff;
                }
                .trO {
                    background: #f2f2f2;
                }
                .trE {
                    background: white;
                }
                .tblAtas {
                    margin-left: 30px;
            font-family: 'Public Sans', sans-serif;
                    border-collapse: collapse;
                    font-size: 11px;
                }
                thead {
                    display: table-header-group;
                }
    CSS;

            // Setting untuk file PDF dengan CSS
            $pdf = new Pdf([
                'mode' => Pdf::MODE_CORE,
                'format' => Pdf::FORMAT_A4,
                'orientation' => Pdf::ORIENT_PORTRAIT,
                'destination' => Pdf::DEST_DOWNLOAD,
                'filename' => $title . '.pdf',
                'content' => $content, // Konten dari POST
                'cssInline' => $css, // Gunakan CSS inline
                'options' => ['title' => $title],
                'methods' => [
                    'SetHeader' => [$title],
                    'SetFooter' => ['{PAGENO}'],
                ],
            ]);

            return $pdf->render(); // Generate dan kembalikan PDF untuk diunduh
        }
    }

    public function actionIndexLaporanAnalisisSasaranTahunan($refperiode_id = null, $refsasaranrenstra_id = null)
    {
        // Get refskpd_id from the current user
        $user = Yii::$app->user->identity;
        $refskpd_id = $user->refskpd_id;

        // Get the name of the SKPD based on refskpd_id
        $nama_skpd = SakipSkpd::find()->select('nama_skpd')->where(['refskpd_id' => $refskpd_id])->scalar();

        // Set default period to this year if not provided
        if ($refperiode_id === null) {
            $currentYear = date('Y');
            $defaultPeriod = SakipPeriode::find()->where(['periode' => $currentYear])->one();
            $refperiode_id = $defaultPeriod ? $defaultPeriod->refperiode_id : null;
        }

        // Fetch all periods
        $periodeList = SakipPeriode::find()->all();

        // Indicators list based on SKPD and selected period
        $sasaranRenstraList = SakipSasaranrenstra::find()
            ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
            ->all();

        // Fetch filtered data based on selected indicators and triwulan
        $indikators = SakipIndikatorsasaranrenstra::find()
            ->where([
                'refskpd_id' => $refskpd_id,
                'refperiode_id' => $refperiode_id,
            ])
            ->andFilterWhere(['refsasaranrenstra_id' => $refsasaranrenstra_id])
            ->all();

        // Retrieve the uraian_sasaranrenstra based on refsasaranrenstra_id
        $selectedSasaranRenstra = SakipSasaranrenstra::find()
            ->where(['refsasaranrenstra_id' => $refsasaranrenstra_id])
            ->select('uraian_sasaranrenstra')
            ->scalar();


        // Retrieve the periode based on refperiode_id
        $selectedPeriod = SakipPeriode::find()->where(['refperiode_id' => $refperiode_id])->one();
        $selectedPeriodValue = $selectedPeriod ? $selectedPeriod->periode : null; // Get the periode value

        // Set the dynamic title
        $this->view->title = "Laporan Laporan Analisis Pencapaian Sasaran per Tahunan  $selectedPeriodValue - " . Html::encode($nama_skpd);

        return $this->render('index-laporan-analisis-sasaran-tahunan', [
            'periodeList' => $periodeList,
            'selectedPeriodId' => $refperiode_id,
            'refskpd_id' => $refskpd_id,
            'refperiode_id' => $refperiode_id,
            'sasaranRenstraList' => $sasaranRenstraList,
            'indikators' => $indikators,
            'nama_skpd' => $nama_skpd,
            'selectedPeriodValue' => $selectedPeriodValue, // Include selected period value
            'selectedSasaranRenstraId' => $refsasaranrenstra_id, // Include selected triwulan id
            'selectedSasaranRenstraUraian' => $selectedSasaranRenstra, // Include selected sasaran uraian
        ]);
    }

    // public function actionIndexLaporanAnalisisSasaranTahunanDev($refperiode_id = null, $refsasaranrenstra_id = null, $refskpd_id = null)
    // {
    //     // Ambil refskpd_id dari user saat ini jika tidak ada di request
    //     if ($refskpd_id === null) {
    //         $user = Yii::$app->user->identity;
    //         $refskpd_id = $user->refskpd_id;
    //     }

    //     // Get the name of the SKPD based on refskpd_id
    //     $nama_skpd = SakipSkpd::find()->select('nama_skpd')->where(['refskpd_id' => $refskpd_id])->scalar();

    //     // Set default period to this year if not provided
    //     if ($refperiode_id === null) {
    //         $currentYear = date('Y');
    //         $defaultPeriod = SakipPeriode::find()->where(['periode' => $currentYear])->one();
    //         $refperiode_id = $defaultPeriod ? $defaultPeriod->refperiode_id : null;
    //     }

    //     // Fetch all periods
    //     $periodeList = SakipPeriode::find()->all();

    //     // Indicators list based on SKPD and selected period
    //     $sasaranRenstraList = SakipSasaranrenstra::find()
    //         ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
    //         ->all();

    //     // Fetch filtered data based on selected indicators and triwulan
    //     $indikators = SakipIndikatorsasaranrenstra::find()
    //         ->where([
    //             'refskpd_id' => $refskpd_id,
    //             'refperiode_id' => $refperiode_id,
    //         ])
    //         ->andFilterWhere(['refsasaranrenstra_id' => $refsasaranrenstra_id])
    //         ->all();

    //     // Retrieve the uraian_sasaranrenstra based on refsasaranrenstra_id
    //     $selectedSasaranRenstra = SakipSasaranrenstra::find()
    //         ->where(['refsasaranrenstra_id' => $refsasaranrenstra_id])
    //         ->select('uraian_sasaranrenstra')
    //         ->scalar();

    //     // Ambil daftar SKPD untuk dropdown
    //     $skpdList = ArrayHelper::map(SakipSkpd::find()->all(), 'refskpd_id', 'nama_skpd');

    //     // Retrieve the periode based on refperiode_id
    //     $selectedPeriod = SakipPeriode::find()->where(['refperiode_id' => $refperiode_id])->one();
    //     $selectedPeriodValue = $selectedPeriod ? $selectedPeriod->periode : null; // Get the periode value

    //     // Set the dynamic title
    //     $this->view->title = "Laporan Laporan Analisis Pencapaian Sasaran per Tahunan  $selectedPeriodValue - " . Html::encode($nama_skpd);

    //     return $this->render('index-laporan-analisis-sasaran-tahunan-dev', [
    //         'periodeList' => $periodeList,
    //         'selectedPeriodId' => $refperiode_id,
    //         'refskpd_id' => $refskpd_id,
    //         'refperiode_id' => $refperiode_id,
    //         'sasaranRenstraList' => $sasaranRenstraList,
    //         'indikators' => $indikators,
    //         'nama_skpd' => $nama_skpd,
    //         'selectedPeriodValue' => $selectedPeriodValue, // Include selected period value
    //         'selectedSasaranRenstraId' => $refsasaranrenstra_id, // Include selected triwulan id
    //         'selectedSasaranRenstraUraian' => $selectedSasaranRenstra, // Include selected sasaran uraian
    //         'selectedSkpdId' => $refskpd_id,
    //         'skpdList' => $skpdList,
    //     ]);
    // }

    public function actionIndexLaporanAnalisisSasaranTahunanDev($refperiode_id = null, $refsasaranrenstra_id = null, $refskpd_id = null)
    {
        // =========================================================================
        // BLOK LOGIKA BARU UNTUK KEAMANAN SKPD
        // =========================================================================

        $user = Yii::$app->user->identity;
        $assignments = Yii::$app->authManager->getAssignments($user->id);

        $skpdList = [];
        $allowedSkpdIds = []; // Daftar ID SKPD yang diizinkan untuk user ini

        if (isset($assignments['superadmin']) || isset($assignments['admin'])) {
            // --- BLOK UNTUK ADMIN ---
            $allSkpd = SakipSkpd::find()->where(['skpd_isaktif' => 'T'])->orderBy('nama_skpd ASC')->all();
            $skpdList = ArrayHelper::map($allSkpd, 'refskpd_id', 'nama_skpd');
            $allowedSkpdIds = array_keys($skpdList);
        } else {
            // --- BLOK UNTUK NON-ADMIN (KOORDINATOR) ---
            $coordinatedSkpdIds = SakipKoordinasi::find()
                ->select('refskpd_id')
                ->where(['refuser_id' => $user->id])
                ->column();

            $allowedSkpdIds = $coordinatedSkpdIds;

            if (!empty($allowedSkpdIds)) {
                $skpdList = ArrayHelper::map(
                    SakipSkpd::find()->where(['refskpd_id' => $allowedSkpdIds, 'skpd_isaktif' => 'T'])->orderBy('nama_skpd ASC')->all(),
                    'refskpd_id',
                    'nama_skpd'
                );
            }
        }

        // --- Validasi Keamanan ---
        // Cek apakah refskpd_id dari URL diizinkan untuk diakses
        if ($refskpd_id !== null && !in_array($refskpd_id, $allowedSkpdIds)) {
            throw new ForbiddenHttpException('Anda tidak memiliki hak akses untuk melihat data SKPD ini.');
        }

        // Tentukan refskpd_id yang akan digunakan jika tidak ada di URL atau tidak valid
        if ($refskpd_id === null || !in_array($refskpd_id, $allowedSkpdIds)) {
            $refskpd_id = !empty($allowedSkpdIds) ? $allowedSkpdIds[0] : null;
        }

        // =========================================================================
        // AKHIR DARI BLOK LOGIKA BARU
        // Kode di bawah ini sekarang menggunakan $refskpd_id yang sudah aman
        // =========================================================================

        // Get the name of the SKPD based on refskpd_id
        $nama_skpd = $refskpd_id ? SakipSkpd::find()->select('nama_skpd')->where(['refskpd_id' => $refskpd_id])->scalar() : 'Tidak ada SKPD dipilih';

        // Set default period to this year if not provided
        if ($refperiode_id === null) {
            $currentYear = date('Y');
            $defaultPeriod = SakipPeriode::find()->where(['periode' => $currentYear])->one();
            $refperiode_id = $defaultPeriod ? $defaultPeriod->refperiode_id : null;
        }

        // Fetch all periods
        $periodeList = SakipPeriode::find()->all();

        // Logika query utama Anda tidak diubah, namun ditambahkan pengecekan null
        $sasaranRenstraList = [];
        $indikators = [];

        if ($refskpd_id && $refperiode_id) {
            // Indicators list based on SKPD and selected period
            $sasaranRenstraList = SakipSasaranrenstra::find()
                ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
                ->all();

            // Fetch filtered data based on selected indicators
            $indikators = SakipIndikatorsasaranrenstra::find()
                ->where([
                    'refskpd_id' => $refskpd_id,
                    'refperiode_id' => $refperiode_id,
                ])
                ->andFilterWhere(['refsasaranrenstra_id' => $refsasaranrenstra_id])
                ->all();
        }

        // Retrieve the uraian_sasaranrenstra based on refsasaranrenstra_id
        $selectedSasaranRenstra = $refsasaranrenstra_id ? SakipSasaranrenstra::find()
            ->where(['refsasaranrenstra_id' => $refsasaranrenstra_id])
            ->select('uraian_sasaranrenstra')
            ->scalar() : null;

        // Retrieve the periode based on refperiode_id
        $selectedPeriod = SakipPeriode::find()->where(['refperiode_id' => $refperiode_id])->one();
        $selectedPeriodValue = $selectedPeriod ? $selectedPeriod->periode : null; // Get the periode value

        // Set the dynamic title
        $this->view->title = "Laporan Analisis Pencapaian Sasaran Tahunan $selectedPeriodValue - " . Html::encode($nama_skpd);

        return $this->render('index-laporan-analisis-sasaran-tahunan-dev', [
            'periodeList' => $periodeList,
            'selectedPeriodId' => $refperiode_id,
            'refskpd_id' => $refskpd_id,
            'refperiode_id' => $refperiode_id,
            'sasaranRenstraList' => $sasaranRenstraList,
            'indikators' => $indikators,
            'nama_skpd' => $nama_skpd,
            'selectedPeriodValue' => $selectedPeriodValue, // Include selected period value
            'selectedSasaranRenstraId' => $refsasaranrenstra_id,
            'selectedSasaranRenstraUraian' => $selectedSasaranRenstra,
            'selectedSkpdId' => $refskpd_id,
            'skpdList' => $skpdList,
        ]);
    }

    public function actionDownloadPdfAnalisisSasaranTahunan()
    {
        if (Yii::$app->request->isPost) {
            $content = Yii::$app->request->post('content'); // Konten dari POST
            $title = Yii::$app->request->post('title', 'Laporan'); // Judul untuk nama file

            // Define CSS untuk styling PDF
            $css = <<<CSS
                .tbdata {
                    border-collapse: collapse;
                    font-family: 'Public Sans', sans-serif;
                    font-size: 11px;
                }
                .tbdata th {
                    height: 40px;
                    background: rgb(0, 160, 221);
                    text-align: center;
                    border: 1px solid #cfcfcf;
                    color: #ffffff;
                    padding: 2px;
                }
                .title {
            border-collapse: collapse;
            font-family: 'Public Sans', sans-serif;
            /* Menggunakan font Public Sans */
            font-size: 11px;
            text-align: center;
        }
                .tbdata td {
                    padding: 2px;
                    vertical-align: top;
                }
                .tebal {
                    font-weight: bold;
                }
                .tblRenstra {
                    width: 100%;
            font-family: 'Public Sans', sans-serif;
                    border-collapse: collapse;
                    font-size: 11px;
                }
                .tblRenstra td {
                    padding: 2px;
                    border: 1px solid #f2f2f2;
                }
                .tblRenstra .header {
                    text-align: center;
                    vertical-align: middle;
                    font-weight: bold;
                    background: #03b0e2;
                    color: #ffffff;
                }
                .trO {
                    background: #f2f2f2;
                }
                .trE {
                    background: white;
                }
                .tblAtas {
                    margin-left: 30px;
            font-family: 'Public Sans', sans-serif;
                    border-collapse: collapse;
                    font-size: 11px;
                }
                thead {
                    display: table-header-group;
                }
    CSS;

            // Setting untuk file PDF dengan CSS
            $pdf = new Pdf([
                'mode' => Pdf::MODE_CORE,
                'format' => Pdf::FORMAT_A4,
                'orientation' => Pdf::ORIENT_PORTRAIT,
                'destination' => Pdf::DEST_DOWNLOAD,
                'filename' => $title . '.pdf',
                'content' => $content, // Konten dari POST
                'cssInline' => $css, // Gunakan CSS inline
                'options' => ['title' => $title],
                'methods' => [
                    'SetHeader' => [$title],
                    'SetFooter' => ['{PAGENO}'],
                ],
            ]);

            return $pdf->render(); // Generate dan kembalikan PDF untuk diunduh
        }
    }

    public function actionIndexLaporanEvaluasiRkpd($refperiode_id = null)
    {
        // Get refskpd_id from the current user
        $user = Yii::$app->user->identity;
        $refskpd_id = $user->refskpd_id;

        // Get the name of the SKPD based on refskpd_id
        $nama_skpd = SakipSkpd::find()->select('nama_skpd')->where(['refskpd_id' => $refskpd_id])->scalar();

        // Set default period to this year if not provided
        if ($refperiode_id === null) {
            $currentYear = date('Y');
            $defaultPeriod = SakipPeriode::find()->where(['periode' => $currentYear])->one();
            $refperiode_id = $defaultPeriod ? $defaultPeriod->refperiode_id : null;
        }

        // Fetch all periods
        $periodeList = SakipPeriode::find()->all();



        // Retrieve the periode based on refperiode_id
        $selectedPeriod = SakipPeriode::find()->where(['refperiode_id' => $refperiode_id])->one();
        $selectedPeriodValue = $selectedPeriod ? $selectedPeriod->periode : null; // Get the periode value

        // Set the dynamic title
        $this->view->title = "Laporan Evaluasi RKPD $selectedPeriodValue - " . Html::encode($nama_skpd);

        return $this->render('index-laporan-evaluasi-rkpd', [
            'periodeList' => $periodeList,
            'selectedPeriodId' => $refperiode_id,
            'refskpd_id' => $refskpd_id,
            'refperiode_id' => $refperiode_id,
            'nama_skpd' => $nama_skpd,
            'selectedPeriodValue' => $selectedPeriodValue, // Include selected period value
        ]);
    }


    public function actionDownloadPdfEvaluasiRkpd()
    {
        if (Yii::$app->request->isPost) {
            $content = Yii::$app->request->post('content'); // Konten dari POST
            $title = Yii::$app->request->post('title', 'Laporan'); // Judul untuk nama file

            // Define CSS untuk styling PDF
            $css = <<<CSS
                .tbdata {
                    border-collapse: collapse;
                    font-family: 'Public Sans', sans-serif;
                    font-size: 11px;
                }
                .tbdata th {
                    height: 40px;
                    background: rgb(0, 160, 221);
                    text-align: center;
                    border: 1px solid #cfcfcf;
                    color: #ffffff;
                    padding: 2px;
                }
                .title {
            border-collapse: collapse;
            font-family: 'Public Sans', sans-serif;
            /* Menggunakan font Public Sans */
            font-size: 11px;
            text-align: center;
        }
                .tbdata td {
                    padding: 2px;
                    vertical-align: top;
                }
                .tebal {
                    font-weight: bold;
                }
                .tblRenstra {
                    width: 100%;
            font-family: 'Public Sans', sans-serif;
                    border-collapse: collapse;
                    font-size: 11px;
                }
                .tblRenstra td {
                    padding: 2px;
                    border: 1px solid #f2f2f2;
                }
                .tblRenstra .header {
                    text-align: center;
                    vertical-align: middle;
                    font-weight: bold;
                    background: #03b0e2;
                    color: #ffffff;
                }
                .trO {
                    background: #f2f2f2;
                }
                .trE {
                    background: white;
                }
                .tblAtas {
                    margin-left: 30px;
            font-family: 'Public Sans', sans-serif;
                    border-collapse: collapse;
                    font-size: 11px;
                }
                thead {
                    display: table-header-group;
                }
    CSS;

            // Setting untuk file PDF dengan CSS
            $pdf = new Pdf([
                'mode' => Pdf::MODE_CORE,
                'format' => Pdf::FORMAT_A4,
                'orientation' => Pdf::ORIENT_PORTRAIT,
                'destination' => Pdf::DEST_DOWNLOAD,
                'filename' => $title . '.pdf',
                'content' => $content, // Konten dari POST
                'cssInline' => $css, // Gunakan CSS inline
                'options' => ['title' => $title],
                'methods' => [
                    'SetHeader' => [$title],
                    'SetFooter' => ['{PAGENO}'],
                ],
            ]);

            return $pdf->render(); // Generate dan kembalikan PDF untuk diunduh
        }
    }
}
