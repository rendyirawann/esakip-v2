<?php

namespace frontend\controllers;

use Yii;
use frontend\models\SakipIndikatorcascadingkegiatanTriwulan;
use frontend\models\search\SakipIndikatorcascadingkegiatanTriwulanSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use frontend\models\SakipIndikatorcascadingkegiatan;
use frontend\models\search\SakipIndikatorcascadingkegiatanSearch;
use frontend\models\SakipIndikatorcascadingprogram;
use frontend\models\SakipIndikatorcascadingprogramTriwulan;
use frontend\models\search\SakipIndikatorcascadingprogramSearch;
use frontend\models\SakipSasaranrenstra;
use frontend\models\SakipSkpd;
use frontend\models\SakipPeriode;
use frontend\models\SakipSasaran;
use frontend\models\SakipTujuan;
use frontend\models\SakipTujuanrenstra;
use frontend\models\SakipIndikatorsasaranrenstra;
use frontend\models\SakipIndikatorsasaranrenstraTriwulan;
use frontend\models\search\SakipIndikatorsasaranrenstraSearch;
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
use frontend\models\SakipKoordinasi;

/**
 * SakipIndikatorcascadingkegiatanTriwulanController implements the CRUD actions for SakipIndikatorcascadingkegiatanTriwulan model.
 */
class SakipIndikatorcascadingkegiatanTriwulanController extends Controller
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
     * Lists all SakipIndikatorcascadingkegiatanTriwulan models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new SakipIndikatorcascadingkegiatanTriwulanSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionIndexTriwulanPk($refperiode_id = null, $reftriwulan_id = null)
    {
        $searchModel = new SakipIndikatorcascadingkegiatanTriwulanSearch();
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

        // Set default triwulan to 1 if not provided
        if ($reftriwulan_id === null) {
            $reftriwulan_id = 1;
        }

        // Ambil data dari sakip_indikatorsasaranrenstra sesuai dengan refskpd_id user dan refperiode_id (jika ada)
        $query = SakipIndikatorcascadingkegiatanTriwulan::find()->where(['refskpd_id' => $refskpd_id]);

        if ($refperiode_id !== null) {
            $query->andWhere(['refperiode_id' => $refperiode_id]);
        }

        // Add condition to filter by reftriwulan_id
        if ($reftriwulan_id !== null) {
            $query->andWhere(['reftriwulan_id' => $reftriwulan_id]);
        }

        // Execute query and get data
        $data = $query->all();

        // Add a flag to check if data is empty
        $dataEmpty = empty($data);

        // Retrieve the periode based on refperiode_id
        $selectedPeriod = SakipPeriode::find()->where(['refperiode_id' => $refperiode_id])->one();
        $selectedPeriodValue = $selectedPeriod ? $selectedPeriod->periode : null; // Get the periode value

        return $this->render('index-triwulan-pk', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'nama_skpd' => $nama_skpd,
            'periodeList' => $periodeList,  // Kirim data periode ke view
            'selectedPeriodId' => $refperiode_id, // Add selected period id
            'selectedTriwulanId' => $reftriwulan_id, // Include selected triwulan id
            'selectedPeriodValue' => $selectedPeriodValue, // Include selected period value
            'dataEmpty' => $dataEmpty, // Pass the data empty flag
            'data' => $data, // Send the queried data
        ]);
    }

    // public function actionIndexTriwulanPkDev($refperiode_id = null, $reftriwulan_id = null, $refskpd_id = null)
    // {
    //     $searchModel = new SakipIndikatorcascadingkegiatanTriwulanSearch();
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

    //     // Set default triwulan to 1 if not provided
    //     if ($reftriwulan_id === null) {
    //         $reftriwulan_id = 1;
    //     }

    //     // Ambil data dari sakip_indikatorsasaranrenstra sesuai dengan refskpd_id user dan refperiode_id (jika ada)
    //     $query = SakipIndikatorcascadingkegiatanTriwulan::find()->where(['refskpd_id' => $refskpd_id]);

    //     if ($refperiode_id !== null) {
    //         $query->andWhere(['refperiode_id' => $refperiode_id]);
    //     }

    //     // Add condition to filter by reftriwulan_id
    //     if ($reftriwulan_id !== null) {
    //         $query->andWhere(['reftriwulan_id' => $reftriwulan_id]);
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

    //     return $this->render('index-triwulan-pk-dev', [
    //         'searchModel' => $searchModel,
    //         'dataProvider' => $dataProvider,
    //         'nama_skpd' => $nama_skpd,
    //         'periodeList' => $periodeList,  // Kirim data periode ke view
    //         'selectedPeriodId' => $refperiode_id, // Add selected period id
    //         'selectedTriwulanId' => $reftriwulan_id, // Include selected triwulan id
    //         'selectedPeriodValue' => $selectedPeriodValue, // Include selected period value
    //         'dataEmpty' => $dataEmpty, // Pass the data empty flag
    //         'data' => $data, // Send the queried data
    //         'selectedSkpdId' => $refskpd_id,
    //         'skpdList' => $skpdList,
    //     ]);
    // }

    public function actionIndexTriwulanPkDev($refperiode_id = null, $reftriwulan_id = null, $refskpd_id = null)
    {
        $searchModel = new SakipIndikatorcascadingkegiatanTriwulanSearch();
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

        // Set default triwulan to 1 if not provided
        if ($reftriwulan_id === null) {
            $reftriwulan_id = 1;
        }

        // Logika query utama Anda tidak diubah
        $query = SakipIndikatorcascadingkegiatanTriwulan::find()->where(['refskpd_id' => $refskpd_id]);

        if ($refperiode_id !== null) {
            $query->andWhere(['refperiode_id' => $refperiode_id]);
        }

        // Add condition to filter by reftriwulan_id
        if ($reftriwulan_id !== null) {
            $query->andWhere(['reftriwulan_id' => $reftriwulan_id]);
        }

        // Execute query and get data
        $data = $query->all();

        // Add a flag to check if data is empty
        $dataEmpty = empty($data);

        // Retrieve the periode based on refperiode_id
        $selectedPeriod = SakipPeriode::find()->where(['refperiode_id' => $refperiode_id])->one();
        $selectedPeriodValue = $selectedPeriod ? $selectedPeriod->periode : null; // Get the periode value

        return $this->render('index-triwulan-pk-dev', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'nama_skpd' => $nama_skpd,
            'periodeList' => $periodeList,  // Kirim data periode ke view
            'selectedPeriodId' => $refperiode_id, // Add selected period id
            'selectedTriwulanId' => $reftriwulan_id, // Include selected triwulan id
            'selectedPeriodValue' => $selectedPeriodValue, // Include selected period value
            'dataEmpty' => $dataEmpty, // Pass the data empty flag
            'data' => $data, // Send the queried data
            'selectedSkpdId' => $refskpd_id,
            'skpdList' => $skpdList,
        ]);
    }

    public function actionIndexTriwulanPkp($refperiode_id = null, $reftriwulan_id = null)
    {
        $searchModel = new SakipIndikatorcascadingkegiatanTriwulanSearch();
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

        // Set default triwulan to 1 if not provided
        if ($reftriwulan_id === null) {
            $reftriwulan_id = 1;
        }

        // Ambil data dari sakip_indikatorsasaranrenstra sesuai dengan refskpd_id user dan refperiode_id (jika ada)
        $query = SakipIndikatorcascadingkegiatanTriwulan::find()->where(['refskpd_id' => $refskpd_id]);

        if ($refperiode_id !== null) {
            $query->andWhere(['refperiode_id' => $refperiode_id]);
        }

        // Add condition to filter by reftriwulan_id
        if ($reftriwulan_id !== null) {
            $query->andWhere(['reftriwulan_id' => $reftriwulan_id]);
        }

        // Execute query and get data
        $data = $query->all();

        // Add a flag to check if data is empty
        $dataEmpty = empty($data);

        // Retrieve the periode based on refperiode_id
        $selectedPeriod = SakipPeriode::find()->where(['refperiode_id' => $refperiode_id])->one();
        $selectedPeriodValue = $selectedPeriod ? $selectedPeriod->periode : null; // Get the periode value

        return $this->render('index-triwulan-pkp', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'nama_skpd' => $nama_skpd,
            'periodeList' => $periodeList,  // Kirim data periode ke view
            'selectedPeriodId' => $refperiode_id, // Add selected period id
            'selectedTriwulanId' => $reftriwulan_id, // Include selected triwulan id
            'selectedPeriodValue' => $selectedPeriodValue, // Include selected period value
            'dataEmpty' => $dataEmpty, // Pass the data empty flag
            'data' => $data, // Send the queried data
        ]);
    }

    // public function actionIndexTriwulanPkpDev($refperiode_id = null, $reftriwulan_id = null, $refskpd_id = null)
    // {
    //     $searchModel = new SakipIndikatorcascadingkegiatanTriwulanSearch();
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

    //     // Set default triwulan to 1 if not provided
    //     if ($reftriwulan_id === null) {
    //         $reftriwulan_id = 1;
    //     }

    //     // Ambil data dari sakip_indikatorsasaranrenstra sesuai dengan refskpd_id user dan refperiode_id (jika ada)
    //     $query = SakipIndikatorcascadingkegiatanTriwulan::find()->where(['refskpd_id' => $refskpd_id]);

    //     if ($refperiode_id !== null) {
    //         $query->andWhere(['refperiode_id' => $refperiode_id]);
    //     }

    //     // Add condition to filter by reftriwulan_id
    //     if ($reftriwulan_id !== null) {
    //         $query->andWhere(['reftriwulan_id' => $reftriwulan_id]);
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

    //     return $this->render('index-triwulan-pkp-dev', [
    //         'searchModel' => $searchModel,
    //         'dataProvider' => $dataProvider,
    //         'nama_skpd' => $nama_skpd,
    //         'periodeList' => $periodeList,  // Kirim data periode ke view
    //         'selectedPeriodId' => $refperiode_id, // Add selected period id
    //         'selectedTriwulanId' => $reftriwulan_id, // Include selected triwulan id
    //         'selectedPeriodValue' => $selectedPeriodValue, // Include selected period value
    //         'dataEmpty' => $dataEmpty, // Pass the data empty flag
    //         'data' => $data, // Send the queried data
    //         'selectedSkpdId' => $refskpd_id,
    //         'skpdList' => $skpdList,
    //     ]);
    // }

    public function actionIndexTriwulanPkpDev($refperiode_id = null, $reftriwulan_id = null, $refskpd_id = null)
    {
        $searchModel = new SakipIndikatorcascadingkegiatanTriwulanSearch();
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

        // Set default triwulan to 1 if not provided
        if ($reftriwulan_id === null) {
            $reftriwulan_id = 1;
        }

        // Logika query utama Anda tidak diubah
        $query = SakipIndikatorcascadingkegiatanTriwulan::find()->where(['refskpd_id' => $refskpd_id]);

        if ($refperiode_id !== null) {
            $query->andWhere(['refperiode_id' => $refperiode_id]);
        }

        // Add condition to filter by reftriwulan_id
        if ($reftriwulan_id !== null) {
            $query->andWhere(['reftriwulan_id' => $reftriwulan_id]);
        }

        // Execute query and get data
        $data = $query->all();

        // Add a flag to check if data is empty
        $dataEmpty = empty($data);

        // Retrieve the periode based on refperiode_id
        $selectedPeriod = SakipPeriode::find()->where(['refperiode_id' => $refperiode_id])->one();
        $selectedPeriodValue = $selectedPeriod ? $selectedPeriod->periode : null; // Get the periode value

        return $this->render('index-triwulan-pkp-dev', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'nama_skpd' => $nama_skpd,
            'periodeList' => $periodeList,  // Kirim data periode ke view
            'selectedPeriodId' => $refperiode_id, // Add selected period id
            'selectedTriwulanId' => $reftriwulan_id, // Include selected triwulan id
            'selectedPeriodValue' => $selectedPeriodValue, // Include selected period value
            'dataEmpty' => $dataEmpty, // Pass the data empty flag
            'data' => $data, // Send the queried data
            'selectedSkpdId' => $refskpd_id,
            'skpdList' => $skpdList,
        ]);
    }

    public function actionIndexTriwulanCapaian($refperiode_id = null, $reftriwulan_id = null)
    {
        $searchModel = new SakipIndikatorcascadingkegiatanTriwulanSearch();
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

        // Set default triwulan to 1 if not provided
        if ($reftriwulan_id === null) {
            $reftriwulan_id = 1;
        }

        // Ambil data dari sakip_indikatorsasaranrenstra sesuai dengan refskpd_id user dan refperiode_id (jika ada)
        $query = SakipIndikatorcascadingkegiatanTriwulan::find()->where(['refskpd_id' => $refskpd_id]);

        if ($refperiode_id !== null) {
            $query->andWhere(['refperiode_id' => $refperiode_id]);
        }

        // Add condition to filter by reftriwulan_id
        if ($reftriwulan_id !== null) {
            $query->andWhere(['reftriwulan_id' => $reftriwulan_id]);
        }

        // Execute query and get data
        $data = $query->all();

        // Add a flag to check if data is empty
        $dataEmpty = empty($data);

        // Retrieve the periode based on refperiode_id
        $selectedPeriod = SakipPeriode::find()->where(['refperiode_id' => $refperiode_id])->one();
        $selectedPeriodValue = $selectedPeriod ? $selectedPeriod->periode : null; // Get the periode value

        return $this->render('index-triwulan-capaian', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'nama_skpd' => $nama_skpd,
            'periodeList' => $periodeList,  // Kirim data periode ke view
            'selectedPeriodId' => $refperiode_id, // Add selected period id
            'selectedTriwulanId' => $reftriwulan_id, // Include selected triwulan id
            'selectedPeriodValue' => $selectedPeriodValue, // Include selected period value
            'dataEmpty' => $dataEmpty, // Pass the data empty flag
            'data' => $data, // Send the queried data
        ]);
    }

    // public function actionIndexTriwulanCapaianDev($refperiode_id = null, $reftriwulan_id = null, $refskpd_id = null)
    // {
    //     $searchModel = new SakipIndikatorcascadingkegiatanTriwulanSearch();
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

    //     // Set default triwulan to 1 if not provided
    //     if ($reftriwulan_id === null) {
    //         $reftriwulan_id = 1;
    //     }

    //     // Ambil data dari sakip_indikatorsasaranrenstra sesuai dengan refskpd_id user dan refperiode_id (jika ada)
    //     $query = SakipIndikatorcascadingkegiatanTriwulan::find()->where(['refskpd_id' => $refskpd_id]);

    //     if ($refperiode_id !== null) {
    //         $query->andWhere(['refperiode_id' => $refperiode_id]);
    //     }

    //     // Add condition to filter by reftriwulan_id
    //     if ($reftriwulan_id !== null) {
    //         $query->andWhere(['reftriwulan_id' => $reftriwulan_id]);
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

    //     return $this->render('index-triwulan-capaian-dev', [
    //         'searchModel' => $searchModel,
    //         'dataProvider' => $dataProvider,
    //         'nama_skpd' => $nama_skpd,
    //         'periodeList' => $periodeList,  // Kirim data periode ke view
    //         'selectedPeriodId' => $refperiode_id, // Add selected period id
    //         'selectedTriwulanId' => $reftriwulan_id, // Include selected triwulan id
    //         'selectedPeriodValue' => $selectedPeriodValue, // Include selected period value
    //         'dataEmpty' => $dataEmpty, // Pass the data empty flag
    //         'data' => $data, // Send the queried data
    //         'selectedSkpdId' => $refskpd_id,
    //         'skpdList' => $skpdList,
    //     ]);
    // }

    public function actionIndexTriwulanCapaianDev($refperiode_id = null, $reftriwulan_id = null, $refskpd_id = null)
    {
        $searchModel = new SakipIndikatorcascadingkegiatanTriwulanSearch();
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

        // Set default triwulan to 1 if not provided
        if ($reftriwulan_id === null) {
            $reftriwulan_id = 1;
        }

        // Logika query utama Anda tidak diubah
        $query = SakipIndikatorcascadingkegiatanTriwulan::find()->where(['refskpd_id' => $refskpd_id]);

        if ($refperiode_id !== null) {
            $query->andWhere(['refperiode_id' => $refperiode_id]);
        }

        // Add condition to filter by reftriwulan_id
        if ($reftriwulan_id !== null) {
            $query->andWhere(['reftriwulan_id' => $reftriwulan_id]);
        }

        // Execute query and get data
        $data = $query->all();

        // Check if data is empty
        $dataEmpty = empty($data);

        // Retrieve the periode based on refperiode_id
        $selectedPeriod = SakipPeriode::find()->where(['refperiode_id' => $refperiode_id])->one();
        $selectedPeriodValue = $selectedPeriod ? $selectedPeriod->periode : null; // Get the periode value

        return $this->render('index-triwulan-capaian-dev', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'nama_skpd' => $nama_skpd,
            'periodeList' => $periodeList,  // Kirim data periode ke view
            'selectedPeriodId' => $refperiode_id, // Add selected period id
            'selectedTriwulanId' => $reftriwulan_id, // Include selected triwulan id
            'selectedPeriodValue' => $selectedPeriodValue, // Include selected period value
            'dataEmpty' => $dataEmpty, // Pass the data empty flag
            'data' => $data, // Send the queried data
            'selectedSkpdId' => $refskpd_id,
            'skpdList' => $skpdList,
        ]);
    }


    /**
     * Displays a single SakipIndikatorcascadingkegiatanTriwulan model.
     * @param int $refindikatorkegiatantriwulan_id Refindikatorkegiatantriwulan ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($refindikatorkegiatantriwulan_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($refindikatorkegiatantriwulan_id),
        ]);
    }

    /**
     * Creates a new SakipIndikatorcascadingkegiatanTriwulan model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new SakipIndikatorcascadingkegiatanTriwulan();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'refindikatorkegiatantriwulan_id' => $model->refindikatorkegiatantriwulan_id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing SakipIndikatorcascadingkegiatanTriwulan model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $refindikatorkegiatantriwulan_id Refindikatorkegiatantriwulan ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($refindikatorkegiatantriwulan_id)
    {
        $model = $this->findModel($refindikatorkegiatantriwulan_id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'refindikatorkegiatantriwulan_id' => $model->refindikatorkegiatantriwulan_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionUpdateTriwulanPk($refindikatorkegiatantriwulan_id, $reftriwulan_id = null, $refperiode_id = null)
    {
        $model = $this->findModel($refindikatorkegiatantriwulan_id);

        if (Yii::$app->request->isAjax) {
            if ($model->load(Yii::$app->request->post())) {
                // Konversi koma ke titik untuk target_pk jika ada koma
                $model->triwulan_target_pk = str_replace(',', '.', $model->triwulan_target_pk);
                // Save model with updated target_rkt
                if ($model->save()) {
                    Yii::$app->session->setFlash('success', 'Berhasil Memperbarui Data');
                    $referrer = Yii::$app->request->referrer;
                    $redirectUrl = \yii\helpers\Url::to([
                        'sakip-indikatorcascadingkegiatan-triwulan/index-triwulan-pk',
                        'refperiode_id' => $model->refperiode_id,
                        'reftriwulan_id' => $reftriwulan_id
                    ]);
                    if (strpos($referrer, 'index-triwulan-pk-dev') !== false) {
                        $redirectUrl = \yii\helpers\Url::to([
                            'sakip-indikatorcascadingkegiatan-triwulan/index-triwulan-pk-dev',
                            'refperiode_id' => $model->refperiode_id,
                            'reftriwulan_id' => $reftriwulan_id,
                            'refskpd_id' => $model->refskpd_id
                        ]);
                    }
                    return $this->asJson([
                        'success' => true,
                        'redirect' => $redirectUrl
                    ]);
                } else {
                    Yii::error("Error saving data: " . json_encode($model->getErrors()));
                    return $this->asJson(['success' => false, 'errors' => $model->getErrors()]);
                }
            }

            return $this->renderAjax('_formupdatetriwulanpk', [
                'model' => $model,
                'reftriwulan_id' => $reftriwulan_id, // Pass it to the view
            ]);
        }

        return $this->render('update-triwulan-pk', [
            'model' => $model,
        ]);
    }

    public function actionUpdateTriwulanPkp($refindikatorkegiatantriwulan_id, $reftriwulan_id = null, $refperiode_id = null)
    {
        $model = $this->findModel($refindikatorkegiatantriwulan_id);

        if (Yii::$app->request->isAjax) {
            if ($model->load(Yii::$app->request->post())) {
                // Konversi koma ke titik untuk target_pk jika ada koma
                $model->triwulan_target_pk_p = str_replace(',', '.', $model->triwulan_target_pk_p);
                // Save model with updated target_rkt
                if ($model->save()) {
                    Yii::$app->session->setFlash('success', 'Berhasil Memperbarui Data');
                    $referrer = Yii::$app->request->referrer;
                    $redirectUrl = \yii\helpers\Url::to([
                        'sakip-indikatorcascadingkegiatan-triwulan/index-triwulan-pkp',
                        'refperiode_id' => $model->refperiode_id,
                        'reftriwulan_id' => $reftriwulan_id
                    ]);
                    if (strpos($referrer, 'index-triwulan-pkp-dev') !== false) {
                        $redirectUrl = \yii\helpers\Url::to([
                            'sakip-indikatorcascadingkegiatan-triwulan/index-triwulan-pkp-dev',
                            'refperiode_id' => $model->refperiode_id,
                            'reftriwulan_id' => $reftriwulan_id,
                            'refskpd_id' => $model->refskpd_id
                        ]);
                    }
                    return $this->asJson([
                        'success' => true,
                        'redirect' => $redirectUrl
                    ]);
                } else {
                    Yii::error("Error saving data: " . json_encode($model->getErrors()));
                    return $this->asJson(['success' => false, 'errors' => $model->getErrors()]);
                }
            }

            return $this->renderAjax('_formupdatetriwulanpkp', [
                'model' => $model,
                'reftriwulan_id' => $reftriwulan_id, // Pass it to the view
            ]);
        }

        return $this->render('update-triwulan-pkp', [
            'model' => $model,
        ]);
    }

    public function actionUpdateTriwulanCapaian($refindikatorkegiatantriwulan_id, $reftriwulan_id = null, $refperiode_id = null)
    {
        $model = $this->findModel($refindikatorkegiatantriwulan_id);

        if (Yii::$app->request->isAjax) {
            if ($model->load(Yii::$app->request->post())) {

                // Konversi koma ke titik untuk target_pk jika ada koma
                $model->triwulan_realisasi = str_replace(',', '.', $model->triwulan_realisasi);
                $model->triwulan_capaian = str_replace(',', '.', $model->triwulan_capaian);
                // Save model with updated target_rkt
                if ($model->save()) {
                    Yii::$app->session->setFlash('success', 'Berhasil Memperbarui Data');
                    $referrer = Yii::$app->request->referrer;
                    $redirectUrl = \yii\helpers\Url::to([
                        'sakip-indikatorcascadingkegiatan-triwulan/index-triwulan-capaian',
                        'refperiode_id' => $model->refperiode_id,
                        'reftriwulan_id' => $reftriwulan_id
                    ]);
                    if (strpos($referrer, 'index-triwulan-capaian-dev') !== false) {
                        $redirectUrl = \yii\helpers\Url::to([
                            'sakip-indikatorcascadingkegiatan-triwulan/index-triwulan-capaian-dev',
                            'refperiode_id' => $model->refperiode_id,
                            'reftriwulan_id' => $reftriwulan_id,
                            'refskpd_id' => $model->refskpd_id
                        ]);
                    }
                    return $this->asJson([
                        'success' => true,
                        'redirect' => $redirectUrl
                    ]);
                } else {
                    Yii::error("Error saving data: " . json_encode($model->getErrors()));
                    return $this->asJson(['success' => false, 'errors' => $model->getErrors()]);
                }
            }

            return $this->renderAjax('_formupdatetriwulancapaian', [
                'model' => $model,
                'reftriwulan_id' => $reftriwulan_id, // Pass it to the view
            ]);
        }

        return $this->render('update-triwulan-capaian', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing SakipIndikatorcascadingkegiatanTriwulan model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $refindikatorkegiatantriwulan_id Refindikatorkegiatantriwulan ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($refindikatorkegiatantriwulan_id)
    {
        $this->findModel($refindikatorkegiatantriwulan_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the SakipIndikatorcascadingkegiatanTriwulan model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $refindikatorkegiatantriwulan_id Refindikatorkegiatantriwulan ID
     * @return SakipIndikatorcascadingkegiatanTriwulan the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($refindikatorkegiatantriwulan_id)
    {
        if (($model = SakipIndikatorcascadingkegiatanTriwulan::findOne(['refindikatorkegiatantriwulan_id' => $refindikatorkegiatantriwulan_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
