<?php

namespace backend\controllers;

use Yii;
use backend\models\SakipLkesubkriteria;
use backend\models\search\SakipLkesubkriteriaSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use backend\models\SakipLkekomponen;
use backend\models\search\SakipLkekomponenSearch;
use yii\web\UploadedFile;
use yii\helpers\Url;
use yii\web\Response;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\db\Expression;
use yii\helpers\Json;
use yii\helpers\ArrayHelper;

/**
 * SakipLkesubkriteriaController implements the CRUD actions for SakipLkesubkriteria model.
 */
class SakipLkesubkriteriaController extends Controller
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
     * Lists all SakipLkesubkriteria models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new SakipLkesubkriteriaSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
        $dataProvider->pagination = false;

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single SakipLkesubkriteria model.
     * @param int $reflkesubkriteria_id Reflkesubkriteria ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($reflkesubkriteria_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($reflkesubkriteria_id),
        ]);
    }

    /**
     * Creates a new SakipLkesubkriteria model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate($reflkekomponen_id = null, $reflkesubkomponen_id = null)
    {
        $model = new SakipLkesubkriteria();

        // Set the refcascadingprogram_id and refindikatorcascadingprogram_id from the GET parameters
        if ($reflkekomponen_id) {
            $model->reflkekomponen_id = $reflkekomponen_id;
        }

        // Set the refcascadingprogram_id and refindikatorcascadingprogram_id from the GET parameters
        if ($reflkesubkomponen_id) {
            $model->reflkesubkomponen_id = $reflkesubkomponen_id;
        }

        if (Yii::$app->request->isAjax) {
            if ($model->load(Yii::$app->request->post())) {
                if ($model->save()) {
                    Yii::$app->session->setFlash('success', 'Berhasil Tambah Data');
                    return $this->asJson(['success' => true, 'redirect' => \yii\helpers\Url::to(['sakip-lkekomponen/index'])]);
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
     * Updates an existing SakipLkesubkriteria model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $reflkesubkriteria_id Reflkesubkriteria ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($reflkesubkriteria_id)
    {
        $model = $this->findModel($reflkesubkriteria_id);

        if (Yii::$app->request->isAjax) {
            if ($model->load(Yii::$app->request->post())) {
                if ($model->save()) {
                    Yii::$app->session->setFlash('success', 'Berhasil Memperbarui Data');
                    return $this->asJson(['success' => true, 'redirect' => \yii\helpers\Url::to(['sakip-lkekomponen/index'])]);
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
     * Deletes an existing SakipLkesubkriteria model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $reflkesubkriteria_id Reflkesubkriteria ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($reflkesubkriteria_id)
    {
        $this->findModel($reflkesubkriteria_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the SakipLkesubkriteria model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $reflkesubkriteria_id Reflkesubkriteria ID
     * @return SakipLkesubkriteria the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($reflkesubkriteria_id)
    {
        if (($model = SakipLkesubkriteria::findOne(['reflkesubkriteria_id' => $reflkesubkriteria_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
