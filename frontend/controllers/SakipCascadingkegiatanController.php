<?php

namespace frontend\controllers;

use Yii;
use frontend\models\SakipCascadingkegiatan;
use frontend\models\search\SakipCascadingkegiatanSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\BadRequestHttpException;
use yii\filters\VerbFilter;
use frontend\models\SakipCascadingprogram;
use frontend\models\search\SakipCascadingprogramSearch;
use frontend\models\SakipKebijakan;
use frontend\models\search\SakipKebijakanSearch;
use frontend\models\SakipStrategi;
use frontend\models\search\SakipStrategiSearch;
use frontend\models\SakipIndikatortujuanrenstra;
use frontend\models\SakipIndikatorsasaranrenstra;
use frontend\models\SakipIndikatorcascadingkegiatan;
use frontend\models\SakipIndikatorcascadingkegiatanTriwulan;
use frontend\models\SakipSkpd;
use frontend\models\SakipPeriode;
use frontend\models\SakipSasaran;
use frontend\models\SakipMisi;
use frontend\models\SakipBidang;
use frontend\models\SakipProgram;
use frontend\models\SakipKegiatan;
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
 * SakipCascadingkegiatanController implements the CRUD actions for SakipCascadingkegiatan model.
 */
class SakipCascadingkegiatanController extends Controller
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
     * Lists all SakipCascadingkegiatan models.
     *
     * @return string
     */
    public function actionIndex($refperiode_id = null)
    {
        $searchModel = new SakipCascadingkegiatanSearch();

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

        $query = SakipCascadingkegiatan::find()
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
    //     $searchModel = new SakipCascadingkegiatanSearch();

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
    //     $query = SakipCascadingkegiatan::find()->where(['refskpd_id' => $refskpd_id]);

    //     if ($refperiode_id !== null) {
    //         $query->andWhere(['refperiode_id' => $refperiode_id]);
    //     }

    //     // Execute query and get data
    //     $cascadingKegiatans = $query->all(); // Ambil data cascading program

    //     // Add a flag to check if data is empty
    //     $dataEmpty = empty($cascadingKegiatans);

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
    //         'cascadingKegiatans' => $cascadingKegiatans, // Send the queried data
    //     ]);
    // }

    public function actionIndexDev($refperiode_id = null, $refskpd_id = null)
    {
        $searchModel = new SakipCascadingkegiatanSearch();
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

        // Ambil data dari sakip_cascadingkegiatan sesuai dengan refskpd_id user dan refperiode_id (jika ada)
        $query = SakipCascadingkegiatan::find()->where(['refskpd_id' => $refskpd_id]);

        if ($refperiode_id !== null) {
            $query->andWhere(['refperiode_id' => $refperiode_id]);
        }

        // Execute query and get data
        $cascadingKegiatans = $query->all();

        // Add a flag to check if data is empty
        $dataEmpty = empty($cascadingKegiatans);

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
            'cascadingKegiatans' => $cascadingKegiatans, // Send the queried data
        ]);
    }

    /**
     * Displays a single SakipCascadingkegiatan model.
     * @param int $refcascadingkegiatan_id Refcascadingkegiatan ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($refcascadingkegiatan_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($refcascadingkegiatan_id),
        ]);
    }

    /**
     * Creates a new SakipCascadingkegiatan model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new SakipCascadingkegiatan();

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
                $model->kegiatan_target = str_replace(',', '.', $model->kegiatan_target);
                // Coba untuk menyimpan model sakip_cascadingprogram
                if ($model->save()) {
                    // Langkah 1: Buat entri untuk sakip_indikatorcascadingprogram
                    $indikatorKegiatanModel = new SakipIndikatorCascadingkegiatan();
                    $indikatorKegiatanModel->refcascadingprogram_id = $model->refcascadingprogram_id;
                    $indikatorKegiatanModel->refcascadingkegiatan_id = $model->refcascadingkegiatan_id;
                    $indikatorKegiatanModel->refsasaranrenstra_id = $model->refsasaranrenstra_id;
                    $indikatorKegiatanModel->refindikatorsasaranrenstra_id = $model->refindikatorsasaranrenstra_id;
                    $indikatorKegiatanModel->refskpd_id = $model->refskpd_id;
                    $indikatorKegiatanModel->refperiode_id = $model->refperiode_id;
                    $indikatorKegiatanModel->refprogram_id = $model->refprogram_id;
                    $indikatorKegiatanModel->refkegiatan_id = $model->refkegiatan_id;
                    $indikatorKegiatanModel->target_rkt = $model->kegiatan_target; // Mengisi target_rkt dari program_target
                    // Konversi koma menjadi titik pada target_rkt sebelum menyimpan
                    $indikatorKegiatanModel->target_rkt = str_replace(',', '.', $indikatorKegiatanModel->target_rkt);

                    if ($indikatorKegiatanModel->save()) {
                        // Langkah 2: Buat entri untuk sakip_indikatorcascadingprogram_triwulan
                        for ($i = 1; $i <= 4; $i++) {
                            $triwulanModel = new SakipIndikatorCascadingkegiatanTriwulan();
                            $triwulanModel->refindikatorkegiatan_id = $indikatorKegiatanModel->refindikatorkegiatan_id;
                            $triwulanModel->refcascadingprogram_id = $model->refcascadingprogram_id;
                            $triwulanModel->refcascadingkegiatan_id = $model->refcascadingkegiatan_id;
                            $triwulanModel->refsasaranrenstra_id = $model->refsasaranrenstra_id;
                            $triwulanModel->refindikatorsasaranrenstra_id = $model->refindikatorsasaranrenstra_id;
                            $triwulanModel->refskpd_id = $model->refskpd_id;
                            $triwulanModel->refperiode_id = $model->refperiode_id;
                            $triwulanModel->refprogram_id = $model->refprogram_id;
                            $triwulanModel->refkegiatan_id = $model->refkegiatan_id;
                            $triwulanModel->triwulan_target_rkt = $model->kegiatan_target;
                            $triwulanModel->reftriwulan_id = $i; // Isi reftriwulan_id sesuai dengan loop (1, 2, 3, 4)
                            // Konversi koma menjadi titik pada triwulan_target_rkt sebelum menyimpan
                            $triwulanModel->triwulan_target_rkt = str_replace(',', '.', $triwulanModel->triwulan_target_rkt);
                            // Simpan triwulan model
                            if (!$triwulanModel->save()) {
                                // Log error jika tidak bisa menyimpan data triwulan
                                Yii::error("Error saving triwulan data for triwulan $i: " . json_encode($triwulanModel->getErrors()));
                                return $this->asJson(['success' => false, 'errors' => array_merge($model->getErrors(), $indikatorKegiatanModel->getErrors(), $triwulanModel->getErrors())]);
                            }
                        }
                    } else {
                        Yii::error("Error saving indikator kegiatan: " . json_encode($indikatorKegiatanModel->getErrors()));
                        return $this->asJson(['success' => false, 'errors' => $indikatorKegiatanModel->getErrors()]);
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




    public function actionGetProgramByPeriode()
    {
        if (Yii::$app->request->isAjax) {
            $refperiode_id = Yii::$app->request->post('refperiode_id');
            $user = Yii::$app->user->identity;
            $refskpd_id = $user->refskpd_id;

            // Cari program dari sakip_cascadingprogram berdasarkan refperiode_id dan refskpd_id, ambil hanya yang unik berdasarkan refprogram_id
            $programList = SakipCascadingprogram::find()
                ->joinWith('refProgram') // Relation ke sakip_program
                ->where(['sakip_cascadingprogram.refskpd_id' => $refskpd_id, 'sakip_cascadingprogram.refperiode_id' => $refperiode_id])
                ->groupBy('sakip_cascadingprogram.refprogram_id') // Untuk mengelompokkan berdasarkan refprogram_id
                ->all();

            if ($programList) {
                // Bangun dropdown options dengan kode_program - nama_program
                $dropdown = "<option value=''>Pilih Program</option>";
                foreach ($programList as $program) {
                    $kode_program = $program->refProgram->kode_program; // Pastikan kode_program ada dalam relasi refProgram
                    $nama_program = $program->refProgram->nama_program; // Pastikan nama_program ada dalam relasi refProgram
                    $dropdown .= "<option value='{$program->refprogram_id}'>{$kode_program} - {$nama_program}</option>";
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



    public function actionGetCascadingprogramByProgram()
    {
        if (Yii::$app->request->isAjax) {
            $refprogram_id = Yii::$app->request->post('refprogram_id');
            $refperiode_id = Yii::$app->request->post('refperiode_id'); // ambil refperiode_id juga dari form
            $refskpd_id = Yii::$app->user->identity->refskpd_id;

            $cascadingPrograms = SakipCascadingprogram::find()
                ->where(['refprogram_id' => $refprogram_id])
                ->andWhere(['refskpd_id' => $refskpd_id])
                ->andWhere(['refperiode_id' => $refperiode_id]) // tambahkan ini
                ->all();

            if ($cascadingPrograms) {
                $indicatorDropdown = "<option value=''>Select Indicator</option>";
                $refcascadingprogram_id = null;
                $sasaranRenstraText = '';

                foreach ($cascadingPrograms as $program) {
                    $uraianSasaran = $program->sasaranRenstra ? $program->sasaranRenstra->uraian_sasaranrenstra : 'Tidak Ada Sasaran';

                    // Tambahkan refsasaranrenstra ke setiap indikator
                    $indicatorDropdown .= "<option value='{$program->refcascadingprogram_id}'>(Sasaran Renstra){$uraianSasaran} - (Sasaran Program){$program->uraian_sasaranprogram} - (Indikator Program){$program->uraian_indikatorprogram}</option>";

                    // Ambil hanya satu ID pertama untuk dikembalikan
                    if (!$refcascadingprogram_id) {
                        $refcascadingprogram_id = $program->refcascadingprogram_id;
                        $sasaranRenstraText = $uraianSasaran;
                    }
                }

                return $this->asJson([
                    'success' => true,
                    'refcascadingprogram_id' => $refcascadingprogram_id, // ID pertama yang ditemukan
                    'sasaranRenstra' => $sasaranRenstraText, // Uraian sasaran pertama yang ditemukan
                    'indicatorDropdown' => $indicatorDropdown // Dropdown indikator dengan uraian sasaran
                ]);
            } else {
                return $this->asJson([
                    'success' => false,
                    'message' => 'No cascading program found for the selected program.'
                ]);
            }
        }
    }


    public function actionGetSasaranrenstraByCascadingprogram()
    {
        if (Yii::$app->request->isAjax) {
            $refcascadingprogram_id = Yii::$app->request->post('refcascadingprogram_id');

            // Cari cascading program berdasarkan ID
            $cascadingProgram = SakipCascadingprogram::findOne(['refcascadingprogram_id' => $refcascadingprogram_id]);

            if ($cascadingProgram) {
                return $this->asJson([
                    'success' => true,
                    'refsasaranrenstra_id' => $cascadingProgram->refsasaranrenstra_id, // Mengirimkan refsasaranrenstra_id
                    'uraian_sasaranrenstra' => $cascadingProgram->uraian_sasaranprogram, // Mengirimkan uraian sasaran renstra
                ]);
            } else {
                return $this->asJson([
                    'success' => false,
                    'message' => 'No cascading program found for the selected program.'
                ]);
            }
        }
    }

    public function actionGetIndikatorsasaranrenstraByCascadingprogram()
    {
        if (Yii::$app->request->isAjax) {
            $refcascadingprogram_id = Yii::$app->request->post('refcascadingprogram_id');

            // Find the cascading program
            $cascadingProgram = SakipCascadingprogram::findOne(['refcascadingprogram_id' => $refcascadingprogram_id]);

            if ($cascadingProgram) {
                // Assuming the relationship is set in the SakipCascadingprogram model
                $indikator = SakipIndikatorsasaranrenstra::find()
                    ->where(['refindikatorsasaranrenstra_id' => $cascadingProgram->refindikatorsasaranrenstra_id])
                    ->one();

                return $this->asJson([
                    'success' => true,
                    'refindikatorsasaranrenstra_id' => $indikator ? $indikator->refindikatorsasaranrenstra_id : null,
                    'uraian_indikatorsasaranrenstra' => $indikator ? $indikator->uraian_indikatorsasaranrenstra : 'Indikator tidak ditemukan'
                ]);
            } else {
                return $this->asJson([
                    'success' => false,
                    'message' => 'No cascading program found for the selected program.'
                ]);
            }
        }
    }




    public function actionGetKegiatanByProgram()
    {
        if (Yii::$app->request->isAjax) {
            $refprogram_id = Yii::$app->request->post('refprogram_id');

            // Cari data kegiatan berdasarkan refprogram_id
            $kegiatanList = SakipKegiatan::find()
                ->where(['refprogram_id' => $refprogram_id])
                ->all();

            if ($kegiatanList) {
                // Bangun dropdown options
                $dropdown = "<option value=''>Select Kegiatan</option>";
                foreach ($kegiatanList as $kegiatan) {
                    // Combine kode_kegiatan and nama_kegiatan
                    $dropdown .= "<option value='{$kegiatan->refkegiatan_id}'>{$kegiatan->kode_kegiatan} - {$kegiatan->nama_kegiatan}</option>";
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
     * Updates an existing SakipCascadingkegiatan model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $refcascadingkegiatan_id Refcascadingkegiatan ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($refcascadingkegiatan_id)
    {
        $model = $this->findModel($refcascadingkegiatan_id);

        // Ambil data dari sakip_periode
        $periodeList = SakipPeriode::find()->all(); // Query to get all periods

        if (Yii::$app->request->isAjax) {
            if ($model->load(Yii::$app->request->post())) {
                // Konversi koma ke titik untuk kegiatan_target sebelum disimpan
                $model->kegiatan_target = str_replace(',', '.', $model->kegiatan_target);
                // Simpan perubahan di sakip_cascadingprogram
                if ($model->save()) {
                    // Jika ada perubahan pada program_target, update target_rkt dan triwulan_target_rkt
                    $newKegiatanTarget = $model->kegiatan_target;

                    // Update sakip_indikatorcascadingprogram
                    $indikatorKegiatans = SakipIndikatorcascadingkegiatan::find()
                        ->where(['refcascadingkegiatan_id' => $model->refcascadingkegiatan_id])
                        ->all();

                    foreach ($indikatorKegiatans as $indikatorKegiatan) {
                        $indikatorKegiatan->target_rkt = $newKegiatanTarget; // Update target_rkt
                        if (!$indikatorKegiatan->save()) {
                            Yii::error("Error updating indikator kegiatan: " . json_encode($indikatorKegiatan->getErrors()));
                            return $this->asJson(['success' => false, 'errors' => $indikatorKegiatan->getErrors()]);
                        }

                        // Update sakip_indikatorcascadingprogram_triwulan
                        for ($i = 1; $i <= 4; $i++) {
                            $triwulanModel = SakipIndikatorcascadingkegiatanTriwulan::findOne([
                                'refindikatorkegiatan_id' => $indikatorKegiatan->refindikatorkegiatan_id,
                                'reftriwulan_id' => $i,
                            ]);

                            if ($triwulanModel) {
                                $triwulanModel->triwulan_target_rkt = $newKegiatanTarget; // Update triwulan_target_rkt
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
                'periodeList' => $periodeList, // Pass periodeList to form
            ]);
        }

        return $this->render('update', [
            'model' => $model,
            'periodeList' => $periodeList, // Pass periodeList to form
        ]);
    }




    /**
     * Deletes an existing SakipCascadingkegiatan model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $refcascadingkegiatan_id Refcascadingkegiatan ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($refcascadingkegiatan_id)
    {
        // Temukan model sakip_cascadingprogram berdasarkan refcascadingkegiatan_id
        $model = $this->findModel($refcascadingkegiatan_id);

        if ($model) {
            // Hapus data di sakip_indikatorcascadingprogram terlebih dahulu
            $indikatorKegiatans = SakipIndikatorcascadingkegiatan::find()
                ->where(['refcascadingkegiatan_id' => $refcascadingkegiatan_id])
                ->all();

            foreach ($indikatorKegiatans as $indikatorKegiatan) {
                // Hapus data di sakip_indikatorcascadingprogram_triwulan
                SakipIndikatorcascadingkegiatanTriwulan::deleteAll(['refindikatorkegiatan_id' => $indikatorKegiatan->refindikatorkegiatan_id]);

                // Hapus data di sakip_indikatorcascadingprogram
                $indikatorKegiatan->delete();
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
     * Finds the SakipCascadingkegiatan model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $refcascadingkegiatan_id Refcascadingkegiatan ID
     * @return SakipCascadingkegiatan the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($refcascadingkegiatan_id)
    {
        if (($model = SakipCascadingkegiatan::findOne(['refcascadingkegiatan_id' => $refcascadingkegiatan_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
