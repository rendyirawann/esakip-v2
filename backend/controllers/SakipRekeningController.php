<?php

namespace backend\controllers;

use Yii;
use backend\models\SakipRekening;
use backend\models\search\SakipRekeningSearch;
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
 * SakipRekeningController implements the CRUD actions for SakipRekening model.
 */
class SakipRekeningController extends Controller
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
     * Lists all SakipRekening models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new SakipRekeningSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
        $dataProvider->pagination = false;

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single SakipRekening model.
     * @param int $refrekening_id Refrekening ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($refrekening_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($refrekening_id),
        ]);
    }

    /**
     * Creates a new SakipRekening model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new SakipRekening();

        if (Yii::$app->request->isAjax) {
            if ($model->load(Yii::$app->request->post())) {
                if ($model->save()) {
                    Yii::$app->session->setFlash('success', 'Berhasil Tambah Data');
                    return $this->asJson(['success' => true, 'redirect' => \yii\helpers\Url::to(['view', 'refrekening_id' => $model->refrekening_id])]);
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
    public function actionUpdate($refrekening_id)
    {
        $model = $this->findModel($refrekening_id);
    
        if (Yii::$app->request->isAjax) {
            if ($model->load(Yii::$app->request->post())) {
                if ($model->save()) {
                    Yii::$app->session->setFlash('success', 'Berhasil Memperbarui Data');
                    return $this->asJson(['success' => true, 'redirect' => \yii\helpers\Url::to(['view', 'refrekening_id' => $model->refrekening_id])]);
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
     * Deletes an existing SakipRekening model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $refrekening_id Refrekening ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($refrekening_id)
    {
        $this->findModel($refrekening_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the SakipRekening model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $refrekening_id Refrekening ID
     * @return SakipRekening the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($refrekening_id)
    {
        if (($model = SakipRekening::findOne(['refrekening_id' => $refrekening_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
