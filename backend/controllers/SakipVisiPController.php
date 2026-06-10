<?php

namespace backend\controllers;

use Yii;
use backend\models\SakipVisiP;
use backend\models\search\SakipVisiPSearch;
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
 * SakipVisiPController implements the CRUD actions for SakipVisiP model.
 */
class SakipVisiPController extends Controller
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
     * Lists all SakipVisiP models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new SakipVisiPSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
        $dataProvider->pagination = false;

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single SakipVisiP model.
     * @param int $refvisi_p_id Refvisi ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($refvisi_p_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($refvisi_p_id),
        ]);
    }

    /**
     * Creates a new SakipVisiP model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new SakipVisiP();

        if (Yii::$app->request->isAjax) {
            if ($model->load(Yii::$app->request->post())) {
                if ($model->save()) {
                    Yii::$app->session->setFlash('success', 'Berhasil Tambah Data');
                    return $this->asJson(['success' => true, 'redirect' => \yii\helpers\Url::to(['view', 'refvisi_p_id' => $model->refvisi_p_id])]);
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
     * Updates an existing SakipVisiP model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $refvisi_p_id Refvisi ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($refvisi_p_id)
    {
        $model = $this->findModel($refvisi_p_id);

        if (Yii::$app->request->isAjax) {
            if ($model->load(Yii::$app->request->post())) {
                if ($model->save()) {
                    Yii::$app->session->setFlash('success', 'Berhasil Memperbarui Data');
                    return $this->asJson(['success' => true, 'redirect' => \yii\helpers\Url::to(['view', 'refvisi_p_id' => $model->refvisi_p_id])]);
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

    public function actionDuplicate($refvisi_p_id)
    {
        $model = $this->findModel($refvisi_p_id);

        $newModel = new SakipVisiP(); // Ganti YourModel dengan nama model yang sesuai
        $newModel->attributes = $model->attributes; // Duplikasi semua atribut
        $newModel->isNewRecord = true; // Tandai sebagai record baru
        $newModel->refvisi_p_id = null; // Kosongkan ID agar tidak berbenturan

        if ($newModel->save()) {
            Yii::$app->session->setFlash('success', 'Data berhasil diduplikasi.');
            return $this->redirect(['view', 'refvisi_p_id' => $newModel->refvisi_p_id]); // Redirect ke halaman view dari data yang baru
        } else {
            Yii::$app->session->setFlash('error', 'Data gagal diduplikasi.');
            return $this->redirect(['index']); // Redirect ke halaman index jika gagal
        }
    }



    /**
     * Deletes an existing SakipVisiP model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $refvisi_p_id Refvisi ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($refvisi_p_id)
    {
        $this->findModel($refvisi_p_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the SakipVisiP model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $refvisi_p_id Refvisi ID
     * @return SakipVisiP the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($refvisi_p_id)
    {
        if (($model = SakipVisiP::findOne(['refvisi_p_id' => $refvisi_p_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
