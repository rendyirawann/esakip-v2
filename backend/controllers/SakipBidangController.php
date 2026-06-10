<?php

namespace backend\controllers;

use Yii;
use backend\models\SakipBidang;
use backend\models\SakipProgram;
use backend\models\SakipKegiatan;
use backend\models\SakipUrusan;
use backend\models\search\SakipBidangSearch;
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
 * SakipBidangController implements the CRUD actions for SakipBidang model.
 */
class SakipBidangController extends Controller
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
     * Lists all SakipBidang models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new SakipBidangSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
        $dataProvider->pagination = false;

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionPrintAll()
    {
        // Ambil semua data dari tabel
        $models = SakipProgram::find()->all();

        // Kirim data ke view khusus untuk pencetakan
        return $this->render('print-all', [
            'models' => $models,
        ]);
    }


    /**
     * Displays a single SakipBidang model.
     * @param int $refbidang_id Refbidang ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($refbidang_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($refbidang_id),
        ]);
    }

    /**
     * Creates a new SakipBidang model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new SakipBidang();

        if (Yii::$app->request->isAjax) {
            if ($model->load(Yii::$app->request->post())) {
                if ($model->save()) {
                    Yii::$app->session->setFlash('success', 'Berhasil Tambah Data');
                    return $this->asJson(['success' => true, 'redirect' => \yii\helpers\Url::to(['view', 'refbidang_id' => $model->refbidang_id])]);
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
     * Updates an existing SakipBidang model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $refbidang_id Refbidang ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($refbidang_id)
    {
        $model = $this->findModel($refbidang_id);

        if (Yii::$app->request->isAjax) {
            if ($model->load(Yii::$app->request->post())) {
                if ($model->save()) {
                    Yii::$app->session->setFlash('success', 'Berhasil Memperbarui Data');
                    return $this->asJson(['success' => true, 'redirect' => \yii\helpers\Url::to(['view', 'refbidang_id' => $model->refbidang_id])]);
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
     * Deletes an existing SakipBidang model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $refbidang_id Refbidang ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($refbidang_id)
    {
        $this->findModel($refbidang_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the SakipBidang model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $refbidang_id Refbidang ID
     * @return SakipBidang the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($refbidang_id)
    {
        if (($model = SakipBidang::findOne(['refbidang_id' => $refbidang_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
