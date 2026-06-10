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

    public function actionCariCapkinIndikatorRenstra()
    {

        die('masuk'); // jika ini tidak muncul saat request, berarti routing belum benar
        Yii::$app->response->format = Response::FORMAT_JSON;
        $startTime = microtime(true);

        $request = Yii::$app->request;
        $refperiode_id = $request->post('refperiode_id', $request->get('refperiode_id'));
        $refskpd_id = $request->post('refskpd_id', $request->get('refskpd_id'));


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

        $responseTime = round((microtime(true) - $startTime) * 1000, 2);

        return [
            'success' => true,
            'refperiode_id' => (int)$refperiode_id,
            'refskpd_id' => $refskpd_id !== null ? (int)$refskpd_id : null,
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
}
