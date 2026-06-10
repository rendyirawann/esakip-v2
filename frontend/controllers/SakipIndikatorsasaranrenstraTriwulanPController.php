<?php

namespace frontend\controllers;

use Yii;
use frontend\models\SakipIndikatorsasaranrenstraPTriwulan;
use frontend\models\search\SakipIndikatorsasaranrenstraPTriwulanSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use frontend\models\SakipSasaranrenstraP;
use frontend\models\SakipSkpd;
use frontend\models\SakipPeriode;
use frontend\models\SakipSasaranP;
use frontend\models\SakipTujuanP;
use frontend\models\SakipKoordinasi;
use frontend\models\SakipTujuanrenstraP;
use frontend\models\SakipIndikatorsasaranrenstraP;
use frontend\models\search\SakipIndikatorsasaranrenstraPSearch;
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
 * SakipIndikatorsasaranrenstraTriwulanController implements the CRUD actions for SakipIndikatorsasaranrenstraTriwulan model.
 */
class SakipIndikatorsasaranrenstraPTriwulanController extends Controller
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
     * Lists all SakipIndikatorsasaranrenstraTriwulan models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new SakipIndikatorsasaranrenstraPTriwulanSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionIndexTriwulanPk($refperiode_id = null, $reftriwulan_id = null)
    {
        $searchModel = new SakipIndikatorsasaranrenstraPSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
        $dataProvider->pagination = false;

        // Get the current user's refskpd_id
        $user = Yii::$app->user->identity;
        $refskpd_id = $user->refskpd_id;

        // Get the SKPD name based on refskpd_id
        $nama_skpd = SakipSkpd::find()->select('nama_skpd')->where(['refskpd_id' => $refskpd_id])->scalar();

        // Get all periods
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

        // Query the data based on refskpd_id, refperiode_id, and reftriwulan_id
        $query = SakipIndikatorsasaranrenstraPTriwulan::find()
            ->where(['refskpd_id' => $refskpd_id]);

        if ($refperiode_id !== null) {
            $query->andWhere(['refperiode_id' => $refperiode_id]);
        }

        // Add condition to filter by reftriwulan_id
        if ($reftriwulan_id !== null) {
            $query->andWhere(['reftriwulan_id' => $reftriwulan_id]);
        }

        // Execute the query and get the data
        $data = $query->all();

        // Check if data is empty
        $dataEmpty = empty($data);

        // Retrieve the periode based on refperiode_id
        $selectedPeriod = SakipPeriode::find()->where(['refperiode_id' => $refperiode_id])->one();
        $selectedPeriodValue = $selectedPeriod ? $selectedPeriod->periode : null; // Get the periode value

        return $this->render('index-triwulan-pk', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'nama_skpd' => $nama_skpd,
            'periodeList' => $periodeList,
            'selectedPeriodId' => $refperiode_id,
            'selectedTriwulanId' => $reftriwulan_id, // Include selected triwulan id
            'selectedPeriodValue' => $selectedPeriodValue, // Include selected period value
            'data' => $data, // Pass the filtered data to the view
            'dataEmpty' => $dataEmpty, // Check if data is empty
        ]);
    }

    // public function actionIndexTriwulanPkDev($refperiode_id = null, $reftriwulan_id = null, $refskpd_id = null)
    // {
    //     $searchModel = new SakipIndikatorsasaranrenstraSearch();
    //     $dataProvider = $searchModel->search($this->request->queryParams);
    //     $dataProvider->pagination = false;

    //     // Ambil refskpd_id dari user saat ini jika tidak ada di request
    //     if ($refskpd_id === null) {
    //         $user = Yii::$app->user->identity;
    //         $refskpd_id = $user->refskpd_id;
    //     }

    //     // Get the SKPD name based on refskpd_id
    //     $nama_skpd = SakipSkpd::find()->select('nama_skpd')->where(['refskpd_id' => $refskpd_id])->scalar();

    //     // Get all periods
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

    //     // Query the data based on refskpd_id, refperiode_id, and reftriwulan_id
    //     $query = SakipIndikatorsasaranrenstraTriwulan::find()
    //         ->where(['refskpd_id' => $refskpd_id]);

    //     if ($refperiode_id !== null) {
    //         $query->andWhere(['refperiode_id' => $refperiode_id]);
    //     }

    //     // Add condition to filter by reftriwulan_id
    //     if ($reftriwulan_id !== null) {
    //         $query->andWhere(['reftriwulan_id' => $reftriwulan_id]);
    //     }

    //     // Execute the query and get the data
    //     $data = $query->all();

    //     // Check if data is empty
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
    //         'periodeList' => $periodeList,
    //         'selectedPeriodId' => $refperiode_id,
    //         'selectedTriwulanId' => $reftriwulan_id, // Include selected triwulan id
    //         'selectedPeriodValue' => $selectedPeriodValue, // Include selected period value
    //         'data' => $data, // Pass the filtered data to the view
    //         'dataEmpty' => $dataEmpty, // Check if data is empty
    //         'selectedSkpdId' => $refskpd_id,
    //         'skpdList' => $skpdList,
    //     ]);
    // }

    public function actionIndexTriwulanPkDev($refperiode_id = null, $reftriwulan_id = null, $refskpd_id = null)
    {
        $searchModel = new SakipIndikatorsasaranrenstraPSearch();
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

        // Get the SKPD name based on refskpd_id
        $nama_skpd = $refskpd_id ? SakipSkpd::find()->select('nama_skpd')->where(['refskpd_id' => $refskpd_id])->scalar() : 'Tidak ada SKPD dipilih';

        // Get all periods
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

        // Query the data based on refskpd_id, refperiode_id, and reftriwulan_id
        $query = SakipIndikatorsasaranrenstraPTriwulan::find()
            ->where(['refskpd_id' => $refskpd_id]);

        if ($refperiode_id !== null) {
            $query->andWhere(['refperiode_id' => $refperiode_id]);
        }

        // Add condition to filter by reftriwulan_id
        if ($reftriwulan_id !== null) {
            $query->andWhere(['reftriwulan_id' => $reftriwulan_id]);
        }

        // Execute the query and get the data
        $data = $query->all();

        // Check if data is empty
        $dataEmpty = empty($data);

        // Retrieve the periode based on refperiode_id
        $selectedPeriod = SakipPeriode::find()->where(['refperiode_id' => $refperiode_id])->one();
        $selectedPeriodValue = $selectedPeriod ? $selectedPeriod->periode : null; // Get the periode value

        return $this->render('index-triwulan-pk-dev', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'nama_skpd' => $nama_skpd,
            'periodeList' => $periodeList,
            'selectedPeriodId' => $refperiode_id,
            'selectedTriwulanId' => $reftriwulan_id, // Include selected triwulan id
            'selectedPeriodValue' => $selectedPeriodValue, // Include selected period value
            'data' => $data, // Pass the filtered data to the view
            'dataEmpty' => $dataEmpty, // Check if data is empty
            'selectedSkpdId' => $refskpd_id,
            'skpdList' => $skpdList,
        ]);
    }

    public function actionIndexTriwulanPkp($refperiode_id = null, $reftriwulan_id = null)
    {
        $searchModel = new SakipIndikatorsasaranrenstraPTriwulanSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
        $dataProvider->pagination = false;

        // Get the current user's refskpd_id
        $user = Yii::$app->user->identity;
        $refskpd_id = $user->refskpd_id;

        // Get the SKPD name based on refskpd_id
        $nama_skpd = SakipSkpd::find()->select('nama_skpd')->where(['refskpd_id' => $refskpd_id])->scalar();

        // Get all periods
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

        // Query the data based on refskpd_id, refperiode_id, and reftriwulan_id
        $query = SakipIndikatorsasaranrenstraPTriwulan::find()
            ->where(['refskpd_id' => $refskpd_id]);

        if ($refperiode_id !== null) {
            $query->andWhere(['refperiode_id' => $refperiode_id]);
        }

        // Add condition to filter by reftriwulan_id
        if ($reftriwulan_id !== null) {
            $query->andWhere(['reftriwulan_id' => $reftriwulan_id]);
        }

        // Execute the query and get the data
        $data = $query->all();

        // Check if data is empty
        $dataEmpty = empty($data);

        // Retrieve the periode based on refperiode_id
        $selectedPeriod = SakipPeriode::find()->where(['refperiode_id' => $refperiode_id])->one();
        $selectedPeriodValue = $selectedPeriod ? $selectedPeriod->periode : null; // Get the periode value

        return $this->render('index-triwulan-pkp', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'nama_skpd' => $nama_skpd,
            'periodeList' => $periodeList,
            'selectedPeriodId' => $refperiode_id,
            'selectedTriwulanId' => $reftriwulan_id, // Include selected triwulan id
            'selectedPeriodValue' => $selectedPeriodValue, // Include selected period value
            'data' => $data, // Pass the filtered data to the view
            'dataEmpty' => $dataEmpty, // Check if data is empty
        ]);
    }

    // public function actionIndexTriwulanPkpDev($refperiode_id = null, $reftriwulan_id = null, $refskpd_id = null)
    // {
    //     $searchModel = new SakipIndikatorsasaranrenstraTriwulanSearch();
    //     $dataProvider = $searchModel->search($this->request->queryParams);
    //     $dataProvider->pagination = false;

    //     // Ambil refskpd_id dari user saat ini jika tidak ada di request
    //     if ($refskpd_id === null) {
    //         $user = Yii::$app->user->identity;
    //         $refskpd_id = $user->refskpd_id;
    //     }

    //     // Get the SKPD name based on refskpd_id
    //     $nama_skpd = SakipSkpd::find()->select('nama_skpd')->where(['refskpd_id' => $refskpd_id])->scalar();

    //     // Get all periods
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

    //     // Query the data based on refskpd_id, refperiode_id, and reftriwulan_id
    //     $query = SakipIndikatorsasaranrenstraTriwulan::find()
    //         ->where(['refskpd_id' => $refskpd_id]);

    //     if ($refperiode_id !== null) {
    //         $query->andWhere(['refperiode_id' => $refperiode_id]);
    //     }

    //     // Add condition to filter by reftriwulan_id
    //     if ($reftriwulan_id !== null) {
    //         $query->andWhere(['reftriwulan_id' => $reftriwulan_id]);
    //     }

    //     // Execute the query and get the data
    //     $data = $query->all();

    //     // Check if data is empty
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
    //         'periodeList' => $periodeList,
    //         'selectedPeriodId' => $refperiode_id,
    //         'selectedTriwulanId' => $reftriwulan_id, // Include selected triwulan id
    //         'selectedPeriodValue' => $selectedPeriodValue, // Include selected period value
    //         'data' => $data, // Pass the filtered data to the view
    //         'dataEmpty' => $dataEmpty, // Check if data is empty
    //         'selectedSkpdId' => $refskpd_id,
    //         'skpdList' => $skpdList,
    //     ]);
    // }

    public function actionIndexTriwulanPkpDev($refperiode_id = null, $reftriwulan_id = null, $refskpd_id = null)
    {
        $searchModel = new SakipIndikatorsasaranrenstraPTriwulanSearch();
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

        // Get the SKPD name based on refskpd_id
        $nama_skpd = $refskpd_id ? SakipSkpd::find()->select('nama_skpd')->where(['refskpd_id' => $refskpd_id])->scalar() : 'Tidak ada SKPD dipilih';

        // Get all periods
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

        // Query the data based on refskpd_id, refperiode_id, and reftriwulan_id
        $query = SakipIndikatorsasaranrenstraPTriwulan::find()
            ->where(['refskpd_id' => $refskpd_id]);

        if ($refperiode_id !== null) {
            $query->andWhere(['refperiode_id' => $refperiode_id]);
        }

        // Add condition to filter by reftriwulan_id
        if ($reftriwulan_id !== null) {
            $query->andWhere(['reftriwulan_id' => $reftriwulan_id]);
        }

        // Execute the query and get the data
        $data = $query->all();

        // Check if data is empty
        $dataEmpty = empty($data);

        // Retrieve the periode based on refperiode_id
        $selectedPeriod = SakipPeriode::find()->where(['refperiode_id' => $refperiode_id])->one();
        $selectedPeriodValue = $selectedPeriod ? $selectedPeriod->periode : null; // Get the periode value

        return $this->render('index-triwulan-pkp-dev', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'nama_skpd' => $nama_skpd,
            'periodeList' => $periodeList,
            'selectedPeriodId' => $refperiode_id,
            'selectedTriwulanId' => $reftriwulan_id, // Include selected triwulan id
            'selectedPeriodValue' => $selectedPeriodValue, // Include selected period value
            'data' => $data, // Pass the filtered data to the view
            'dataEmpty' => $dataEmpty, // Check if data is empty
            'selectedSkpdId' => $refskpd_id,
            'skpdList' => $skpdList,
        ]);
    }

    public function actionIndexTriwulanCapaian($refperiode_id = null, $reftriwulan_id = null)
    {
        $searchModel = new SakipIndikatorsasaranrenstraPTriwulanSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
        $dataProvider->pagination = false;

        // Get the current user's refskpd_id
        $user = Yii::$app->user->identity;
        $refskpd_id = $user->refskpd_id;

        // Get the SKPD name based on refskpd_id
        $nama_skpd = SakipSkpd::find()->select('nama_skpd')->where(['refskpd_id' => $refskpd_id])->scalar();

        // Get all periods
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

        // Query the data based on refskpd_id, refperiode_id, and reftriwulan_id
        $query = SakipIndikatorsasaranrenstraPTriwulan::find()
            ->where(['refskpd_id' => $refskpd_id]);

        if ($refperiode_id !== null) {
            $query->andWhere(['refperiode_id' => $refperiode_id]);
        }

        // Add condition to filter by reftriwulan_id
        if ($reftriwulan_id !== null) {
            $query->andWhere(['reftriwulan_id' => $reftriwulan_id]);
        }

        // Execute the query and get the data
        $data = $query->all();

        // Check if data is empty
        $dataEmpty = empty($data);

        // Retrieve the periode based on refperiode_id
        $selectedPeriod = SakipPeriode::find()->where(['refperiode_id' => $refperiode_id])->one();
        $selectedPeriodValue = $selectedPeriod ? $selectedPeriod->periode : null; // Get the periode value

        return $this->render('index-triwulan-capaian', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'nama_skpd' => $nama_skpd,
            'periodeList' => $periodeList,
            'selectedPeriodId' => $refperiode_id,
            'selectedTriwulanId' => $reftriwulan_id, // Include selected triwulan id
            'selectedPeriodValue' => $selectedPeriodValue, // Include selected period value
            'data' => $data, // Pass the filtered data to the view
            'dataEmpty' => $dataEmpty, // Check if data is empty
        ]);
    }

    // public function actionIndexTriwulanCapaianDev($refperiode_id = null, $reftriwulan_id = null, $refskpd_id = null)
    // {
    //     $searchModel = new SakipIndikatorsasaranrenstraTriwulanSearch();
    //     $dataProvider = $searchModel->search($this->request->queryParams);
    //     $dataProvider->pagination = false;

    //     // Ambil refskpd_id dari user saat ini jika tidak ada di request
    //     if ($refskpd_id === null) {
    //         $user = Yii::$app->user->identity;
    //         $refskpd_id = $user->refskpd_id;
    //     }

    //     // Get the SKPD name based on refskpd_id
    //     $nama_skpd = SakipSkpd::find()->select('nama_skpd')->where(['refskpd_id' => $refskpd_id])->scalar();

    //     // Get all periods
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

    //     // Query the data based on refskpd_id, refperiode_id, and reftriwulan_id
    //     $query = SakipIndikatorsasaranrenstraTriwulan::find()
    //         ->where(['refskpd_id' => $refskpd_id]);

    //     if ($refperiode_id !== null) {
    //         $query->andWhere(['refperiode_id' => $refperiode_id]);
    //     }

    //     // Add condition to filter by reftriwulan_id
    //     if ($reftriwulan_id !== null) {
    //         $query->andWhere(['reftriwulan_id' => $reftriwulan_id]);
    //     }

    //     // Execute the query and get the data
    //     $data = $query->all();

    //     // Check if data is empty
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
    //         'periodeList' => $periodeList,
    //         'selectedPeriodId' => $refperiode_id,
    //         'selectedTriwulanId' => $reftriwulan_id, // Include selected triwulan id
    //         'selectedPeriodValue' => $selectedPeriodValue, // Include selected period value
    //         'data' => $data, // Pass the filtered data to the view
    //         'dataEmpty' => $dataEmpty, // Check if data is empty
    //         'selectedSkpdId' => $refskpd_id,
    //         'skpdList' => $skpdList,
    //     ]);
    // }

    public function actionIndexTriwulanCapaianDev($refperiode_id = null, $reftriwulan_id = null, $refskpd_id = null)
    {
        $searchModel = new SakipIndikatorsasaranrenstraPTriwulanSearch();
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

        // Get the SKPD name based on refskpd_id
        $nama_skpd = $refskpd_id ? SakipSkpd::find()->select('nama_skpd')->where(['refskpd_id' => $refskpd_id])->scalar() : 'Tidak ada SKPD dipilih';

        // Get all periods
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

        // Query the data based on refskpd_id, refperiode_id, and reftriwulan_id
        $query = SakipIndikatorsasaranrenstraPTriwulan::find()
            ->where(['refskpd_id' => $refskpd_id]);

        if ($refperiode_id !== null) {
            $query->andWhere(['refperiode_id' => $refperiode_id]);
        }

        // Add condition to filter by reftriwulan_id
        if ($reftriwulan_id !== null) {
            $query->andWhere(['reftriwulan_id' => $reftriwulan_id]);
        }

        // Execute the query and get the data
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
            'periodeList' => $periodeList,
            'selectedPeriodId' => $refperiode_id,
            'selectedTriwulanId' => $reftriwulan_id, // Include selected triwulan id
            'selectedPeriodValue' => $selectedPeriodValue, // Include selected period value
            'data' => $data, // Pass the filtered data to the view
            'dataEmpty' => $dataEmpty, // Check if data is empty
            'selectedSkpdId' => $refskpd_id,
            'skpdList' => $skpdList,
        ]);
    }

    /**
     * Displays a single SakipIndikatorsasaranrenstraPTriwulan model.
     * @param int $refindikatorsasaranrenstratriwulan_p_id Refindikatorsasaranrenstratriwulan ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($refindikatorsasaranrenstratriwulan_p_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($refindikatorsasaranrenstratriwulan_p_id),
        ]);
    }

    /**
     * Creates a new SakipIndikatorsasaranrenstraTriwulan model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new SakipIndikatorsasaranrenstraPTriwulan();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'refindikatorsasaranrenstratriwulan_p_id' => $model->refindikatorsasaranrenstratriwulan_p_id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing SakipIndikatorsasaranrenstraTriwulan model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $refindikatorsasaranrenstratriwulan_p_id Refindikatorsasaranrenstratriwulan ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($refindikatorsasaranrenstratriwulan_p_id)
    {
        $model = $this->findModel($refindikatorsasaranrenstratriwulan_p_id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'refindikatorsasaranrenstratriwulan_p_id' => $model->refindikatorsasaranrenstratriwulan_p_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionUpdateTriwulanPk($refindikatorsasaranrenstratriwulan_p_id, $reftriwulan_id = null, $refperiode_id = null)
    {
        $model = $this->findModel($refindikatorsasaranrenstratriwulan_p_id);

        if (Yii::$app->request->isAjax) {
            if ($model->load(Yii::$app->request->post())) {
                // Konversi koma ke titik pada triwulan_target_pk
                $model->triwulan_target_pk = str_replace(',', '.', $model->triwulan_target_pk);
                // Save model with updated target_rkt
                if ($model->save()) {
                    Yii::$app->session->setFlash('success', 'Berhasil Memperbarui Data');
                    // Redirect back to the page with both refperiode_id and reftriwulan_id
                    return $this->asJson([
                        'success' => true,
                        'redirect' => \yii\helpers\Url::to([
                            'sakip-indikatorsasaranrenstra-triwulan/index-triwulan-pk',
                            'refperiode_id' => $model->refperiode_id,
                            'reftriwulan_id' => $reftriwulan_id
                        ])
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

    public function actionUpdateTriwulanPkp($refindikatorsasaranrenstratriwulan_p_id, $reftriwulan_id = null, $refperiode_id = null)
    {
        $model = $this->findModel($refindikatorsasaranrenstratriwulan_p_id);

        if (Yii::$app->request->isAjax) {
            if ($model->load(Yii::$app->request->post())) {
                // Konversi koma ke titik pada triwulan_target_pk
                $model->triwulan_target_pk_p = str_replace(',', '.', $model->triwulan_target_pk_p);
                // Save model with updated target_rkt
                if ($model->save()) {
                    Yii::$app->session->setFlash('success', 'Berhasil Memperbarui Data');
                    // Redirect back to the page with both refperiode_id and reftriwulan_id
                    return $this->asJson([
                        'success' => true,
                        'redirect' => \yii\helpers\Url::to([
                            'sakip-indikatorsasaranrenstra-triwulan/index-triwulan-pkp',
                            'refperiode_id' => $model->refperiode_id,
                            'reftriwulan_id' => $reftriwulan_id
                        ])
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

    public function actionUpdateTriwulanCapaian($refindikatorsasaranrenstratriwulan_p_id, $reftriwulan_id = null, $refperiode_id = null)
    {
        $model = $this->findModel($refindikatorsasaranrenstratriwulan_p_id);

        if (Yii::$app->request->isAjax) {
            if ($model->load(Yii::$app->request->post())) {
                // Konversi koma ke titik pada triwulan_target_pk
                $model->triwulan_realisasi = str_replace(',', '.', $model->triwulan_realisasi);
                $model->triwulan_capaian = str_replace(',', '.', $model->triwulan_capaian);
                // Save model with updated target_rkt
                if ($model->save()) {
                    Yii::$app->session->setFlash('success', 'Berhasil Memperbarui Data');
                    // Redirect back to the page with both refperiode_id and reftriwulan_id
                    return $this->asJson([
                        'success' => true,
                        'redirect' => \yii\helpers\Url::to([
                            'sakip-indikatorsasaranrenstra-triwulan/index-triwulan-capaian',
                            'refperiode_id' => $model->refperiode_id,
                            'reftriwulan_id' => $reftriwulan_id
                        ])
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
     * Deletes an existing SakipIndikatorsasaranrenstraTriwulan model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $refindikatorsasaranrenstratriwulan_p_id Refindikatorsasaranrenstratriwulan ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($refindikatorsasaranrenstratriwulan_p_id)
    {
        $this->findModel($refindikatorsasaranrenstratriwulan_p_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the SakipIndikatorsasaranrenstraPTriwulan model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $refindikatorsasaranrenstratriwulan_p_id Refindikatorsasaranrenstratriwulan ID
     * @return SakipIndikatorsasaranrenstraPTriwulan the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($refindikatorsasaranrenstratriwulan_p_id)
    {
        if (($model = SakipIndikatorsasaranrenstraPTriwulan::findOne(['refindikatorsasaranrenstratriwulan_p_id' => $refindikatorsasaranrenstratriwulan_p_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
