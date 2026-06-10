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

    public function actionIndexEvaluasiRkpd($refperiode_id = null, $refskpd_id = null)
    {
        $user = Yii::$app->user->identity;
        $refskpd_id = $user->refskpd_id;

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

        return $this->render('index-evaluasi-rkpd', [
            'periodeList' => $periodeList,
            'selectedPeriodId' => $refperiode_id,
            'refskpd_id' => $refskpd_id,
            'refperiode_id' => $refperiode_id,
            'nama_skpd' => $nama_skpd,
            'selectedPeriodValue' => $selectedPeriodValue, // Include selected period value
            'selectedSkpdId' => $refskpd_id,
            'laporanData' => $laporanData, // <-- Kirim data laporan ke view
        ]);
    }

    public function actionCetakEvaluasiRkpd($refperiode_id = null, $refskpd_id = null)
    {
        $user = Yii::$app->user->identity;
        $refskpd_id = $user->refskpd_id;

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
        $refskpd_id = $user->refskpd_id;

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

    public function actionCetakEvaluasiRkpdDev($refperiode_id = null, $refskpd_id = null)
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
    public function actionCetakEvaluasiRkpdExcelDev($refperiode_id = null, $refskpd_id = null)
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

    /**
     * Map periode terpilih (refperiode_id) -> periode renstra 5 tahun (refperiode_5tahun_id).
     * Tabel renstra (sasaran/strategi/kebijakan/tujuan) dikunci oleh refperiode_5tahun_id,
     * BUKAN refperiode_id — ini sumber error "Unknown column 'refperiode_id'".
     */
    private function periode5($refperiode_id)
    {
        static $cache = [];
        if ($refperiode_id === null) {
            return null;
        }
        if (!array_key_exists($refperiode_id, $cache)) {
            $p = SakipPeriode::find()->where(['refperiode_id' => $refperiode_id])->one();
            $cache[$refperiode_id] = $p ? $p->refperiode_5tahun_id : null;
        }
        return $cache[$refperiode_id];
    }

    /**
     * Hilangkan duplikasi Sasaran Renstra: tiap sasaran (refsasaran_id) hanya tampil sekali
     * untuk periode yang dipilih.
     */
    private function uniqueBySasaran($list)
    {
        $seen = [];
        $unique = [];
        foreach ($list as $item) {
            // "Sama" = teks Sasaran Renstra identik. Hanya entri yang benar-benar sama
            // yang digabung; sasaran berbeda (meski 1 sasaran RPJMD) tetap ditampilkan.
            $key = trim(strtolower(strip_tags((string) $item->uraian_sasaranrenstra)));
            if ($key === '') {
                $key = 'rsr-' . $item->refsasaranrenstra_id;
            }
            if (isset($seen[$key])) {
                continue;
            }
            $seen[$key] = true;
            $unique[] = $item;
        }
        return $unique;
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

        // Periode renstra 5 tahun (tabel renstra dikunci refperiode_5tahun_id, bukan refperiode_id)
        $refperiode_5tahun_id = $this->periode5($refperiode_id);

        // Fetch data based on refskpd_id and refperiode_5tahun_id
        $sasaranRenstra = SakipSasaranrenstra::find()
            ->where(['refskpd_id' => $refskpd_id, 'refperiode_5tahun_id' => $refperiode_5tahun_id])
            ->with(['refVisi', 'refMisi', 'refTujuan', 'refSasaran'])
            ->all();
        // Hilangkan duplikasi sasaran (tampilkan tiap sasaran sekali untuk periode terpilih)
        $sasaranRenstra = $this->uniqueBySasaran($sasaranRenstra);

        // Fetch strategi based on refskpd_id and refperiode_5tahun_id
        $strategiList = SakipStrategi::find()
            ->where(['refskpd_id' => $refskpd_id, 'refperiode_5tahun_id' => $refperiode_5tahun_id])
            ->all();

        // Fetch kebijakan based on refskpd_id and refperiode_5tahun_id
        $kebijakanList = SakipKebijakan::find()
            ->where(['refskpd_id' => $refskpd_id, 'refperiode_5tahun_id' => $refperiode_5tahun_id])
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
            'selectedPeriodValue' => $selectedPeriodValue,
            'selectedSkpdId' => $refskpd_id, // Include selected period value
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
                ->where(['refskpd_id' => $refskpd_id, 'refperiode_5tahun_id' => $this->periode5($refperiode_id)])
                ->with(['refVisi', 'refMisi', 'refTujuan', 'refSasaran'])
                ->all();

            $strategiList = SakipStrategi::find()
                ->where(['refskpd_id' => $refskpd_id, 'refperiode_5tahun_id' => $this->periode5($refperiode_id)])
                ->all();

            $kebijakanList = SakipKebijakan::find()
                ->where(['refskpd_id' => $refskpd_id, 'refperiode_5tahun_id' => $this->periode5($refperiode_id)])
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

    public function actionCetakLaporanRenstra($refperiode_id = null, $refskpd_id = null)
    {
        // Logika keamanan dan pengambilan data disalin dari actionIndexLaporanRenstraDev
        // untuk memastikan konsistensi hak akses.
        $user = Yii::$app->user->identity;
        $assignments = Yii::$app->authManager->getAssignments($user->id);
        $allowedSkpdIds = [];

        if (isset($assignments['superadmin']) || isset($assignments['admin'])) {
            $allowedSkpdIds = ArrayHelper::map(SakipSkpd::find()->where(['skpd_isaktif' => 'T'])->all(), 'refskpd_id', 'refskpd_id');
        } else {
            $allowedSkpdIds = SakipKoordinasi::find()->select('refskpd_id')->where(['refuser_id' => $user->id])->column();
        }

        // Jika user biasa (bukan admin/koordinator), paksa gunakan SKPD miliknya
        if (empty($allowedSkpdIds)) {
            $refskpd_id = $user->refskpd_id;
        } elseif ($refskpd_id === null || !in_array($refskpd_id, $allowedSkpdIds)) {
            // Jika admin tapi tidak memilih SKPD, atau mencoba akses SKPD yang tidak diizinkan
            throw new ForbiddenHttpException('Anda tidak memiliki hak akses atau SKPD tidak valid.');
        }


        $nama_skpd = SakipSkpd::find()->select('nama_skpd')->where(['refskpd_id' => $refskpd_id])->scalar();
        if ($refperiode_id === null) {
            $currentYear = date('Y');
            $defaultPeriod = SakipPeriode::find()->where(['periode' => $currentYear])->one();
            $refperiode_id = $defaultPeriod ? $defaultPeriod->refperiode_id : null;
        }

        // Fetch data (sama seperti di actionIndex) — tabel renstra dikunci refperiode_5tahun_id
        $refperiode_5tahun_id = $this->periode5($refperiode_id);
        $sasaranRenstra = SakipSasaranrenstra::find()->where(['refskpd_id' => $refskpd_id, 'refperiode_5tahun_id' => $refperiode_5tahun_id])->with(['refVisi', 'refMisi', 'refTujuan', 'refSasaran'])->all();
        $sasaranRenstra = $this->uniqueBySasaran($sasaranRenstra);
        $strategiList = SakipStrategi::find()->where(['refskpd_id' => $refskpd_id, 'refperiode_5tahun_id' => $refperiode_5tahun_id])->all();
        $kebijakanList = SakipKebijakan::find()->where(['refskpd_id' => $refskpd_id, 'refperiode_5tahun_id' => $refperiode_5tahun_id])->all();
        $selectedPeriod = SakipPeriode::findOne($refperiode_id);
        $selectedPeriodValue = $selectedPeriod ? $selectedPeriod->periode : 'N/A';

        // Render konten PDF dari partial view
        $content = $this->renderPartial('_cetak_laporan_renstra', [
            'sasaranRenstra' => $sasaranRenstra,
            'strategiList' => $strategiList,
            'kebijakanList' => $kebijakanList,
            'nama_skpd' => $nama_skpd,
            'selectedPeriodValue' => $selectedPeriodValue,
        ]);

        $pdf = new Pdf([
            'mode' => Pdf::MODE_UTF8,
            'format' => Pdf::FORMAT_A4,
            'orientation' => Pdf::ORIENT_PORTRAIT, // Ganti ke PORTRAIT jika landscape tidak perlu
            'destination' => Pdf::DEST_BROWSER,
            'content' => $content,
            'cssFile' => '@vendor/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css',
            'cssInline' => '
                .tbdata { font-family: sans-serif; font-size: 11px; }
                .title { text-align: center; }
                .tebal { font-weight: bold; }
                .tblAtas { margin-left: 20px; font-size: 11px; }
                .tblRenstra { width: 100%; border-collapse: collapse; font-size: 10px; }
                .tblRenstra td { border: 1px solid #f2f2f2; padding: 4px; vertical-align: top; }
                .tblRenstra .header { 
                    background-color: #03b0e2; 
                    color: white; 
                    text-align: center; 
                    font-weight: bold;
                    vertical-align: middle;
                }
            ', // <-- GANTI DENGAN BLOK CSS INI
            'options' => ['title' => 'Laporan Renstra'],
            'methods' => [
                'SetHeader' => ["Laporan Renstra - {$nama_skpd} - Periode {$selectedPeriodValue}"],
                'SetFooter' => ['{PAGENO}'],
            ]
        ]);

        return $pdf->render();
    }

    public function actionCetakLaporanRenstraExcel($refperiode_id = null, $refskpd_id = null)
    {
        // Logika keamanan dan pengambilan data (sama seperti di atas)
        $user = Yii::$app->user->identity;
        $assignments = Yii::$app->authManager->getAssignments($user->id);
        $allowedSkpdIds = [];

        if (isset($assignments['superadmin']) || isset($assignments['admin'])) {
            $allowedSkpdIds = ArrayHelper::map(SakipSkpd::find()->where(['skpd_isaktif' => 'T'])->all(), 'refskpd_id', 'refskpd_id');
        } else {
            $allowedSkpdIds = SakipKoordinasi::find()->select('refskpd_id')->where(['refuser_id' => $user->id])->column();
        }

        if (empty($allowedSkpdIds)) {
            $refskpd_id = $user->refskpd_id;
        } elseif ($refskpd_id === null || !in_array($refskpd_id, $allowedSkpdIds)) {
            throw new ForbiddenHttpException('Anda tidak memiliki hak akses atau SKPD tidak valid.');
        }


        $nama_skpd = SakipSkpd::find()->select('nama_skpd')->where(['refskpd_id' => $refskpd_id])->scalar();
        if ($refperiode_id === null) {
            $currentYear = date('Y');
            $defaultPeriod = SakipPeriode::find()->where(['periode' => $currentYear])->one();
            $refperiode_id = $defaultPeriod ? $defaultPeriod->refperiode_id : null;
        }

        $sasaranRenstra = SakipSasaranrenstra::find()->where(['refskpd_id' => $refskpd_id, 'refperiode_5tahun_id' => $this->periode5($refperiode_id)])->with('indikatorSasaran')->all();
        $sasaranRenstra = $this->uniqueBySasaran($sasaranRenstra);
        $selectedPeriod = SakipPeriode::findOne($refperiode_id);
        $selectedPeriodValue = $selectedPeriod ? $selectedPeriod->periode : 'N/A';

        // Buat objek Spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Laporan Renstra');

        // Tulis Judul Laporan
        $sheet->mergeCells('A1:H1')->setCellValue('A1', 'Rencana Strategis ' . ucwords(strtolower($nama_skpd)));
        $sheet->mergeCells('A2:H2')->setCellValue('A2', 'Periode ' . $selectedPeriodValue);
        $sheet->getStyle('A1:A2')->getFont()->setBold(true);
        $sheet->getStyle('A1:A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Header Tabel
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrapText' => true],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF03b0e2']]
        ];
        $sheet->setCellValue('A4', 'No')->setCellValue('B4', 'Tujuan Renstra')->setCellValue('C4', 'Sasaran Renstra')->setCellValue('D4', 'Indikator Kinerja')->setCellValue('E4', 'Satuan')->setCellValue('F4', 'Target');
        $sheet->getStyle('A4:F4')->applyFromArray($headerStyle);

        // Isi data
        $row = 5;
        $no = 1;
        foreach ($sasaranRenstra as $sasaran) {
            if (!empty($sasaran->indikatorSasaran)) {
                foreach ($sasaran->indikatorSasaran as $index => $indikator) {
                    if ($index == 0) { // Hanya tulis tujuan dan sasaran di baris pertama
                        $sheet->setCellValue('A' . $row, $no++);
                        $sheet->setCellValue('B' . $row, strip_tags($sasaran->refTujuan->uraian_tujuan));
                        $sheet->setCellValue('C' . $row, strip_tags($sasaran->uraian_sasaranrenstra));
                    }
                    $sheet->setCellValue('D' . $row, strip_tags($indikator->uraian_indikatorsasaranrenstra));
                    $sheet->setCellValue('E' . $row, strip_tags($indikator->indikatorsasaranrenstra_satuan));
                    $sheet->setCellValue('F' . $row, strip_tags($indikator->indikatorsasaranrenstra_target));
                    $row++;
                }
            }
        }

        // Atur lebar kolom
        foreach (range('A', 'F') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        // Kirim file ke browser
        $fileName = "Laporan_Renstra_{$nama_skpd}_{$selectedPeriodValue}.xlsx";
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . urlencode($fileName) . '"');
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit();
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
            ->where(['refskpd_id' => $refskpd_id, 'refperiode_5tahun_id' => $this->periode5($refperiode_id)])
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
            'selectedPeriodValue' => $selectedPeriodValue,
            'selectedSkpdId' => $refskpd_id, // Include selected period value
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
                ->where(['refskpd_id' => $refskpd_id, 'refperiode_5tahun_id' => $this->periode5($refperiode_id)])
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

    public function actionCetakRenjaTahunan($refperiode_id = null, $refskpd_id = null)
    {
        // Logika keamanan dan pengambilan data disalin dari actionCetakLaporanRenstra
        $user = Yii::$app->user->identity;
        $assignments = Yii::$app->authManager->getAssignments($user->id);
        $allowedSkpdIds = [];

        if (isset($assignments['superadmin']) || isset($assignments['admin'])) {
            $allowedSkpdIds = ArrayHelper::map(SakipSkpd::find()->where(['skpd_isaktif' => 'T'])->all(), 'refskpd_id', 'refskpd_id');
        } else {
            $allowedSkpdIds = SakipKoordinasi::find()->select('refskpd_id')->where(['refuser_id' => $user->id])->column();
        }

        if (empty($allowedSkpdIds)) {
            $refskpd_id = $user->refskpd_id;
        } elseif ($refskpd_id === null || !in_array($refskpd_id, $allowedSkpdIds)) {
            throw new ForbiddenHttpException('Anda tidak memiliki hak akses atau SKPD tidak valid.');
        }

        $nama_skpd = SakipSkpd::find()->select('nama_skpd')->where(['refskpd_id' => $refskpd_id])->scalar();
        if ($refperiode_id === null) {
            $currentYear = date('Y');
            $defaultPeriod = SakipPeriode::find()->where(['periode' => $currentYear])->one();
            $refperiode_id = $defaultPeriod ? $defaultPeriod->refperiode_id : null;
        }

        // Mengambil data (sama seperti di actionIndexLaporanRenjaTahunanDev)
        $sasaranRenstra = [];
        if ($refskpd_id && $refperiode_id) {
            $sasaranRenstra = SakipSasaranrenstra::find()
                ->where(['refskpd_id' => $refskpd_id, 'refperiode_5tahun_id' => $this->periode5($refperiode_id)])
                ->with('indikatorSasaran')
                ->all();
        }

        $selectedPeriod = SakipPeriode::findOne($refperiode_id);
        $selectedPeriodValue = $selectedPeriod ? $selectedPeriod->periode : 'N/A';

        // Render konten PDF dari partial view baru
        $content = $this->renderPartial('_cetak_laporan_renja_tahunan', [
            'sasaranRenstra' => $sasaranRenstra,
            'nama_skpd' => $nama_skpd,
            'selectedPeriodValue' => $selectedPeriodValue,
        ]);

        // Konfigurasi mPDF
        $pdf = new Pdf([
            'mode' => Pdf::MODE_UTF8,
            'format' => Pdf::FORMAT_A4,
            'orientation' => Pdf::ORIENT_PORTRAIT,
            'destination' => Pdf::DEST_BROWSER,
            'content' => $content,
            'cssFile' => '@vendor/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css',
            'cssInline' => '
            .tbdata { border-collapse: collapse; font-family: sans-serif; font-size: 11px; }
            .tbdata td { padding: 2px; vertical-align: top; border: 1px solid #cfcfcf; }
            .title { text-align: center; }
            .tebal { font-weight: bold; }
            .tblRenstra { width: 100%; border-collapse: collapse; font-size: 10px; }
            .tblRenstra td { padding: 4px; border: 1px solid #cfcfcf; vertical-align: top; }
            .tblRenstra .header { background-color: #03b0e2; color: white; text-align: center; font-weight: bold; vertical-align: middle; }
        ',
            'options' => ['title' => 'Laporan Rencana Kinerja Tahunan'],
            'methods' => [
                'SetHeader' => ["Rencana Kinerja Tahunan - {$nama_skpd} - Periode {$selectedPeriodValue}"],
                'SetFooter' => ['{PAGENO}'],
            ]
        ]);

        return $pdf->render();
    }

    public function actionCetakRenjaTahunanExcel($refperiode_id = null, $refskpd_id = null)
    {
        // Logika keamanan dan pengambilan data (sama seperti di atas)
        $user = Yii::$app->user->identity;
        $assignments = Yii::$app->authManager->getAssignments($user->id);
        $allowedSkpdIds = [];

        if (isset($assignments['superadmin']) || isset($assignments['admin'])) {
            $allowedSkpdIds = ArrayHelper::map(SakipSkpd::find()->where(['skpd_isaktif' => 'T'])->all(), 'refskpd_id', 'refskpd_id');
        } else {
            $allowedSkpdIds = SakipKoordinasi::find()->select('refskpd_id')->where(['refuser_id' => $user->id])->column();
        }

        if (empty($allowedSkpdIds)) {
            $refskpd_id = $user->refskpd_id;
        } elseif ($refskpd_id === null || !in_array($refskpd_id, $allowedSkpdIds)) {
            throw new ForbiddenHttpException('Anda tidak memiliki hak akses atau SKPD tidak valid.');
        }

        $nama_skpd = SakipSkpd::find()->select('nama_skpd')->where(['refskpd_id' => $refskpd_id])->scalar();
        if ($refperiode_id === null) {
            $currentYear = date('Y');
            $defaultPeriod = SakipPeriode::find()->where(['periode' => $currentYear])->one();
            $refperiode_id = $defaultPeriod ? $defaultPeriod->refperiode_id : null;
        }

        // Mengambil data
        $sasaranRenstra = [];
        if ($refskpd_id && $refperiode_id) {
            $sasaranRenstra = SakipSasaranrenstra::find()
                ->where(['refskpd_id' => $refskpd_id, 'refperiode_5tahun_id' => $this->periode5($refperiode_id)])
                ->with('indikatorSasaran')
                ->all();
        }

        $selectedPeriod = SakipPeriode::findOne($refperiode_id);
        $selectedPeriodValue = $selectedPeriod ? $selectedPeriod->periode : 'N/A';

        // Buat objek Spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Laporan Renja Tahunan');

        // Judul
        $sheet->mergeCells('A1:F1')->setCellValue('A1', 'Rencana Kinerja Tahunan ' . ucwords(strtolower($nama_skpd)));
        $sheet->mergeCells('A2:F2')->setCellValue('A2', 'Periode ' . $selectedPeriodValue);
        $sheet->getStyle('A1:A2')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1:A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Header Tabel
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrapText' => true],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF03b0e2']]
        ];
        $sheet->setCellValue('A4', 'No')->setCellValue('B4', 'Sasaran Renstra')->setCellValue('C4', 'Kode')->setCellValue('D4', 'Indikator Kinerja')->setCellValue('E4', 'Satuan')->setCellValue('F4', 'Target');
        $sheet->getStyle('A4:F4')->applyFromArray($headerStyle);

        // Isi data
        $row = 5;
        $no = 1;
        $currentNumber = 0;
        $lastSasaran = '';
        foreach ($sasaranRenstra as $item) {
            $sasaranCode = $item->uraian_sasaranrenstra;
            if ($lastSasaran !== $sasaranCode) {
                $currentNumber++;
                $lastSasaran = $sasaranCode;
                $indicatorIndex = 1;
            }

            if (!empty($item->indikatorSasaran)) {
                foreach ($item->indikatorSasaran as $i => $indikator) {
                    if ($i === 0) {
                        $sheet->setCellValue('A' . $row, $no++);
                        $sheet->setCellValue('B' . $row, strip_tags($item->uraian_sasaranrenstra));
                    }
                    $sheet->setCellValue('C' . $row, $currentNumber . '.' . $indicatorIndex++);
                    $sheet->setCellValue('D' . $row, strip_tags($indikator->uraian_indikatorsasaranrenstra));
                    $sheet->setCellValue('E' . $row, strip_tags($indikator->indikatorsasaranrenstra_satuan));
                    $sheet->setCellValue('F' . $row, strip_tags($indikator->indikatorsasaranrenstra_target));
                    $row++;
                }
            }
        }

        // Atur lebar kolom
        foreach (range('A', 'F') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        // Kirim file ke browser
        $fileName = "Laporan_Renja_Tahunan_{$nama_skpd}_{$selectedPeriodValue}.xlsx";
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . urlencode($fileName) . '"');
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit();
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

        // Fetch indicators with related sasaran using 'with()' for efficiency
        $sasaranRenstra = [];
        if ($refskpd_id && $refperiode_id) {
            $indikators = SakipIndikatorsasaranrenstra::find()
                ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
                ->with('refSasaranrenstra') // Eager loading
                ->all();

            foreach ($indikators as $indikator) {
                if ($indikator->refSasaranrenstra) { // Perbaikan nama relasi
                    $sasaranRenstra[] = [
                        'indikator' => $indikator,
                        'formulasi' => $indikator->refSasaranrenstra->formulasi_sasaranrenstra, // Perbaikan nama relasi
                    ];
                }
            }
        }

        // Retrieve the periode based on refperiode_id
        $selectedPeriod = SakipPeriode::find()->where(['refperiode_id' => $refperiode_id])->one();
        $selectedPeriodValue = $selectedPeriod ? $selectedPeriod->periode : null;

        // Set the dynamic title
        $this->view->title = "Laporan Indikator Kinerja Utama $selectedPeriodValue - " . Html::encode($nama_skpd);

        return $this->render('index-laporan-iku', [
            'periodeList' => $periodeList,
            'selectedPeriodId' => $refperiode_id,
            'refskpd_id' => $refskpd_id,
            'refperiode_id' => $refperiode_id,
            'sasaranRenstra' => $sasaranRenstra,
            'nama_skpd' => $nama_skpd,
            'selectedPeriodValue' => $selectedPeriodValue,
            'selectedSkpdId' => $refskpd_id,
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
        $user = Yii::$app->user->identity;
        $assignments = Yii::$app->authManager->getAssignments($user->id);
        $skpdList = [];
        $allowedSkpdIds = [];

        if (isset($assignments['superadmin']) || isset($assignments['admin'])) {
            $allSkpd = SakipSkpd::find()->where(['skpd_isaktif' => 'T'])->orderBy('nama_skpd ASC')->all();
            $skpdList = ArrayHelper::map($allSkpd, 'refskpd_id', 'nama_skpd');
            $allowedSkpdIds = array_keys($skpdList);
        } else {
            $coordinatedSkpdIds = SakipKoordinasi::find()->select('refskpd_id')->where(['refuser_id' => $user->id])->column();
            $allowedSkpdIds = $coordinatedSkpdIds;
            if (!empty($allowedSkpdIds)) {
                $skpdList = ArrayHelper::map(
                    SakipSkpd::find()->where(['refskpd_id' => $allowedSkpdIds, 'skpd_isaktif' => 'T'])->orderBy('nama_skpd ASC')->all(),
                    'refskpd_id',
                    'nama_skpd'
                );
            }
        }

        if ($refskpd_id !== null && !in_array($refskpd_id, $allowedSkpdIds)) {
            throw new ForbiddenHttpException('Anda tidak memiliki hak akses untuk melihat data SKPD ini.');
        }
        if ($refskpd_id === null || !in_array($refskpd_id, $allowedSkpdIds)) {
            $refskpd_id = !empty($allowedSkpdIds) ? $allowedSkpdIds[0] : null;
        }

        $nama_skpd = $refskpd_id ? SakipSkpd::find()->select('nama_skpd')->where(['refskpd_id' => $refskpd_id])->scalar() : 'Tidak ada SKPD dipilih';
        if ($refperiode_id === null) {
            $currentYear = date('Y');
            $defaultPeriod = SakipPeriode::find()->where(['periode' => $currentYear])->one();
            $refperiode_id = $defaultPeriod ? $defaultPeriod->refperiode_id : null;
        }
        $periodeList = SakipPeriode::find()->all();

        $sasaranRenstra = [];
        if ($refskpd_id && $refperiode_id) {
            $indikators = SakipIndikatorsasaranrenstra::find()
                ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
                ->with('refSasaranrenstra') // Eager loading
                ->all();

            foreach ($indikators as $indikator) {
                if ($indikator->refSasaranrenstra) { // Perbaikan nama relasi
                    $sasaranRenstra[] = [
                        'indikator' => $indikator,
                        'formulasi' => $indikator->refSasaranrenstra->formulasi_sasaranrenstra, // Perbaikan nama relasi
                    ];
                }
            }
        }

        $selectedPeriod = SakipPeriode::find()->where(['refperiode_id' => $refperiode_id])->one();
        $selectedPeriodValue = $selectedPeriod ? $selectedPeriod->periode : null;
        $this->view->title = "Laporan Indikator Kinerja Utama $selectedPeriodValue - " . Html::encode($nama_skpd);

        return $this->render('index-laporan-iku-dev', [
            'periodeList' => $periodeList,
            'selectedPeriodId' => $refperiode_id,
            'refskpd_id' => $refskpd_id,
            'refperiode_id' => $refperiode_id,
            'sasaranRenstra' => $sasaranRenstra,
            'nama_skpd' => $nama_skpd,
            'selectedPeriodValue' => $selectedPeriodValue,
            'selectedSkpdId' => $refskpd_id,
            'skpdList' => $skpdList,
        ]);
    }

    public function actionCetakLaporanIku($refperiode_id = null, $refskpd_id = null)
    {
        $user = Yii::$app->user->identity;
        $assignments = Yii::$app->authManager->getAssignments($user->id);
        $allowedSkpdIds = [];

        if (isset($assignments['superadmin']) || isset($assignments['admin'])) {
            $allowedSkpdIds = ArrayHelper::map(SakipSkpd::find()->where(['skpd_isaktif' => 'T'])->all(), 'refskpd_id', 'refskpd_id');
        } else {
            $allowedSkpdIds = SakipKoordinasi::find()->select('refskpd_id')->where(['refuser_id' => $user->id])->column();
        }
        if (empty($allowedSkpdIds)) {
            $refskpd_id = $user->refskpd_id;
        } elseif ($refskpd_id === null || !in_array($refskpd_id, $allowedSkpdIds)) {
            throw new ForbiddenHttpException('Anda tidak memiliki hak akses atau SKPD tidak valid.');
        }

        $nama_skpd = SakipSkpd::find()->select('nama_skpd')->where(['refskpd_id' => $refskpd_id])->scalar();
        if ($refperiode_id === null) {
            $currentYear = date('Y');
            $defaultPeriod = SakipPeriode::find()->where(['periode' => $currentYear])->one();
            $refperiode_id = $defaultPeriod ? $defaultPeriod->refperiode_id : null;
        }

        $sasaranRenstra = [];
        if ($refskpd_id && $refperiode_id) {
            $indikators = SakipIndikatorsasaranrenstra::find()
                ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
                ->with('refSasaranrenstra') // Eager loading
                ->all();

            foreach ($indikators as $indikator) {
                if ($indikator->refSasaranrenstra) { // Perbaikan nama relasi
                    $sasaranRenstra[] = [
                        'indikator' => $indikator,
                        'formulasi' => $indikator->refSasaranrenstra->formulasi_sasaranrenstra, // Perbaikan nama relasi
                    ];
                }
            }
        }

        $selectedPeriod = SakipPeriode::findOne($refperiode_id);
        $selectedPeriodValue = $selectedPeriod ? $selectedPeriod->periode : 'N/A';

        $content = $this->renderPartial('_cetak_laporan_iku', [
            'sasaranRenstra' => $sasaranRenstra,
            'nama_skpd' => $nama_skpd,
            'selectedPeriodValue' => $selectedPeriodValue,
        ]);

        $pdf = new Pdf([
            'mode' => Pdf::MODE_UTF8,
            'format' => Pdf::FORMAT_A4,
            'orientation' => Pdf::ORIENT_PORTRAIT,
            'destination' => Pdf::DEST_BROWSER,
            'content' => $content,
            'cssFile' => '@vendor/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css',
            'cssInline' => '
            .tbdata, .tblRenstra { border-collapse: collapse; font-family: sans-serif; font-size: 11px; }
            .title { text-align: center; }
            .tblRenstra { width: 100%; }
            .tblRenstra td { padding: 4px; border: 1px solid #ccc; vertical-align: top; }
            .tblRenstra .header { background-color: #03b0e2; color: white; text-align: center; font-weight: bold; }
        ',
            'options' => ['title' => 'Laporan Indikator Kinerja Utama'],
            'methods' => [
                'SetHeader' => ["Indikator Kinerja Utama - {$nama_skpd} - Periode {$selectedPeriodValue}"],
                'SetFooter' => ['{PAGENO}'],
            ]
        ]);
        return $pdf->render();
    }

    public function actionCetakLaporanIkuExcel($refperiode_id = null, $refskpd_id = null)
    {
        $user = Yii::$app->user->identity;
        $assignments = Yii::$app->authManager->getAssignments($user->id);
        $allowedSkpdIds = [];

        if (isset($assignments['superadmin']) || isset($assignments['admin'])) {
            $allowedSkpdIds = ArrayHelper::map(SakipSkpd::find()->where(['skpd_isaktif' => 'T'])->all(), 'refskpd_id', 'refskpd_id');
        } else {
            $allowedSkpdIds = SakipKoordinasi::find()->select('refskpd_id')->where(['refuser_id' => $user->id])->column();
        }
        if (empty($allowedSkpdIds)) {
            $refskpd_id = $user->refskpd_id;
        } elseif ($refskpd_id === null || !in_array($refskpd_id, $allowedSkpdIds)) {
            throw new ForbiddenHttpException('Anda tidak memiliki hak akses atau SKPD tidak valid.');
        }

        $nama_skpd = SakipSkpd::find()->select('nama_skpd')->where(['refskpd_id' => $refskpd_id])->scalar();
        if ($refperiode_id === null) {
            $currentYear = date('Y');
            $defaultPeriod = SakipPeriode::find()->where(['periode' => $currentYear])->one();
            $refperiode_id = $defaultPeriod ? $defaultPeriod->refperiode_id : null;
        }

        $sasaranRenstra = [];
        if ($refskpd_id && $refperiode_id) {
            $indikators = SakipIndikatorsasaranrenstra::find()
                ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
                ->with('refSasaranrenstra') // Eager loading
                ->all();

            foreach ($indikators as $indikator) {
                if ($indikator->refSasaranrenstra) { // Perbaikan nama relasi
                    $sasaranRenstra[] = [
                        'indikator' => $indikator,
                        'formulasi' => $indikator->refSasaranrenstra->formulasi_sasaranrenstra, // Perbaikan nama relasi
                    ];
                }
            }
        }

        $selectedPeriod = SakipPeriode::findOne($refperiode_id);
        $selectedPeriodValue = $selectedPeriod ? $selectedPeriod->periode : 'N/A';

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Laporan IKU');

        $sheet->mergeCells('A1:C1')->setCellValue('A1', 'Indikator Kinerja Utama ' . ucwords(strtolower($nama_skpd)));
        $sheet->mergeCells('A2:C2')->setCellValue('A2', 'Periode ' . $selectedPeriodValue);
        $sheet->getStyle('A1:A2')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1:A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrapText' => true],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF03b0e2']]
        ];
        $sheet->setCellValue('A4', 'No')->setCellValue('B4', 'Indikator Kinerja Utama')->setCellValue('C4', 'Formulasi Sasaran Renstra');
        $sheet->getStyle('A4:C4')->applyFromArray($headerStyle);

        $row = 5;
        foreach ($sasaranRenstra as $index => $item) {
            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, strip_tags($item['indikator']->uraian_indikatorsasaranrenstra));
            $sheet->setCellValue('C' . $row, strip_tags($item['formulasi']));
            $row++;
        }

        foreach (range('A', 'C') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $fileName = "Laporan_IKU_{$nama_skpd}_{$selectedPeriodValue}.xlsx";
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . urlencode($fileName) . '"');
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit();
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
            'selectedSkpdId' => $refskpd_id,
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
                ->where(['refskpd_id' => $refskpd_id, 'refperiode_5tahun_id' => $this->periode5($refperiode_id)])
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

    public function actionCetakLaporanTapkin($refperiode_id = null, $refskpd_id = null, $refpenjabatskpd_id = null, $selected_date = null)
    {
        // 1. Logika Keamanan & Pengambilan Data Dasar
        $user = Yii::$app->user->identity;
        $assignments = Yii::$app->authManager->getAssignments($user->id);
        $allowedSkpdIds = [];

        if (isset($assignments['superadmin']) || isset($assignments['admin'])) {
            $allowedSkpdIds = ArrayHelper::map(SakipSkpd::find()->where(['skpd_isaktif' => 'T'])->all(), 'refskpd_id', 'refskpd_id');
        } else {
            $allowedSkpdIds = SakipKoordinasi::find()->select('refskpd_id')->where(['refuser_id' => $user->id])->column();
        }
        if (empty($allowedSkpdIds)) {
            $refskpd_id = $user->refskpd_id;
        } elseif ($refskpd_id === null || !in_array($refskpd_id, $allowedSkpdIds)) {
            throw new ForbiddenHttpException('Anda tidak memiliki hak akses atau SKPD tidak valid.');
        }

        // 2. Mengambil semua data yang diperlukan
        $nama_skpd = SakipSkpd::find()->select('nama_skpd')->where(['refskpd_id' => $refskpd_id])->scalar();
        $skpdHead = SakipSkpd::find()->where(['refskpd_id' => $refskpd_id])->one();
        $selectedPeriod = SakipPeriode::findOne($refperiode_id);
        $selectedPeriodValue = $selectedPeriod ? $selectedPeriod->periode : date('Y');
        $leadership = SakipPimpinan::find()->where(['refperiode_id' => $refperiode_id])->one();
        $penjabatSkpd = $refpenjabatskpd_id ? SakipPenjabatSkpd::findOne($refpenjabatskpd_id) : null;
        $refeselonId = $penjabatSkpd ? $penjabatSkpd->refeselon_id : null;

        $sasaranRenstra = SakipSasaranrenstra::find()->where(['refskpd_id' => $refskpd_id, 'refperiode_5tahun_id' => $this->periode5($refperiode_id)])->all();
        $indikators = SakipIndikatorsasaranrenstra::find()->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])->all();
        $programs = SakipCascadingprogram::find()->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])->with('refProgram')->groupBy('refprogram_id')->all();

        // 3. OPTIMASI: Hitung total anggaran program dalam satu query
        $totalAnggaranProgram = (new \yii\db\Query())
            ->select(['refprogram_id', 'total_anggaran' => 'SUM(anggaran_pk_p)'])
            ->from('sakip_indikatorcascadingsubkegiatan')
            ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
            ->groupBy('refprogram_id')
            ->all();
        $programAnggaranMap = ArrayHelper::map($totalAnggaranProgram, 'refprogram_id', 'total_anggaran');

        // 4. Data kondisional berdasarkan pejabat
        $penjabatskpdCascadingProgram = [];
        $penjabatskpdCascadingKegiatan = [];
        $penjabatskpdCascadingSubkegiatan = [];
        $indikatorCascadingSubkegiatan = [];
        if ($refpenjabatskpd_id) {
            $penjabatskpdCascadingProgram = SakipPenjabatskpdCascadingprogram::find()->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id, 'refpenjabatskpd_id' => $refpenjabatskpd_id])->with('refProgram')->all();
            $penjabatskpdCascadingKegiatan = SakipPenjabatskpdCascadingkegiatan::find()->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id, 'refpenjabatskpd_id' => $refpenjabatskpd_id])->with('refKegiatan')->all();
            $penjabatskpdCascadingSubkegiatan = SakipPenjabatskpdCascadingsubkegiatan::find()->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id, 'refpenjabatskpd_id' => $refpenjabatskpd_id])->with('refSubkegiatan')->all();
            $indikatorCascadingSubkegiatan = SakipIndikatorcascadingsubkegiatan::find()->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])->all();
        }

        // 5. Render konten ke variabel
        $content = $this->renderPartial('_cetak_laporan_tapkin', [
            'sasaranRenstra' => $sasaranRenstra,
            'indikators' => $indikators,
            'programs' => $programs,
            'programAnggaranMap' => $programAnggaranMap,
            'nama_skpd' => $nama_skpd,
            'selectedPeriodValue' => $selectedPeriodValue,
            'skpdHead' => $skpdHead,
            'leadership' => $leadership,
            'penjabatSkpd' => $penjabatSkpd,
            'refpenjabatskpd_id' => $refpenjabatskpd_id,
            'refeselonId' => $refeselonId,
            'selected_date' => $selected_date,
            'penjabatskpdCascadingProgram' => $penjabatskpdCascadingProgram,
            'penjabatskpdCascadingKegiatan' => $penjabatskpdCascadingKegiatan,
            'penjabatskpdCascadingSubkegiatan' => $penjabatskpdCascadingSubkegiatan,
            'indikatorCascadingSubkegiatan' => $indikatorCascadingSubkegiatan,
            'refskpd_id' => $refskpd_id,
        ]);

        // 6. Konfigurasi PDF dengan CSS Inline (INI BAGIAN PERBAIKANNYA)
        $pdf = new Pdf([
            'mode' => Pdf::MODE_UTF8,
            'format' => Pdf::FORMAT_A4,
            'orientation' => Pdf::ORIENT_PORTRAIT,
            'destination' => Pdf::DEST_BROWSER,
            'content' => $content,
            'cssInline' => '
            #halamanlaporan { font-family: \'Times New Roman\', Times, serif; font-size: 12pt; }
            .isilaporan { line-height: 1.5; }
            .isilaporan h3, .isilaporan h4, .isilaporan h5 { text-align: center; margin: 0; padding: 0; }
            .isilaporan h3 { font-weight: normal; }
            .isilaporan h5 { margin-top: 20px; margin-bottom: 20px; }
            .isilaporan p { text-align: justify; margin-top: 1em; margin-bottom: 1em; }
            .tblPihak { width: 100%; margin-top: 30px; }
            .tblPihak td { width: 50%; text-align: center; }
            .tbdata { width: 100%; margin-top: 20px; border-collapse: collapse; }
            .tbdata th, .tbdata td { padding: 5px; vertical-align: top; border: 1px solid black; }
            .tbdata th { text-align: center; font-weight: bold; }
            .tengah { text-align: center; }
            .kanan { text-align: right; }
            .page-break { page-break-after: always; }
        ',
            'options' => ['title' => 'Laporan Perjanjian Kinerja'],
            'methods' => ['SetFooter' => ['Halaman {PAGENO}'],]
        ]);
        return $pdf->render();
    }


    public function actionCetakLaporanTapkinExcel($refperiode_id = null, $refskpd_id = null, $refpenjabatskpd_id = null)
    {
        // (Blok keamanan dan pengambilan data tetap sama)
        $user = Yii::$app->user->identity;
        $assignments = Yii::$app->authManager->getAssignments($user->id);
        $allowedSkpdIds = [];
        if (isset($assignments['superadmin']) || isset($assignments['admin'])) {
            $allowedSkpdIds = ArrayHelper::map(SakipSkpd::find()->where(['skpd_isaktif' => 'T'])->all(), 'refskpd_id', 'refskpd_id');
        } else {
            $allowedSkpdIds = SakipKoordinasi::find()->select('refskpd_id')->where(['refuser_id' => $user->id])->column();
        }
        if (empty($allowedSkpdIds)) {
            $refskpd_id = $user->refskpd_id;
        } elseif ($refskpd_id === null || !in_array($refskpd_id, $allowedSkpdIds)) {
            throw new ForbiddenHttpException('Anda tidak memiliki hak akses atau SKPD tidak valid.');
        }
        $nama_skpd = SakipSkpd::find()->select('nama_skpd')->where(['refskpd_id' => $refskpd_id])->scalar();
        $selectedPeriod = SakipPeriode::findOne($refperiode_id);
        $selectedPeriodValue = $selectedPeriod ? $selectedPeriod->periode : date('Y');

        $sasaranRenstra = SakipSasaranrenstra::find()->where(['refskpd_id' => $refskpd_id, 'refperiode_5tahun_id' => $this->periode5($refperiode_id)])->all();
        $indikators = SakipIndikatorsasaranrenstra::find()->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])->all();
        $programs = SakipCascadingprogram::find()->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])->with('refProgram')->groupBy('refprogram_id')->all();

        $totalAnggaranProgram = (new \yii\db\Query())->select(['refprogram_id', 'total_anggaran' => 'SUM(anggaran_pk_p)'])->from('sakip_indikatorcascadingsubkegiatan')->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])->groupBy('refprogram_id')->all();
        $programAnggaranMap = ArrayHelper::map($totalAnggaranProgram, 'refprogram_id', 'total_anggaran');

        $spreadsheet = new Spreadsheet();

        // Sheet 1: Kinerja
        $sheetKinerja = $spreadsheet->getActiveSheet();
        $sheetKinerja->setTitle('Kinerja');
        $sheetKinerja->setCellValue('A1', 'Perjanjian Kinerja - Sasaran & Indikator');
        $sheetKinerja->setCellValue('A2', $nama_skpd . ' - Periode ' . $selectedPeriodValue);
        $sheetKinerja->fromArray(['NO', 'SASARAN STRATEGIS', 'KODE', 'INDIKATOR KINERJA', 'SATUAN', 'TARGET'], null, 'A4');

        $row = 5;
        $sasaranCounter = 1;
        foreach ($sasaranRenstra as $sasaran) {
            $firstIndikatorRow = true;
            $indikatorCounter = 1;
            foreach ($indikators as $indikator) {
                if ($indikator->refsasaranrenstra_id == $sasaran->refsasaranrenstra_id) {
                    $sheetKinerja->setCellValue('A' . $row, $firstIndikatorRow ? $sasaranCounter : '');
                    $sheetKinerja->setCellValue('B' . $row, $firstIndikatorRow ? $sasaran->uraian_sasaranrenstra : '');
                    $sheetKinerja->setCellValue('C' . $row, $sasaranCounter . '.' . $indikatorCounter++);
                    $sheetKinerja->setCellValue('D' . $row, $indikator->uraian_indikatorsasaranrenstra);
                    $sheetKinerja->setCellValue('E' . $row, $indikator->indikatorsasaranrenstra_satuan);
                    $sheetKinerja->setCellValue('F' . $row, $indikator->indikatorsasaranrenstra_target);
                    $firstIndikatorRow = false;
                    $row++;
                }
            }
            $sasaranCounter++;
        }

        // Sheet 2: Anggaran
        $sheetAnggaran = $spreadsheet->createSheet();
        $sheetAnggaran->setTitle('Anggaran');
        $sheetAnggaran->setCellValue('A1', 'Perjanjian Kinerja - Program & Anggaran');
        $sheetAnggaran->setCellValue('A2', $nama_skpd . ' - Periode ' . $selectedPeriodValue);
        $sheetAnggaran->fromArray(['No', 'Program', 'Anggaran (Rp)', 'Keterangan'], null, 'A4');

        $row = 5;
        $programCounter = 1;
        $totalAnggaranpkpSum = 0;
        foreach ($programs as $program) {
            $totalAnggaran = $programAnggaranMap[$program->refprogram_id] ?? 0;
            $sheetAnggaran->setCellValue('A' . $row, $programCounter++);
            $sheetAnggaran->setCellValue('B' . $row, $program->refProgram->nama_program);
            $sheetAnggaran->setCellValue('C' . $row, $totalAnggaran);
            $totalAnggaranpkpSum += $totalAnggaran;
            $row++;
        }
        $sheetAnggaran->setCellValue('B' . $row, 'Total');
        $sheetAnggaran->setCellValue('C' . $row, $totalAnggaranpkpSum);

        // --- KODE PERBAIKAN DITAMBAHKAN DI SINI ---
        // 1. Atur Format Angka Menjadi Rupiah
        $currencyFormat = '"Rp." #,##0';
        $sheetAnggaran->getStyle('C5:C' . $row)->getNumberFormat()->setFormatCode($currencyFormat);

        // 2. Atur Lebar Kolom Otomatis untuk Kedua Sheet
        foreach (range('A', 'F') as $columnID) {
            $sheetKinerja->getColumnDimension($columnID)->setAutoSize(true);
        }
        foreach (range('A', 'D') as $columnID) {
            $sheetAnggaran->getColumnDimension($columnID)->setAutoSize(true);
        }
        // --- AKHIR DARI KODE PERBAIKAN ---

        $spreadsheet->setActiveSheetIndex(0);
        $fileName = "Laporan_TAPKIN_{$nama_skpd}_{$selectedPeriodValue}.xlsx";
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . urlencode($fileName) . '"');
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit();
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
            'selectedSkpdId' => $refskpd_id,
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

    public function actionCetakLaporanCapkinIku($refperiode_id = null, $refskpd_id = null)
    {
        // Logika keamanan dan pengambilan data yang digabungkan untuk user biasa & dev
        $user = Yii::$app->user->identity;
        $assignments = Yii::$app->authManager->getAssignments($user->id);
        $allowedSkpdIds = [];

        if (isset($assignments['superadmin']) || isset($assignments['admin'])) {
            $allowedSkpdIds = ArrayHelper::map(SakipSkpd::find()->where(['skpd_isaktif' => 'T'])->all(), 'refskpd_id', 'refskpd_id');
        } else {
            $allowedSkpdIds = SakipKoordinasi::find()->select('refskpd_id')->where(['refuser_id' => $user->id])->column();
        }

        if (empty($allowedSkpdIds)) {
            $refskpd_id = $user->refskpd_id;
        } elseif ($refskpd_id === null || !in_array($refskpd_id, $allowedSkpdIds)) {
            throw new ForbiddenHttpException('Anda tidak memiliki hak akses atau SKPD tidak valid.');
        }

        $nama_skpd = SakipSkpd::find()->select('nama_skpd')->where(['refskpd_id' => $refskpd_id])->scalar();
        if ($refperiode_id === null) {
            $currentYear = date('Y');
            $defaultPeriod = SakipPeriode::find()->where(['periode' => $currentYear])->one();
            $refperiode_id = $defaultPeriod ? $defaultPeriod->refperiode_id : null;
        }

        // Fetch sasaran renstra with related indicators
        $indikators = [];
        if ($refskpd_id && $refperiode_id) {
            $indikators = SakipIndikatorsasaranrenstra::find()
                ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
                ->with('refSasaranrenstra') // Eager load
                ->all();
        }

        $selectedPeriod = SakipPeriode::findOne($refperiode_id);
        $selectedPeriodValue = $selectedPeriod ? $selectedPeriod->periode : 'N/A';

        // Render konten PDF dari partial view baru
        $content = $this->renderPartial('_cetak_laporan_capkin_iku', [
            'indikators' => $indikators,
            'nama_skpd' => $nama_skpd,
            'selectedPeriodValue' => $selectedPeriodValue,
            'refskpd_id' => $refskpd_id, // Kirim refskpd_id untuk query triwulan
        ]);

        // Konfigurasi mPDF
        $pdf = new Pdf([
            'mode' => Pdf::MODE_UTF8,
            'format' => Pdf::FORMAT_A4,
            'orientation' => Pdf::ORIENT_LANDSCAPE, // Menggunakan landscape agar tabel muat
            'destination' => Pdf::DEST_BROWSER,
            'content' => $content,
            'cssFile' => '@vendor/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css',
            'cssInline' => '
            .title { text-align: center; font-family: sans-serif; }
            .tbdata { border-collapse: collapse; width: 100%; font-family: sans-serif; font-size: 9px; }
            .tbdata th, .tbdata td { border: 1px solid #cfcfcf; padding: 4px; vertical-align: top; }
            .tbdata th { background-color: #03b0e2; color: white; text-align: center; }
            .tengah { text-align: center; }
            .keterangan { margin-top: 15px; font-size: 9px; border-collapse: collapse; }
            .keterangan td { border: 1px solid #e3e3e3; padding: 4px; }
        ',
            'options' => ['title' => 'Laporan Capaian Kinerja IKU'],
            'methods' => [
                'SetHeader' => ["Laporan Capaian Kinerja IKU - {$nama_skpd} - Periode {$selectedPeriodValue}"],
                'SetFooter' => ['{PAGENO}'],
            ]
        ]);

        return $pdf->render();
    }

    public function actionCetakLaporanCapkinIkuExcel($refperiode_id = null, $refskpd_id = null)
    {
        // Logika keamanan dan pengambilan data (sama seperti di atas)
        $user = Yii::$app->user->identity;
        $assignments = Yii::$app->authManager->getAssignments($user->id);
        $allowedSkpdIds = [];

        if (isset($assignments['superadmin']) || isset($assignments['admin'])) {
            $allowedSkpdIds = ArrayHelper::map(SakipSkpd::find()->where(['skpd_isaktif' => 'T'])->all(), 'refskpd_id', 'refskpd_id');
        } else {
            $allowedSkpdIds = SakipKoordinasi::find()->select('refskpd_id')->where(['refuser_id' => $user->id])->column();
        }
        if (empty($allowedSkpdIds)) {
            $refskpd_id = $user->refskpd_id;
        } elseif ($refskpd_id === null || !in_array($refskpd_id, $allowedSkpdIds)) {
            throw new ForbiddenHttpException('Anda tidak memiliki hak akses atau SKPD tidak valid.');
        }

        $nama_skpd = SakipSkpd::find()->select('nama_skpd')->where(['refskpd_id' => $refskpd_id])->scalar();
        if ($refperiode_id === null) {
            $currentYear = date('Y');
            $defaultPeriod = SakipPeriode::find()->where(['periode' => $currentYear])->one();
            $refperiode_id = $defaultPeriod ? $defaultPeriod->refperiode_id : null;
        }

        // Mengambil data
        $indikators = [];
        if ($refskpd_id && $refperiode_id) {
            $indikators = SakipIndikatorsasaranrenstra::find()
                ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
                ->with(['refSasaranrenstra', 'indikatorsasaranrenstraTriwulan']) // Eager load
                ->all();
        }

        $selectedPeriod = SakipPeriode::findOne($refperiode_id);
        $selectedPeriodValue = $selectedPeriod ? $selectedPeriod->periode : 'N/A';

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Laporan Capkin IKU');

        // Judul
        $sheet->mergeCells('A1:K1')->setCellValue('A1', 'Capaian Kinerja Indikator Kinerja Utama ' . ucwords(strtolower($nama_skpd)));
        $sheet->mergeCells('A2:K2')->setCellValue('A2', 'Periode ' . $selectedPeriodValue);
        $sheet->getStyle('A1:A2')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1:A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Header
        $headers = ['Nomor Renstra', 'Sasaran Renstra', 'Nomor Indikator', 'Indikator Sasaran Renstra', 'Satuan', 'Target Tahun', 'Triwulan', 'Target', 'Realisasi', 'Capaian %', 'Keterangan'];
        $sheet->fromArray($headers, null, 'A4');
        $sheet->getStyle('A4:K4')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF03b0e2']]
        ]);

        // Isi data
        $row = 5;
        $renstraNomor = 0;
        $indikatorNomor = 0;
        $lastRefSasaranRenstraId = null;
        foreach ($indikators as $indikator) {
            $rowspan = count($indikator->indikatorsasaranrenstraTriwulan) + 1;

            if ($indikator->refsasaranrenstra_id !== $lastRefSasaranRenstraId) {
                $renstraNomor++;
                $indikatorNomor = 1;
                $lastRefSasaranRenstraId = $indikator->refsasaranrenstra_id;
                $sheet->mergeCells('A' . $row . ':A' . ($row + $rowspan - 1))->setCellValue('A' . $row, $renstraNomor);
                $sheet->mergeCells('B' . $row . ':B' . ($row + $rowspan - 1))->setCellValue('B' . $row, $indikator->refSasaranrenstra->uraian_sasaranrenstra);
            } else {
                $indikatorNomor++;
            }

            $sheet->mergeCells('C' . $row . ':C' . ($row + $rowspan - 1))->setCellValue('C' . $row, $renstraNomor . '.' . $indikatorNomor);
            $sheet->mergeCells('D' . $row . ':D' . ($row + $rowspan - 1))->setCellValue('D' . $row, $indikator->uraian_indikatorsasaranrenstra);
            $sheet->mergeCells('E' . $row . ':E' . ($row + $rowspan - 1))->setCellValue('E' . $row, $indikator->indikatorsasaranrenstra_satuan);
            $sheet->mergeCells('F' . $row . ':F' . ($row + $rowspan - 1))->setCellValue('F' . $row, $indikator->indikatorsasaranrenstra_target);

            // Data Triwulan
            foreach ($indikator->indikatorsasaranrenstraTriwulan as $triwulan) {
                $sheet->setCellValue('G' . $row, 'Triwulan ' . $triwulan->reftriwulan_id);
                $sheet->setCellValue('H' . $row, $triwulan->triwulan_target_pk_p);
                $sheet->setCellValue('I' . $row, $triwulan->triwulan_realisasi);
                $sheet->setCellValue('J' . $row, $triwulan->triwulan_capaian);
                $row++;
            }
            // Kondisi Akhir
            $sheet->setCellValue('G' . $row, 'Kondisi Akhir F');
            $sheet->setCellValue('H' . $row, ''); // Target akhir kosong
            $sheet->setCellValue('I' . $row, $indikator->realisasi);
            $sheet->setCellValue('J' . $row, $indikator->capaian);
            $row++;
        }

        // Atur lebar kolom
        foreach (range('A', 'K') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $fileName = "Laporan_Capkin_IKU_{$nama_skpd}_{$selectedPeriodValue}.xlsx";
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . urlencode($fileName) . '"');
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit();
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
            'selectedSkpdId' => $refskpd_id,
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
                ->with(['refCascadingProgram', 'refProgram', 'refSasaranrenstra', 'refSubkegiatan'])
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
            $programId = $indikator->refcascadingprogram_id;
            if (!isset($data[$sasaranId]['programs'][$programId])) {
                $data[$sasaranId]['programs'][$programId] = [
                    'nama_program' => $indikator->refProgram ? $indikator->refProgram->nama_program : 'N/A',
                    'anggaran_pk_p' => 0,
                    'subkegiatan' => [],
                    'quarterly' => []
                ];
            }
            $subkegiatanId = $indikator->refsubkegiatan_id;
            if (!isset($data[$sasaranId]['programs'][$programId]['subkegiatan'][$subkegiatanId])) {
                $data[$sasaranId]['programs'][$programId]['subkegiatan'][$subkegiatanId] = [
                    'nama_subkegiatan' => $indikator->refSubkegiatan ? $indikator->refSubkegiatan->nama_subkegiatan : 'N/A',
                    'anggaran_subkegiatan' => (float) ($indikator->anggaran_pk_p ?? 0)
                ];
            }
        }
        foreach ($data as $sasaranId => $entry) {
            foreach ($entry['programs'] as $programId => $program) {
                $totalProgramAnggaran = array_sum(array_column($program['subkegiatan'], 'anggaran_subkegiatan'));
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
                            'triwulan_penyerapan_anggaran' => 0,
                            'triwulan_realisasi' => 0
                        ];
                    }
                    $data[$sasaranId]['programs'][$programId]['quarterly'][$quarterIndex]['triwulan_penyerapan_anggaran'] += (float) ($quarter->triwulan_penyerapan_anggaran ?? 0);
                    $data[$sasaranId]['programs'][$programId]['quarterly'][$quarterIndex]['triwulan_realisasi'] += (float) ($quarter->triwulan_capaian ?? 0);
                    $data[$sasaranId]['total_quarterly_penyerapan_anggaran'][$quarterIndex] += (float) ($quarter->triwulan_penyerapan_anggaran ?? 0);
                    $data[$sasaranId]['total_quarterly_realisasi'][$quarterIndex] += (float) ($quarter->triwulan_capaian ?? 0);
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
            'indikators' => $indikators,
            'data' => $data,
            'nama_skpd' => $nama_skpd,
            'selectedPeriodValue' => $selectedPeriodValue,
            'selectedSkpdId' => $refskpd_id,
            'skpdList' => $skpdList,
        ]);
    }

    public function actionCetakRealisasiAnggaran($refperiode_id = null, $refskpd_id = null)
    {
        // Logika keamanan dan pengambilan data yang digabungkan untuk user biasa & dev
        $user = Yii::$app->user->identity;
        $assignments = Yii::$app->authManager->getAssignments($user->id);
        $allowedSkpdIds = [];

        if (isset($assignments['superadmin']) || isset($assignments['admin'])) {
            $allowedSkpdIds = ArrayHelper::map(SakipSkpd::find()->where(['skpd_isaktif' => 'T'])->all(), 'refskpd_id', 'refskpd_id');
        } else {
            $allowedSkpdIds = SakipKoordinasi::find()->select('refskpd_id')->where(['refuser_id' => $user->id])->column();
        }

        if (empty($allowedSkpdIds)) {
            $refskpd_id = $user->refskpd_id;
        } elseif ($refskpd_id === null || !in_array($refskpd_id, $allowedSkpdIds)) {
            throw new ForbiddenHttpException('Anda tidak memiliki hak akses atau SKPD tidak valid.');
        }

        $nama_skpd = SakipSkpd::find()->select('nama_skpd')->where(['refskpd_id' => $refskpd_id])->scalar();
        if ($refperiode_id === null) {
            $currentYear = date('Y');
            $defaultPeriod = SakipPeriode::find()->where(['periode' => $currentYear])->one();
            $refperiode_id = $defaultPeriod ? $defaultPeriod->refperiode_id : null;
        }

        // Logika pengambilan data (sama seperti di actionIndexLaporanRealisasiAnggaran)
        $indikators = SakipIndikatorcascadingsubkegiatan::find()
            ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
            ->with(['refCascadingProgram', 'refProgram', 'refSasaranrenstra', 'refSubkegiatan'])
            ->all();
        $quarterlyData = SakipIndikatorcascadingsubkegiatanTriwulan::find()
            ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
            ->all();

        // Proses dan group data (sama seperti di actionIndex...)
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
            $programId = $indikator->refcascadingprogram_id;
            if (!isset($data[$sasaranId]['programs'][$programId])) {
                $data[$sasaranId]['programs'][$programId] = [
                    'nama_program' => $indikator->refProgram->nama_program,
                    'anggaran_pk_p' => 0,
                    'subkegiatan' => []
                ];
            }
            $subkegiatanId = $indikator->refsubkegiatan_id;
            if (!isset($data[$sasaranId]['programs'][$programId]['subkegiatan'][$subkegiatanId])) {
                $data[$sasaranId]['programs'][$programId]['subkegiatan'][$subkegiatanId] = [
                    'nama_subkegiatan' => $indikator->refSubkegiatan->nama_subkegiatan,
                    'anggaran_subkegiatan' => (float) ($indikator->anggaran_pk_p ?? 0)
                ];
            }
        }
        foreach ($data as $sasaranId => $entry) {
            foreach ($entry['programs'] as $programId => $program) {
                $totalProgramAnggaran = array_sum(array_column($program['subkegiatan'], 'anggaran_subkegiatan'));
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
                            'triwulan_penyerapan_anggaran' => 0,
                            'triwulan_realisasi' => 0
                        ];
                    }
                    $data[$sasaranId]['programs'][$programId]['quarterly'][$quarterIndex]['triwulan_penyerapan_anggaran'] += (float) ($quarter->triwulan_penyerapan_anggaran ?? 0);
                    $data[$sasaranId]['programs'][$programId]['quarterly'][$quarterIndex]['triwulan_realisasi'] += (float) ($quarter->triwulan_capaian ?? 0);
                    $data[$sasaranId]['total_quarterly_penyerapan_anggaran'][$quarterIndex] += (float) ($quarter->triwulan_penyerapan_anggaran ?? 0);
                    $data[$sasaranId]['total_quarterly_realisasi'][$quarterIndex] += (float) ($quarter->triwulan_capaian ?? 0);
                }
            }
        }

        $selectedPeriod = SakipPeriode::findOne($refperiode_id);
        $selectedPeriodValue = $selectedPeriod ? $selectedPeriod->periode : 'N/A';

        $content = $this->renderPartial('_cetak_laporan_realisasi_anggaran', [
            'data' => $data,
            'nama_skpd' => $nama_skpd,
            'selectedPeriodValue' => $selectedPeriodValue,
        ]);

        $pdf = new Pdf([
            'mode' => Pdf::MODE_UTF8,
            'format' => Pdf::FORMAT_A4,
            'orientation' => Pdf::ORIENT_LANDSCAPE,
            'destination' => Pdf::DEST_BROWSER,
            'content' => $content,
            'cssFile' => '@vendor/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css',
            'cssInline' => '
                .title { text-align: center; font-family: sans-serif; }
                .tbdata { border-collapse: collapse; width: 100%; font-family: sans-serif; font-size: 9px; }
                .tbdata th, .tbdata td { border: 1px solid #cfcfcf; padding: 4px; vertical-align: top; }
                .tbdata th { background-color: #03b0e2; color: white; text-align: center; }
                .tengah { text-align: center; }
                .kanan { text-align: right; }
            ',
            'options' => ['title' => 'Laporan Realisasi Anggaran'],
            'methods' => [
                'SetHeader' => ["Laporan Pagu dan Realisasi Anggaran - {$nama_skpd} - Periode {$selectedPeriodValue}"],
                'SetFooter' => ['{PAGENO}'],
            ]
        ]);
        return $pdf->render();
    }

    public function actionCetakRealisasiAnggaranExcel($refperiode_id = null, $refskpd_id = null)
    {
        // Logika keamanan dan pengambilan data (sama seperti di atas)
        $user = Yii::$app->user->identity;
        $assignments = Yii::$app->authManager->getAssignments($user->id);
        $allowedSkpdIds = [];
        if (isset($assignments['superadmin']) || isset($assignments['admin'])) {
            $allowedSkpdIds = ArrayHelper::map(SakipSkpd::find()->where(['skpd_isaktif' => 'T'])->all(), 'refskpd_id', 'refskpd_id');
        } else {
            $allowedSkpdIds = SakipKoordinasi::find()->select('refskpd_id')->where(['refuser_id' => $user->id])->column();
        }
        if (empty($allowedSkpdIds)) {
            $refskpd_id = $user->refskpd_id;
        } elseif ($refskpd_id === null || !in_array($refskpd_id, $allowedSkpdIds)) {
            throw new ForbiddenHttpException('Anda tidak memiliki hak akses atau SKPD tidak valid.');
        }
        $nama_skpd = SakipSkpd::find()->select('nama_skpd')->where(['refskpd_id' => $refskpd_id])->scalar();
        if ($refperiode_id === null) {
            $currentYear = date('Y');
            $defaultPeriod = SakipPeriode::find()->where(['periode' => $currentYear])->one();
            $refperiode_id = $defaultPeriod ? $defaultPeriod->refperiode_id : null;
        }

        // Logika pengambilan dan pemrosesan data (sama seperti di atas)
        $indikators = SakipIndikatorcascadingsubkegiatan::find()
            ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
            ->with(['refCascadingProgram', 'refProgram', 'refSasaranrenstra', 'refSubkegiatan'])
            ->all();
        $quarterlyData = SakipIndikatorcascadingsubkegiatanTriwulan::find()
            ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
            ->all();
        $data = [];
        // (Salin blok pemrosesan data $data dari actionCetakRealisasiAnggaran di atas)
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
            $programId = $indikator->refcascadingprogram_id;
            if (!isset($data[$sasaranId]['programs'][$programId])) {
                $data[$sasaranId]['programs'][$programId] = [
                    'nama_program' => $indikator->refProgram->nama_program,
                    'anggaran_pk_p' => 0,
                    'subkegiatan' => []
                ];
            }
            $subkegiatanId = $indikator->refsubkegiatan_id;
            if (!isset($data[$sasaranId]['programs'][$programId]['subkegiatan'][$subkegiatanId])) {
                $data[$sasaranId]['programs'][$programId]['subkegiatan'][$subkegiatanId] = [
                    'nama_subkegiatan' => $indikator->refSubkegiatan->nama_subkegiatan,
                    'anggaran_subkegiatan' => (float) ($indikator->anggaran_pk_p ?? 0)
                ];
            }
        }
        foreach ($data as $sasaranId => $entry) {
            foreach ($entry['programs'] as $programId => $program) {
                $totalProgramAnggaran = array_sum(array_column($program['subkegiatan'], 'anggaran_subkegiatan'));
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
                            'triwulan_penyerapan_anggaran' => 0,
                            'triwulan_realisasi' => 0
                        ];
                    }
                    $data[$sasaranId]['programs'][$programId]['quarterly'][$quarterIndex]['triwulan_penyerapan_anggaran'] += (float) ($quarter->triwulan_penyerapan_anggaran ?? 0);
                    $data[$sasaranId]['programs'][$programId]['quarterly'][$quarterIndex]['triwulan_realisasi'] += (float) ($quarter->triwulan_capaian ?? 0);
                    $data[$sasaranId]['total_quarterly_penyerapan_anggaran'][$quarterIndex] += (float) ($quarter->triwulan_penyerapan_anggaran ?? 0);
                    $data[$sasaranId]['total_quarterly_realisasi'][$quarterIndex] += (float) ($quarter->triwulan_capaian ?? 0);
                }
            }
        }

        $selectedPeriod = SakipPeriode::findOne($refperiode_id);
        $selectedPeriodValue = $selectedPeriod ? $selectedPeriod->periode : 'N/A';

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Realisasi Anggaran');

        // Judul
        $sheet->mergeCells('A1:K1')->setCellValue('A1', 'Pagu dan Realisasi Anggaran ' . ucwords(strtolower($nama_skpd)));
        $sheet->mergeCells('A2:K2')->setCellValue('A2', 'Periode ' . $selectedPeriodValue);
        $sheet->getStyle('A1:A2')->getFont()->setBold(true)->setSize(14)->setItalic(true);
        $sheet->getStyle('A1:A2')->getAlignment()->setHorizontal('center');

        // Header
        $sheet->mergeCells('A4:A5')->setCellValue('A4', 'No');
        $sheet->mergeCells('B4:B5')->setCellValue('B4', 'Program');
        $sheet->mergeCells('C4:C5')->setCellValue('C4', 'Pagu Anggaran');
        $sheet->mergeCells('D4:E4')->setCellValue('D4', 'Triwulan 1');
        $sheet->setCellValue('D5', 'Penyerapan')->setCellValue('E5', 'Realisasi (%)');
        $sheet->mergeCells('F4:G4')->setCellValue('F4', 'Triwulan 2');
        $sheet->setCellValue('F5', 'Penyerapan')->setCellValue('G5', 'Realisasi (%)');
        $sheet->mergeCells('H4:I4')->setCellValue('H4', 'Triwulan 3');
        $sheet->setCellValue('H5', 'Penyerapan')->setCellValue('I5', 'Realisasi (%)');
        $sheet->mergeCells('J4:K4')->setCellValue('J4', 'Triwulan 4');
        $sheet->setCellValue('J5', 'Penyerapan')->setCellValue('K5', 'Realisasi (%)');

        $headerStyle = ['font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']], 'fill' => ['fillType' => 'solid', 'startColor' => ['argb' => 'FF03b0e2']], 'alignment' => ['horizontal' => 'center', 'vertical' => 'center', 'wrapText' => true]];
        $sheet->getStyle('A4:K5')->applyFromArray($headerStyle);

        // Isi data
        $row = 6;
        $no = 1;
        foreach ($data as $entry) {
            $sheet->mergeCells('A' . $row . ':K' . $row)->setCellValue('A' . $row, $entry['uraian_sasaran']);
            $sheet->getStyle('A' . $row)->getFont()->setBold(true);
            $row++;
            foreach ($entry['programs'] as $program) {
                $sheet->setCellValue('A' . $row, $no++)->getStyle('A' . $row)->getAlignment()->setHorizontal('center');
                $sheet->setCellValue('B' . $row, $program['nama_program']);
                $sheet->setCellValue('C' . $row, $program['anggaran_pk_p']);
                for ($i = 0; $i < 4; $i++) {
                    $col = chr(68 + $i * 2); // D, F, H, J
                    $penyerapan = $program['quarterly'][$i]['triwulan_penyerapan_anggaran'] ?? 0;
                    $pagu = $program['anggaran_pk_p'];
                    $persen = ($pagu > 0) ? ($penyerapan / $pagu) * 100 : 0;
                    $sheet->setCellValue($col . $row, $penyerapan);
                    $sheet->setCellValue(chr(ord($col) + 1) . $row, round($persen, 2));
                }
                $row++;
            }
            $sheet->mergeCells('A' . $row . ':B' . $row)->setCellValue('A' . $row, 'Total Anggaran:')->getStyle('A' . $row)->getAlignment()->setHorizontal('right');
            $sheet->setCellValue('C' . $row, $entry['total_anggaran']);
            for ($i = 0; $i < 4; $i++) {
                $col = chr(68 + $i * 2);
                $penyerapanTotal = $entry['total_quarterly_penyerapan_anggaran'][$i] ?? 0;
                $paguTotal = $entry['total_anggaran'];
                $persenTotal = ($paguTotal > 0) ? ($penyerapanTotal / $paguTotal) * 100 : 0;
                $sheet->setCellValue($col . $row, $penyerapanTotal);
                $sheet->setCellValue(chr(ord($col) + 1) . $row, round($persenTotal, 2));
            }
            $sheet->getStyle('A' . $row . ':K' . $row)->getFont()->setBold(true);
            $row++;
        }

        $currencyFormat = '"Rp. "#,##0.00';
        $sheet->getStyle('C6:C' . $row)->getNumberFormat()->setFormatCode($currencyFormat);
        $sheet->getStyle('D6:D' . $row)->getNumberFormat()->setFormatCode($currencyFormat);
        $sheet->getStyle('F6:F' . $row)->getNumberFormat()->setFormatCode($currencyFormat);
        $sheet->getStyle('H6:H' . $row)->getNumberFormat()->setFormatCode($currencyFormat);
        $sheet->getStyle('J6:J' . $row)->getNumberFormat()->setFormatCode($currencyFormat);

        foreach (range('A', 'K') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $fileName = "Laporan_Realisasi_Anggaran_{$nama_skpd}_{$selectedPeriodValue}.xlsx";
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . urlencode($fileName) . '"');
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit();
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
            'selectedSkpdId' => $refskpd_id,
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

    public function actionCetakRencanaAksi($refperiode_id = null, $refskpd_id = null)
    {
        // Logika keamanan dan pengambilan data
        $user = Yii::$app->user->identity;
        $assignments = Yii::$app->authManager->getAssignments($user->id);
        $allowedSkpdIds = [];

        if (isset($assignments['superadmin']) || isset($assignments['admin'])) {
            $allowedSkpdIds = ArrayHelper::map(SakipSkpd::find()->where(['skpd_isaktif' => 'T'])->all(), 'refskpd_id', 'refskpd_id');
        } else {
            $allowedSkpdIds = SakipKoordinasi::find()->select('refskpd_id')->where(['refuser_id' => $user->id])->column();
        }

        if (empty($allowedSkpdIds)) {
            $refskpd_id = $user->refskpd_id;
        } elseif ($refskpd_id === null || !in_array($refskpd_id, $allowedSkpdIds)) {
            throw new ForbiddenHttpException('Anda tidak memiliki hak akses atau SKPD tidak valid.');
        }

        $nama_skpd = SakipSkpd::find()->select('nama_skpd')->where(['refskpd_id' => $refskpd_id])->scalar();
        if ($refperiode_id === null) {
            $currentYear = date('Y');
            $defaultPeriod = SakipPeriode::find()->where(['periode' => $currentYear])->one();
            $refperiode_id = $defaultPeriod ? $defaultPeriod->refperiode_id : null;
        }

        // Logika pengambilan data (sama seperti di actionIndexLaporanRencanaAksi)
        $cascadingSubkegiatan = [];
        $groupedSubkegiatan = [];
        $anggaranPerKegiatan = [];
        $anggaranPerProgram = [];

        if ($refskpd_id && $refperiode_id) {
            $cascadingSubkegiatan = SakipCascadingsubkegiatan::find()
                ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
                ->with([
                    'indikatorTriwulan',
                    'refCascadingKegiatan.refKegiatan',
                    'refCascadingKegiatan.refCascadingProgram.refProgram',
                    'refSubkegiatan'
                ])
                ->all();

            foreach ($cascadingSubkegiatan as $item) {
                if (isset($item->refCascadingKegiatan->refcascadingkegiatan_id)) {
                    $groupedSubkegiatan[$item->refCascadingKegiatan->refcascadingkegiatan_id][] = $item;
                }
            }
            foreach ($groupedSubkegiatan as $refcascadingkegiatan_id => $subkegiatanGroup) {
                $totalAnggaran = array_sum(array_column($subkegiatanGroup, 'subkegiatan_anggaran'));
                $anggaranPerKegiatan[$refcascadingkegiatan_id] = $totalAnggaran;
            }
            foreach ($cascadingSubkegiatan as $subkegiatan) {
                if (isset($subkegiatan->refCascadingKegiatan->refCascadingProgram->refProgram->refprogram_id)) {
                    $programId = $subkegiatan->refCascadingKegiatan->refCascadingProgram->refProgram->refprogram_id;
                    if (!isset($anggaranPerProgram[$programId])) {
                        $anggaranPerProgram[$programId] = 0;
                    }
                    $anggaranPerProgram[$programId] += $subkegiatan->subkegiatan_anggaran;
                }
            }
        }

        $selectedPeriod = SakipPeriode::findOne($refperiode_id);
        $selectedPeriodValue = $selectedPeriod ? $selectedPeriod->periode : 'N/A';

        $content = $this->renderPartial('_cetak_laporan_rencana_aksi', [
            'groupedSubkegiatan' => $groupedSubkegiatan,
            'anggaranPerKegiatan' => $anggaranPerKegiatan,
            'anggaranPerProgram' => $anggaranPerProgram,
            'nama_skpd' => $nama_skpd,
            'selectedPeriodValue' => $selectedPeriodValue,
        ]);

        $pdf = new Pdf([
            'mode' => Pdf::MODE_UTF8,
            'format' => Pdf::FORMAT_A4,
            'orientation' => Pdf::ORIENT_LANDSCAPE,
            'destination' => Pdf::DEST_BROWSER,
            'content' => $content,
            'cssFile' => '@vendor/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css',
            'cssInline' => '
            .title { text-align: center; font-family: sans-serif; }
            .tbdata { border-collapse: collapse; width: 100%; font-family: sans-serif; font-size: 9px; }
            .tbdata th, .tbdata td { border: 1px solid #5d5d5d; padding: 4px; vertical-align: top; white-space: normal; }
            .tbdata th { background-color: #03b0e2; color: white; text-align: center; }
            .tengah { text-align: center; }
        ',
            'options' => ['title' => 'Laporan Rencana Aksi'],
            'methods' => [
                'SetHeader' => ["Laporan Rencana Aksi - {$nama_skpd} - Periode {$selectedPeriodValue}"],
                'SetFooter' => ['{PAGENO}'],
            ]
        ]);
        return $pdf->render();
    }

    public function actionCetakRencanaAksiExcel($refperiode_id = null, $refskpd_id = null)
    {
        // Logika keamanan dan pengambilan data (sama seperti di atas)
        $user = Yii::$app->user->identity;
        $assignments = Yii::$app->authManager->getAssignments($user->id);
        $allowedSkpdIds = [];
        if (isset($assignments['superadmin']) || isset($assignments['admin'])) {
            $allowedSkpdIds = ArrayHelper::map(SakipSkpd::find()->where(['skpd_isaktif' => 'T'])->all(), 'refskpd_id', 'refskpd_id');
        } else {
            $allowedSkpdIds = SakipKoordinasi::find()->select('refskpd_id')->where(['refuser_id' => $user->id])->column();
        }
        if (empty($allowedSkpdIds)) {
            $refskpd_id = $user->refskpd_id;
        } elseif ($refskpd_id === null || !in_array($refskpd_id, $allowedSkpdIds)) {
            throw new ForbiddenHttpException('Anda tidak memiliki hak akses atau SKPD tidak valid.');
        }
        $nama_skpd = SakipSkpd::find()->select('nama_skpd')->where(['refskpd_id' => $refskpd_id])->scalar();
        if ($refperiode_id === null) {
            $currentYear = date('Y');
            $defaultPeriod = SakipPeriode::find()->where(['periode' => $currentYear])->one();
            $refperiode_id = $defaultPeriod ? $defaultPeriod->refperiode_id : null;
        }

        // Logika pengambilan dan pemrosesan data (sama seperti di atas)
        // (Salin blok logika pengambilan data dari actionCetakRencanaAksi)
        $cascadingSubkegiatan = [];
        $groupedSubkegiatan = [];
        $anggaranPerKegiatan = [];
        $anggaranPerProgram = [];
        if ($refskpd_id && $refperiode_id) {
            $cascadingSubkegiatan = SakipCascadingsubkegiatan::find()
                ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
                ->with([
                    'indikatorTriwulan',
                    'refCascadingKegiatan.refKegiatan',
                    'refCascadingKegiatan.refCascadingProgram.refProgram',
                    'refSubkegiatan'
                ])
                ->all();

            foreach ($cascadingSubkegiatan as $item) {
                if (isset($item->refCascadingKegiatan->refcascadingkegiatan_id)) {
                    $groupedSubkegiatan[$item->refCascadingKegiatan->refcascadingkegiatan_id][] = $item;
                }
            }
            foreach ($groupedSubkegiatan as $refcascadingkegiatan_id => $subkegiatanGroup) {
                $totalAnggaran = array_sum(array_column($subkegiatanGroup, 'subkegiatan_anggaran'));
                $anggaranPerKegiatan[$refcascadingkegiatan_id] = $totalAnggaran;
            }
            foreach ($cascadingSubkegiatan as $subkegiatan) {
                if (isset($subkegiatan->refCascadingKegiatan->refCascadingProgram->refProgram->refprogram_id)) {
                    $programId = $subkegiatan->refCascadingKegiatan->refCascadingProgram->refProgram->refprogram_id;
                    if (!isset($anggaranPerProgram[$programId])) {
                        $anggaranPerProgram[$programId] = 0;
                    }
                    $anggaranPerProgram[$programId] += $subkegiatan->subkegiatan_anggaran;
                }
            }
        }
        $selectedPeriod = SakipPeriode::findOne($refperiode_id);
        $selectedPeriodValue = $selectedPeriod ? $selectedPeriod->periode : 'N/A';

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Rencana Aksi');

        // Judul
        $sheet->mergeCells('A1:I1')->setCellValue('A1', 'Rencana Aksi ' . ucwords(strtolower($nama_skpd)));
        $sheet->mergeCells('A2:I2')->setCellValue('A2', 'Periode ' . $selectedPeriodValue);
        $sheet->getStyle('A1:A2')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1:A2')->getAlignment()->setHorizontal('center');

        // Header
        $headers = ['No', 'Program', 'Anggaran Program', 'Kegiatan', 'Anggaran Kegiatan', 'Sub Kegiatan', 'Anggaran Sub Kegiatan', 'Indikator Sub Kegiatan', 'Target Sub Kegiatan'];
        $sheet->fromArray($headers, null, 'A4');
        $sheet->getStyle('A4:I4')->applyFromArray(['font' => ['bold' => true], 'fill' => ['fillType' => 'solid', 'startColor' => ['argb' => 'FF03b0e2']], 'alignment' => ['horizontal' => 'center', 'vertical' => 'center', 'wrapText' => true]]);

        // Isi Data
        $row = 5;
        $nomor = 1;
        foreach ($groupedSubkegiatan as $refcascadingkegiatan_id => $subkegiatanGroup) {
            $rowspan = count($subkegiatanGroup);
            $firstItem = reset($subkegiatanGroup);
            $startRow = $row;
            foreach ($subkegiatanGroup as $index => $item) {
                $sheet->setCellValue('F' . $row, $item->refSubkegiatan ? $item->refSubkegiatan->nama_subkegiatan : '-');
                $sheet->setCellValue('G' . $row, $item->subkegiatan_anggaran);
                $sheet->setCellValue('H' . $row, $item->uraian_indikatorsubkegiatan);
                $targetStr = '';
                if ($item->indikatorTriwulan) {
                    foreach ($item->indikatorTriwulan as $triwulan) {
                        $targetStr .= "Trw ({$triwulan->reftriwulan_id}) = {$triwulan->triwulan_target_rkt}\n";
                    }
                }
                $sheet->setCellValue('I' . $row, $targetStr);
                $sheet->getStyle('I' . $row)->getAlignment()->setWrapText(true);
                $row++;
            }

            $sheet->mergeCells('A' . $startRow . ':A' . ($row - 1))->setCellValue('A' . $startRow, $nomor++)->getStyle('A' . $startRow)->getAlignment()->setVertical('center')->setHorizontal('center');
            $sheet->mergeCells('B' . $startRow . ':B' . ($row - 1))->setCellValue('B' . $startRow, $firstItem->refCascadingKegiatan->refCascadingProgram->refProgram->nama_program)->getStyle('B' . $startRow)->getAlignment()->setWrapText(true)->setVertical('center');
            $sheet->mergeCells('C' . $startRow . ':C' . ($row - 1))->setCellValue('C' . $startRow, $anggaranPerProgram[$firstItem->refCascadingKegiatan->refCascadingProgram->refProgram->refprogram_id] ?? 0)->getStyle('C' . $startRow)->getAlignment()->setVertical('center');
            $sheet->mergeCells('D' . $startRow . ':D' . ($row - 1))->setCellValue('D' . $startRow, $firstItem->refCascadingKegiatan->refKegiatan->nama_kegiatan)->getStyle('D' . $startRow)->getAlignment()->setWrapText(true)->setVertical('center');
            $sheet->mergeCells('E' . $startRow . ':E' . ($row - 1))->setCellValue('E' . $startRow, $anggaranPerKegiatan[$refcascadingkegiatan_id] ?? 0)->getStyle('E' . $startRow)->getAlignment()->setVertical('center');
        }

        $currencyFormat = '"Rp. "#,##0';
        $sheet->getStyle('C5:C' . $row)->getNumberFormat()->setFormatCode($currencyFormat);
        $sheet->getStyle('E5:E' . $row)->getNumberFormat()->setFormatCode($currencyFormat);
        $sheet->getStyle('G5:G' . $row)->getNumberFormat()->setFormatCode($currencyFormat);

        foreach (range('A', 'I') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $fileName = "Laporan_Rencana_Aksi_{$nama_skpd}_{$selectedPeriodValue}.xlsx";
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . urlencode($fileName) . '"');
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit();
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
            ->where(['refskpd_id' => $refskpd_id, 'refperiode_5tahun_id' => $this->periode5($refperiode_id)])
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
            'selectedSkpdId' => $refskpd_id,
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
                ->where(['refskpd_id' => $refskpd_id, 'refperiode_5tahun_id' => $this->periode5($refperiode_id)])
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

    public function actionCetakLaporanEkinerja($refperiode_id = null, $refskpd_id = null)
    {
        // Logika keamanan dan pengambilan data
        $user = Yii::$app->user->identity;
        $assignments = Yii::$app->authManager->getAssignments($user->id);
        $allowedSkpdIds = [];

        if (isset($assignments['superadmin']) || isset($assignments['admin'])) {
            $allowedSkpdIds = ArrayHelper::map(SakipSkpd::find()->where(['skpd_isaktif' => 'T'])->all(), 'refskpd_id', 'refskpd_id');
        } else {
            $allowedSkpdIds = SakipKoordinasi::find()->select('refskpd_id')->where(['refuser_id' => $user->id])->column();
        }

        if (empty($allowedSkpdIds)) {
            $refskpd_id = $user->refskpd_id;
        } elseif ($refskpd_id === null || !in_array($refskpd_id, $allowedSkpdIds)) {
            throw new ForbiddenHttpException('Anda tidak memiliki hak akses atau SKPD tidak valid.');
        }

        $nama_skpd = SakipSkpd::find()->select('nama_skpd')->where(['refskpd_id' => $refskpd_id])->scalar();
        if ($refperiode_id === null) {
            $currentYear = date('Y');
            $defaultPeriod = SakipPeriode::find()->where(['periode' => $currentYear])->one();
            $refperiode_id = $defaultPeriod ? $defaultPeriod->refperiode_id : null;
        }

        // Logika pengambilan data (sama seperti di actionIndexLaporanEkinerja)
        $sasaranRenstra = [];
        $programDataMap = [];
        if ($refskpd_id && $refperiode_id) {
            $sasaranRenstra = SakipSasaranrenstra::find()
                ->where(['refskpd_id' => $refskpd_id, 'refperiode_5tahun_id' => $this->periode5($refperiode_id)])
                ->with([
                    'indikatorSasaran.cascadingPrograms.refProgram'
                ])
                ->all();

            // Optimasi: Kumpulkan semua ID program dan hitung total dalam satu query
            $programIds = [];
            foreach ($sasaranRenstra as $sasaran) {
                foreach ($sasaran->indikatorSasaran as $indikator) {
                    foreach ($indikator->cascadingPrograms as $cascadingProgram) {
                        $programIds[] = $cascadingProgram->refcascadingprogram_id;
                    }
                }
            }

            if (!empty($programIds)) {
                $programData = SakipIndikatorcascadingsubkegiatan::find()
                    ->select([
                        'refcascadingprogram_id',
                        'totalPagu' => 'SUM(anggaran_rkt)',
                        'totalRealisasiAnggaran' => 'SUM(realisasi)',
                        'totalCapaianAnggaran' => 'AVG(capaian)'
                    ])
                    ->where(['refcascadingprogram_id' => array_unique($programIds)])
                    ->groupBy('refcascadingprogram_id')
                    ->asArray()
                    ->all();
                $programDataMap = ArrayHelper::index($programData, 'refcascadingprogram_id');
            }
        }

        $selectedPeriod = SakipPeriode::findOne($refperiode_id);
        $selectedPeriodValue = $selectedPeriod ? $selectedPeriod->periode : 'N/A';

        $content = $this->renderPartial('_cetak_laporan_ekinerja', [
            'sasaranRenstra' => $sasaranRenstra,
            'programDataMap' => $programDataMap, // Data program yang sudah dihitung
            'nama_skpd' => $nama_skpd,
            'selectedPeriodValue' => $selectedPeriodValue,
        ]);

        $pdf = new Pdf([
            'mode' => Pdf::MODE_UTF8,
            'format' => Pdf::FORMAT_A4,
            'orientation' => Pdf::ORIENT_LANDSCAPE,
            'destination' => Pdf::DEST_BROWSER,
            'content' => $content,
            'cssInline' => '
            .title { text-align: center; font-family: sans-serif; }
            .tbdata { border-collapse: collapse; width: 100%; font-family: sans-serif; font-size: 9px; }
            .tbdata th, .tbdata td { border: 1px solid #5d5d5d; padding: 4px; vertical-align: middle; white-space: normal; }
            .tbdata th { text-align: center; }
            .tengah { text-align: center; }
            th.head1 { background: #2980b9; color: #fff; }
            th.head2, th.head2a { background: #16a085; color: #fff; }
            th.head3, th.head3a { background: #27ae60; color: #fff; }
        ',
            'options' => ['title' => 'Laporan E-Kinerja'],
            'methods' => [
                'SetHeader' => ["Laporan E-Kinerja - {$nama_skpd} - Periode {$selectedPeriodValue}"],
                'SetFooter' => ['{PAGENO}'],
            ]
        ]);
        return $pdf->render();
    }

    public function actionCetakLaporanEkinerjaExcel($refperiode_id = null, $refskpd_id = null)
    {
        $user = Yii::$app->user->identity;
        $assignments = Yii::$app->authManager->getAssignments($user->id);
        $allowedSkpdIds = [];
        if (isset($assignments['superadmin']) || isset($assignments['admin'])) {
            $allowedSkpdIds = ArrayHelper::map(SakipSkpd::find()->where(['skpd_isaktif' => 'T'])->all(), 'refskpd_id', 'refskpd_id');
        } else {
            $allowedSkpdIds = SakipKoordinasi::find()->select('refskpd_id')->where(['refuser_id' => $user->id])->column();
        }
        if (empty($allowedSkpdIds)) {
            $refskpd_id = $user->refskpd_id;
        } elseif ($refskpd_id === null || !in_array($refskpd_id, $allowedSkpdIds)) {
            throw new ForbiddenHttpException('Anda tidak memiliki hak akses atau SKPD tidak valid.');
        }
        $nama_skpd = SakipSkpd::find()->select('nama_skpd')->where(['refskpd_id' => $refskpd_id])->scalar();
        if ($refperiode_id === null) {
            $currentYear = date('Y');
            $defaultPeriod = SakipPeriode::find()->where(['periode' => $currentYear])->one();
            $refperiode_id = $defaultPeriod ? $defaultPeriod->refperiode_id : null;
        }
        $sasaranRenstra = [];
        $programDataMap = [];
        if ($refskpd_id && $refperiode_id) {
            $sasaranRenstra = SakipSasaranrenstra::find()->where(['refskpd_id' => $refskpd_id, 'refperiode_5tahun_id' => $this->periode5($refperiode_id)])->with(['indikatorSasaran.cascadingPrograms.refProgram'])->all();
            $programIds = [];
            foreach ($sasaranRenstra as $sasaran) {
                foreach ($sasaran->indikatorSasaran as $indikator) {
                    foreach ($indikator->cascadingPrograms as $cascadingProgram) {
                        $programIds[] = $cascadingProgram->refcascadingprogram_id;
                    }
                }
            }
            if (!empty($programIds)) {
                $programData = SakipIndikatorcascadingsubkegiatan::find()
                    ->select([
                        'refcascadingprogram_id',
                        'totalPagu' => 'SUM(anggaran_rkt)',
                        'totalRealisasiAnggaran' => 'SUM(realisasi)',
                        'totalCapaianAnggaran' => 'AVG(capaian)'
                    ])
                    ->where(['refcascadingprogram_id' => array_unique($programIds)])
                    ->groupBy('refcascadingprogram_id')
                    ->asArray()
                    ->all();
                $programDataMap = ArrayHelper::index($programData, 'refcascadingprogram_id');
            }
        }
        $selectedPeriod = SakipPeriode::findOne($refperiode_id);
        $selectedPeriodValue = $selectedPeriod ? $selectedPeriod->periode : 'N/A';

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Laporan E-Kinerja');

        // Judul
        $sheet->mergeCells('A1:L1')->setCellValue('A1', 'Tingkat Efisiensi & Efektifitas Kinerja - ' . ucwords(strtolower($nama_skpd)));
        $sheet->mergeCells('A2:L2')->setCellValue('A2', 'Periode ' . $selectedPeriodValue);

        // Header
        $sheet->mergeCells('A4:A5')->setCellValue('A4', 'No');
        $sheet->mergeCells('B4:B5')->setCellValue('B4', 'Sasaran');
        $sheet->mergeCells('C4:C5')->setCellValue('C4', 'Kode');
        $sheet->mergeCells('D4:D5')->setCellValue('D4', 'Indikator');
        $sheet->mergeCells('E4:E5')->setCellValue('E4', 'Satuan');
        $sheet->mergeCells('F4:H4')->setCellValue('F4', 'Kinerja');
        $sheet->setCellValue('F5', 'Target')->setCellValue('G5', 'Realisasi')->setCellValue('H5', '(%)');
        $sheet->mergeCells('I4:L4')->setCellValue('I4', 'Keuangan');
        $sheet->setCellValue('I5', 'Program')->setCellValue('J5', 'Pagu')->setCellValue('K5', 'Realisasi')->setCellValue('L5', '(%)');

        $row = 6;
        $no = 1;
        foreach ($sasaranRenstra as $sasaran) {
            $firstSasaranRow = $row;
            $sasaranRowSpan = 0;
            foreach ($sasaran->indikatorSasaran as $indikator) {
                $sasaranRowSpan += count($indikator->cascadingPrograms) > 0 ? count($indikator->cascadingPrograms) : 1;
            }

            $firstIndicator = true;
            $i = 0;
            foreach ($sasaran->indikatorSasaran as $indikator) {
                $firstIndicatorRow = $row;

                if (!empty($indikator->cascadingPrograms)) {
                    foreach ($indikator->cascadingPrograms as $cascadingProgram) {
                        $pData = $programDataMap[$cascadingProgram->refcascadingprogram_id] ?? null;
                        $sheet->setCellValue('I' . $row, $cascadingProgram->refProgram->nama_program ?? '-');
                        $sheet->setCellValue('J' . $row, $pData['totalPagu'] ?? 0);
                        $sheet->setCellValue('K' . $row, $pData['totalRealisasiAnggaran'] ?? 0);
                        $sheet->setCellValue('L' . $row, $pData['totalCapaianAnggaran'] ?? 0);
                        $row++;
                    }
                } else {
                    $sheet->setCellValue('I' . $row, 'Tidak ada program terkait');
                    $row++;
                }

                if ($firstIndicator) {
                    $sheet->mergeCells('C' . $firstIndicatorRow . ':C' . ($row - 1))->setCellValue('C' . $firstIndicatorRow, $no . '.1');
                } else {
                    $sheet->mergeCells('C' . $firstIndicatorRow . ':C' . ($row - 1))->setCellValue('C' . $firstIndicatorRow, $no . '.' . ($i + 1));
                }
                $sheet->mergeCells('D' . $firstIndicatorRow . ':D' . ($row - 1))->setCellValue('D' . $firstIndicatorRow, $indikator->uraian_indikatorsasaranrenstra);
                $sheet->mergeCells('E' . $firstIndicatorRow . ':E' . ($row - 1))->setCellValue('E' . $firstIndicatorRow, $indikator->indikatorsasaranrenstra_satuan);
                $sheet->mergeCells('F' . $firstIndicatorRow . ':F' . ($row - 1))->setCellValue('F' . $firstIndicatorRow, $indikator->indikatorsasaranrenstra_target);
                $sheet->mergeCells('G' . $firstIndicatorRow . ':G' . ($row - 1))->setCellValue('G' . $firstIndicatorRow, $indikator->realisasi);
                $sheet->mergeCells('H' . $firstIndicatorRow . ':H' . ($row - 1))->setCellValue('H' . $firstIndicatorRow, $indikator->capaian);
                $firstIndicator = false;
                $i++;
            }
            $sheet->mergeCells('A' . $firstSasaranRow . ':A' . ($row - 1))->setCellValue('A' . $firstSasaranRow, $no);
            $sheet->mergeCells('B' . $firstSasaranRow . ':B' . ($row - 1))->setCellValue('B' . $firstSasaranRow, $sasaran->uraian_sasaranrenstra);
            $no++;
        }

        foreach (range('A', 'L') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $fileName = "Laporan_E-Kinerja_{$nama_skpd}_{$selectedPeriodValue}.xlsx";
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . urlencode($fileName) . '"');
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit();
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
            ->where(['refskpd_id' => $refskpd_id, 'refperiode_5tahun_id' => $this->periode5($refperiode_id)])
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
            'selectedSkpdId' => $refskpd_id,
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
                ->where(['refskpd_id' => $refskpd_id, 'refperiode_5tahun_id' => $this->periode5($refperiode_id)])
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

    public function actionCetakAnalisisSasaranTriwulan($refperiode_id = null, $refskpd_id = null, $reftriwulan_id = null, $refsasaranrenstra_id = null)
    {
        // Logika keamanan
        $user = Yii::$app->user->identity;
        $assignments = Yii::$app->authManager->getAssignments($user->id);
        $allowedSkpdIds = [];
        if (isset($assignments['superadmin']) || isset($assignments['admin'])) {
            $allowedSkpdIds = ArrayHelper::map(SakipSkpd::find()->where(['skpd_isaktif' => 'T'])->all(), 'refskpd_id', 'refskpd_id');
        } else {
            $allowedSkpdIds = SakipKoordinasi::find()->select('refskpd_id')->where(['refuser_id' => $user->id])->column();
        }
        if (empty($allowedSkpdIds)) {
            $refskpd_id = $user->refskpd_id;
        } elseif ($refskpd_id === null || !in_array($refskpd_id, $allowedSkpdIds)) {
            throw new ForbiddenHttpException('Anda tidak memiliki hak akses atau SKPD tidak valid.');
        }

        // Pengambilan data
        $nama_skpd = SakipSkpd::find()->select('nama_skpd')->where(['refskpd_id' => $refskpd_id])->scalar();
        $selectedPeriod = SakipPeriode::findOne($refperiode_id);
        $selectedPeriodValue = $selectedPeriod ? $selectedPeriod->periode : 'N/A';
        $selectedSasaranRenstra = SakipSasaranrenstra::findOne($refsasaranrenstra_id);
        $selectedSasaranRenstraUraian = $selectedSasaranRenstra ? $selectedSasaranRenstra->uraian_sasaranrenstra : 'Semua Sasaran';

        // Query utama dengan optimasi eager loading
        $indikators = SakipIndikatorsasaranrenstraTriwulan::find()
            ->where([
                'refskpd_id' => $refskpd_id,
                'refperiode_id' => $refperiode_id,
                'reftriwulan_id' => $reftriwulan_id,
            ])
            ->andFilterWhere(['refsasaranrenstra_id' => $refsasaranrenstra_id])
            ->with('refIndikatorsasaranrenstra') // Eager loading
            ->all();

        $content = $this->renderPartial('_cetak_laporan_analisis_sasaran_triwulan', [
            'indikators' => $indikators,
            'nama_skpd' => $nama_skpd,
            'selectedPeriodValue' => $selectedPeriodValue,
            'selectedTriwulanId' => $reftriwulan_id,
            'selectedSasaranRenstraUraian' => $selectedSasaranRenstraUraian,
        ]);

        $pdf = new Pdf([
            'mode' => Pdf::MODE_UTF8,
            'format' => Pdf::FORMAT_A4,
            'orientation' => Pdf::ORIENT_PORTRAIT,
            'destination' => Pdf::DEST_BROWSER,
            'content' => $content,
            'cssInline' => '
            .title { text-align: center; font-family: sans-serif; }
            .tbdata { border-collapse: collapse; width: 100%; font-family: sans-serif; font-size: 10px; }
            .tbdata th, .tbdata td { border: 1px solid #ccc; padding: 5px; vertical-align: top; }
            .tbdata th { background-color: #03b0e2; color: white; text-align: center; }
        ',
            'options' => ['title' => 'Laporan Analisis Sasaran Triwulan'],
        ]);
        return $pdf->render();
    }

    public function actionCetakAnalisisSasaranTriwulanExcel($refperiode_id = null, $refskpd_id = null, $reftriwulan_id = null, $refsasaranrenstra_id = null)
    {
        // (Salin blok keamanan dan pengambilan data dari actionCetakAnalisisSasaranTriwulan di atas)
        $user = Yii::$app->user->identity;
        $assignments = Yii::$app->authManager->getAssignments($user->id);
        $allowedSkpdIds = [];
        if (isset($assignments['superadmin']) || isset($assignments['admin'])) {
            $allowedSkpdIds = ArrayHelper::map(SakipSkpd::find()->where(['skpd_isaktif' => 'T'])->all(), 'refskpd_id', 'refskpd_id');
        } else {
            $allowedSkpdIds = SakipKoordinasi::find()->select('refskpd_id')->where(['refuser_id' => $user->id])->column();
        }
        if (empty($allowedSkpdIds)) {
            $refskpd_id = $user->refskpd_id;
        } elseif ($refskpd_id === null || !in_array($refskpd_id, $allowedSkpdIds)) {
            throw new ForbiddenHttpException('Anda tidak memiliki hak akses atau SKPD tidak valid.');
        }
        $nama_skpd = SakipSkpd::find()->select('nama_skpd')->where(['refskpd_id' => $refskpd_id])->scalar();
        $selectedPeriod = SakipPeriode::findOne($refperiode_id);
        $selectedPeriodValue = $selectedPeriod ? $selectedPeriod->periode : 'N/A';
        $selectedSasaranRenstra = SakipSasaranrenstra::findOne($refsasaranrenstra_id);
        $selectedSasaranRenstraUraian = $selectedSasaranRenstra ? $selectedSasaranRenstra->uraian_sasaranrenstra : 'Semua Sasaran';
        $indikators = SakipIndikatorsasaranrenstraTriwulan::find()
            ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id, 'reftriwulan_id' => $reftriwulan_id])
            ->andFilterWhere(['refsasaranrenstra_id' => $refsasaranrenstra_id])
            ->with('refIndikatorsasaranrenstra')
            ->all();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Analisis Sasaran Triwulan');

        // Judul
        $sheet->mergeCells('A1:F1')->setCellValue('A1', 'Analisis Pencapaian Sasaran ' . ucwords(strtolower($nama_skpd)));
        $sheet->mergeCells('A2:F2')->setCellValue('A2', "Tahun {$selectedPeriodValue} - Triwulan {$reftriwulan_id}");
        $sheet->mergeCells('A3:F3')->setCellValue('A3', 'Sasaran: ' . $selectedSasaranRenstraUraian);

        // Header
        $sheet->mergeCells('A5:A6')->setCellValue('A5', 'No');
        $sheet->mergeCells('B5:B6')->setCellValue('B5', 'Indikator Kinerja Utama');
        $sheet->mergeCells('C5:C6')->setCellValue('C5', 'Satuan');
        $sheet->mergeCells('D5:E5')->setCellValue('D5', 'Triwulan ' . $reftriwulan_id);
        $sheet->setCellValue('D6', 'Target')->setCellValue('E6', 'Realisasi');
        $sheet->mergeCells('F5:F6')->setCellValue('F5', '%');

        // Isi Data
        $row = 7;
        foreach ($indikators as $index => $sasaran) {
            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $sasaran->refIndikatorsasaranrenstra->uraian_indikatorsasaranrenstra);
            $sheet->setCellValue('C' . $row, $sasaran->refIndikatorsasaranrenstra->indikatorsasaranrenstra_satuan);
            $sheet->setCellValue('D' . $row, $sasaran->triwulan_target_rkt);
            $sheet->setCellValue('E' . $row, $sasaran->triwulan_realisasi);
            $sheet->setCellValue('F' . $row, $sasaran->triwulan_capaian);
            $row++;
        }

        foreach (range('A', 'F') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $fileName = "Laporan_Analisis_Sasaran_Triwulan_{$nama_skpd}_{$selectedPeriodValue}.xlsx";
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . urlencode($fileName) . '"');
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit();
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
            ->where(['refskpd_id' => $refskpd_id, 'refperiode_5tahun_id' => $this->periode5($refperiode_id)])
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
            'selectedSkpdId' => $refskpd_id,
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
                ->where(['refskpd_id' => $refskpd_id, 'refperiode_5tahun_id' => $this->periode5($refperiode_id)])
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

    public function actionCetakAnalisisSasaranTahunan($refperiode_id = null, $refskpd_id = null, $refsasaranrenstra_id = null)
    {
        // Logika keamanan
        $user = Yii::$app->user->identity;
        $assignments = Yii::$app->authManager->getAssignments($user->id);
        $allowedSkpdIds = [];
        if (isset($assignments['superadmin']) || isset($assignments['admin'])) {
            $allowedSkpdIds = ArrayHelper::map(SakipSkpd::find()->where(['skpd_isaktif' => 'T'])->all(), 'refskpd_id', 'refskpd_id');
        } else {
            $allowedSkpdIds = SakipKoordinasi::find()->select('refskpd_id')->where(['refuser_id' => $user->id])->column();
        }
        if (empty($allowedSkpdIds)) {
            $refskpd_id = $user->refskpd_id;
        } elseif ($refskpd_id === null || !in_array($refskpd_id, $allowedSkpdIds)) {
            throw new ForbiddenHttpException('Anda tidak memiliki hak akses atau SKPD tidak valid.');
        }

        // Pengambilan data
        $nama_skpd = SakipSkpd::find()->select('nama_skpd')->where(['refskpd_id' => $refskpd_id])->scalar();
        $selectedPeriod = SakipPeriode::findOne($refperiode_id);
        $selectedPeriodValue = $selectedPeriod ? $selectedPeriod->periode : 'N/A';
        $selectedSasaranRenstra = SakipSasaranrenstra::findOne($refsasaranrenstra_id);
        $selectedSasaranRenstraUraian = $selectedSasaranRenstra ? $selectedSasaranRenstra->uraian_sasaranrenstra : 'Semua Sasaran';

        // Query utama
        $indikators = SakipIndikatorsasaranrenstra::find()
            ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
            ->andFilterWhere(['refsasaranrenstra_id' => $refsasaranrenstra_id])
            ->all();

        $content = $this->renderPartial('_cetak_laporan_analisis_sasaran_tahunan', [
            'indikators' => $indikators,
            'nama_skpd' => $nama_skpd,
            'selectedPeriodValue' => $selectedPeriodValue,
            'selectedSasaranRenstraUraian' => $selectedSasaranRenstraUraian,
        ]);

        $pdf = new Pdf([
            'mode' => Pdf::MODE_UTF8,
            'format' => Pdf::FORMAT_A4,
            'orientation' => Pdf::ORIENT_PORTRAIT,
            'destination' => Pdf::DEST_BROWSER,
            'content' => $content,
            'cssInline' => '
            .title { text-align: center; font-family: sans-serif; }
            .tbdata { border-collapse: collapse; width: 100%; font-family: sans-serif; font-size: 10px; }
            .tbdata th, .tbdata td { border: 1px solid #ccc; padding: 5px; vertical-align: top; }
            .tbdata th { background-color: #03b0e2; color: white; text-align: center; }
        ',
            'options' => ['title' => 'Laporan Analisis Sasaran Tahunan'],
        ]);
        return $pdf->render();
    }

    public function actionCetakAnalisisSasaranTahunanExcel($refperiode_id = null, $refskpd_id = null, $refsasaranrenstra_id = null)
    {
        // (Salin blok keamanan dan pengambilan data dari actionCetakAnalisisSasaranTahunan di atas)
        $user = Yii::$app->user->identity;
        $assignments = Yii::$app->authManager->getAssignments($user->id);
        $allowedSkpdIds = [];
        if (isset($assignments['superadmin']) || isset($assignments['admin'])) {
            $allowedSkpdIds = ArrayHelper::map(SakipSkpd::find()->where(['skpd_isaktif' => 'T'])->all(), 'refskpd_id', 'refskpd_id');
        } else {
            $allowedSkpdIds = SakipKoordinasi::find()->select('refskpd_id')->where(['refuser_id' => $user->id])->column();
        }
        if (empty($allowedSkpdIds)) {
            $refskpd_id = $user->refskpd_id;
        } elseif ($refskpd_id === null || !in_array($refskpd_id, $allowedSkpdIds)) {
            throw new ForbiddenHttpException('Anda tidak memiliki hak akses atau SKPD tidak valid.');
        }
        $nama_skpd = SakipSkpd::find()->select('nama_skpd')->where(['refskpd_id' => $refskpd_id])->scalar();
        $selectedPeriod = SakipPeriode::findOne($refperiode_id);
        $selectedPeriodValue = $selectedPeriod ? $selectedPeriod->periode : 'N/A';
        $selectedSasaranRenstra = SakipSasaranrenstra::findOne($refsasaranrenstra_id);
        $selectedSasaranRenstraUraian = $selectedSasaranRenstra ? $selectedSasaranRenstra->uraian_sasaranrenstra : 'Semua Sasaran';
        $indikators = SakipIndikatorsasaranrenstra::find()
            ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
            ->andFilterWhere(['refsasaranrenstra_id' => $refsasaranrenstra_id])
            ->all();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Analisis Sasaran Tahunan');

        // Judul
        $sheet->mergeCells('A1:F1')->setCellValue('A1', 'Analisis Pencapaian Sasaran ' . ucwords(strtolower($nama_skpd)));
        $sheet->mergeCells('A2:F2')->setCellValue('A2', "Tahun {$selectedPeriodValue}");
        $sheet->mergeCells('A3:F3')->setCellValue('A3', 'Sasaran: ' . $selectedSasaranRenstraUraian);

        // Header
        $sheet->mergeCells('A5:A6')->setCellValue('A5', 'No');
        $sheet->mergeCells('B5:B6')->setCellValue('B5', 'Indikator Kinerja Utama');
        $sheet->mergeCells('C5:C6')->setCellValue('C5', 'Satuan');
        $sheet->mergeCells('D5:E5')->setCellValue('D5', 'Tahun ' . $selectedPeriodValue);
        $sheet->setCellValue('D6', 'Target')->setCellValue('E6', 'Realisasi');
        $sheet->mergeCells('F5:F6')->setCellValue('F5', '%');

        // Isi Data
        $row = 7;
        foreach ($indikators as $index => $sasaran) {
            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $sasaran->uraian_indikatorsasaranrenstra);
            $sheet->setCellValue('C' . $row, $sasaran->indikatorsasaranrenstra_satuan);
            $sheet->setCellValue('D' . $row, $sasaran->target_rkt);
            $sheet->setCellValue('E' . $row, $sasaran->realisasi);
            $sheet->setCellValue('F' . $row, $sasaran->capaian);
            $row++;
        }

        foreach (range('A', 'F') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $fileName = "Laporan_Analisis_Sasaran_Tahunan_{$nama_skpd}_{$selectedPeriodValue}.xlsx";
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . urlencode($fileName) . '"');
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit();
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
