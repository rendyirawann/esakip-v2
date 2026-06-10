<?php

namespace backend\controllers;

use Yii;
use backend\models\SakipTujuan;
use backend\models\SakipVisi;
use backend\models\SakipMisi;
use backend\models\search\SakipTujuanSearch;
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
 * SakipTujuanController implements the CRUD actions for SakipTujuan model.
 */
class SakipTujuanController extends Controller
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
     * Lists all SakipTujuan models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new SakipTujuanSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
        $dataProvider->pagination = false;

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single SakipTujuan model.
     * @param int $reftujuan_id Reftujuan ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($reftujuan_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($reftujuan_id),
        ]);
    }

    /**
     * Creates a new SakipTujuan model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new SakipTujuan();

        if (Yii::$app->request->isAjax) {
            if ($model->load(Yii::$app->request->post())) {
                if ($model->save()) {
                    Yii::$app->session->setFlash('success', 'Berhasil Tambah Data');
                    return $this->asJson(['success' => true, 'redirect' => \yii\helpers\Url::to(['view', 'reftujuan_id' => $model->reftujuan_id])]);
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
     * Updates an existing Sakiptujuan model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $reftujuan_id Reftujuan ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($reftujuan_id)
    {
        $model = $this->findModel($reftujuan_id);

        if (Yii::$app->request->isAjax) {
            if ($model->load(Yii::$app->request->post())) {
                if ($model->save()) {
                    Yii::$app->session->setFlash('success', 'Berhasil Memperbarui Data');
                    return $this->asJson(['success' => true, 'redirect' => \yii\helpers\Url::to(['view', 'reftujuan_id' => $model->reftujuan_id])]);
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
     * Deletes an existing SakipTujuan model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $reftujuan_id Reftujuan ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($reftujuan_id)
    {
        $this->findModel($reftujuan_id)->delete();

        return $this->redirect(['index']);
    }

    public function actionDuplicate($reftujuan_id)
    {
        $model = $this->findModel($reftujuan_id);

        // Buat model baru berdasarkan data yang di-duplikat
        $newModel = new SakipTujuan();
        $newModel->attributes = $model->attributes;
        $newModel->isNewRecord = true; // Tandai sebagai record baru
        $newModel->reftujuan_id = null; // Kosongkan ID agar tidak berbenturan

        // Increment refperiode_id
        $newModel->refperiode_id = $model->refperiode_id + 1;

        // Cari refvisi_id yang sesuai dengan refperiode_id baru
        $newVisi = $newPeriode = \backend\models\SakipPeriode::findOne($newModel->refperiode_id);
        $newP5Id = $newPeriode ? $newPeriode->refperiode_5tahun_id : null;
        $newVisi = SakipVisi::find()
            ->where(['refperiode_5tahun_id' => $newP5Id])
            ->one();

        if ($newVisi) {
            $newModel->refvisi_id = $newVisi->refvisi_id;
        } else {
            // Jika tidak ditemukan visi yang sesuai, berikan pesan error dan redirect ke halaman index
            Yii::$app->session->setFlash('error', 'Visi untuk periode baru tidak ditemukan.');
            return $this->redirect(['index']);
        }

        // Increment refmisi_id dengan mencari nilai terbesar kemudian ditambah 1
        $newMisiId = SakipTujuan::find()
            ->select('MAX(refmisi_id) as refmisi_id')
            ->scalar();

        $newModel->refmisi_id = $newMisiId + 1;

        // Simpan model baru
        if ($newModel->save()) {
            Yii::$app->session->setFlash('success', 'Data berhasil diduplikasi.');
            return $this->redirect(['view', 'reftujuan_id' => $newModel->reftujuan_id]); // Redirect ke halaman view dari data yang baru
        } else {
            Yii::$app->session->setFlash('error', 'Data gagal diduplikasi.');
            return $this->redirect(['index']); // Redirect ke halaman index jika gagal
        }
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

    public function actionListMisi($id)
    {
        $countMisi = SakipMisi::find()
            ->where(['refvisi_id' => $id])
            ->count();

        $misi = SakipMisi::find()
            ->where(['refvisi_id' => $id])
            ->all();

        if ($countMisi > 0) {
            foreach ($misi as $m) {
                echo "<option value='" . $m->refmisi_id . "'>" . Html::decode($m->uraian_misi) . "</option>";
            }
        } else {
            echo "<option>-</option>";
        }
    }


    /**
     * Finds the SakipTujuan model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $reftujuan_id Reftujuan ID
     * @return SakipTujuan the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($reftujuan_id)
    {
        if (($model = SakipTujuan::findOne(['reftujuan_id' => $reftujuan_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
