<?php

namespace frontend\controllers;

use frontend\models\SakipIndikatorsasaranrenstraPTriwulan;
use frontend\models\search\SakipIndikatorsasaranrenstraPTriwulanSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * SakipIndikatorsasaranrenstraPTriwulanController implements the CRUD actions for SakipIndikatorsasaranrenstraPTriwulan model.
 */
class SakipIndikatorsasaranrenstraPTriwulanController extends Controller
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
     * Lists all SakipIndikatorsasaranrenstraPTriwulan models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new SakipIndikatorsasaranrenstraPTriwulanSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single SakipIndikatorsasaranrenstraPTriwulan model.
     * @param int $refindikatorsasaranrenstratriwulan_p_id Refindikatorsasaranrenstratriwulan P ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($refindikatorsasaranrenstratriwulan_p_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($refindikatorsasaranrenstratriwulan_p_id),
        ]);
    }

    /**
     * Creates a new SakipIndikatorsasaranrenstraPTriwulan model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new SakipIndikatorsasaranrenstraPTriwulan();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'refindikatorsasaranrenstratriwulan_p_id' => $model->refindikatorsasaranrenstratriwulan_p_id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing SakipIndikatorsasaranrenstraPTriwulan model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $refindikatorsasaranrenstratriwulan_p_id Refindikatorsasaranrenstratriwulan P ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($refindikatorsasaranrenstratriwulan_p_id)
    {
        $model = $this->findModel($refindikatorsasaranrenstratriwulan_p_id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'refindikatorsasaranrenstratriwulan_p_id' => $model->refindikatorsasaranrenstratriwulan_p_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing SakipIndikatorsasaranrenstraPTriwulan model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $refindikatorsasaranrenstratriwulan_p_id Refindikatorsasaranrenstratriwulan P ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($refindikatorsasaranrenstratriwulan_p_id)
    {
        $this->findModel($refindikatorsasaranrenstratriwulan_p_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the SakipIndikatorsasaranrenstraPTriwulan model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $refindikatorsasaranrenstratriwulan_p_id Refindikatorsasaranrenstratriwulan P ID
     * @return SakipIndikatorsasaranrenstraPTriwulan the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($refindikatorsasaranrenstratriwulan_p_id)
    {
        if (($model = SakipIndikatorsasaranrenstraPTriwulan::findOne(['refindikatorsasaranrenstratriwulan_p_id' => $refindikatorsasaranrenstratriwulan_p_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
