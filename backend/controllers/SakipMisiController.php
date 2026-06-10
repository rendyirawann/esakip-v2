<?php

namespace backend\controllers;

use Yii;
use backend\models\SakipMisi;
use backend\models\SakipVisi;
use backend\models\search\SakipMisiSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\web\Response;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\db\Expression;
use yii\helpers\Json;
use yii\helpers\ArrayHelper;

/**
 * SakipMisiController implements the CRUD actions for SakipMisi model.
 */
class SakipMisiController extends Controller
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
     * Lists all SakipMisi models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new SakipMisiSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
        $dataProvider->pagination = false;

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single SakipMisi model.
     * @param int $refmisi_id Refmisi ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($refmisi_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($refmisi_id),
        ]);
    }

    /**
     * Creates a new SakipMisi model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new SakipMisi();

        if (Yii::$app->request->isAjax) {
            if ($model->load(Yii::$app->request->post())) {
                if ($model->save()) {
                    Yii::$app->session->setFlash('success', 'Berhasil Tambah Data');
                    return $this->asJson(['success' => true, 'redirect' => \yii\helpers\Url::to(['view', 'refmisi_id' => $model->refmisi_id])]);
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
     * Updates an existing Sakipmisi model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $refmisi_id Refmisi ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($refmisi_id)
    {
        $model = $this->findModel($refmisi_id);

        if (Yii::$app->request->isAjax) {
            if ($model->load(Yii::$app->request->post())) {
                if ($model->save()) {
                    Yii::$app->session->setFlash('success', 'Berhasil Memperbarui Data');
                    return $this->asJson(['success' => true, 'redirect' => \yii\helpers\Url::to(['view', 'refmisi_id' => $model->refmisi_id])]);
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
        $periode = \backend\models\SakipPeriode::findOne($id);
        $p5Id = $periode ? $periode->refperiode_5tahun_id : null;
        $countVisi = SakipVisi::find()
            ->where(['refperiode_5tahun_id' => $p5Id])
            ->count();

        $visi = SakipVisi::find()
            ->where(['refperiode_5tahun_id' => $p5Id])
            ->all();

        if ($countVisi > 0) {
            foreach ($visi as $v) {
                echo "<option value='" . $v->refvisi_id . "'>" . $v->uraian_visi . " - " . $v->refperiode_id . "</option>";
            }
        } else {
            echo "<option>-</option>";
        }
    }



    /**
     * Deletes an existing SakipMisi model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $refmisi_id Refmisi ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($refmisi_id)
    {
        $this->findModel($refmisi_id)->delete();

        return $this->redirect(['index']);
    }

    public function actionDuplicate($refmisi_id)
    {
        $model = $this->findModel($refmisi_id);

        $newModel = new SakipMisi(); // Ganti SakipMisi dengan nama model yang sesuai
        $newModel->attributes = $model->attributes; // Duplikasi semua atribut

        // Inkrementasi refperiode_id
        $newModel->refperiode_id = $model->refperiode_id + 1; // Mengubah refperiode_id menjadi satu lebih

        // Ambil refvisi_id yang sesuai dengan periode baru
        $newVisi = $newPeriode = \backend\models\SakipPeriode::findOne($newModel->refperiode_id);
        $newP5Id = $newPeriode ? $newPeriode->refperiode_5tahun_id : null;
        $newVisi = SakipVisi::find()->where(['refperiode_5tahun_id' => $newP5Id])->one();
        $newModel->refvisi_id = $newVisi ? $newVisi->refvisi_id : null; // Atur refvisi_id sesuai periode baru atau null jika tidak ada

        $newModel->isNewRecord = true; // Tandai sebagai record baru
        $newModel->refmisi_id = null; // Kosongkan ID agar tidak berbenturan

        if ($newModel->save()) {
            Yii::$app->session->setFlash('success', 'Data berhasil diduplikasi.');
            return $this->redirect(['view', 'refmisi_id' => $newModel->refmisi_id]); // Redirect ke halaman view dari data yang baru
        } else {
            Yii::$app->session->setFlash('error', 'Data gagal diduplikasi.');
            return $this->redirect(['index']); // Redirect ke halaman index jika gagal
        }
    }



    /**
     * Finds the SakipMisi model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $refmisi_id Refmisi ID
     * @return SakipMisi the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($refmisi_id)
    {
        if (($model = SakipMisi::findOne(['refmisi_id' => $refmisi_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
