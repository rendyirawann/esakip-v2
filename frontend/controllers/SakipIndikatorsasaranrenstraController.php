<?php

namespace frontend\controllers;

use Yii;
use frontend\models\SakipSasaranrenstra;
use frontend\models\SakipSkpd;
use frontend\models\SakipPeriode;
use frontend\models\SakipSasaran;
use frontend\models\SakipTujuan;
use frontend\models\SakipKoordinasi;
use frontend\models\SakipTujuanrenstra;
use frontend\models\SakipIndikatorsasaranrenstra;
use frontend\models\SakipIndikatorsasaranrenstraTriwulan;
use frontend\models\search\SakipIndikatorsasaranrenstraSearch;
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
 * SakipIndikatorsasaranrenstraController implements the CRUD actions for SakipIndikatorsasaranrenstra model.
 */
class SakipIndikatorsasaranrenstraController extends Controller
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
     * Lists all SakipIndikatorsasaranrenstra models.
     *
     * @return string
     */
    public function actionIndex($refperiode_id = null)
    {
        $searchModel = new SakipIndikatorsasaranrenstraSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
        $dataProvider->pagination = false;

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

        // Ambil data dari sakip_indikatorsasaranrenstra sesuai dengan refskpd_id user dan refperiode_id (jika ada)
        $query = SakipIndikatorsasaranrenstra::find()->where(['refskpd_id' => $refskpd_id]);

        if ($refperiode_id !== null) {
            $query->andWhere(['refperiode_id' => $refperiode_id]);
        }

        // Execute query and get data
        $data = $query->all();

        // Add a flag to check if data is empty
        $dataEmpty = empty($data);

        // Retrieve the periode based on refperiode_id
        $selectedPeriod = SakipPeriode::find()->where(['refperiode_id' => $refperiode_id])->one();
        $selectedPeriodValue = $selectedPeriod ? $selectedPeriod->periode : null; // Get the periode value


        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'nama_skpd' => $nama_skpd,
            'periodeList' => $periodeList,  // Kirim data periode ke view
            'selectedPeriodId' => $refperiode_id, // Add selected period id
            'dataEmpty' => $dataEmpty, // Pass the data empty flag
            'data' => $data, // Send the queried data
            'selectedPeriodValue' => $selectedPeriodValue, // Include selected period value
        ]);
    }

    // public function actionIndexRktDev($refperiode_id = null, $refskpd_id = null)
    // {
    //     $searchModel = new SakipIndikatorsasaranrenstraSearch();
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

    //     // Ambil data dari sakip_indikatorsasaranrenstra sesuai dengan refskpd_id user dan refperiode_id (jika ada)
    //     $query = SakipIndikatorsasaranrenstra::find()->where(['refskpd_id' => $refskpd_id]);

    //     if ($refperiode_id !== null) {
    //         $query->andWhere(['refperiode_id' => $refperiode_id]);
    //     }

    //     // Execute query and get data
    //     $data = $query->all();

    //     // Add a flag to check if data is empty
    //     $dataEmpty = empty($data);

    //     // Ambil daftar SKPD untuk dropdown
    //     $skpdList = ArrayHelper::map(SakipSkpd::find()->all(), 'refskpd_id', 'nama_skpd');

    //     // Retrieve the periode based on refperiode_id
    //     $selectedPeriod = SakipPeriode::find()->where(['refperiode_id' => $refperiode_id])->one();
    //     $selectedPeriodValue = $selectedPeriod ? $selectedPeriod->periode : null; // Get the periode value


    //     return $this->render('index-rkt-dev', [
    //         'searchModel' => $searchModel,
    //         'dataProvider' => $dataProvider,
    //         'nama_skpd' => $nama_skpd,
    //         'periodeList' => $periodeList,  // Kirim data periode ke view
    //         'selectedPeriodId' => $refperiode_id, // Add selected period id
    //         'dataEmpty' => $dataEmpty, // Pass the data empty flag
    //         'data' => $data, // Send the queried data
    //         'selectedPeriodValue' => $selectedPeriodValue, // Include selected period value
    //         'selectedSkpdId' => $refskpd_id,
    //         'skpdList' => $skpdList,
    //     ]);
    // }

    public function actionIndexRktDev($refperiode_id = null, $refskpd_id = null)
    {
        $searchModel = new SakipIndikatorsasaranrenstraSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
        $dataProvider->pagination = false;

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

        // Logika query utama Anda tidak diubah
        $query = SakipIndikatorsasaranrenstra::find()->where(['refskpd_id' => $refskpd_id]);

        if ($refperiode_id !== null) {
            $query->andWhere(['refperiode_id' => $refperiode_id]);
        }

        // Execute query and get data
        $data = $query->all();

        // Add a flag to check if data is empty
        $dataEmpty = empty($data);

        // Retrieve the periode based on refperiode_id
        $selectedPeriod = SakipPeriode::find()->where(['refperiode_id' => $refperiode_id])->one();
        $selectedPeriodValue = $selectedPeriod ? $selectedPeriod->periode : null; // Get the periode value

        return $this->render('index-rkt-dev', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'nama_skpd' => $nama_skpd,
            'periodeList' => $periodeList,  // Kirim data periode ke view
            'selectedPeriodId' => $refperiode_id, // Add selected period id
            'dataEmpty' => $dataEmpty, // Pass the data empty flag
            'data' => $data, // Send the queried data
            'selectedPeriodValue' => $selectedPeriodValue, // Include selected period value
            'selectedSkpdId' => $refskpd_id,
            'skpdList' => $skpdList,
        ]);
    }

    public function actionIndexTahunanPk($refperiode_id = null)
    {
        $searchModel = new SakipIndikatorsasaranrenstraSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
        $dataProvider->pagination = false;

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

        // Ambil data dari sakip_indikatorsasaranrenstra sesuai dengan refskpd_id user dan refperiode_id (jika ada)
        $query = SakipIndikatorsasaranrenstra::find()->where(['refskpd_id' => $refskpd_id]);

        if ($refperiode_id !== null) {
            $query->andWhere(['refperiode_id' => $refperiode_id]);
        }

        // Execute query and get data
        $data = $query->all();

        // Add a flag to check if data is empty
        $dataEmpty = empty($data);

        // Retrieve the periode based on refperiode_id
        $selectedPeriod = SakipPeriode::find()->where(['refperiode_id' => $refperiode_id])->one();
        $selectedPeriodValue = $selectedPeriod ? $selectedPeriod->periode : null; // Get the periode value

        return $this->render('index-tahunan-pk', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'nama_skpd' => $nama_skpd,
            'periodeList' => $periodeList,  // Kirim data periode ke view
            'selectedPeriodId' => $refperiode_id, // Add selected period id
            'dataEmpty' => $dataEmpty, // Pass the data empty flag
            'data' => $data, // Send the queried data
            'selectedPeriodValue' => $selectedPeriodValue, // Include selected period value
        ]);
    }

    // public function actionIndexTahunanPkDev($refperiode_id = null, $refskpd_id = null)
    // {
    //     $searchModel = new SakipIndikatorsasaranrenstraSearch();
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

    //     // Ambil data dari sakip_indikatorsasaranrenstra sesuai dengan refskpd_id user dan refperiode_id (jika ada)
    //     $query = SakipIndikatorsasaranrenstra::find()->where(['refskpd_id' => $refskpd_id]);

    //     if ($refperiode_id !== null) {
    //         $query->andWhere(['refperiode_id' => $refperiode_id]);
    //     }

    //     // Execute query and get data
    //     $data = $query->all();

    //     // Add a flag to check if data is empty
    //     $dataEmpty = empty($data);

    //     // Ambil daftar SKPD untuk dropdown
    //     $skpdList = ArrayHelper::map(SakipSkpd::find()->all(), 'refskpd_id', 'nama_skpd');

    //     // Retrieve the periode based on refperiode_id
    //     $selectedPeriod = SakipPeriode::find()->where(['refperiode_id' => $refperiode_id])->one();
    //     $selectedPeriodValue = $selectedPeriod ? $selectedPeriod->periode : null; // Get the periode value

    //     return $this->render('index-tahunan-pk-dev', [
    //         'searchModel' => $searchModel,
    //         'dataProvider' => $dataProvider,
    //         'nama_skpd' => $nama_skpd,
    //         'periodeList' => $periodeList,  // Kirim data periode ke view
    //         'selectedPeriodId' => $refperiode_id, // Add selected period id
    //         'dataEmpty' => $dataEmpty, // Pass the data empty flag
    //         'data' => $data, // Send the queried data
    //         'selectedPeriodValue' => $selectedPeriodValue, // Include selected period value
    //         'selectedSkpdId' => $refskpd_id,
    //         'skpdList' => $skpdList,
    //     ]);
    // }

    public function actionIndexTahunanPkDev($refperiode_id = null, $refskpd_id = null)
    {
        $searchModel = new SakipIndikatorsasaranrenstraSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
        $dataProvider->pagination = false;

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

        // Logika query utama Anda tidak diubah
        $query = SakipIndikatorsasaranrenstra::find()->where(['refskpd_id' => $refskpd_id]);

        if ($refperiode_id !== null) {
            $query->andWhere(['refperiode_id' => $refperiode_id]);
        }

        // Execute query and get data
        $data = $query->all();

        // Add a flag to check if data is empty
        $dataEmpty = empty($data);

        // Retrieve the periode based on refperiode_id
        $selectedPeriod = SakipPeriode::find()->where(['refperiode_id' => $refperiode_id])->one();
        $selectedPeriodValue = $selectedPeriod ? $selectedPeriod->periode : null; // Get the periode value

        return $this->render('index-tahunan-pk-dev', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'nama_skpd' => $nama_skpd,
            'periodeList' => $periodeList,  // Kirim data periode ke view
            'selectedPeriodId' => $refperiode_id, // Add selected period id
            'dataEmpty' => $dataEmpty, // Pass the data empty flag
            'data' => $data, // Send the queried data
            'selectedPeriodValue' => $selectedPeriodValue, // Include selected period value
            'selectedSkpdId' => $refskpd_id,
            'skpdList' => $skpdList,
        ]);
    }

    public function actionIndexTahunanPkp($refperiode_id = null)
    {
        $searchModel = new SakipIndikatorsasaranrenstraSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
        $dataProvider->pagination = false;

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

        // Ambil data dari sakip_indikatorsasaranrenstra sesuai dengan refskpd_id user dan refperiode_id (jika ada)
        $query = SakipIndikatorsasaranrenstra::find()->where(['refskpd_id' => $refskpd_id]);

        if ($refperiode_id !== null) {
            $query->andWhere(['refperiode_id' => $refperiode_id]);
        }

        // Execute query and get data
        $data = $query->all();

        // Add a flag to check if data is empty
        $dataEmpty = empty($data);

        // Retrieve the periode based on refperiode_id
        $selectedPeriod = SakipPeriode::find()->where(['refperiode_id' => $refperiode_id])->one();
        $selectedPeriodValue = $selectedPeriod ? $selectedPeriod->periode : null; // Get the periode value

        return $this->render('index-tahunan-pkp', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'nama_skpd' => $nama_skpd,
            'periodeList' => $periodeList,  // Kirim data periode ke view
            'selectedPeriodId' => $refperiode_id, // Add selected period id
            'dataEmpty' => $dataEmpty, // Pass the data empty flag
            'data' => $data, // Send the queried data
            'selectedPeriodValue' => $selectedPeriodValue, // Include selected period value
        ]);
    }

    // public function actionIndexTahunanPkpDev($refperiode_id = null, $refskpd_id = null)
    // {
    //     $searchModel = new SakipIndikatorsasaranrenstraSearch();
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

    //     // Ambil data dari sakip_indikatorsasaranrenstra sesuai dengan refskpd_id user dan refperiode_id (jika ada)
    //     $query = SakipIndikatorsasaranrenstra::find()->where(['refskpd_id' => $refskpd_id]);

    //     if ($refperiode_id !== null) {
    //         $query->andWhere(['refperiode_id' => $refperiode_id]);
    //     }

    //     // Execute query and get data
    //     $data = $query->all();

    //     // Add a flag to check if data is empty
    //     $dataEmpty = empty($data);

    //     // Ambil daftar SKPD untuk dropdown
    //     $skpdList = ArrayHelper::map(SakipSkpd::find()->all(), 'refskpd_id', 'nama_skpd');

    //     // Retrieve the periode based on refperiode_id
    //     $selectedPeriod = SakipPeriode::find()->where(['refperiode_id' => $refperiode_id])->one();
    //     $selectedPeriodValue = $selectedPeriod ? $selectedPeriod->periode : null; // Get the periode value

    //     return $this->render('index-tahunan-pkp-dev', [
    //         'searchModel' => $searchModel,
    //         'dataProvider' => $dataProvider,
    //         'nama_skpd' => $nama_skpd,
    //         'periodeList' => $periodeList,  // Kirim data periode ke view
    //         'selectedPeriodId' => $refperiode_id, // Add selected period id
    //         'dataEmpty' => $dataEmpty, // Pass the data empty flag
    //         'data' => $data, // Send the queried data
    //         'selectedPeriodValue' => $selectedPeriodValue, // Include selected period value
    //         'selectedSkpdId' => $refskpd_id,
    //         'skpdList' => $skpdList,
    //     ]);
    // }

    public function actionIndexTahunanPkpDev($refperiode_id = null, $refskpd_id = null)
    {
        $searchModel = new SakipIndikatorsasaranrenstraSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
        $dataProvider->pagination = false;

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

        // Logika query utama Anda tidak diubah
        $query = SakipIndikatorsasaranrenstra::find()->where(['refskpd_id' => $refskpd_id]);

        if ($refperiode_id !== null) {
            $query->andWhere(['refperiode_id' => $refperiode_id]);
        }

        // Execute query and get data
        $data = $query->all();

        // Add a flag to check if data is empty
        $dataEmpty = empty($data);

        // Retrieve the periode based on refperiode_id
        $selectedPeriod = SakipPeriode::find()->where(['refperiode_id' => $refperiode_id])->one();
        $selectedPeriodValue = $selectedPeriod ? $selectedPeriod->periode : null; // Get the periode value

        return $this->render('index-tahunan-pkp-dev', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'nama_skpd' => $nama_skpd,
            'periodeList' => $periodeList,  // Kirim data periode ke view
            'selectedPeriodId' => $refperiode_id, // Add selected period id
            'dataEmpty' => $dataEmpty, // Pass the data empty flag
            'data' => $data, // Send the queried data
            'selectedPeriodValue' => $selectedPeriodValue, // Include selected period value
            'selectedSkpdId' => $refskpd_id,
            'skpdList' => $skpdList,
        ]);
    }

    public function actionIndexTahunanCapaian($refperiode_id = null)
    {
        $searchModel = new SakipIndikatorsasaranrenstraSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
        $dataProvider->pagination = false;

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

        // Ambil data dari sakip_indikatorsasaranrenstra sesuai dengan refskpd_id user dan refperiode_id (jika ada)
        $query = SakipIndikatorsasaranrenstra::find()->where(['refskpd_id' => $refskpd_id]);

        if ($refperiode_id !== null) {
            $query->andWhere(['refperiode_id' => $refperiode_id]);
        }

        // Execute query and get data
        $data = $query->all();

        // Add a flag to check if data is empty
        $dataEmpty = empty($data);

        // Retrieve the periode based on refperiode_id
        $selectedPeriod = SakipPeriode::find()->where(['refperiode_id' => $refperiode_id])->one();
        $selectedPeriodValue = $selectedPeriod ? $selectedPeriod->periode : null; // Get the periode value

        return $this->render('index-tahunan-capaian', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'nama_skpd' => $nama_skpd,
            'periodeList' => $periodeList,  // Kirim data periode ke view
            'selectedPeriodId' => $refperiode_id, // Add selected period id
            'dataEmpty' => $dataEmpty, // Pass the data empty flag
            'data' => $data, // Send the queried data
            'selectedPeriodValue' => $selectedPeriodValue, // Include selected period value
        ]);
    }

    // public function actionIndexTahunanCapaianDev($refperiode_id = null, $refskpd_id = null)
    // {
    //     $searchModel = new SakipIndikatorsasaranrenstraSearch();
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

    //     // Ambil data dari sakip_indikatorsasaranrenstra sesuai dengan refskpd_id user dan refperiode_id (jika ada)
    //     $query = SakipIndikatorsasaranrenstra::find()->where(['refskpd_id' => $refskpd_id]);

    //     if ($refperiode_id !== null) {
    //         $query->andWhere(['refperiode_id' => $refperiode_id]);
    //     }

    //     // Execute query and get data
    //     $data = $query->all();

    //     // Add a flag to check if data is empty
    //     $dataEmpty = empty($data);

    //     // Ambil daftar SKPD untuk dropdown
    //     $skpdList = ArrayHelper::map(SakipSkpd::find()->all(), 'refskpd_id', 'nama_skpd');

    //     // Retrieve the periode based on refperiode_id
    //     $selectedPeriod = SakipPeriode::find()->where(['refperiode_id' => $refperiode_id])->one();
    //     $selectedPeriodValue = $selectedPeriod ? $selectedPeriod->periode : null; // Get the periode value

    //     return $this->render('index-tahunan-capaian-dev', [
    //         'searchModel' => $searchModel,
    //         'dataProvider' => $dataProvider,
    //         'nama_skpd' => $nama_skpd,
    //         'periodeList' => $periodeList,  // Kirim data periode ke view
    //         'selectedPeriodId' => $refperiode_id, // Add selected period id
    //         'dataEmpty' => $dataEmpty, // Pass the data empty flag
    //         'data' => $data, // Send the queried data
    //         'selectedPeriodValue' => $selectedPeriodValue, // Include selected period value
    //         'selectedSkpdId' => $refskpd_id,
    //         'skpdList' => $skpdList,
    //     ]);
    // }

    public function actionIndexTahunanCapaianDev($refperiode_id = null, $refskpd_id = null)
    {
        $searchModel = new SakipIndikatorsasaranrenstraSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
        $dataProvider->pagination = false;

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

        // Logika query utama Anda tidak diubah
        $query = SakipIndikatorsasaranrenstra::find()->where(['refskpd_id' => $refskpd_id]);

        if ($refperiode_id !== null) {
            $query->andWhere(['refperiode_id' => $refperiode_id]);
        }

        // Execute query and get data
        $data = $query->all();

        // Add a flag to check if data is empty
        $dataEmpty = empty($data);

        // Retrieve the periode based on refperiode_id
        $selectedPeriod = SakipPeriode::find()->where(['refperiode_id' => $refperiode_id])->one();
        $selectedPeriodValue = $selectedPeriod ? $selectedPeriod->periode : null; // Get the periode value

        return $this->render('index-tahunan-capaian-dev', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'nama_skpd' => $nama_skpd,
            'periodeList' => $periodeList,  // Kirim data periode ke view
            'selectedPeriodId' => $refperiode_id, // Add selected period id
            'dataEmpty' => $dataEmpty, // Pass the data empty flag
            'data' => $data, // Send the queried data
            'selectedPeriodValue' => $selectedPeriodValue, // Include selected period value
            'selectedSkpdId' => $refskpd_id,
            'skpdList' => $skpdList,
        ]);
    }



    public function actionIndexFormulasi($refperiode_id = null)
    {
        $searchModel = new SakipIndikatorsasaranrenstraSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
        $dataProvider->pagination = false;

        $user = Yii::$app->user->identity;
        $refskpd_id = $user->refskpd_id;

        $nama_skpd = SakipSkpd::find()->select('nama_skpd')->where(['refskpd_id' => $refskpd_id])->scalar();
        $periodeList = SakipPeriode::find()->all();

        if ($refperiode_id === null) {
            $currentYear = date('Y');
            $defaultPeriod = SakipPeriode::find()->where(['periode' => $currentYear])->one();
            $refperiode_id = $defaultPeriod ? $defaultPeriod->refperiode_id : null;
        }

        $query = SakipSasaranrenstra::find()
            ->where(['refskpd_id' => $refskpd_id])
            ->with(['indikators' => function ($q) use ($refperiode_id) {
                $q->andWhere(['refperiode_id' => $refperiode_id]);
            }]);

        if ($refperiode_id !== null) {
            $query->andWhere(['refperiode_id' => $refperiode_id]);
        }

        $data = $query->all();
        $dataEmpty = empty($data);

        // Retrieve the periode based on refperiode_id
        $selectedPeriod = SakipPeriode::find()->where(['refperiode_id' => $refperiode_id])->one();
        $selectedPeriodValue = $selectedPeriod ? $selectedPeriod->periode : null; // Get the periode value

        return $this->render('index-formulasi', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'nama_skpd' => $nama_skpd,
            'periodeList' => $periodeList,
            'selectedPeriodId' => $refperiode_id,
            'dataEmpty' => $dataEmpty,
            'data' => $data,
            'selectedPeriodValue' => $selectedPeriodValue, // Include selected period value
        ]);
    }

    // public function actionIndexFormulasiDev($refperiode_id = null, $refskpd_id = null)
    // {
    //     $searchModel = new SakipIndikatorsasaranrenstraSearch();
    //     $dataProvider = $searchModel->search($this->request->queryParams);
    //     $dataProvider->pagination = false;

    //     // Ambil refskpd_id dari user saat ini jika tidak ada di request
    //     if ($refskpd_id === null) {
    //         $user = Yii::$app->user->identity;
    //         $refskpd_id = $user->refskpd_id;
    //     }

    //     $nama_skpd = SakipSkpd::find()->select('nama_skpd')->where(['refskpd_id' => $refskpd_id])->scalar();
    //     $periodeList = SakipPeriode::find()->all();

    //     if ($refperiode_id === null) {
    //         $currentYear = date('Y');
    //         $defaultPeriod = SakipPeriode::find()->where(['periode' => $currentYear])->one();
    //         $refperiode_id = $defaultPeriod ? $defaultPeriod->refperiode_id : null;
    //     }

    //     $query = SakipSasaranrenstra::find()
    //         ->where(['refskpd_id' => $refskpd_id])
    //         ->with(['indikators' => function ($q) use ($refperiode_id) {
    //             $q->andWhere(['refperiode_id' => $refperiode_id]);
    //         }]);

    //     if ($refperiode_id !== null) {
    //         $query->andWhere(['refperiode_id' => $refperiode_id]);
    //     }

    //     $data = $query->all();
    //     $dataEmpty = empty($data);

    //     // Ambil daftar SKPD untuk dropdown
    //     $skpdList = ArrayHelper::map(SakipSkpd::find()->all(), 'refskpd_id', 'nama_skpd');

    //     // Retrieve the periode based on refperiode_id
    //     $selectedPeriod = SakipPeriode::find()->where(['refperiode_id' => $refperiode_id])->one();
    //     $selectedPeriodValue = $selectedPeriod ? $selectedPeriod->periode : null; // Get the periode value

    //     return $this->render('index-formulasi-dev', [
    //         'searchModel' => $searchModel,
    //         'dataProvider' => $dataProvider,
    //         'nama_skpd' => $nama_skpd,
    //         'periodeList' => $periodeList,
    //         'selectedPeriodId' => $refperiode_id,
    //         'dataEmpty' => $dataEmpty,
    //         'data' => $data,
    //         'selectedPeriodValue' => $selectedPeriodValue, // Include selected period value
    //         'selectedSkpdId' => $refskpd_id,
    //         'skpdList' => $skpdList,
    //     ]);
    // }

    public function actionIndexFormulasiDev($refperiode_id = null, $refskpd_id = null)
    {
        $searchModel = new SakipIndikatorsasaranrenstraSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
        $dataProvider->pagination = false;

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

        $nama_skpd = $refskpd_id ? SakipSkpd::find()->select('nama_skpd')->where(['refskpd_id' => $refskpd_id])->scalar() : 'Tidak ada SKPD dipilih';
        $periodeList = SakipPeriode::find()->all();

        if ($refperiode_id === null) {
            $currentYear = date('Y');
            $defaultPeriod = SakipPeriode::find()->where(['periode' => $currentYear])->one();
            $refperiode_id = $defaultPeriod ? $defaultPeriod->refperiode_id : null;
        }

        // Logika query utama Anda tidak diubah
        $query = SakipSasaranrenstra::find()
            ->where(['refskpd_id' => $refskpd_id])
            ->with(['indikators' => function ($q) use ($refperiode_id) {
                $q->andWhere(['refperiode_id' => $refperiode_id]);
            }]);

        if ($refperiode_id !== null) {
            $query->andWhere(['refperiode_id' => $refperiode_id]);
        }

        $data = $query->all();
        $dataEmpty = empty($data);

        // Retrieve the periode based on refperiode_id
        $selectedPeriod = SakipPeriode::find()->where(['refperiode_id' => $refperiode_id])->one();
        $selectedPeriodValue = $selectedPeriod ? $selectedPeriod->periode : null; // Get the periode value

        return $this->render('index-formulasi-dev', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'nama_skpd' => $nama_skpd,
            'periodeList' => $periodeList,
            'selectedPeriodId' => $refperiode_id,
            'dataEmpty' => $dataEmpty,
            'data' => $data,
            'selectedPeriodValue' => $selectedPeriodValue, // Include selected period value
            'selectedSkpdId' => $refskpd_id,
            'skpdList' => $skpdList,
        ]);
    }

    public function actionUpdateFormulasi($refindikatorsasaranrenstra_id, $refperiode_id = null)
    {
        $model = $this->findModel($refindikatorsasaranrenstra_id);

        if (Yii::$app->request->isAjax) {
            if ($model->load(Yii::$app->request->post())) {
                if ($model->save()) {
                    Yii::$app->session->setFlash('success', 'Berhasil Memperbarui Data');
                    return $this->asJson(['success' => true, 'redirect' => \yii\helpers\Url::to(['index-formulasi', 'refperiode_id' => $model->refperiode_id])]);
                } else {
                    // Tambahkan log error
                    Yii::error("Error saving data: " . json_encode($model->getErrors()));
                    return $this->asJson(['success' => false, 'errors' => $model->getErrors()]);
                }
            }

            return $this->renderAjax('_formformulasi', [
                'model' => $model,
            ]);
        }

        return $this->render('update-formulasi', [
            'model' => $model,
        ]);
    }

    public function actionGetReftujuan($id)
    {
        $model = SakipSasaran::find()->where(['refsasaran_id' => $id])->one();
        if ($model && $model->refTujuan) {
            return $this->asJson([
                'success' => true,
                'reftujuan_id' => $model->refTujuan->reftujuan_id,
                'uraian_tujuan' => $model->refTujuan->uraian_tujuan,
            ]);
        }
        return $this->asJson(['success' => false]);
    }

    public function actionGetRefvisi($id)
    {
        $model = SakipSasaran::find()->where(['refsasaran_id' => $id])->one();
        if ($model && $model->refVisi) {
            return $this->asJson([
                'success' => true,
                'refvisi_id' => $model->refVisi->refvisi_id,
                'uraian_visi' => $model->refVisi->uraian_visi,
            ]);
        }
        return $this->asJson(['success' => false]);
    }


    public function actionGetRefmisi($id)
    {
        $model = SakipSasaran::find()->where(['refsasaran_id' => $id])->one();
        if ($model && $model->refMisi) {
            return $this->asJson([
                'success' => true,
                'refmisi_id' => $model->refMisi->refmisi_id,
                'uraian_misi' => $model->refMisi->uraian_misi,
            ]);
        }
        return $this->asJson(['success' => false]);
    }


    /**
     * Displays a single SakipIndikatorsasaranrenstra model.
     * @param int $refindikatorsasaranrenstra_id Refindikatorsasaranrenstra ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($refindikatorsasaranrenstra_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($refindikatorsasaranrenstra_id),
        ]);
    }

    /**
     * Creates a new SakipIndikatorsasaranrenstra model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate($refperiode_id = null, $refsasaranrenstra_id = null)
    {
        $model = new SakipIndikatorsasaranrenstra();

        // Set the refperiode_id and refsasaranrenstra_id from the GET parameters
        if ($refperiode_id) {
            $model->refperiode_id = $refperiode_id;
        }

        if ($refsasaranrenstra_id) {
            $model->refsasaranrenstra_id = $refsasaranrenstra_id;
        }
        // Ambil refskpd_id dari user saat ini
        $user = Yii::$app->user->identity;
        $refskpd_id = $user->refskpd_id;

        // Ambil data dari sakip_periode
        $periodeList = SakipPeriode::find()->all(); // Query to get all periods

        if (Yii::$app->request->isAjax) {
            if ($model->load(Yii::$app->request->post())) {

                // Konversi koma ke titik pada indikatorsasaranrenstra_target dan target_rkt
                $model->indikatorsasaranrenstra_target = str_replace(',', '.', $model->indikatorsasaranrenstra_target);
                $model->target_rkt = str_replace(',', '.', $model->indikatorsasaranrenstra_target);


                if ($model->save()) {
                    // Loop through each triwulan (1 to 4)
                    for ($i = 1; $i <= 4; $i++) {
                        $triwulanModel = new SakipIndikatorsasaranrenstraTriwulan(); // New model for each triwulan

                        // Set values for the triwulan model from the main model
                        $triwulanModel->refindikatorsasaranrenstra_id = $model->refindikatorsasaranrenstra_id;
                        $triwulanModel->refsasaranrenstra_id = $model->refsasaranrenstra_id;
                        $triwulanModel->refskpd_id = $model->refskpd_id;
                        $triwulanModel->refperiode_id = $model->refperiode_id;
                        // Konversi koma ke titik pada triwulan_target_rkt
                        $triwulanModel->triwulan_target_rkt = str_replace(',', '.', $model->indikatorsasaranrenstra_target);
                        $triwulanModel->reftriwulan_id = $i; // Set triwulan_id to the current loop index

                        // Save each triwulan model
                        if (!$triwulanModel->save()) {
                            // Log error for triwulan
                            Yii::error("Error saving triwulan data for triwulan $i: " . json_encode($triwulanModel->getErrors()));
                            return $this->asJson(['success' => false, 'errors' => array_merge($model->getErrors(), $triwulanModel->getErrors())]);
                        }
                    }

                    Yii::$app->session->setFlash('success', 'Berhasil Tambah Data');
                    return $this->asJson(['success' => true, 'redirect' => \yii\helpers\Url::to(['sakip-sasaranrenstra/index', 'refperiode_id' => $model->refperiode_id, 'active_tab' => 'murni'])]);
                } else {
                    // Log error for the main model
                    Yii::error("Error saving data: " . json_encode($model->getErrors()));
                    return $this->asJson(['success' => false, 'errors' => $model->getErrors()]);
                }
            }

            return $this->renderAjax('_form', [
                'model' => $model,
                'periodeList' => $periodeList,
            ]);
        }

        return $this->render('create', [
            'model' => $model,
            'periodeList' => $periodeList,
        ]);
    }




    /**
     * Updates an existing Sakipvisi model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $refindikatorsasaranrenstra_id Refvisi ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($refindikatorsasaranrenstra_id, $refperiode_id = null)
    {
        $model = $this->findModel($refindikatorsasaranrenstra_id);

        if (Yii::$app->request->isAjax) {
            if ($model->load(Yii::$app->request->post())) {

                // Konversi koma ke titik pada indikatorsasaranrenstra_target dan target_rkt
                $model->indikatorsasaranrenstra_target = str_replace(',', '.', $model->indikatorsasaranrenstra_target);
                $model->target_rkt = str_replace(',', '.', $model->indikatorsasaranrenstra_target);

                // Mengatur target_rkt sesuai dengan indikatorsasaranrenstra_target yang sudah dikonversi
                $model->target_rkt = $model->indikatorsasaranrenstra_target;

                if ($model->save()) {
                    // Setelah data utama berhasil disimpan, kita update data triwulan
                    for ($i = 1; $i <= 4; $i++) {
                        // Cari model triwulan yang sesuai
                        $triwulanModel = SakipIndikatorsasaranrenstraTriwulan::findOne([
                            'refindikatorsasaranrenstra_id' => $model->refindikatorsasaranrenstra_id,
                            'reftriwulan_id' => $i
                        ]);

                        if ($triwulanModel !== null) {
                            // Set nilai triwulan_target_rkt sesuai dengan indikatorsasaranrenstra_target yang baru
                            // Konversi koma ke titik pada triwulan_target_rkt
                            $triwulanModel->triwulan_target_rkt = str_replace(',', '.', $model->indikatorsasaranrenstra_target);


                            // Simpan triwulan model
                            if (!$triwulanModel->save()) {
                                // Jika terjadi kesalahan pada penyimpanan triwulan, log error
                                Yii::error("Error updating triwulan $i: " . json_encode($triwulanModel->getErrors()));
                                return $this->asJson(['success' => false, 'errors' => $triwulanModel->getErrors()]);
                            }
                        }
                    }

                    Yii::$app->session->setFlash('success', 'Berhasil Memperbarui Data');
                    $referrer = Yii::$app->request->referrer;
                    $redirectUrl = \yii\helpers\Url::to(['sakip-sasaranrenstra/index', 'refperiode_id' => $model->refperiode_id, 'active_tab' => 'murni']);
                    if (strpos($referrer, 'index-dev') !== false) {
                        $redirectUrl = \yii\helpers\Url::to(['sakip-sasaranrenstra/index-dev', 'refperiode_id' => $model->refperiode_id, 'refskpd_id' => $model->refskpd_id]);
                    }
                    return $this->asJson(['success' => true, 'redirect' => $redirectUrl]);
                } else {
                    // Log error untuk model utama
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


    public function actionUpdateRkt($refindikatorsasaranrenstra_id, $refperiode_id = null)
    {
        $model = $this->findModel($refindikatorsasaranrenstra_id);

        if (Yii::$app->request->isAjax) {
            if ($model->load(Yii::$app->request->post())) {

                // Konversi koma ke titik pada target_rkt
                $model->target_rkt = str_replace(',', '.', $model->target_rkt);
                $model->target_pk = str_replace(',', '.', $model->target_rkt);

                $model->target_pk = $model->target_rkt;

                // Simpan model utama yang memuat target_rkt baru
                if ($model->save()) {

                    // Update triwulan berdasarkan target_rkt yang baru
                    for ($i = 1; $i <= 4; $i++) {
                        // Cari model triwulan yang sesuai dengan reftriwulan_id
                        $triwulanModel = SakipIndikatorsasaranrenstraTriwulan::findOne([
                            'refindikatorsasaranrenstra_id' => $model->refindikatorsasaranrenstra_id,
                            'reftriwulan_id' => $i
                        ]);

                        if ($triwulanModel !== null) {
                            // Set nilai triwulan_target_rkt sesuai dengan target_rkt yang baru
                            // Set nilai triwulan_target_rkt sesuai dengan target_rkt yang baru, dan konversi jika perlu
                            $triwulanModel->triwulan_target_rkt = str_replace(',', '.', $model->target_rkt);

                            // Simpan perubahan di model triwulan
                            if (!$triwulanModel->save()) {
                                // Jika ada error saat menyimpan triwulan, log error
                                Yii::error("Error updating triwulan $i: " . json_encode($triwulanModel->getErrors()));
                                return $this->asJson(['success' => false, 'errors' => $triwulanModel->getErrors()]);
                            }
                        }
                    }

                    // Berikan feedback ke user bahwa data berhasil diperbarui
                    Yii::$app->session->setFlash('success', 'Berhasil Memperbarui Data');
                    $referrer = Yii::$app->request->referrer;
                    $redirectUrl = \yii\helpers\Url::to(['sakip-indikatorsasaranrenstra/index', 'refperiode_id' => $model->refperiode_id]);
                    if (strpos($referrer, 'index-rkt-dev') !== false) {
                        $redirectUrl = \yii\helpers\Url::to(['sakip-indikatorsasaranrenstra/index-rkt-dev', 'refperiode_id' => $model->refperiode_id, 'refskpd_id' => $model->refskpd_id]);
                    }
                    return $this->asJson(['success' => true, 'redirect' => $redirectUrl]);
                } else {
                    // Log error jika model utama gagal disimpan
                    Yii::error("Error saving data: " . json_encode($model->getErrors()));
                    return $this->asJson(['success' => false, 'errors' => $model->getErrors()]);
                }
            }

            return $this->renderAjax('_formupdaterkt', [
                'model' => $model,
            ]);
        }

        return $this->render('update-rkt', [
            'model' => $model,
        ]);
    }

    public function actionUpdateTargetPk($refindikatorsasaranrenstra_id, $refperiode_id = null)
    {
        $model = $this->findModel($refindikatorsasaranrenstra_id);

        if (Yii::$app->request->isAjax) {
            if ($model->load(Yii::$app->request->post())) {

                // Konversi koma ke titik pada target_pk
                $model->target_pk = str_replace(',', '.', $model->target_pk);

                // Simpan model utama yang memuat target_rkt baru
                if ($model->save()) {
                    // Berikan feedback ke user bahwa data berhasil diperbarui
                    Yii::$app->session->setFlash('success', 'Berhasil Memperbarui Data');
                    $referrer = Yii::$app->request->referrer;
                    $redirectUrl = \yii\helpers\Url::to(['sakip-indikatorsasaranrenstra/index-tahunan-pk', 'refperiode_id' => $model->refperiode_id]);
                    if (strpos($referrer, 'index-tahunan-pk-dev') !== false) {
                        $redirectUrl = \yii\helpers\Url::to(['sakip-indikatorsasaranrenstra/index-tahunan-pk-dev', 'refperiode_id' => $model->refperiode_id, 'refskpd_id' => $model->refskpd_id]);
                    }
                    return $this->asJson(['success' => true, 'redirect' => $redirectUrl]);
                } else {
                    // Log error jika model utama gagal disimpan
                    Yii::error("Error saving data: " . json_encode($model->getErrors()));
                    return $this->asJson(['success' => false, 'errors' => $model->getErrors()]);
                }
            }

            return $this->renderAjax('_formupdatetargetpk', [
                'model' => $model,
            ]);
        }

        return $this->render('update-target-pk', [
            'model' => $model,
        ]);
    }

    public function actionUpdateTargetPkp($refindikatorsasaranrenstra_id, $refperiode_id = null)
    {
        $model = $this->findModel($refindikatorsasaranrenstra_id);

        if (Yii::$app->request->isAjax) {
            if ($model->load(Yii::$app->request->post())) {
                // Konversi koma ke titik pada target_pk_p
                $model->target_pk_p = str_replace(',', '.', $model->target_pk_p);

                // Simpan model utama yang memuat target_rkt baru
                if ($model->save()) {
                    // Berikan feedback ke user bahwa data berhasil diperbarui
                    Yii::$app->session->setFlash('success', 'Berhasil Memperbarui Data');
                    $referrer = Yii::$app->request->referrer;
                    $redirectUrl = \yii\helpers\Url::to(['sakip-indikatorsasaranrenstra/index-tahunan-pkp', 'refperiode_id' => $model->refperiode_id]);
                    if (strpos($referrer, 'index-tahunan-pkp-dev') !== false) {
                        $redirectUrl = \yii\helpers\Url::to([
                            'sakip-indikatorsasaranrenstra/index-tahunan-pkp-dev',
                            'refperiode_id' => $model->refperiode_id,
                            'refskpd_id' => $model->refskpd_id
                        ]);
                    }
                    return $this->asJson([
                        'success' => true,
                        'redirect' => $redirectUrl
                    ]);
                } else {
                    // Log error jika model utama gagal disimpan
                    Yii::error("Error saving data: " . json_encode($model->getErrors()));
                    return $this->asJson(['success' => false, 'errors' => $model->getErrors()]);
                }
            }

            return $this->renderAjax('_formupdatetargetpkp', [
                'model' => $model,
            ]);
        }

        return $this->render('update-target-pkp', [
            'model' => $model,
        ]);
    }

    public function actionUpdateTargetCapaian($refindikatorsasaranrenstra_id, $refperiode_id = null)
    {
        $model = $this->findModel($refindikatorsasaranrenstra_id);

        if (Yii::$app->request->isAjax) {
            if ($model->load(Yii::$app->request->post())) {

                // Konversi koma ke titik pada realisasi dan capaian
                $model->realisasi = str_replace(',', '.', $model->realisasi);
                $model->capaian = str_replace(',', '.', $model->capaian);

                // Simpan model utama yang memuat target_rkt baru
                if ($model->save()) {
                    Yii::$app->session->setFlash('success', 'Berhasil Memperbarui Data');
                    $referrer = Yii::$app->request->referrer;
                    $redirectUrl = \yii\helpers\Url::to([
                        'sakip-indikatorsasaranrenstra/index-tahunan-capaian',
                        'refperiode_id' => $model->refperiode_id
                    ]);
                    if (strpos($referrer, 'index-tahunan-capaian-dev') !== false) {
                        $redirectUrl = \yii\helpers\Url::to([
                            'sakip-indikatorsasaranrenstra/index-tahunan-capaian-dev',
                            'refperiode_id' => $model->refperiode_id,
                            'refskpd_id' => $model->refskpd_id
                        ]);
                    }
                    return $this->asJson([
                        'success' => true,
                        'redirect' => $redirectUrl
                    ]);
                } else {
                    // Log error jika model utama gagal disimpan
                    Yii::error("Error saving data: " . json_encode($model->getErrors()));
                    return $this->asJson(['success' => false, 'errors' => $model->getErrors()]);
                }
            }

            return $this->renderAjax('_formupdatetargetcapaian', [
                'model' => $model,
            ]);
        }

        return $this->render('update-target-capaian', [
            'model' => $model,
        ]);
    }


    public function actionGetRefperiode($id)
    {
        $model = SakipSasaranrenstra::find()->where(['refsasaranrenstra_id' => $id])->one();
        if ($model && $model->refPeriode) {
            return $this->asJson([
                'success' => true,
                'refperiode_id' => $model->refPeriode->refperiode_id,
                'periode' => $model->refPeriode->periode,
            ]);
        }
        return $this->asJson(['success' => false]);
    }




    /**
     * Deletes an existing SakipIndikatorsasaranrenstra model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $refindikatorsasaranrenstra_id Refindikatorsasaranrenstra ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($refindikatorsasaranrenstra_id)
    {
        // Hapus data di sakip_indikatorsasaranrenstra
        $model = $this->findModel($refindikatorsasaranrenstra_id);
        // Hapus semua data di sakip_indikatorsasaranrenstra_triwulan yang memiliki refindikatorsasaranrenstra_id sama
        SakipIndikatorsasaranrenstraTriwulan::deleteAll(['refindikatorsasaranrenstra_id' => $refindikatorsasaranrenstra_id]);

        // Ambil refperiode_id sebelum menghapus model
        $refperiode_id = $model->refperiode_id;

        $model->delete();

        // Redirect ke halaman index
        return $this->redirect(['sakip-sasaranrenstra/index', 'refperiode_id' => $refperiode_id]);
    }

    /**
     * Finds the SakipIndikatorsasaranrenstra model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $refindikatorsasaranrenstra_id Refindikatorsasaranrenstra ID
     * @return SakipIndikatorsasaranrenstra the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($refindikatorsasaranrenstra_id)
    {
        if (($model = SakipIndikatorsasaranrenstra::findOne(['refindikatorsasaranrenstra_id' => $refindikatorsasaranrenstra_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
