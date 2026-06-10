<?php

namespace frontend\controllers;

use Yii;
use frontend\models\SimonaRincianbelanjacascadingkegiatan;
use frontend\models\search\SimonaRincianbelanjacascadingkegiatanSearch;
use frontend\models\SimonaCascadingkegiatan;
use frontend\models\SakipCascadingsubkegiatan;
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
use yii\filters\AccessControl;

/**
 * SimonaRincianbelanjacascadingkegiatanController implements the CRUD actions for SimonaRincianbelanjacascadingkegiatan model.
 */
class SimonaRincianbelanjacascadingkegiatanController extends Controller
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
     * Lists all SimonaRincianbelanjacascadingkegiatan models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new SimonaRincianbelanjacascadingkegiatanSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
        $dataProvider->pagination = false;

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single SimonaRincianbelanjacascadingkegiatan model.
     * @param int $refsimonarincianbelanjacascadingkegiatan_id Refsimonarincianbelanjacascadingkegiatan ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($refsimonarincianbelanjacascadingkegiatan_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($refsimonarincianbelanjacascadingkegiatan_id),
        ]);
    }

    /**
     * Creates a new SimonaRincianbelanjacascadingkegiatan model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate($refsimonacascadingkegiatan_id = null, $refcascadingkegiatan_id = null, $refkegiatan_id = null, $refcascadingprogram_id = null, $refprogram_id = null, $refperiode_id = null)
    {
        $model = new SimonaRincianbelanjacascadingkegiatan();

        // Ambil refskpd_id dari user saat ini
        $user = Yii::$app->user->identity;
        $refskpd_id = $user->refskpd_id;

        // If refsimonacascadingkegiatan_id is provided, load the respective data
        if ($refsimonacascadingkegiatan_id) {
            $detail = SimonaCascadingKegiatan::findOne($refsimonacascadingkegiatan_id);
            if ($detail) {
                $model->refsimonacascadingkegiatan_id = $refsimonacascadingkegiatan_id;
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

        if ($refperiode_id) {
            $model->refperiode_id = $refperiode_id;
        }

        // Calculate subkegiatanAnggaran
        $subkegiatanAnggaran = SakipCascadingsubkegiatan::find()
            ->where([
                'refkegiatan_id' => $refkegiatan_id,
                'refcascadingkegiatan_id' => $refcascadingkegiatan_id,
            ])
            ->sum('CAST(subkegiatan_anggaran AS UNSIGNED)');

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
     * Updates an existing SimonaRincianbelanjacascadingkegiatan model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $refsimonarincianbelanjacascadingkegiatan_id Refsimonarincianbelanjacascadingkegiatan ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($refsimonarincianbelanjacascadingkegiatan_id)
    {
        $model = $this->findModel($refsimonarincianbelanjacascadingkegiatan_id);

        $detail = SimonaCascadingkegiatan::findOne($model->refsimonacascadingkegiatan_id);

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
     * Deletes an existing SimonaRincianbelanjacascadingkegiatan model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $refsimonarincianbelanjacascadingkegiatan_id Refsimonarincianbelanjacascadingkegiatan ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($refsimonarincianbelanjacascadingkegiatan_id)
    {
        $this->findModel($refsimonarincianbelanjacascadingkegiatan_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the SimonaRincianbelanjacascadingkegiatan model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $refsimonarincianbelanjacascadingkegiatan_id Refsimonarincianbelanjacascadingkegiatan ID
     * @return SimonaRincianbelanjacascadingkegiatan the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($refsimonarincianbelanjacascadingkegiatan_id)
    {
        if (($model = SimonaRincianbelanjacascadingkegiatan::findOne(['refsimonarincianbelanjacascadingkegiatan_id' => $refsimonarincianbelanjacascadingkegiatan_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
