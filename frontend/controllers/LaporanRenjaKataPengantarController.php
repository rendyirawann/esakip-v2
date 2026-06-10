<?php

namespace frontend\controllers;

use Yii;
use frontend\models\LaporanRenjaKataPengantar;
use frontend\models\SakipPeriode;
use frontend\models\SakipSkpd;
use frontend\models\search\LaporanRenjaKataPengantarSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\IdentityInterface;
use yii\web\UploadedFile;
use yii\helpers\Url;
use yii\web\Response;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\db\Expression;
use yii\helpers\Json;
use yii\helpers\ArrayHelper;
use yii\filters\AccessControl;

/**
 * LaporanRenjaKataPengantarController implements the CRUD actions for LaporanRenjaKataPengantar model.
 */
class LaporanRenjaKataPengantarController extends Controller
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
     * Lists all LaporanRenjaKataPengantar models.
     *
     * @return string
     */
    public function actionIndex($refperiode_id = null, $refskpd_id = null)
    {
        $searchModel = new LaporanRenjaKataPengantarSearch();
        $this->layout = 'main-bukulaporan';

        $dataProvider = $searchModel->search($this->request->queryParams);
        // Menonaktifkan paginasi
        $dataProvider->pagination = false;

        // Get the name of the SKPD based on refskpd_id
        $nama_skpd = SakipSkpd::find()->select('nama_skpd')->where(['refskpd_id' => $refskpd_id])->scalar();

        // Fetch all periods
        $periodeList = SakipPeriode::find()->all();

        // Ambil daftar SKPD untuk dropdown
        $skpdList = ArrayHelper::map(SakipSkpd::find()->all(), 'refskpd_id', 'nama_skpd');

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
            'selectedSkpdId' => $refskpd_id,
            'skpdList' => $skpdList,
        ]);
    }

    /**
     * Displays a single LaporanRenjaKataPengantar model.
     * @param int $laporan_renja_kata_pengantar_id Laporan Renja Kata Pengantar ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($laporan_renja_kata_pengantar_id, $refperiode_id = null, $refskpd_id = null)
    {

        // Get the name of the SKPD based on refskpd_id
        $nama_skpd = SakipSkpd::find()->select('nama_skpd')->where(['refskpd_id' => $refskpd_id])->scalar();

        // Fetch all periods
        $periodeList = SakipPeriode::find()->all();

        // Ambil daftar SKPD untuk dropdown
        $skpdList = ArrayHelper::map(SakipSkpd::find()->all(), 'refskpd_id', 'nama_skpd');

        // Retrieve the periode based on refperiode_id
        $selectedPeriod = SakipPeriode::find()->where(['refperiode_id' => $refperiode_id])->one();
        $selectedPeriodValue = $selectedPeriod ? $selectedPeriod->periode : null; // Get the periode value

        return $this->render('view', [
            'model' => $this->findModel($laporan_renja_kata_pengantar_id),
            'nama_skpd' => $nama_skpd,
            'periodeList' => $periodeList,
            'selectedPeriodId' => $refperiode_id,
            'selectedPeriodValue' => $selectedPeriodValue, // Include selected period value
            'selectedSkpdId' => $refskpd_id,
            'skpdList' => $skpdList,
        ]);
    }

    /**
     * Creates a new LaporanRenjaKataPengantar model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new LaporanRenjaKataPengantar();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'laporan_renja_kata_pengantar_id' => $model->laporan_renja_kata_pengantar_id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing LaporanRenjaKataPengantar model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $laporan_renja_kata_pengantar_id Laporan Renja Kata Pengantar ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($laporan_renja_kata_pengantar_id)
    {
        $model = $this->findModel($laporan_renja_kata_pengantar_id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'laporan_renja_kata_pengantar_id' => $model->laporan_renja_kata_pengantar_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing LaporanRenjaKataPengantar model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $laporan_renja_kata_pengantar_id Laporan Renja Kata Pengantar ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($laporan_renja_kata_pengantar_id)
    {
        $this->findModel($laporan_renja_kata_pengantar_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the LaporanRenjaKataPengantar model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $laporan_renja_kata_pengantar_id Laporan Renja Kata Pengantar ID
     * @return LaporanRenjaKataPengantar the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($laporan_renja_kata_pengantar_id)
    {
        if (($model = LaporanRenjaKataPengantar::findOne(['laporan_renja_kata_pengantar_id' => $laporan_renja_kata_pengantar_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
