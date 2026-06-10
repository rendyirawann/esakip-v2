<?php

namespace backend\controllers;

use Yii;
use backend\models\SakipPenjabatSkpd;
use backend\models\SakipPeriode;
use backend\models\search\SakipPenjabatSkpdSearch;
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
 * SakipPenjabatSkpdController implements the CRUD actions for SakipPenjabatSkpd model.
 */
class SakipPenjabatSkpdController extends Controller
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
     * Lists all SakipPenjabatSkpd models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new SakipPenjabatSkpdSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
        $dataProvider->pagination = false;

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single SakipPenjabatSkpd model.
     * @param int $refpenjabatskpd_id Refpenjabatskpd ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($refpenjabatskpd_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($refpenjabatskpd_id),
        ]);
    }

    /**
     * Creates a new SakipPenjabatSkpd model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate($refperiode_id = null, $refskpd_id = null)
    {
        $model = new SakipPenjabatSkpd();

        // Ambil data dari sakip_periode
        $periodeList = SakipPeriode::find()->all(); // Query to get all periods

        // Set refskpd_id in the model if provided and fetch nama_skpd
        $namaSkpd = null;
        if ($refskpd_id) {
            $model->refskpd_id = $refskpd_id;
            $namaSkpd = SakipSkpd::find()->select('nama_skpd')->where(['refskpd_id' => $refskpd_id])->scalar();
        }

        if (Yii::$app->request->isAjax) {
            if (Yii::$app->request->isPost) {
                $postData = Yii::$app->request->post();

                // Handle each set of data (for multiple entries)
                $penjabatData = [];
                foreach ($postData['SakipPenjabatSkpd']['nama_penjabat'] as $key => $namaPenjabat) {
                    $penjabatModel = new SakipPenjabatSkpd();
                    $penjabatModel->refskpd_id = $postData['SakipPenjabatSkpd']['refskpd_id'];
                    $penjabatModel->refperiode_id = $postData['SakipPenjabatSkpd']['refperiode_id'];
                    $penjabatModel->nama_penjabat = $namaPenjabat;
                    $penjabatModel->nip_penjabat = $postData['SakipPenjabatSkpd']['nip_penjabat'][$key];
                    $penjabatModel->jabatan_eselon = $postData['SakipPenjabatSkpd']['jabatan_eselon'][$key];
                    $penjabatModel->pangkat_eselon = $postData['SakipPenjabatSkpd']['pangkat_eselon'][$key];
                    $penjabatModel->refeselon_id = $postData['SakipPenjabatSkpd']['refeselon_id'][$key] ?? null;


                    if ($penjabatModel->validate()) {
                        $penjabatData[] = $penjabatModel;
                    }
                }

                // Save each penjabat data
                if ($penjabatData) {
                    foreach ($penjabatData as $penjabat) {
                        $penjabat->save(); // Save the penjabat data
                    }

                    Yii::$app->session->setFlash('success', 'Data saved successfully.');
                    return $this->redirect(['sakip-skpd/view', 'refskpd_id' => $model->refskpd_id, 'refperiode_id' => $postData['SakipPenjabatSkpd']['refperiode_id']]);
                } else {
                    Yii::$app->session->setFlash('error', 'There was an error saving the data.');
                }
            }

            return $this->renderAjax('_form', [
                'model' => $model,
                'periodeList' => $periodeList, // Pass periodeList to form
                'namaSkpd' => $namaSkpd, // Pass namaSkpd to the form
            ]);
        }

        return $this->render('create', [
            'model' => $model,
            'periodeList' => $periodeList, // Pass periodeList to form
            'namaSkpd' => $namaSkpd, // Pass namaSkpd to the form
        ]);
    }

    /**
     * Updates an existing SakipPenjabatSkpd model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $refpenjabatskpd_id Refpenjabatskpd ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($refpenjabatskpd_id, $refperiode_id = null)
    {
        $model = $this->findModel($refpenjabatskpd_id);

        if (Yii::$app->request->isAjax) {
            if ($model->load(Yii::$app->request->post())) {
                if ($model->save()) {
                    Yii::$app->session->setFlash('success', 'Berhasil Memperbarui Data');
                    // Update redirect URL to include refskpd_id and refperiode_id
                    return $this->asJson([
                        'success' => true,
                        'redirect' => \yii\helpers\Url::to([
                            'sakip-skpd/view',
                            'refskpd_id' => $model->refskpd_id,
                            'refperiode_id' => $model->refperiode_id
                        ])
                    ]);
                } else {
                    // Log error for debugging purposes
                    Yii::error("Error saving data: " . json_encode($model->getErrors()));
                    return $this->asJson(['success' => false, 'errors' => $model->getErrors()]);
                }
            }

            return $this->renderAjax('_formupdate', [
                'model' => $model,
            ]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }


    /**
     * Deletes an existing SakipPenjabatSkpd model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $refpenjabatskpd_id Refpenjabatskpd ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($refpenjabatskpd_id)
    {
        $this->findModel($refpenjabatskpd_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the SakipPenjabatSkpd model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $refpenjabatskpd_id Refpenjabatskpd ID
     * @return SakipPenjabatSkpd the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($refpenjabatskpd_id)
    {
        if (($model = SakipPenjabatSkpd::findOne(['refpenjabatskpd_id' => $refpenjabatskpd_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
