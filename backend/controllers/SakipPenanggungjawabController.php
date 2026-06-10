<?php

namespace backend\controllers;

use Yii;
use backend\models\SakipPenanggungjawab;
use backend\models\search\SakipPenanggungjawabSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use backend\models\SakipSkpd;
use yii\web\UploadedFile;
use yii\helpers\Url;
use yii\web\Response;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\db\Expression;
use yii\helpers\Json;
use yii\helpers\ArrayHelper;

/**
 * SakipPenanggungjawabController implements the CRUD actions for SakipPenanggungjawab model.
 */
class SakipPenanggungjawabController extends Controller
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
     * Lists all SakipPenanggungjawab models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new SakipPenanggungjawabSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
        $dataProvider->pagination = false;

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single SakipPenanggungjawab model.
     * @param int $refpenanggungjawab_id Refpenanggungjawab ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($refpenanggungjawab_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($refpenanggungjawab_id),
        ]);
    }

    /**
     * Creates a new SakipPenanggungjawab model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate($refpegawai_id = null, $refbidangbappeda_id = null)
    {
        $model = new SakipPenanggungjawab();

        if ($refpegawai_id) {
            $model->refpegawai_id = $refpegawai_id;
        }
        if ($refbidangbappeda_id) {
            $model->refbidangbappeda_id = $refbidangbappeda_id;
        }

        if (Yii::$app->request->isAjax) {
            if ($model->load(Yii::$app->request->post())) {
                if ($model->save()) {
                    Yii::$app->session->setFlash('success', 'Berhasil Tambah Data');
                    return $this->asJson(['success' => true, 'redirect' => \yii\helpers\Url::to(['sakip-pegawaibappeda/view', 'refpegawai_id' => $model->refpegawai_id])]);
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
     * Updates an existing SakipPenanggungjawab model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $refpenanggungjawab_id Refpenanggungjawab ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($refpenanggungjawab_id, $refpegawai_id = null, $refbidangbappeda_id = null)
    {
        $model = $this->findModel($refpenanggungjawab_id);

        if (Yii::$app->request->isAjax) {
            if ($model->load(Yii::$app->request->post())) {
                if ($model->save()) {
                    Yii::$app->session->setFlash('success', 'Berhasil Memperbarui Data');
                    // Update redirect URL to include refpegawai_id and refpegawai_id
                    return $this->asJson([
                        'success' => true,
                        'redirect' => \yii\helpers\Url::to([
                            'sakip-pegawaibappeda/view',
                            'refpegawai_id' => $model->refpegawai_id
                        ])
                    ]);
                } else {
                    // Log error for debugging purposes
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
     * Deletes an existing SakipPenanggungjawab model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $refpenanggungjawab_id Refpenanggungjawab ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($refpenanggungjawab_id)
    {
        $this->findModel($refpenanggungjawab_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the SakipPenanggungjawab model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $refpenanggungjawab_id Refpenanggungjawab ID
     * @return SakipPenanggungjawab the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($refpenanggungjawab_id)
    {
        if (($model = SakipPenanggungjawab::findOne(['refpenanggungjawab_id' => $refpenanggungjawab_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
