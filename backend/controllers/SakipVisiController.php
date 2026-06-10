<?php

namespace backend\controllers;

use Yii;
use backend\models\SakipVisi;
use backend\models\search\SakipVisiSearch;
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
 * SakipVisiController implements the CRUD actions for SakipVisi model.
 */
class SakipVisiController extends Controller
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
     * Lists all SakipVisi models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new SakipVisiSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
        $dataProvider->pagination = false;

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single SakipVisi model.
     * @param int $refvisi_id Refvisi ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($refvisi_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($refvisi_id),
        ]);
    }

    /**
     * Creates a new SakipVisi model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new SakipVisi();

        if (Yii::$app->request->isAjax) {
            if ($model->load(Yii::$app->request->post())) {
                if ($model->save()) {
                    Yii::$app->session->setFlash('success', 'Berhasil Tambah Data');
                    return $this->asJson(['success' => true, 'redirect' => \yii\helpers\Url::to(['view', 'refvisi_id' => $model->refvisi_id])]);
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
     * Updates an existing Sakipvisi model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $refvisi_id Refvisi ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($refvisi_id)
    {
        $model = $this->findModel($refvisi_id);

        if (Yii::$app->request->isAjax) {
            if ($model->load(Yii::$app->request->post())) {
                if ($model->save()) {
                    Yii::$app->session->setFlash('success', 'Berhasil Memperbarui Data');
                    return $this->asJson(['success' => true, 'redirect' => \yii\helpers\Url::to(['view', 'refvisi_id' => $model->refvisi_id])]);
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

    public function actionDuplicate($refvisi_id)
    {
        $model = $this->findModel($refvisi_id);

        $newModel = new SakipVisi(); // Ganti YourModel dengan nama model yang sesuai
        $newModel->attributes = $model->attributes; // Duplikasi semua atribut
        $newModel->isNewRecord = true; // Tandai sebagai record baru
        $newModel->refvisi_id = null; // Kosongkan ID agar tidak berbenturan

        if ($newModel->save()) {
            Yii::$app->session->setFlash('success', 'Data berhasil diduplikasi.');
            return $this->redirect(['view', 'refvisi_id' => $newModel->refvisi_id]); // Redirect ke halaman view dari data yang baru
        } else {
            Yii::$app->session->setFlash('error', 'Data gagal diduplikasi.');
            return $this->redirect(['index']); // Redirect ke halaman index jika gagal
        }
    }



    /**
     * Deletes an existing SakipVisi model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $refvisi_id Refvisi ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($refvisi_id)
    {
        $this->findModel($refvisi_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the SakipVisi model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $refvisi_id Refvisi ID
     * @return SakipVisi the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($refvisi_id)
    {
        if (($model = SakipVisi::findOne(['refvisi_id' => $refvisi_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
