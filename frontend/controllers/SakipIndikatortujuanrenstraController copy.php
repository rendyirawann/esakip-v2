<?php

namespace frontend\controllers;

use Yii;
use frontend\models\SakipIndikatortujuanrenstra;
use frontend\models\SakipSkpd;
use frontend\models\SakipPeriode;
use frontend\models\SakipMisi;
use frontend\models\SakipTujuan;
use frontend\models\SakipTujuanRenstra;
use frontend\models\SakipSasaranRenstra;
use frontend\models\search\SakipIndikatortujuanrenstraSearch;
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

/**
 * SakipIndikatortujuanrenstraController implements the CRUD actions for SakipIndikatortujuanrenstra model.
 */
class SakipIndikatortujuanrenstraController extends Controller
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
     * Lists all SakipIndikatortujuanrenstra models.
     *
     * @return string
     */
    public function actionIndex($refperiode_id = null)
    {
        $searchModel = new SakipIndikatortujuanrenstraSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
        $dataProvider->pagination = false;

        // Ambil refskpd_id dari user saat ini
        $user = Yii::$app->user->identity;
        $refskpd_id = $user->refskpd_id;

        // Ambil nama_skpd berdasarkan refskpd_id
        $nama_skpd = SakipSkpd::find()->select('nama_skpd')->where(['refskpd_id' => $refskpd_id])->scalar();

        // Ambil semua periode
        $periodeList = SakipPeriode::find()->all();

        // Set default period ke tahun ini jika tidak disediakan
        if ($refperiode_id === null) {
            $currentYear = date('Y');
            $defaultPeriod = SakipPeriode::find()->where(['periode' => $currentYear])->one();
            $refperiode_id = $defaultPeriod ? $defaultPeriod->refperiode_id : null;
        }

        // Ambil data tujuan renstra berdasarkan refskpd_id dan refperiode_id, dikelompokkan berdasarkan refsasaranrenstra_id
        $tujuanRenstraList = SakipTujuanRenstra::find()
            ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
            ->orderBy(['refsasaranrenstra_id' => SORT_ASC]) // Urutkan berdasarkan refsasaranrenstra_id
            ->all();

        // Kelompokkan berdasarkan refsasaranrenstra_id
        $groupedTujuanRenstra = [];
        foreach ($tujuanRenstraList as $tujuanRenstra) {
            $groupedTujuanRenstra[$tujuanRenstra->refsasaranrenstra_id][] = $tujuanRenstra;
        }

        // Buat data provider manual untuk daftar tujuan renstra
        $dataProvider = new \yii\data\ArrayDataProvider([
            'allModels' => $groupedTujuanRenstra,
            'pagination' => false,
        ]);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'nama_skpd' => $nama_skpd,
            'periodeList' => $periodeList,  // Kirim data periode ke view
            'selectedPeriodId' => $refperiode_id, // Add selected period id
        ]);
    }






    /**
     * Displays a single SakipIndikatortujuanrenstra model.
     * @param int $refindikatortujuanrenstra_id Refindikatortujuanrenstra ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($refindikatortujuanrenstra_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($refindikatortujuanrenstra_id),
        ]);
    }

    /**
     * Creates a new SakipIndikatortujuanrenstra model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate($refsasaranrenstra_id = null, $refperiode_id = null)
    {
        $model = new SakipIndikatortujuanrenstra();

        // Ambil refskpd_id dari user saat ini
        $user = Yii::$app->user->identity;
        $refskpd_id = $user->refskpd_id;

        // Set nilai refsasaranrenstra_id, reftujuanrenstra_id, dan refperiode_id pada model jika diberikan
        if ($refsasaranrenstra_id) {
            $model->refsasaranrenstra_id = $refsasaranrenstra_id;
        }

        // Set nilai refsasaranrenstra_id dan refperiode_id pada model jika diberikan
        if ($refsasaranrenstra_id) {
            $model->reftujuanrenstra_id = $refsasaranrenstra_id;
        }

        if ($refperiode_id) {
            $model->refperiode_id = $refperiode_id;
        }

        // Jika ada request Ajax
        if (Yii::$app->request->isAjax) {
            if ($model->load(Yii::$app->request->post())) {
                // Set refskpd_id from user identity
                $model->refskpd_id = $refskpd_id;


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
     * Updates an existing SakipIndikatortujuanrenstra model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $refindikatortujuanrenstra_id Refindikatortujuanrenstra ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($refindikatortujuanrenstra_id)
    {
        $model = $this->findModel($refindikatortujuanrenstra_id);

        if (Yii::$app->request->isAjax) {
            if ($model->load(Yii::$app->request->post())) {
                if ($model->save()) {
                    Yii::$app->session->setFlash('success', 'Berhasil Memperbarui Data');
                    return $this->asJson(['success' => true, 'redirect' => \yii\helpers\Url::to(['index'])]);
                } else {
                    // Tambahkan log error
                    Yii::error("Error saving data: " . json_encode($model->getErrors()));
                    return $this->asJson(['success' => false, 'errors' => $model->getErrors()]);
                }
            }

            return $this->renderAjax('_form', [
                'model' => $model,
            ]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }
    /**
     * Deletes an existing SakipIndikatortujuanrenstra model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $refindikatortujuanrenstra_id Refindikatortujuanrenstra ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($refindikatortujuanrenstra_id)
    {
        $this->findModel($refindikatortujuanrenstra_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the SakipIndikatortujuanrenstra model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $refindikatortujuanrenstra_id Refindikatortujuanrenstra ID
     * @return SakipIndikatortujuanrenstra the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($refindikatortujuanrenstra_id)
    {
        if (($model = SakipIndikatortujuanrenstra::findOne(['refindikatortujuanrenstra_id' => $refindikatortujuanrenstra_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
