<?php

namespace backend\controllers;

use Yii;
use backend\models\SakipProgram;
use backend\models\SakipUrusan;
use backend\models\SakipBidang;
use backend\models\SakipKegiatan;
use backend\models\SakipSubkegiatan;
use backend\models\search\SakipSubkegiatanSearch;
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
use backend\models\UploadSakipSubkegiatan;
use PhpOffice\PhpSpreadsheet\Reader\Csv as CsvReader;
use PhpOffice\PhpSpreadsheet\Writer\Csv as CsvWriter;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

/**
 * SakipSubkegiatanController implements the CRUD actions for SakipSubkegiatan model.
 */
class SakipSubkegiatanController extends Controller
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
     * Lists all SakipSubkegiatan models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new SakipSubkegiatanSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
        $dataProvider->pagination = false;

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single SakipSubkegiatan model.
     * @param int $refsubkegiatan_id Refsubkegiatan ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($refsubkegiatan_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($refsubkegiatan_id),
        ]);
    }

    /**
     * Creates a new SakipSubkegiatan model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new SakipSubkegiatan();

        if (Yii::$app->request->isAjax) {
            if ($model->load(Yii::$app->request->post())) {
                if ($model->save()) {
                    Yii::$app->session->setFlash('success', 'Berhasil Tambah Data');
                    return $this->asJson(['success' => true, 'redirect' => \yii\helpers\Url::to(['view', 'refsubkegiatan_id' => $model->refsubkegiatan_id])]);
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
    public function actionUpdate($refsubkegiatan_id)
    {
        $model = $this->findModel($refsubkegiatan_id);

        if (Yii::$app->request->isAjax) {
            if ($model->load(Yii::$app->request->post())) {
                if ($model->save()) {
                    Yii::$app->session->setFlash('success', 'Berhasil Memperbarui Data');
                    return $this->asJson(['success' => true, 'redirect' => \yii\helpers\Url::to(['view', 'refsubkegiatan_id' => $model->refsubkegiatan_id])]);
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


    public function actionListKegiatan($id)
    {
        $countKegiatan = SakipKegiatan::find()
            ->where(['refprogram_id' => $id])
            ->count();

        $kegiatan = SakipKegiatan::find()
            ->where(['refprogram_id' => $id])
            ->orderBy('kode_kegiatan ASC')
            ->all();

        if ($countKegiatan > 0) {
            foreach ($kegiatan as $k) {
                echo "<option value='" . $k->refkegiatan_id . "'>" . $k->kode_kegiatan . " - " . $k->nama_kegiatan . "</option>";
            }
        } else {
            echo "<option>-</option>";
        }
    }

    public function actionUpload()
    {
        $model = new UploadSakipSubkegiatan();

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

                    $subkegiatan = new SakipSubkegiatan();
                    $subkegiatan->refsubkegiatan_id = !empty($row[0]) ? $row[0] : null; // Column 0
                    $subkegiatan->kode_subkegiatan = !empty($row[1]) ? $row[1] : null; // Column 1
                    $subkegiatan->nama_subkegiatan = !empty($row[2]) ? $row[2] : null; // Column 2
                    $subkegiatan->refurusan_id = !empty($row[3]) ? (int)$row[3] : null; // Column 3
                    $subkegiatan->refbidang_id = !empty($row[4]) ? (int)$row[4] : null; // Column 4
                    $subkegiatan->refprogram_id = !empty($row[5]) ? (int)$row[5] : null; // Column 5
                    $subkegiatan->refkegiatan_id = !empty($row[6]) ? (int)$row[6] : null; // Column 5
                    $subkegiatan->subkegiatan_isaktif = !empty($row[7]) ? $row[7] : null; // Column 6

                    if (!$subkegiatan->save()) {
                        // Handle validation errors or log them
                        Yii::$app->session->setFlash('error', 'Error saving row ' . ($index + 1) . ': ' . implode(', ', $subkegiatan->getFirstErrors()));
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
     * Deletes an existing SakipSubkegiatan model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $refsubkegiatan_id Refsubkegiatan ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($refsubkegiatan_id)
    {
        $this->findModel($refsubkegiatan_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the SakipSubkegiatan model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $refsubkegiatan_id Refsubkegiatan ID
     * @return SakipSubkegiatan the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($refsubkegiatan_id)
    {
        if (($model = SakipSubkegiatan::findOne(['refsubkegiatan_id' => $refsubkegiatan_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
