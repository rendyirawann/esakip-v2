<?php

namespace frontend\controllers;

use Yii;
use frontend\models\SimonaMediacascadingsubkegiatan;
use frontend\models\search\SimonaMediacascadingsubkegiatanSearch;
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
 * SimonaMediacascadingsubkegiatanController implements the CRUD actions for SimonaMediacascadingsubkegiatan model.
 */
class SimonaMediacascadingsubkegiatanController extends Controller
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
     * Lists all SimonaMediacascadingsubkegiatan models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new SimonaMediacascadingsubkegiatanSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
        $dataProvider->pagination = false;

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single SimonaMediacascadingsubkegiatan model.
     * @param int $refsimonamediacascadingsubkegiatan_id Refsimonamediacascadingsubkegiatan ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($refsimonamediacascadingsubkegiatan_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($refsimonamediacascadingsubkegiatan_id),
        ]);
    }

    /**
     * Creates a new SimonaMediacascadingsubkegiatan model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new SimonaMediacascadingsubkegiatan();

        if ($this->request->isPost) {
            // Get the instances of the uploaded files
            $model->file_docs = UploadedFile::getInstances($model, 'file_docs');  // This returns an array of UploadedFile objects

            // Loop through each file
            if ($model->file_docs) {
                foreach ($model->file_docs as $uploadedFile) {
                    // Ensure that $uploadedFile is an instance of UploadedFile
                    if ($uploadedFile instanceof UploadedFile) {
                        $newModel = new SimonaMediacascadingsubkegiatan(); // Create a new instance for each file

                        // Load other attributes from POST
                        $newModel->load($this->request->post());

                        // Generate a unique filename for the uploaded file
                        $fileName = $uploadedFile->baseName . '-' . time() . '.' . $uploadedFile->extension;

                        // Define where to save the file (both frontend and backend)
                        $uploadPathFrontend = Yii::getAlias('@frontend/web/uploads/simona_mediacascadingsubkegiatan/') . $fileName;

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




    public function actionDownload($refsimonamediacascadingsubkegiatan_id)
    {
        $model = $this->findModel($refsimonamediacascadingsubkegiatan_id);

        // Adjust the path based on how you store your files
        $filePath = 'uploads/simona_mediacascadingsubkegiatan/' . $model->file;

        if (file_exists($filePath)) {
            Yii::$app->response->sendFile($filePath);
        } else {
            throw new \yii\web\NotFoundHttpException('The requested file does not exist.');
        }
    }

    /**
     * Updates an existing SimonaMediacascadingsubkegiatan model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $refsimonamediacascadingsubkegiatan_id Refsimonamediacascadingsubkegiatan ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($refsimonamediacascadingsubkegiatan_id)
    {
        $model = $this->findModel($refsimonamediacascadingsubkegiatan_id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'refsimonamediacascadingsubkegiatan_id' => $model->refsimonamediacascadingsubkegiatan_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing SimonaMediacascadingsubkegiatan model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $refsimonamediacascadingsubkegiatan_id Refsimonamediacascadingsubkegiatan ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($refsimonamediacascadingsubkegiatan_id)
    {
        $this->findModel($refsimonamediacascadingsubkegiatan_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the SimonaMediacascadingsubkegiatan model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $refsimonamediacascadingsubkegiatan_id Refsimonamediacascadingsubkegiatan ID
     * @return SimonaMediacascadingsubkegiatan the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($refsimonamediacascadingsubkegiatan_id)
    {
        if (($model = SimonaMediacascadingsubkegiatan::findOne(['refsimonamediacascadingsubkegiatan_id' => $refsimonamediacascadingsubkegiatan_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
