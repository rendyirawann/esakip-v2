<?php

namespace frontend\controllers;

use Yii;
use frontend\models\SakipCascadingsubkegiatan;
use frontend\models\search\SakipCascadingsubkegiatanSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use frontend\models\SakipCascadingkegiatan;
use frontend\models\search\SakipCascadingkegiatanSearch;
use frontend\models\SakipCascadingprogram;
use frontend\models\search\SakipCascadingprogramSearch;
use frontend\models\SakipKebijakan;
use frontend\models\search\SakipKebijakanSearch;
use frontend\models\SakipStrategi;
use frontend\models\search\SakipStrategiSearch;
use frontend\models\SakipIndikatortujuanrenstra;
use frontend\models\SakipIndikatorsasaranrenstra;
use frontend\models\SakipIndikatorcascadingsubkegiatan;
use frontend\models\SakipIndikatorcascadingsubkegiatanTriwulan;
use frontend\models\SakipSkpd;
use frontend\models\SakipPeriode;
use frontend\models\SakipSasaran;
use frontend\models\SakipMisi;
use frontend\models\SakipBidang;
use frontend\models\SakipProgram;
use frontend\models\SakipKegiatan;
use frontend\models\SakipSubkegiatan;
use frontend\models\SakipTujuan;
use frontend\models\SakipKoordinasi;
use frontend\models\SakipTujuanrenstra;
use frontend\models\SakipSasaranrenstra;
use frontend\models\search\SakipIndikatortujuanrenstraSearch;
use frontend\models\search\SakipSasaranrenstraSearch;
use yii\web\UploadedFile;
use yii\helpers\Url;
use yii\web\Response;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\db\Expression;
use yii\helpers\Json;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;
use yii\filters\AccessControl;
use yii\web\ForbiddenHttpException;

/**
 * SakipCascadingsubkegiatanController implements the CRUD actions for SakipCascadingsubkegiatan model.
 */
class SakipCascadingsubkegiatanController extends Controller
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
     * Lists all SakipCascadingsubkegiatan models.
     *
     * @return string
     */
    public function actionIndex($refperiode_id = null)
    {
        $searchModel = new SakipCascadingsubkegiatanSearch();

        // Set default period to this year if not provided
        if ($refperiode_id === null) {
            $currentYear = date('Y');
            $defaultPeriod = SakipPeriode::find()->where(['periode' => $currentYear])->one();
            $refperiode_id = $defaultPeriod ? $defaultPeriod->refperiode_id : null;
        }

        // If a refperiode_id is selected, add it to the query parameters
        if ($refperiode_id !== null) {
            $searchModel->refperiode_id = $refperiode_id;
        }

        // $dataProvider = $searchModel->search($this->request->queryParams);
        // $dataProvider->pagination = false;

        $query = SakipCascadingsubkegiatan::find()
            ->where(['refperiode_id' => $refperiode_id])
            ->andWhere(['refskpd_id' => Yii::$app->user->identity->refskpd_id]);


        // Initialize the data provider
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
        ]);

        // Get refskpd_id from the current user
        $user = Yii::$app->user->identity;
        $refskpd_id = $user->refskpd_id;

        // Get the name of the SKPD based on refskpd_id
        $nama_skpd = SakipSkpd::find()->select('nama_skpd')->where(['refskpd_id' => $refskpd_id])->scalar();

        // Fetch all periods
        $periodeList = SakipPeriode::find()->all();



        // Retrieve the periode based on refperiode_id
        $selectedPeriod = SakipPeriode::find()->where(['refperiode_id' => $refperiode_id])->one();
        $selectedPeriodValue = $selectedPeriod ? $selectedPeriod->periode : null; // Get the periode value

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'nama_skpd' => $nama_skpd,
            'periodeList' => $periodeList,
            'selectedPeriodId' => $refperiode_id,
            'selectedPeriodValue' => $selectedPeriodValue, // Include selected period value
        ]);
    }

    // public function actionIndexDev($refperiode_id = null, $refskpd_id = null)
    // {
    //     $searchModel = new SakipCascadingsubkegiatanSearch();

    //     // Set default period to this year if not provided
    //     if ($refperiode_id === null) {
    //         $currentYear = date('Y');
    //         $defaultPeriod = SakipPeriode::find()->where(['periode' => $currentYear])->one();
    //         $refperiode_id = $defaultPeriod ? $defaultPeriod->refperiode_id : null;
    //     }

    //     $dataProvider = $searchModel->search($this->request->queryParams);
    //     $dataProvider->pagination = false;

    //     // Ambil refskpd_id dari user saat ini jika tidak ada di request
    //     if ($refskpd_id === null) {
    //         $user = Yii::$app->user->identity;
    //         $refskpd_id = $user->refskpd_id;
    //     }

    //     // Get the name of the SKPD based on refskpd_id
    //     $nama_skpd = SakipSkpd::find()->select('nama_skpd')->where(['refskpd_id' => $refskpd_id])->scalar();

    //     // Fetch all periods
    //     $periodeList = SakipPeriode::find()->all();


    //     // Ambil data dari sakip_cascadingprogram sesuai dengan refskpd_id user dan refperiode_id (jika ada)
    //     $query = SakipCascadingsubkegiatan::find()->where(['refskpd_id' => $refskpd_id]);

    //     if ($refperiode_id !== null) {
    //         $query->andWhere(['refperiode_id' => $refperiode_id]);
    //     }

    //     // Execute query and get data
    //     $cascadingSubkegiatans = $query->all(); // Ambil data cascading program

    //     // Add a flag to check if data is empty
    //     $dataEmpty = empty($cascadingSubkegiatans);

    //     // Ambil daftar SKPD untuk dropdown
    //     $skpdList = ArrayHelper::map(SakipSkpd::find()->all(), 'refskpd_id', 'nama_skpd');

    //     // Retrieve the periode based on refperiode_id
    //     $selectedPeriod = SakipPeriode::find()->where(['refperiode_id' => $refperiode_id])->one();
    //     $selectedPeriodValue = $selectedPeriod ? $selectedPeriod->periode : null; // Get the periode value

    //     return $this->render('index-dev', [
    //         'searchModel' => $searchModel,
    //         'dataProvider' => $dataProvider,
    //         'nama_skpd' => $nama_skpd,
    //         'periodeList' => $periodeList,
    //         'selectedPeriodId' => $refperiode_id,
    //         'selectedPeriodValue' => $selectedPeriodValue, // Include selected period value
    //         'selectedSkpdId' => $refskpd_id,
    //         'skpdList' => $skpdList,
    //         'dataEmpty' => $dataEmpty, // Pass the data empty flag
    //         'cascadingSubkegiatans' => $cascadingSubkegiatans, // Send the queried data
    //     ]);
    // }

    public function actionIndexDev($refperiode_id = null, $refskpd_id = null)
    {
        $searchModel = new SakipCascadingsubkegiatanSearch();
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

        // Set default period to this year if not provided
        if ($refperiode_id === null) {
            $currentYear = date('Y');
            $defaultPeriod = SakipPeriode::find()->where(['periode' => $currentYear])->one();
            $refperiode_id = $defaultPeriod ? $defaultPeriod->refperiode_id : null;
        }

        // Get the name of the SKPD based on refskpd_id
        $nama_skpd = $refskpd_id ? SakipSkpd::find()->select('nama_skpd')->where(['refskpd_id' => $refskpd_id])->scalar() : 'Tidak ada SKPD dipilih';

        // Fetch all periods
        $periodeList = SakipPeriode::find()->all();


        // Ambil data dari sakip_cascadingprogram sesuai dengan refskpd_id user dan refperiode_id (jika ada)
        $query = SakipCascadingsubkegiatan::find()->where(['refskpd_id' => $refskpd_id]);

        if ($refperiode_id !== null) {
            $query->andWhere(['refperiode_id' => $refperiode_id]);
        }

        // Execute query and get data
        $cascadingSubkegiatans = $query->all(); // Ambil data cascading program

        // Add a flag to check if data is empty
        $dataEmpty = empty($cascadingSubkegiatans);

        // Retrieve the periode based on refperiode_id
        $selectedPeriod = SakipPeriode::find()->where(['refperiode_id' => $refperiode_id])->one();
        $selectedPeriodValue = $selectedPeriod ? $selectedPeriod->periode : null; // Get the periode value

        return $this->render('index-dev', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'nama_skpd' => $nama_skpd,
            'periodeList' => $periodeList,
            'selectedPeriodId' => $refperiode_id,
            'selectedPeriodValue' => $selectedPeriodValue, // Include selected period value
            'selectedSkpdId' => $refskpd_id,
            'skpdList' => $skpdList,
            'dataEmpty' => $dataEmpty, // Pass the data empty flag
            'cascadingSubkegiatans' => $cascadingSubkegiatans, // Send the queried data
        ]);
    }

    /**
     * Displays a single SakipCascadingsubkegiatan model.
     * @param int $refcascadingsubkegiatan_id Refcascadingsubkegiatan ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($refcascadingsubkegiatan_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($refcascadingsubkegiatan_id),
        ]);
    }

    /**
     * Creates a new SakipCascadingsubkegiatan model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new SakipCascadingsubkegiatan();

        // Ambil refskpd_id dari user saat ini
        $user = Yii::$app->user->identity;
        $refskpd_id = $user->refskpd_id;

        // Ambil data dari sakip_periode
        $periodeList = SakipPeriode::find()
            ->where(['periode_isaktif' => 'T'])
            ->all();

        if (Yii::$app->request->isAjax) {
            if ($model->load(Yii::$app->request->post())) {
                // Konversi koma menjadi titik pada program_target sebelum menyimpan
                $model->subkegiatan_target = str_replace(',', '.', $model->subkegiatan_target);
                // Coba untuk menyimpan model sakip_cascadingprogram
                if ($model->save()) {
                    // Langkah 1: Buat entri untuk sakip_indikatorcascadingprogram
                    $indikatorSubkegiatanModel = new SakipIndikatorCascadingsubkegiatan();
                    $indikatorSubkegiatanModel->refcascadingprogram_id = $model->refcascadingprogram_id;
                    $indikatorSubkegiatanModel->refcascadingkegiatan_id = $model->refcascadingkegiatan_id;
                    $indikatorSubkegiatanModel->refcascadingsubkegiatan_id = $model->refcascadingsubkegiatan_id;
                    $indikatorSubkegiatanModel->refsasaranrenstra_id = $model->refsasaranrenstra_id;
                    $indikatorSubkegiatanModel->refindikatorsasaranrenstra_id = $model->refindikatorsasaranrenstra_id;
                    $indikatorSubkegiatanModel->refskpd_id = $model->refskpd_id;
                    $indikatorSubkegiatanModel->refperiode_id = $model->refperiode_id;
                    $indikatorSubkegiatanModel->refprogram_id = $model->refprogram_id;
                    $indikatorSubkegiatanModel->refkegiatan_id = $model->refkegiatan_id;
                    $indikatorSubkegiatanModel->refsubkegiatan_id = $model->refsubkegiatan_id;
                    $indikatorSubkegiatanModel->target_rkt = $model->subkegiatan_target; // Mengisi target_rkt dari program_target
                    $indikatorSubkegiatanModel->anggaran_rkt = $model->subkegiatan_anggaran; // Mengisi target_rkt dari program_target
                    // Konversi koma menjadi titik pada target_rkt sebelum menyimpan
                    $indikatorSubkegiatanModel->target_rkt = str_replace(',', '.', $indikatorSubkegiatanModel->target_rkt);

                    if ($indikatorSubkegiatanModel->save()) {
                        // Langkah 2: Buat entri untuk sakip_indikatorcascadingprogram_triwulan
                        for ($i = 1; $i <= 4; $i++) {
                            $triwulanModel = new SakipIndikatorCascadingsubkegiatanTriwulan();
                            $triwulanModel->refindikatorsubkegiatan_id = $indikatorSubkegiatanModel->refindikatorsubkegiatan_id;
                            $triwulanModel->refcascadingprogram_id = $model->refcascadingprogram_id;
                            $triwulanModel->refcascadingkegiatan_id = $model->refcascadingkegiatan_id;
                            $triwulanModel->refcascadingsubkegiatan_id = $model->refcascadingsubkegiatan_id;
                            $triwulanModel->refsasaranrenstra_id = $model->refsasaranrenstra_id;
                            $triwulanModel->refindikatorsasaranrenstra_id = $model->refindikatorsasaranrenstra_id;
                            $triwulanModel->refskpd_id = $model->refskpd_id;
                            $triwulanModel->refperiode_id = $model->refperiode_id;
                            $triwulanModel->refprogram_id = $model->refprogram_id;
                            $triwulanModel->refkegiatan_id = $model->refkegiatan_id;
                            $triwulanModel->refsubkegiatan_id = $model->refsubkegiatan_id;
                            $triwulanModel->triwulan_target_rkt = $model->subkegiatan_target;
                            $triwulanModel->reftriwulan_id = $i; // Isi reftriwulan_id sesuai dengan loop (1, 2, 3, 4)
                            // Konversi koma menjadi titik pada triwulan_target_rkt sebelum menyimpan
                            $triwulanModel->triwulan_target_rkt = str_replace(',', '.', $triwulanModel->triwulan_target_rkt);
                            // Simpan triwulan model
                            if (!$triwulanModel->save()) {
                                // Log error jika tidak bisa menyimpan data triwulan
                                Yii::error("Error saving triwulan data for triwulan $i: " . json_encode($triwulanModel->getErrors()));
                                return $this->asJson(['success' => false, 'errors' => array_merge($model->getErrors(), $indikatorSubkegiatanModel->getErrors(), $triwulanModel->getErrors())]);
                            }
                        }
                    } else {
                        Yii::error("Error saving indikator kegiatan: " . json_encode($indikatorSubkegiatanModel->getErrors()));
                        return $this->asJson(['success' => false, 'errors' => $indikatorSubkegiatanModel->getErrors()]);
                    }

                    // Redirect ke index dengan refperiode_id yang dipilih
                    Yii::$app->session->setFlash('success', 'Berhasil Tambah Data');
                    return $this->asJson([
                        'success' => true,
                        'redirect' => \yii\helpers\Url::to(['index', 'refperiode_id' => $model->refperiode_id])
                    ]);
                } else {
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




    /**
     * Updates an existing SakipCascadingsubkegiatan model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $refcascadingsubkegiatan_id Refcascadingsubkegiatan ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($refcascadingsubkegiatan_id)
    {
        $model = $this->findModel($refcascadingsubkegiatan_id);

        if (Yii::$app->request->isAjax) {
            if ($model->load(Yii::$app->request->post())) {
                // Strip dots from subkegiatan_anggaran
                $model->subkegiatan_anggaran = str_replace('.', '', $model->subkegiatan_anggaran);
                // Konversi koma ke titik untuk kegiatan_target sebelum disimpan
                $model->subkegiatan_target = str_replace(',', '.', $model->subkegiatan_target);
                // Simpan perubahan di sakip_cascadingprogram
                if ($model->save()) {
                    // Jika ada perubahan pada program_target, update target_rkt dan triwulan_target_rkt
                    $newSubkegiatanTarget = $model->subkegiatan_target;

                    // Update sakip_indikatorcascadingprogram
                    $indikatorSubkegiatans = SakipIndikatorcascadingsubkegiatan::find()
                        ->where(['refcascadingsubkegiatan_id' => $model->refcascadingsubkegiatan_id])
                        ->all();

                    foreach ($indikatorSubkegiatans as $indikatorSubkegiatan) {
                        $indikatorSubkegiatan->target_rkt = $newSubkegiatanTarget; // Update target_rkt
                        if (!$indikatorSubkegiatan->save()) {
                            Yii::error("Error updating indikator sub kegiatan: " . json_encode($indikatorSubkegiatan->getErrors()));
                            return $this->asJson(['success' => false, 'errors' => $indikatorSubkegiatan->getErrors()]);
                        }

                        // Update sakip_indikatorcascadingprogram_triwulan
                        for ($i = 1; $i <= 4; $i++) {
                            $triwulanModel = SakipIndikatorcascadingsubkegiatanTriwulan::findOne([
                                'refindikatorsubkegiatan_id' => $indikatorSubkegiatan->refindikatorsubkegiatan_id,
                                'reftriwulan_id' => $i,
                            ]);

                            if ($triwulanModel) {
                                $triwulanModel->triwulan_target_rkt = $newSubkegiatanTarget; // Update triwulan_target_rkt
                                if (!$triwulanModel->save()) {
                                    Yii::error("Error updating triwulan data for triwulan $i: " . json_encode($triwulanModel->getErrors()));
                                    return $this->asJson(['success' => false, 'errors' => $triwulanModel->getErrors()]);
                                }
                            }
                        }
                    }

                    // Redirect ke index dengan refperiode_id yang dipilih
                    Yii::$app->session->setFlash('success', 'Berhasil Memperbarui Data');
                    $referrer = Yii::$app->request->referrer;
                    $redirectUrl = \yii\helpers\Url::to(['index', 'refperiode_id' => $model->refperiode_id]);
                    if (strpos($referrer, 'index-dev') !== false) {
                        $redirectUrl = \yii\helpers\Url::to(['index-dev', 'refperiode_id' => $model->refperiode_id, 'refskpd_id' => $model->refskpd_id]);
                    }
                    return $this->asJson([
                        'success' => true,
                        'redirect' => $redirectUrl
                    ]);
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


    public function actionGetKegiatanByPeriode()
    {
        if (Yii::$app->request->isAjax) {
            $refperiode_id = Yii::$app->request->post('refperiode_id');
            $user = Yii::$app->user->identity;
            $refskpd_id = $user->refskpd_id;

            // Cari program dari sakip_cascadingprogram berdasarkan refperiode_id dan refskpd_id, ambil hanya yang unik berdasarkan refprogram_id
            $kegiatanList = SakipCascadingkegiatan::find()
                ->joinWith('refKegiatan') // Relation ke sakip_kegiatan
                ->where(['sakip_cascadingkegiatan.refskpd_id' => $refskpd_id, 'sakip_cascadingkegiatan.refperiode_id' => $refperiode_id])
                ->groupBy('sakip_cascadingkegiatan.refkegiatan_id') // Untuk mengelompokkan berdasarkan refkegiatan_id
                ->all();

            if ($kegiatanList) {
                // Bangun dropdown options
                $dropdown = "<option value=''>Pilih Kegiatan</option>";
                foreach ($kegiatanList as $kegiatan) {
                    $kodeKegiatan = $kegiatan->refKegiatan->kode_kegiatan;
                    $namaKegiatan = $kegiatan->refKegiatan->nama_kegiatan;
                    $dropdown .= "<option value='{$kegiatan->refkegiatan_id}'>{$kodeKegiatan} - {$namaKegiatan}</option>";
                }


                return $this->asJson([
                    'success' => true,
                    'dropdown' => $dropdown
                ]);
            } else {
                return $this->asJson([
                    'success' => false,
                    'message' => 'No programs found for the selected period.'
                ]);
            }
        }
    }



    public function actionGetCascadingkegiatanByKegiatan()
    {
        if (Yii::$app->request->isAjax) {
            $refkegiatan_id = Yii::$app->request->post('refkegiatan_id');
            $refperiode_id = Yii::$app->request->post('refperiode_id'); // ambil refperiode_id juga dari form
            $refskpd_id = Yii::$app->user->identity->refskpd_id; // Ambil refskpd_id dari user saat ini

            // Cari data cascading kegiatan berdasarkan refkegiatan_id
            $cascadingKegiatans = SakipCascadingkegiatan::find()
                ->where(['refkegiatan_id' => $refkegiatan_id])
                ->andWhere(['refskpd_id' => $refskpd_id])
                ->andWhere(['refperiode_id' => $refperiode_id]) // tambahkan ini
                ->all();

            if ($cascadingKegiatans) {
                $indicatorDropdown = "<option value=''>Select Indicator</option>";
                // $refcascadingkegiatan_id = null;
                $refprogram_id = null;
                $sasaranRenstraText = '';

                foreach ($cascadingKegiatans as $kegiatan) {
                    $uraianSasaran = $kegiatan->refSasaranRenstra ? $kegiatan->refSasaranRenstra->uraian_sasaranrenstra : 'Tidak Ada Sasaran';

                    $indicatorDropdown .= "<option value='{$kegiatan->refcascadingkegiatan_id}'>{$uraianSasaran} - {$kegiatan->uraian_indikatorkegiatan}</option>";

                    // Ambil refprogram_id dari kegiatan terkait
                    if ($kegiatan->refprogram_id) {
                        $refprogram_id = $kegiatan->refprogram_id; // Ambil refprogram_id dari objek kegiatan
                        $sasaranRenstraText = $uraianSasaran;
                    }

                    // Ambil hanya satu ID pertama untuk dikembalikan
                    // if (!$refcascadingkegiatan_id) {
                    //     $refcascadingkegiatan_id = $kegiatan->refcascadingkegiatan_id;
                    //     $sasaranRenstraText = $uraianSasaran;
                    // }
                }

                return $this->asJson([
                    'success' => true,
                    'indicatorDropdown' => $indicatorDropdown,
                    // 'refcascadingkegiatan_id' => $refcascadingkegiatan_id, // ID pertama yang ditemukan
                    'refprogram_id' => $refprogram_id, // Kembalikan refprogram_id
                    'sasaranRenstra' => $sasaranRenstraText, // Uraian sasaran pertama yang ditemukan
                ]);
            } else {
                return $this->asJson([
                    'success' => false,
                    'message' => 'No cascading activities found for the selected activity.'
                ]);
            }
        }
    }

    public function actionGetCascadingprogramByKegiatan()
    {
        if (Yii::$app->request->isAjax) {
            $refcascadingkegiatan_id = Yii::$app->request->post('refcascadingkegiatan_id');

            // Fetch the cascading kegiatan data
            $cascadingKegiatan = SakipCascadingkegiatan::findOne(['refcascadingkegiatan_id' => $refcascadingkegiatan_id]);

            if ($cascadingKegiatan) {
                return $this->asJson([
                    'success' => true,
                    'refcascadingprogram_id' => $cascadingKegiatan->refcascadingprogram_id,
                    'refsasaranrenstra_id' => $cascadingKegiatan->refsasaranrenstra_id,
                    'uraian_sasarankegiatan' => $cascadingKegiatan->uraian_sasarankegiatan,
                    'refindikatorsasaranrenstra_id' => $cascadingKegiatan->refindikatorsasaranrenstra_id,
                    'uraian_indikatorkegiatan' => $cascadingKegiatan->uraian_indikatorkegiatan,
                ]);
            } else {
                return $this->asJson(['success' => false, 'message' => 'Cascading Kegiatan not found.']);
            }
        }

        throw new BadRequestHttpException('Invalid request.');
    }




    public function actionGetSubkegiatanByKegiatan()
    {
        if (Yii::$app->request->isAjax) {
            $refkegiatan_id = Yii::$app->request->post('refkegiatan_id');

            // Cari data sub kegiatan berdasarkan refkegiatan_id
            $subkegiatanList = SakipSubkegiatan::find()
                ->where(['refkegiatan_id' => $refkegiatan_id])
                ->all();

            if ($subkegiatanList) {
                // Bangun dropdown options
                $dropdown = "<option value=''>Pilih Sub Kegiatan</option>";
                foreach ($subkegiatanList as $subkegiatan) {
                    $kodeSubkegiatan = $subkegiatan->kode_subkegiatan;
                    $namaSubkegiatan = $subkegiatan->nama_subkegiatan;
                    $dropdown .= "<option value='{$subkegiatan->refsubkegiatan_id}'>{$kodeSubkegiatan} - {$namaSubkegiatan}</option>";
                }

                return $this->asJson([
                    'success' => true,
                    'dropdown' => $dropdown
                ]);
            } else {
                return $this->asJson([
                    'success' => false,
                    'message' => 'No activities found for the selected program.'
                ]);
            }
        }
    }


    /**
     * Deletes an existing SakipCascadingsubkegiatan model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $refcascadingsubkegiatan_id Refcascadingsubkegiatan ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($refcascadingsubkegiatan_id)
    {
        // Temukan model sakip_cascadingprogram berdasarkan refcascadingsubkegiatan_id
        $model = $this->findModel($refcascadingsubkegiatan_id);

        if ($model) {
            // Hapus data di sakip_indikatorcascadingprogram terlebih dahulu
            $indikatorSubkegiatans = SakipIndikatorcascadingsubkegiatan::find()
                ->where(['refcascadingsubkegiatan_id' => $refcascadingsubkegiatan_id])
                ->all();

            foreach ($indikatorSubkegiatans as $indikatorSubkegiatan) {
                // Hapus data di sakip_indikatorcascadingprogram_triwulan
                SakipIndikatorcascadingsubkegiatanTriwulan::deleteAll(['refindikatorsubkegiatan_id' => $indikatorSubkegiatan->refindikatorsubkegiatan_id]);

                // Hapus data di sakip_indikatorcascadingprogram
                $indikatorSubkegiatan->delete();
            }

            // Ambil refperiode_id sebelum menghapus model
            $refperiode_id = $model->refperiode_id;
            // Setelah semua data terkait dihapus, hapus sakip_cascadingprogram
            $model->delete();

            // Redirect ke index setelah penghapusan berhasil
            Yii::$app->session->setFlash('success', 'Data berhasil dihapus.');
        } else {
            Yii::$app->session->setFlash('error', 'Data tidak ditemukan.');
        }

        // Redirect kembali ke index dengan refperiode_id
        return $this->redirect(['index', 'refperiode_id' => $refperiode_id]);
    }

    /**
     * Finds the SakipCascadingsubkegiatan model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $refcascadingsubkegiatan_id Refcascadingsubkegiatan ID
     * @return SakipCascadingsubkegiatan the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($refcascadingsubkegiatan_id)
    {
        if (($model = SakipCascadingsubkegiatan::findOne(['refcascadingsubkegiatan_id' => $refcascadingsubkegiatan_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
