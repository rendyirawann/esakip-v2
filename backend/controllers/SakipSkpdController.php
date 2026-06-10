<?php

namespace backend\controllers;

use Yii;
use backend\models\SakipSkpd;
use backend\models\SakipPeriode;
use backend\models\SakipPenjabatSkpd;
use backend\models\SakipUrusan;
use backend\models\SakipBidang;
use backend\models\search\SakipSkpdSearch;
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

/**
 * SakipSkpdController implements the CRUD actions for SakipSkpd model.
 */
class SakipSkpdController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all SakipSkpd models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new SakipSkpdSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
        $dataProvider->pagination = false;

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single SakipSkpd model.
     * @param int $refskpd_id Refskpd ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($refskpd_id, $refperiode_id = null)
    {
        // Fetch the model based on refskpd_id
        $model = $this->findModel($refskpd_id);

        // Fetch all available periods
        $periodeList = SakipPeriode::find()->all();

        // If a refperiode_id is selected, add it to the query parameters
        if ($refperiode_id !== null) {
            $penjabatData = SakipPenjabatSkpd::find()
                ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
                ->orderBy(['refperiode_id' => SORT_DESC]) // Sort by refperiode_id in descending order
                ->all();
        } else {
            // If no refperiode_id is selected, use the current year as the default period
            $currentYear = date('Y');
            $defaultPeriod = SakipPeriode::find()->where(['periode' => $currentYear])->one();
            $refperiode_id = $defaultPeriod ? $defaultPeriod->refperiode_id : null;

            // Fetch the data for the default period
            $penjabatData = SakipPenjabatSkpd::find()
                ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
                ->orderBy(['refperiode_id' => SORT_DESC])
                ->all();
        }

        // Return the view and pass the necessary data
        return $this->render('view', [
            'model' => $model,
            'penjabatData' => $penjabatData, // Pass the penjabat data to the view
            'periodeList' => $periodeList, // List of available periods
            'selectedPeriodId' => $refperiode_id, // Currently selected period
        ]);
    }




    /**
     * Creates a new SakipSkpd model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new SakipSkpd();

        if (Yii::$app->request->isAjax) {
            if ($model->load(Yii::$app->request->post())) {
                if ($model->save()) {
                    Yii::$app->session->setFlash('success', 'Berhasil Tambah Data');
                    return $this->asJson(['success' => true, 'redirect' => \yii\helpers\Url::to(['view', 'refskpd_id' => $model->refskpd_id])]);
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

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing SakipSkpd model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $refskpd_id Refskpd ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($refskpd_id)
    {
        $model = $this->findModel($refskpd_id);

        if (Yii::$app->request->isAjax) {
            if ($model->load(Yii::$app->request->post())) {
                if ($model->save()) {
                    Yii::$app->session->setFlash('success', 'Berhasil Memperbarui Data');
                    return $this->asJson(['success' => true, 'redirect' => \yii\helpers\Url::to(['view', 'refskpd_id' => $model->refskpd_id])]);
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

    public function actionLists($id)
    {
        $countBidang = SakipBidang::find()
            ->where(['refurusan_id' => $id])
            ->count();

        $bidang = SakipBidang::find()
            ->where(['refurusan_id' => $id])
            ->orderBy('kode_bidang ASC')
            ->all();

        if ($countBidang > 0) {
            foreach ($bidang as $b) {
                echo "<option value='" . $b->refbidang_id . "'>" . $b->kode_bidang . " - " . $b->nama_bidang . "</option>";
            }
        } else {
            echo "<option>-</option>";
        }
    }


    /**
     * Deletes an existing SakipSkpd model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $refskpd_id Refskpd ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($refskpd_id)
    {
        $this->findModel($refskpd_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the SakipSkpd model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $refskpd_id Refskpd ID
     * @return SakipSkpd the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($refskpd_id)
    {
        if (($model = SakipSkpd::findOne(['refskpd_id' => $refskpd_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
