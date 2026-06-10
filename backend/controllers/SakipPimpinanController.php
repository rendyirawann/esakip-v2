<?php

namespace backend\controllers;

use Yii;
use backend\models\SakipPimpinan;
use backend\models\search\SakipPimpinanSearch;
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
 * SakipPimpinanController implements the CRUD actions for SakipPimpinan model.
 */
class SakipPimpinanController extends Controller
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
     * Lists all SakipPimpinan models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new SakipPimpinanSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
        $dataProvider->pagination = false;

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single SakipPimpinan model.
     * @param int $refpimpinan_id Refpimpinan ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($refpimpinan_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($refpimpinan_id),
        ]);
    }

    /**
     * Creates a new SakipPimpinan model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new SakipPimpinan();

        if (Yii::$app->request->isAjax) {
            if ($model->load(Yii::$app->request->post())) {
                // Set user_edit and date_edit if not already set (not necessary due to form default values)
                $model->user_edit = Yii::$app->user->identity->username; // This is optional
                $model->date_edit = date('Y-m-d H:i:s'); // This is optional

                if ($model->save()) {
                    Yii::$app->session->setFlash('success', 'Berhasil Tambah Data');
                    return $this->asJson(['success' => true, 'redirect' => \yii\helpers\Url::to(['index'])]);
                } else {
                    // Log error if save fails
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
     * Updates an existing SakipPimpinan model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $refpimpinan_id Refpimpinan ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($refpimpinan_id)
    {
        $model = $this->findModel($refpimpinan_id);

        if (Yii::$app->request->isAjax) {
            if ($model->load(Yii::$app->request->post())) {
                // Update date_edit to current datetime upon update
                $model->date_edit = date('Y-m-d H:i:s'); // Automatically set to current datetime
                $model->user_edit = Yii::$app->user->identity->username; // This is optional

                if ($model->save()) {
                    Yii::$app->session->setFlash('success', 'Berhasil Memperbarui Data');
                    return $this->asJson(['success' => true, 'redirect' => \yii\helpers\Url::to(['index'])]);
                } else {
                    // Log error if save fails
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
     * Deletes an existing SakipPimpinan model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $refpimpinan_id Refpimpinan ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($refpimpinan_id)
    {
        $this->findModel($refpimpinan_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the SakipPimpinan model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $refpimpinan_id Refpimpinan ID
     * @return SakipPimpinan the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($refpimpinan_id)
    {
        if (($model = SakipPimpinan::findOne(['refpimpinan_id' => $refpimpinan_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
