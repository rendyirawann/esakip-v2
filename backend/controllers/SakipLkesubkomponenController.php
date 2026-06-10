<?php

namespace backend\controllers;

use Yii;
use backend\models\SakipLkesubkomponen;
use backend\models\search\SakipLkesubkomponenSearch;
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
 * SakipLkesubkomponenController implements the CRUD actions for SakipLkesubkomponen model.
 */
class SakipLkesubkomponenController extends Controller
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
     * Lists all SakipLkesubkomponen models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new SakipLkesubkomponenSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
        $dataProvider->pagination = false;

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single SakipLkesubkomponen model.
     * @param int $reflkesubkomponen_id Reflkesubkomponen ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($reflkesubkomponen_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($reflkesubkomponen_id),
        ]);
    }

    /**
     * Creates a new SakipLkesubkomponen model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate($reflkekomponen_id = null)
    {
        $model = new SakipLkesubkomponen();

        // Set the refcascadingprogram_id and refindikatorcascadingprogram_id from the GET parameters
        if ($reflkekomponen_id) {
            $model->reflkekomponen_id = $reflkekomponen_id;
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
     * Updates an existing SakipLkesubkomponen model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $reflkesubkomponen_id Reflkesubkomponen ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($reflkesubkomponen_id)
    {
        $model = $this->findModel($reflkesubkomponen_id);

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
     * Deletes an existing SakipLkesubkomponen model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $reflkesubkomponen_id Reflkesubkomponen ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($reflkesubkomponen_id)
    {
        $this->findModel($reflkesubkomponen_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the SakipLkesubkomponen model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $reflkesubkomponen_id Reflkesubkomponen ID
     * @return SakipLkesubkomponen the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($reflkesubkomponen_id)
    {
        if (($model = SakipLkesubkomponen::findOne(['reflkesubkomponen_id' => $reflkesubkomponen_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
