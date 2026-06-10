<?php

namespace frontend\controllers;

use frontend\models\SakipCascadingkegiatan;
use frontend\models\SakipCascadingprogram;
use frontend\models\SakipCascadingsubkegiatan;
use frontend\models\SakipIndikatorsasaranrenstra;
use frontend\models\SakipIndikatorsasaranrenstraTriwulan;
use frontend\models\SakipKegiatan;
use frontend\models\SakipPeriode;
use frontend\models\SakipProgram;
use frontend\models\SakipSasaranrenstra;
use frontend\models\SakipSkpd;
use frontend\models\SakipSubkegiatan;
use Yii;
use yii\rest\Controller;
use yii\web\Response;
use frontend\models\User;

class EsakipApiController extends Controller
{

    public function beforeAction($action)
    {
        $actionsToDisableCsrf = [
            'list-capkin-renstra-skpd-paginated',
            'list-capkin-program-skpd-paginated',
            'list-capkin-kegiatan-skpd-paginated',
            'list-capkin-subkegiatan-skpd-paginated'  // Action baru (kebab-case)
        ];
        if (in_array($action->id, $actionsToDisableCsrf)) {
            $this->enableCsrfValidation = false;
        }
        return parent::beforeAction($action);
    }
    // public function actionUser()
    // {
    //     Yii::$app->response->format = Response::FORMAT_JSON;

    //     $startTime = microtime(true);

    //     $users = User::find()->asArray()->all();

    //     $responseTime = round((microtime(true) - $startTime) * 1000, 2); // dalam ms

    //     return [
    //         'success' => true,
    //         'response_time_ms' => $responseTime,
    //         'data' => $users
    //     ];
    // }

    public function actionSkpd()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $startTime = microtime(true);

        $skpds = SakipSkpd::find()->asArray()->all();

        $responseTime = round((microtime(true) - $startTime) * 1000, 2); // dalam ms

        return [
            'success' => true,
            'response_time_ms' => $responseTime,
            'data' => $skpds
        ];
    }

    public function actionPeriode()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $startTime = microtime(true);

        $periods = SakipPeriode::find()->asArray()->all();

        $responseTime = round((microtime(true) - $startTime) * 1000, 2); // dalam ms

        return [
            'success' => true,
            'response_time_ms' => $responseTime,
            'data' => $periods
        ];
    }

    public function actionProgram()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $startTime = microtime(true);

        $page = Yii::$app->request->get('page', 1);
        $limit = Yii::$app->request->get('limit', 10);
        $offset = ($page - 1) * $limit;

        $query = SakipProgram::find();
        $total = $query->count();
        $programs = $query->offset($offset)->limit($limit)->asArray()->all();

        $responseTime = round((microtime(true) - $startTime) * 1000, 2); // dalam ms

        return [
            'success' => true,
            'page' => (int)$page,
            'limit' => (int)$limit,
            'total' => (int)$total,
            'response_time_ms' => $responseTime,
            'data' => $programs
        ];
    }

    public function actionKegiatan()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $startTime = microtime(true);

        $page = Yii::$app->request->get('page', 1);
        $limit = Yii::$app->request->get('limit', 10);
        $offset = ($page - 1) * $limit;

        $query = SakipKegiatan::find();
        $total = $query->count();
        $kegiatans = $query->offset($offset)->limit($limit)->asArray()->all();

        $responseTime = round((microtime(true) - $startTime) * 1000, 2); // dalam ms

        return [
            'success' => true,
            'page' => (int)$page,
            'limit' => (int)$limit,
            'total' => (int)$total,
            'response_time_ms' => $responseTime,
            'data' => $kegiatans
        ];
    }

    public function actionSubkegiatan()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $startTime = microtime(true);

        $page = Yii::$app->request->get('page', 1);
        $limit = Yii::$app->request->get('limit', 10);
        $offset = ($page - 1) * $limit;

        $query = SakipSubkegiatan::find();
        $total = $query->count();
        $subkegiatans = $query->offset($offset)->limit($limit)->asArray()->all();

        $responseTime = round((microtime(true) - $startTime) * 1000, 2); // dalam ms

        return [
            'success' => true,
            'page' => (int)$page,
            'limit' => (int)$limit,
            'total' => (int)$total,
            'response_time_ms' => $responseTime,
            'data' => $subkegiatans
        ];
    }

    public function actionCapkinIndikatorRenstra($refperiode_id = null, $refskpd_id = null)

    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $startTime = microtime(true);

        if ($refperiode_id === null) {
            $currentYear = date('Y');
            $defaultPeriod = SakipPeriode::find()->where(['periode' => $currentYear])->one();
            $refperiode_id = $defaultPeriod ? $defaultPeriod->refperiode_id : null;
        }

        $skpdQuery = SakipSkpd::find();

        if ($refskpd_id !== null) {
            $skpdQuery->where(['refskpd_id' => $refskpd_id]);
        }

        $skpdList = $skpdQuery->asArray()->all();

        $dataRenstra = [];

        foreach ($skpdList as $skpd) {
            $refskpd_id = $skpd['refskpd_id'];
            if ($refskpd_id == 1) continue;

            $sasaranList = SakipSasaranrenstra::find()
                ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
                ->asArray()
                ->all();

            $sasaranData = [];

            foreach ($sasaranList as $sasaran) {
                $indikatorList = SakipIndikatorsasaranrenstra::find()
                    ->where([
                        'refsasaranrenstra_id' => $sasaran['refsasaranrenstra_id'],
                        'refperiode_id' => $refperiode_id,
                    ])
                    ->asArray()
                    ->all();

                $indikatorData = [];

                foreach ($indikatorList as $indikator) {
                    $triwulanData = SakipIndikatorsasaranrenstraTriwulan::find()
                        ->where([
                            'refindikatorsasaranrenstra_id' => $indikator['refindikatorsasaranrenstra_id'],
                            'refsasaranrenstra_id' => $sasaran['refsasaranrenstra_id'],
                            'refskpd_id' => $refskpd_id,
                            'refperiode_id' => $refperiode_id,
                        ])
                        ->orderBy(['reftriwulan_id' => SORT_ASC])
                        ->asArray()
                        ->all();

                    $indikatorData[] = [
                        'uraian_indikator' => $indikator['uraian_indikatorsasaranrenstra'],
                        'triwulan' => $triwulanData,
                    ];
                }

                $sasaranData[] = [
                    'uraian_sasaran' => $sasaran['uraian_sasaranrenstra'],
                    'indikator' => $indikatorData,
                ];
            }

            $dataRenstra[] = [
                'refskpd_id' => $refskpd_id,
                'nama_skpd' => $skpd['nama_skpd'],
                'sasaran' => $sasaranData,
            ];
        }

        $responseTime = round((microtime(true) - $startTime) * 1000, 2); // dalam ms

        return [
            'success' => true,
            'refperiode_id' => (int)$refperiode_id,
            'refskpd_id' => $refskpd_id !== null ? (int)$refskpd_id : null,
            'response_time_ms' => $responseTime,
            'data' => $dataRenstra,
        ];
    }

    // NAMA ACTION BARU
    public function actionListCapkinRenstraSkpdPaginated($refperiode_id = null, $filter_refskpd_id = null, $page = 1)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $startTime = microtime(true);

        // 1. Handle refperiode_id (default ke periode tahun ini jika null)
        if ($refperiode_id === null) {
            $currentYear = date('Y');
            $defaultPeriod = SakipPeriode::find()->where(['periode' => $currentYear])->one();
            if ($defaultPeriod) {
                $refperiode_id = $defaultPeriod->refperiode_id;
            }
        } else {
            $refperiode_id = (int) $refperiode_id;
        }

        // Jika refperiode_id masih null setelah mencoba default, kembalikan error
        if ($refperiode_id === null) {
            Yii::$app->response->statusCode = 400; // Bad Request
            return [
                'success' => false,
                'message' => 'refperiode_id tidak valid atau tidak ditemukan periode default untuk tahun ini.',
                'refperiode_id_aktif' => null,
                'filter_refskpd_id_applied' => $filter_refskpd_id !== null ? (int)$filter_refskpd_id : null,
                'pagination_skpd' => null,
                'response_time_ms' => round((microtime(true) - $startTime) * 1000, 2),
                'data' => [],
            ];
        }

        $dataRenstra = [];
        $paginationInfo = null;
        $limit_skpd_per_page = 5; // Paginasi per 5 SKPD

        // Query dasar untuk SKPD, mengecualikan SKPD ID 1
        $skpdQuery = SakipSkpd::find()->where(['!=', 'refskpd_id', 1]);

        if ($filter_refskpd_id !== null) {
            $filter_refskpd_id = (int) $filter_refskpd_id;
            $skpdQuery->andWhere(['refskpd_id' => $filter_refskpd_id]);
            $skpdList = $skpdQuery->orderBy(['refskpd_id' => SORT_ASC])->asArray()->all();

            $totalSkpdCount = count($skpdList);
            $paginationInfo = [
                'currentPage' => $totalSkpdCount > 0 ? 1 : 0,
                'skpdPerHalaman' => $totalSkpdCount,
                'totalSkpdKeseluruhan' => $totalSkpdCount,
                'totalSkpdDiHalamanIni' => $totalSkpdCount,
                'totalPages' => $totalSkpdCount > 0 ? 1 : 0,
                'note' => 'Data difilter berdasarkan refskpd_id tertentu.',
            ];
        } else {
            $page = (int)$page;
            if ($page < 1) {
                $page = 1;
            }

            $countQuery = clone $skpdQuery;
            $totalSkpdCount = (int)$countQuery->count();
            $totalPages = (int)ceil($totalSkpdCount / $limit_skpd_per_page);

            if ($page > $totalPages && $totalPages > 0) {
                $page = $totalPages;
            }

            $offset = ($page - 1) * $limit_skpd_per_page;
            $skpdList = $skpdQuery->offset($offset)
                ->limit($limit_skpd_per_page)
                ->orderBy(['refskpd_id' => SORT_ASC])
                ->asArray()
                ->all();

            $paginationInfo = [
                'currentPage' => $page,
                'skpdPerHalaman' => $limit_skpd_per_page,
                'totalSkpdKeseluruhan' => $totalSkpdCount,
                'totalSkpdDiHalamanIni' => count($skpdList),
                'totalPages' => $totalPages,
            ];
        }

        foreach ($skpdList as $skpd) {
            $current_refskpd_id = $skpd['refskpd_id'];

            $sasaranList = SakipSasaranrenstra::find()
                ->where(['refskpd_id' => $current_refskpd_id, 'refperiode_id' => $refperiode_id])
                ->asArray()
                ->all();

            $sasaranData = [];
            foreach ($sasaranList as $sasaran) {
                $indikatorList = SakipIndikatorsasaranrenstra::find()
                    ->where([
                        'refsasaranrenstra_id' => $sasaran['refsasaranrenstra_id'],
                        'refperiode_id' => $refperiode_id,
                    ])
                    ->asArray()
                    ->all();

                $indikatorData = [];
                foreach ($indikatorList as $indikator) {
                    $triwulanData = SakipIndikatorsasaranrenstraTriwulan::find()
                        ->where([
                            'refindikatorsasaranrenstra_id' => $indikator['refindikatorsasaranrenstra_id'],
                            'refsasaranrenstra_id' => $sasaran['refsasaranrenstra_id'],
                            'refskpd_id' => $current_refskpd_id,
                            'refperiode_id' => $refperiode_id,
                        ])
                        ->orderBy(['reftriwulan_id' => SORT_ASC])
                        ->asArray()
                        ->all();

                    $indikatorData[] = [
                        'refindikatorsasaranrenstra_id' => $indikator['refindikatorsasaranrenstra_id'] ?? null,
                        'uraian_indikatorsasaranrenstra' => $indikator['uraian_indikatorsasaranrenstra'] ?? null,
                        'indikatorsasaranrenstra_satuan' => $indikator['indikatorsasaranrenstra_satuan'] ?? null,
                        'indikatorsasaranrenstra_target' => $indikator['indikatorsasaranrenstra_target'] ?? null,
                        'target_rkt' => $indikator['target_rkt'] ?? null,
                        'target_rkt_p' => $indikator['target_rkt_p'] ?? null,
                        'target_pk' => $indikator['target_pk'] ?? null,
                        'target_pk_p' => $indikator['target_pk_p'] ?? null,
                        'realisasi' => $indikator['realisasi'] ?? null,
                        'capaian' => $indikator['capaian'] ?? null,
                        'analisis' => $indikator['analisis'] ?? null,
                        'keterangan' => $indikator['keterangan'] ?? null,
                        'triwulan' => $triwulanData,
                    ];
                }

                $sasaranData[] = [
                    'refsasaranrenstra_id' => $sasaran['refsasaranrenstra_id'] ?? null,
                    'uraian_sasaranrenstra' => $sasaran['uraian_sasaranrenstra'] ?? null,
                    'alasan_sasaranrenstra' => $sasaran['alasan_sasaranrenstra'] ?? null,
                    'kriteria_sasaranrenstra' => $sasaran['kriteria_sasaranrenstra'] ?? null,
                    'formulasi_sasaranrenstra' => $sasaran['formulasi_sasaranrenstra'] ?? null,
                    'indikator' => $indikatorData,
                ];
            }

            $dataRenstra[] = [
                'refskpd_id' => $current_refskpd_id,
                'nama_skpd' => $skpd['nama_skpd'] ?? null,
                'sasaran' => $sasaranData,
            ];
        }

        $responseTime = round((microtime(true) - $startTime) * 1000, 2);

        return [
            'success' => true,
            'refperiode_id_aktif' => (int)$refperiode_id,
            'filter_refskpd_id_applied' => $filter_refskpd_id !== null ? (int)$filter_refskpd_id : null,
            'pagination_skpd' => $paginationInfo,
            'response_time_ms' => $responseTime,
            'data' => $dataRenstra,
        ];
    }

    public function actionCapkinIndikatorProgram($refperiode_id = null, $refskpd_id = null)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $startTime = microtime(true);

        if ($refperiode_id === null) {
            $currentYear = date('Y');
            $defaultPeriod = SakipPeriode::find()->where(['periode' => $currentYear])->one();
            $refperiode_id = $defaultPeriod ? $defaultPeriod->refperiode_id : null;
        }

        $skpdQuery = SakipSkpd::find();
        if ($refskpd_id !== null) {
            $skpdQuery->where(['refskpd_id' => $refskpd_id]);
        }

        $skpdList = $skpdQuery->all();
        $dataProgram = [];

        foreach ($skpdList as $skpd) {
            $refskpd_id = $skpd->refskpd_id;
            if ($refskpd_id == 1) continue;

            $programList = SakipCascadingprogram::find()
                ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
                ->with([
                    'indikatorCascadingPrograms.refIndikatorCascadingProgramTriwulan' => function ($query) use ($refskpd_id, $refperiode_id) {
                        $query->andWhere(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
                            ->orderBy(['reftriwulan_id' => SORT_ASC]);
                    }
                ])
                ->all();

            $programData = [];

            foreach ($programList as $program) {
                $indikatorData = [];

                foreach ($program->indikatorCascadingPrograms as $indikator) {
                    $triwulanData = $indikator->refIndikatorCascadingProgramTriwulan;

                    $indikatorData[] = [
                        'uraian_indikator' => $program->uraian_indikatorprogram ?? $indikator->uraian_indikatorprogram,
                        'program_satuan' => $program->program_satuan,
                        'triwulan' => $triwulanData,
                    ];
                }

                $programData[] = [
                    'uraian_program' => $program->uraian_sasaranprogram,
                    'indikator' => $indikatorData,
                ];
            }

            $dataProgram[] = [
                'refskpd_id' => $refskpd_id,
                'nama_skpd' => $skpd->nama_skpd,
                'program' => $programData,
            ];
        }

        $endTime = microtime(true);
        $responseTime = round(($endTime - $startTime) * 1000, 2); // ms

        return [
            'success' => true,
            'refperiode_id' => (int)$refperiode_id,
            'refskpd_id' => $refskpd_id !== null ? (int)$refskpd_id : null,
            'response_time_ms' => $responseTime,
            'data' => $dataProgram,
        ];
    }

    public function actionListCapkinProgramSkpdPaginated($refperiode_id = null, $filter_refskpd_id = null, $page = 1)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $startTime = microtime(true);

        // 1. Handle refperiode_id
        if ($refperiode_id === null) {
            $currentYear = date('Y');
            $defaultPeriod = SakipPeriode::find()->where(['periode' => $currentYear])->one();
            if ($defaultPeriod) {
                $refperiode_id = $defaultPeriod->refperiode_id;
            }
        } else {
            $refperiode_id = (int) $refperiode_id;
        }

        if ($refperiode_id === null) {
            Yii::$app->response->statusCode = 400;
            return [
                'success' => false,
                'message' => 'refperiode_id tidak valid atau tidak ditemukan periode default untuk tahun ini.',
                'refperiode_id_aktif' => null,
                'filter_refskpd_id_applied' => $filter_refskpd_id !== null ? (int)$filter_refskpd_id : null,
                'pagination_skpd' => null,
                'response_time_ms' => round((microtime(true) - $startTime) * 1000, 2),
                'data' => [],
            ];
        }

        $dataOutput = []; // Ganti nama variabel agar tidak bentrok dengan $dataProgram di dalam loop
        $paginationInfo = null;
        $limit_skpd_per_page = 5;

        $skpdQuery = SakipSkpd::find()->where(['!=', 'refskpd_id', 1]); // Asumsi SKPD ID 1 selalu dilewati

        if ($filter_refskpd_id !== null) {
            $filter_refskpd_id = (int) $filter_refskpd_id;
            $skpdQuery->andWhere(['refskpd_id' => $filter_refskpd_id]);
            // Menggunakan ->all() untuk mendapatkan objek, bukan array, agar relasi eager loading berfungsi
            $skpdList = $skpdQuery->orderBy(['refskpd_id' => SORT_ASC])->all();

            $totalSkpdCount = count($skpdList);
            $paginationInfo = [
                'currentPage' => $totalSkpdCount > 0 ? 1 : 0,
                'skpdPerHalaman' => $totalSkpdCount,
                'totalSkpdKeseluruhan' => $totalSkpdCount,
                'totalSkpdDiHalamanIni' => $totalSkpdCount,
                'totalPages' => $totalSkpdCount > 0 ? 1 : 0,
                'note' => 'Data difilter berdasarkan refskpd_id tertentu.',
            ];
        } else {
            $page = (int)$page;
            if ($page < 1) {
                $page = 1;
            }

            $countQuery = clone $skpdQuery;
            $totalSkpdCount = (int)$countQuery->count();
            $totalPages = (int)ceil($totalSkpdCount / $limit_skpd_per_page);

            if ($page > $totalPages && $totalPages > 0) {
                $page = $totalPages;
            }

            $offset = ($page - 1) * $limit_skpd_per_page;
            // Menggunakan ->all() untuk mendapatkan objek
            $skpdList = $skpdQuery->offset($offset)
                ->limit($limit_skpd_per_page)
                ->orderBy(['refskpd_id' => SORT_ASC])
                ->all();

            $paginationInfo = [
                'currentPage' => $page,
                'skpdPerHalaman' => $limit_skpd_per_page,
                'totalSkpdKeseluruhan' => $totalSkpdCount,
                'totalSkpdDiHalamanIni' => count($skpdList),
                'totalPages' => $totalPages,
            ];
        }

        foreach ($skpdList as $skpd) { // $skpd adalah objek SakipSkpd
            // $current_refskpd_id = $skpd->refskpd_id; // sudah dihandle oleh where clause di $skpdQuery

            $programList = SakipCascadingprogram::find()
                ->where(['refskpd_id' => $skpd->refskpd_id, 'refperiode_id' => $refperiode_id])
                ->with([
                    // Pastikan nama relasi ini ('indikatorCascadingPrograms' dan 'refIndikatorCascadingProgramTriwulan') sudah benar
                    // sesuai definisi di model SakipCascadingprogram dan model Indikatornya.
                    'indikatorCascadingPrograms.refIndikatorCascadingProgramTriwulan' => function ($query) use ($skpd, $refperiode_id) {
                        $query->andWhere(['refskpd_id' => $skpd->refskpd_id, 'refperiode_id' => $refperiode_id])
                            ->orderBy(['reftriwulan_id' => SORT_ASC]);
                    }
                ])
                ->all();

            $programDataFormatted = [];
            foreach ($programList as $program) { // $program adalah objek SakipCascadingprogram
                $indikatorDataFormatted = [];
                if ($program->indikatorCascadingPrograms) { // Cek jika relasi ada dan tidak null
                    foreach ($program->indikatorCascadingPrograms as $indikator) { // $indikator adalah objek IndikatorCascadingProgram
                        $triwulanDataFormatted = [];
                        if ($indikator->refIndikatorCascadingProgramTriwulan) { // Cek jika relasi ada
                            foreach ($indikator->refIndikatorCascadingProgramTriwulan as $triwulan) { // $triwulan adalah objek IndikatorCascadingProgramTriwulan
                                // Ambil field sesuai definisi Swagger & kebutuhan
                                $triwulanDataFormatted[] = [
                                    'reftriwulan_id' => $triwulan->reftriwulan_id ?? null,
                                    'triwulan_target_rkt' => $triwulan->triwulan_target_rkt ?? null,
                                    'triwulan_target_rkt_p' => $triwulan->triwulan_target_rkt_p ?? null,
                                    'triwulan_target_pk' => $triwulan->triwulan_target_pk ?? null,
                                    'triwulan_target_pk_p' => $triwulan->triwulan_target_pk_p ?? null,
                                    'triwulan_realisasi' => $triwulan->triwulan_realisasi ?? null,
                                    'triwulan_capaian' => $triwulan->triwulan_capaian ?? null,
                                    'triwulan_keterangan' => $triwulan->triwulan_keterangan ?? null,
                                    'triwulan_analisis' => $triwulan->triwulan_analisis ?? null,
                                ];
                            }
                        }

                        // Sesuaikan field indikator. Swagger untuk program lebih simpel.
                        // 'uraian_indikator' dan 'program_satuan' dari Swagger.
                        $indikatorDataFormatted[] = [
                            // Asumsi $indikator memiliki ID, misal 'refindikatorcascadingprogram_id'
                            'refindikatorcascadingprogram_id' => $indikator->refindikatorcascadingprogram_id ?? ($indikator->id ?? null),
                            'uraian_indikator' => $indikator->uraian_indikatorprogram ?? null, // Dari objek indikator
                            'program_satuan' => $indikator->satuan ?? ($program->program_satuan ?? null), // Prioritaskan dari indikator, fallback ke program
                            'triwulan' => $triwulanDataFormatted,
                        ];
                    }
                }

                $programDataFormatted[] = [
                    // Asumsi $program memiliki ID, misal 'refprogram_id' atau 'id'
                    'refprogram_id' => $program->refprogram_id ?? ($program->id ?? null),
                    'uraian_program' => $program->uraian_sasaranprogram ?? ($program->uraian_program ?? null), // Sesuaikan dengan field yang benar
                    'indikator' => $indikatorDataFormatted,
                ];
            }

            $dataOutput[] = [
                'refskpd_id' => $skpd->refskpd_id,
                'nama_skpd' => $skpd->nama_skpd,
                'program' => $programDataFormatted,
            ];
        }

        $responseTime = round((microtime(true) - $startTime) * 1000, 2);

        return [
            'success' => true,
            'refperiode_id_aktif' => (int)$refperiode_id,
            'filter_refskpd_id_applied' => $filter_refskpd_id !== null ? (int)$filter_refskpd_id : null,
            'pagination_skpd' => $paginationInfo,
            'response_time_ms' => $responseTime,
            'data' => $dataOutput,
        ];
    }

    public function actionCapkinIndikatorKegiatan($refperiode_id = null, $refskpd_id = null)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $startTime = microtime(true);

        // Jika periode tidak diset, pakai tahun berjalan
        if ($refperiode_id === null) {
            $currentYear = date('Y');
            $defaultPeriod = SakipPeriode::find()->where(['periode' => $currentYear])->one();
            $refperiode_id = $defaultPeriod ? $defaultPeriod->refperiode_id : null;
        }

        // Query SKPD, jika $refskpd_id diberikan maka filter sesuai
        $skpdQuery = SakipSkpd::find();
        if ($refskpd_id !== null) {
            $skpdQuery->where(['refskpd_id' => $refskpd_id]);
        }
        $skpdList = $skpdQuery->all();

        $dataKegiatan = [];

        foreach ($skpdList as $skpd) {
            $refskpd_id = $skpd->refskpd_id;
            if ($refskpd_id == 1) continue; // melewati SKPD ID 1 jika diperlukan

            // Ambil daftar kegiatan pada SKPD dan periode tersebut
            $kegiatanList = SakipCascadingkegiatan::find()
                ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
                ->with([
                    'indikatorCascadingKegiatan.refIndikatorCascadingKegiatanTriwulan' => function ($query) use ($refskpd_id, $refperiode_id) {
                        $query->andWhere([
                            'refskpd_id' => $refskpd_id,
                            'refperiode_id' => $refperiode_id,
                        ])->orderBy(['reftriwulan_id' => SORT_ASC]);
                    }
                ])
                ->all();

            $kegiatanData = [];

            foreach ($kegiatanList as $kegiatan) {
                $indikatorData = [];

                foreach ($kegiatan->indikatorCascadingKegiatan as $indikator) {
                    $triwulanData = $indikator->refIndikatorCascadingKegiatanTriwulan;

                    $indikatorData[] = [
                        'uraian_indikator' => $kegiatan->uraian_indikatorkegiatan ?? $indikator->uraian_indikatorkegiatan,
                        'kegiatan_satuan' => $kegiatan->kegiatan_satuan,
                        'triwulan' => $triwulanData,
                    ];
                }

                $kegiatanData[] = [
                    'uraian_kegiatan' => $kegiatan->uraian_sasarankegiatan,
                    'indikator' => $indikatorData,
                ];
            }

            $dataKegiatan[] = [
                'refskpd_id' => $refskpd_id,
                'nama_skpd' => $skpd->nama_skpd,
                'kegiatan' => $kegiatanData,
            ];
        }

        $endTime = microtime(true);
        $responseTime = round(($endTime - $startTime) * 1000, 2); // milidetik

        return [
            'success' => true,
            'refperiode_id' => (int)$refperiode_id,
            'refskpd_id' => $refskpd_id !== null ? (int)$refskpd_id : null,
            'response_time_ms' => $responseTime,
            'data' => $dataKegiatan,
        ];
    }

    public function actionListCapkinKegiatanSkpdPaginated($refperiode_id = null, $filter_refskpd_id = null, $page = 1)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $startTime = microtime(true);

        // 1. Handle refperiode_id
        if ($refperiode_id === null) {
            $currentYear = date('Y');
            $defaultPeriod = SakipPeriode::find()->where(['periode' => $currentYear])->one();
            if ($defaultPeriod) {
                $refperiode_id = $defaultPeriod->refperiode_id;
            }
        } else {
            $refperiode_id = (int) $refperiode_id;
        }

        if ($refperiode_id === null) {
            Yii::$app->response->statusCode = 400;
            return [
                'success' => false,
                'message' => 'refperiode_id tidak valid atau tidak ditemukan periode default untuk tahun ini.',
                'refperiode_id_aktif' => null,
                'filter_refskpd_id_applied' => $filter_refskpd_id !== null ? (int)$filter_refskpd_id : null,
                'pagination_skpd' => null,
                'response_time_ms' => round((microtime(true) - $startTime) * 1000, 2),
                'data' => [],
            ];
        }

        $dataOutput = [];
        $paginationInfo = null;
        $limit_skpd_per_page = 5;

        $skpdQuery = SakipSkpd::find()->where(['!=', 'refskpd_id', 1]); // Asumsi SKPD ID 1 selalu dilewati

        if ($filter_refskpd_id !== null) {
            $filter_refskpd_id = (int) $filter_refskpd_id;
            $skpdQuery->andWhere(['refskpd_id' => $filter_refskpd_id]);
            $skpdList = $skpdQuery->orderBy(['refskpd_id' => SORT_ASC])->all();

            $totalSkpdCount = count($skpdList);
            $paginationInfo = [
                'currentPage' => $totalSkpdCount > 0 ? 1 : 0,
                'skpdPerHalaman' => $totalSkpdCount,
                'totalSkpdKeseluruhan' => $totalSkpdCount,
                'totalSkpdDiHalamanIni' => $totalSkpdCount,
                'totalPages' => $totalSkpdCount > 0 ? 1 : 0,
                'note' => 'Data difilter berdasarkan refskpd_id tertentu.',
            ];
        } else {
            $page = (int)$page;
            if ($page < 1) {
                $page = 1;
            }

            $countQuery = clone $skpdQuery;
            $totalSkpdCount = (int)$countQuery->count();
            $totalPages = (int)ceil($totalSkpdCount / $limit_skpd_per_page);

            if ($page > $totalPages && $totalPages > 0) {
                $page = $totalPages;
            }

            $offset = ($page - 1) * $limit_skpd_per_page;
            $skpdList = $skpdQuery->offset($offset)
                ->limit($limit_skpd_per_page)
                ->orderBy(['refskpd_id' => SORT_ASC])
                ->all();

            $paginationInfo = [
                'currentPage' => $page,
                'skpdPerHalaman' => $limit_skpd_per_page,
                'totalSkpdKeseluruhan' => $totalSkpdCount,
                'totalSkpdDiHalamanIni' => count($skpdList),
                'totalPages' => $totalPages,
            ];
        }

        foreach ($skpdList as $skpd) { // $skpd adalah objek SakipSkpd
            $kegiatanList = SakipCascadingkegiatan::find()
                ->where(['refskpd_id' => $skpd->refskpd_id, 'refperiode_id' => $refperiode_id])
                ->with([
                    // Pastikan nama relasi ini ('indikatorCascadingKegiatan' dan 'refIndikatorCascadingKegiatanTriwulan') sudah benar
                    // sesuai definisi di model SakipCascadingkegiatan dan model Indikatornya.
                    'indikatorCascadingKegiatan.refIndikatorCascadingKegiatanTriwulan' => function ($query) use ($skpd, $refperiode_id) {
                        $query->andWhere([
                            'refskpd_id' => $skpd->refskpd_id, // Kondisi tambahan jika diperlukan, atau bisa di-skip jika FK sudah cukup
                            'refperiode_id' => $refperiode_id, // Kondisi tambahan jika diperlukan
                        ])->orderBy(['reftriwulan_id' => SORT_ASC]);
                    }
                ])
                ->all();

            $kegiatanDataFormatted = [];
            foreach ($kegiatanList as $kegiatan) { // $kegiatan adalah objek SakipCascadingkegiatan
                $indikatorDataFormatted = [];
                if ($kegiatan->indikatorCascadingKegiatan) {
                    foreach ($kegiatan->indikatorCascadingKegiatan as $indikator) { // $indikator adalah objek IndikatorCascadingKegiatan
                        $triwulanDataFormatted = [];
                        if ($indikator->refIndikatorCascadingKegiatanTriwulan) {
                            foreach ($indikator->refIndikatorCascadingKegiatanTriwulan as $triwulan) {
                                $triwulanDataFormatted[] = [
                                    'refindikatorkegiatantriwulan_id' => $triwulan->refindikatorkegiatantriwulan_id ?? ($triwulan->id ?? null), // Sesuai Swagger
                                    'reftriwulan_id' => $triwulan->reftriwulan_id ?? null, // Jika ada field ini
                                    'triwulan_target_rkt' => $triwulan->triwulan_target_rkt ?? null,
                                    'triwulan_target_rkt_p' => $triwulan->triwulan_target_rkt_p ?? null,
                                    'triwulan_target_pk' => $triwulan->triwulan_target_pk ?? null,
                                    'triwulan_target_pk_p' => $triwulan->triwulan_target_pk_p ?? null,
                                    'triwulan_realisasi' => $triwulan->triwulan_realisasi ?? null,
                                    'triwulan_capaian' => $triwulan->triwulan_capaian ?? null,
                                    'triwulan_keterangan' => $triwulan->triwulan_keterangan ?? null,
                                    'triwulan_analisis' => $triwulan->triwulan_analisis ?? null,
                                ];
                            }
                        }

                        // Mengisi field indikator sesuai Swagger untuk Kegiatan
                        $indikatorDataFormatted[] = [
                            'refindikatorkegiatan_id' => $indikator->refindikatorkegiatan_id ?? ($indikator->id ?? null), // Asumsi ID indikator
                            'uraian_indikator' => $indikator->uraian_indikatorkegiatan ?? null,
                            'target_rkt' => $indikator->target_rkt ?? null,
                            'target_rkt_p' => $indikator->target_rkt_p ?? null,
                            'target_pk' => $indikator->target_pk ?? null,
                            'target_pk_p' => $indikator->target_pk_p ?? null,
                            'realisasi' => $indikator->realisasi ?? null,
                            'capaian' => $indikator->capaian ?? null,
                            'keterangan' => $indikator->keterangan ?? null,
                            'analisis' => $indikator->analisis ?? null,
                            'triwulan' => $triwulanDataFormatted,
                        ];
                    }
                }

                $kegiatanDataFormatted[] = [
                    'refkegiatan_id' => $kegiatan->refkegiatan_id ?? ($kegiatan->id ?? null), // Asumsi ID kegiatan
                    'uraian_kegiatan' => $kegiatan->uraian_sasarankegiatan ?? ($kegiatan->uraian_kegiatan ?? null), // Sesuaikan field
                    'kegiatan_satuan' => $kegiatan->kegiatan_satuan ?? null, // Satuan di level kegiatan, sesuai Swagger
                    'indikator' => $indikatorDataFormatted,
                ];
            }

            $dataOutput[] = [
                'refskpd_id' => $skpd->refskpd_id,
                'nama_skpd' => $skpd->nama_skpd,
                'kegiatan' => $kegiatanDataFormatted,
            ];
        }

        $responseTime = round((microtime(true) - $startTime) * 1000, 2);

        return [
            'success' => true,
            'refperiode_id_aktif' => (int)$refperiode_id,
            'filter_refskpd_id_applied' => $filter_refskpd_id !== null ? (int)$filter_refskpd_id : null,
            'pagination_skpd' => $paginationInfo,
            'response_time_ms' => $responseTime,
            'data' => $dataOutput,
        ];
    }

    public function actionCapkinIndikatorSubkegiatan($refperiode_id = null, $refskpd_id = null)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $startTime = microtime(true);

        // Ambil refperiode_id default jika null
        if ($refperiode_id === null) {
            $currentYear = date('Y');
            $defaultPeriod = SakipPeriode::find()->where(['periode' => $currentYear])->one();
            $refperiode_id = $defaultPeriod ? $defaultPeriod->refperiode_id : null;
        }

        // Ambil list SKPD (atau filter jika refskpd_id diberikan)
        $skpdQuery = SakipSkpd::find();
        if ($refskpd_id !== null) {
            $skpdQuery->where(['refskpd_id' => $refskpd_id]);
        }
        $skpdList = $skpdQuery->all();

        $dataSubkegiatan = [];

        foreach ($skpdList as $skpd) {
            $refskpd_id = $skpd->refskpd_id;
            if ($refskpd_id == 1) continue;

            $subkegiatanList = SakipCascadingsubkegiatan::find()
                ->where([
                    'refskpd_id' => $refskpd_id,
                    'refperiode_id' => $refperiode_id
                ])
                ->with([
                    'refIndikatorcascadingsubkegiatan.refIndikatorCascadingSubkegiatanTriwulan' => function ($query) use ($refskpd_id, $refperiode_id) {
                        $query->andWhere([
                            'refskpd_id' => $refskpd_id,
                            'refperiode_id' => $refperiode_id,
                        ])->orderBy(['reftriwulan_id' => SORT_ASC]);
                    }
                ])
                ->all();

            $subkegiatanData = [];

            foreach ($subkegiatanList as $subkegiatan) {
                $indikatorData = [];

                foreach ($subkegiatan->refIndikatorcascadingsubkegiatan as $indikator) {
                    $triwulanData = $indikator->refIndikatorCascadingSubkegiatanTriwulan;

                    $indikatorData[] = [
                        'uraian_indikator' => $subkegiatan->uraian_indikatorsubkegiatan ?? $indikator->uraian_indikatorsubkegiatan,
                        'subkegiatan_satuan' => $subkegiatan->subkegiatan_satuan,
                        'triwulan' => $triwulanData,
                    ];
                }

                $subkegiatanData[] = [
                    'uraian_subkegiatan' => $subkegiatan->uraian_sasaransubkegiatan,
                    'indikator' => $indikatorData,
                ];
            }

            $endTime = microtime(true);
            $responseTime = round(($endTime - $startTime) * 1000, 2); // milidetik


            $dataSubkegiatan[] = [
                'refskpd_id' => $refskpd_id,
                'nama_skpd' => $skpd->nama_skpd,
                'subkegiatan' => $subkegiatanData,
            ];
        }

        return [
            'success' => true,
            'refperiode_id' => (int)$refperiode_id,
            'refskpd_id' => $refskpd_id !== null ? (int)$refskpd_id : null,
            'response_time_ms' => $responseTime,
            'data' => $dataSubkegiatan,
        ];
    }

    public function actionListCapkinSubkegiatanSkpdPaginated($refperiode_id = null, $filter_refskpd_id = null, $page = 1)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $startTime = microtime(true); // Pindahkan startTime ke awal

        // 1. Handle refperiode_id
        if ($refperiode_id === null) {
            $currentYear = date('Y');
            $defaultPeriod = SakipPeriode::find()->where(['periode' => $currentYear])->one();
            if ($defaultPeriod) {
                $refperiode_id = $defaultPeriod->refperiode_id;
            }
        } else {
            $refperiode_id = (int) $refperiode_id;
        }

        if ($refperiode_id === null) {
            Yii::$app->response->statusCode = 400;
            // responseTime dihitung bahkan untuk error response
            $responseTime = round((microtime(true) - $startTime) * 1000, 2);
            return [
                'success' => false,
                'message' => 'refperiode_id tidak valid atau tidak ditemukan periode default untuk tahun ini.',
                'refperiode_id_aktif' => null,
                'filter_refskpd_id_applied' => $filter_refskpd_id !== null ? (int)$filter_refskpd_id : null,
                'pagination_skpd' => null,
                'response_time_ms' => $responseTime,
                'data' => [],
            ];
        }

        $dataOutput = [];
        $paginationInfo = null;
        $limit_skpd_per_page = 5;

        $skpdQuery = SakipSkpd::find()->where(['!=', 'refskpd_id', 1]);

        if ($filter_refskpd_id !== null) {
            $filter_refskpd_id = (int) $filter_refskpd_id;
            $skpdQuery->andWhere(['refskpd_id' => $filter_refskpd_id]);
            $skpdList = $skpdQuery->orderBy(['refskpd_id' => SORT_ASC])->all();

            $totalSkpdCount = count($skpdList);
            $paginationInfo = [
                'currentPage' => $totalSkpdCount > 0 ? 1 : 0,
                'skpdPerHalaman' => $totalSkpdCount,
                'totalSkpdKeseluruhan' => $totalSkpdCount,
                'totalSkpdDiHalamanIni' => $totalSkpdCount,
                'totalPages' => $totalSkpdCount > 0 ? 1 : 0,
                'note' => 'Data difilter berdasarkan refskpd_id tertentu.',
            ];
        } else {
            $page = (int)$page;
            if ($page < 1) {
                $page = 1;
            }

            $countQuery = clone $skpdQuery;
            $totalSkpdCount = (int)$countQuery->count();
            $totalPages = (int)ceil($totalSkpdCount / $limit_skpd_per_page);

            if ($page > $totalPages && $totalPages > 0) {
                $page = $totalPages;
            }

            $offset = ($page - 1) * $limit_skpd_per_page;
            $skpdList = $skpdQuery->offset($offset)
                ->limit($limit_skpd_per_page)
                ->orderBy(['refskpd_id' => SORT_ASC])
                ->all();

            $paginationInfo = [
                'currentPage' => $page,
                'skpdPerHalaman' => $limit_skpd_per_page,
                'totalSkpdKeseluruhan' => $totalSkpdCount,
                'totalSkpdDiHalamanIni' => count($skpdList),
                'totalPages' => $totalPages,
            ];
        }

        foreach ($skpdList as $skpd) { // $skpd adalah objek SakipSkpd
            $subkegiatanList = SakipCascadingsubkegiatan::find()
                ->where([
                    'refskpd_id' => $skpd->refskpd_id,
                    'refperiode_id' => $refperiode_id
                ])
                ->with([
                    // Pastikan nama relasi ini benar:
                    // 'refIndikatorcascadingsubkegiatan' adalah relasi dari SakipCascadingsubkegiatan ke model Indikator Subkegiatan.
                    // 'refIndikatorCascadingSubkegiatanTriwulan' adalah relasi dari model Indikator Subkegiatan ke model Triwulan Indikator Subkegiatan.
                    'refIndikatorcascadingsubkegiatan.refIndikatorCascadingSubkegiatanTriwulan' => function ($query) use ($skpd, $refperiode_id) {
                        $query->andWhere([
                            'refskpd_id' => $skpd->refskpd_id, // Tambahkan kondisi ini jika tabel triwulan juga punya refskpd_id
                            'refperiode_id' => $refperiode_id, // Tambahkan kondisi ini jika tabel triwulan juga punya refperiode_id
                        ])->orderBy(['reftriwulan_id' => SORT_ASC]);
                    }
                ])
                ->all();

            $subkegiatanDataFormatted = [];
            foreach ($subkegiatanList as $subkegiatan) { // $subkegiatan adalah objek SakipCascadingsubkegiatan
                $indikatorDataFormatted = [];
                if ($subkegiatan->refIndikatorcascadingsubkegiatan) { // Cek relasi
                    foreach ($subkegiatan->refIndikatorcascadingsubkegiatan as $indikator) { // $indikator adalah objek Indikator Subkegiatan
                        $triwulanDataFormatted = [];
                        if ($indikator->refIndikatorCascadingSubkegiatanTriwulan) { // Cek relasi
                            foreach ($indikator->refIndikatorCascadingSubkegiatanTriwulan as $triwulan) { // $triwulan adalah objek Triwulan Indikator Subkegiatan
                                $triwulanDataFormatted[] = [
                                    'refindikatorsubkegiatantriwulan_id' => $triwulan->refindikatorsubkegiatantriwulan_id ?? ($triwulan->id ?? null), // Sesuai Swagger
                                    'reftriwulan_id' => $triwulan->reftriwulan_id ?? null,
                                    'triwulan_target_rkt' => $triwulan->triwulan_target_rkt ?? null,
                                    'triwulan_target_rkt_p' => $triwulan->triwulan_target_rkt_p ?? null,
                                    'triwulan_target_pk' => $triwulan->triwulan_target_pk ?? null,
                                    'triwulan_target_pk_p' => $triwulan->triwulan_target_pk_p ?? null,
                                    'triwulan_realisasi' => $triwulan->triwulan_realisasi ?? null,
                                    'triwulan_capaian' => $triwulan->triwulan_capaian ?? null,
                                    'triwulan_keterangan' => $triwulan->triwulan_keterangan ?? null,
                                    'triwulan_analisis' => $triwulan->triwulan_analisis ?? null,
                                    'penyerapan_anggaran' => $triwulan->penyerapan_anggaran ?? null, // Sesuai Swagger
                                ];
                            }
                        }

                        // Mengisi field indikator sesuai Swagger untuk Subkegiatan
                        $indikatorDataFormatted[] = [
                            'refindikatorsubkegiatan_id' => $indikator->refindikatorsubkegiatan_id ?? ($indikator->id ?? null), // Asumsi ID indikator
                            'uraian_indikator' => $indikator->uraian_indikatorsubkegiatan ?? null,
                            'target_rkt' => $indikator->target_rkt ?? null,
                            'target_rkt_p' => $indikator->target_rkt_p ?? null,
                            'target_pk' => $indikator->target_pk ?? null,
                            'target_pk_p' => $indikator->target_pk_p ?? null,
                            'realisasi' => $indikator->realisasi ?? null,
                            'capaian' => $indikator->capaian ?? null,
                            'keterangan' => $indikator->keterangan ?? null,
                            'analisis' => $indikator->analisis ?? null,
                            'triwulan' => $triwulanDataFormatted,
                        ];
                    }
                }

                $subkegiatanDataFormatted[] = [
                    'refsubkegiatan_id' => $subkegiatan->refsubkegiatan_id ?? ($subkegiatan->id ?? null), // Asumsi ID subkegiatan
                    'uraian_subkegiatan' => $subkegiatan->uraian_sasaransubkegiatan ?? ($subkegiatan->uraian_subkegiatan ?? null), // Sesuaikan field
                    'subkegiatan_satuan' => $subkegiatan->subkegiatan_satuan ?? null, // Sesuai Swagger (level subkegiatan)
                    'subkegiatan_target' => $subkegiatan->subkegiatan_target ?? null, // Sesuai Swagger
                    'subkegiatan_anggaran' => $subkegiatan->subkegiatan_anggaran ?? null, // Sesuai Swagger
                    'indikator' => $indikatorDataFormatted,
                ];
            }

            $dataOutput[] = [
                'refskpd_id' => $skpd->refskpd_id,
                'nama_skpd' => $skpd->nama_skpd,
                'subkegiatan' => $subkegiatanDataFormatted,
            ];
        }

        // Hitung responseTime di akhir, sebelum return
        $responseTime = round((microtime(true) - $startTime) * 1000, 2);

        return [
            'success' => true,
            'refperiode_id_aktif' => (int)$refperiode_id,
            'filter_refskpd_id_applied' => $filter_refskpd_id !== null ? (int)$filter_refskpd_id : null,
            'pagination_skpd' => $paginationInfo,
            'response_time_ms' => $responseTime,
            'data' => $dataOutput,
        ];
    }
}
