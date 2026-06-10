<?php

namespace frontend\controllers;

use Yii;
use frontend\models\SimonaCascadingsubkegiatan;
use frontend\models\search\SimonaCascadingsubkegiatanSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use frontend\models\SimonaCascadingkegiatan;
use frontend\models\search\SimonaCascadingkegiatanSearch;
use frontend\models\SakipCascadingprogram;
use frontend\models\search\SakipCascadingprogramSearch;
use frontend\models\SakipCascadingkegiatan;
use frontend\models\search\SakipCascadingkegiatanSearch;
use frontend\models\SakipKebijakan;
use frontend\models\search\SakipKebijakanSearch;
use frontend\models\SakipStrategi;
use frontend\models\search\SakipStrategiSearch;
use frontend\models\SakipIndikatortujuanrenstra;
use frontend\models\SakipIndikatorsasaranrenstra;
use frontend\models\SakipIndikatorcascadingprogram;
use frontend\models\SakipIndikatorcascadingprogramTriwulan;
use frontend\models\SakipSkpd;
use frontend\models\SakipPeriode;
use frontend\models\SakipSasaran;
use frontend\models\SakipMisi;
use frontend\models\SakipBidang;
use frontend\models\SakipProgram;
use frontend\models\SakipTujuan;
use frontend\models\SakipTujuanRenstra;
use frontend\models\SakipSasaranRenstra;
use frontend\models\search\SakipIndikatortujuanrenstraSearch;
use frontend\models\search\SakipSasaranrenstraSearch;
use yii\web\UploadedFile;
use yii\helpers\Url;
use yii\web\Response;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\db\Expression;
use yii\helpers\Json;
use yii\helpers\ArrayHelper;
use yii\filters\AccessControl;

/**
 * SimonaCascadingsubkegiatanController implements the CRUD actions for SimonaCascadingsubkegiatan model.
 */
class SimonaCascadingsubkegiatanController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'access' => [
                    'class' => AccessControl::class,
                    'rules' => [
                        [
                            'allow' => true,
                            'roles' => ['@'], // Hanya untuk pengguna yang sudah login
                        ],
                    ],
                    'denyCallback' => function ($rule, $action) {
                        return Yii::$app->response->redirect(['site/login']);
                    },
                ],
                'verbs' => [
                    'class' => VerbFilter::class,
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all SimonaCascadingsubkegiatan models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new SimonaCascadingsubkegiatanSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
        $dataProvider->pagination = false;

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single SimonaCascadingsubkegiatan model.
     * @param int $refsimonacascadingsubkegiatan_id Refsimonacascadingsubkegiatan ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($refsimonacascadingsubkegiatan_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($refsimonacascadingsubkegiatan_id),
        ]);
    }

    /**
     * Creates a new SimonaCascadingsubkegiatan model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate($refcascadingkegiatan_id = null, $refcascadingprogram_id = null, $refcascadingsubkegiatan_id = null, $refperiode_id = null, $refsasaranrenstra_id = null, $refindikatorsasaranrenstra_id = null, $refprogram_id = null, $refkegiatan_id = null, $refsubkegiatan_id = null, $uraian_sasaransubkegiatan = null, $uraian_indikatorsubkegiatan = null, $subkegiatan_target = null, $subkegiatan_satuan = null)
    {
        $model = new SimonaCascadingsubkegiatan();

        $this->layout = 'main-simona';

        // Set the refcascadingkegiatan_id and refindikatorcascadingprogram_id from the GET parameters
        if ($refcascadingkegiatan_id) {
            $model->refcascadingkegiatan_id = $refcascadingkegiatan_id;
        }

        if ($refcascadingsubkegiatan_id) {
            $model->refcascadingsubkegiatan_id = $refcascadingsubkegiatan_id;
        }

        // Set the refcascadingprogram_id and refindikatorcascadingprogram_id from the GET parameters
        if ($refcascadingprogram_id) {
            $model->refcascadingprogram_id = $refcascadingprogram_id;
        }

        // Set the refperiode_id and refsasaranrenstra_id from the GET parameters
        if ($refperiode_id) {
            $model->refperiode_id = $refperiode_id;
        }

        if ($refsasaranrenstra_id) {
            $model->refsasaranrenstra_id = $refsasaranrenstra_id;
        }

        // Set the refindikatorsasaranrenstra_id and refbidang_id from the GET parameters
        if ($refindikatorsasaranrenstra_id) {
            $model->refindikatorsasaranrenstra_id = $refindikatorsasaranrenstra_id;
        }

        // Set the refprogram_id and uraian_sasaranprogram from the GET parameters
        if ($refprogram_id) {
            $model->refprogram_id = $refprogram_id;
        }

        if ($refkegiatan_id) {
            $model->refkegiatan_id = $refkegiatan_id;
        }

        if ($refsubkegiatan_id) {
            $model->refsubkegiatan_id = $refsubkegiatan_id;
        }

        if ($uraian_sasaransubkegiatan) {
            $model->uraian_sasaransubkegiatan = $uraian_sasaransubkegiatan;
        }
        // Set the uraian_indikatorsubkegiatan and kegiatan_target from the GET parameters
        if ($uraian_indikatorsubkegiatan) {
            $model->uraian_indikatorsubkegiatan = $uraian_indikatorsubkegiatan;
        }

        if ($subkegiatan_target) {
            $model->subkegiatan_target = $subkegiatan_target;
        }

        // Set the kegiatan_satuan from the GET parameters
        if ($subkegiatan_satuan) {
            $model->subkegiatan_satuan = $subkegiatan_satuan;
        }

        if (Yii::$app->request->isAjax) {
            if ($model->load(Yii::$app->request->post())) {
                if ($model->save()) {
                    Yii::$app->session->setFlash('success', 'Berhasil Tambah Data');
                    return $this->asJson(['success' => true, 'redirect' => \yii\helpers\Url::to([
                        'simona-perencanaan-kegiatan/view-subkegiatan',
                        'refcascadingkegiatan_id' => $model->refcascadingkegiatan_id
                    ])]);
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
     * Updates an existing SimonaCascadingsubkegiatan model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $refsimonacascadingsubkegiatan_id Refsimonacascadingsubkegiatan ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($refsimonacascadingsubkegiatan_id,  $refcascadingkegiatan_id = null)
    {
        $model = $this->findModel($refsimonacascadingsubkegiatan_id);

        if (Yii::$app->request->isAjax) {
            if ($model->load(Yii::$app->request->post())) {
                if ($model->save()) {
                    Yii::$app->session->setFlash('success', 'Berhasil Memperbarui Data');
                    return $this->asJson(['success' => true, 'redirect' => \yii\helpers\Url::to([
                        'simona-perencanaan-kegiatan/view-subkegiatan',
                        'refcascadingkegiatan_id' => $model->refcascadingkegiatan_id
                    ])]);
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
     * Deletes an existing SimonaCascadingsubkegiatan model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $refsimonacascadingsubkegiatan_id Refsimonacascadingsubkegiatan ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($refsimonacascadingsubkegiatan_id)
    {
        $this->findModel($refsimonacascadingsubkegiatan_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the SimonaCascadingsubkegiatan model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $refsimonacascadingsubkegiatan_id Refsimonacascadingsubkegiatan ID
     * @return SimonaCascadingsubkegiatan the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($refsimonacascadingsubkegiatan_id)
    {
        if (($model = SimonaCascadingsubkegiatan::findOne(['refsimonacascadingsubkegiatan_id' => $refsimonacascadingsubkegiatan_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
