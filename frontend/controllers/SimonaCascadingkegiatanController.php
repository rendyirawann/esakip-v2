<?php

namespace frontend\controllers;

use Yii;
use frontend\models\SimonaCascadingkegiatan;
use frontend\models\search\SimonaCascadingkegiatanSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
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
 * SimonaCascadingkegiatanController implements the CRUD actions for SimonaCascadingkegiatan model.
 */
class SimonaCascadingkegiatanController extends Controller
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
     * Lists all SimonaCascadingkegiatan models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new SimonaCascadingkegiatanSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
        $dataProvider->pagination = false;

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single SimonaCascadingkegiatan model.
     * @param int $refsimonacascadingkegiatan_id Refsimonacascadingkegiatan ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($refsimonacascadingkegiatan_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($refsimonacascadingkegiatan_id),
        ]);
    }

    /**
     * Creates a new SimonaCascadingkegiatan model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate($refcascadingkegiatan_id = null, $refcascadingprogram_id = null, $refperiode_id = null, $refsasaranrenstra_id = null, $refindikatorsasaranrenstra_id = null, $refprogram_id = null, $refkegiatan_id = null, $uraian_sasarankegiatan = null, $uraian_indikatorkegiatan = null, $kegiatan_target = null, $kegiatan_satuan = null)
    {
        $model = new SimonaCascadingkegiatan();

        $this->layout = 'main-simona';

        // Set the refcascadingkegiatan_id and refindikatorcascadingprogram_id from the GET parameters
        if ($refcascadingkegiatan_id) {
            $model->refcascadingkegiatan_id = $refcascadingkegiatan_id;
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

        if ($uraian_sasarankegiatan) {
            $model->uraian_sasarankegiatan = $uraian_sasarankegiatan;
        }
        // Set the uraian_indikatorkegiatan and kegiatan_target from the GET parameters
        if ($uraian_indikatorkegiatan) {
            $model->uraian_indikatorkegiatan = $uraian_indikatorkegiatan;
        }

        if ($kegiatan_target) {
            $model->kegiatan_target = $kegiatan_target;
        }

        // Set the kegiatan_satuan from the GET parameters
        if ($kegiatan_satuan) {
            $model->kegiatan_satuan = $kegiatan_satuan;
        }

        if (Yii::$app->request->isAjax) {
            if ($model->load(Yii::$app->request->post())) {
                if ($model->save()) {
                    Yii::$app->session->setFlash('success', 'Berhasil Tambah Data');
                    return $this->asJson(['success' => true, 'redirect' => \yii\helpers\Url::to([
                        'simona-perencanaan/view-kegiatan',
                        'refcascadingprogram_id' => $model->refcascadingprogram_id
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
     * Updates an existing SimonaCascadingkegiatan model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $refsimonacascadingkegiatan_id Refsimonacascadingkegiatan ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($refsimonacascadingkegiatan_id,  $refcascadingprogram_id = null)
    {
        $model = $this->findModel($refsimonacascadingkegiatan_id);

        if (Yii::$app->request->isAjax) {
            if ($model->load(Yii::$app->request->post())) {
                if ($model->save()) {
                    Yii::$app->session->setFlash('success', 'Berhasil Memperbarui Data');
                    return $this->asJson(['success' => true, 'redirect' => \yii\helpers\Url::to([
                        'simona-perencanaan/view-kegiatan',
                        'refcascadingprogram_id' => $model->refcascadingprogram_id
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
     * Deletes an existing SimonaCascadingkegiatan model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $refsimonacascadingkegiatan_id Refsimonacascadingkegiatan ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($refsimonacascadingkegiatan_id)
    {
        $this->findModel($refsimonacascadingkegiatan_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the SimonaCascadingkegiatan model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $refsimonacascadingkegiatan_id Refsimonacascadingkegiatan ID
     * @return SimonaCascadingkegiatan the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($refsimonacascadingkegiatan_id)
    {
        if (($model = SimonaCascadingkegiatan::findOne(['refsimonacascadingkegiatan_id' => $refsimonacascadingkegiatan_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
