<?php

namespace frontend\controllers;

use Yii;
use frontend\models\SakipTujuanrenstra;
use frontend\models\search\SakipTujuanrenstraSearch;
use frontend\models\SakipSasaranrenstra;
use frontend\models\SakipSkpd;
use frontend\models\SakipMisi;
use frontend\models\SakipPeriode;
use frontend\models\SakipSasaran;
use frontend\models\SakipTujuan;
use frontend\models\search\SakipSasaranrenstraSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
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
 * SakipTujuanrenstraController implements the CRUD actions for SakipTujuanrenstra model.
 */
class SakipTujuanrenstraController extends Controller
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
     * Lists all SakipTujuanrenstra models.
     *
     * @return string
     */
    public function actionIndex($refperiode_id = null)
    {
        $searchModel = new SakipTujuanrenstraSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
        $dataProvider->pagination = false;

        // Ambil refskpd_id dari user saat ini
        $user = Yii::$app->user->identity;
        $refskpd_id = $user->refskpd_id;

        // Ambil nama_skpd berdasarkan refskpd_id
        $nama_skpd = SakipSkpd::find()->select('nama_skpd')->where(['refskpd_id' => $refskpd_id])->scalar();

        // Ambil semua periode
        $periodeList = SakipPeriode::find()->all();

        // Set default period to this year if not provided
        if ($refperiode_id === null) {
            $currentYear = date('Y');
            $defaultPeriod = SakipPeriode::find()->where(['periode' => $currentYear])->one();
            $refperiode_id = $defaultPeriod ? $defaultPeriod->refperiode_id : null;
        }

        $query = SakipSasaranRenstra::find()
            ->select(['refsasaran_id', 'refskpd_id', 'refperiode_id', 'refmisi_id', 'reftujuan_id', 'refsasaranrenstra_id'])
            ->where(['refskpd_id' => $refskpd_id])
            ->andWhere(['refperiode_id' => $refperiode_id])
            ->groupBy(['refsasaran_id']) // Grouping by refsasaran_id
            ->orderBy(['refmisi_id' => SORT_ASC, 'reftujuan_id' => SORT_ASC]);

        $data = $query->all();

        // Add a flag to check if data is empty
        $dataEmpty = empty($data);

        // Retrieve the periode based on refperiode_id
        $selectedPeriod = SakipPeriode::find()->where(['refperiode_id' => $refperiode_id])->one();
        $selectedPeriodValue = $selectedPeriod ? $selectedPeriod->periode : null; // Get the periode value

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'nama_skpd' => $nama_skpd,
            'periodeList' => $periodeList,  // Kirim data periode ke view
            'selectedPeriodId' => $refperiode_id, // Add selected period id
            'data' => $data, // Pass sorted data to the view
            'dataEmpty' => $dataEmpty,
            'selectedPeriodValue' => $selectedPeriodValue, // Include selected period value
        ]);
    }



    public function actionIndexDev($refperiode_id = null, $refskpd_id = null)
    {
        $searchModel = new SakipTujuanrenstraSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
        $dataProvider->pagination = false;

        // Ambil refskpd_id dari user saat ini jika tidak ada di request
        if ($refskpd_id === null) {
            $user = Yii::$app->user->identity;
            $refskpd_id = $user->refskpd_id;
        }

        // Ambil nama_skpd berdasarkan refskpd_id
        $nama_skpd = SakipSkpd::find()->select('nama_skpd')->where(['refskpd_id' => $refskpd_id])->scalar();

        // Ambil semua periode
        $periodeList = SakipPeriode::find()->all();

        // Set default period to this year if not provided
        if ($refperiode_id === null) {
            $currentYear = date('Y');
            $defaultPeriod = SakipPeriode::find()->where(['periode' => $currentYear])->one();
            $refperiode_id = $defaultPeriod ? $defaultPeriod->refperiode_id : null;
        }

        $query = SakipSasaranRenstra::find()
            ->select(['refsasaran_id', 'refskpd_id', 'refperiode_id', 'refmisi_id', 'reftujuan_id', 'refsasaranrenstra_id'])
            ->where(['refskpd_id' => $refskpd_id])
            ->andWhere(['refperiode_id' => $refperiode_id])
            ->groupBy(['refsasaran_id']) // Grouping by refsasaran_id
            ->orderBy(['refmisi_id' => SORT_ASC, 'reftujuan_id' => SORT_ASC]);

        $data = $query->all();

        // Add a flag to check if data is empty
        $dataEmpty = empty($data);

        // Ambil daftar SKPD untuk dropdown
        $skpdList = ArrayHelper::map(SakipSkpd::find()->all(), 'refskpd_id', 'nama_skpd');

        // Retrieve the periode based on refperiode_id
        $selectedPeriod = SakipPeriode::find()->where(['refperiode_id' => $refperiode_id])->one();
        $selectedPeriodValue = $selectedPeriod ? $selectedPeriod->periode : null; // Get the periode value

        return $this->render('index-dev', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'nama_skpd' => $nama_skpd,
            'periodeList' => $periodeList,  // Kirim data periode ke view
            'selectedPeriodId' => $refperiode_id, // Add selected period id
            'data' => $data, // Pass sorted data to the view
            'dataEmpty' => $dataEmpty,
            'selectedPeriodValue' => $selectedPeriodValue, // Include selected period value
            'selectedSkpdId' => $refskpd_id,
            'skpdList' => $skpdList,
        ]);
    }




    /**
     * Displays a single SakipTujuanrenstra model.
     * @param int $reftujuanrenstra_id Reftujuanrenstra ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($reftujuanrenstra_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($reftujuanrenstra_id),
        ]);
    }

    /**
     * Creates a new SakipTujuanrenstra model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate($refsasaranrenstra_id = null, $refperiode_id = null)
    {
        $model = new SakipTujuanrenstra();

        // Ambil refskpd_id dari user saat ini
        $user = Yii::$app->user->identity;
        $refskpd_id = $user->refskpd_id;

        // Set nilai refsasaranrenstra_id, reftujuanrenstra_id, dan refperiode_id pada model jika diberikan
        if ($refsasaranrenstra_id) {
            $model->refsasaranrenstra_id = $refsasaranrenstra_id;

            // Cari data yang terkait dengan refsasaranrenstra_id
            $sasaranRenstra = SakipSasaranrenstra::findOne($refsasaranrenstra_id);

            if ($sasaranRenstra) {
                $model->refsasaran_id = $sasaranRenstra->refsasaran_id;
                $model->refmisi_id = $sasaranRenstra->misi->refmisi_id;
                $model->reftujuan_id = $sasaranRenstra->tujuan->reftujuan_id;
            }
        }

        if ($refperiode_id) {
            $model->refperiode_id = $refperiode_id;
        }
        // Jika ada request Ajax
        if (Yii::$app->request->isAjax) {
            if ($model->load(Yii::$app->request->post())) {
                // Set refskpd_id from user identity
                $model->refskpd_id = $refskpd_id;
                $model->user_create = $user->username; // Mengambil username dari user saat ini
                $model->date_create = date('Y-m-d'); // Mengambil tanggal saat ini

                // Set user_edit, date_edit, user_delete, dan date_delete menjadi null
                $model->user_edit = null;
                $model->date_edit = null;
                $model->user_delete = null;
                $model->date_delete = null;

                if ($model->save()) {
                    Yii::$app->session->setFlash('success', 'Berhasil Tambah Data');
                    return $this->asJson(['success' => true, 'redirect' => \yii\helpers\Url::to(['index', 'refperiode_id' => $refperiode_id])]);
                } else {
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




    public function actionGetRefData($id)
    {
        $sasaran = SakipSasaranRenstra::findOne($id);
        if ($sasaran) {
            // Fetch the related Misi and Tujuan models
            $misi = $sasaran->getMisi()->one(); // Adjust this if the relationship name is different
            $tujuan = $sasaran->getTujuan()->one(); // Adjust this if the relationship name is different

            return $this->asJson([
                'success' => true,
                'refmisi_id' => $sasaran->refmisi_id,
                'reftujuan_id' => $sasaran->reftujuan_id,
                'refperiode_id' => $sasaran->refperiode_id,
                'periode' => $sasaran->refPeriode ? $sasaran->refPeriode->periode : null,
                'uraian_misi' => $misi ? $misi->uraian_misi : null, // Fetch uraian_misi
                'uraian_tujuan' => $tujuan ? $tujuan->uraian_tujuan : null, // Fetch uraian_tujuan
            ]);
        }
        return $this->asJson(['success' => false]);
    }




    /**
     * Updates an existing SakipTujuanrenstra model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $reftujuanrenstra_id Reftujuanrenstra ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($reftujuanrenstra_id, $refperiode_id = null)
    {
        $model = $this->findModel($reftujuanrenstra_id);

        // Ambil refskpd_id dari user saat ini
        $user = Yii::$app->user->identity;
        $refskpd_id = $user->refskpd_id;

        // Ambil data refsasaran dari sakip_sasaranrenstra
        $sasaranList = SakipSasaranRenstra::find()
            ->where(['refskpd_id' => $refskpd_id])
            ->all();

        if (Yii::$app->request->isAjax) {
            if ($model->load(Yii::$app->request->post())) {
                if ($model->save()) {
                    Yii::$app->session->setFlash('success', 'Berhasil Memperbarui Data');
                    return $this->asJson(['success' => true, 'redirect' => \yii\helpers\Url::to(['index', 'refperiode_id' => $model->refperiode_id])]);
                } else {
                    // Tambahkan log error
                    Yii::error("Error saving data: " . json_encode($model->getErrors()));
                    return $this->asJson(['success' => false, 'errors' => $model->getErrors()]);
                }
            }

            return $this->renderAjax('_formupdate', [
                'model' => $model,
                'sasaranList' => $sasaranList, // Pass sasaran list to the view
            ]);
        }

        return $this->render('update', [
            'model' => $model,
            'sasaranList' => $sasaranList, // Pass sasaran list to the view
        ]);
    }

    /**
     * Deletes an existing SakipTujuanrenstra model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $reftujuanrenstra_id Reftujuanrenstra ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($reftujuanrenstra_id)
    {
        $model = $this->findModel($reftujuanrenstra_id);

        // Ambil refperiode_id sebelum menghapus model
        $refperiode_id = $model->refperiode_id;

        $model->delete();

        // Redirect kembali ke index dengan refperiode_id
        return $this->redirect(['index', 'refperiode_id' => $refperiode_id]);
    }


    /**
     * Finds the SakipTujuanrenstra model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $reftujuanrenstra_id Reftujuanrenstra ID
     * @return SakipTujuanrenstra the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($reftujuanrenstra_id)
    {
        if (($model = SakipTujuanrenstra::findOne(['reftujuanrenstra_id' => $reftujuanrenstra_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
