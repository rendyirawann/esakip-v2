<?php

namespace frontend\controllers;

use Yii;
use frontend\models\SakipSkpd;
use frontend\models\SakipPeriode;
use frontend\models\SakipKegiatan;
use frontend\models\SakipPenanggungjawab;
use frontend\models\SakipPimpinan;
use frontend\models\SakipSasaranrenstra;
use frontend\models\SakipIndikatorsasaranrenstra;
use frontend\models\SakipIndikatorsasaranrenstraTriwulan;
use frontend\models\SakipStrategi;
use frontend\models\SakipKebijakan;
use frontend\models\SakipCascadingprogram;
use frontend\models\SakipCascadingkegiatan;
use frontend\models\SimonaCascadingkegiatan;
use frontend\models\SimonaMediacascadingkegiatan;
use frontend\models\SimonaKeluaranmediacascadingkegiatan;
use frontend\models\SimonaMediacascadingkegiatanOpd;
use frontend\models\SimonaMediacascadingsubkegiatan;
use frontend\models\SimonaKeluaranmediacascadingsubkegiatan;
use frontend\models\SimonaMediacascadingsubkegiatanOpd;
use frontend\models\search\SakipCascadingprogramSearch;
use frontend\models\search\SakipCascadingkegiatanSearch;
use frontend\models\search\SakipCascadingsubkegiatanSearch;
use frontend\models\SakipCascadingsubkegiatan;
use frontend\models\SakipPenjabatskpdCascadingprogram;
use frontend\models\SakipPenjabatskpdCascadingkegiatan;
use frontend\models\SakipPenjabatskpdCascadingsubkegiatan;
use frontend\models\SakipIndikatorcascadingprogram;
use frontend\models\SakipIndikatorcascadingsubkegiatan;
use frontend\models\SakipIndikatorcascadingsubkegiatanTriwulan;
use Moonland\Phpexcel\Excel;
use yii\web\Controller;
use yii\web\Response;
use yii\helpers\Html;
use kartik\mpdf\Pdf;
use kartik\export\ExportMenu; // Import ExportMenu
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;

class SimonaPerencanaanController extends Controller
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
        $this->layout = 'main-simona';

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

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'nama_skpd' => $nama_skpd,
            'periodeList' => $periodeList,
            'selectedPeriodId' => $refperiode_id,
            'selectedPeriodValue' => $selectedPeriodValue,
        ]);
    }

    public function actionIndexPegawai($refperiode_id = null, $refskpd_id = null)
    {
        $searchModel = new SakipCascadingprogramSearch();
        $this->layout = 'main-simona';

        // Inisialisasi variabel untuk dropdown dan data
        $periodeList = SakipPeriode::find()->all(); // Fetch all periods
        $refskpdList = [];
        $nama_skpd = null;
        $dataProvider = null;

        // Mendapatkan assignments user
        $assignments = Yii::$app->authManager->getAssignments(Yii::$app->user->getId());

        // Dapatkan user aktif
        $user = Yii::$app->user->identity;

        // Cari refpegawai_id
        $refpegawai_id = $user->refpegawai_id;

        // Cari daftar refskpd_id dari tabel sakip_penanggungjawab
        $skpdIds = SakipPenanggungjawab::find()
            ->select('refskpd_id')
            ->distinct()
            ->where(['refpegawai_id' => $refpegawai_id])
            ->asArray()
            ->column();

        // Format refskpdList untuk dropdown
        $refskpdList = SakipSkpd::find()
            ->select(['refskpd_id', 'nama_skpd'])
            ->where(['refskpd_id' => $skpdIds])
            ->asArray()
            ->all();

        // Cari nama SKPD yang dipilih
        if ($refskpd_id !== null) {
            $nama_skpd = SakipSkpd::find()
                ->select('nama_skpd')
                ->where(['refskpd_id' => $refskpd_id])
                ->scalar();
        }

        // Tetapkan periode default jika tidak dipilih
        if ($refperiode_id === null) {
            $currentYear = date('Y');
            $defaultPeriod = SakipPeriode::find()->where(['periode' => $currentYear])->one();
            $refperiode_id = $defaultPeriod ? $defaultPeriod->refperiode_id : null;
        }

        // Tetapkan periode dan SKPD pada $searchModel jika ada
        if ($refperiode_id !== null) {
            $searchModel->refperiode_id = $refperiode_id;
        }
        if ($refskpd_id !== null) {
            $searchModel->refskpd_id = $refskpd_id;
        }

        // Jalankan pencarian hanya jika periode dan SKPD dipilih
        if ($refperiode_id !== null && $refskpd_id !== null) {
            $dataProvider = $searchModel->search($this->request->queryParams);
            $dataProvider->pagination = false; // Nonaktifkan pagination
        }

        // Ambil periode berdasarkan ID
        $selectedPeriod = SakipPeriode::find()->where(['refperiode_id' => $refperiode_id])->one();
        $selectedPeriodValue = $selectedPeriod ? $selectedPeriod->periode : null;

        // Render view
        return $this->render('index-pegawai', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'periodeList' => $periodeList,
            'selectedPeriodId' => $refperiode_id,
            'selectedPeriodValue' => $selectedPeriodValue,
            'selectedSkpdId' => $refskpd_id,
            'refskpdList' => $refskpdList,
            'nama_skpd' => $nama_skpd,
        ]);
    }


    public function actionKalenderPerencanaan($refperiode_id = null)
    {
        $searchModel = new SakipCascadingprogramSearch();
        $this->layout = 'main-simona';

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

        $dataProvider = $searchModel->search($this->request->queryParams);
        $dataProvider->pagination = false;

        // Get refskpd_id from the current user
        $user = Yii::$app->user->identity;
        $refskpd_id = $user->refskpd_id;

        // Get the name of the SKPD based on refskpd_id
        $nama_skpd = SakipSkpd::find()->select('nama_skpd')->where(['refskpd_id' => $refskpd_id])->scalar();

        // Fetch all periods
        $periodeList = SakipPeriode::find()->all();

        // Fetch events from simona_cascadingkegiatan based on date range
        $events = SimonaCascadingkegiatan::find()
            ->where(['>=', 'date_start', date('Y-m-d')])
            ->andWhere(['<=', 'expired_date', date('Y-m-d')])
            ->all();

        // Prepare event data for FullCalendar
        $fullCalendarEvents = [];
        foreach ($events as $event) {
            $kegiatan = SakipKegiatan::findOne($event->refkegiatan_id);
            $fullCalendarEvents[] = [
                'title' => $kegiatan ? $kegiatan->nama_kegiatan : 'No Title',
                'start' => $event->date_start,
                'end' => $event->expired_date,
                'description' => $event->uraian_sasarankegiatan,
            ];
        }

        return $this->render('kalender-perencanaan', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'nama_skpd' => $nama_skpd,
            'periodeList' => $periodeList,
            'selectedPeriodId' => $refperiode_id,
            'events' => json_encode($fullCalendarEvents), // Pass events to the view
        ]);
    }


    /**
     * Displays a single SakipCascadingprogram model.
     * @param int $refcascadingprogram_id Refcascadingprogram ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionViewKegiatan($refcascadingprogram_id, $refperiode_id = null)
    {
        $this->layout = 'main-simona';

        $searchModel = new SakipCascadingkegiatanSearch();
        $searchModel->refcascadingprogram_id = $refcascadingprogram_id; // Filter by cascading program ID

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

        $dataProvider = $searchModel->search($this->request->queryParams);
        $dataProvider->pagination = false;

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

        // Create an instance of SimonaMediacascadingkegiatan to upload files
        $uploadModel = new SimonaMediacascadingkegiatan();
        $uploadModelOpd = new SimonaMediacascadingkegiatanOpd();
        $uploadModelKeluaran = new SimonaKeluaranmediacascadingkegiatan();

        return $this->render('view-kegiatan', [
            'model' => $this->findModel($refcascadingprogram_id),
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'nama_skpd' => $nama_skpd,
            'periodeList' => $periodeList,
            'selectedPeriodId' => $refperiode_id,
            'selectedPeriodValue' => $selectedPeriodValue, // Include selected period value
            'uploadModel' => $uploadModel, // Pass the model to the view
            'uploadModelOpd' => $uploadModelOpd, // Pass the model to the view
            'uploadModelKeluaran' => $uploadModelKeluaran, // Pass the model to the view
        ]);
    }

    public function actionViewKegiatanRincian($refcascadingkegiatan_id, $refperiode_id = null)
    {
        $this->layout = 'main-simona';

        $searchModel = new SakipCascadingsubkegiatanSearch();
        $searchModel->refcascadingkegiatan_id = $refcascadingkegiatan_id; // Filter by cascading program ID


        // If a refperiode_id is selected, add it to the query parameters
        if ($refperiode_id !== null) {
            $searchModel->refperiode_id = $refperiode_id;
        }

        $dataProvider = $searchModel->search($this->request->queryParams);
        $dataProvider->pagination = false;

        // Get refskpd_id from the current user
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

        // Create an instance of SimonaMediacascadingkegiatan to upload files
        $uploadModel = new SimonaMediacascadingsubkegiatan();
        $uploadModelOpd = new SimonaMediacascadingsubkegiatanOpd();
        $uploadModelKeluaran = new SimonaKeluaranmediacascadingsubkegiatan();

        return $this->render('view-kegiatan-rincian', [
            'model' => $this->findModel($refcascadingkegiatan_id),
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'nama_skpd' => $nama_skpd,
            'periodeList' => $periodeList,
            'selectedPeriodId' => $refperiode_id,
            'selectedPeriodValue' => $selectedPeriodValue, // Include selected period value
            'uploadModel' => $uploadModel, // Pass the model to the view
            'uploadModelOpd' => $uploadModelOpd, // Pass the model to the view
            'uploadModelKeluaran' => $uploadModelKeluaran, // Pass the model to the view
        ]);
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
