<?php

namespace backend\controllers;

use Yii;
use backend\models\UserGroup;
use backend\models\search\UserGroupSearch;
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
 * UserGroupController implements the CRUD actions for UserGroup model.
 */
class UserGroupController extends Controller
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
     * Lists all UserGroup models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new UserGroupSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
        $dataProvider->pagination = false;

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single UserGroup model.
     * @param int $kode_group Kode Group
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($kode_group)
    {
        return $this->render('view', [
            'model' => $this->findModel($kode_group),
        ]);
    }

    /**
     * Creates a new UserGroup model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new UserGroup();

        if (Yii::$app->request->isAjax) {
            if ($model->load(Yii::$app->request->post())) {
                if ($model->save()) {
                    Yii::$app->session->setFlash('success', 'Berhasil Tambah Data');
                    return $this->asJson(['success' => true, 'redirect' => \yii\helpers\Url::to(['view', 'kode_group' => $model->kode_group])]);
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
     * Updates an existing Sakipperiode model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $kode_group Refperiode ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($kode_group)
    {
        $model = $this->findModel($kode_group);
    
        if (Yii::$app->request->isAjax) {
            if ($model->load(Yii::$app->request->post())) {
                if ($model->save()) {
                    Yii::$app->session->setFlash('success', 'Berhasil Memperbarui Data');
                    return $this->asJson(['success' => true, 'redirect' => \yii\helpers\Url::to(['view', 'kode_group' => $model->kode_group])]);
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
     * Deletes an existing UserGroup model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $kode_group Kode Group
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($kode_group)
    {
        $this->findModel($kode_group)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the UserGroup model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $kode_group Kode Group
     * @return UserGroup the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($kode_group)
    {
        if (($model = UserGroup::findOne(['kode_group' => $kode_group])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
