<?php

namespace frontend\controllers;

use Yii;
use frontend\models\SakipKebijakan;
use frontend\models\search\SakipKebijakanSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use frontend\models\SakipStrategi;
use frontend\models\search\SakipStrategiSearch;
use frontend\models\SakipIndikatortujuanrenstra;
use frontend\models\SakipSkpd;
use frontend\models\SakipPeriode;
use frontend\models\SakipMisi;
use frontend\models\SakipTujuan;
use frontend\models\SakipTujuanRenstra;
use frontend\models\SakipSasaranRenstra;
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

/**
 * SakipKebijakanController implements the CRUD actions for SakipKebijakan model.
 */
class SakipKebijakanController extends Controller
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
     * Lists all SakipKebijakan models.
     *
     * @return string
     */
    public function actionIndex($refperiode_id = null)
    {
        $searchModel = new SakipKebijakanSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
        $dataProvider->pagination = false;
        // Ambil refskpd_id dari user yang login
        $user = Yii::$app->user->identity;
        $refskpd_id = $user->refskpd_id;

        // Ambil nama_skpd berdasarkan refskpd_id
        $nama_skpd = SakipSkpd::find()->select('nama_skpd')->where(['refskpd_id' => $refskpd_id])->scalar();

        // Ambil semua periode
        $periodeList = SakipPeriode::find()->all();

        // Set default period to this year if not provided
        if ($refperiode_id === null) {
            $currentYear = date('Y');
            $defaultPeriod = SakipPeriode::find()->where(['periode' => $currentYear])->one();
            $refperiode_id = $defaultPeriod ? $defaultPeriod->refperiode_id : null;
        }

        // Ambil data sasaran renstra berdasarkan refskpd_id dan refperiode_id
        $sasaranRenstraList = SakipSasaranRenstra::find()
            ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
            ->all();

        // Jika tidak ada data
        $dataEmpty = empty($sasaranRenstraList);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'nama_skpd' => $nama_skpd,
            'periodeList' => $periodeList,
            'selectedPeriodId' => $refperiode_id,
            'sasaranRenstraList' => $sasaranRenstraList,
            'dataEmpty' => $dataEmpty,
            'refperiode_id' => $refperiode_id, // Ensure this is included
        ]);
    }


    /**
     * Displays a single SakipKebijakan model.
     * @param int $refkebijakan_id Refkebijakan ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($refkebijakan_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($refkebijakan_id),
        ]);
    }

    /**
     * Creates a new SakipKebijakan model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate($refsasaranrenstra_id = null, $refstrategi_id = null, $refperiode_id = null)
    {
        $model = new SakipKebijakan();

        // Ambil refskpd_id dari user saat ini
        $user = Yii::$app->user->identity;
        $refskpd_id = $user->refskpd_id;

        // Set nilai refsasaranrenstra_id, refstrategi_id, dan refperiode_id pada model jika diberikan
        if ($refsasaranrenstra_id) {
            $model->refsasaranrenstra_id = $refsasaranrenstra_id;

            // Cari data yang terkait dengan refsasaranrenstra_id
            $sasaranRenstra = SakipSasaranrenstra::findOne($refsasaranrenstra_id);

            if ($sasaranRenstra) {
                $model->refsasaran_id = $sasaranRenstra->refsasaran_id;
                $model->refmisi_id = $sasaranRenstra->misi->refmisi_id;
                $model->reftujuan_id = $sasaranRenstra->tujuan->reftujuan_id;
            }
        }

        if ($refstrategi_id) {
            $model->refstrategi_id = $refstrategi_id;
        }
        if ($refperiode_id) {
            $model->refperiode_id = $refperiode_id;
        }

        // Jika ada request Ajax
        if (Yii::$app->request->isAjax) {
            if ($model->load(Yii::$app->request->post())) {
                $model->refskpd_id = $refskpd_id; // Set refskpd_id dari user identity
                $model->user_create = $user->username; // Mengambil username dari user saat ini
                $model->date_create = date('Y-m-d'); // Mengambil tanggal saat ini

                // Set user_edit, date_edit, user_delete, dan date_delete menjadi null
                $model->user_edit = null;
                $model->date_edit = null;
                $model->user_delete = null;
                $model->date_delete = null;

                if ($model->save()) {
                    Yii::$app->session->setFlash('success', 'Berhasil Tambah Data');
                    return $this->asJson(['success' => true, 'redirect' => \yii\helpers\Url::to(['index'])]);
                } else {
                    Yii::error("Error saving data: " . json_encode($model->getErrors()));
                    return $this->asJson(['success' => false, 'errors' => $model->getErrors()]);
                }
            }

            return $this->renderAjax('_form', [
                'model' => $model,
            ]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }



    /**
     * Updates an existing SakipKebijakan model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $refkebijakan_id Refkebijakan ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($refkebijakan_id)
    {
        $model = $this->findModel($refkebijakan_id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'refkebijakan_id' => $model->refkebijakan_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing SakipKebijakan model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $refkebijakan_id Refkebijakan ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($refkebijakan_id)
    {
        $this->findModel($refkebijakan_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the SakipKebijakan model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $refkebijakan_id Refkebijakan ID
     * @return SakipKebijakan the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($refkebijakan_id)
    {
        if (($model = SakipKebijakan::findOne(['refkebijakan_id' => $refkebijakan_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
