<?php

namespace frontend\controllers;

use Yii;
use frontend\models\SimonaKeluaranmediacascadingkegiatan;
use frontend\models\search\SimonaKeluaranmediacascadingkegiatanSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use frontend\models\SimonaMediacascadingkegiatan;
use frontend\models\search\SimonaMediacascadingkegiatanSearch;
use yii\web\IdentityInterface;
use yii\web\UploadedFile;
use yii\helpers\Url;
use yii\web\Response;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\db\Expression;
use yii\helpers\FileHelper;
use yii\helpers\Html;
use yii\filters\AccessControl;

/**
 * SimonaKeluaranmediacascadingkegiatanController implements the CRUD actions for SimonaKeluaranmediacascadingkegiatan model.
 */
class SimonaKeluaranmediacascadingkegiatanController extends Controller
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
     * Lists all SimonaKeluaranmediacascadingkegiatan models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new SimonaKeluaranmediacascadingkegiatanSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
        $dataProvider->pagination = false;

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single SimonaKeluaranmediacascadingkegiatan model.
     * @param int $refsimonakeluaranmediacascadingkegiatan_id Refsimonakeluaranmediacascadingkegiatan ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($refsimonakeluaranmediacascadingkegiatan_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($refsimonakeluaranmediacascadingkegiatan_id),
        ]);
    }

    /**
     * Creates a new SimonaKeluaranmediacascadingkegiatan model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new SimonaKeluaranmediacascadingkegiatan();

        if ($this->request->isPost) {
            // Get the instances of the uploaded files
            $model->file_docs = UploadedFile::getInstances($model, 'file_docs');  // This returns an array of UploadedFile objects

            // Loop through each file
            if ($model->file_docs) {
                foreach ($model->file_docs as $uploadedFile) {
                    // Ensure that $uploadedFile is an instance of UploadedFile
                    if ($uploadedFile instanceof UploadedFile) {
                        $newModel = new SimonaKeluaranmediacascadingkegiatan(); // Create a new instance for each file

                        // Load other attributes from POST
                        $newModel->load($this->request->post());

                        // Generate a unique filename for the uploaded file
                        $fileName = $uploadedFile->baseName . '-' . time() . '.' . $uploadedFile->extension;

                        // Define where to save the file (both frontend and backend)
                        $uploadPathFrontend = Yii::getAlias('@frontend/web/uploads/simona_keluaranmediacascadingkegiatan/') . $fileName;

                        // Save the uploaded file
                        if ($uploadedFile->saveAs($uploadPathFrontend, false)) {
                            // Set the file name in the new model
                            $newModel->file = $fileName;

                            // Save the model (for each file uploaded)
                            if (!$newModel->save()) {
                                Yii::$app->session->setFlash('error', 'Failed to save the file.');
                            }
                        } else {
                            Yii::$app->session->setFlash('error', 'Failed to upload the file.');
                        }
                    } else {
                        Yii::$app->session->setFlash('error', 'Uploaded file is not valid.');
                    }
                }

                Yii::$app->session->setFlash('success', 'Files successfully uploaded.');

                // Redirect back to the previous URL
                return $this->redirect(Yii::$app->request->referrer ?: ['index']); // Fallback to 'index' if referrer is not available
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }




    public function actionDownload($refsimonakeluaranmediacascadingkegiatan_id)
    {
        $model = $this->findModel($refsimonakeluaranmediacascadingkegiatan_id);

        // --- PERBAIKAN DI SINI ---
        // Gunakan Yii::getAlias('@webroot') untuk membuat path absolut ke file
        $filePath = Yii::getAlias('@webroot/uploads/simona_keluaranmediacascadingkegiatan/' . $model->file);

        if (file_exists($filePath)) {
            // Yii::$app->response->sendFile() akan menangani header dan pengiriman file
            return Yii::$app->response->sendFile($filePath);
        } else {
            // Jika file tetap tidak ada, lempar error
            throw new \yii\web\NotFoundHttpException('File yang diminta tidak ada di server.');
        }
    }

    /**
     * Updates an existing SimonaKeluaranmediacascadingkegiatan model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $refsimonakeluaranmediacascadingkegiatan_id Refsimonakeluaranmediacascadingkegiatan ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($refsimonakeluaranmediacascadingkegiatan_id)
    {
        $model = $this->findModel($refsimonakeluaranmediacascadingkegiatan_id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'refsimonakeluaranmediacascadingkegiatan_id' => $model->refsimonakeluaranmediacascadingkegiatan_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing SimonaKeluaranmediacascadingkegiatan model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $refsimonakeluaranmediacascadingkegiatan_id Refsimonakeluaranmediacascadingkegiatan ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($refsimonakeluaranmediacascadingkegiatan_id)
    {
        $this->findModel($refsimonakeluaranmediacascadingkegiatan_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the SimonaKeluaranmediacascadingkegiatan model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $refsimonakeluaranmediacascadingkegiatan_id Refsimonakeluaranmediacascadingkegiatan ID
     * @return SimonaKeluaranmediacascadingkegiatan the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($refsimonakeluaranmediacascadingkegiatan_id)
    {
        if (($model = SimonaKeluaranmediacascadingkegiatan::findOne(['refsimonakeluaranmediacascadingkegiatan_id' => $refsimonakeluaranmediacascadingkegiatan_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
