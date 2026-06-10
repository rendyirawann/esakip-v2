<?php

namespace backend\controllers;

use Yii;
use backend\models\SakipPegawaibappeda;
use backend\models\search\SakipPegawaibappedaSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use backend\models\SakipBidangbappeda;
use backend\models\search\SakipBidangbappedaSearch;
use backend\models\SakipTitle;
use backend\models\search\SakipTitleSearch;
use backend\models\SakipBidang;
use backend\models\SakipProgram;
use backend\models\SakipKegiatan;
use backend\models\SakipPenanggungjawab;
use backend\models\SakipUrusan;
use backend\models\search\SakipBidangSearch;
use yii\web\UploadedFile;
use yii\helpers\Url;
use yii\web\Response;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\db\Expression;
use yii\helpers\Json;
use yii\helpers\ArrayHelper;

/**
 * SakipPegawaibappedaController implements the CRUD actions for SakipPegawaibappeda model.
 */
class SakipPegawaibappedaController extends Controller
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
     * Lists all SakipPegawaibappeda models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new SakipPegawaibappedaSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
        $dataProvider->pagination = false;

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single SakipPegawaibappeda model.
     * @param int $refpegawai_id Refpegawai ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($refpegawai_id)
    {
        $penanggungjawabData = SakipPenanggungjawab::find()
            ->where(['refpegawai_id' => $refpegawai_id])
            ->orderBy(['refpegawai_id' => SORT_DESC]) // Sort by refperiode_id in descending order
            ->all();

        return $this->render('view', [
            'penanggungjawabData' => $penanggungjawabData, // Pass the penjabat data to the view
            'model' => $this->findModel($refpegawai_id),
        ]);
    }

    /**
     * Creates a new SakipPegawaibappeda model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new SakipPegawaibappeda();

        if (Yii::$app->request->isAjax) {
            if ($model->load(Yii::$app->request->post())) {
                if ($model->save()) {
                    Yii::$app->session->setFlash('success', 'Berhasil Tambah Data');
                    return $this->asJson(['success' => true, 'redirect' => \yii\helpers\Url::to(['view', 'refpegawai_id' => $model->refpegawai_id])]);
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
     * Updates an existing SakipPegawaibappeda model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $refpegawai_id Refpegawai ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($refpegawai_id)
    {
        $model = $this->findModel($refpegawai_id);

        if (Yii::$app->request->isAjax) {
            if ($model->load(Yii::$app->request->post())) {
                if ($model->save()) {
                    Yii::$app->session->setFlash('success', 'Berhasil Memperbarui Data');
                    return $this->asJson(['success' => true, 'redirect' => \yii\helpers\Url::to(['view', 'refpegawai_id' => $model->refpegawai_id])]);
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
     * Deletes an existing SakipPegawaibappeda model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $refpegawai_id Refpegawai ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($refpegawai_id)
    {
        $this->findModel($refpegawai_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the SakipPegawaibappeda model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $refpegawai_id Refpegawai ID
     * @return SakipPegawaibappeda the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($refpegawai_id)
    {
        if (($model = SakipPegawaibappeda::findOne(['refpegawai_id' => $refpegawai_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
