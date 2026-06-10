<?php

namespace frontend\controllers;

use Yii;
use frontend\models\SimonaMediacascadingsubkegiatanOpd;
use frontend\models\search\SimonaMediacascadingsubkegiatanOpdSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use frontend\models\SimonaMediacascadingkegiatanOpd;
use frontend\models\search\SimonaMediacascadingkegiatanOpdSearch;
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
 * SimonaMediacascadingsubkegiatanOpdController implements the CRUD actions for SimonaMediacascadingsubkegiatanOpd model.
 */
class SimonaMediacascadingsubkegiatanOpdController extends Controller
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
     * Lists all SimonaMediacascadingsubkegiatanOpd models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new SimonaMediacascadingsubkegiatanOpdSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
        $dataProvider->pagination = false;

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single SimonaMediacascadingsubkegiatanOpd model.
     * @param int $refsimonamediacascadingsubkegiatanopd_id Refsimonamediacascadingsubkegiatanopd ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($refsimonamediacascadingsubkegiatanopd_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($refsimonamediacascadingsubkegiatanopd_id),
        ]);
    }

    /**
     * Creates a new SimonaMediacascadingsubkegiatanOpd model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new SimonaMediacascadingsubkegiatanOpd();

        if ($this->request->isPost) {
            // Get the instances of the uploaded files
            $model->file_docs = UploadedFile::getInstances($model, 'file_docs');  // This returns an array of UploadedFile objects

            // Loop through each file
            if ($model->file_docs) {
                foreach ($model->file_docs as $uploadedFile) {
                    // Ensure that $uploadedFile is an instance of UploadedFile
                    if ($uploadedFile instanceof UploadedFile) {
                        $newModel = new SimonaMediacascadingsubkegiatanOpd(); // Create a new instance for each file

                        // Load other attributes from POST
                        $newModel->load($this->request->post());

                        // Generate a unique filename for the uploaded file
                        $fileName = $uploadedFile->baseName . '-' . time() . '.' . $uploadedFile->extension;

                        // Define where to save the file (both frontend and backend)
                        $uploadPathFrontend = Yii::getAlias('@frontend/web/uploads/simona_mediacascadingsubkegiatan_opd/') . $fileName;

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




    public function actionDownload($refsimonamediacascadingsubkegiatanopd_id)
    {
        $model = $this->findModel($refsimonamediacascadingsubkegiatanopd_id);

        // Adjust the path based on how you store your files
        $filePath = 'uploads/simona_mediacascadingsubkegiatan_opd/' . $model->file;

        if (file_exists($filePath)) {
            Yii::$app->response->sendFile($filePath);
        } else {
            throw new \yii\web\NotFoundHttpException('The requested file does not exist.');
        }
    }

    /**
     * Updates an existing SimonaMediacascadingsubkegiatanOpd model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $refsimonamediacascadingsubkegiatanopd_id Refsimonamediacascadingsubkegiatanopd ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($refsimonamediacascadingsubkegiatanopd_id)
    {
        $model = $this->findModel($refsimonamediacascadingsubkegiatanopd_id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'refsimonamediacascadingsubkegiatanopd_id' => $model->refsimonamediacascadingsubkegiatanopd_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing SimonaMediacascadingsubkegiatanOpd model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $refsimonamediacascadingsubkegiatanopd_id Refsimonamediacascadingsubkegiatanopd ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($refsimonamediacascadingsubkegiatanopd_id)
    {
        $this->findModel($refsimonamediacascadingsubkegiatanopd_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the SimonaMediacascadingsubkegiatanOpd model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $refsimonamediacascadingsubkegiatanopd_id Refsimonamediacascadingsubkegiatanopd ID
     * @return SimonaMediacascadingsubkegiatanOpd the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($refsimonamediacascadingsubkegiatanopd_id)
    {
        if (($model = SimonaMediacascadingsubkegiatanOpd::findOne(['refsimonamediacascadingsubkegiatanopd_id' => $refsimonamediacascadingsubkegiatanopd_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
