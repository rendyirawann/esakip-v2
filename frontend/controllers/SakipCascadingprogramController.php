<?php

namespace frontend\controllers;

use Yii;
use frontend\models\SakipCascadingprogram;
use frontend\models\SakipCascadingkegiatan;
use frontend\models\SakipCascadingsubkegiatan;
use frontend\models\search\SakipCascadingprogramSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use frontend\models\SakipKebijakan;
use frontend\models\search\SakipKebijakanSearch;
use frontend\models\SakipStrategi;
use frontend\models\search\SakipStrategiSearch;
use frontend\models\SakipIndikatortujuanrenstra;
use frontend\models\SakipIndikatorsasaranrenstra;
use frontend\models\SakipIndikatorcascadingprogram;
use frontend\models\SakipPenjabatSkpd;
use frontend\models\SakipIndikatorcascadingprogramTriwulan;
use frontend\models\SakipSkpd;
use frontend\models\SakipPeriode;
use frontend\models\SakipSasaran;
use frontend\models\SakipMisi;
use frontend\models\SakipBidang;
use frontend\models\SakipProgram;
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
use yii\filters\AccessControl;
use yii\web\ForbiddenHttpException;

/**
 * SakipCascadingprogramController implements the CRUD actions for SakipCascadingprogram model.
 */
class SakipCascadingprogramController extends Controller
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
     * Lists all SakipCascadingprogram models.
     *
     * @return string
     */
    public function actionIndex($refperiode_id = null)
    {
        $searchModel = new SakipCascadingprogramSearch();

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

        $query = SakipCascadingprogram::find()
            ->where(['refperiode_id' => $refperiode_id])
            ->andWhere(['refskpd_id' => Yii::$app->user->identity->refskpd_id]);


        // Initialize the data provider
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
        ]);

        // $dataProvider = $searchModel->search($this->request->queryParams);
        // $dataProvider->pagination = false;

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

        // Cek apakah data penjabat untuk SKPD & periode saat ini ada
        $penjabatExists = SakipPenjabatskpd::find()
            ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
            ->exists();


        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'nama_skpd' => $nama_skpd,
            'periodeList' => $periodeList,
            'selectedPeriodId' => $refperiode_id,
            'selectedPeriodValue' => $selectedPeriodValue, // Include selected period value
            'penjabatExists' => $penjabatExists, // kirim status ketersediaan data
        ]);
    }

    // public function actionIndexDev($refperiode_id = null, $refskpd_id = null)
    // {
    //     $searchModel = new SakipCascadingprogramSearch();

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
    //     $query = SakipCascadingprogram::find()->where(['refskpd_id' => $refskpd_id]);

    //     if ($refperiode_id !== null) {
    //         $query->andWhere(['refperiode_id' => $refperiode_id]);
    //     }

    //     // Execute query and get data
    //     $cascadingPrograms = $query->all(); // Ambil data cascading program

    //     // Add a flag to check if data is empty
    //     $dataEmpty = empty($cascadingPrograms);

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
    //         'cascadingPrograms' => $cascadingPrograms, // Send the queried data
    //     ]);
    // }

    public function actionIndexDev($refperiode_id = null, $refskpd_id = null)
    {
        $searchModel = new SakipCascadingprogramSearch();
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
        $query = SakipCascadingprogram::find()->where(['refskpd_id' => $refskpd_id]);

        if ($refperiode_id !== null) {
            $query->andWhere(['refperiode_id' => $refperiode_id]);
        }

        // Execute query and get data
        $cascadingPrograms = $query->all(); // Ambil data cascading program

        // Add a flag to check if data is empty
        $dataEmpty = empty($cascadingPrograms);

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
            'cascadingPrograms' => $cascadingPrograms, // Send the queried data
        ]);
    }


    /**
     * Displays a single SakipCascadingprogram model.
     * @param int $refcascadingprogram_id Refcascadingprogram ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($refcascadingprogram_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($refcascadingprogram_id),
        ]);
    }

    /**
     * Creates a new SakipCascadingprogram model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate($refperiode_id = null)
    {
        $model = new SakipCascadingprogram();

        // Ambil refskpd_id dari user saat ini
        $user = Yii::$app->user->identity;
        $refskpd_id = $user->refskpd_id;

        // Ambil data dari sakip_bidang
        $bidangList = SakipBidang::find()->where(['bidang_isaktif' => 'T'])->all();

        // Ambil data dari sakip_periode
        $periodeList = SakipPeriode::find()
            ->where(['periode_isaktif' => 'T'])
            ->all();

        if (Yii::$app->request->isAjax) {
            if ($model->load(Yii::$app->request->post())) {
                // Konversi koma menjadi titik pada program_target sebelum menyimpan
                $model->program_target = str_replace(',', '.', $model->program_target);

                // Coba untuk menyimpan model sakip_cascadingprogram
                if ($model->save()) {
                    // Langkah 1: Buat entri untuk sakip_indikatorcascadingprogram
                    $indikatorProgramModel = new SakipIndikatorcascadingprogram();
                    $indikatorProgramModel->refcascadingprogram_id = $model->refcascadingprogram_id;
                    $indikatorProgramModel->refsasaranrenstra_id = $model->refsasaranrenstra_id;
                    $indikatorProgramModel->refskpd_id = $model->refskpd_id;
                    $indikatorProgramModel->refperiode_id = $model->refperiode_id;
                    $indikatorProgramModel->refbidang_id = $model->refbidang_id;
                    $indikatorProgramModel->refprogram_id = $model->refprogram_id;
                    $indikatorProgramModel->target_rkt = $model->program_target; // Mengisi target_rkt dari program_target
                    // Konversi koma menjadi titik pada target_rkt sebelum menyimpan
                    $indikatorProgramModel->target_rkt = str_replace(',', '.', $indikatorProgramModel->target_rkt);

                    if ($indikatorProgramModel->save()) {
                        // Langkah 2: Buat entri untuk sakip_indikatorcascadingprogram_triwulan
                        for ($i = 1; $i <= 4; $i++) {
                            $triwulanModel = new SakipIndikatorcascadingprogramTriwulan();
                            $triwulanModel->refindikatorprogram_id = $indikatorProgramModel->refindikatorprogram_id;
                            $triwulanModel->refcascadingprogram_id = $model->refcascadingprogram_id;
                            $triwulanModel->refsasaranrenstra_id = $model->refsasaranrenstra_id;
                            $triwulanModel->refskpd_id = $model->refskpd_id;
                            $triwulanModel->refperiode_id = $model->refperiode_id;
                            $triwulanModel->refbidang_id = $model->refbidang_id;
                            $triwulanModel->refprogram_id = $model->refprogram_id;
                            $triwulanModel->triwulan_target_rkt = $model->program_target;
                            $triwulanModel->reftriwulan_id = $i; // Isi reftriwulan_id sesuai dengan loop (1, 2, 3, 4)
                            // Konversi koma menjadi titik pada triwulan_target_rkt sebelum menyimpan
                            $triwulanModel->triwulan_target_rkt = str_replace(',', '.', $triwulanModel->triwulan_target_rkt);
                            // Simpan triwulan model
                            if (!$triwulanModel->save()) {
                                // Log error jika tidak bisa menyimpan data triwulan
                                Yii::error("Error saving triwulan data for triwulan $i: " . json_encode($triwulanModel->getErrors()));
                                return $this->asJson(['success' => false, 'errors' => array_merge($model->getErrors(), $indikatorProgramModel->getErrors(), $triwulanModel->getErrors())]);
                            }
                        }
                    } else {
                        Yii::error("Error saving indikator program: " . json_encode($indikatorProgramModel->getErrors()));
                        return $this->asJson(['success' => false, 'errors' => $indikatorProgramModel->getErrors()]);
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
                'bidangList' => $bidangList,
                'periodeList' => $periodeList, // Pass periodeList to form
            ]);
        }

        return $this->render('create', [
            'model' => $model,
            'bidangList' => $bidangList,
            'periodeList' => $periodeList, // Pass periodeList to form
        ]);
    }




    public function actionGetSasaranRenstra($refperiode_id)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        // Ambil refskpd_id dari user saat ini
        $user = Yii::$app->user->identity;
        $refskpd_id = $user->refskpd_id;

        // Ambil data dari sakip_sasaranrenstra berdasarkan refperiode_id dan refskpd_id
        $sasaranList = SakipSasaranrenstra::find()
            ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
            ->all();

        return ArrayHelper::map($sasaranList, 'refsasaranrenstra_id', 'uraian_sasaranrenstra');
    }

    public function actionGetIndikatorSasaranRenstra($refsasaranrenstra_id)
    {
        // Fetch indicators for the selected Sasaran Renstra
        $indikatorList = SakipIndikatorsasaranrenstra::find()
            ->where(['refsasaranrenstra_id' => $refsasaranrenstra_id])
            ->all();

        // Convert data to array to be used in the dropdown
        $data = ArrayHelper::map($indikatorList, 'refindikatorsasaranrenstra_id', 'uraian_indikatorsasaranrenstra');

        return $this->asJson($data);
    }


    public function actionGetAssociatedValues($refsasaranrenstra_id)
    {
        // Fetch the associated records based on refsasaranrenstra_id
        $sasaranRenstra = SakipSasaranrenstra::findOne($refsasaranrenstra_id);

        if ($sasaranRenstra) {
            return $this->asJson([
                'refsasaran_id' => $sasaranRenstra->refsasaran_id,
                'reftujuan_id' => $sasaranRenstra->reftujuan_id, // Ensure these relationships are set
                'refmisi_id' => $sasaranRenstra->refmisi_id,
                'refsasaran_uraian' => $sasaranRenstra->uraian_sasaranrenstra,
                'reftujuan_uraian' => $sasaranRenstra->tujuan->uraian_tujuan ?? '', // Adjust if relationship exists
                'refmisi_uraian' => $sasaranRenstra->misi->uraian_misi ?? '', // Adjust if relationship exists
            ]);
        }

        return $this->asJson([]);
    }



    public function actionGetPrograms($bidang_id)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        // Fetch programs based on refbidang_id and only active ones
        $programs = SakipProgram::find()
            ->where(['refbidang_id' => $bidang_id, 'program_isaktif' => 'T'])
            ->all();

        // Map the results so that the key is refprogram_id and the value is a combination of kode_program and nama_program
        $programList = \yii\helpers\ArrayHelper::map($programs, 'refprogram_id', function ($model) {
            return $model->kode_program . ' - ' . $model->nama_program;
        });

        return $programList;
    }

    /**
     * Updates an existing SakipCascadingprogram model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $refcascadingprogram_id Refcascadingprogram ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($refcascadingprogram_id)
    {
        $model = $this->findModel($refcascadingprogram_id);

        if (Yii::$app->request->isAjax) {
            if ($model->load(Yii::$app->request->post())) {

                // Konversi koma ke titik untuk program_target sebelum disimpan
                $model->program_target = str_replace(',', '.', $model->program_target);
                // Simpan perubahan di sakip_cascadingprogram
                if ($model->save()) {
                    // Jika ada perubahan pada program_target, update target_rkt dan triwulan_target_rkt
                    $newProgramTarget = $model->program_target;

                    // Update sakip_indikatorcascadingprogram
                    $indikatorPrograms = SakipIndikatorcascadingprogram::find()
                        ->where(['refcascadingprogram_id' => $model->refcascadingprogram_id])
                        ->all();

                    foreach ($indikatorPrograms as $indikatorProgram) {
                        $indikatorProgram->target_rkt = $newProgramTarget; // Update target_rkt
                        if (!$indikatorProgram->save()) {
                            Yii::error("Error updating indikator program: " . json_encode($indikatorProgram->getErrors()));
                            return $this->asJson(['success' => false, 'errors' => $indikatorProgram->getErrors()]);
                        }

                        // Update sakip_indikatorcascadingprogram_triwulan
                        for ($i = 1; $i <= 4; $i++) {
                            $triwulanModel = SakipIndikatorcascadingprogramTriwulan::findOne([
                                'refindikatorprogram_id' => $indikatorProgram->refindikatorprogram_id,
                                'reftriwulan_id' => $i,
                            ]);

                            if ($triwulanModel) {
                                $triwulanModel->triwulan_target_rkt = $newProgramTarget; // Update triwulan_target_rkt
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



    /**
     * Deletes an existing SakipCascadingprogram model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $refcascadingprogram_id Refcascadingprogram ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($refcascadingprogram_id)
    {
        // Temukan model sakip_cascadingprogram berdasarkan refcascadingprogram_id
        $model = $this->findModel($refcascadingprogram_id);

        if ($model) {
            // Ambil refperiode_id lebih awal supaya tersedia di semua alur
            $refperiode_id = $model->refperiode_id;

            // Cek apakah ada referensi di sakip_cascadingkegiatan
            $kegiatanCount = SakipCascadingkegiatan::find()
                ->where(['refcascadingprogram_id' => $refcascadingprogram_id])
                ->count();

            // Cek apakah ada referensi di sakip_cascadingsubkegiatan
            $subkegiatanCount = SakipCascadingsubkegiatan::find()
                ->where(['refcascadingprogram_id' => $refcascadingprogram_id])
                ->count();

            if ($kegiatanCount > 0 || $subkegiatanCount > 0) {
                // Tidak bisa dihapus karena masih ada referensi
                Yii::$app->session->setFlash('error', 'Tidak dapat menghapus data karena masih ada referensi di kegiatan atau subkegiatan.');
            } else {
                // Hapus data di sakip_indikatorcascadingprogram terlebih dahulu
                $indikatorPrograms = SakipIndikatorcascadingprogram::find()
                    ->where(['refcascadingprogram_id' => $refcascadingprogram_id])
                    ->all();

                foreach ($indikatorPrograms as $indikatorProgram) {
                    // Hapus data di sakip_indikatorcascadingprogram_triwulan
                    SakipIndikatorcascadingprogramTriwulan::deleteAll(['refindikatorprogram_id' => $indikatorProgram->refindikatorprogram_id]);

                    // Hapus data di sakip_indikatorcascadingprogram
                    $indikatorProgram->delete();
                }

                // Hapus sakip_cascadingprogram
                $model->delete();

                Yii::$app->session->setFlash('success', 'Data berhasil dihapus.');
            }
        } else {
            Yii::$app->session->setFlash('error', 'Data tidak ditemukan.');
            // Redirect tanpa refperiode_id karena model tidak ada
            return $this->redirect(['index']);
        }

        // Redirect kembali ke index dengan refperiode_id
        return $this->redirect(['index', 'refperiode_id' => $refperiode_id]);
    }

    /**
     * Exports data to Excel and downloads the file automatically.
     *
     * @param int|null $refperiode_id Refperiode ID
     * @return \yii\web\Response
     */
    public function actionExportExcel($refperiode_id = null)
    {
        // Set default period to this year if not provided
        if ($refperiode_id === null) {
            $currentYear = date('Y');
            $defaultPeriod = SakipPeriode::find()->where(['periode' => $currentYear])->one();
            $refperiode_id = $defaultPeriod ? $defaultPeriod->refperiode_id : null;
        }

        // Fetch data based on refperiode_id and current user's refskpd_id
        $user = Yii::$app->user->identity;
        $refskpd_id = $user->refskpd_id;

        $data = SakipCascadingprogram::find()
            ->where(['refperiode_id' => $refperiode_id, 'refskpd_id' => $refskpd_id])
            ->all();

        // Prepare Excel file
        $objPHPExcel = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $objPHPExcel->getActiveSheet();

        // Set header row
        $sheet->setCellValue('A1', 'No')
            ->setCellValue('B1', 'Program Name')
            ->setCellValue('C1', 'Target')
            ->setCellValue('D1', 'Period');

        // Populate data rows
        $row = 2;
        foreach ($data as $index => $program) {
            $sheet->setCellValue("A{$row}", $index + 1)
                ->setCellValue("B{$row}", $program->refprogram_id)
                ->setCellValue("C{$row}", $program->refprogram_id)
                ->setCellValue("D{$row}", $program->periode->periode ?? '');
            $row++;
        }

        // Set filename
        $filename = 'Sakip_Cascading_Program_' . date('Ymd_His') . '.xlsx';

        // Send file to browser for download
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($objPHPExcel);
        $tempFile = tempnam(sys_get_temp_dir(), $filename);
        $writer->save($tempFile);

        return Yii::$app->response->sendFile($tempFile, $filename)->on(\yii\web\Response::EVENT_AFTER_SEND, function ($event) {
            unlink($event->data);
        }, $tempFile);
    }


    /**
     * Finds the SakipCascadingprogram model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $refcascadingprogram_id Refcascadingprogram ID
     * @return SakipCascadingprogram the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($refcascadingprogram_id)
    {
        if (($model = SakipCascadingprogram::findOne(['refcascadingprogram_id' => $refcascadingprogram_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
