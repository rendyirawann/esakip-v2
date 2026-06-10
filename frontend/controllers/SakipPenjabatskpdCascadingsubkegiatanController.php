<?php

namespace frontend\controllers;

use Yii;
use frontend\models\SakipPenjabatskpdCascadingsubkegiatan;
use frontend\models\search\SakipPenjabatskpdCascadingsubkegiatanSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use frontend\models\SakipPenjabatskpdCascadingkegiatan;
use frontend\models\search\SakipPenjabatskpdCascadingkegiatanSearch;
use frontend\models\SakipPenjabatskpdCascadingprogram;
use frontend\models\SakipPenjabatSkpd;
use frontend\models\SakipIndikatorcascadingprogram;
use frontend\models\SakipIndikatorcascadingkegiatan;
use frontend\models\SakipIndikatorcascadingsubkegiatan;
use frontend\models\search\SakipPenjabatskpdCascadingprogramSearch;
use backend\models\SakipEselon;
use backend\models\search\SakipEselonSearch;
use backend\models\SakipPeriode;
use backend\models\search\SakipPeriodeSearch;
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
 * SakipPenjabatskpdCascadingsubkegiatanController implements the CRUD actions for SakipPenjabatskpdCascadingsubkegiatan model.
 */
class SakipPenjabatskpdCascadingsubkegiatanController extends Controller
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
     * Lists all SakipPenjabatskpdCascadingsubkegiatan models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new SakipPenjabatskpdCascadingsubkegiatanSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
        $dataProvider->pagination = false;

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single SakipPenjabatskpdCascadingsubkegiatan model.
     * @param int $refpenjabatcascadingsubkegiatan_id Refpenjabatcascadingsubkegiatan ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($refpenjabatcascadingsubkegiatan_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($refpenjabatcascadingsubkegiatan_id),
        ]);
    }

    /**
     * Creates a new SakipPenjabatskpdCascadingsubkegiatan model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate($refcascadingsubkegiatan_id = null, $refcascadingkegiatan_id = null, $refcascadingprogram_id = null, $refindikatorsubkegiatan_id = null, $refperiode_id = null, $refsasaranrenstra_id = null, $refindikatorsasaranrenstra_id = null, $refprogram_id = null, $refkegiatan_id = null, $refsubkegiatan_id = null, $uraian_sasaransubkegiatan = null, $uraian_indikatorsubkegiatan = null, $subkegiatan_target = null, $subkegiatan_satuan = null)
    {
        $model = new SakipPenjabatskpdCascadingsubkegiatan();

        // Set the refcascadingsubkegiatan_id and refindikatorcascadingprogram_id from the GET parameters
        if ($refcascadingsubkegiatan_id) {
            $model->refcascadingsubkegiatan_id = $refcascadingsubkegiatan_id;
        }

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

        if ($refsubkegiatan_id) {
            $model->refsubkegiatan_id = $refsubkegiatan_id;
        }

        if ($uraian_sasaransubkegiatan) {
            $model->uraian_sasaransubkegiatan = $uraian_sasaransubkegiatan;
        }
        // Set the uraian_indikatorsubkegiatan and subkegiatan_target from the GET parameters
        if ($uraian_indikatorsubkegiatan) {
            $model->uraian_indikatorsubkegiatan = $uraian_indikatorsubkegiatan;
        }

        if ($subkegiatan_target) {
            $model->subkegiatan_target = $subkegiatan_target;
        }

        // Set the subkegiatan_satuan from the GET parameters
        if ($subkegiatan_satuan) {
            $model->subkegiatan_satuan = $subkegiatan_satuan;
        }

        if (Yii::$app->request->isAjax) {
            if ($model->load(Yii::$app->request->post())) {
                if ($model->save()) {
                    Yii::$app->session->setFlash('success', 'Berhasil Tambah Data');
                    return $this->asJson(['success' => true, 'redirect' => \yii\helpers\Url::to([
                        'sakip-cascadingsubkegiatan/index',
                        'refperiode_id' => $model->refperiode_id
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

    public function actionFetchIndikator($refcascadingsubkegiatan_id)
    {
        // Query the `sakip_indikatorcascadingprogram` table to fetch `refindikatorprogram_id`
        $indikator = SakipIndikatorcascadingsubkegiatan::find()
            ->where(['refcascadingsubkegiatan_id' => $refcascadingsubkegiatan_id])
            ->one();  // Assuming the refcascadingsubkegiatan_id is unique for this query

        // Return the fetched refindikatorsubkegiatan_id as JSON response
        if ($indikator) {
            return $this->asJson([
                'refindikatorsubkegiatan_id' => $indikator->refindikatorsubkegiatan_id
            ]);
        }

        // In case no data is found, return an empty value
        return $this->asJson([
            'refindikatorsubkegiatan_id' => null
        ]);
    }

    public function actionFetchEselon($refpenjabatskpd_id)
    {
        // Find the Penjabat SKPD with the provided ID
        $penjabatSkpd = SakipPenjabatSkpd::findOne($refpenjabatskpd_id);

        // Check if the record exists and has a refeselon_id
        if ($penjabatSkpd && $penjabatSkpd->refeselon_id) {
            // Find the Eselon record using the refeselon_id from sakip_eselon table
            $eselon = SakipEselon::findOne($penjabatSkpd->refeselon_id);

            // Prepare response using the title_eselon from sakip_eselon
            $response = [
                'refeselon_id' => $penjabatSkpd->refeselon_id,
                'title_eselon' => $eselon ? $eselon->title_eselon : null,
            ];
        } else {
            $response = [
                'refeselon_id' => null,
                'title_eselon' => null,
            ];
        }

        return $this->asJson($response);
    }





    public function actionFetchPenjabatskpd($refperiode_id, $refcascadingsubkegiatan_id)
    {
        $currentUserSkpdId = Yii::$app->user->identity->refskpd_id;

        // Fetch Penjabat SKPD that are already associated with refcascadingsubkegiatan_id
        $existingPenjabatIds = SakipPenjabatskpdCascadingsubkegiatan::find()
            ->select('refpenjabatskpd_id')
            ->where(['refcascadingsubkegiatan_id' => $refcascadingsubkegiatan_id])
            ->column();

        // Fetch the available Penjabat SKPD excluding the ones already associated
        $penjabatskpdList = SakipPenjabatSkpd::find()
            ->where(['refperiode_id' => $refperiode_id, 'refskpd_id' => $currentUserSkpdId])
            ->andWhere(['not in', 'refpenjabatskpd_id', $existingPenjabatIds])
            ->orderBy(['refpenjabatskpd_id' => SORT_ASC])
            ->all();

        // Prepare data to be returned as JSON
        $data = ArrayHelper::toArray($penjabatskpdList, [
            'frontend\models\SakipPenjabatSkpd' => [
                'refpenjabatskpd_id',
                'nama_penjabat',
            ],
        ]);

        return $this->asJson($data);
    }

    /**
     * Updates an existing SakipPenjabatskpdCascadingsubkegiatan model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $refpenjabatcascadingsubkegiatan_id Refpenjabatcascadingsubkegiatan ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($refpenjabatcascadingsubkegiatan_id)
    {
        $model = $this->findModel($refpenjabatcascadingsubkegiatan_id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'refpenjabatcascadingsubkegiatan_id' => $model->refpenjabatcascadingsubkegiatan_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing SakipPenjabatskpdCascadingsubkegiatan model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $refpenjabatcascadingsubkegiatan_id Refpenjabatcascadingsubkegiatan ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($refpenjabatcascadingsubkegiatan_id)
    {
        $this->findModel($refpenjabatcascadingsubkegiatan_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the SakipPenjabatskpdCascadingsubkegiatan model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $refpenjabatcascadingsubkegiatan_id Refpenjabatcascadingsubkegiatan ID
     * @return SakipPenjabatskpdCascadingsubkegiatan the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($refpenjabatcascadingsubkegiatan_id)
    {
        if (($model = SakipPenjabatskpdCascadingsubkegiatan::findOne(['refpenjabatcascadingsubkegiatan_id' => $refpenjabatcascadingsubkegiatan_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
