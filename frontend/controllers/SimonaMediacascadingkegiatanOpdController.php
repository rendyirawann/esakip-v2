<?php

namespace frontend\controllers;

use Yii;
use frontend\models\SimonaMediacascadingkegiatanOpd;
use frontend\models\search\SimonaMediacascadingkegiatanOpdSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
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
 * SimonaMediacascadingkegiatanOpdController implements the CRUD actions for SimonaMediacascadingkegiatanOpd model.
 */
class SimonaMediacascadingkegiatanOpdController extends Controller
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
     * Lists all SimonaMediacascadingkegiatanOpd models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new SimonaMediacascadingkegiatanOpdSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
        $dataProvider->pagination = false;

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single SimonaMediacascadingkegiatanOpd model.
     * @param int $refsimonamediacascadingkegiatanopd_id Refsimonamediacascadingkegiatanopd ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($refsimonamediacascadingkegiatanopd_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($refsimonamediacascadingkegiatanopd_id),
        ]);
    }

    /**
     * Creates a new SimonaMediacascadingkegiatanOpd model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new SimonaMediacascadingkegiatanOpd();

        if ($this->request->isPost) {
            // Get the instances of the uploaded files
            $model->file_docs = UploadedFile::getInstances($model, 'file_docs');  // This returns an array of UploadedFile objects

            // Loop through each file
            if ($model->file_docs) {
                foreach ($model->file_docs as $uploadedFile) {
                    // Ensure that $uploadedFile is an instance of UploadedFile
                    if ($uploadedFile instanceof UploadedFile) {
                        $newModel = new SimonaMediacascadingkegiatanOpd(); // Create a new instance for each file

                        // Load other attributes from POST
                        $newModel->load($this->request->post());

                        // Generate a unique filename for the uploaded file
                        $fileName = $uploadedFile->baseName . '-' . time() . '.' . $uploadedFile->extension;

                        // Define where to save the file (both frontend and backend)
                        $uploadPathFrontend = Yii::getAlias('@frontend/web/uploads/simona_mediacascadingkegiatan_opd/') . $fileName;

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




    public function actionDownload($refsimonamediacascadingkegiatanopd_id)
    {
        $model = $this->findModel($refsimonamediacascadingkegiatanopd_id);

        // Adjust the path based on how you store your files
        $filePath = 'uploads/simona_mediacascadingkegiatan_opd/' . $model->file;

        if (file_exists($filePath)) {
            Yii::$app->response->sendFile($filePath);
        } else {
            throw new \yii\web\NotFoundHttpException('The requested file does not exist.');
        }
    }

    /**
     * Updates an existing SimonaMediacascadingkegiatanOpd model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $refsimonamediacascadingkegiatanopd_id Refsimonamediacascadingkegiatanopd ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($refsimonamediacascadingkegiatanopd_id)
    {
        $model = $this->findModel($refsimonamediacascadingkegiatanopd_id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'refsimonamediacascadingkegiatanopd_id' => $model->refsimonamediacascadingkegiatanopd_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing SimonaMediacascadingkegiatanOpd model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $refsimonamediacascadingkegiatanopd_id Refsimonamediacascadingkegiatanopd ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($refsimonamediacascadingkegiatanopd_id)
    {
        $this->findModel($refsimonamediacascadingkegiatanopd_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the SimonaMediacascadingkegiatanOpd model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $refsimonamediacascadingkegiatanopd_id Refsimonamediacascadingkegiatanopd ID
     * @return SimonaMediacascadingkegiatanOpd the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($refsimonamediacascadingkegiatanopd_id)
    {
        if (($model = SimonaMediacascadingkegiatanOpd::findOne(['refsimonamediacascadingkegiatanopd_id' => $refsimonamediacascadingkegiatanopd_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
