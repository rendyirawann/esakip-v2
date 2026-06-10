<?php

namespace backend\controllers;

use Yii;
use backend\models\SakipEselon;
use backend\models\search\SakipEselonSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use backend\models\SakipPeriode;
use backend\models\search\SakipPeriodeSearch;
use yii\web\UploadedFile;
use yii\helpers\Url;
use yii\web\Response;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\db\Expression;
use yii\helpers\Json;
use yii\helpers\ArrayHelper;

/**
 * SakipEselonController implements the CRUD actions for SakipEselon model.
 */
class SakipEselonController extends Controller
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
     * Lists all SakipEselon models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new SakipEselonSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
        $dataProvider->pagination = false;

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single SakipEselon model.
     * @param int $refeselon_id Refeselon ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($refeselon_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($refeselon_id),
        ]);
    }

    /**
     * Creates a new SakipEselon model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new SakipEselon();

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
     * Updates an existing SakipEselon model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $refeselon_id Refeselon ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($refeselon_id)
    {
        $model = $this->findModel($refeselon_id);

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
     * Deletes an existing SakipEselon model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $refeselon_id Refeselon ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($refeselon_id)
    {
        $this->findModel($refeselon_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the SakipEselon model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $refeselon_id Refeselon ID
     * @return SakipEselon the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($refeselon_id)
    {
        if (($model = SakipEselon::findOne(['refeselon_id' => $refeselon_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
