<?php

namespace frontend\controllers;

use Yii;
use frontend\models\SakipTujuanrenstra;
use frontend\models\search\SakipTujuanrenstraSearch;
use frontend\models\SakipIndikatortujuanrenstra;
use frontend\models\SakipSkpd;
use frontend\models\SakipPeriode;
use frontend\models\SakipMisi;
use frontend\models\SakipTujuan;
use frontend\models\SakipKoordinasi;
use frontend\models\SakipSasaranrenstra;
use frontend\models\search\SakipIndikatortujuanrenstraSearch;
use frontend\models\search\SakipSasaranrenstraSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
use yii\helpers\Url;
use yii\web\Response;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\db\Expression;
use yii\helpers\Json;
use yii\helpers\ArrayHelper;
use yii\filters\AccessControl;
use yii\web\ForbiddenHttpException;

/**
 * SakipTujuanrenstraController implements the CRUD actions for SakipTujuanrenstra model.
 */
class SakipTujuanrenstraController extends Controller
{
    /**
     * @inheritDoc
     */
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

    /**
     * Lists all SakipTujuanrenstra models.
     *
     * @return string
     */
    // public function actionIndex($refperiode_id = null)
    // {
    //     // Inisialisasi model pencarian dan data provider
    //     $searchModel = new SakipTujuanrenstraSearch();
    //     $dataProvider = $searchModel->search($this->request->queryParams);
    //     $dataProvider->pagination = false;

    //     // Ambil informasi SKPD dari user yang sedang login
    //     $user = Yii::$app->user->identity;
    //     $refskpd_id = $user->refskpd_id;
    //     $nama_skpd = SakipSkpd::find()->select('nama_skpd')->where(['refskpd_id' => $refskpd_id])->scalar();

    //     // Ambil semua data periode untuk dropdown filter
    //     $periodeList = SakipPeriode::find()->all();

    //     // Jika periode tidak dipilih, atur periode default ke tahun berjalan
    //     if ($refperiode_id === null) {
    //         $currentYear = date('Y');
    //         $defaultPeriod = SakipPeriode::find()->where(['periode' => $currentYear])->one();
    //         $refperiode_id = $defaultPeriod ? $defaultPeriod->refperiode_id : null;
    //     }

    //     // Bangun query untuk mengambil data sasaran renstra
    //     $sasaranRenstraQuery = SakipSasaranrenstra::find()
    //         ->select([
    //             'refsasaran_id',
    //             'refskpd_id',
    //             'refperiode_id',
    //             'refmisi_id',
    //             'reftujuan_id',
    //             'refsasaranrenstra_id',
    //             'uraian_sasaranrenstra'
    //         ])
    //         ->where(['refskpd_id' => $refskpd_id])
    //         ->groupBy(['refsasaran_id'])
    //         ->orderBy(['refmisi_id' => SORT_ASC, 'reftujuan_id' => SORT_ASC]);

    //     // Terapkan filter periode jika ID periode valid
    //     if ($refperiode_id !== null) {
    //         $sasaranRenstraQuery->andWhere(['refperiode_id' => $refperiode_id]);
    //     }

    //     // Eksekusi query dan periksa jika hasilnya kosong
    //     $sasaranRenstraList = $sasaranRenstraQuery->all();
    //     $dataEmpty = empty($sasaranRenstraList);

    //     // Dapatkan nilai (tahun) dari periode yang dipilih untuk ditampilkan di view
    //     $selectedPeriod = SakipPeriode::find()->where(['refperiode_id' => $refperiode_id])->one();
    //     $selectedPeriodValue = $selectedPeriod ? $selectedPeriod->periode : null;

    //     // Kirim semua data yang diperlukan ke view
    //     return $this->render('index', [
    //         'searchModel'         => $searchModel,
    //         'dataProvider'        => $dataProvider,
    //         'nama_skpd'           => $nama_skpd,
    //         'periodeList'         => $periodeList,
    //         'sasaranRenstraList'  => $sasaranRenstraList,
    //         'selectedPeriodId'    => $refperiode_id,
    //         'selectedPeriodValue' => $selectedPeriodValue,
    //         'dataEmpty'           => $dataEmpty,
    //     ]);
    // }

    public function actionIndex($refperiode_id = null)
    {
        // Ambil refskpd_id dari user saat ini
        $user = Yii::$app->user->identity;
        $refskpd_id = $user->refskpd_id;

        // Ambil nama_skpd berdasarkan refskpd_id
        $nama_skpd = SakipSkpd::find()->select('nama_skpd')->where(['refskpd_id' => $refskpd_id])->scalar();

        // Ambil semua periode
        $periodeList = SakipPeriode::find()->all();

        // Set default period to this year if not provided
        if ($refperiode_id === null) {
            $currentYear = date('Y');
            $defaultPeriod = SakipPeriode::find()->where(['periode' => $currentYear])->one();
            $refperiode_id = $defaultPeriod ? $defaultPeriod->refperiode_id : null;
        }

        // Retrieve the periode based on refperiode_id
        $selectedPeriod = SakipPeriode::find()->where(['refperiode_id' => $refperiode_id])->one();
        $selectedPeriodValue = $selectedPeriod ? $selectedPeriod->periode : null; // Get the periode value
        $refperiode_5tahun_id = $selectedPeriod ? $selectedPeriod->refperiode_5tahun_id : null;

        $searchParams = $this->request->queryParams;
        if (!isset($searchParams['SakipTujuanrenstraSearch'])) {
            $searchParams['SakipTujuanrenstraSearch'] = [];
        }
        $searchParams['SakipTujuanrenstraSearch']['refperiode_5tahun_id'] = $refperiode_5tahun_id;
        $searchParams['SakipTujuanrenstraSearch']['refskpd_id'] = $refskpd_id;

        $searchModel = new SakipTujuanrenstraSearch();
        $dataProvider = $searchModel->search($searchParams);
        $dataProvider->pagination = false;

        // --- LOGIKA BARU: PENGELOMPOKAN DATA ---

        // 1. Ambil semua data yang dibutuhkan dengan Eager Loading agar efisien
        $sasaranRenstraList = SakipSasaranrenstra::find()
            ->where(['refskpd_id' => $refskpd_id, 'refperiode_5tahun_id' => $refperiode_5tahun_id])
            ->with(['misi', 'tujuan', 'tujuanRenstra']) // Memuat semua relasi dalam satu query
            ->orderBy(['refmisi_id' => SORT_ASC, 'reftujuan_id' => SORT_ASC])
            ->all();

        // 2. Susun data ke dalam array bersarang (grouped)
        $groupedData = [];
        foreach ($sasaranRenstraList as $sasaran) {
            $misiId = $sasaran->refmisi_id;
            $tujuanId = $sasaran->reftujuan_id;

            // Group berdasarkan Misi
            if (!isset($groupedData[$misiId])) {
                $groupedData[$misiId] = [
                    'uraian' => $sasaran->misi ? $sasaran->misi->uraian_misi : 'Misi tidak ditemukan',
                    'tujuans' => []
                ];
            }

            // Group berdasarkan Tujuan di dalam Misi
            if (!isset($groupedData[$misiId]['tujuans'][$tujuanId])) {
                $groupedData[$misiId]['tujuans'][$tujuanId] = [
                    'uraian' => $sasaran->tujuan ? $sasaran->tujuan->uraian_tujuan : 'Tujuan tidak ditemukan',
                    'sasarans' => []
                ];
            }

            // Tambahkan Sasaran ke dalam Tujuan yang sesuai
            $groupedData[$misiId]['tujuans'][$tujuanId]['sasarans'][] = $sasaran;
        }

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'nama_skpd' => $nama_skpd,
            'periodeList' => $periodeList,  // Kirim data periode ke view
            'selectedPeriodId' => $refperiode_id, // Add selected period id
            'sasaranRenstraList' => $sasaranRenstraList, // Pass the list of renstra data
            'groupedData' => $groupedData, // <-- Kirim data yang sudah dikelompokkan
            'selectedPeriodValue' => $selectedPeriodValue, // Include selected period value
        ]);
    }

    // public function actionIndexDev($refperiode_id = null, $refskpd_id = null)
    // {
    //     $searchModel = new SakipTujuanrenstraSearch();
    //     $dataProvider = $searchModel->search($this->request->queryParams);
    //     $dataProvider->pagination = false;

    //     // Ambil refskpd_id dari user saat ini jika tidak ada di request
    //     if ($refskpd_id === null) {
    //         $user = Yii::$app->user->identity;
    //         $refskpd_id = $user->refskpd_id;
    //     }

    //     // Ambil nama_skpd berdasarkan refskpd_id
    //     $nama_skpd = SakipSkpd::find()->select('nama_skpd')->where(['refskpd_id' => $refskpd_id])->scalar();

    //     // Ambil semua periode
    //     $periodeList = SakipPeriode::find()->all();

    //     // Set default period to this year if not provided
    //     if ($refperiode_id === null) {
    //         $currentYear = date('Y');
    //         $defaultPeriod = SakipPeriode::find()->where(['periode' => $currentYear])->one();
    //         $refperiode_id = $defaultPeriod ? $defaultPeriod->refperiode_id : null;
    //     }

    //     // Ambil data dari sakip_sasaranrenstra sesuai dengan refskpd_id user dan refperiode_id
    //     $sasaranRenstraQuery = SakipSasaranrenstra::find()
    //         ->select(['refsasaran_id', 'refskpd_id', 'refperiode_id', 'refmisi_id', 'reftujuan_id', 'refsasaranrenstra_id'])
    //         ->where(['refskpd_id' => $refskpd_id])
    //         ->andWhere(['refperiode_id' => $refperiode_id])
    //         ->groupBy(['refsasaran_id']) // Grouping by refsasaran_id
    //         ->orderBy(['refmisi_id' => SORT_ASC, 'reftujuan_id' => SORT_ASC]);

    //     if ($refperiode_id !== null) {
    //         $sasaranRenstraQuery->andWhere(['refperiode_id' => $refperiode_id]);
    //     }

    //     $sasaranRenstraList = $sasaranRenstraQuery->all();

    //     // Cek apakah data kosong
    //     $dataEmpty = empty($sasaranRenstraList);

    //     // Ambil daftar SKPD untuk dropdown
    //     $skpdList = ArrayHelper::map(SakipSkpd::find()->all(), 'refskpd_id', 'nama_skpd');

    //     // Retrieve the periode based on refperiode_id
    //     $selectedPeriod = SakipPeriode::find()->where(['refperiode_id' => $refperiode_id])->one();
    //     $selectedPeriodValue = $selectedPeriod ? $selectedPeriod->periode : null; // Get the periode value

    //     return $this->render('index-dev', [
    //         'searchModel' => $searchModel,
    //         'dataProvider' => $dataProvider,
    //         'nama_skpd' => $nama_skpd,
    //         'periodeList' => $periodeList,  // Kirim data periode ke view
    //         'selectedPeriodId' => $refperiode_id, // Add selected period id
    //         'sasaranRenstraList' => $sasaranRenstraList, // Pass the list of renstra data
    //         'dataEmpty' => $dataEmpty, // Pass the data empty flag
    //         'selectedPeriodValue' => $selectedPeriodValue, // Include selected period value
    //         'selectedSkpdId' => $refskpd_id,
    //         'skpdList' => $skpdList,
    //     ]);
    // }

    public function actionIndexDev($refperiode_id = null, $refskpd_id = null)
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

        // Ambil nama_skpd berdasarkan refskpd_id
        $nama_skpd = $refskpd_id ? SakipSkpd::find()->select('nama_skpd')->where(['refskpd_id' => $refskpd_id])->scalar() : 'Tidak ada SKPD dipilih';

        // Ambil semua periode
        $periodeList = SakipPeriode::find()->all();

        // Set default period to this year if not provided
        if ($refperiode_id === null) {
            $currentYear = date('Y');
            $defaultPeriod = SakipPeriode::find()->where(['periode' => $currentYear])->one();
            $refperiode_id = $defaultPeriod ? $defaultPeriod->refperiode_id : null;
        }

        // Retrieve the periode based on refperiode_id
        $selectedPeriod = SakipPeriode::find()->where(['refperiode_id' => $refperiode_id])->one();
        $selectedPeriodValue = $selectedPeriod ? $selectedPeriod->periode : null; // Get the periode value
        $refperiode_5tahun_id = $selectedPeriod ? $selectedPeriod->refperiode_5tahun_id : null;

        $searchParams = $this->request->queryParams;
        if (!isset($searchParams['SakipTujuanrenstraSearch'])) {
            $searchParams['SakipTujuanrenstraSearch'] = [];
        }
        $searchParams['SakipTujuanrenstraSearch']['refperiode_5tahun_id'] = $refperiode_5tahun_id;
        $searchParams['SakipTujuanrenstraSearch']['refskpd_id'] = $refskpd_id;

        $searchModel = new SakipTujuanrenstraSearch();
        $dataProvider = $searchModel->search($searchParams);
        $dataProvider->pagination = false;

        // Ambil data dari sakip_sasaranrenstra sesuai dengan refskpd_id user dan refperiode_5tahun_id
        $sasaranRenstraQuery = SakipSasaranrenstra::find()
            ->select(['refsasaran_id', 'refskpd_id', 'refperiode_5tahun_id', 'refmisi_id', 'reftujuan_id', 'refsasaranrenstra_id'])
            ->where(['refskpd_id' => $refskpd_id])
            ->andWhere(['refperiode_5tahun_id' => $refperiode_5tahun_id])
            ->groupBy(['refsasaran_id']) // Grouping by refsasaran_id
            ->orderBy(['refmisi_id' => SORT_ASC, 'reftujuan_id' => SORT_ASC]);

        $sasaranRenstraList = $sasaranRenstraQuery->all();

        // Cek apakah data kosong
        $dataEmpty = empty($sasaranRenstraList);

        return $this->render('index-dev', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'nama_skpd' => $nama_skpd,
            'periodeList' => $periodeList,  // Kirim data periode ke view
            'selectedPeriodId' => $refperiode_id, // Add selected period id
            'sasaranRenstraList' => $sasaranRenstraList, // Pass the list of renstra data
            'dataEmpty' => $dataEmpty, // Pass the data empty flag
            'selectedPeriodValue' => $selectedPeriodValue, // Include selected period value
            'selectedSkpdId' => $refskpd_id,
            'skpdList' => $skpdList,
        ]);
    }

    /**
     * Displays a single SakipTujuanrenstra model.
     * @param int $reftujuanrenstra_id Refstrategi ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found reftujuanrens$reftujuanrenstra_id
     */
    public function actionView($reftujuanrenstra_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($reftujuanrenstra_id),
        ]);
    }

    /**
     * Creates a new SakipTujuanrenstra model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate($refsasaranrenstra_id = null, $refperiode_id = null)
    {
        $model = new SakipTujuanrenstra();

        // Ambil refskpd_id dari user saat ini
        $user = Yii::$app->user->identity;
        $refskpd_id = $user->refskpd_id;

        // Set nilai refsasaranrenstra_id, reftujuanrenstra_id, dan refperiode_id pada model jika diberikan
        if ($refsasaranrenstra_id) {
            $model->refsasaranrenstra_id = $refsasaranrenstra_id;

            // Cari data yang terkait dengan refsasaranrenstra_id
            $sasaranRenstra = SakipSasaranrenstra::findOne($refsasaranrenstra_id);

            if ($sasaranRenstra) {
                $model->refsasaran_id = $sasaranRenstra->refsasaran_id;
                $model->refmisi_id = $sasaranRenstra->misi->refmisi_id;
                $model->reftujuan_id = $sasaranRenstra->tujuan->reftujuan_id;
            }
        }

        if ($refperiode_id) {
            $model->refperiode_id = $refperiode_id;
        }


        if (Yii::$app->request->isAjax) {
            if ($model->load(Yii::$app->request->post())) {
                // Set refskpd_id from user identity
                $model->refskpd_id = $refskpd_id;
                $model->user_create = $user->username; // Mengambil username dari user saat ini
                $model->date_create = date('Y-m-d'); // Mengambil tanggal saat ini

                // Set user_edit, date_edit, user_delete, dan date_delete menjadi null
                $model->user_edit = null;
                $model->date_edit = null;
                $model->user_delete = null;
                $model->date_delete = null;

                if ($model->save()) {
                    Yii::$app->session->setFlash('success', 'Berhasil Tambah Data');
                    return $this->asJson(['success' => true, 'redirect' => \yii\helpers\Url::to(['index', 'refperiode_id' => $model->refperiode_id])]);
                } else {
                    Yii::error("Error saving data: " . json_encode($model->getErrors()));
                    return $this->asJson(['success' => false, 'errors' => $model->getErrors()]);
                }
            }

            return $this->renderAjax('_form', [
                'model' => $model,
            ]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }



    /**
     * Updates an existing SakipTujuanrenstra model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $reftujuanrenstra_id Refstrategi ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($reftujuanrenstra_id, $refperiode_id = null)
    {
        $model = $this->findModel($reftujuanrenstra_id);

        if (Yii::$app->request->isAjax) {
            if ($model->load(Yii::$app->request->post())) {
                if ($model->save()) {
                    Yii::$app->session->setFlash('success', 'Berhasil Memperbarui Data');
                    $referrer = Yii::$app->request->referrer;
                    $redirectUrl = \yii\helpers\Url::to(['index', 'refperiode_id' => $model->refperiode_id]);
                    if (strpos($referrer, 'index-dev') !== false) {
                        $redirectUrl = \yii\helpers\Url::to(['index-dev', 'refperiode_id' => $model->refperiode_id, 'refskpd_id' => $model->refskpd_id]);
                    }
                    return $this->asJson(['success' => true, 'redirect' => $redirectUrl]);
                } else {
                    // Tambahkan log error
                    Yii::error("Error saving data: " . json_encode($model->getErrors()));
                    return $this->asJson(['success' => false, 'errors' => $model->getErrors()]);
                }
            }

            return $this->renderAjax('_formupdate', [
                'model' => $model,
            ]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing SakipTujuanrenstra model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $reftujuanrenstra_id Refstrategi ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($reftujuanrenstra_id)
    {
        $model = $this->findModel($reftujuanrenstra_id);

        // Ambil refperiode_id sebelum menghapus model
        $refperiode_id = $model->refperiode_id;

        $model->delete();

        // Redirect kembali ke index dengan refperiode_id
        return $this->redirect(['index', 'refperiode_id' => $refperiode_id]);
    }
    /**
     * Finds the SakipTujuanrenstra model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $reftujuanrenstra_id Refstrategi ID
     * @return SakipTujuanrenstra the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($reftujuanrenstra_id)
    {
        if (($model = SakipTujuanrenstra::findOne(['reftujuanrenstra_id' => $reftujuanrenstra_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
