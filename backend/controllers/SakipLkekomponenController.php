<?php

namespace backend\controllers;

use Yii;
use backend\models\SakipLkekomponen;
use backend\models\search\SakipLkekomponenSearch;
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
 * SakipLkekomponenController implements the CRUD actions for SakipLkekomponen model.
 */
class SakipLkekomponenController extends Controller
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
     * Lists all SakipLkekomponen models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new SakipLkekomponenSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
        $dataProvider->pagination = false;

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single SakipLkekomponen model.
     * @param int $reflkekomponen_id Reflkekomponen ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($reflkekomponen_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($reflkekomponen_id),
        ]);
    }

    /**
     * Creates a new SakipLkekomponen model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new SakipLkekomponen();

        if (Yii::$app->request->isAjax) {
            if ($model->load(Yii::$app->request->post())) {
                if ($model->save()) {
                    Yii::$app->session->setFlash('success', 'Berhasil Tambah Data');
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

        return $this->render('create', [
            'model' => $model,
        ]);
    }


    /**
     * Updates an existing SakipLkekomponen model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $reflkekomponen_id Reflkekomponen ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($reflkekomponen_id)
    {
        $model = $this->findModel($reflkekomponen_id);

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
     * Deletes an existing SakipLkekomponen model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $reflkekomponen_id Reflkekomponen ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($reflkekomponen_id)
    {
        $this->findModel($reflkekomponen_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the SakipLkekomponen model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $reflkekomponen_id Reflkekomponen ID
     * @return SakipLkekomponen the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($reflkekomponen_id)
    {
        if (($model = SakipLkekomponen::findOne(['reflkekomponen_id' => $reflkekomponen_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
