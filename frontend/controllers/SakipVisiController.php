<?php

namespace frontend\controllers;

use Yii;
use frontend\models\SakipVisi;
use frontend\models\SakipMisi;
use frontend\models\SakipSasaranrenstra;
use frontend\models\SakipVisiP;
use frontend\models\SakipMisiP;
use frontend\models\SakipSasaranrenstraP;
use frontend\models\SakipPeriode;
use frontend\models\SakipSkpd;
use frontend\models\SakipKoordinasi;
use frontend\models\search\SakipSasaranrenstraSearch;
use frontend\models\search\SakipVisiSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\filters\AccessControl;
use yii\web\ForbiddenHttpException; // <-- Tambahkan ini

/**
 * SakipVisiController implements the CRUD actions for SakipVisi model.
 */
class SakipVisiController extends Controller
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
     * Lists all SakipVisi models.
     *
     * @return string
     */
    public function actionIndex($refperiode_id = null, $refskpd_id = null)
    {
        // Ambil refskpd_id dari user saat ini
        $user = Yii::$app->user->identity;
        $refskpd_id = $user->refskpd_id;

        // Ambil nama_skpd berdasarkan refskpd_id
        $nama_skpd = SakipSkpd::find()->select('nama_skpd')->where(['refskpd_id' => $refskpd_id])->scalar();

        // Fetch all periods
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

        $searchModel = new SakipVisiSearch();
        $searchModel->refperiode_5tahun_id = $refperiode_5tahun_id;
        $dataProvider = $searchModel->search($this->request->queryParams);
        $dataProvider->pagination = false;

        // 1. Ambil Data Induk
        $visi = SakipVisi::find()->where(['refperiode_5tahun_id' => $refperiode_5tahun_id])->one();

        // Ambil semua ID misi dari sasaran renstra untuk skpd dan periode terpilih
        $refMisiIds = SakipSasaranRenstra::find()
            ->select('refmisi_id')
            ->where(['refskpd_id' => $refskpd_id, 'refperiode_5tahun_id' => $refperiode_5tahun_id])
            ->distinct()
            ->column();

        $misiData = SakipMisi::find()
            ->where(['refmisi_id' => $refMisiIds, 'refperiode_5tahun_id' => $refperiode_5tahun_id])
            ->all();

        // 2. Ambil Data Perubahan
        $visiP = SakipVisiP::find()->where(['refperiode_5tahun_id' => $refperiode_5tahun_id])->one();

        // Ambil semua ID misi perubahan dari sasaran renstra perubahan
        $refMisiPIds = SakipSasaranRenstraP::find()
            ->select('refmisi_p_id')
            ->where(['refskpd_id' => $refskpd_id, 'refperiode_5tahun_id' => $refperiode_5tahun_id])
            ->distinct()
            ->column();

        $misiPData = SakipMisiP::find()
            ->where(['refmisi_p_id' => $refMisiPIds, 'refperiode_5tahun_id' => $refperiode_5tahun_id])
            ->all();

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'nama_skpd' => $nama_skpd,
            'periodeList' => $periodeList,
            'selectedPeriodId' => $refperiode_id,
            'selectedPeriodValue' => $selectedPeriodValue, // Include selected period value
            // Data Induk
            'visi' => $visi,
            'misiData' => $misiData,
            // Data Perubahan
            'visiP' => $visiP,
            'misiPData' => $misiPData,
        ]);
    }

    // public function actionIndexDev($refperiode_id = null, $refskpd_id = null)
    // {
    //     $searchModel = new SakipSasaranrenstraSearch();
    //     $dataProvider = $searchModel->search($this->request->queryParams);
    //     $dataProvider->pagination = false;

    //     // Ambil refskpd_id dari user saat ini jika tidak ada di request
    //     if ($refskpd_id === null) {
    //         $user = Yii::$app->user->identity;
    //         $refskpd_id = $user->refskpd_id;
    //     }

    //     // Ambil nama_skpd berdasarkan refskpd_id
    //     $nama_skpd = SakipSkpd::find()->select('nama_skpd')->where(['refskpd_id' => $refskpd_id])->scalar();

    //     // Fetch all periods
    //     $periodeList = SakipPeriode::find()->all();

    //     // Set default period to this year if not provided
    //     if ($refperiode_id === null) {
    //         $currentYear = date('Y');
    //         $defaultPeriod = SakipPeriode::find()->where(['periode' => $currentYear])->one();
    //         $refperiode_id = $defaultPeriod ? $defaultPeriod->refperiode_id : null;
    //     }

    //     // Query untuk mendapatkan refmisi_id dari sakip_sasaranrenstra yang sesuai dengan refskpd_id pengguna saat ini
    //     $refMisiIds = SakipSasaranRenstra::find()
    //         ->select('refmisi_id')
    //         ->where(['refskpd_id' => $refskpd_id])
    //         ->column();

    //     // Ambil uraian_misi berdasarkan refmisi_id dan refperiode_id yang dipilih
    //     $misiData = SakipMisi::find()
    //         ->where(['refmisi_id' => $refMisiIds, 'refperiode_id' => $refperiode_id])
    //         ->all();

    //     // Ambil data sasaran renstra berdasarkan refskpd_id dan refperiode_id
    //     $sasaranData = SakipSasaranRenstra::find()
    //         ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
    //         ->all();

    //     $assignments = Yii::$app->authManager->getAssignments(Yii::$app->user->getId());
    //     if (isset($assignments['superadmin']) || isset($assignments['admin'])) {

    //         // Ambil daftar SKPD untuk dropdown
    //         $skpdList = ArrayHelper::map(
    //             SakipSkpd::find()->where(['skpd_isaktif' => 'T'])->orderBy('nama_skpd ASC')->all(),
    //             'refskpd_id',
    //             'nama_skpd'
    //         );
    //     } else {
    //         // =========================================================================
    //         // PERUBAHAN HANYA DI BLOK INI
    //         // =========================================================================
    //         // Jika bukan superadmin/admin, ambil SKPD berdasarkan tabel koordinasi.

    //         // 1. Dapatkan ID user yang sedang login.
    //         $userId = Yii::$app->user->getId();

    //         // 2. Cari semua refskpd_id yang dikoordinasikan oleh user ini.
    //         $coordinatedSkpdIds = SakipKoordinasi::find()
    //             ->select('refskpd_id')
    //             ->where(['refuser_id' => $userId])
    //             ->column();

    //         // 3. Ambil data SKPD berdasarkan daftar ID tersebut.
    //         //    Default ke array kosong jika user tidak mengoordinasikan SKPD apa pun.
    //         $skpdList = [];
    //         if (!empty($coordinatedSkpdIds)) {
    //             $skpdList = ArrayHelper::map(
    //                 SakipSkpd::find()
    //                     ->where(['refskpd_id' => $coordinatedSkpdIds, 'skpd_isaktif' => 'T'])
    //                     ->orderBy('nama_skpd ASC')
    //                     ->all(),
    //                 'refskpd_id',
    //                 'nama_skpd'
    //             );
    //         }
    //         // =========================================================================
    //         // AKHIR PERUBAHAN
    //         // =========================================================================
    //     }

    //     // Retrieve the periode based on refperiode_id
    //     $selectedPeriod = SakipPeriode::find()->where(['refperiode_id' => $refperiode_id])->one();
    //     $selectedPeriodValue = $selectedPeriod ? $selectedPeriod->periode : null; // Get the periode value

    //     return $this->render('index-dev', [
    //         'searchModel' => $searchModel,
    //         'dataProvider' => $dataProvider,
    //         'nama_skpd' => $nama_skpd,
    //         'periodeList' => $periodeList,
    //         'selectedPeriodId' => $refperiode_id,
    //         'selectedSkpdId' => $refskpd_id,
    //         'selectedPeriodValue' => $selectedPeriodValue, // Include selected period value
    //         'misiData' => $misiData,
    //         'sasaranData' => $sasaranData,
    //         'skpdList' => $skpdList,
    //     ]);
    // }

    public function actionIndexDev($refperiode_id = null, $refskpd_id = null)
    {
        $user = Yii::$app->user->identity;
        $assignments = Yii::$app->authManager->getAssignments($user->id);

        $skpdList = [];
        $allowedSkpdIds = []; // Daftar ID SKPD yang diizinkan untuk user ini

        // =========================================================================
        // TAHAP 1: TENTUKAN HAK AKSES DAN DAFTAR SKPD YANG DIIZINKAN
        // =========================================================================
        if (isset($assignments['superadmin']) || isset($assignments['admin'])) {
            // --- BLOK UNTUK ADMIN ---
            $allSkpd = SakipSkpd::find()->where(['skpd_isaktif' => 'T'])->orderBy('nama_skpd ASC')->all();
            $skpdList = ArrayHelper::map($allSkpd, 'refskpd_id', 'nama_skpd');
            // Admin boleh melihat semua ID
            $allowedSkpdIds = array_keys($skpdList);
        } else {
            // --- BLOK UNTUK NON-ADMIN (KOORDINATOR) ---
            $coordinatedSkpdIds = SakipKoordinasi::find()
                ->select('refskpd_id')
                ->where(['refuser_id' => $user->id])
                ->column();

            $allowedSkpdIds = $coordinatedSkpdIds; // Simpan ID yang diizinkan

            if (!empty($allowedSkpdIds)) {
                $skpdList = ArrayHelper::map(
                    SakipSkpd::find()->where(['refskpd_id' => $allowedSkpdIds, 'skpd_isaktif' => 'T'])->orderBy('nama_skpd ASC')->all(),
                    'refskpd_id',
                    'nama_skpd'
                );
            }
        }

        // =========================================================================
        // TAHAP 2: VALIDASI DAN PENENTUAN refskpd_id YANG AKAN DIGUNAKAN
        // =========================================================================

        // Jika user mencoba mengakses SKPD via URL, periksa apakah ID itu ada di dalam daftar yang diizinkan.
        if ($refskpd_id !== null && !in_array($refskpd_id, $allowedSkpdIds)) {
            // Jika tidak diizinkan, lemparkan error 403 (Forbidden)
            throw new ForbiddenHttpException('Anda tidak memiliki hak akses untuk melihat data SKPD ini.');
        }

        // Jika tidak ada refskpd_id yang diminta dari URL (atau yang diminta tidak valid),
        // gunakan ID pertama dari daftar yang diizinkan sebagai default.
        if ($refskpd_id === null || !in_array($refskpd_id, $allowedSkpdIds)) {
            if (!empty($allowedSkpdIds)) {
                $refskpd_id = $allowedSkpdIds[0]; // Ambil ID pertama sebagai default
            } else {
                $refskpd_id = null; // User ini tidak punya akses ke SKPD manapun
            }
        }

        // =========================================================================
        // TAHAP 3: AMBIL DATA BERDASARKAN ID YANG SUDAH AMAN
        // Setelah blok di atas, variabel $refskpd_id dijamin aman untuk digunakan.
        // =========================================================================

        // Ambil nama_skpd berdasarkan refskpd_id yang sudah divalidasi
        // Set default periode jika tidak ada di request
        if ($refperiode_id === null) {
            $currentYear = date('Y');
            $defaultPeriod = SakipPeriode::find()->where(['periode' => $currentYear])->one();
            $refperiode_id = $defaultPeriod ? $defaultPeriod->refperiode_id : null;
        }

        $selectedPeriod = SakipPeriode::find()->where(['refperiode_id' => $refperiode_id])->one();
        $selectedPeriodValue = $selectedPeriod ? $selectedPeriod->periode : null;
        $refperiode_5tahun_id = $selectedPeriod ? $selectedPeriod->refperiode_5tahun_id : null;

        // Instantiate search model and dataProvider here
        $searchParams = $this->request->queryParams;
        $searchParams['SakipSasaranrenstraSearch']['refskpd_id'] = $refskpd_id;
        $searchParams['SakipSasaranrenstraSearch']['refperiode_5tahun_id'] = $refperiode_5tahun_id;

        $searchModel = new SakipSasaranrenstraSearch();
        $dataProvider = $searchModel->search($searchParams);
        $dataProvider->pagination = false;

        // Lanjutkan pengambilan data dengan $refskpd_id yang sudah aman
        $misiData = [];
        $sasaranData = [];
        if ($refskpd_id && $refperiode_id) {
            $refMisiIds = SakipSasaranRenstra::find()
                ->select('refmisi_id')
                ->where(['refskpd_id' => $refskpd_id, 'refperiode_5tahun_id' => $refperiode_5tahun_id])
                ->distinct()
                ->column();

            $misiData = SakipMisi::find()
                ->where(['refmisi_id' => $refMisiIds, 'refperiode_5tahun_id' => $refperiode_5tahun_id])
                ->all();

            $sasaranData = SakipSasaranRenstra::find()
                ->where(['refskpd_id' => $refskpd_id, 'refperiode_5tahun_id' => $refperiode_5tahun_id])
                ->all();
        }

        $periodeList = SakipPeriode::find()->all();

        return $this->render('index-dev', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'nama_skpd' => $nama_skpd,
            'periodeList' => $periodeList,
            'selectedPeriodId' => $refperiode_id,
            'selectedSkpdId' => $refskpd_id,
            'selectedPeriodValue' => $selectedPeriodValue,
            'misiData' => $misiData,
            'sasaranData' => $sasaranData,
            'skpdList' => $skpdList,
        ]);
    }




    /**
     * Displays a single SakipVisi model.
     * @param int $refvisi_id Refvisi ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($refvisi_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($refvisi_id),
        ]);
    }

    /**
     * Creates a new SakipVisi model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new SakipVisi();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'refvisi_id' => $model->refvisi_id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing SakipVisi model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $refvisi_id Refvisi ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($refvisi_id)
    {
        $model = $this->findModel($refvisi_id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'refvisi_id' => $model->refvisi_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing SakipVisi model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $refvisi_id Refvisi ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($refvisi_id)
    {
        $this->findModel($refvisi_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the SakipVisi model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $refvisi_id Refvisi ID
     * @return SakipVisi the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($refvisi_id)
    {
        if (($model = SakipVisi::findOne(['refvisi_id' => $refvisi_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
