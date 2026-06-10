<?php

namespace frontend\controllers;

use Yii;
use frontend\models\SakipSasaranrenstra;
use frontend\models\SakipSasaranrenstraP;
use frontend\models\SakipIndikatorsasaranrenstra;
use frontend\models\SakipSkpd;
use frontend\models\SakipPeriode;
use frontend\models\SakipSasaran;
use frontend\models\SakipTujuan;
use frontend\models\SakipVisi;
use frontend\models\SakipMisi;
use frontend\models\SakipKoordinasi;
use frontend\models\SakipTujuanrenstra;
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
 * SakipSasaranrenstraController implements the CRUD actions for SakipSasaranrenstra model.
 */
class SakipSasaranrenstraController extends Controller
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
     * Lists all SakipSasaranrenstra models.
     *
     * @return string
     */
    public function actionIndex($refperiode_id = null, $active_tab = 'murni')
    {
        $searchModel = new SakipSasaranrenstraSearch();

        // Ambil refskpd_id dari user saat ini
        $user = Yii::$app->user->identity;
        $refskpd_id = $user->refskpd_id;

        // Get the name of the SKPD based on refskpd_id
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

        $searchParams = $this->request->queryParams;
        if (!isset($searchParams['SakipSasaranrenstraSearch'])) {
            $searchParams['SakipSasaranrenstraSearch'] = [];
        }
        $searchParams['SakipSasaranrenstraSearch']['refperiode_5tahun_id'] = $refperiode_5tahun_id;
        $searchParams['SakipSasaranrenstraSearch']['refskpd_id'] = $refskpd_id;

        $dataProvider = $searchModel->search($searchParams);
        $dataProvider->pagination = false;

        $sasaranRenstra = SakipSasaranRenstra::find()
            ->with([
                'sasaran',
                'sasaran.refTujuan',
                'indikators' => function ($q) use ($refperiode_id) {
                    $q->andWhere(['refperiode_id' => $refperiode_id]);
                }
            ])
            ->where(['refperiode_5tahun_id' => $refperiode_5tahun_id, 'refskpd_id' => $refskpd_id])
            ->orderBy(['reftujuan_id' => SORT_ASC, 'refmisi_id' => SORT_ASC])
            ->all();

        $sasaranRenstraP = SakipSasaranrenstraP::find()
            ->with([
                'sasaran',
                'sasaran.refTujuan',
                'indikators' => function ($q) use ($refperiode_id) {
                    $q->andWhere(['refperiode_id' => $refperiode_id]);
                }
            ])
            ->where(['refperiode_5tahun_id' => $refperiode_5tahun_id, 'refskpd_id' => $refskpd_id])
            ->orderBy(['reftujuan_p_id' => SORT_ASC, 'refmisi_p_id' => SORT_ASC])
            ->all();

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'sasaranRenstra' => $sasaranRenstra,
            'sasaranRenstraP' => $sasaranRenstraP,
            'nama_skpd' => $nama_skpd,
            'periodeList' => $periodeList,
            'selectedPeriodId' => $refperiode_id,
            'selectedPeriodValue' => $selectedPeriodValue, // Include selected period value
            'active_tab' => $active_tab, // <-- Kirim variabel ini ke view
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

    //     $nama_skpd = SakipSkpd::find()->select('nama_skpd')->where(['refskpd_id' => $refskpd_id])->scalar();

    //     // Fetch all periods
    //     $periodeList = SakipPeriode::find()->all();

    //     // Set default period to this year if not provided
    //     if ($refperiode_id === null) {
    //         $currentYear = date('Y');
    //         $defaultPeriod = SakipPeriode::find()->where(['periode' => $currentYear])->one();
    //         $refperiode_id = $defaultPeriod ? $defaultPeriod->refperiode_id : null;
    //     }

    //     $sasaranRenstra = SakipSasaranRenstra::find()
    //         ->with(['sasaran', 'sasaran.refTujuan', 'indikators']) // Eager loading untuk relasi
    //         ->where(['refperiode_id' => $refperiode_id, 'refskpd_id' => $refskpd_id]) // Filter by refperiode_id dan refskpd_id
    //         ->orderBy(['reftujuan_id' => SORT_ASC, 'refmisi_id' => SORT_ASC]) // Urutkan berdasarkan reftujuan_id dan refmisi_id
    //         ->all();

    //     // Ambil daftar SKPD untuk dropdown
    //     $skpdList = ArrayHelper::map(SakipSkpd::find()->all(), 'refskpd_id', 'nama_skpd');


    //     // Retrieve the periode based on refperiode_id
    //     $selectedPeriod = SakipPeriode::find()->where(['refperiode_id' => $refperiode_id])->one();
    //     $selectedPeriodValue = $selectedPeriod ? $selectedPeriod->periode : null; // Get the periode value

    //     return $this->render('index-dev', [
    //         'searchModel' => $searchModel,
    //         'dataProvider' => $dataProvider,
    //         'sasaranRenstra' => $sasaranRenstra,
    //         'nama_skpd' => $nama_skpd,
    //         'periodeList' => $periodeList,
    //         'selectedPeriodId' => $refperiode_id,
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

        $nama_skpd = $refskpd_id ? SakipSkpd::find()->select('nama_skpd')->where(['refskpd_id' => $refskpd_id])->scalar() : 'Tidak ada SKPD dipilih';

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

        $searchParams = $this->request->queryParams;
        if (!isset($searchParams['SakipSasaranrenstraSearch'])) {
            $searchParams['SakipSasaranrenstraSearch'] = [];
        }
        $searchParams['SakipSasaranrenstraSearch']['refperiode_5tahun_id'] = $refperiode_5tahun_id;
        $searchParams['SakipSasaranrenstraSearch']['refskpd_id'] = $refskpd_id;

        $searchModel = new SakipSasaranrenstraSearch();
        $dataProvider = $searchModel->search($searchParams);
        $dataProvider->pagination = false;

        $sasaranRenstra = [];
        if ($refskpd_id && $refperiode_id) {
            $sasaranRenstra = SakipSasaranRenstra::find()
                ->with([
                    'sasaran',
                    'sasaran.refTujuan',
                    'indikators' => function ($q) use ($refperiode_id) {
                        $q->andWhere(['refperiode_id' => $refperiode_id]);
                    }
                ])
                ->where(['refperiode_5tahun_id' => $refperiode_5tahun_id, 'refskpd_id' => $refskpd_id])
                ->orderBy(['reftujuan_id' => SORT_ASC, 'refmisi_id' => SORT_ASC])
                ->all();
        }

        return $this->render('index-dev', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'sasaranRenstra' => $sasaranRenstra,
            'nama_skpd' => $nama_skpd,
            'periodeList' => $periodeList,
            'selectedPeriodId' => $refperiode_id,
            'selectedPeriodValue' => $selectedPeriodValue, // Include selected period value
            'selectedSkpdId' => $refskpd_id,
            'skpdList' => $skpdList,
        ]);
    }

    public function actionIndexFormulasi($refperiode_id = null)
    {
        $searchModel = new SakipSasaranrenstraSearch();

        $user = Yii::$app->user->identity;
        $refskpd_id = $user->refskpd_id;

        $nama_skpd = SakipSkpd::find()->select('nama_skpd')->where(['refskpd_id' => $refskpd_id])->scalar();
        $periodeList = SakipPeriode::find()->all();

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
        if (!isset($searchParams['SakipSasaranrenstraSearch'])) {
            $searchParams['SakipSasaranrenstraSearch'] = [];
        }
        $searchParams['SakipSasaranrenstraSearch']['refperiode_5tahun_id'] = $refperiode_5tahun_id;
        $searchParams['SakipSasaranrenstraSearch']['refskpd_id'] = $refskpd_id;

        $dataProvider = $searchModel->search($searchParams);
        $dataProvider->pagination = false;

        $query = SakipSasaranrenstra::find()
            ->where(['refskpd_id' => $refskpd_id])
            ->with(['indikators' => function ($q) use ($refperiode_id) {
                $q->andWhere(['refperiode_id' => $refperiode_id]);
            }]);

        if ($refperiode_5tahun_id !== null) {
            $query->andWhere(['refperiode_5tahun_id' => $refperiode_5tahun_id]);
        }

        $data = $query->all();
        $dataEmpty = empty($data);

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
    //     $searchModel = new SakipSasaranrenstraSearch();
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

        // Retrieve the periode based on refperiode_id
        $selectedPeriod = SakipPeriode::find()->where(['refperiode_id' => $refperiode_id])->one();
        $selectedPeriodValue = $selectedPeriod ? $selectedPeriod->periode : null; // Get the periode value
        $refperiode_5tahun_id = $selectedPeriod ? $selectedPeriod->refperiode_5tahun_id : null;

        $searchParams = $this->request->queryParams;
        if (!isset($searchParams['SakipSasaranrenstraSearch'])) {
            $searchParams['SakipSasaranrenstraSearch'] = [];
        }
        $searchParams['SakipSasaranrenstraSearch']['refperiode_5tahun_id'] = $refperiode_5tahun_id;
        $searchParams['SakipSasaranrenstraSearch']['refskpd_id'] = $refskpd_id;

        $searchModel = new SakipSasaranrenstraSearch();
        $dataProvider = $searchModel->search($searchParams);
        $dataProvider->pagination = false;

        $query = SakipSasaranrenstra::find()
            ->where(['refskpd_id' => $refskpd_id])
            ->with(['indikators' => function ($q) use ($refperiode_id) {
                $q->andWhere(['refperiode_id' => $refperiode_id]);
            }]);

        if ($refperiode_5tahun_id !== null) {
            $query->andWhere(['refperiode_5tahun_id' => $refperiode_5tahun_id]);
        }

        $data = $query->all();
        $dataEmpty = empty($data);

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

    /**
     * Displays a single SakipSasaranrenstra model.
     * @param int $refsasaranrenstra_id Refsasaranrenstra ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($refsasaranrenstra_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($refsasaranrenstra_id),
        ]);
    }

    /**
     * Creates a new SakipSasaranrenstra model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate($refperiode_id = null)
    {
        $model = new SakipSasaranrenstra();

        // Ambil refskpd_id dari user saat ini
        $user = Yii::$app->user->identity;
        $refskpd_id = $user->refskpd_id;

        // Ambil data dari sakip_periode
        $periodeList = SakipPeriode::find()
            ->where(['periode_isaktif' => 'T'])
            ->all();


        if (Yii::$app->request->isAjax) {
            if ($model->load(Yii::$app->request->post())) {
                if ($model->save()) {
                    Yii::$app->session->setFlash('success', 'Berhasil Tambah Data');
                    return $this->asJson(['success' => true, 'redirect' => \yii\helpers\Url::to(['index', 'refperiode_id' => $model->refperiode_id])]);
                } else {
                    // Tambahkan log error
                    Yii::error("Error saving data: " . json_encode($model->getErrors()));
                    return $this->asJson(['success' => false, 'errors' => $model->getErrors()]);
                }
            }

            return $this->renderAjax('_form', [
                'model' => $model,
                'periodeList' => $periodeList, // Pass periodeList to form
            ]);
        }

        return $this->render('create', [
            'model' => $model,
            'periodeList' => $periodeList, // Pass periodeList to form
        ]);
    }

    public function actionGetSasaranOptions($refperiode_id)
    {
        $selectedPeriod = SakipPeriode::find()->where(['refperiode_id' => $refperiode_id])->one();
        $refperiode_5tahun_id = $selectedPeriod ? $selectedPeriod->refperiode_5tahun_id : null;

        $options = SakipSasaran::find()
            ->where(['refperiode_5tahun_id' => $refperiode_5tahun_id])
            ->all();

        $optionsHtml = "<option value=''>Select Sasaran</option>";
        foreach ($options as $option) {
            $optionsHtml .= "<option value='{$option->refsasaran_id}'>{$option->uraian_sasaran}</option>";
        }

        return $optionsHtml;
    }


    /**
     * Updates an existing Sakipvisi model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $refsasaranrenstra_id Refvisi ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($refsasaranrenstra_id, $refperiode_id = null)
    {
        $model = $this->findModel($refsasaranrenstra_id);

        // Ambil refskpd_id dari model, atau user saat ini jika null
        $refskpd_id = $model->refskpd_id ?: Yii::$app->user->identity->refskpd_id;

        // Ambil data dari sakip_periode
        $periodeList = SakipPeriode::find()->all(); // Query to get all periods

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

            return $this->renderAjax('_formupdatesasaranrenstra', [
                'model' => $model,
                'refskpd_id' => $refskpd_id,
                'periodeList' => $periodeList,
            ]);
        }

        return $this->render('update', [
            'model' => $model,
            'refskpd_id' => $refskpd_id,
            'periodeList' => $periodeList,
        ]);
    }

    public function actionUpdateTujuanrenstra($refsasaranrenstra_id, $refperiode_id = null)
    {
        $model = $this->findModel($refsasaranrenstra_id);

        // Fetch the list of Tujuan that match the refsasaranrenstra_id of the current model
        $tujuanList = SakipTujuanrenstra::find()
            ->where([
                'refskpd_id' => $model->refskpd_id,
                // 'refsasaranrenstra_id' => $model->refsasaranrenstra_id, // Filter by refsasaranrenstra_id
            ])
            ->all();

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
                    Yii::error("Error saving data: " . json_encode($model->getErrors()));
                    return $this->asJson(['success' => false, 'errors' => $model->getErrors()]);
                }
            }

            return $this->renderAjax('_formupdate', [
                'model' => $model,
                'tujuanList' => $tujuanList, // Pass the filtered list to the view
            ]);
        }

        return $this->render('update-tujuanrenstra', [
            'model' => $model,
            'tujuanList' => $tujuanList, // Pass the filtered list to the view
        ]);
    }

    public function actionUpdateFormulasi($refsasaranrenstra_id, $refperiode_id = null)
    {
        $model = $this->findModel($refsasaranrenstra_id);

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
     * Deletes an existing SakipSasaranrenstra model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $refsasaranrenstra_id Refsasaranrenstra ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($refsasaranrenstra_id)
    {
        $model = $this->findModel($refsasaranrenstra_id);

        if (!$model) {
            throw new NotFoundHttpException('Data tidak ditemukan.');
        }

        // Cek apakah refsasaranrenstra_id masih terkait dengan refindikatorsasaranrenstra_id
        $relatedIndicators = SakipIndikatorsasaranrenstra::find()
            ->where(['refsasaranrenstra_id' => $refsasaranrenstra_id])
            ->exists();

        if ($relatedIndicators) {
            Yii::$app->session->setFlash('error', 'Gagal menghapus! refsasaranrenstra_id masih terkait dengan salah satu refindikatorsasaranrenstra_id.');
            return $this->redirect(['index', 'refperiode_id' => $model->refperiode_id]);
        }

        // Ambil refperiode_id sebelum menghapus model
        $refperiode_id = $model->refperiode_id;

        // Hapus model jika tidak ada keterkaitan
        $model->delete();

        Yii::$app->session->setFlash('success', 'Data berhasil dihapus.');

        // Redirect kembali ke index dengan refperiode_id
        return $this->redirect(['index', 'refperiode_id' => $refperiode_id]);
    }


    /**
     * Finds the SakipSasaranrenstra model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $refsasaranrenstra_id Refsasaranrenstra ID
     * @return SakipSasaranrenstra the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($refsasaranrenstra_id)
    {
        if (($model = SakipSasaranrenstra::findOne(['refsasaranrenstra_id' => $refsasaranrenstra_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
