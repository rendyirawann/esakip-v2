<?php

namespace backend\controllers;

use Yii;
use backend\models\SakipTujuanP;
use backend\models\SakipVisiP;
use backend\models\SakipMisiP;
use backend\models\search\SakipTujuanPSearch;
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
 * SakipTujuanPController implements the CRUD actions for SakipTujuanP model.
 */
class SakipTujuanPController extends Controller
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
     * Lists all SakipTujuanP models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new SakipTujuanPSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
        $dataProvider->pagination = false;

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single SakipTujuanP model.
     * @param int $reftujuan_p_id Reftujuan ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($reftujuan_p_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($reftujuan_p_id),
        ]);
    }

    /**
     * Creates a new SakipTujuanP model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new SakipTujuanP();

        if (Yii::$app->request->isAjax) {
            if ($model->load(Yii::$app->request->post())) {
                if ($model->save()) {
                    Yii::$app->session->setFlash('success', 'Berhasil Tambah Data');
                    return $this->asJson(['success' => true, 'redirect' => \yii\helpers\Url::to(['view', 'reftujuan_p_id' => $model->reftujuan_p_id])]);
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
     * Updates an existing SakipTujuanP model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $reftujuan_p_id Reftujuan ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($reftujuan_p_id)
    {
        $model = $this->findModel($reftujuan_p_id);

        if (Yii::$app->request->isAjax) {
            if ($model->load(Yii::$app->request->post())) {
                if ($model->save()) {
                    Yii::$app->session->setFlash('success', 'Berhasil Memperbarui Data');
                    return $this->asJson(['success' => true, 'redirect' => \yii\helpers\Url::to(['view', 'reftujuan_p_id' => $model->reftujuan_p_id])]);
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
     * Deletes an existing SakipTujuanP model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $reftujuan_p_id Reftujuan ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($reftujuan_p_id)
    {
        $this->findModel($reftujuan_p_id)->delete();

        return $this->redirect(['index']);
    }

    public function actionDuplicate($reftujuan_p_id)
    {
        $model = $this->findModel($reftujuan_p_id);

        // Buat model baru berdasarkan data yang di-duplikat
        $newModel = new SakipTujuanP();
        $newModel->attributes = $model->attributes;
        $newModel->isNewRecord = true; // Tandai sebagai record baru
        $newModel->reftujuan_p_id = null; // Kosongkan ID agar tidak berbenturan

        // Increment refperiode_id
        $newModel->refperiode_id = $model->refperiode_id + 1;

        // Cari refvisi_p_id yang sesuai dengan refperiode_id baru
        $newVisi = $newPeriode = \backend\models\SakipPeriode::findOne($newModel->refperiode_id);
        $newP5Id = $newPeriode ? $newPeriode->refperiode_5tahun_id : null;
        $newVisi = SakipVisiP::find()
            ->where(['refperiode_5tahun_id' => $newP5Id])
            ->one();

        if ($newVisi) {
            $newModel->refvisi_p_id = $newVisi->refvisi_p_id;
        } else {
            // Jika tidak ditemukan visi yang sesuai, berikan pesan error dan redirect ke halaman index
            Yii::$app->session->setFlash('error', 'Visi untuk periode baru tidak ditemukan.');
            return $this->redirect(['index']);
        }

        // Increment refmisi_p_id dengan mencari nilai terbesar kemudian ditambah 1
        $newMisiId = SakipTujuanP::find()
            ->select('MAX(refmisi_p_id) as refmisi_p_id')
            ->scalar();

        $newModel->refmisi_p_id = $newMisiId + 1;

        // Simpan model baru
        if ($newModel->save()) {
            Yii::$app->session->setFlash('success', 'Data berhasil diduplikasi.');
            return $this->redirect(['view', 'reftujuan_p_id' => $newModel->reftujuan_p_id]); // Redirect ke halaman view dari data yang baru
        } else {
            Yii::$app->session->setFlash('error', 'Data gagal diduplikasi.');
            return $this->redirect(['index']); // Redirect ke halaman index jika gagal
        }
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


    /**
     * Finds the SakipTujuanP model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $reftujuan_p_id Reftujuan ID
     * @return SakipTujuanP the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($reftujuan_p_id)
    {
        if (($model = SakipTujuanP::findOne(['reftujuan_p_id' => $reftujuan_p_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
