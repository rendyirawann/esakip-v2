<?php

namespace backend\controllers;

use backend\models\SakipLke;
use backend\models\search\SakipLkeSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * SakipLkeController implements the CRUD actions for SakipLke model.
 */
class SakipLkeController extends Controller
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
     * Lists all SakipLke models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new SakipLkeSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single SakipLke model.
     * @param int $reflke_id Reflke ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($reflke_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($reflke_id),
        ]);
    }

    /**
     * Creates a new SakipLke model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new SakipLke();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'reflke_id' => $model->reflke_id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing SakipLke model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $reflke_id Reflke ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($reflke_id)
    {
        $model = $this->findModel($reflke_id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'reflke_id' => $model->reflke_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing SakipLke model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $reflke_id Reflke ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($reflke_id)
    {
        $this->findModel($reflke_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the SakipLke model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $reflke_id Reflke ID
     * @return SakipLke the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($reflke_id)
    {
        if (($model = SakipLke::findOne(['reflke_id' => $reflke_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
