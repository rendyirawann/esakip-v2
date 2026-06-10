<?php

namespace frontend\controllers;

use Yii;
use frontend\models\SimonaRincianbelanjacascadingsubkegiatan;
use frontend\models\search\SimonaRincianbelanjacascadingsubkegiatanSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use frontend\models\SimonaRincianbelanjacascadingkegiatan;
use frontend\models\search\SimonaRincianbelanjacascadingkegiatanSearch;
use frontend\models\SimonaCascadingkegiatan;
use frontend\models\SimonaCascadingsubkegiatan;
use frontend\models\SakipCascadingsubkegiatan;
use yii\web\UploadedFile;
use yii\helpers\Url;
use yii\web\Response;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\db\Expression;
use yii\helpers\Json;
use yii\helpers\ArrayHelper;
use yii\filters\AccessControl;

/**
 * SimonaRincianbelanjacascadingsubkegiatanController implements the CRUD actions for SimonaRincianbelanjacascadingsubkegiatan model.
 */
class SimonaRincianbelanjacascadingsubkegiatanController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'access' => [
                    'class' => AccessControl::class,
                    'rules' => [
                        [
                            'allow' => true,
                            'roles' => ['@'], // Hanya untuk pengguna yang sudah login
                        ],
                    ],
                    'denyCallback' => function ($rule, $action) {
                        return Yii::$app->response->redirect(['site/login']);
                    },
                ],
                'verbs' => [
                    'class' => VerbFilter::class,
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all SimonaRincianbelanjacascadingsubkegiatan models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new SimonaRincianbelanjacascadingsubkegiatanSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
        $dataProvider->pagination = false;


        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single SimonaRincianbelanjacascadingsubkegiatan model.
     * @param int $refsimonarincianbelanjacascadingsubkegiatan_id Refsimonarincianbelanjacascadingsubkegiatan ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($refsimonarincianbelanjacascadingsubkegiatan_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($refsimonarincianbelanjacascadingsubkegiatan_id),
        ]);
    }

    /**
     * Creates a new SimonaRincianbelanjacascadingsubkegiatan model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate($refsimonacascadingsubkegiatan_id = null, $refcascadingkegiatan_id = null, $refkegiatan_id = null, $refcascadingprogram_id = null, $refprogram_id = null, $refcascadingsubkegiatan_id = null, $refsubkegiatan_id = null, $refperiode_id = null)
    {
        $model = new SimonaRincianbelanjacascadingsubkegiatan();

        // If refsimonacascadingkegiatan_id is provided, load the respective data
        if ($refsimonacascadingsubkegiatan_id) {
            $detail = SimonaCascadingsubkegiatan::findOne($refsimonacascadingsubkegiatan_id);
            if ($detail) {
                $model->refsimonacascadingsubkegiatan_id = $refsimonacascadingsubkegiatan_id;
            }
        }

        // Cek dan set nilai refcascadingkegiatan_id jika tersedia
        if ($refcascadingkegiatan_id) {
            $model->refcascadingkegiatan_id = $refcascadingkegiatan_id;
        }

        // Cek dan set nilai refkegiatan_id jika tersedia
        if ($refkegiatan_id) {
            $model->refkegiatan_id = $refkegiatan_id;
        }

        // Cek dan set nilai refcascadingkegiatan_id jika tersedia
        if ($refcascadingkegiatan_id) {
            $model->refcascadingkegiatan_id = $refcascadingkegiatan_id;
        }

        // Cek dan set nilai refkegiatan_id jika tersedia
        if ($refkegiatan_id) {
            $model->refkegiatan_id = $refkegiatan_id;
        }

        if ($refcascadingprogram_id) {
            $model->refcascadingprogram_id = $refcascadingprogram_id;
        }

        // Cek dan set nilai refprogram_id jika tersedia
        if ($refprogram_id) {
            $model->refprogram_id = $refprogram_id;
        }

        if ($refcascadingsubkegiatan_id) {
            $model->refcascadingsubkegiatan_id = $refcascadingsubkegiatan_id;
        }

        // Cek dan set nilai refsubkegiatan_id jika tersedia
        if ($refsubkegiatan_id) {
            $model->refsubkegiatan_id = $refsubkegiatan_id;
        }

        if ($refperiode_id) {
            $model->refperiode_id = $refperiode_id;
        }

        // If it's an AJAX request, render the form as AJAX
        if (Yii::$app->request->isAjax) {
            if (Yii::$app->request->isPost) { // Handle POST for saving data
                if ($model->load(Yii::$app->request->post()) && $model->save()) {
                    Yii::$app->session->setFlash('success', 'Data successfully added.');
                    return $this->asJson([
                        'success' => true,
                        'redirect' => Yii::$app->request->referrer ?: ['index'],
                    ]);
                } else {
                    // Add error log if saving fails
                    Yii::error("Error saving data: " . json_encode($model->getErrors()));
                    return $this->asJson(['success' => false, 'errors' => $model->getErrors()]);
                }
            }

            // Pass $detail to the _form view if needed
            return $this->renderAjax('_form', [
                'model' => $model,
                'detail' => $detail ?? null, // Pass $detail if available
            ]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing SimonaRincianbelanjacascadingsubkegiatan model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $refsimonarincianbelanjacascadingsubkegiatan_id Refsimonarincianbelanjacascadingsubkegiatan ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($refsimonarincianbelanjacascadingsubkegiatan_id)
    {
        $model = $this->findModel($refsimonarincianbelanjacascadingsubkegiatan_id);

        $detail = SimonaCascadingsubkegiatan::findOne($model->refsimonacascadingsubkegiatan_id);

        // If it's an AJAX request, render the form as AJAX
        if (Yii::$app->request->isAjax) {
            if (Yii::$app->request->isPost) { // Handle POST for saving data
                if ($model->load(Yii::$app->request->post()) && $model->save()) {
                    Yii::$app->session->setFlash('success', 'Data successfully added.');
                    return $this->asJson([
                        'success' => true,
                        'redirect' => Yii::$app->request->referrer ?: ['index'],
                    ]);
                } else {
                    // Add error log if saving fails
                    Yii::error("Error saving data: " . json_encode($model->getErrors()));
                    return $this->asJson(['success' => false, 'errors' => $model->getErrors()]);
                }
            }

            // Pass $detail to the _formupdate view
            return $this->renderAjax('_formupdate', [
                'model' => $model,
                'detail' => $detail, // Make sure $detail is passed
            ]);
        }

        // If it's not an AJAX request, render the standard update view
        return $this->render('update', [
            'model' => $model,
            'detail' => $detail, // Pass $detail to the update view as well
        ]);
    }

    /**
     * Deletes an existing SimonaRincianbelanjacascadingsubkegiatan model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $refsimonarincianbelanjacascadingsubkegiatan_id Refsimonarincianbelanjacascadingsubkegiatan ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($refsimonarincianbelanjacascadingsubkegiatan_id)
    {
        $this->findModel($refsimonarincianbelanjacascadingsubkegiatan_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the SimonaRincianbelanjacascadingsubkegiatan model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $refsimonarincianbelanjacascadingsubkegiatan_id Refsimonarincianbelanjacascadingsubkegiatan ID
     * @return SimonaRincianbelanjacascadingsubkegiatan the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($refsimonarincianbelanjacascadingsubkegiatan_id)
    {
        if (($model = SimonaRincianbelanjacascadingsubkegiatan::findOne(['refsimonarincianbelanjacascadingsubkegiatan_id' => $refsimonarincianbelanjacascadingsubkegiatan_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
