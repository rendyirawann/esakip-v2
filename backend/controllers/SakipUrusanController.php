<?php

namespace backend\controllers;

use Yii;
use backend\models\SakipUrusan;
use backend\models\search\SakipUrusanSearch;
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
 * SakipUrusanController implements the CRUD actions for SakipUrusan model.
 */
class SakipUrusanController extends Controller
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
     * Lists all SakipUrusan models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new SakipUrusanSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
        $dataProvider->pagination = false;

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single SakipUrusan model.
     * @param int $urusan_id Urusan ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($urusan_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($urusan_id),
        ]);
    }

    /**
     * Creates a new SakipUrusan model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new SakipUrusan();

        if (Yii::$app->request->isAjax) {
            if ($model->load(Yii::$app->request->post())) {
                if ($model->save()) {
                    Yii::$app->session->setFlash('success', 'Berhasil Tambah Data');
                    return $this->asJson(['success' => true, 'redirect' => \yii\helpers\Url::to(['view', 'urusan_id' => $model->urusan_id])]);
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
     * Updates an existing SakipUrusan model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $urusan_id Urusan ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($urusan_id)
    {
        $model = $this->findModel($urusan_id);
    
        if (Yii::$app->request->isAjax) {
            if ($model->load(Yii::$app->request->post())) {
                if ($model->save()) {
                    Yii::$app->session->setFlash('success', 'Berhasil Memperbarui Data');
                    return $this->asJson(['success' => true, 'redirect' => \yii\helpers\Url::to(['view', 'urusan_id' => $model->urusan_id])]);
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
     * Deletes an existing SakipUrusan model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $urusan_id Urusan ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($urusan_id)
    {
        $this->findModel($urusan_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the SakipUrusan model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $urusan_id Urusan ID
     * @return SakipUrusan the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($urusan_id)
    {
        if (($model = SakipUrusan::findOne(['urusan_id' => $urusan_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
