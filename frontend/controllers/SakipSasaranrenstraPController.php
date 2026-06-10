<?php

namespace frontend\controllers;

use frontend\models\SakipSasaranrenstraP;
use frontend\models\search\SakipSasaranrenstraPSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use Yii;
use frontend\models\SakipSasaranrenstra;
use frontend\models\SakipIndikatorsasaranrenstraP;
use frontend\models\SakipSkpd;
use frontend\models\SakipPeriode;
use frontend\models\SakipSasaranP;
use frontend\models\SakipTujuanP;
use frontend\models\SakipTujuanrenstraP;
use frontend\models\SakipVisiP;
use frontend\models\SakipMisiP;
use frontend\models\SakipKoordinasi;
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
use yii\web\ForbiddenHttpException;

/**
 * SakipSasaranrenstraPController implements the CRUD actions for SakipSasaranrenstraP model.
 */
class SakipSasaranrenstraPController extends Controller
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
     * Lists all SakipSasaranrenstraP models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new SakipSasaranrenstraPSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
        $dataProvider->pagination = false;

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single SakipSasaranrenstraP model.
     * @param int $refsasaranrenstra_p_id Refsasaranrenstra P ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($refsasaranrenstra_p_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($refsasaranrenstra_p_id),
        ]);
    }

    /**
     * Creates a new SakipSasaranrenstraP model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate($refperiode_id = null)
    {
        $model = new SakipSasaranrenstraP();

        // Ambil refskpd_id dari user saat ini
        $user = Yii::$app->user->identity;
        $refskpd_id = $user->refskpd_id;

        // Ambil data dari sakip_periode
        $periodeList = SakipPeriode::find()
            ->where(['periode_isaktif' => 'T'])
            ->all();


        if (Yii::$app->request->isAjax) {
            if ($model->load(Yii::$app->request->post())) {
                if ($model->save()) {
                    Yii::$app->session->setFlash('success', 'Berhasil Tambah Data');
                    return $this->asJson(['success' => true, 'redirect' => \yii\helpers\Url::to(['sakip-sasaranrenstra/index', 'refperiode_id' => $model->refperiode_id])]);
                } else {
                    // Tambahkan log error
                    Yii::error("Error saving data: " . json_encode($model->getErrors()));
                    return $this->asJson(['success' => false, 'errors' => $model->getErrors()]);
                }
            }

            return $this->renderAjax('_form', [
                'model' => $model,
                'periodeList' => $periodeList, // Pass periodeList to form
            ]);
        }

        return $this->render('create', [
            'model' => $model,
            'periodeList' => $periodeList, // Pass periodeList to form
        ]);
    }

    public function actionGetSasaranOptions($refperiode_id)
    {
        $selectedPeriod = SakipPeriode::find()->where(['refperiode_id' => $refperiode_id])->one();
        $refperiode_5tahun_id = $selectedPeriod ? $selectedPeriod->refperiode_5tahun_id : null;

        $options = SakipSasaranP::find()
            ->where(['refperiode_5tahun_id' => $refperiode_5tahun_id])
            ->all();

        $optionsHtml = "<option value=''>Select Sasaran</option>";
        foreach ($options as $option) {
            $optionsHtml .= "<option value='{$option->refsasaran_p_id}'>{$option->uraian_sasaran_p}</option>";
        }

        return $optionsHtml;
    }

    /**
     * Updates an existing SakipSasaranrenstraP model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $refsasaranrenstra_p_id Refsasaranrenstra P ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($refsasaranrenstra_p_id, $refperiode_id = null)
    {
        $model = $this->findModel($refsasaranrenstra_p_id);

        // Ambil refskpd_id dari user saat ini
        $user = Yii::$app->user->identity;
        $refskpd_id = $user->refskpd_id;

        // Ambil data dari sakip_periode
        $periodeList = SakipPeriode::find()->all(); // Query to get all periods

        if (Yii::$app->request->isAjax) {
            if ($model->load(Yii::$app->request->post())) {
                if ($model->save()) {
                    Yii::$app->session->setFlash('success', 'Berhasil Memperbarui Data');
                    return $this->asJson(['success' => true, 'redirect' => \yii\helpers\Url::to(['sakip-sasaranrenstra/index', 'refperiode_id' => $model->refperiode_id])]);
                } else {
                    // Tambahkan log error
                    Yii::error("Error saving data: " . json_encode($model->getErrors()));
                    return $this->asJson(['success' => false, 'errors' => $model->getErrors()]);
                }
            }

            return $this->renderAjax('_formupdatesasaranrenstra', [
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

    public function actionUpdateTujuanrenstra($refsasaranrenstra_p_id, $refperiode_id = null)
    {
        $model = $this->findModel($refsasaranrenstra_p_id);

        // Fetch the list of Tujuan that match the refsasaranrenstra_p_id of the current model
        $tujuanList = SakipTujuanrenstraP::find()
            ->where([
                'refskpd_id' => Yii::$app->user->identity->refskpd_id,
                // 'refsasaranrenstra_p_id' => $model->refsasaranrenstra_p_id, // Filter by refsasaranrenstra_p_id
            ])
            ->all();

        if (Yii::$app->request->isAjax) {
            if ($model->load(Yii::$app->request->post())) {
                if ($model->save()) {
                    Yii::$app->session->setFlash('success', 'Berhasil Memperbarui Data');
                    return $this->asJson(['success' => true, 'redirect' => \yii\helpers\Url::to(['index', 'refperiode_id' => $model->refperiode_id])]);
                } else {
                    Yii::error("Error saving data: " . json_encode($model->getErrors()));
                    return $this->asJson(['success' => false, 'errors' => $model->getErrors()]);
                }
            }

            return $this->renderAjax('_formupdate', [
                'model' => $model,
                'tujuanList' => $tujuanList, // Pass the filtered list to the view
            ]);
        }

        return $this->render('update-tujuanrenstra', [
            'model' => $model,
            'tujuanList' => $tujuanList, // Pass the filtered list to the view
        ]);
    }

    public function actionUpdateFormulasi($refsasaranrenstra_p_id, $refperiode_id = null)
    {
        $model = $this->findModel($refsasaranrenstra_p_id);

        if (Yii::$app->request->isAjax) {
            if ($model->load(Yii::$app->request->post())) {
                if ($model->save()) {
                    Yii::$app->session->setFlash('success', 'Berhasil Memperbarui Data');
                    return $this->asJson(['success' => true, 'redirect' => \yii\helpers\Url::to(['index-formulasi', 'refperiode_id' => $model->refperiode_id])]);
                } else {
                    // Tambahkan log error
                    Yii::error("Error saving data: " . json_encode($model->getErrors()));
                    return $this->asJson(['success' => false, 'errors' => $model->getErrors()]);
                }
            }

            return $this->renderAjax('_formformulasi', [
                'model' => $model,
            ]);
        }

        return $this->render('update-formulasi', [
            'model' => $model,
        ]);
    }

    public function actionGetReftujuan($id)
    {
        $model = SakipSasaranP::find()->where(['refsasaran_p_id' => $id])->one();
        if ($model && $model->refTujuan) {
            return $this->asJson([
                'success' => true,
                'reftujuan_p_id' => $model->refTujuan->reftujuan_p_id,
                'uraian_tujuan_p' => $model->refTujuan->uraian_tujuan_p,
            ]);
        }
        return $this->asJson(['success' => false]);
    }

    public function actionGetRefvisi($id)
    {
        $model = SakipSasaranP::find()->where(['refsasaran_p_id' => $id])->one();
        if ($model && $model->refVisi) {
            return $this->asJson([
                'success' => true,
                'refvisi_p_id' => $model->refVisi->refvisi_p_id,
                'uraian_visi_p' => $model->refVisi->uraian_visi_p,
            ]);
        }
        return $this->asJson(['success' => false]);
    }


    public function actionGetRefmisi($id)
    {
        $model = SakipSasaranP::find()->where(['refsasaran_p_id' => $id])->one();
        if ($model && $model->refMisi) {
            return $this->asJson([
                'success' => true,
                'refmisi_p_id' => $model->refMisi->refmisi_p_id,
                'uraian_misi_p' => $model->refMisi->uraian_misi_p,
            ]);
        }
        return $this->asJson(['success' => false]);
    }


    /**
     * Deletes an existing SakipSasaranrenstra model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $refsasaranrenstra_id Refsasaranrenstra ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($refsasaranrenstra_p_id)
    {
        $model = $this->findModel($refsasaranrenstra_p_id);

        if (!$model) {
            throw new NotFoundHttpException('Data tidak ditemukan.');
        }

        // Cek apakah refsasaranrenstra_id masih terkait dengan refindikatorsasaranrenstra_id
        $relatedIndicators = SakipIndikatorsasaranrenstraP::find()
            ->where(['refsasaranrenstra_p_id' => $refsasaranrenstra_p_id])
            ->exists();

        if ($relatedIndicators) {
            Yii::$app->session->setFlash('error', 'Gagal menghapus! refsasaranrenstra_p_id masih terkait dengan salah satu refindikatorsasaranrenstra_p_id.');
            return $this->redirect(['index', 'refperiode_id' => $model->refperiode_id]);
        }

        // Ambil refperiode_id sebelum menghapus model
        $refperiode_id = $model->refperiode_id;

        // Hapus model jika tidak ada keterkaitan
        $model->delete();

        Yii::$app->session->setFlash('success', 'Data berhasil dihapus.');

        // Redirect kembali ke index dengan refperiode_id
        return $this->redirect(['index', 'refperiode_id' => $refperiode_id]);
    }


    /**
     * Finds the SakipSasaranrenstra model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $refsasaranrenstra_id Refsasaranrenstra ID
     * @return SakipSasaranrenstra the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($refsasaranrenstra_p_id)
    {
        if (($model = SakipSasaranrenstraP::findOne(['refsasaranrenstra_p_id' => $refsasaranrenstra_p_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
