<?php

namespace frontend\controllers;

use Yii;
use frontend\models\SakipSkpd;
use frontend\models\SakipPeriode;
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

class SimonaPerencanaanKegiatanController extends Controller
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
        $searchModel = new SakipCascadingkegiatanSearch();
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

    /**
     * Displays a single SakipCascadingprogram model.
     * @param int $refcascadingprogram_id Refcascadingprogram ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionViewSubkegiatan($refcascadingkegiatan_id, $refperiode_id = null)
    {
        $this->layout = 'main-simona';

        $searchModel = new SakipCascadingsubkegiatanSearch();
        $searchModel->refcascadingkegiatan_id = $refcascadingkegiatan_id; // Filter by cascading program ID

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
        $uploadModel = new SimonaMediacascadingsubkegiatan();
        $uploadModelOpd = new SimonaMediacascadingsubkegiatanOpd();
        $uploadModelKeluaran = new SimonaKeluaranmediacascadingsubkegiatan();

        return $this->render('view-subkegiatan', [
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
    protected function findModel($refcascadingkegiatan_id)
    {
        if (($model = SakipCascadingkegiatan::findOne(['refcascadingkegiatan_id' => $refcascadingkegiatan_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
