<?php

namespace backend\controllers;

use Yii;
use backend\models\SakipKoordinasi;
use backend\models\search\SakipKoordinasiSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use backend\models\SakipSkpd;
use backend\models\User;
use yii\web\UploadedFile;
use yii\helpers\Url;
use yii\web\Response;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\db\Expression;
use yii\helpers\Json;
use yii\helpers\ArrayHelper;

/**
 * SakipKoordinasiController implements the CRUD actions for SakipKoordinasi model.
 */
class SakipKoordinasiController extends Controller
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
     * Lists all SakipKoordinasi models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new SakipKoordinasiSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
        $dataProvider->pagination = false;

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single SakipKoordinasi model.
     * @param int $refkoordinasi_id Refkoordinasi ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($refkoordinasi_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($refkoordinasi_id),
        ]);
    }

    /**
     * Creates a new SakipKoordinasi model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate($refuser_id) // 1. Terima parameter refuser_id
    {
        $model = new SakipKoordinasi();
        $model->refuser_id = $refuser_id; // 2. Set refuser_id secara otomatis

        if (Yii::$app->request->isAjax) {
            if ($model->load(Yii::$app->request->post())) {
                if ($model->save()) {
                    Yii::$app->session->setFlash('success', 'Berhasil Tambah Data');
                    return $this->asJson(['success' => true, 'redirect' => \yii\helpers\Url::to(['user/view', 'id' => $model->refuser_id])]);
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
     * Updates an existing SakipKoordinasi model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $refkoordinasi_id Refkoordinasi ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($refkoordinasi_id)
    {
        $model = $this->findModel($refkoordinasi_id);

        if (Yii::$app->request->isAjax) {
            if ($model->load(Yii::$app->request->post())) {
                if ($model->save()) {
                    Yii::$app->session->setFlash('success', 'Berhasil Memperbarui Data');
                    return $this->asJson(['success' => true, 'redirect' => \yii\helpers\Url::to(['user/view', 'id' => $model->refuser_id])]);
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
     * Deletes an existing SakipKoordinasi model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $refkoordinasi_id Refkoordinasi ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($refkoordinasi_id)
    {
        // 1. Cari model yang akan dihapus
        $model = $this->findModel($refkoordinasi_id);

        // 2. Simpan refuser_id ke dalam sebuah variabel SEBELUM model dihapus
        $refuser_id = $model->refuser_id;

        // 3. Hapus model dari database
        $model->delete();

        // Tambahkan flash message untuk notifikasi (opsional tapi direkomendasikan)
        Yii::$app->session->setFlash('success', 'Data Koordinasi SKPD berhasil dihapus.');

        // 4. Redirect ke halaman user/view dengan id dari variabel yang sudah disimpan
        return $this->redirect(['/user/view', 'id' => $refuser_id]);
    }

    /**
     * Finds the SakipKoordinasi model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $refkoordinasi_id Refkoordinasi ID
     * @return SakipKoordinasi the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($refkoordinasi_id)
    {
        if (($model = SakipKoordinasi::findOne(['refkoordinasi_id' => $refkoordinasi_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
