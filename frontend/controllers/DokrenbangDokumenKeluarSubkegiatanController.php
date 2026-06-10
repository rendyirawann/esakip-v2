<?php

namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\data\ActiveDataProvider;
use frontend\models\SakipCascadingkegiatan;
use frontend\models\SakipCascadingsubkegiatan;
use frontend\models\SakipSkpd;
use frontend\models\SakipPeriode;
use frontend\models\search\SakipCascadingsubkegiatanSearch;
use frontend\models\SimonaKeluaranmediacascadingsubkegiatan;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

class DokrenbangDokumenKeluarSubkegiatanController extends Controller
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

    public function actionIndex($refperiode_id = null)
    {
        $this->layout = 'main-dokrenbang';
        $searchModel = new SakipCascadingsubkegiatanSearch();

        // Default period
        if ($refperiode_id === null) {
            $currentYear = date('Y');
            $defaultPeriod = SakipPeriode::find()->where(['periode' => $currentYear])->one();
            $refperiode_id = $defaultPeriod ? $defaultPeriod->refperiode_id : null;
        }

        if ($refperiode_id !== null) {
            $searchModel->refperiode_id = $refperiode_id;
        }

        $dataProvider = $searchModel->search($this->request->queryParams);
        $dataProvider->pagination = false;

        // Get refskpd_id from the current user
        $user = Yii::$app->user->identity;
        $refskpd_id = $user->refskpd_id;

        // Get the name of the SKPD
        $nama_skpd = SakipSkpd::find()->select('nama_skpd')->where(['refskpd_id' => $refskpd_id])->scalar();

        // Fetch all periods
        $periodeList = SakipPeriode::find()->all();

        // Get refcascadingkegiatan_id that exist in simona_keluaranmediacascadingkegiatan
        $existingCascadingSubkegiatanIds = SimonaKeluaranmediacascadingsubkegiatan::find()
            ->select('refcascadingsubkegiatan_id')
            ->where(['refskpd_id' => $refskpd_id])
            ->distinct()
            ->column();

        // Fetch cascading kegiatan with documents
        $cascadingSubkegiatanList = SakipCascadingsubkegiatan::find()
            ->where([
                'refskpd_id' => $refskpd_id,
                'refperiode_id' => $refperiode_id,
                'refcascadingsubkegiatan_id' => $existingCascadingSubkegiatanIds,
            ])
            ->with('simonaKeluaranmediacascadingsubkegiatan') // Assuming relation exists
            ->all();

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'nama_skpd' => $nama_skpd,
            'periodeList' => $periodeList,
            'selectedPeriodId' => $refperiode_id,
            'cascadingSubkegiatanList' => $cascadingSubkegiatanList,
        ]);
    }
}
