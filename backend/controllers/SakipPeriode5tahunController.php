<?php

namespace backend\controllers;

use Yii;
use backend\models\SakipPeriode5tahun;
use backend\models\search\SakipPeriode5tahunSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * SakipPeriode5tahunController implements the CRUD actions for SakipPeriode5tahun model.
 */
class SakipPeriode5tahunController extends Controller
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
     * Lists all SakipPeriode5tahun models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new SakipPeriode5tahunSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
        $dataProvider->pagination = false;

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single SakipPeriode5tahun model.
     * @param int $refperiode_5tahun_id Refperiode 5 Tahun ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($refperiode_5tahun_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($refperiode_5tahun_id),
        ]);
    }

    /**
     * Creates a new SakipPeriode5tahun model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new SakipPeriode5tahun();

        if (Yii::$app->request->isAjax) {
            if ($model->load(Yii::$app->request->post())) {
                if ($model->save()) {
                    Yii::$app->session->setFlash('success', 'Berhasil Tambah Data');
                    return $this->asJson(['success' => true, 'redirect' => \yii\helpers\Url::to(['view', 'refperiode_5tahun_id' => $model->refperiode_5tahun_id])]);
                } else {
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
     * Updates an existing SakipPeriode5tahun model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $refperiode_5tahun_id Refperiode 5 Tahun ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($refperiode_5tahun_id)
    {
        $model = $this->findModel($refperiode_5tahun_id);

        if (Yii::$app->request->isAjax) {
            if ($model->load(Yii::$app->request->post())) {
                if ($model->save()) {
                    Yii::$app->session->setFlash('success', 'Berhasil Memperbarui Data');
                    return $this->asJson(['success' => true, 'redirect' => \yii\helpers\Url::to(['view', 'refperiode_5tahun_id' => $model->refperiode_5tahun_id])]);
                } else {
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
     * Deletes an existing SakipPeriode5tahun model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $refperiode_5tahun_id Refperiode 5 Tahun ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($refperiode_5tahun_id)
    {
        $this->findModel($refperiode_5tahun_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the SakipPeriode5tahun model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $refperiode_5tahun_id Refperiode 5 Tahun ID
     * @return SakipPeriode5tahun the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($refperiode_5tahun_id)
    {
        if (($model = SakipPeriode5tahun::findOne(['refperiode_5tahun_id' => $refperiode_5tahun_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
