<?php

namespace frontend\controllers;

use Yii;
use frontend\models\SakipLke;
use frontend\models\search\SakipLkeSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use frontend\models\SakipSkpd;
use frontend\models\SakipKoordinasi;
use frontend\models\SakipPeriode;
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
 * SakipLkeController implements the CRUD actions for SakipLke model.
 */
class SakipLkeController extends Controller
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
     * Lists all SakipLke models.
     *
     * @return string
     */
    public function actionIndex($refperiode_id = null)
    {
        $searchModel = new SakipLkeSearch();

        // If a refperiode_id is selected, add it to the query parameters
        if ($refperiode_id !== null) {
            $searchModel->refperiode_id = $refperiode_id;
        }

        $dataProvider = $searchModel->search($this->request->queryParams);
        $dataProvider->pagination = false;

        // Get refskpd_id from the current user
        $user = Yii::$app->user->identity;
        $refskpd_id = $user->refskpd_id;

        // Get the name of the SKPD based on refskpd_id
        $nama_skpd = SakipSkpd::find()->select('nama_skpd')->where(['refskpd_id' => $refskpd_id])->scalar();

        // Fetch all periods
        $periodeList = SakipPeriode::find()->all();

        // Set default period to this year if not provided
        if ($refperiode_id === null) {
            $currentYear = date('Y');
            $defaultPeriod = SakipPeriode::find()->where(['periode' => $currentYear])->one();
            $refperiode_id = $defaultPeriod ? $defaultPeriod->refperiode_id : null;
        }

        // Retrieve the periode based on refperiode_id
        $selectedPeriod = SakipPeriode::find()->where(['refperiode_id' => $refperiode_id])->one();
        $selectedPeriodValue = $selectedPeriod ? $selectedPeriod->periode : null; // Get the periode value

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'nama_skpd' => $nama_skpd,
            'periodeList' => $periodeList,
            'selectedPeriodId' => $refperiode_id,
            'selectedPeriodValue' => $selectedPeriodValue, // Include selected period value
            'user' => $user, // Add user here
        ]);
    }

    // public function actionIndexDev($refperiode_id = null, $refskpd_id = null)
    // {
    //     $searchModel = new SakipLkeSearch();

    //     $dataProvider = $searchModel->search($this->request->queryParams);
    //     $dataProvider->pagination = false;

    //     // Ambil refskpd_id dari user saat ini jika tidak ada di request
    //     if ($refskpd_id === null) {
    //         $user = Yii::$app->user->identity;
    //         $refskpd_id = $user->refskpd_id;
    //     }

    //     // Get the name of the SKPD based on refskpd_id
    //     $nama_skpd = SakipSkpd::find()->select('nama_skpd')->where(['refskpd_id' => $refskpd_id])->scalar();

    //     // Fetch all periods
    //     $periodeList = SakipPeriode::find()->all();

    //     // Set default period to this year if not provided
    //     if ($refperiode_id === null) {
    //         $currentYear = date('Y');
    //         $defaultPeriod = SakipPeriode::find()->where(['periode' => $currentYear])->one();
    //         $refperiode_id = $defaultPeriod ? $defaultPeriod->refperiode_id : null;
    //     }

    //     // Ambil data dari sakip_cascadingprogram sesuai dengan refskpd_id user dan refperiode_id (jika ada)
    //     $query = SakipLke::find()->where(['refskpd_id' => $refskpd_id]);

    //     if ($refperiode_id !== null) {
    //         $query->andWhere(['refperiode_id' => $refperiode_id]);
    //     }

    //     // Execute query and get data
    //     $sakipLkes = $query->all(); // Ambil data cascading program

    //     // Add a flag to check if data is empty
    //     $dataEmpty = empty($sakipLkes);

    //     // Ambil daftar SKPD untuk dropdown
    //     $skpdList = ArrayHelper::map(SakipSkpd::find()->all(), 'refskpd_id', 'nama_skpd');

    //     // Retrieve the periode based on refperiode_id
    //     $selectedPeriod = SakipPeriode::find()->where(['refperiode_id' => $refperiode_id])->one();
    //     $selectedPeriodValue = $selectedPeriod ? $selectedPeriod->periode : null; // Get the periode value

    //     return $this->render('index-dev', [
    //         'searchModel' => $searchModel,
    //         'dataProvider' => $dataProvider,
    //         'nama_skpd' => $nama_skpd,
    //         'periodeList' => $periodeList,
    //         'selectedPeriodId' => $refperiode_id,
    //         'selectedPeriodValue' => $selectedPeriodValue, // Include selected period value
    //         'selectedSkpdId' => $refskpd_id,
    //         'skpdList' => $skpdList,
    //         'dataEmpty' => $dataEmpty, // Pass the data empty flag
    //         'sakipLkes' => $sakipLkes, // Send the queried data
    //     ]);
    // }

    public function actionIndexDev($refperiode_id = null, $refskpd_id = null)
    {
        $searchModel = new SakipLkeSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
        $dataProvider->pagination = false;

        // =========================================================================
        // BLOK LOGIKA BARU UNTUK KEAMANAN SKPD
        // =========================================================================

        $user = Yii::$app->user->identity;
        $assignments = Yii::$app->authManager->getAssignments($user->id);

        $skpdList = [];
        $allowedSkpdIds = []; // Daftar ID SKPD yang diizinkan untuk user ini

        if (isset($assignments['superadmin']) || isset($assignments['admin'])) {
            // --- BLOK UNTUK ADMIN ---
            $allSkpd = SakipSkpd::find()->where(['skpd_isaktif' => 'T'])->orderBy('nama_skpd ASC')->all();
            $skpdList = ArrayHelper::map($allSkpd, 'refskpd_id', 'nama_skpd');
            $allowedSkpdIds = array_keys($skpdList);
        } else {
            // --- BLOK UNTUK NON-ADMIN (KOORDINATOR) ---
            $coordinatedSkpdIds = SakipKoordinasi::find()
                ->select('refskpd_id')
                ->where(['refuser_id' => $user->id])
                ->column();

            $allowedSkpdIds = $coordinatedSkpdIds;

            if (!empty($allowedSkpdIds)) {
                $skpdList = ArrayHelper::map(
                    SakipSkpd::find()->where(['refskpd_id' => $allowedSkpdIds, 'skpd_isaktif' => 'T'])->orderBy('nama_skpd ASC')->all(),
                    'refskpd_id',
                    'nama_skpd'
                );
            }
        }

        // --- Validasi Keamanan ---
        // Cek apakah refskpd_id dari URL diizinkan untuk diakses
        if ($refskpd_id !== null && !in_array($refskpd_id, $allowedSkpdIds)) {
            throw new ForbiddenHttpException('Anda tidak memiliki hak akses untuk melihat data SKPD ini.');
        }

        // Tentukan refskpd_id yang akan digunakan jika tidak ada di URL atau tidak valid
        if ($refskpd_id === null || !in_array($refskpd_id, $allowedSkpdIds)) {
            $refskpd_id = !empty($allowedSkpdIds) ? $allowedSkpdIds[0] : null;
        }

        // =========================================================================
        // AKHIR DARI BLOK LOGIKA BARU
        // Kode di bawah ini sekarang menggunakan $refskpd_id yang sudah aman
        // =========================================================================

        // Get the name of the SKPD based on refskpd_id
        $nama_skpd = $refskpd_id ? SakipSkpd::find()->select('nama_skpd')->where(['refskpd_id' => $refskpd_id])->scalar() : 'Tidak ada SKPD dipilih';

        // Fetch all periods
        $periodeList = SakipPeriode::find()->all();

        // Set default period to this year if not provided
        if ($refperiode_id === null) {
            $currentYear = date('Y');
            $defaultPeriod = SakipPeriode::find()->where(['periode' => $currentYear])->one();
            $refperiode_id = $defaultPeriod ? $defaultPeriod->refperiode_id : null;
        }

        // Logika query utama Anda tidak diubah
        $query = SakipLke::find()->where(['refskpd_id' => $refskpd_id]);

        if ($refperiode_id !== null) {
            $query->andWhere(['refperiode_id' => $refperiode_id]);
        }

        // Execute query and get data
        $sakipLkes = $query->all();

        // Add a flag to check if data is empty
        $dataEmpty = empty($sakipLkes);

        // Retrieve the periode based on refperiode_id
        $selectedPeriod = SakipPeriode::find()->where(['refperiode_id' => $refperiode_id])->one();
        $selectedPeriodValue = $selectedPeriod ? $selectedPeriod->periode : null; // Get the periode value

        return $this->render('index-dev', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'nama_skpd' => $nama_skpd,
            'periodeList' => $periodeList,
            'selectedPeriodId' => $refperiode_id,
            'selectedPeriodValue' => $selectedPeriodValue, // Include selected period value
            'selectedSkpdId' => $refskpd_id,
            'skpdList' => $skpdList,
            'dataEmpty' => $dataEmpty, // Pass the data empty flag
            'sakipLkes' => $sakipLkes, // Send the queried data
        ]);
    }

    /**
     * Displays a single SakipLke model.
     * @param int $reflke_id Reflke ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($reflke_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($reflke_id),
        ]);
    }

    /**
     * Creates a new SakipLke model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate($reflkekomponen_id = null, $reflkesubkomponen_id = null, $refperiode_id = null)
    {
        $model = new SakipLke();

        if ($reflkekomponen_id) {
            $model->reflkekomponen_id = $reflkekomponen_id;
        }

        if ($reflkesubkomponen_id) {
            $model->reflkesubkomponen_id = $reflkesubkomponen_id;
        }
        if ($refperiode_id) {
            $model->refperiode_id = $refperiode_id; // Assuming the model has these attributes
        }

        if (Yii::$app->request->isAjax) {
            if ($model->load(Yii::$app->request->post())) {
                if ($model->save()) {
                    Yii::$app->session->setFlash('success', 'Berhasil Tambah Data');
                    return $this->asJson(['success' => true, 'redirect' => \yii\helpers\Url::to(['sakip-lke/index-dev', 'refperiode_id' => $refperiode_id])]);
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

    public function actionCreateDev($reflkekomponen_id = null, $reflkesubkomponen_id = null, $refperiode_id = null, $refskpd_id = null)
    {
        $model = new SakipLke();

        if ($reflkekomponen_id) {
            $model->reflkekomponen_id = $reflkekomponen_id;
        }

        if ($reflkesubkomponen_id) {
            $model->reflkesubkomponen_id = $reflkesubkomponen_id;
        }
        if ($refperiode_id) {
            $model->refperiode_id = $refperiode_id; // Assuming the model has these attributes
        }

        if ($refskpd_id) {
            $model->refskpd_id = $refskpd_id; // Assuming the model has these attributes
        }

        if (Yii::$app->request->isAjax) {
            if ($model->load(Yii::$app->request->post())) {
                if ($model->save()) {
                    Yii::$app->session->setFlash('success', 'Berhasil Tambah Data');
                    return $this->asJson(['success' => true, 'redirect' => \yii\helpers\Url::to(['sakip-lke/index-dev', 'refperiode_id' => $refperiode_id, 'refskpd_id' => $refskpd_id])]);
                } else {
                    // Tambahkan log error
                    Yii::error("Error saving data: " . json_encode($model->getErrors()));
                    return $this->asJson(['success' => false, 'errors' => $model->getErrors()]);
                }
            }

            return $this->renderAjax('_formdev', [
                'model' => $model,
            ]);
        }

        return $this->render('create-dev', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing SakipLke model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $reflke_id Reflke ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($reflke_id, $refperiode_id = null, $refskpd_id = null)
    {
        $model = $this->findModel($reflke_id);

        if ($refperiode_id) {
            $model->refperiode_id = $refperiode_id; // Assuming the model has these attributes
        }

        if ($refskpd_id) {
            $model->refskpd_id = $refskpd_id; // Assuming the model has these attributes
        }

        if (Yii::$app->request->isAjax) {
            if ($model->load(Yii::$app->request->post())) {
                if ($model->save()) {
                    Yii::$app->session->setFlash('success', 'Berhasil Memperbarui Data');
                    return $this->asJson(['success' => true, 'redirect' => Url::to(['sakip-lke/index-dev', 'refperiode_id' => $refperiode_id, 'refskpd_id' => $refskpd_id])]);
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
     * Deletes an existing SakipLke model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $reflke_id Reflke ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($reflke_id)
    {
        $this->findModel($reflke_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the SakipLke model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $reflke_id Reflke ID
     * @return SakipLke the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($reflke_id)
    {
        if (($model = SakipLke::findOne(['reflke_id' => $reflke_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
