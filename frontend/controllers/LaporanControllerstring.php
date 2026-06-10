<?php

namespace frontend\controllers;

use backend\models\SakipPenjabatSkpd;
use Yii;
use frontend\models\SakipSkpd;
use frontend\models\SakipPeriode;
use frontend\models\SakipPimpinan;
use frontend\models\SakipSasaranrenstra;
use frontend\models\SakipIndikatorsasaranrenstra;
use frontend\models\SakipIndikatorsasaranrenstraTriwulan;
use frontend\models\SakipStrategi;
use frontend\models\SakipKebijakan;
use frontend\models\SakipCascadingprogram;
use frontend\models\SakipCascadingsubkegiatan;
use frontend\models\SakipPenjabatskpdCascadingprogram;
use frontend\models\SakipPenjabatskpdCascadingkegiatan;
use frontend\models\SakipPenjabatskpdCascadingsubkegiatan;
use frontend\models\SakipIndikatorcascadingprogram;
use frontend\models\SakipIndikatorcascadingsubkegiatan;
use frontend\models\SakipIndikatorcascadingsubkegiatanTriwulan;
use Moonland\Phpexcel\Excel;
use yii\web\Controller;
use yii\web\Response;
use yii\helpers\Html;
use kartik\mpdf\Pdf;
use kartik\export\ExportMenu; // Import ExportMenu
use yii\web\UploadedFile;
use yii\helpers\Url;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\db\Expression;
use yii\helpers\Json;
use yii\helpers\ArrayHelper;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

class LaporanController extends Controller
{

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

    public function actionIndexLaporanRenstra($refperiode_id = null)
    {
        // Get refskpd_id from the current user
        $user = Yii::$app->user->identity;
        $refskpd_id = $user->refskpd_id;

        // Get the name of the SKPD based on refskpd_id
        $nama_skpd = SakipSkpd::find()->select('nama_skpd')->where(['refskpd_id' => $refskpd_id])->scalar();

        // Set default period to this year if not provided
        if ($refperiode_id === null) {
            $currentYear = date('Y');
            $defaultPeriod = SakipPeriode::find()->where(['periode' => $currentYear])->one();
            $refperiode_id = $defaultPeriod ? $defaultPeriod->refperiode_id : null;
        }

        // Fetch all periods
        $periodeList = SakipPeriode::find()->all();

        // Fetch data based on refskpd_id and refperiode_id
        $sasaranRenstra = SakipSasaranrenstra::find()
            ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
            ->with(['refVisi', 'refMisi', 'refTujuan', 'refSasaran'])
            ->all();

        // Fetch strategi based on refskpd_id and refperiode_id
        $strategiList = SakipStrategi::find()
            ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
            ->all();

        // Fetch kebijakan based on refskpd_id and refperiode_id
        $kebijakanList = SakipKebijakan::find()
            ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
            ->all();

        // Retrieve the periode based on refperiode_id
        $selectedPeriod = SakipPeriode::find()->where(['refperiode_id' => $refperiode_id])->one();
        $selectedPeriodValue = $selectedPeriod ? $selectedPeriod->periode : null; // Get the periode value

        // Set the dynamic title
        $this->view->title = "Laporan Renstra $selectedPeriodValue - " . Html::encode($nama_skpd);

        return $this->render('index-laporan-renstra', [
            'periodeList' => $periodeList,
            'selectedPeriodId' => $refperiode_id,
            'refskpd_id' => $refskpd_id,
            'refperiode_id' => $refperiode_id,
            'sasaranRenstra' => $sasaranRenstra,
            'strategiList' => $strategiList,
            'kebijakanList' => $kebijakanList,
            'nama_skpd' => $nama_skpd,
            'selectedPeriodValue' => $selectedPeriodValue, // Include selected period value
        ]);
    }

    public function actionIndexLaporanRenstraDev($refperiode_id = null, $refskpd_id = null)
    {
        // Ambil refskpd_id dari user saat ini jika tidak ada di request
        if ($refskpd_id === null) {
            $user = Yii::$app->user->identity;
            $refskpd_id = $user->refskpd_id;
        }


        // Get the name of the SKPD based on refskpd_id
        $nama_skpd = SakipSkpd::find()->select('nama_skpd')->where(['refskpd_id' => $refskpd_id])->scalar();

        // Set default period to this year if not provided
        if ($refperiode_id === null) {
            $currentYear = date('Y');
            $defaultPeriod = SakipPeriode::find()->where(['periode' => $currentYear])->one();
            $refperiode_id = $defaultPeriod ? $defaultPeriod->refperiode_id : null;
        }

        // Fetch all periods
        $periodeList = SakipPeriode::find()->all();

        // Fetch data based on refskpd_id and refperiode_id
        $sasaranRenstra = SakipSasaranrenstra::find()
            ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
            ->with(['refVisi', 'refMisi', 'refTujuan', 'refSasaran'])
            ->all();

        // Fetch strategi based on refskpd_id and refperiode_id
        $strategiList = SakipStrategi::find()
            ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
            ->all();

        // Fetch kebijakan based on refskpd_id and refperiode_id
        $kebijakanList = SakipKebijakan::find()
            ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
            ->all();

        // Ambil daftar SKPD untuk dropdown
        $skpdList = ArrayHelper::map(SakipSkpd::find()->all(), 'refskpd_id', 'nama_skpd');


        // Retrieve the periode based on refperiode_id
        $selectedPeriod = SakipPeriode::find()->where(['refperiode_id' => $refperiode_id])->one();
        $selectedPeriodValue = $selectedPeriod ? $selectedPeriod->periode : null; // Get the periode value

        // Set the dynamic title
        $this->view->title = "Laporan Renstra $selectedPeriodValue - " . Html::encode($nama_skpd);

        return $this->render('index-laporan-renstra-dev', [
            'periodeList' => $periodeList,
            'selectedPeriodId' => $refperiode_id,
            'refskpd_id' => $refskpd_id,
            'refperiode_id' => $refperiode_id,
            'sasaranRenstra' => $sasaranRenstra,
            'strategiList' => $strategiList,
            'kebijakanList' => $kebijakanList,
            'nama_skpd' => $nama_skpd,
            'selectedPeriodValue' => $selectedPeriodValue, // Include selected period value
            'selectedSkpdId' => $refskpd_id,
            'skpdList' => $skpdList,
        ]);
    }

    public function actionDownloadPdf()
    {
        if (Yii::$app->request->isPost) {
            $content = Yii::$app->request->post('content'); // Konten dari POST
            $title = Yii::$app->request->post('title', 'Laporan'); // Judul untuk nama file

            // Define CSS untuk styling PDF
            $css = <<<CSS
            .tbdata {
                border-collapse: collapse;
                font-family: 'Public Sans', sans-serif;
                font-size: 11px;
            }
            .tbdata th {
                height: 40px;
                background: rgb(0, 160, 221);
                text-align: center;
                border: 1px solid #cfcfcf;
                color: #ffffff;
                padding: 2px;
            }
            .title {
        border-collapse: collapse;
        font-family: 'Public Sans', sans-serif;
        /* Menggunakan font Public Sans */
        font-size: 11px;
        text-align: center;
    }
            .tbdata td {
                padding: 2px;
                vertical-align: top;
            }
            .tebal {
                font-weight: bold;
            }
            .tblRenstra {
                width: 100%;
        font-family: 'Public Sans', sans-serif;
                border-collapse: collapse;
                font-size: 11px;
            }
            .tblRenstra td {
                padding: 2px;
                border: 1px solid #f2f2f2;
            }
            .tblRenstra .header {
                text-align: center;
                vertical-align: middle;
                font-weight: bold;
                background: #03b0e2;
                color: #ffffff;
            }
            .trO {
                background: #f2f2f2;
            }
            .trE {
                background: white;
            }
            .tblAtas {
                margin-left: 30px;
        font-family: 'Public Sans', sans-serif;
                border-collapse: collapse;
                font-size: 11px;
            }
            thead {
                display: table-header-group;
            }
CSS;

            // Setting untuk file PDF dengan CSS
            $pdf = new Pdf([
                'mode' => Pdf::MODE_CORE,
                'format' => Pdf::FORMAT_A4,
                'orientation' => Pdf::ORIENT_PORTRAIT,
                'destination' => Pdf::DEST_DOWNLOAD,
                'filename' => $title . '.pdf',
                'content' => $content, // Konten dari POST
                'cssInline' => $css, // Gunakan CSS inline
                'options' => ['title' => $title],
                'methods' => [
                    'SetHeader' => [$title],
                    'SetFooter' => ['{PAGENO}'],
                ],
            ]);

            return $pdf->render(); // Generate dan kembalikan PDF untuk diunduh
        }
    }

    // Renja Tahunan
    public function actionIndexLaporanRenjaTahunan($refperiode_id = null)
    {
        // Get refskpd_id from the current user
        $user = Yii::$app->user->identity;
        $refskpd_id = $user->refskpd_id;

        // Get the name of the SKPD based on refskpd_id
        $nama_skpd = SakipSkpd::find()->select('nama_skpd')->where(['refskpd_id' => $refskpd_id])->scalar();

        // Set default period to this year if not provided
        if ($refperiode_id === null) {
            $currentYear = date('Y');
            $defaultPeriod = SakipPeriode::find()->where(['periode' => $currentYear])->one();
            $refperiode_id = $defaultPeriod ? $defaultPeriod->refperiode_id : null;
        }

        // Fetch all periods
        $periodeList = SakipPeriode::find()->all();

        // Fetch data based on refskpd_id and refperiode_id
        $sasaranRenstra = SakipSasaranrenstra::find()
            ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
            ->with(['refVisi', 'refMisi', 'refTujuan', 'refSasaran'])
            ->all();

        // Retrieve the periode based on refperiode_id
        $selectedPeriod = SakipPeriode::find()->where(['refperiode_id' => $refperiode_id])->one();
        $selectedPeriodValue = $selectedPeriod ? $selectedPeriod->periode : null; // Get the periode value

        // Set the dynamic title
        $this->view->title = "Laporan Rencana Kinerja Tahun $selectedPeriodValue - " . Html::encode($nama_skpd);

        return $this->render('index-laporan-renja-tahunan', [
            'periodeList' => $periodeList,
            'selectedPeriodId' => $refperiode_id,
            'refskpd_id' => $refskpd_id,
            'refperiode_id' => $refperiode_id,
            'sasaranRenstra' => $sasaranRenstra,
            'nama_skpd' => $nama_skpd,
            'selectedPeriodValue' => $selectedPeriodValue, // Include selected period value
        ]);
    }

    public function actionIndexLaporanRenjaTahunanDev($refperiode_id = null, $refskpd_id = null)
    {
        // Ambil refskpd_id dari user saat ini jika tidak ada di request
        if ($refskpd_id === null) {
            $user = Yii::$app->user->identity;
            $refskpd_id = $user->refskpd_id;
        }

        // Get the name of the SKPD based on refskpd_id
        $nama_skpd = SakipSkpd::find()->select('nama_skpd')->where(['refskpd_id' => $refskpd_id])->scalar();

        // Set default period to this year if not provided
        if ($refperiode_id === null) {
            $currentYear = date('Y');
            $defaultPeriod = SakipPeriode::find()->where(['periode' => $currentYear])->one();
            $refperiode_id = $defaultPeriod ? $defaultPeriod->refperiode_id : null;
        }

        // Fetch all periods
        $periodeList = SakipPeriode::find()->all();

        // Fetch data based on refskpd_id and refperiode_id
        $sasaranRenstra = SakipSasaranrenstra::find()
            ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
            ->with(['refVisi', 'refMisi', 'refTujuan', 'refSasaran'])
            ->all();

        // Ambil daftar SKPD untuk dropdown
        $skpdList = ArrayHelper::map(SakipSkpd::find()->all(), 'refskpd_id', 'nama_skpd');

        // Retrieve the periode based on refperiode_id
        $selectedPeriod = SakipPeriode::find()->where(['refperiode_id' => $refperiode_id])->one();
        $selectedPeriodValue = $selectedPeriod ? $selectedPeriod->periode : null; // Get the periode value

        // Set the dynamic title
        $this->view->title = "Laporan Rencana Kinerja Tahun $selectedPeriodValue - " . Html::encode($nama_skpd);

        return $this->render('index-laporan-renja-tahunan-dev', [
            'periodeList' => $periodeList,
            'selectedPeriodId' => $refperiode_id,
            'refskpd_id' => $refskpd_id,
            'refperiode_id' => $refperiode_id,
            'sasaranRenstra' => $sasaranRenstra,
            'nama_skpd' => $nama_skpd,
            'selectedPeriodValue' => $selectedPeriodValue, // Include selected period value
            'selectedSkpdId' => $refskpd_id,
            'skpdList' => $skpdList,
        ]);
    }

    public function actionDownloadPdfRenja()
    {
        if (Yii::$app->request->isPost) {
            $content = Yii::$app->request->post('content'); // Konten dari POST
            $title = Yii::$app->request->post('title', 'Laporan'); // Judul untuk nama file

            // Define CSS untuk styling PDF
            $css = <<<CSS
            .tbdata {
                border-collapse: collapse;
                font-family: 'Public Sans', sans-serif;
                font-size: 11px;
            }
            .tbdata th {
                height: 40px;
                background: rgb(0, 160, 221);
                text-align: center;
                border: 1px solid #cfcfcf;
                color: #ffffff;
                padding: 2px;
            }
            .title {
        border-collapse: collapse;
        font-family: 'Public Sans', sans-serif;
        /* Menggunakan font Public Sans */
        font-size: 11px;
        text-align: center;
    }
            .tbdata td {
                padding: 2px;
                vertical-align: top;
            }
            .tebal {
                font-weight: bold;
            }
            .tblRenstra {
                width: 100%;
        font-family: 'Public Sans', sans-serif;
                border-collapse: collapse;
                font-size: 11px;
            }
            .tblRenstra td {
                padding: 2px;
                border: 1px solid #f2f2f2;
            }
            .tblRenstra .header {
                text-align: center;
                vertical-align: middle;
                font-weight: bold;
                background: #03b0e2;
                color: #ffffff;
            }
            .trO {
                background: #f2f2f2;
            }
            .trE {
                background: white;
            }
            .tblAtas {
                margin-left: 30px;
        font-family: 'Public Sans', sans-serif;
                border-collapse: collapse;
                font-size: 11px;
            }
            thead {
                display: table-header-group;
            }
CSS;

            // Setting untuk file PDF dengan CSS
            $pdf = new Pdf([
                'mode' => Pdf::MODE_CORE,
                'format' => Pdf::FORMAT_A4,
                'orientation' => Pdf::ORIENT_PORTRAIT,
                'destination' => Pdf::DEST_DOWNLOAD,
                'filename' => $title . '.pdf',
                'content' => $content, // Konten dari POST
                'cssInline' => $css, // Gunakan CSS inline
                'options' => ['title' => $title],
                'methods' => [
                    'SetHeader' => [$title],
                    'SetFooter' => ['{PAGENO}'],
                ],
            ]);

            return $pdf->render(); // Generate dan kembalikan PDF untuk diunduh
        }
    }

    // Laporan IKU
    public function actionIndexLaporanIku($refperiode_id = null)
    {
        // Get refskpd_id from the current user
        $user = Yii::$app->user->identity;
        $refskpd_id = $user->refskpd_id;

        // Get the name of the SKPD based on refskpd_id
        $nama_skpd = SakipSkpd::find()->select('nama_skpd')->where(['refskpd_id' => $refskpd_id])->scalar();

        // Set default period to this year if not provided
        if ($refperiode_id === null) {
            $currentYear = date('Y');
            $defaultPeriod = SakipPeriode::find()->where(['periode' => $currentYear])->one();
            $refperiode_id = $defaultPeriod ? $defaultPeriod->refperiode_id : null;
        }

        // Fetch all periods
        $periodeList = SakipPeriode::find()->all();

        // Fetch indicators based on refskpd_id and refperiode_id
        $indikators = SakipIndikatorsasaranrenstra::find()
            ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
            ->all();

        // Prepare the sasaranRenstra with their formulations
        $sasaranRenstra = [];
        foreach ($indikators as $indikator) {
            $sasaran = SakipSasaranrenstra::find()
                ->where(['refsasaranrenstra_id' => $indikator->refsasaranrenstra_id])
                ->one();

            if ($sasaran) {
                $sasaranRenstra[] = [
                    'indikator' => $indikator,
                    'formulasi' => $sasaran->formulasi_sasaranrenstra,
                ];
            }
        }

        // Retrieve the periode based on refperiode_id
        $selectedPeriod = SakipPeriode::find()->where(['refperiode_id' => $refperiode_id])->one();
        $selectedPeriodValue = $selectedPeriod ? $selectedPeriod->periode : null; // Get the periode value

        // Set the dynamic title
        $this->view->title = "Laporan Indikator Kinerja Utama $selectedPeriodValue - " . Html::encode($nama_skpd);

        return $this->render('index-laporan-iku', [
            'periodeList' => $periodeList,
            'selectedPeriodId' => $refperiode_id,
            'refskpd_id' => $refskpd_id,
            'refperiode_id' => $refperiode_id,
            'sasaranRenstra' => $sasaranRenstra,
            'nama_skpd' => $nama_skpd,
            'selectedPeriodValue' => $selectedPeriodValue, // Include selected period value
        ]);
    }

    public function actionIndexLaporanIkuDev($refperiode_id = null, $refskpd_id = null)
    {
        // Ambil refskpd_id dari user saat ini jika tidak ada di request
        if ($refskpd_id === null) {
            $user = Yii::$app->user->identity;
            $refskpd_id = $user->refskpd_id;
        }


        // Get the name of the SKPD based on refskpd_id
        $nama_skpd = SakipSkpd::find()->select('nama_skpd')->where(['refskpd_id' => $refskpd_id])->scalar();

        // Set default period to this year if not provided
        if ($refperiode_id === null) {
            $currentYear = date('Y');
            $defaultPeriod = SakipPeriode::find()->where(['periode' => $currentYear])->one();
            $refperiode_id = $defaultPeriod ? $defaultPeriod->refperiode_id : null;
        }

        // Fetch all periods
        $periodeList = SakipPeriode::find()->all();

        // Fetch indicators based on refskpd_id and refperiode_id
        $indikators = SakipIndikatorsasaranrenstra::find()
            ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
            ->all();

        // Prepare the sasaranRenstra with their formulations
        $sasaranRenstra = [];
        foreach ($indikators as $indikator) {
            $sasaran = SakipSasaranrenstra::find()
                ->where(['refsasaranrenstra_id' => $indikator->refsasaranrenstra_id])
                ->one();

            if ($sasaran) {
                $sasaranRenstra[] = [
                    'indikator' => $indikator,
                    'formulasi' => $sasaran->formulasi_sasaranrenstra,
                ];
            }
        }

        // Ambil daftar SKPD untuk dropdown
        $skpdList = ArrayHelper::map(SakipSkpd::find()->all(), 'refskpd_id', 'nama_skpd');

        // Retrieve the periode based on refperiode_id
        $selectedPeriod = SakipPeriode::find()->where(['refperiode_id' => $refperiode_id])->one();
        $selectedPeriodValue = $selectedPeriod ? $selectedPeriod->periode : null; // Get the periode value

        // Set the dynamic title
        $this->view->title = "Laporan Indikator Kinerja Utama $selectedPeriodValue - " . Html::encode($nama_skpd);

        return $this->render('index-laporan-iku-dev', [
            'periodeList' => $periodeList,
            'selectedPeriodId' => $refperiode_id,
            'refskpd_id' => $refskpd_id,
            'refperiode_id' => $refperiode_id,
            'sasaranRenstra' => $sasaranRenstra,
            'nama_skpd' => $nama_skpd,
            'selectedPeriodValue' => $selectedPeriodValue, // Include selected period value
            'selectedSkpdId' => $refskpd_id,
            'skpdList' => $skpdList,
        ]);
    }

    public function actionDownloadPdfIku()
    {
        if (Yii::$app->request->isPost) {
            $content = Yii::$app->request->post('content'); // Konten dari POST
            $title = Yii::$app->request->post('title', 'Laporan'); // Judul untuk nama file

            // Define CSS untuk styling PDF
            $css = <<<CSS
                .tbdata {
                    border-collapse: collapse;
                    font-family: 'Public Sans', sans-serif;
                    font-size: 11px;
                }
                .tbdata th {
                    height: 40px;
                    background: rgb(0, 160, 221);
                    text-align: center;
                    border: 1px solid #cfcfcf;
                    color: #ffffff;
                    padding: 2px;
                }
                .title {
            border-collapse: collapse;
            font-family: 'Public Sans', sans-serif;
            /* Menggunakan font Public Sans */
            font-size: 11px;
            text-align: center;
        }
                .tbdata td {
                    padding: 2px;
                    vertical-align: top;
                }
                .tebal {
                    font-weight: bold;
                }
                .tblRenstra {
                    width: 100%;
            font-family: 'Public Sans', sans-serif;
                    border-collapse: collapse;
                    font-size: 11px;
                }
                .tblRenstra td {
                    padding: 2px;
                    border: 1px solid #f2f2f2;
                }
                .tblRenstra .header {
                    text-align: center;
                    vertical-align: middle;
                    font-weight: bold;
                    background: #03b0e2;
                    color: #ffffff;
                }
                .trO {
                    background: #f2f2f2;
                }
                .trE {
                    background: white;
                }
                .tblAtas {
                    margin-left: 30px;
            font-family: 'Public Sans', sans-serif;
                    border-collapse: collapse;
                    font-size: 11px;
                }
                thead {
                    display: table-header-group;
                }
    CSS;

            // Setting untuk file PDF dengan CSS
            $pdf = new Pdf([
                'mode' => Pdf::MODE_CORE,
                'format' => Pdf::FORMAT_A4,
                'orientation' => Pdf::ORIENT_PORTRAIT,
                'destination' => Pdf::DEST_DOWNLOAD,
                'filename' => $title . '.pdf',
                'content' => $content, // Konten dari POST
                'cssInline' => $css, // Gunakan CSS inline
                'options' => ['title' => $title],
                'methods' => [
                    'SetHeader' => [$title],
                    'SetFooter' => ['{PAGENO}'],
                ],
            ]);

            return $pdf->render(); // Generate dan kembalikan PDF untuk diunduh
        }
    }

    // Laporan IKU
    public function actionIndexLaporanTapkin($refperiode_id = null, $refpenjabatskpd_id = null)
    {
        // Get refskpd_id from the current user
        $user = Yii::$app->user->identity;
        $refskpd_id = $user->refskpd_id;

        // Get the name of the SKPD based on refskpd_id
        $nama_skpd = SakipSkpd::find()->select('nama_skpd')->where(['refskpd_id' => $refskpd_id])->scalar();

        // Fetch the head of SKPD information
        $skpdHead = SakipSkpd::find()->where(['refskpd_id' => $refskpd_id])->one();

        // Set default period to this year if not provided
        if ($refperiode_id === null) {
            $currentYear = date('Y');
            $defaultPeriod = SakipPeriode::find()->where(['periode' => $currentYear])->one();
            $refperiode_id = $defaultPeriod ? $defaultPeriod->refperiode_id : null;
        }

        // Fetch the leadership information for the selected period
        if ($refperiode_id !== null) {
            $leadership = SakipPimpinan::find()->where(['refperiode_id' => $refperiode_id])->one();
        } else {
            // If refperiode_id is null, get the leadership data for the current year
            $currentYear = date('Y');
            $defaultPeriod = SakipPeriode::find()->where(['periode' => $currentYear])->one();
            if ($defaultPeriod) {
                $leadership = SakipPimpinan::find()->where(['refperiode_id' => $defaultPeriod->refperiode_id])->one();
            } else {
                $leadership = null; // Handle case where there is no current year period
            }
        }

        // Fetch the selected penjabat SKPD
        $penjabatSkpd = null;
        $refeselonId = null;
        if ($refpenjabatskpd_id) {
            $penjabatSkpd = SakipPenjabatSkpd::find()->where(['refpenjabatskpd_id' => $refpenjabatskpd_id])->one();
            $refeselonId = $penjabatSkpd ? $penjabatSkpd->refeselon_id : null;
        }

        // Fetch all periods
        $periodeList = SakipPeriode::find()->all();

        $penjabatSkpdList = SakipPenjabatSkpd::find()
            ->where(['refskpd_id' => $refskpd_id])
            ->andFilterWhere(['refperiode_id' => $refperiode_id])
            ->all();

        $sasaranRenstra = SakipSasaranRenstra::find()
            ->where(['refskpd_id' => $refskpd_id])
            ->andFilterWhere(['refperiode_id' => $refperiode_id])
            ->all();

        $indikators = SakipIndikatorsasaranrenstra::find()
            ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
            ->all();

        // Retrieve programs for the refskpd_id and refperiode_id
        $programs = SakipCascadingprogram::find()
            ->where(['refskpd_id' => $refskpd_id])
            ->andFilterWhere(['refperiode_id' => $refperiode_id])
            ->all();

        // Fetch cascading program
        $penjabatskpdCascadingProgram = SakipPenjabatskpdCascadingprogram::find()
            ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
            ->andWhere(['refpenjabatskpd_id' => $refpenjabatskpd_id]) // Tambahkan filter refpenjabatskpd_id
            ->all();

        // Fetch cascading kegiatan
        $penjabatskpdCascadingKegiatan = SakipPenjabatskpdCascadingkegiatan::find()
            ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
            ->andWhere(['refpenjabatskpd_id' => $refpenjabatskpd_id]) // Tambahkan filter refpenjabatskpd_id
            ->all();

        // Fetch cascading sub-kegiatan
        $penjabatskpdCascadingSubkegiatan = SakipPenjabatskpdCascadingsubkegiatan::find()
            ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
            ->andWhere(['refpenjabatskpd_id' => $refpenjabatskpd_id]) // Tambahkan filter refpenjabatskpd_id
            ->all();

        $indikatorCascadingSubkegiatan = SakipIndikatorcascadingsubkegiatan::find()
            ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
            ->all();


        // Retrieve the periode based on refperiode_id
        $selectedPeriod = SakipPeriode::find()->where(['refperiode_id' => $refperiode_id])->one();
        $selectedPeriodValue = $selectedPeriod ? $selectedPeriod->periode : null; // Get the periode value

        // Set the dynamic title
        $this->view->title = "Laporan Perjanjian Kinerja $selectedPeriodValue - " . Html::encode($nama_skpd);

        return $this->render('index-laporan-tapkin', [
            'periodeList' => $periodeList,
            'penjabatSkpdList' => $penjabatSkpdList,
            'selectedPeriodId' => $refperiode_id,
            'refskpd_id' => $refskpd_id,
            'refperiode_id' => $refperiode_id,
            'refpenjabatskpd_id' => $refpenjabatskpd_id,
            'sasaranRenstra' => $sasaranRenstra,
            'indikators' => $indikators,
            'nama_skpd' => $nama_skpd,
            'selectedPeriodValue' => $selectedPeriodValue, // Include selected period value
            'skpdHead' => $skpdHead,
            'penjabatSkpd' => $penjabatSkpd,
            'penjabatskpdCascadingProgram' => $penjabatskpdCascadingProgram,
            'penjabatskpdCascadingKegiatan' => $penjabatskpdCascadingKegiatan,
            'penjabatskpdCascadingSubkegiatan' => $penjabatskpdCascadingSubkegiatan,
            'indikatorCascadingSubkegiatan' => $indikatorCascadingSubkegiatan,
            'refeselonId' => $refeselonId, // Pass refeselon_id to the view
            'leadership' => $leadership, // Pass leadership data to the view
            'programs' => $programs, // Pass programs to the view
        ]);
    }

    // Laporan IKU
    public function actionIndexLaporanTapkinDev($refperiode_id = null, $refpenjabatskpd_id = null, $refskpd_id = null)
    {
        // Ambil refskpd_id dari user saat ini jika tidak ada di request
        if ($refskpd_id === null) {
            $user = Yii::$app->user->identity;
            $refskpd_id = $user->refskpd_id;
        }

        // Get the name of the SKPD based on refskpd_id
        $nama_skpd = SakipSkpd::find()->select('nama_skpd')->where(['refskpd_id' => $refskpd_id])->scalar();

        // Fetch the head of SKPD information
        $skpdHead = SakipSkpd::find()->where(['refskpd_id' => $refskpd_id])->one();

        // Set default period to this year if not provided
        if ($refperiode_id === null) {
            $currentYear = date('Y');
            $defaultPeriod = SakipPeriode::find()->where(['periode' => $currentYear])->one();
            $refperiode_id = $defaultPeriod ? $defaultPeriod->refperiode_id : null;
        }

        // Fetch the leadership information for the selected period
        if ($refperiode_id !== null) {
            $leadership = SakipPimpinan::find()->where(['refperiode_id' => $refperiode_id])->one();
        } else {
            // If refperiode_id is null, get the leadership data for the current year
            $currentYear = date('Y');
            $defaultPeriod = SakipPeriode::find()->where(['periode' => $currentYear])->one();
            if ($defaultPeriod) {
                $leadership = SakipPimpinan::find()->where(['refperiode_id' => $defaultPeriod->refperiode_id])->one();
            } else {
                $leadership = null; // Handle case where there is no current year period
            }
        }

        // Fetch the selected penjabat SKPD
        $penjabatSkpd = null;
        $refeselonId = null;
        if ($refpenjabatskpd_id) {
            $penjabatSkpd = SakipPenjabatSkpd::find()->where(['refpenjabatskpd_id' => $refpenjabatskpd_id])->one();
            $refeselonId = $penjabatSkpd ? $penjabatSkpd->refeselon_id : null;
        }

        // Fetch all periods
        $periodeList = SakipPeriode::find()->all();

        $penjabatSkpdList = SakipPenjabatSkpd::find()
            ->where(['refskpd_id' => $refskpd_id])
            ->andFilterWhere(['refperiode_id' => $refperiode_id])
            ->all();

        $sasaranRenstra = SakipSasaranRenstra::find()
            ->where(['refskpd_id' => $refskpd_id])
            ->andFilterWhere(['refperiode_id' => $refperiode_id])
            ->all();

        $indikators = SakipIndikatorsasaranrenstra::find()
            ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
            ->all();

        // Retrieve programs for the refskpd_id and refperiode_id
        $programs = SakipCascadingprogram::find()
            ->where(['refskpd_id' => $refskpd_id])
            ->andFilterWhere(['refperiode_id' => $refperiode_id])
            ->all();

        // Fetch cascading program
        $penjabatskpdCascadingProgram = SakipPenjabatskpdCascadingprogram::find()
            ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
            ->andWhere(['refpenjabatskpd_id' => $refpenjabatskpd_id]) // Tambahkan filter refpenjabatskpd_id
            ->all();

        // Fetch cascading kegiatan
        $penjabatskpdCascadingKegiatan = SakipPenjabatskpdCascadingkegiatan::find()
            ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
            ->andWhere(['refpenjabatskpd_id' => $refpenjabatskpd_id]) // Tambahkan filter refpenjabatskpd_id
            ->all();

        // Fetch cascading sub-kegiatan
        $penjabatskpdCascadingSubkegiatan = SakipPenjabatskpdCascadingsubkegiatan::find()
            ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
            ->andWhere(['refpenjabatskpd_id' => $refpenjabatskpd_id]) // Tambahkan filter refpenjabatskpd_id
            ->all();

        $indikatorCascadingSubkegiatan = SakipIndikatorcascadingsubkegiatan::find()
            ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
            ->all();

        // Ambil daftar SKPD untuk dropdown
        $skpdList = ArrayHelper::map(SakipSkpd::find()->all(), 'refskpd_id', 'nama_skpd');

        // Retrieve the periode based on refperiode_id
        $selectedPeriod = SakipPeriode::find()->where(['refperiode_id' => $refperiode_id])->one();
        $selectedPeriodValue = $selectedPeriod ? $selectedPeriod->periode : null; // Get the periode value

        // Set the dynamic title
        $this->view->title = "Laporan Perjanjian Kinerja $selectedPeriodValue - " . Html::encode($nama_skpd);

        return $this->render('index-laporan-tapkin-dev', [
            'periodeList' => $periodeList,
            'penjabatSkpdList' => $penjabatSkpdList,
            'selectedPeriodId' => $refperiode_id,
            'refskpd_id' => $refskpd_id,
            'refperiode_id' => $refperiode_id,
            'refpenjabatskpd_id' => $refpenjabatskpd_id,
            'sasaranRenstra' => $sasaranRenstra,
            'indikators' => $indikators,
            'nama_skpd' => $nama_skpd,
            'selectedPeriodValue' => $selectedPeriodValue, // Include selected period value
            'skpdHead' => $skpdHead,
            'penjabatSkpd' => $penjabatSkpd,
            'penjabatskpdCascadingProgram' => $penjabatskpdCascadingProgram,
            'penjabatskpdCascadingKegiatan' => $penjabatskpdCascadingKegiatan,
            'penjabatskpdCascadingSubkegiatan' => $penjabatskpdCascadingSubkegiatan,
            'indikatorCascadingSubkegiatan' => $indikatorCascadingSubkegiatan,
            'refeselonId' => $refeselonId, // Pass refeselon_id to the view
            'leadership' => $leadership, // Pass leadership data to the view
            'programs' => $programs, // Pass programs to the view
            'selectedSkpdId' => $refskpd_id,
            'skpdList' => $skpdList,
        ]);
    }

    public function actionDownloadPdfTapkin()
    {
        if (Yii::$app->request->isPost) {
            $content = Yii::$app->request->post('content'); // Konten dari POST
            $title = Yii::$app->request->post('title', 'Laporan'); // Judul untuk nama file

            // Define CSS untuk styling PDF
            $css = <<<CSS
    .title {
        border-collapse: collapse;
        font-family: 'Public Sans', sans-serif;
        /* Menggunakan font Public Sans */
        font-size: 11px;
        text-align: center;
    }

    #halamanlaporan {
        font-family: 'Public Sans', sans-serif;
        font-size: 0.35cm;
        width: 600px;
        margin: auto;
    }

    #halamanlaporan .isilaporan {
        font-size: 0.35cm;
        line-height: 1.3;
    }

    #halamanlaporan .isilaporan h3 {
        font-weight: normal;
        font-size: 0.35cm;
        text-align: center;
        margin-bottom: 20px;
    }

    #halamanlaporan .isilaporan h4 {
        font-size: 0.35cm;
        font-weight: bold;
        text-align: center;
    }

    #halamanlaporan .isilaporan h5 {
        font-size: 0.35cm;
        font-weight: bold;
        text-align: center;
        margin-top: 20px;
        margin-bottom: 20px;
    }

    #halamanlaporan .isilaporan p {
        text-align: justify;
    }

    #halamanlaporan .tblPihak {
        width: 100%;
        font-size: 0.35cm;
    }

    #halamanlaporan .tblPihak td {
        width: 50%;
        text-align: center;
    }

    #halamanlaporan .tbdata {
        width: 100%;
        font-size: 0.35cm;
        margin-top: 20px;
        border-collapse: collapse;
    }

    #halamanlaporan .tbdata th {
        padding: 5px;
        text-align: center;
    }

    #halamanlaporan .tbdata td {
        padding-left: 3px;
        padding-right: 3px;
        vertical-align: top;
    }

    #halamanlaporan .tengah {
        text-align: center;
    }

    #halamanlaporan .kanan {
        text-align: right;
    }
    CSS;

            // Setting untuk file PDF dengan CSS
            $pdf = new Pdf([
                'mode' => Pdf::MODE_CORE,
                'format' => Pdf::FORMAT_A4,
                'orientation' => Pdf::ORIENT_PORTRAIT,
                'destination' => Pdf::DEST_DOWNLOAD,
                'filename' => $title . '.pdf',
                'content' => $content, // Konten dari POST
                'cssInline' => $css, // Gunakan CSS inline
                'options' => ['title' => $title],
                'methods' => [
                    'SetHeader' => [$title],
                    'SetFooter' => ['{PAGENO}'],
                ],
            ]);

            return $pdf->render(); // Generate dan kembalikan PDF untuk diunduh
        }
    }

    // Capkin IKu
    public function actionIndexLaporanCapkinIku($refperiode_id = null)
    {
        // Get refskpd_id from the current user
        $user = Yii::$app->user->identity;
        $refskpd_id = $user->refskpd_id;

        // Get the name of the SKPD based on refskpd_id
        $nama_skpd = SakipSkpd::find()->select('nama_skpd')->where(['refskpd_id' => $refskpd_id])->scalar();

        // Set default period to this year if not provided
        if ($refperiode_id === null) {
            $currentYear = date('Y');
            $defaultPeriod = SakipPeriode::find()->where(['periode' => $currentYear])->one();
            $refperiode_id = $defaultPeriod ? $defaultPeriod->refperiode_id : null;
        }

        // Fetch all periods
        $periodeList = SakipPeriode::find()->all();

        // Fetch sasaran renstra with related indicators
        $indikators = SakipIndikatorsasaranrenstra::find()
            ->where(['refskpd_id' => $refskpd_id])
            ->andWhere(['refperiode_id' => $refperiode_id])
            ->with('refSasaranrenstra') // Eager load related sasaran renstra
            ->all();

        // Retrieve the periode based on refperiode_id
        $selectedPeriod = SakipPeriode::find()->where(['refperiode_id' => $refperiode_id])->one();
        $selectedPeriodValue = $selectedPeriod ? $selectedPeriod->periode : null; // Get the periode value

        // Set the dynamic title
        $this->view->title = "Laporan Rencana Kinerja Tahun $selectedPeriodValue - " . Html::encode($nama_skpd);

        return $this->render('index-laporan-capkin-iku', [
            'periodeList' => $periodeList,
            'selectedPeriodId' => $refperiode_id,
            'refskpd_id' => $refskpd_id,
            'refperiode_id' => $refperiode_id,
            'indikators' => $indikators,
            'nama_skpd' => $nama_skpd,
            'selectedPeriodValue' => $selectedPeriodValue, // Include selected period value
        ]);
    }

    // Capkin IKu
    public function actionIndexLaporanCapkinIkuDev($refperiode_id = null, $refskpd_id = null)
    {
        // Ambil refskpd_id dari user saat ini jika tidak ada di request
        if ($refskpd_id === null) {
            $user = Yii::$app->user->identity;
            $refskpd_id = $user->refskpd_id;
        }

        // Get the name of the SKPD based on refskpd_id
        $nama_skpd = SakipSkpd::find()->select('nama_skpd')->where(['refskpd_id' => $refskpd_id])->scalar();

        // Set default period to this year if not provided
        if ($refperiode_id === null) {
            $currentYear = date('Y');
            $defaultPeriod = SakipPeriode::find()->where(['periode' => $currentYear])->one();
            $refperiode_id = $defaultPeriod ? $defaultPeriod->refperiode_id : null;
        }

        // Fetch all periods
        $periodeList = SakipPeriode::find()->all();

        // Fetch sasaran renstra with related indicators
        $indikators = SakipIndikatorsasaranrenstra::find()
            ->where(['refskpd_id' => $refskpd_id])
            ->andWhere(['refperiode_id' => $refperiode_id])
            ->with('refSasaranrenstra') // Eager load related sasaran renstra
            ->all();

        // Ambil daftar SKPD untuk dropdown
        $skpdList = ArrayHelper::map(SakipSkpd::find()->all(), 'refskpd_id', 'nama_skpd');

        // Retrieve the periode based on refperiode_id
        $selectedPeriod = SakipPeriode::find()->where(['refperiode_id' => $refperiode_id])->one();
        $selectedPeriodValue = $selectedPeriod ? $selectedPeriod->periode : null; // Get the periode value

        // Set the dynamic title
        $this->view->title = "Laporan Rencana Kinerja Tahun $selectedPeriodValue - " . Html::encode($nama_skpd);

        return $this->render('index-laporan-capkin-iku-dev', [
            'periodeList' => $periodeList,
            'selectedPeriodId' => $refperiode_id,
            'refskpd_id' => $refskpd_id,
            'refperiode_id' => $refperiode_id,
            'indikators' => $indikators,
            'nama_skpd' => $nama_skpd,
            'selectedPeriodValue' => $selectedPeriodValue, // Include selected period value
            'selectedSkpdId' => $refskpd_id,
            'skpdList' => $skpdList,
        ]);
    }

    public function actionDownloadPdfCapkinIku()
    {
        if (Yii::$app->request->isPost) {
            $content = Yii::$app->request->post('content'); // Konten dari POST
            $title = Yii::$app->request->post('title', 'Laporan'); // Judul untuk nama file

            // Define CSS untuk styling PDF
            $css = <<<CSS
                    .title {
        border-collapse: collapse;
        font-family: 'Public Sans', sans-serif;
        /* Menggunakan font Public Sans */
        font-size: 11px;
        text-align: center;
    }

    .tbdata {
        border-collapse: collapse;
        font-family: 'Public Sans', sans-serif;
        font-size: 11px;
    }

    .tbdata th {
        height: 40px;
        background: rgb(0, 160, 221);
        text-align: center;
        border: 1px solid #cfcfcf;
        color: #ffffff;
        padding: 2px;
    }

    .tbdata td {
        padding: 2px;
        border: 1px solid #cfcfcf;
        vertical-align: top;
    }

    .tengah {
        text-align: center;
    }

    thead {
        display: table-header-group;
    }

    .keterangan {
        border-collapse: collapse;
    }

    .keterangan td {
        padding: 5px;
        border: 1px solid #e3e3e3;
    }

    .merah {
        background: #ff0404;
        color: white;
    }

    .hijau {
        background: #006600;
        color: white;
    }

    .biru {
        background: #000266;
        color: white;
    }

    .abu {
        background: #95a5a6;
        color: white;
    }
    CSS;

            // Setting untuk file PDF dengan CSS
            $pdf = new Pdf([
                'mode' => Pdf::MODE_CORE,
                'format' => Pdf::FORMAT_A4,
                'orientation' => Pdf::ORIENT_PORTRAIT,
                'destination' => Pdf::DEST_DOWNLOAD,
                'filename' => $title . '.pdf',
                'content' => $content, // Konten dari POST
                'cssInline' => $css, // Gunakan CSS inline
                'options' => ['title' => $title],
                'methods' => [
                    'SetHeader' => [$title],
                    'SetFooter' => ['{PAGENO}'],
                ],
            ]);

            return $pdf->render(); // Generate dan kembalikan PDF untuk diunduh
        }
    }

    public function actionIndexLaporanRealisasiAnggaran($refperiode_id = null)
    {
        // Get refskpd_id from the current user
        $user = Yii::$app->user->identity;
        $refskpd_id = $user->refskpd_id;

        // Get the name of the SKPD based on refskpd_id
        $nama_skpd = SakipSkpd::find()->select('nama_skpd')->where(['refskpd_id' => $refskpd_id])->scalar();

        // Set default period to this year if not provided
        if ($refperiode_id === null) {
            $currentYear = date('Y');
            $defaultPeriod = SakipPeriode::find()->where(['periode' => $currentYear])->one();
            $refperiode_id = $defaultPeriod ? $defaultPeriod->refperiode_id : null;
        }

        // Fetch all periods
        $periodeList = SakipPeriode::find()->all();

        // Fetch main indicator data with related programs and SKPD info
        $indikators = SakipIndikatorcascadingsubkegiatan::find()
            ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
            ->with(['refCascadingProgram', 'refProgram', 'refSasaranrenstra'])
            ->all();

        // Fetch quarterly data
        $quarterlyData = SakipIndikatorcascadingsubkegiatanTriwulan::find()
            ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
            ->all();

        // Organize data for the view
        $data = [];
        foreach ($indikators as $indikator) {
            $sasaranId = $indikator->refsasaranrenstra_id;
            if (!isset($data[$sasaranId])) {
                $data[$sasaranId] = [
                    'uraian_sasaran' => $indikator->refSasaranrenstra->uraian_sasaranrenstra,
                    'programs' => [],
                    'total_anggaran' => 0,
                    'total_quarterly_penyerapan_anggaran' => [0, 0, 0, 0],
                    'total_quarterly_realisasi' => [0, 0, 0, 0]
                ];
            }

            // Add program details
            $programId = $indikator->refcascadingprogram_id;
            if (!isset($data[$sasaranId]['programs'][$programId])) {
                $data[$sasaranId]['programs'][$programId] = [
                    'nama_program' => $indikator->refProgram->nama_program,
                    'anggaran_pk_p' => $indikator->anggaran_pk_p,
                    'quarterly' => []
                ];
                $data[$sasaranId]['total_anggaran'] += (float) $indikator->anggaran_pk_p;
            }
        }

        // Populate quarterly data
        foreach ($quarterlyData as $quarter) {
            $sasaranId = $quarter->refsasaranrenstra_id;
            $programId = $quarter->refcascadingprogram_id;

            if (isset($data[$sasaranId]['programs'][$programId])) {
                $quarterIndex = $quarter->reftriwulan_id - 1; // Assuming reftriwulan_id is 1 to 4 for quarters
                $data[$sasaranId]['programs'][$programId]['quarterly'][$quarterIndex] = [
                    'triwulan_target_rkt' => $quarter->triwulan_target_rkt,
                    'triwulan_realisasi' => $quarter->triwulan_realisasi,
                    'triwulan_penyerapan_anggaran' => $quarter->triwulan_penyerapan_anggaran
                ];

                // Sum totals by quarter
                $data[$sasaranId]['total_quarterly_penyerapan_anggaran'][$quarterIndex] += $quarter->triwulan_penyerapan_anggaran;
                $data[$sasaranId]['total_quarterly_realisasi'][$quarterIndex] += $quarter->triwulan_realisasi;
            }
        }

        // Retrieve the periode based on refperiode_id
        $selectedPeriod = SakipPeriode::find()->where(['refperiode_id' => $refperiode_id])->one();
        $selectedPeriodValue = $selectedPeriod ? $selectedPeriod->periode : null; // Get the periode value

        // Set the dynamic title
        $this->view->title = "Laporan Rencana Kinerja Tahun $selectedPeriodValue - " . Html::encode($nama_skpd);

        return $this->render('index-laporan-realisasi-anggaran', [
            'periodeList' => $periodeList,
            'selectedPeriodId' => $refperiode_id,
            'refskpd_id' => $refskpd_id,
            'refperiode_id' => $refperiode_id,
            'indikators' => $indikators,
            'data' => $data,
            'nama_skpd' => $nama_skpd,
            'selectedPeriodValue' => $selectedPeriodValue, // Include selected period value
        ]);
    }

    public function actionIndexLaporanRealisasiAnggaranDev($refperiode_id = null, $refskpd_id = null)
    {
        // Ambil refskpd_id dari user saat ini jika tidak ada di request
        if ($refskpd_id === null) {
            $user = Yii::$app->user->identity;
            $refskpd_id = $user->refskpd_id;
        }

        // Get the name of the SKPD based on refskpd_id
        $nama_skpd = SakipSkpd::find()->select('nama_skpd')->where(['refskpd_id' => $refskpd_id])->scalar();

        // Set default period to this year if not provided
        if ($refperiode_id === null) {
            $currentYear = date('Y');
            $defaultPeriod = SakipPeriode::find()->where(['periode' => $currentYear])->one();
            $refperiode_id = $defaultPeriod ? $defaultPeriod->refperiode_id : null;
        }

        // Fetch all periods
        $periodeList = SakipPeriode::find()->all();

        // Fetch main indicator data with related programs and SKPD info
        $indikators = SakipIndikatorcascadingsubkegiatan::find()
            ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
            ->with(['refCascadingProgram', 'refProgram', 'refSasaranrenstra'])
            ->all();

        // Fetch quarterly data
        $quarterlyData = SakipIndikatorcascadingsubkegiatanTriwulan::find()
            ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
            ->all();

        // Organize data for the view
        $data = [];
        foreach ($indikators as $indikator) {
            $sasaranId = $indikator->refsasaranrenstra_id;
            if (!isset($data[$sasaranId])) {
                $data[$sasaranId] = [
                    'uraian_sasaran' => $indikator->refSasaranrenstra->uraian_sasaranrenstra,
                    'programs' => [],
                    'total_anggaran' => 0,
                    'total_quarterly_penyerapan_anggaran' => [0, 0, 0, 0],
                    'total_quarterly_realisasi' => [0, 0, 0, 0]
                ];
            }

            // Add program details
            $programId = $indikator->refcascadingprogram_id;
            if (!isset($data[$sasaranId]['programs'][$programId])) {
                $data[$sasaranId]['programs'][$programId] = [
                    'nama_program' => $indikator->refProgram->nama_program,
                    'anggaran_pk_p' => $indikator->anggaran_pk_p,
                    'quarterly' => []
                ];
                $data[$sasaranId]['total_anggaran'] += (float) $indikator->anggaran_pk_p;
            }
        }

        // Populate quarterly data
        foreach ($quarterlyData as $quarter) {
            $sasaranId = $quarter->refsasaranrenstra_id;
            $programId = $quarter->refcascadingprogram_id;

            if (isset($data[$sasaranId]['programs'][$programId])) {
                $quarterIndex = $quarter->reftriwulan_id - 1; // Assuming reftriwulan_id is 1 to 4 for quarters
                $data[$sasaranId]['programs'][$programId]['quarterly'][$quarterIndex] = [
                    'triwulan_target_rkt' => $quarter->triwulan_target_rkt,
                    'triwulan_realisasi' => $quarter->triwulan_realisasi,
                    'triwulan_penyerapan_anggaran' => $quarter->triwulan_penyerapan_anggaran
                ];

                // Sum totals by quarter
                $data[$sasaranId]['total_quarterly_penyerapan_anggaran'][$quarterIndex] += $quarter->triwulan_penyerapan_anggaran;
                $data[$sasaranId]['total_quarterly_realisasi'][$quarterIndex] += $quarter->triwulan_realisasi;
            }
        }

        // Ambil daftar SKPD untuk dropdown
        $skpdList = ArrayHelper::map(SakipSkpd::find()->all(), 'refskpd_id', 'nama_skpd');

        // Retrieve the periode based on refperiode_id
        $selectedPeriod = SakipPeriode::find()->where(['refperiode_id' => $refperiode_id])->one();
        $selectedPeriodValue = $selectedPeriod ? $selectedPeriod->periode : null; // Get the periode value

        // Set the dynamic title
        $this->view->title = "Laporan Rencana Kinerja Tahun $selectedPeriodValue - " . Html::encode($nama_skpd);

        return $this->render('index-laporan-realisasi-anggaran-dev', [
            'periodeList' => $periodeList,
            'selectedPeriodId' => $refperiode_id,
            'refskpd_id' => $refskpd_id,
            'refperiode_id' => $refperiode_id,
            'indikators' => $indikators,
            'data' => $data,
            'nama_skpd' => $nama_skpd,
            'selectedPeriodValue' => $selectedPeriodValue, // Include selected period value
            'selectedSkpdId' => $refskpd_id,
            'skpdList' => $skpdList,
        ]);
    }

    public function actionDownloadPdfRealisasiAnggaran()
    {
        if (Yii::$app->request->isPost) {
            $content = Yii::$app->request->post('content'); // Konten dari POST
            $title = Yii::$app->request->post('title', 'Laporan'); // Judul untuk nama file

            // Define CSS untuk styling PDF
            $css = <<<CSS
                    .title {
        border-collapse: collapse;
        font-family: 'Public Sans', sans-serif;
        /* Menggunakan font Public Sans */
        font-size: 11px;
        text-align: center;
    }

    .tbdata {
        border-collapse: collapse;
        font-family: "Bookman Old Style", "Verdana";
        font-size: 11px;
    }

    .tbdata th {
        height: 40px;
        background: rgb(0, 160, 221);
        text-align: center;
        border: 0.2px solid #cfcfcf;
        color: #ffffff;
        padding: 2px;
    }

    .tbdata td {
        padding: 2px;
        border: 0.2px solid #cfcfcf;
        vertical-align: top;
    }

    .tengah {
        text-align: center;
    }

    thead {
        display: table-header-group;
    }
    CSS;

            // Setting untuk file PDF dengan CSS
            $pdf = new Pdf([
                'mode' => Pdf::MODE_CORE,
                'format' => Pdf::FORMAT_A4,
                'orientation' => Pdf::ORIENT_PORTRAIT,
                'destination' => Pdf::DEST_DOWNLOAD,
                'filename' => $title . '.pdf',
                'content' => $content, // Konten dari POST
                'cssInline' => $css, // Gunakan CSS inline
                'options' => ['title' => $title],
                'methods' => [
                    'SetHeader' => [$title],
                    'SetFooter' => ['{PAGENO}'],
                ],
            ]);

            return $pdf->render(); // Generate dan kembalikan PDF untuk diunduh
        }
    }

    public function actionIndexLaporanRencanaAksi($refperiode_id = null)
    {
        // Get refskpd_id from the current user
        $user = Yii::$app->user->identity;
        $refskpd_id = $user->refskpd_id;

        // Get the name of the SKPD based on refskpd_id
        $nama_skpd = SakipSkpd::find()->select('nama_skpd')->where(['refskpd_id' => $refskpd_id])->scalar();

        // Set default period to this year if not provided
        if ($refperiode_id === null) {
            $currentYear = date('Y');
            $defaultPeriod = SakipPeriode::find()->where(['periode' => $currentYear])->one();
            $refperiode_id = $defaultPeriod ? $defaultPeriod->refperiode_id : null;
        }

        // Fetch all periods
        $periodeList = SakipPeriode::find()->all();

        // Fetch cascadingSubkegiatan along with associated data
        $cascadingSubkegiatan = SakipCascadingsubkegiatan::find()
            ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
            ->with([
                'refIndikatorcascadingsubkegiatan',
                'indikatorTriwulan' => function ($query) use ($refperiode_id, $refskpd_id) {
                    $query->andWhere(['refperiode_id' => $refperiode_id, 'refskpd_id' => $refskpd_id]);
                },
                'refCascadingKegiatan.refKegiatan',  // Related Kegiatan data
                'refCascadingKegiatan.refCascadingProgram.refProgram'  // Fetch Program info
            ])
            ->all();

        // Group the cascadingSubkegiatan by refcascadingkegiatan_id
        $groupedSubkegiatan = [];
        foreach ($cascadingSubkegiatan as $item) {
            $groupedSubkegiatan[$item->refCascadingKegiatan->refCascadingProgram->refProgram->refprogram_id][] = $item;
        }


        // Calculate total anggaran for each refcascadingkegiatan_id
        $anggaranPerKegiatan = [];
        foreach ($groupedSubkegiatan as $refcascadingkegiatan_id => $subkegiatanGroup) {
            $totalAnggaran = array_sum(array_column($subkegiatanGroup, 'subkegiatan_anggaran'));
            $anggaranPerKegiatan[$refcascadingkegiatan_id] = $totalAnggaran;
        }

        // Fetch anggaran per cascading program to display totals in the view
        $anggaranPerProgram = [];
        foreach ($cascadingSubkegiatan as $subkegiatan) {
            $programId = $subkegiatan->refCascadingKegiatan->refCascadingProgram->refProgram->refprogram_id;
            if (!isset($anggaranPerProgram[$programId])) {
                $anggaranPerProgram[$programId] = 0;
            }
            $anggaranPerProgram[$programId] += $subkegiatan->subkegiatan_anggaran;
        }


        // Retrieve the periode based on refperiode_id
        $selectedPeriod = SakipPeriode::find()->where(['refperiode_id' => $refperiode_id])->one();
        $selectedPeriodValue = $selectedPeriod ? $selectedPeriod->periode : null; // Get the periode value

        // Set the dynamic title
        $this->view->title = "Laporan Rencana Kinerja Tahun $selectedPeriodValue - " . Html::encode($nama_skpd);

        return $this->render('index-laporan-rencana-aksi', [
            'periodeList' => $periodeList,
            'selectedPeriodId' => $refperiode_id,
            'refskpd_id' => $refskpd_id,
            'refperiode_id' => $refperiode_id,
            'cascadingSubkegiatan' => $cascadingSubkegiatan,
            'groupedSubkegiatan' => $groupedSubkegiatan,
            'anggaranPerKegiatan' => $anggaranPerKegiatan,
            'anggaranPerProgram' => $anggaranPerProgram,
            'nama_skpd' => $nama_skpd,
            'selectedPeriodValue' => $selectedPeriodValue, // Include selected period value
        ]);
    }

    public function actionIndexLaporanRencanaAksiDev($refperiode_id = null, $refskpd_id = null)
    {
        // Ambil refskpd_id dari user saat ini jika tidak ada di request
        if ($refskpd_id === null) {
            $user = Yii::$app->user->identity;
            $refskpd_id = $user->refskpd_id;
        }

        // Get the name of the SKPD based on refskpd_id
        $nama_skpd = SakipSkpd::find()->select('nama_skpd')->where(['refskpd_id' => $refskpd_id])->scalar();

        // Set default period to this year if not provided
        if ($refperiode_id === null) {
            $currentYear = date('Y');
            $defaultPeriod = SakipPeriode::find()->where(['periode' => $currentYear])->one();
            $refperiode_id = $defaultPeriod ? $defaultPeriod->refperiode_id : null;
        }

        // Fetch all periods
        $periodeList = SakipPeriode::find()->all();

        // Fetch cascadingSubkegiatan along with associated data
        $cascadingSubkegiatan = SakipCascadingsubkegiatan::find()
            ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
            ->with([
                'refIndikatorcascadingsubkegiatan',
                'indikatorTriwulan' => function ($query) use ($refperiode_id, $refskpd_id) {
                    $query->andWhere(['refperiode_id' => $refperiode_id, 'refskpd_id' => $refskpd_id]);
                },
                'refCascadingKegiatan.refKegiatan',  // Related Kegiatan data
                'refCascadingKegiatan.refCascadingProgram.refProgram'  // Fetch Program info
            ])
            ->all();

        // Group the cascadingSubkegiatan by refcascadingkegiatan_id
        $groupedSubkegiatan = [];
        foreach ($cascadingSubkegiatan as $item) {
            $groupedSubkegiatan[$item->refCascadingKegiatan->refCascadingProgram->refProgram->refprogram_id][] = $item;
        }


        // Calculate total anggaran for each refcascadingkegiatan_id
        $anggaranPerKegiatan = [];
        foreach ($groupedSubkegiatan as $refcascadingkegiatan_id => $subkegiatanGroup) {
            $totalAnggaran = array_sum(array_column($subkegiatanGroup, 'subkegiatan_anggaran'));
            $anggaranPerKegiatan[$refcascadingkegiatan_id] = $totalAnggaran;
        }

        // Fetch anggaran per cascading program to display totals in the view
        $anggaranPerProgram = [];
        foreach ($cascadingSubkegiatan as $subkegiatan) {
            $programId = $subkegiatan->refCascadingKegiatan->refCascadingProgram->refProgram->refprogram_id;
            if (!isset($anggaranPerProgram[$programId])) {
                $anggaranPerProgram[$programId] = 0;
            }
            $anggaranPerProgram[$programId] += $subkegiatan->subkegiatan_anggaran;
        }

        // Ambil daftar SKPD untuk dropdown
        $skpdList = ArrayHelper::map(SakipSkpd::find()->all(), 'refskpd_id', 'nama_skpd');

        // Retrieve the periode based on refperiode_id
        $selectedPeriod = SakipPeriode::find()->where(['refperiode_id' => $refperiode_id])->one();
        $selectedPeriodValue = $selectedPeriod ? $selectedPeriod->periode : null; // Get the periode value

        // Set the dynamic title
        $this->view->title = "Laporan Rencana Kinerja Tahun $selectedPeriodValue - " . Html::encode($nama_skpd);

        return $this->render('index-laporan-rencana-aksi-dev', [
            'periodeList' => $periodeList,
            'selectedPeriodId' => $refperiode_id,
            'refskpd_id' => $refskpd_id,
            'refperiode_id' => $refperiode_id,
            'cascadingSubkegiatan' => $cascadingSubkegiatan,
            'groupedSubkegiatan' => $groupedSubkegiatan,
            'anggaranPerKegiatan' => $anggaranPerKegiatan,
            'anggaranPerProgram' => $anggaranPerProgram,
            'nama_skpd' => $nama_skpd,
            'selectedPeriodValue' => $selectedPeriodValue, // Include selected period value
            'selectedSkpdId' => $refskpd_id,
            'skpdList' => $skpdList,
        ]);
    }

    public function actionDownloadPdfRencanaAksi()
    {
        if (Yii::$app->request->isPost) {
            $content = Yii::$app->request->post('content'); // Konten dari POST
            $title = Yii::$app->request->post('title', 'Laporan'); // Judul untuk nama file

            // Define CSS untuk styling PDF
            $css = <<<CSS
                             #loadingOverlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        /* semi-transparent background */
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        /* Make sure it overlays above everything */
    }

    /*  */
    .title {
        border-collapse: collapse;
        font-family: 'Public Sans', sans-serif;
        /* Menggunakan font Public Sans */
        font-size: 11px;
        text-align: center;
    }

    .judultabel {
        margin-top: 0px;
        font-family: 'Bookman Old Style', 'Verdana';
        font-size: 12px;
        font-weight: bold;
    }

    .tbdata {
        border-collapse: collapse;
        font-family: 'Bookman Old Style', 'Verdana';
        font-size: 10px;
        /* border: 1px solid #5d5d5d; */
    }

    .tbdata th {
        height: 40px;
        background: rgb(0, 160, 221);
        text-align: center;
        border: 1px solid #5d5d5d;
        color: #ffffff;
        padding: 3px 5px;
    }

    .tbdata td {
        padding: 3px 5px;
        border: 1px solid #5d5d5d;
        vertical-align: top;
    }

    .tbdata td.tdisi {
        border-bottom: 0px;
    }

    .tbdata td.tdkosong {
        border-bottom: 0px;
        border-top: 0px;
        visibility: hidden;
    }

    .tengah {
        text-align: center;
    }

    .kanan {
        text-align: right;
    }

    thead {
        display: table-header-group;
    }
    CSS;

            // Setting untuk file PDF dengan CSS
            $pdf = new Pdf([
                'mode' => Pdf::MODE_CORE,
                'format' => Pdf::FORMAT_A4,
                'orientation' => Pdf::ORIENT_PORTRAIT,
                'destination' => Pdf::DEST_DOWNLOAD,
                'filename' => $title . '.pdf',
                'content' => $content, // Konten dari POST
                'cssInline' => $css, // Gunakan CSS inline
                'options' => ['title' => $title],
                'methods' => [
                    'SetHeader' => [$title],
                    'SetFooter' => ['{PAGENO}'],
                ],
            ]);

            return $pdf->render(); // Generate dan kembalikan PDF untuk diunduh
        }
    }

    public function actionIndexLaporanEkinerja($refperiode_id = null)
    {
        // Get refskpd_id from the current user
        $user = Yii::$app->user->identity;
        $refskpd_id = $user->refskpd_id;

        // Get the name of the SKPD based on refskpd_id
        $nama_skpd = SakipSkpd::find()->select('nama_skpd')->where(['refskpd_id' => $refskpd_id])->scalar();

        // Set default period to this year if not provided
        if ($refperiode_id === null) {
            $currentYear = date('Y');
            $defaultPeriod = SakipPeriode::find()->where(['periode' => $currentYear])->one();
            $refperiode_id = $defaultPeriod ? $defaultPeriod->refperiode_id : null;
        }

        // Fetch all periods
        $periodeList = SakipPeriode::find()->all();

        // Fetch Sasaran Renstra with related indicators
        $sasaranRenstra = SakipSasaranrenstra::find()
            ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
            ->with([
                'indikatorSasaran' => function ($query) {
                    $query->with(['cascadingPrograms.refProgram']); // Load refProgram details for each cascading program
                }
            ])
            ->all();



        // Retrieve the periode based on refperiode_id
        $selectedPeriod = SakipPeriode::find()->where(['refperiode_id' => $refperiode_id])->one();
        $selectedPeriodValue = $selectedPeriod ? $selectedPeriod->periode : null; // Get the periode value

        // Set the dynamic title
        $this->view->title = "Laporan Rencana Kinerja Tahun $selectedPeriodValue - " . Html::encode($nama_skpd);

        return $this->render('index-laporan-ekinerja', [
            'periodeList' => $periodeList,
            'selectedPeriodId' => $refperiode_id,
            'refskpd_id' => $refskpd_id,
            'refperiode_id' => $refperiode_id,
            'sasaranRenstra' => $sasaranRenstra,
            'nama_skpd' => $nama_skpd,
            'selectedPeriodValue' => $selectedPeriodValue, // Include selected period value
        ]);
    }

    public function actionIndexLaporanEkinerjaDev($refperiode_id = null, $refskpd_id = null)
    {
        // Ambil refskpd_id dari user saat ini jika tidak ada di request
        if ($refskpd_id === null) {
            $user = Yii::$app->user->identity;
            $refskpd_id = $user->refskpd_id;
        }

        // Get the name of the SKPD based on refskpd_id
        $nama_skpd = SakipSkpd::find()->select('nama_skpd')->where(['refskpd_id' => $refskpd_id])->scalar();

        // Set default period to this year if not provided
        if ($refperiode_id === null) {
            $currentYear = date('Y');
            $defaultPeriod = SakipPeriode::find()->where(['periode' => $currentYear])->one();
            $refperiode_id = $defaultPeriod ? $defaultPeriod->refperiode_id : null;
        }

        // Fetch all periods
        $periodeList = SakipPeriode::find()->all();

        // Fetch Sasaran Renstra with related indicators
        $sasaranRenstra = SakipSasaranrenstra::find()
            ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
            ->with([
                'indikatorSasaran' => function ($query) {
                    $query->with(['cascadingPrograms.refProgram']); // Load refProgram details for each cascading program
                }
            ])
            ->all();

        // Ambil daftar SKPD untuk dropdown
        $skpdList = ArrayHelper::map(SakipSkpd::find()->all(), 'refskpd_id', 'nama_skpd');

        // Retrieve the periode based on refperiode_id
        $selectedPeriod = SakipPeriode::find()->where(['refperiode_id' => $refperiode_id])->one();
        $selectedPeriodValue = $selectedPeriod ? $selectedPeriod->periode : null; // Get the periode value

        // Set the dynamic title
        $this->view->title = "Laporan Rencana Kinerja Tahun $selectedPeriodValue - " . Html::encode($nama_skpd);

        return $this->render('index-laporan-ekinerja-dev', [
            'periodeList' => $periodeList,
            'selectedPeriodId' => $refperiode_id,
            'refskpd_id' => $refskpd_id,
            'refperiode_id' => $refperiode_id,
            'sasaranRenstra' => $sasaranRenstra,
            'nama_skpd' => $nama_skpd,
            'selectedPeriodValue' => $selectedPeriodValue, // Include selected period value
            'selectedSkpdId' => $refskpd_id,
            'skpdList' => $skpdList,
        ]);
    }

    public function actionDownloadPdfEkinerja()
    {
        if (Yii::$app->request->isPost) {
            $content = Yii::$app->request->post('content'); // Konten dari POST
            $title = Yii::$app->request->post('title', 'Laporan'); // Judul untuk nama file

            // Define CSS untuk styling PDF
            $css = <<<CSS
                                #loadingOverlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        /* semi-transparent background */
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        /* Make sure it overlays above everything */
    }

    /*  */
    .title {
        border-collapse: collapse;
        font-family: 'Public Sans', sans-serif;
        /* Menggunakan font Public Sans */
        font-size: 11px;
        text-align: center;
    }

    .judultabel {
        margin-top: 0px;
        font-family: 'Public Sans', sans-serif;
        font-size: 12px;
        font-weight: bold;
    }

    .tbdata {
        border-collapse: collapse;
        font-family: 'Public Sans', sans-serif;
        font-size: 10px;
        /* border: 1px solid #5d5d5d; */
    }

    .tbdata th {
        height: 40px;
        background: rgb(0, 160, 221);
        text-align: center;
        border: 1px solid #5d5d5d;
        color: #ffffff;
        padding: 2px;
    }

    .tbdata td {
        padding: 2px;
        border: 1px solid #5d5d5d;
        vertical-align: top;
    }

    .tbdata td.tdisi {
        border-bottom: 0px;
    }

    .tbdata td.tdkosong {
        border-bottom: 0px;
        border-top: 0px;
        visibility: hidden;
    }

    .tengah {
        text-align: center;
    }

    .kanan {
        text-align: right;
    }

    thead {
        display: table-header-group;
    }

    .keterangan {
        border-collapse: collapse;
    }

    .keterangan td {
        padding: 5px;
        border: 1px solid #e3e3e3;
        font-size: 12px;
    }

    .tblprogram {
        width: 100%;
    }

    .tblprogram td {
        padding: 2px;
        border-left: 1px solid #c3c3c3;
        border-bottom: 1px solid #c3c3c3;
        font-size: 10px;
        vertical-align: top;
    }

    .tblprogram th {
        padding: 2px;
        border-left: 1px solid #e3e3e3;
        font-size: 10px;
        text-align: center;
        height: 30px;
    }

    .tblprogram tr.odd {
        background-color: #fafafa;
    }

    .tblprogram tr.evn {
        background-color: #e0e0e0;
    }

    #tblSasaran tr.ganjil {
        background-color: #ffffff;
    }

    #tblSasaran tr.genap {
        background-color: #f0f4f4;
    }

    th.head1 {
        vertical-align: middle;
        background: #2980b9;
        color: #fff;
    }

    th.head2 {
        vertical-align: middle;
        background: #16a085;
        color: #fff;
    }

    th.head2a {
        vertical-align: middle;
        background: #1abc9c;
        color: #fff;
    }

    th.head3 {
        vertical-align: middle;
        background: #27ae60;
        color: #fff;
    }

    th.head3a {
        vertical-align: middle;
        background: #2ecc71;
        color: #fff;
    }
    CSS;

            // Setting untuk file PDF dengan CSS
            $pdf = new Pdf([
                'mode' => Pdf::MODE_CORE,
                'format' => Pdf::FORMAT_A4,
                'orientation' => Pdf::ORIENT_PORTRAIT,
                'destination' => Pdf::DEST_DOWNLOAD,
                'filename' => $title . '.pdf',
                'content' => $content, // Konten dari POST
                'cssInline' => $css, // Gunakan CSS inline
                'options' => ['title' => $title],
                'methods' => [
                    'SetHeader' => [$title],
                    'SetFooter' => ['{PAGENO}'],
                ],
            ]);

            return $pdf->render(); // Generate dan kembalikan PDF untuk diunduh
        }
    }

    public function actionIndexLaporanAnalisisSasaranTriwulan($refperiode_id = null, $reftriwulan_id = null, $refsasaranrenstra_id = null)
    {
        // Get refskpd_id from the current user
        $user = Yii::$app->user->identity;
        $refskpd_id = $user->refskpd_id;

        // Get the name of the SKPD based on refskpd_id
        $nama_skpd = SakipSkpd::find()->select('nama_skpd')->where(['refskpd_id' => $refskpd_id])->scalar();

        // Set default period to this year if not provided
        if ($refperiode_id === null) {
            $currentYear = date('Y');
            $defaultPeriod = SakipPeriode::find()->where(['periode' => $currentYear])->one();
            $refperiode_id = $defaultPeriod ? $defaultPeriod->refperiode_id : null;
        }

        // Set default triwulan to 1 if not provided
        if ($reftriwulan_id === null) {
            $reftriwulan_id = 1;
        }

        // Fetch all periods
        $periodeList = SakipPeriode::find()->all();

        // Indicators list based on SKPD and selected period
        $sasaranRenstraList = SakipSasaranrenstra::find()
            ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
            ->all();

        // Fetch filtered data based on selected indicators and triwulan
        $indikators = SakipIndikatorsasaranrenstraTriwulan::find()
            ->where([
                'refskpd_id' => $refskpd_id,
                'refperiode_id' => $refperiode_id,
                'reftriwulan_id' => $reftriwulan_id,
            ])
            ->andFilterWhere(['refsasaranrenstra_id' => $refsasaranrenstra_id])
            ->all();

        // Retrieve the uraian_sasaranrenstra based on refsasaranrenstra_id
        $selectedSasaranRenstra = SakipSasaranrenstra::find()
            ->where(['refsasaranrenstra_id' => $refsasaranrenstra_id])
            ->select('uraian_sasaranrenstra')
            ->scalar();


        // Retrieve the periode based on refperiode_id
        $selectedPeriod = SakipPeriode::find()->where(['refperiode_id' => $refperiode_id])->one();
        $selectedPeriodValue = $selectedPeriod ? $selectedPeriod->periode : null; // Get the periode value

        // Set the dynamic title
        $this->view->title = "Laporan Laporan Analisis Pencapaian Sasaran per Triwulan $reftriwulan_id -  $selectedPeriodValue - " . Html::encode($nama_skpd);

        return $this->render('index-laporan-analisis-sasaran-triwulan', [
            'periodeList' => $periodeList,
            'selectedPeriodId' => $refperiode_id,
            'refskpd_id' => $refskpd_id,
            'refperiode_id' => $refperiode_id,
            'sasaranRenstraList' => $sasaranRenstraList,
            'indikators' => $indikators,
            'nama_skpd' => $nama_skpd,
            'selectedPeriodValue' => $selectedPeriodValue, // Include selected period value
            'selectedTriwulanId' => $reftriwulan_id, // Include selected triwulan id
            'selectedSasaranRenstraId' => $refsasaranrenstra_id, // Include selected triwulan id
            'selectedSasaranRenstraUraian' => $selectedSasaranRenstra, // Include selected sasaran uraian
        ]);
    }

    public function actionIndexLaporanAnalisisSasaranTriwulanDev($refperiode_id = null, $reftriwulan_id = null, $refsasaranrenstra_id = null, $refskpd_id = null)
    {
        // Ambil refskpd_id dari user saat ini jika tidak ada di request
        if ($refskpd_id === null) {
            $user = Yii::$app->user->identity;
            $refskpd_id = $user->refskpd_id;
        }

        // Get the name of the SKPD based on refskpd_id
        $nama_skpd = SakipSkpd::find()->select('nama_skpd')->where(['refskpd_id' => $refskpd_id])->scalar();

        // Set default period to this year if not provided
        if ($refperiode_id === null) {
            $currentYear = date('Y');
            $defaultPeriod = SakipPeriode::find()->where(['periode' => $currentYear])->one();
            $refperiode_id = $defaultPeriod ? $defaultPeriod->refperiode_id : null;
        }

        // Set default triwulan to 1 if not provided
        if ($reftriwulan_id === null) {
            $reftriwulan_id = 1;
        }

        // Fetch all periods
        $periodeList = SakipPeriode::find()->all();

        // Indicators list based on SKPD and selected period
        $sasaranRenstraList = SakipSasaranrenstra::find()
            ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
            ->all();

        // Fetch filtered data based on selected indicators and triwulan
        $indikators = SakipIndikatorsasaranrenstraTriwulan::find()
            ->where([
                'refskpd_id' => $refskpd_id,
                'refperiode_id' => $refperiode_id,
                'reftriwulan_id' => $reftriwulan_id,
            ])
            ->andFilterWhere(['refsasaranrenstra_id' => $refsasaranrenstra_id])
            ->all();

        // Retrieve the uraian_sasaranrenstra based on refsasaranrenstra_id
        $selectedSasaranRenstra = SakipSasaranrenstra::find()
            ->where(['refsasaranrenstra_id' => $refsasaranrenstra_id])
            ->select('uraian_sasaranrenstra')
            ->scalar();

        // Ambil daftar SKPD untuk dropdown
        $skpdList = ArrayHelper::map(SakipSkpd::find()->all(), 'refskpd_id', 'nama_skpd');

        // Retrieve the periode based on refperiode_id
        $selectedPeriod = SakipPeriode::find()->where(['refperiode_id' => $refperiode_id])->one();
        $selectedPeriodValue = $selectedPeriod ? $selectedPeriod->periode : null; // Get the periode value

        // Set the dynamic title
        $this->view->title = "Laporan Laporan Analisis Pencapaian Sasaran per Triwulan $reftriwulan_id -  $selectedPeriodValue - " . Html::encode($nama_skpd);

        return $this->render('index-laporan-analisis-sasaran-triwulan-dev', [
            'periodeList' => $periodeList,
            'selectedPeriodId' => $refperiode_id,
            'refskpd_id' => $refskpd_id,
            'refperiode_id' => $refperiode_id,
            'sasaranRenstraList' => $sasaranRenstraList,
            'indikators' => $indikators,
            'nama_skpd' => $nama_skpd,
            'selectedPeriodValue' => $selectedPeriodValue, // Include selected period value
            'selectedTriwulanId' => $reftriwulan_id, // Include selected triwulan id
            'selectedSasaranRenstraId' => $refsasaranrenstra_id, // Include selected triwulan id
            'selectedSasaranRenstraUraian' => $selectedSasaranRenstra, // Include selected sasaran uraian
            'selectedSkpdId' => $refskpd_id,
            'skpdList' => $skpdList,
        ]);
    }

    public function actionDownloadPdfAnalisisSasaranTriwulan()
    {
        if (Yii::$app->request->isPost) {
            $content = Yii::$app->request->post('content'); // Konten dari POST
            $title = Yii::$app->request->post('title', 'Laporan'); // Judul untuk nama file

            // Define CSS untuk styling PDF
            $css = <<<CSS
                .tbdata {
                    border-collapse: collapse;
                    font-family: 'Public Sans', sans-serif;
                    font-size: 11px;
                }
                .tbdata th {
                    height: 40px;
                    background: rgb(0, 160, 221);
                    text-align: center;
                    border: 1px solid #cfcfcf;
                    color: #ffffff;
                    padding: 2px;
                }
                .title {
            border-collapse: collapse;
            font-family: 'Public Sans', sans-serif;
            /* Menggunakan font Public Sans */
            font-size: 11px;
            text-align: center;
        }
                .tbdata td {
                    padding: 2px;
                    vertical-align: top;
                }
                .tebal {
                    font-weight: bold;
                }
                .tblRenstra {
                    width: 100%;
            font-family: 'Public Sans', sans-serif;
                    border-collapse: collapse;
                    font-size: 11px;
                }
                .tblRenstra td {
                    padding: 2px;
                    border: 1px solid #f2f2f2;
                }
                .tblRenstra .header {
                    text-align: center;
                    vertical-align: middle;
                    font-weight: bold;
                    background: #03b0e2;
                    color: #ffffff;
                }
                .trO {
                    background: #f2f2f2;
                }
                .trE {
                    background: white;
                }
                .tblAtas {
                    margin-left: 30px;
            font-family: 'Public Sans', sans-serif;
                    border-collapse: collapse;
                    font-size: 11px;
                }
                thead {
                    display: table-header-group;
                }
    CSS;

            // Setting untuk file PDF dengan CSS
            $pdf = new Pdf([
                'mode' => Pdf::MODE_CORE,
                'format' => Pdf::FORMAT_A4,
                'orientation' => Pdf::ORIENT_PORTRAIT,
                'destination' => Pdf::DEST_DOWNLOAD,
                'filename' => $title . '.pdf',
                'content' => $content, // Konten dari POST
                'cssInline' => $css, // Gunakan CSS inline
                'options' => ['title' => $title],
                'methods' => [
                    'SetHeader' => [$title],
                    'SetFooter' => ['{PAGENO}'],
                ],
            ]);

            return $pdf->render(); // Generate dan kembalikan PDF untuk diunduh
        }
    }

    public function actionIndexLaporanAnalisisSasaranTahunan($refperiode_id = null, $refsasaranrenstra_id = null)
    {
        // Get refskpd_id from the current user
        $user = Yii::$app->user->identity;
        $refskpd_id = $user->refskpd_id;

        // Get the name of the SKPD based on refskpd_id
        $nama_skpd = SakipSkpd::find()->select('nama_skpd')->where(['refskpd_id' => $refskpd_id])->scalar();

        // Set default period to this year if not provided
        if ($refperiode_id === null) {
            $currentYear = date('Y');
            $defaultPeriod = SakipPeriode::find()->where(['periode' => $currentYear])->one();
            $refperiode_id = $defaultPeriod ? $defaultPeriod->refperiode_id : null;
        }

        // Fetch all periods
        $periodeList = SakipPeriode::find()->all();

        // Indicators list based on SKPD and selected period
        $sasaranRenstraList = SakipSasaranrenstra::find()
            ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
            ->all();

        // Fetch filtered data based on selected indicators and triwulan
        $indikators = SakipIndikatorsasaranrenstra::find()
            ->where([
                'refskpd_id' => $refskpd_id,
                'refperiode_id' => $refperiode_id,
            ])
            ->andFilterWhere(['refsasaranrenstra_id' => $refsasaranrenstra_id])
            ->all();

        // Retrieve the uraian_sasaranrenstra based on refsasaranrenstra_id
        $selectedSasaranRenstra = SakipSasaranrenstra::find()
            ->where(['refsasaranrenstra_id' => $refsasaranrenstra_id])
            ->select('uraian_sasaranrenstra')
            ->scalar();


        // Retrieve the periode based on refperiode_id
        $selectedPeriod = SakipPeriode::find()->where(['refperiode_id' => $refperiode_id])->one();
        $selectedPeriodValue = $selectedPeriod ? $selectedPeriod->periode : null; // Get the periode value

        // Set the dynamic title
        $this->view->title = "Laporan Laporan Analisis Pencapaian Sasaran per Tahunan  $selectedPeriodValue - " . Html::encode($nama_skpd);

        return $this->render('index-laporan-analisis-sasaran-tahunan', [
            'periodeList' => $periodeList,
            'selectedPeriodId' => $refperiode_id,
            'refskpd_id' => $refskpd_id,
            'refperiode_id' => $refperiode_id,
            'sasaranRenstraList' => $sasaranRenstraList,
            'indikators' => $indikators,
            'nama_skpd' => $nama_skpd,
            'selectedPeriodValue' => $selectedPeriodValue, // Include selected period value
            'selectedSasaranRenstraId' => $refsasaranrenstra_id, // Include selected triwulan id
            'selectedSasaranRenstraUraian' => $selectedSasaranRenstra, // Include selected sasaran uraian
        ]);
    }

    public function actionIndexLaporanAnalisisSasaranTahunanDev($refperiode_id = null, $refsasaranrenstra_id = null, $refskpd_id = null)
    {
        // Ambil refskpd_id dari user saat ini jika tidak ada di request
        if ($refskpd_id === null) {
            $user = Yii::$app->user->identity;
            $refskpd_id = $user->refskpd_id;
        }

        // Get the name of the SKPD based on refskpd_id
        $nama_skpd = SakipSkpd::find()->select('nama_skpd')->where(['refskpd_id' => $refskpd_id])->scalar();

        // Set default period to this year if not provided
        if ($refperiode_id === null) {
            $currentYear = date('Y');
            $defaultPeriod = SakipPeriode::find()->where(['periode' => $currentYear])->one();
            $refperiode_id = $defaultPeriod ? $defaultPeriod->refperiode_id : null;
        }

        // Fetch all periods
        $periodeList = SakipPeriode::find()->all();

        // Indicators list based on SKPD and selected period
        $sasaranRenstraList = SakipSasaranrenstra::find()
            ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
            ->all();

        // Fetch filtered data based on selected indicators and triwulan
        $indikators = SakipIndikatorsasaranrenstra::find()
            ->where([
                'refskpd_id' => $refskpd_id,
                'refperiode_id' => $refperiode_id,
            ])
            ->andFilterWhere(['refsasaranrenstra_id' => $refsasaranrenstra_id])
            ->all();

        // Retrieve the uraian_sasaranrenstra based on refsasaranrenstra_id
        $selectedSasaranRenstra = SakipSasaranrenstra::find()
            ->where(['refsasaranrenstra_id' => $refsasaranrenstra_id])
            ->select('uraian_sasaranrenstra')
            ->scalar();

        // Ambil daftar SKPD untuk dropdown
        $skpdList = ArrayHelper::map(SakipSkpd::find()->all(), 'refskpd_id', 'nama_skpd');

        // Retrieve the periode based on refperiode_id
        $selectedPeriod = SakipPeriode::find()->where(['refperiode_id' => $refperiode_id])->one();
        $selectedPeriodValue = $selectedPeriod ? $selectedPeriod->periode : null; // Get the periode value

        // Set the dynamic title
        $this->view->title = "Laporan Laporan Analisis Pencapaian Sasaran per Tahunan  $selectedPeriodValue - " . Html::encode($nama_skpd);

        return $this->render('index-laporan-analisis-sasaran-tahunan-dev', [
            'periodeList' => $periodeList,
            'selectedPeriodId' => $refperiode_id,
            'refskpd_id' => $refskpd_id,
            'refperiode_id' => $refperiode_id,
            'sasaranRenstraList' => $sasaranRenstraList,
            'indikators' => $indikators,
            'nama_skpd' => $nama_skpd,
            'selectedPeriodValue' => $selectedPeriodValue, // Include selected period value
            'selectedSasaranRenstraId' => $refsasaranrenstra_id, // Include selected triwulan id
            'selectedSasaranRenstraUraian' => $selectedSasaranRenstra, // Include selected sasaran uraian
            'selectedSkpdId' => $refskpd_id,
            'skpdList' => $skpdList,
        ]);
    }

    public function actionDownloadPdfAnalisisSasaranTahunan()
    {
        if (Yii::$app->request->isPost) {
            $content = Yii::$app->request->post('content'); // Konten dari POST
            $title = Yii::$app->request->post('title', 'Laporan'); // Judul untuk nama file

            // Define CSS untuk styling PDF
            $css = <<<CSS
                .tbdata {
                    border-collapse: collapse;
                    font-family: 'Public Sans', sans-serif;
                    font-size: 11px;
                }
                .tbdata th {
                    height: 40px;
                    background: rgb(0, 160, 221);
                    text-align: center;
                    border: 1px solid #cfcfcf;
                    color: #ffffff;
                    padding: 2px;
                }
                .title {
            border-collapse: collapse;
            font-family: 'Public Sans', sans-serif;
            /* Menggunakan font Public Sans */
            font-size: 11px;
            text-align: center;
        }
                .tbdata td {
                    padding: 2px;
                    vertical-align: top;
                }
                .tebal {
                    font-weight: bold;
                }
                .tblRenstra {
                    width: 100%;
            font-family: 'Public Sans', sans-serif;
                    border-collapse: collapse;
                    font-size: 11px;
                }
                .tblRenstra td {
                    padding: 2px;
                    border: 1px solid #f2f2f2;
                }
                .tblRenstra .header {
                    text-align: center;
                    vertical-align: middle;
                    font-weight: bold;
                    background: #03b0e2;
                    color: #ffffff;
                }
                .trO {
                    background: #f2f2f2;
                }
                .trE {
                    background: white;
                }
                .tblAtas {
                    margin-left: 30px;
            font-family: 'Public Sans', sans-serif;
                    border-collapse: collapse;
                    font-size: 11px;
                }
                thead {
                    display: table-header-group;
                }
    CSS;

            // Setting untuk file PDF dengan CSS
            $pdf = new Pdf([
                'mode' => Pdf::MODE_CORE,
                'format' => Pdf::FORMAT_A4,
                'orientation' => Pdf::ORIENT_PORTRAIT,
                'destination' => Pdf::DEST_DOWNLOAD,
                'filename' => $title . '.pdf',
                'content' => $content, // Konten dari POST
                'cssInline' => $css, // Gunakan CSS inline
                'options' => ['title' => $title],
                'methods' => [
                    'SetHeader' => [$title],
                    'SetFooter' => ['{PAGENO}'],
                ],
            ]);

            return $pdf->render(); // Generate dan kembalikan PDF untuk diunduh
        }
    }

    public function actionIndexLaporanEvaluasiRkpd($refperiode_id = null)
    {
        // Get refskpd_id from the current user
        $user = Yii::$app->user->identity;
        $refskpd_id = $user->refskpd_id;

        // Get the name of the SKPD based on refskpd_id
        $nama_skpd = SakipSkpd::find()->select('nama_skpd')->where(['refskpd_id' => $refskpd_id])->scalar();

        // Set default period to this year if not provided
        if ($refperiode_id === null) {
            $currentYear = date('Y');
            $defaultPeriod = SakipPeriode::find()->where(['periode' => $currentYear])->one();
            $refperiode_id = $defaultPeriod ? $defaultPeriod->refperiode_id : null;
        }

        // Fetch all periods
        $periodeList = SakipPeriode::find()->all();



        // Retrieve the periode based on refperiode_id
        $selectedPeriod = SakipPeriode::find()->where(['refperiode_id' => $refperiode_id])->one();
        $selectedPeriodValue = $selectedPeriod ? $selectedPeriod->periode : null; // Get the periode value

        // Set the dynamic title
        $this->view->title = "Laporan Evaluasi RKPD $selectedPeriodValue - " . Html::encode($nama_skpd);

        return $this->render('index-laporan-evaluasi-rkpd', [
            'periodeList' => $periodeList,
            'selectedPeriodId' => $refperiode_id,
            'refskpd_id' => $refskpd_id,
            'refperiode_id' => $refperiode_id,
            'nama_skpd' => $nama_skpd,
            'selectedPeriodValue' => $selectedPeriodValue, // Include selected period value
        ]);
    }


    public function actionDownloadPdfEvaluasiRkpd()
    {
        if (Yii::$app->request->isPost) {
            $content = Yii::$app->request->post('content'); // Konten dari POST
            $title = Yii::$app->request->post('title', 'Laporan'); // Judul untuk nama file

            // Define CSS untuk styling PDF
            $css = <<<CSS
                .tbdata {
                    border-collapse: collapse;
                    font-family: 'Public Sans', sans-serif;
                    font-size: 11px;
                }
                .tbdata th {
                    height: 40px;
                    background: rgb(0, 160, 221);
                    text-align: center;
                    border: 1px solid #cfcfcf;
                    color: #ffffff;
                    padding: 2px;
                }
                .title {
            border-collapse: collapse;
            font-family: 'Public Sans', sans-serif;
            /* Menggunakan font Public Sans */
            font-size: 11px;
            text-align: center;
        }
                .tbdata td {
                    padding: 2px;
                    vertical-align: top;
                }
                .tebal {
                    font-weight: bold;
                }
                .tblRenstra {
                    width: 100%;
            font-family: 'Public Sans', sans-serif;
                    border-collapse: collapse;
                    font-size: 11px;
                }
                .tblRenstra td {
                    padding: 2px;
                    border: 1px solid #f2f2f2;
                }
                .tblRenstra .header {
                    text-align: center;
                    vertical-align: middle;
                    font-weight: bold;
                    background: #03b0e2;
                    color: #ffffff;
                }
                .trO {
                    background: #f2f2f2;
                }
                .trE {
                    background: white;
                }
                .tblAtas {
                    margin-left: 30px;
            font-family: 'Public Sans', sans-serif;
                    border-collapse: collapse;
                    font-size: 11px;
                }
                thead {
                    display: table-header-group;
                }
    CSS;

            // Setting untuk file PDF dengan CSS
            $pdf = new Pdf([
                'mode' => Pdf::MODE_CORE,
                'format' => Pdf::FORMAT_A4,
                'orientation' => Pdf::ORIENT_PORTRAIT,
                'destination' => Pdf::DEST_DOWNLOAD,
                'filename' => $title . '.pdf',
                'content' => $content, // Konten dari POST
                'cssInline' => $css, // Gunakan CSS inline
                'options' => ['title' => $title],
                'methods' => [
                    'SetHeader' => [$title],
                    'SetFooter' => ['{PAGENO}'],
                ],
            ]);

            return $pdf->render(); // Generate dan kembalikan PDF untuk diunduh
        }
    }
}
