<?php

namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\data\ActiveDataProvider;
use frontend\models\SakipCascadingkegiatan;
use frontend\models\SakipSkpd;
use frontend\models\SakipPeriode;
use frontend\models\search\SakipCascadingkegiatanSearch;
use frontend\models\SimonaKeluaranmediacascadingkegiatan;
use yii\helpers\ArrayHelper;
use yii\web\UploadedFile;
use yii\helpers\Url;
use yii\web\Response;
use yii\db\Query;
use yii\db\Expression;
use yii\helpers\Json;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;


class DokrenbangDokumenKeluarKegiatanController extends Controller
{
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

    // public function actionIndex($refperiode_id = null)
    // {
    //     $this->layout = 'main-dokrenbang';
    //     $searchModel = new SakipCascadingkegiatanSearch();

    //     // Default period
    //     if ($refperiode_id === null) {
    //         $currentYear = date('Y');
    //         $defaultPeriod = SakipPeriode::find()->where(['periode' => $currentYear])->one();
    //         $refperiode_id = $defaultPeriod ? $defaultPeriod->refperiode_id : null;
    //     }

    //     if ($refperiode_id !== null) {
    //         $searchModel->refperiode_id = $refperiode_id;
    //     }

    //     $dataProvider = $searchModel->search($this->request->queryParams);
    //     $dataProvider->pagination = false;

    //     // Get refskpd_id from the current user
    //     $user = Yii::$app->user->identity;
    //     $refskpd_id = $user->refskpd_id;

    //     // Get the name of the SKPD
    //     $nama_skpd = SakipSkpd::find()->select('nama_skpd')->where(['refskpd_id' => $refskpd_id])->scalar();

    //     // Fetch all periods
    //     $periodeList = SakipPeriode::find()->all();

    //     // Get refcascadingkegiatan_id that exist in simona_keluaranmediacascadingkegiatan
    //     $existingCascadingKegiatanIds = SimonaKeluaranmediacascadingkegiatan::find()
    //         ->select('refcascadingkegiatan_id')
    //         ->where(['refskpd_id' => $refskpd_id])
    //         ->distinct()
    //         ->column();

    //     // Fetch cascading kegiatan with documents
    //     $cascadingKegiatanList = SakipCascadingkegiatan::find()
    //         ->where([
    //             'refskpd_id' => $refskpd_id,
    //             'refperiode_id' => $refperiode_id,
    //             'refcascadingkegiatan_id' => $existingCascadingKegiatanIds,
    //         ])
    //         ->with('simonaKeluaranmediacascadingkegiatan') // Assuming relation exists
    //         ->all();

    //     return $this->render('index', [
    //         'searchModel' => $searchModel,
    //         'dataProvider' => $dataProvider,
    //         'nama_skpd' => $nama_skpd,
    //         'periodeList' => $periodeList,
    //         'selectedPeriodId' => $refperiode_id,
    //         'cascadingKegiatanList' => $cascadingKegiatanList,
    //     ]);
    // }

    public function actionIndex($refperiode_id = null)
    {
        $this->layout = 'main-dokrenbang';
        $searchModel = new SakipCascadingkegiatanSearch();

        // Default period
        if ($refperiode_id === null) {
            $currentYear = date('Y');
            $defaultPeriod = SakipPeriode::find()->where(['periode' => $currentYear])->one();
            $refperiode_id = $defaultPeriod ? $defaultPeriod->refperiode_id : null;
        }

        if ($refperiode_id !== null) {
            $searchModel->refperiode_id = $refperiode_id;
        }



        // Get refskpd_id from the current user
        $user = Yii::$app->user->identity;
        $refskpd_id = $user->refskpd_id;

        // Get the name of the SKPD
        $nama_skpd = SakipSkpd::find()->select('nama_skpd')->where(['refskpd_id' => $refskpd_id])->scalar();

        // Fetch all periods
        $periodeList = SakipPeriode::find()->all();

        // 1. Dapatkan daftar ID dari kegiatan yang memiliki dokumen
        $existingCascadingKegiatanIds = SimonaKeluaranmediacascadingkegiatan::find()
            ->select('refcascadingkegiatan_id')
            ->where(['refskpd_id' => $refskpd_id])
            ->distinct()
            ->column();

        // 2. Buat query utama untuk DataProvider
        $query = SakipCascadingkegiatan::find()
            ->where([
                'refskpd_id' => $refskpd_id,
                'refperiode_id' => $refperiode_id,
                'refcascadingkegiatan_id' => $existingCascadingKegiatanIds,
            ])
            // Eager load relasi yang dibutuhkan agar efisien
            ->with(['refKegiatan', 'simonaKeluaranmediacascadingkegiatans']);

        $dataProvider = $searchModel->search($this->request->queryParams);
        // 3. Buat DataProvider dari query yang sudah lengkap
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 10, // Atur jumlah item per halaman
            ],
            'sort' => false // Nonaktifkan sorting jika tidak perlu
        ]);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'nama_skpd' => $nama_skpd,
            'periodeList' => $periodeList,
            'selectedPeriodId' => $refperiode_id,
            // 'cascadingKegiatanList' => $cascadingKegiatanList,
        ]);
    }

    public function actionIndexDev($refperiode_id = null, $refskpd_id = null)
    {
        $this->layout = 'main-dokrenbang';
        $searchModel = new SakipCascadingkegiatanSearch();

        $dataProvider = $searchModel->search($this->request->queryParams);
        $dataProvider->pagination = false;

        // Ambil refskpd_id dari user saat ini jika tidak ada di request
        if ($refskpd_id === null) {
            $user = Yii::$app->user->identity;
            $refskpd_id = $user->refskpd_id;
        }

        // Get the name of the SKPD
        $nama_skpd = SakipSkpd::find()->select('nama_skpd')->where(['refskpd_id' => $refskpd_id])->scalar();

        // Fetch all periods
        $periodeList = SakipPeriode::find()->all();

        // Default period
        if ($refperiode_id === null) {
            $currentYear = date('Y');
            $defaultPeriod = SakipPeriode::find()->where(['periode' => $currentYear])->one();
            $refperiode_id = $defaultPeriod ? $defaultPeriod->refperiode_id : null;
        }

        // Get refcascadingkegiatan_id that exist in simona_keluaranmediacascadingkegiatan
        $existingCascadingKegiatanIds = SimonaKeluaranmediacascadingkegiatan::find()
            ->select('refcascadingkegiatan_id')
            ->where(['refskpd_id' => $refskpd_id])
            ->distinct()
            ->column();

        // Fetch cascading kegiatan with documents
        $cascadingKegiatanList = SakipCascadingkegiatan::find()
            ->where([
                'refskpd_id' => $refskpd_id,
                'refperiode_id' => $refperiode_id,
                'refcascadingkegiatan_id' => $existingCascadingKegiatanIds,
            ])
            ->with('simonaKeluaranmediacascadingkegiatan') // Assuming relation exists
            ->all();

        // Ambil daftar SKPD untuk dropdown
        $skpdList = ArrayHelper::map(SakipSkpd::find()->all(), 'refskpd_id', 'nama_skpd');

        // Retrieve the periode based on refperiode_id
        $selectedPeriod = SakipPeriode::find()->where(['refperiode_id' => $refperiode_id])->one();
        $selectedPeriodValue = $selectedPeriod ? $selectedPeriod->periode : null; // Get the periode value

        return $this->render('index-dev', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'nama_skpd' => $nama_skpd,
            'periodeList' => $periodeList,
            'selectedPeriodId' => $refperiode_id,
            'cascadingKegiatanList' => $cascadingKegiatanList,
            'selectedPeriodId' => $refperiode_id,
            'selectedPeriodValue' => $selectedPeriodValue, // Include selected period value
            'selectedSkpdId' => $refskpd_id,
            'skpdList' => $skpdList,
        ]);
    }

    public function actionIndexPublik($refperiode_id = null, $refskpd_id = null)
    {
        $this->layout = 'main-dokrenbang';
        $searchModel = new SakipCascadingkegiatanSearch();

        $dataProvider = $searchModel->search($this->request->queryParams);
        $dataProvider->pagination = false;

        // Ambil refskpd_id dari user saat ini jika tidak ada di request
        if ($refskpd_id === null) {
            $user = Yii::$app->user->identity;
            $refskpd_id = $user->refskpd_id;
        }

        // Get the name of the SKPD
        $nama_skpd = SakipSkpd::find()->select('nama_skpd')->where(['refskpd_id' => $refskpd_id])->scalar();

        // Fetch all periods
        $periodeList = SakipPeriode::find()->all();

        // Default period
        if ($refperiode_id === null) {
            $currentYear = date('Y');
            $defaultPeriod = SakipPeriode::find()->where(['periode' => $currentYear])->one();
            $refperiode_id = $defaultPeriod ? $defaultPeriod->refperiode_id : null;
        }

        // Get refcascadingkegiatan_id that exist in simona_keluaranmediacascadingkegiatan
        $existingCascadingKegiatanIds = SimonaKeluaranmediacascadingkegiatan::find()
            ->select('refcascadingkegiatan_id')
            ->where(['refskpd_id' => $refskpd_id])
            ->distinct()
            ->column();

        // Fetch cascading kegiatan with documents
        $cascadingKegiatanList = SakipCascadingkegiatan::find()
            ->where([
                'refskpd_id' => $refskpd_id,
                'refperiode_id' => $refperiode_id,
                'refcascadingkegiatan_id' => $existingCascadingKegiatanIds,
            ])
            ->with('simonaKeluaranmediacascadingkegiatan') // Assuming relation exists
            ->all();

        // Ambil daftar SKPD untuk dropdown
        $skpdList = ArrayHelper::map(SakipSkpd::find()->all(), 'refskpd_id', 'nama_skpd');

        // Retrieve the periode based on refperiode_id
        $selectedPeriod = SakipPeriode::find()->where(['refperiode_id' => $refperiode_id])->one();
        $selectedPeriodValue = $selectedPeriod ? $selectedPeriod->periode : null; // Get the periode value

        return $this->render('index-publik', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'nama_skpd' => $nama_skpd,
            'periodeList' => $periodeList,
            'selectedPeriodId' => $refperiode_id,
            'cascadingKegiatanList' => $cascadingKegiatanList,
            'selectedPeriodId' => $refperiode_id,
            'selectedPeriodValue' => $selectedPeriodValue, // Include selected period value
            'selectedSkpdId' => $refskpd_id,
            'skpdList' => $skpdList,
        ]);
    }
}
