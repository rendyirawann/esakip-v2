<?php

namespace backend\controllers;

use Yii;
use backend\models\SakipBidangbappeda;
use backend\models\search\SakipBidangbappedaSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use backend\models\SakipTitle;
use backend\models\search\SakipTitleSearch;
use backend\models\SakipBidang;
use backend\models\SakipProgram;
use backend\models\SakipKegiatan;
use backend\models\SakipUrusan;
use backend\models\search\SakipBidangSearch;
use yii\web\UploadedFile;
use yii\helpers\Url;
use yii\web\Response;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\db\Expression;
use yii\helpers\Json;
use yii\helpers\ArrayHelper;

/**
 * SakipBidangbappedaController implements the CRUD actions for SakipBidangbappeda model.
 */
class SakipBidangbappedaController extends Controller
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
     * Lists all SakipBidangbappeda models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new SakipBidangbappedaSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
        $dataProvider->pagination = false;

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single SakipBidangbappeda model.
     * @param int $refbidangbappeda_id Refbidangbappeda ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($refbidangbappeda_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($refbidangbappeda_id),
        ]);
    }

    /**
     * Creates a new SakipBidangbappeda model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new SakipBidangbappeda();

        if (Yii::$app->request->isAjax) {
            if ($model->load(Yii::$app->request->post())) {
                if ($model->save()) {
                    Yii::$app->session->setFlash('success', 'Berhasil Tambah Data');
                    return $this->asJson(['success' => true, 'redirect' => \yii\helpers\Url::to(['view', 'refbidangbappeda_id' => $model->refbidangbappeda_id])]);
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
     * Updates an existing SakipBidangbappeda model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $refbidangbappeda_id Refbidangbappeda ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($refbidangbappeda_id)
    {
        $model = $this->findModel($refbidangbappeda_id);

        if (Yii::$app->request->isAjax) {
            if ($model->load(Yii::$app->request->post())) {
                if ($model->save()) {
                    Yii::$app->session->setFlash('success', 'Berhasil Memperbarui Data');
                    return $this->asJson(['success' => true, 'redirect' => \yii\helpers\Url::to(['view', 'refbidangbappeda_id' => $model->refbidangbappeda_id])]);
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
     * Deletes an existing SakipBidangbappeda model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $refbidangbappeda_id Refbidangbappeda ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($refbidangbappeda_id)
    {
        $this->findModel($refbidangbappeda_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the SakipBidangbappeda model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $refbidangbappeda_id Refbidangbappeda ID
     * @return SakipBidangbappeda the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($refbidangbappeda_id)
    {
        if (($model = SakipBidangbappeda::findOne(['refbidangbappeda_id' => $refbidangbappeda_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
