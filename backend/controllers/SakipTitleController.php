<?php

namespace backend\controllers;

use Yii;
use backend\models\SakipTitle;
use backend\models\search\SakipTitleSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
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
 * SakipTitleController implements the CRUD actions for SakipTitle model.
 */
class SakipTitleController extends Controller
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
     * Lists all SakipTitle models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new SakipTitleSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
        $dataProvider->pagination = false;

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single SakipTitle model.
     * @param int $reftitle_id Reftitle ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($reftitle_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($reftitle_id),
        ]);
    }

    /**
     * Creates a new SakipTitle model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new SakipTitle();

        if (Yii::$app->request->isAjax) {
            if ($model->load(Yii::$app->request->post())) {
                if ($model->save()) {
                    Yii::$app->session->setFlash('success', 'Berhasil Tambah Data');
                    return $this->asJson(['success' => true, 'redirect' => \yii\helpers\Url::to(['view', 'reftitle_id' => $model->reftitle_id])]);
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
     * Updates an existing SakipTitle model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $reftitle_id Reftitle ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($reftitle_id)
    {
        $model = $this->findModel($reftitle_id);

        if (Yii::$app->request->isAjax) {
            if ($model->load(Yii::$app->request->post())) {
                if ($model->save()) {
                    Yii::$app->session->setFlash('success', 'Berhasil Memperbarui Data');
                    return $this->asJson(['success' => true, 'redirect' => \yii\helpers\Url::to(['view', 'reftitle_id' => $model->reftitle_id])]);
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
     * Deletes an existing SakipTitle model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $reftitle_id Reftitle ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($reftitle_id)
    {
        $this->findModel($reftitle_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the SakipTitle model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $reftitle_id Reftitle ID
     * @return SakipTitle the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($reftitle_id)
    {
        if (($model = SakipTitle::findOne(['reftitle_id' => $reftitle_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
