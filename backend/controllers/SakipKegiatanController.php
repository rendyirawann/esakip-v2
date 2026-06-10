<?php

namespace backend\controllers;

use Yii;
use backend\models\SakipProgram;
use backend\models\SakipUrusan;
use backend\models\SakipBidang;
use backend\models\SakipKegiatan;
use backend\models\search\SakipKegiatanSearch;
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
use backend\models\UploadSakipKegiatan;
use PhpOffice\PhpSpreadsheet\Reader\Csv as CsvReader;
use PhpOffice\PhpSpreadsheet\Writer\Csv as CsvWriter;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Spreadsheet;


/**
 * SakipKegiatanController implements the CRUD actions for SakipKegiatan model.
 */
class SakipKegiatanController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all SakipKegiatan models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new SakipKegiatanSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
        $dataProvider->pagination = false;

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single SakipKegiatan model.
     * @param int $refkegiatan_id Refkegiatan ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($refkegiatan_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($refkegiatan_id),
        ]);
    }

    /**
     * Creates a new SakipKegiatan model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new SakipKegiatan();

        if (Yii::$app->request->isAjax) {
            if ($model->load(Yii::$app->request->post())) {
                if ($model->save()) {
                    Yii::$app->session->setFlash('success', 'Berhasil Tambah Data');
                    return $this->asJson(['success' => true, 'redirect' => \yii\helpers\Url::to(['view', 'refkegiatan_id' => $model->refkegiatan_id])]);
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

    /**
     * Updates an existing SakipProgram model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $refprogram_id Refprogram ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($refkegiatan_id)
    {
        $model = $this->findModel($refkegiatan_id);

        if (Yii::$app->request->isAjax) {
            if ($model->load(Yii::$app->request->post())) {
                if ($model->save()) {
                    Yii::$app->session->setFlash('success', 'Berhasil Memperbarui Data');
                    return $this->asJson(['success' => true, 'redirect' => \yii\helpers\Url::to(['view', 'refkegiatan_id' => $model->refkegiatan_id])]);
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

    public function actionLists($id)
    {
        $countBidang = SakipBidang::find()
            ->where(['refurusan_id' => $id])
            ->count();

        $bidang = SakipBidang::find()
            ->where(['refurusan_id' => $id])
            ->orderBy('kode_bidang ASC')
            ->all();

        if ($countBidang > 0) {
            foreach ($bidang as $b) {
                echo "<option value='" . $b->refbidang_id . "'>" . $b->kode_bidang . " - " . $b->nama_bidang . "</option>";
            }
        } else {
            echo "<option>-</option>";
        }
    }


    public function actionListProgram($id)
    {
        $countProgram = SakipProgram::find()
            ->where(['refbidang_id' => $id])
            ->count();

        $program = SakipProgram::find()
            ->where(['refbidang_id' => $id])
            ->orderBy('kode_program ASC')
            ->all();

        if ($countProgram > 0) {
            foreach ($program as $p) {
                echo "<option value='" . $p->refprogram_id . "'>" . $p->kode_program . " - " . $p->nama_program . "</option>";
            }
        } else {
            echo "<option>-</option>";
        }
    }

    public function actionUpload()
    {
        $model = new UploadSakipKegiatan();

        if (Yii::$app->request->isPost) {
            $model->file = UploadedFile::getInstance($model, 'file');
            $filePath = $model->upload();
            if ($filePath) {
                // Load CSV file using PhpSpreadsheet CSV reader
                $reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
                $reader->setDelimiter(';'); // Set the delimiter to semicolon
                $spreadsheet = $reader->load($filePath);
                $worksheet = $spreadsheet->getActiveSheet();
                $rows = $worksheet->toArray();

                // Iterate through the rows, skipping the header if necessary
                foreach ($rows as $index => $row) {
                    if ($index === 0) {
                        // Skip the header row if present
                        continue;
                    }

                    // Trim each row's data to remove unwanted spaces
                    $row = array_map('trim', $row);

                    $kegiatan = new SakipKegiatan();
                    $kegiatan->refkegiatan_id = !empty($row[0]) ? $row[0] : null; // Column 0
                    $kegiatan->kode_kegiatan = !empty($row[1]) ? $row[1] : null; // Column 1
                    $kegiatan->nama_kegiatan = !empty($row[2]) ? $row[2] : null; // Column 2
                    $kegiatan->refurusan_id = !empty($row[3]) ? (int)$row[3] : null; // Column 3
                    $kegiatan->refbidang_id = !empty($row[4]) ? (int)$row[4] : null; // Column 4
                    $kegiatan->refprogram_id = !empty($row[5]) ? (int)$row[5] : null; // Column 5
                    $kegiatan->kegiatan_isaktif = !empty($row[6]) ? $row[6] : null; // Column 6

                    if (!$kegiatan->save()) {
                        // Handle validation errors or log them
                        Yii::$app->session->setFlash('error', 'Error saving row ' . ($index + 1) . ': ' . implode(', ', $kegiatan->getFirstErrors()));
                        return $this->render('upload', ['model' => $model]);
                    }
                }

                Yii::$app->session->setFlash('success', 'CSV file has been successfully uploaded and data saved to database.');
                return $this->redirect(['index']);
            }
        }

        return $this->render('upload', [
            'model' => $model,
        ]);
    }

    private function parseDecimal($value)
    {
        // Replace commas with periods for decimal values
        return str_replace(',', '.', $value);
    }


    /**
     * Deletes an existing SakipKegiatan model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $refkegiatan_id Refkegiatan ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($refkegiatan_id)
    {
        $this->findModel($refkegiatan_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the SakipKegiatan model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $refkegiatan_id Refkegiatan ID
     * @return SakipKegiatan the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($refkegiatan_id)
    {
        if (($model = SakipKegiatan::findOne(['refkegiatan_id' => $refkegiatan_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
