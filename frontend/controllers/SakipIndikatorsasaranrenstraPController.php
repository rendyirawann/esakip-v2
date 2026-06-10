<?php

namespace frontend\controllers;

use yii\filters\VerbFilter;
use Yii;
use frontend\models\SakipIndikatorsasaranrenstraP;
use frontend\models\search\SakipIndikatorsasaranrenstraPSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use frontend\models\SakipSasaranrenstraP;
use frontend\models\SakipSkpd;
use frontend\models\SakipPeriode;
use frontend\models\SakipSasaranP;
use frontend\models\SakipTujuanP;
use frontend\models\SakipKoordinasiP;
use frontend\models\SakipTujuanrenstraP;
use frontend\models\SakipIndikatorsasaranrenstraPTriwulan;
use yii\web\UploadedFile;
use yii\helpers\Url;
use yii\web\Response;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\db\Expression;
use yii\helpers\Json;
use yii\helpers\ArrayHelper;
use yii\filters\AccessControl;
use yii\web\ForbiddenHttpException;

/**
 * SakipIndikatorsasaranrenstraPController implements the CRUD actions for SakipIndikatorsasaranrenstraP model.
 */
class SakipIndikatorsasaranrenstraPController extends Controller
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
     * Lists all SakipIndikatorsasaranrenstraP models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new SakipIndikatorsasaranrenstraPSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single SakipIndikatorsasaranrenstraP model.
     * @param int $refindikatorsasaranrenstra_p_id Refindikatorsasaranrenstra P ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($refindikatorsasaranrenstra_p_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($refindikatorsasaranrenstra_p_id),
        ]);
    }

    /**
     * Creates a new SakipIndikatorsasaranrenstraP model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate($refperiode_id = null, $refsasaranrenstra_p_id = null)
    {
        $model = new SakipIndikatorsasaranrenstraP();

        // Set the refperiode_id and refsasaranrenstra_p_id from the GET parameters
        if ($refperiode_id) {
            $model->refperiode_id = $refperiode_id;
        }

        if ($refsasaranrenstra_p_id) {
            $model->refsasaranrenstra_p_id = $refsasaranrenstra_p_id;
        }
        // Ambil refskpd_id dari user saat ini
        $user = Yii::$app->user->identity;
        $refskpd_id = $user->refskpd_id;

        // Ambil data dari sakip_periode
        $periodeList = SakipPeriode::find()->all(); // Query to get all periods

        if (Yii::$app->request->isAjax) {
            if ($model->load(Yii::$app->request->post())) {

                // Konversi koma ke titik pada indikatorsasaranrenstra_p_target dan target_rkt
                $model->indikatorsasaranrenstra_p_target = str_replace(',', '.', $model->indikatorsasaranrenstra_p_target);
                $model->target_rkt = str_replace(',', '.', $model->indikatorsasaranrenstra_p_target);


                if ($model->save()) {
                    // Loop through each triwulan (1 to 4)
                    for ($i = 1; $i <= 4; $i++) {
                        $triwulanModel = new SakipIndikatorsasaranrenstraPTriwulan(); // New model for each triwulan

                        // Set values for the triwulan model from the main model
                        $triwulanModel->refindikatorsasaranrenstra_p_id = $model->refindikatorsasaranrenstra_p_id;
                        $triwulanModel->refsasaranrenstra_p_id = $model->refsasaranrenstra_p_id;
                        $triwulanModel->refskpd_id = $model->refskpd_id;
                        $triwulanModel->refperiode_id = $model->refperiode_id;
                        // Konversi koma ke titik pada triwulan_target_rkt
                        $triwulanModel->triwulan_target_rkt = str_replace(',', '.', $model->indikatorsasaranrenstra_p_target);
                        $triwulanModel->reftriwulan_id = $i; // Set triwulan_id to the current loop index

                        // Save each triwulan model
                        if (!$triwulanModel->save()) {
                            // Log error for triwulan
                            Yii::error("Error saving triwulan data for triwulan $i: " . json_encode($triwulanModel->getErrors()));
                            return $this->asJson(['success' => false, 'errors' => array_merge($model->getErrors(), $triwulanModel->getErrors())]);
                        }
                    }

                    Yii::$app->session->setFlash('success', 'Berhasil Tambah Data');
                    return $this->asJson(['success' => true, 'redirect' => \yii\helpers\Url::to(['sakip-sasaranrenstra/index', 'refperiode_id' => $model->refperiode_id, 'active_tab' => 'perubahan'])]);
                } else {
                    // Log error for the main model
                    Yii::error("Error saving data: " . json_encode($model->getErrors()));
                    return $this->asJson(['success' => false, 'errors' => $model->getErrors()]);
                }
            }

            return $this->renderAjax('_form', [
                'model' => $model,
                'periodeList' => $periodeList,
            ]);
        }

        return $this->render('create', [
            'model' => $model,
            'periodeList' => $periodeList,
        ]);
    }

    /**
     * Updates an existing SakipIndikatorsasaranrenstraP model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $refindikatorsasaranrenstra_p_id Refindikatorsasaranrenstra P ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($refindikatorsasaranrenstra_p_id, $refperiode_id = null)
    {
        $model = $this->findModel($refindikatorsasaranrenstra_p_id);

        if (Yii::$app->request->isAjax) {
            if ($model->load(Yii::$app->request->post())) {

                // Konversi koma ke titik pada indikatorsasaranrenstra_p_target dan target_rkt
                $model->indikatorsasaranrenstra_p_target = str_replace(',', '.', $model->indikatorsasaranrenstra_p_target);
                $model->target_rkt = str_replace(',', '.', $model->indikatorsasaranrenstra_p_target);

                // Mengatur target_rkt sesuai dengan indikatorsasaranrenstra_p_target yang sudah dikonversi
                $model->target_rkt = $model->indikatorsasaranrenstra_p_target;

                if ($model->save()) {
                    // Setelah data utama berhasil disimpan, kita update data triwulan
                    for ($i = 1; $i <= 4; $i++) {
                        // Cari model triwulan yang sesuai
                        $triwulanModel = SakipIndikatorsasaranrenstraPTriwulan::findOne([
                            'refindikatorsasaranrenstra_p_id' => $model->refindikatorsasaranrenstra_p_id,
                            'reftriwulan_id' => $i
                        ]);

                        if ($triwulanModel !== null) {
                            // Set nilai triwulan_target_rkt sesuai dengan indikatorsasaranrenstra_target yang baru
                            // Konversi koma ke titik pada triwulan_target_rkt
                            $triwulanModel->triwulan_target_rkt = str_replace(',', '.', $model->indikatorsasaranrenstra_p_target);


                            // Simpan triwulan model
                            if (!$triwulanModel->save()) {
                                // Jika terjadi kesalahan pada penyimpanan triwulan, log error
                                Yii::error("Error updating triwulan $i: " . json_encode($triwulanModel->getErrors()));
                                return $this->asJson(['success' => false, 'errors' => $triwulanModel->getErrors()]);
                            }
                        }
                    }

                    Yii::$app->session->setFlash('success', 'Berhasil Memperbarui Data');
                    return $this->asJson(['success' => true, 'redirect' => \yii\helpers\Url::to(['sakip-sasaranrenstra/index', 'refperiode_id' => $model->refperiode_id, 'active_tab' => 'perubahan'])]);
                } else {
                    // Log error untuk model utama
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
     * Deletes an existing SakipIndikatorsasaranrenstraP model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $refindikatorsasaranrenstra_p_id Refindikatorsasaranrenstra P ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($refindikatorsasaranrenstra_p_id)
    {
        $this->findModel($refindikatorsasaranrenstra_p_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the SakipIndikatorsasaranrenstraP model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $refindikatorsasaranrenstra_p_id Refindikatorsasaranrenstra P ID
     * @return SakipIndikatorsasaranrenstraP the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($refindikatorsasaranrenstra_p_id)
    {
        if (($model = SakipIndikatorsasaranrenstraP::findOne(['refindikatorsasaranrenstra_p_id' => $refindikatorsasaranrenstra_p_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
