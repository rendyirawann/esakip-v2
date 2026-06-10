<?php

namespace frontend\controllers;

use Yii;
use frontend\models\SimonaMediacascadingkegiatan;
use frontend\models\search\SimonaMediacascadingkegiatanSearch;
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
 * SimonaMediacascadingkegiatanController implements the CRUD actions for SimonaMediacascadingkegiatan model.
 */
class SimonaMediacascadingkegiatanController extends Controller
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
     * Lists all SimonaMediacascadingkegiatan models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new SimonaMediacascadingkegiatanSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
        $dataProvider->pagination = false;

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single SimonaMediacascadingkegiatan model.
     * @param int $refsimonamediacascadingkegiatan_id Refsimonamediacascadingkegiatan ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($refsimonamediacascadingkegiatan_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($refsimonamediacascadingkegiatan_id),
        ]);
    }

    /**
     * Creates a new SimonaMediacascadingkegiatan model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new SimonaMediacascadingkegiatan();

        if ($this->request->isPost) {
            // Get the instances of the uploaded files
            $model->file_docs = UploadedFile::getInstances($model, 'file_docs');  // This returns an array of UploadedFile objects

            // Loop through each file
            if ($model->file_docs) {
                foreach ($model->file_docs as $uploadedFile) {
                    // Ensure that $uploadedFile is an instance of UploadedFile
                    if ($uploadedFile instanceof UploadedFile) {
                        $newModel = new SimonaMediacascadingkegiatan(); // Create a new instance for each file

                        // Load other attributes from POST
                        $newModel->load($this->request->post());

                        // Generate a unique filename for the uploaded file
                        $fileName = $uploadedFile->baseName . '-' . time() . '.' . $uploadedFile->extension;

                        // Define where to save the file (both frontend and backend)
                        $uploadPathFrontend = Yii::getAlias('@frontend/web/uploads/simona_mediacascadingkegiatan/') . $fileName;

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




    public function actionDownload($refsimonamediacascadingkegiatan_id)
    {
        $model = $this->findModel($refsimonamediacascadingkegiatan_id);

        // Adjust the path based on how you store your files
        $filePath = 'uploads/simona_mediacascadingkegiatan/' . $model->file;

        if (file_exists($filePath)) {
            Yii::$app->response->sendFile($filePath);
        } else {
            throw new \yii\web\NotFoundHttpException('The requested file does not exist.');
        }
    }


    /**
     * Updates an existing SimonaMediacascadingkegiatan model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $refsimonamediacascadingkegiatan_id Refsimonamediacascadingkegiatan ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($refsimonamediacascadingkegiatan_id)
    {
        $model = $this->findModel($refsimonamediacascadingkegiatan_id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'refsimonamediacascadingkegiatan_id' => $model->refsimonamediacascadingkegiatan_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing SimonaMediacascadingkegiatan model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $refsimonamediacascadingkegiatan_id Refsimonamediacascadingkegiatan ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($refsimonamediacascadingkegiatan_id)
    {
        $this->findModel($refsimonamediacascadingkegiatan_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the SimonaMediacascadingkegiatan model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $refsimonamediacascadingkegiatan_id Refsimonamediacascadingkegiatan ID
     * @return SimonaMediacascadingkegiatan the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($refsimonamediacascadingkegiatan_id)
    {
        if (($model = SimonaMediacascadingkegiatan::findOne(['refsimonamediacascadingkegiatan_id' => $refsimonamediacascadingkegiatan_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
