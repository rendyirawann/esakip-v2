<?php

namespace backend\controllers;

use Yii;
use backend\models\SakipProgram;
use backend\models\SakipUrusan;
use backend\models\SakipBidang;
use backend\models\search\SakipProgramSearch;
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
 * SakipProgramController implements the CRUD actions for SakipProgram model.
 */
class SakipProgramController extends Controller
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
     * Lists all SakipProgram models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new SakipProgramSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
        $dataProvider->pagination = false;

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single SakipProgram model.
     * @param int $refprogram_id Refprogram ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($refprogram_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($refprogram_id),
        ]);
    }

    /**
     * Creates a new SakipProgram model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new SakipProgram();

        if (Yii::$app->request->isAjax) {
            if ($model->load(Yii::$app->request->post())) {
                if ($model->save()) {
                    Yii::$app->session->setFlash('success', 'Berhasil Tambah Data');
                    return $this->asJson(['success' => true, 'redirect' => \yii\helpers\Url::to(['view', 'refprogram_id' => $model->refprogram_id])]);
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
     * Updates an existing SakipProgram model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $refprogram_id Refprogram ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($refprogram_id)
    {
        $model = $this->findModel($refprogram_id);

        if (Yii::$app->request->isAjax) {
            if ($model->load(Yii::$app->request->post())) {
                if ($model->save()) {
                    Yii::$app->session->setFlash('success', 'Berhasil Memperbarui Data');
                    return $this->asJson(['success' => true, 'redirect' => \yii\helpers\Url::to(['view', 'refprogram_id' => $model->refprogram_id])]);
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

    public function actionLists($id)
    {
        $countBidang = SakipBidang::find()
            ->where(['refurusan_id' => $id])
            ->count();

        $bidang = SakipBidang::find()
            ->where(['refurusan_id' => $id])
            ->orderBy('kode_bidang ASC')
            ->all();

        if ($countBidang > 0) {
            foreach ($bidang as $b) {
                echo "<option value='" . $b->refbidang_id . "'>" . $b->kode_bidang . " - " . $b->nama_bidang . "</option>";
            }
        } else {
            echo "<option>-</option>";
        }
    }

    /**
     * Deletes an existing SakipProgram model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $refprogram_id Refprogram ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($refprogram_id)
    {
        $this->findModel($refprogram_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the SakipProgram model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $refprogram_id Refprogram ID
     * @return SakipProgram the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($refprogram_id)
    {
        if (($model = SakipProgram::findOne(['refprogram_id' => $refprogram_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
