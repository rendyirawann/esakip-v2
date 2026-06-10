<?php

namespace backend\controllers;

use Yii;
use backend\models\SakipSasaranP;
use backend\models\SakipVisiP;
use backend\models\SakipMisiP;
use backend\models\SakipTujuanP;
use backend\models\search\SakipSasaranPSearch;
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
 * SakipSasaranPController implements the CRUD actions for SakipSasaranP model.
 */
class SakipSasaranPController extends Controller
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
     * Lists all SakipSasaranP models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new SakipSasaranPSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
        $dataProvider->pagination = false;

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single SakipSasaranP model.
     * @param int $refsasaran_p_id Refsasaran ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($refsasaran_p_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($refsasaran_p_id),
        ]);
    }

    /**
     * Creates a new SakipSasaranP model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new SakipSasaranP();

        if (Yii::$app->request->isAjax) {
            if ($model->load(Yii::$app->request->post())) {
                if ($model->save()) {
                    Yii::$app->session->setFlash('success', 'Berhasil Tambah Data');
                    return $this->asJson(['success' => true, 'redirect' => \yii\helpers\Url::to(['view', 'refsasaran_p_id' => $model->refsasaran_p_id])]);
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
     * @param int $reftujuan_p_id Reftujuan ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($refsasaran_p_id)
    {
        $model = $this->findModel($refsasaran_p_id);

        if (Yii::$app->request->isAjax) {
            if ($model->load(Yii::$app->request->post())) {
                if ($model->save()) {
                    Yii::$app->session->setFlash('success', 'Berhasil Memperbarui Data');
                    return $this->asJson(['success' => true, 'redirect' => \yii\helpers\Url::to(['view', 'refsasaran_p_id' => $model->refsasaran_p_id])]);
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
     * Deletes an existing SakipSasaranP model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $refsasaran_p_id Refsasaran ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($refsasaran_p_id)
    {
        $this->findModel($refsasaran_p_id)->delete();

        return $this->redirect(['index']);
    }

    public function actionLists($id)
    {
        $periode = \backend\models\SakipPeriode::findOne($id);
        $p5Id = $periode ? $periode->refperiode_5tahun_id : null;
        $countVisi = SakipVisiP::find()
            ->where(['refperiode_5tahun_id' => $p5Id])
            ->count();

        $visi = SakipVisiP::find()
            ->where(['refperiode_5tahun_id' => $p5Id])
            ->all();

        if ($countVisi > 0) {
            foreach ($visi as $v) {
                echo "<option value='" . $v->refvisi_p_id . "'>" . $v->uraian_visi_p . " - " . $v->refperiode_id . "</option>";
            }
        } else {
            echo "<option>-</option>";
        }
    }

    public function actionListMisi($id)
    {
        $countMisi = SakipMisiP::find()
            ->where(['refvisi_p_id' => $id])
            ->count();

        $misi = SakipMisiP::find()
            ->where(['refvisi_p_id' => $id])
            ->all();

        if ($countMisi > 0) {
            foreach ($misi as $m) {
                echo "<option value='" . $m->refmisi_p_id . "'>" . Html::decode($m->uraian_misi_p) . "</option>";
            }
        } else {
            echo "<option>-</option>";
        }
    }

    public function actionListTujuan($id)
    {
        $countTujuan = SakipTujuanP::find()
            ->where(['refmisi_p_id' => $id])
            ->count();

        $tujuan = SakipTujuanP::find()
            ->where(['refmisi_p_id' => $id])
            ->all();

        if ($countTujuan > 0) {
            foreach ($tujuan as $t) {
                echo "<option value='" . $t->reftujuan_p_id . "'>" . Html::decode($t->uraian_tujuan_p) . "</option>";
            }
        } else {
            echo "<option>-</option>";
        }
    }

    public function actionDuplicate($refsasaran_p_id)
    {
        $model = $this->findModel($refsasaran_p_id);

        // Membuat model baru
        $newModel = new SakipSasaranP(); // Ganti SakipSasaranP dengan nama model yang sesuai
        $newModel->attributes = $model->attributes; // Duplikasi semua atribut

        // Increment refperiode_id
        $newModel->refperiode_id = $model->refperiode_id + 1; // Mengubah refperiode_id menjadi satu lebih

        // Ambil refvisi_p_id yang sesuai dengan periode baru
        $newVisi = $newPeriode = \backend\models\SakipPeriode::findOne($newModel->refperiode_id);
        $newP5Id = $newPeriode ? $newPeriode->refperiode_5tahun_id : null;
        $newVisi = SakipVisiP::find()->where(['refperiode_5tahun_id' => $newP5Id])->one();
        $newModel->refvisi_p_id = $newVisi ? $newVisi->refvisi_p_id : null; // Atur refvisi_p_id sesuai periode baru atau null jika tidak ada

        // Increment refmisi_p_id dan reftujuan_p_id
        $newModel->refmisi_p_id = $model->refmisi_p_id + 5; // Mengincrement refmisi_p_id dengan 5
        $newModel->reftujuan_p_id = $model->reftujuan_p_id + 5; // Mengincrement reftujuan_p_id dengan 5

        $newModel->isNewRecord = true; // Tandai sebagai record baru
        $newModel->refsasaran_p_id = null; // Kosongkan ID agar tidak berbenturan

        // Simpan model baru
        if ($newModel->save()) {
            Yii::$app->session->setFlash('success', 'Data berhasil diduplikasi.');
            return $this->redirect(['view', 'refsasaran_p_id' => $newModel->refsasaran_p_id]); // Redirect ke halaman view dari data yang baru
        } else {
            Yii::$app->session->setFlash('error', 'Data gagal diduplikasi.');
            return $this->redirect(['index']); // Redirect ke halaman index jika gagal
        }
    }



    /**
     * Finds the SakipSasaranP model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $refsasaran_p_id Refsasaran ID
     * @return SakipSasaranP the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($refsasaran_p_id)
    {
        if (($model = SakipSasaranP::findOne(['refsasaran_p_id' => $refsasaran_p_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
