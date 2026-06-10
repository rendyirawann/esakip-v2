<?php

namespace backend\controllers;

use Yii;
use backend\models\SakipSasaran;
use backend\models\SakipVisi;
use backend\models\SakipMisi;
use backend\models\SakipTujuan;
use backend\models\search\SakipSasaranSearch;
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
 * SakipSasaranController implements the CRUD actions for SakipSasaran model.
 */
class SakipSasaranController extends Controller
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
     * Lists all SakipSasaran models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new SakipSasaranSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
        $dataProvider->pagination = false;

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single SakipSasaran model.
     * @param int $refsasaran_id Refsasaran ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($refsasaran_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($refsasaran_id),
        ]);
    }

    /**
     * Creates a new SakipSasaran model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new SakipSasaran();

        if (Yii::$app->request->isAjax) {
            if ($model->load(Yii::$app->request->post())) {
                if ($model->save()) {
                    Yii::$app->session->setFlash('success', 'Berhasil Tambah Data');
                    return $this->asJson(['success' => true, 'redirect' => \yii\helpers\Url::to(['view', 'refsasaran_id' => $model->refsasaran_id])]);
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
    public function actionUpdate($refsasaran_id)
    {
        $model = $this->findModel($refsasaran_id);

        if (Yii::$app->request->isAjax) {
            if ($model->load(Yii::$app->request->post())) {
                if ($model->save()) {
                    Yii::$app->session->setFlash('success', 'Berhasil Memperbarui Data');
                    return $this->asJson(['success' => true, 'redirect' => \yii\helpers\Url::to(['view', 'refsasaran_id' => $model->refsasaran_id])]);
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
     * Deletes an existing SakipSasaran model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $refsasaran_id Refsasaran ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($refsasaran_id)
    {
        $this->findModel($refsasaran_id)->delete();

        return $this->redirect(['index']);
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

    public function actionListTujuan($id)
    {
        $countTujuan = SakipTujuan::find()
            ->where(['refmisi_id' => $id])
            ->count();

        $tujuan = SakipTujuan::find()
            ->where(['refmisi_id' => $id])
            ->all();

        if ($countTujuan > 0) {
            foreach ($tujuan as $t) {
                echo "<option value='" . $t->reftujuan_id . "'>" . Html::decode($t->uraian_tujuan) . "</option>";
            }
        } else {
            echo "<option>-</option>";
        }
    }

    public function actionDuplicate($refsasaran_id)
    {
        $model = $this->findModel($refsasaran_id);

        // Membuat model baru
        $newModel = new SakipSasaran(); // Ganti SakipSasaran dengan nama model yang sesuai
        $newModel->attributes = $model->attributes; // Duplikasi semua atribut

        // Increment refperiode_id
        $newModel->refperiode_id = $model->refperiode_id + 1; // Mengubah refperiode_id menjadi satu lebih

        // Ambil refvisi_id yang sesuai dengan periode baru
        $newVisi = $newPeriode = \backend\models\SakipPeriode::findOne($newModel->refperiode_id);
        $newP5Id = $newPeriode ? $newPeriode->refperiode_5tahun_id : null;
        $newVisi = SakipVisi::find()->where(['refperiode_5tahun_id' => $newP5Id])->one();
        $newModel->refvisi_id = $newVisi ? $newVisi->refvisi_id : null; // Atur refvisi_id sesuai periode baru atau null jika tidak ada

        // Increment refmisi_id dan reftujuan_id
        $newModel->refmisi_id = $model->refmisi_id + 5; // Mengincrement refmisi_id dengan 5
        $newModel->reftujuan_id = $model->reftujuan_id + 5; // Mengincrement reftujuan_id dengan 5

        $newModel->isNewRecord = true; // Tandai sebagai record baru
        $newModel->refsasaran_id = null; // Kosongkan ID agar tidak berbenturan

        // Simpan model baru
        if ($newModel->save()) {
            Yii::$app->session->setFlash('success', 'Data berhasil diduplikasi.');
            return $this->redirect(['view', 'refsasaran_id' => $newModel->refsasaran_id]); // Redirect ke halaman view dari data yang baru
        } else {
            Yii::$app->session->setFlash('error', 'Data gagal diduplikasi.');
            return $this->redirect(['index']); // Redirect ke halaman index jika gagal
        }
    }



    /**
     * Finds the SakipSasaran model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $refsasaran_id Refsasaran ID
     * @return SakipSasaran the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($refsasaran_id)
    {
        if (($model = SakipSasaran::findOne(['refsasaran_id' => $refsasaran_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
