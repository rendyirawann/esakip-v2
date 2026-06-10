<?php

namespace frontend\controllers;

use Yii;
use frontend\models\SakipPenjabatskpdCascadingprogram;
use frontend\models\SakipPenjabatSkpd;
use frontend\models\SakipIndikatorcascadingprogram;
use frontend\models\search\SakipPenjabatskpdCascadingprogramSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
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
 * SakipPenjabatskpdCascadingprogramController implements the CRUD actions for SakipPenjabatskpdCascadingprogram model.
 */
class SakipPenjabatskpdCascadingprogramController extends Controller
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
     * Lists all SakipPenjabatskpdCascadingprogram models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new SakipPenjabatskpdCascadingprogramSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
        $dataProvider->pagination = false;

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single SakipPenjabatskpdCascadingprogram model.
     * @param int $refpenjabatcascadingprogram_id Refpenjabatcascadingprogram ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($refpenjabatcascadingprogram_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($refpenjabatcascadingprogram_id),
        ]);
    }

    /**
     * Creates a new SakipPenjabatskpdCascadingprogram model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate($refcascadingprogram_id = null, $refindikatorprogram_id = null, $refperiode_id = null, $refsasaranrenstra_id = null, $refindikatorsasaranrenstra_id = null, $refbidang_id = null, $refprogram_id = null, $uraian_sasaranprogram = null, $uraian_indikatorprogram = null, $program_target = null, $program_satuan = null)
    {
        $model = new SakipPenjabatskpdCascadingprogram();

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

        if ($refbidang_id) {
            $model->refbidang_id = $refbidang_id;
        }

        // Set the refprogram_id and uraian_sasaranprogram from the GET parameters
        if ($refprogram_id) {
            $model->refprogram_id = $refprogram_id;
        }

        if ($uraian_sasaranprogram) {
            $model->uraian_sasaranprogram = $uraian_sasaranprogram;
        }
        // Set the uraian_indikatorprogram and program_target from the GET parameters
        if ($uraian_indikatorprogram) {
            $model->uraian_indikatorprogram = $uraian_indikatorprogram;
        }

        if ($program_target) {
            $model->program_target = $program_target;
        }

        // Set the program_satuan from the GET parameters
        if ($program_satuan) {
            $model->program_satuan = $program_satuan;
        }

        if (Yii::$app->request->isAjax) {
            if ($model->load(Yii::$app->request->post())) {
                if ($model->save()) {
                    Yii::$app->session->setFlash('success', 'Berhasil Tambah Data');
                    return $this->asJson(['success' => true, 'redirect' => \yii\helpers\Url::to([
                        'sakip-cascadingprogram/index',
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

    public function actionFetchIndikator($refcascadingprogram_id)
    {
        // Query the `sakip_indikatorcascadingprogram` table to fetch `refindikatorprogram_id`
        $indikator = SakipIndikatorcascadingprogram::find()
            ->where(['refcascadingprogram_id' => $refcascadingprogram_id])
            ->one();  // Assuming the refcascadingprogram_id is unique for this query

        // Return the fetched refindikatorprogram_id as JSON response
        if ($indikator) {
            return $this->asJson([
                'refindikatorprogram_id' => $indikator->refindikatorprogram_id
            ]);
        }

        // In case no data is found, return an empty value
        return $this->asJson([
            'refindikatorprogram_id' => null
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





    public function actionFetchPenjabatskpd($refperiode_id, $refcascadingprogram_id)
    {
        $currentUserSkpdId = Yii::$app->user->identity->refskpd_id;

        // Fetch Penjabat SKPD that are already associated with refcascadingprogram_id
        $existingPenjabatIds = SakipPenjabatskpdCascadingprogram::find()
            ->select('refpenjabatskpd_id')
            ->where(['refcascadingprogram_id' => $refcascadingprogram_id])
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
     * Updates an existing SakipPenjabatskpdCascadingprogram model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $refpenjabatcascadingprogram_id Refpenjabatcascadingprogram ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */

    public function actionUpdate($refpenjabatcascadingprogram_id, $refperiode_id = null)
    {
        $model = $this->findModel($refpenjabatcascadingprogram_id);

        // Ambil refskpd_id dari user saat ini
        $user = Yii::$app->user->identity;
        $refskpd_id = $user->refskpd_id;

        // Ambil data dari sakip_periode
        $periodeList = SakipPeriode::find()->all(); // Query to get all periods

        if (Yii::$app->request->isAjax) {
            if ($model->load(Yii::$app->request->post())) {
                if ($model->save()) {
                    Yii::$app->session->setFlash('success', 'Berhasil Memperbarui Data');
                    return $this->asJson(['success' => true, 'redirect' => \yii\helpers\Url::to([
                        'sakip-cascadingprogram/index',
                        'refperiode_id' => $model->refperiode_id
                    ])]);
                } else {
                    // Tambahkan log error
                    Yii::error("Error saving data: " . json_encode($model->getErrors()));
                    return $this->asJson(['success' => false, 'errors' => $model->getErrors()]);
                }
            }

            return $this->renderAjax('_formupdate', [
                'model' => $model,
                'refskpd_id' => $refskpd_id,
                'periodeList' => $periodeList,
            ]);
        }

        return $this->render('update', [
            'model' => $model,
            'refskpd_id' => $refskpd_id,
            'periodeList' => $periodeList,
        ]);
    }

    /**
     * Deletes an existing SakipPenjabatskpdCascadingprogram model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $refpenjabatcascadingprogram_id Refpenjabatcascadingprogram ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($refpenjabatcascadingprogram_id, $refperiode_id = null)
    {
        try {
            // Temukan model berdasarkan ID
            $model = $this->findModel($refpenjabatcascadingprogram_id);

            // Hapus model
            if ($model->delete() !== false) {
                // Jika berhasil menghapus
                Yii::$app->session->setFlash('success', 'Berhasil menghapus data.');
            } else {
                // Jika gagal menghapus karena alasan tertentu
                Yii::$app->session->setFlash('error', 'Gagal menghapus data.');
            }
        } catch (\Throwable $e) {
            // Jika terjadi exception (misalnya model tidak ditemukan atau constraint error)
            Yii::$app->session->setFlash('error', 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage());
        }

        // Redirect ke sakip-cascadingprogram/index dengan parameter refperiode_id
        return $this->redirect(['sakip-cascadingprogram/index', 'refperiode_id' => $refperiode_id]);
    }



    /**
     * Finds the SakipPenjabatskpdCascadingprogram model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $refpenjabatcascadingprogram_id Refpenjabatcascadingprogram ID
     * @return SakipPenjabatskpdCascadingprogram the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($refpenjabatcascadingprogram_id)
    {
        if (($model = SakipPenjabatskpdCascadingprogram::findOne(['refpenjabatcascadingprogram_id' => $refpenjabatcascadingprogram_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
