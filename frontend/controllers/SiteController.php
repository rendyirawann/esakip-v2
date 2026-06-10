<?php

namespace frontend\controllers;

use frontend\models\ResendVerificationEmailForm;
use frontend\models\VerifyEmailForm;
use Yii;
use yii\base\InvalidArgumentException;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\db\Expression;
use frontend\models\VerifyOtpForm;
use yii\helpers\Json;
use yii\helpers\ArrayHelper;
use yii\helpers\HtmlPurifier;
use yii\data\ActiveDataProvider;
use common\models\LoginForm;
use common\models\UserAttemptlogin;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\models\SignupForm;
use frontend\models\ContactForm;
use frontend\models\SakipSasaranrenstra;
use frontend\models\SakipCascadingprogram;
use frontend\models\SakipProgram;
use frontend\models\SakipKegiatan;
use frontend\models\SakipSubkegiatan;
use frontend\models\SakipCascadingkegiatan;
use frontend\models\SakipCascadingsubkegiatan;
use frontend\models\SakipIndikatorcascadingprogram;
use frontend\models\SakipIndikatorcascadingkegiatan;
use frontend\models\SakipIndikatorcascadingsubkegiatan;
use frontend\models\SakipIndikatorcascadingprogramTriwulan;
use frontend\models\SakipIndikatorcascadingkegiatanTriwulan;
use frontend\models\SakipIndikatorcascadingsubkegiatanTriwulan;
use frontend\models\SakipPeriode;
use frontend\models\SakipStrategi;
use frontend\models\SakipKebijakan;
use frontend\models\SakipSkpd;
use frontend\models\User;
use frontend\models\SakipTujuanrenstra;
use frontend\models\SakipIndikatortujuanrenstra;
use frontend\models\SakipIndikatorsasaranrenstra;
use frontend\models\SakipIndikatorsasaranrenstraTriwulan;
use frontend\models\SimonaKeluaranmediacascadingkegiatan;
use frontend\models\SimonaKeluaranmediacascadingsubkegiatan;
use frontend\models\SakipEvaluasiRenja;

/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout', 'signup'],
                'rules' => [
                    [
                        'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => \yii\web\ErrorAction::class,
            ],
            'captcha' => [
                'class' => \yii\captcha\CaptchaAction::class,
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function actionIndex()
    {
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['site/welcome']);
        } else {
            return $this->redirect(['site/portal']);
        }
    }

    public function actionIndexMain()
    {
        $this->layout = 'main-app';
        return $this->render('index-main');
    }

    public function actionWelcome()
    {
        $this->layout = 'welcome';

        return $this->render('welcome');
    }

    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndexEsakip($refperiode_id = null, $refskpd_id = null)
    {
        // Cek apakah pengguna sudah login
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['login']);
        }

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

        // Retrieve the periode based on refperiode_id
        $selectedPeriod = SakipPeriode::find()->where(['refperiode_id' => $refperiode_id])->one();
        $refperiode_5tahun_id = $selectedPeriod ? $selectedPeriod->refperiode_5tahun_id : null;

        // Cek status untuk setiap card berdasarkan refperiode_5tahun_id
        $statusSasaranRenstra = (bool) SakipSasaranrenstra::find()
            ->where(['refskpd_id' => $refskpd_id, 'refperiode_5tahun_id' => $refperiode_5tahun_id])
            ->exists();
        // Cek status untuk indikator sasaran renstra
        $statusIndikatorSasaranRenstra = (bool) SakipIndikatorsasaranrenstra::find()
            ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
            ->exists();
        $statusTujuanRenstra = (bool) SakipTujuanrenstra::find()
            ->where(['refskpd_id' => $refskpd_id, 'refperiode_5tahun_id' => $refperiode_5tahun_id])
            ->exists();
        $statusIndikatorTujuanRenstra = (bool) SakipIndikatortujuanrenstra::find()
            ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
            ->exists();

        // Ambil data SakipSasaranRenstra yang memiliki refskpd_id dan refperiode_5tahun_id saat ini
        $sakipSasaranRenstra = SakipSasaranrenstra::find()
            ->where(['refskpd_id' => $refskpd_id, 'refperiode_5tahun_id' => $refperiode_5tahun_id])
            ->with(['refMisi', 'refTujuan', 'refVisi']) // Pastikan relasi ini sesuai dengan model
            ->all();

        // Hitung jumlah sasaran yang belum memiliki indikator
        $jumlahSasaranBelumIndikator = SakipSasaranrenstra::find()
            ->where(['refskpd_id' => $refskpd_id, 'refperiode_5tahun_id' => $refperiode_5tahun_id])
            ->andWhere([
                'not in',
                'refsasaranrenstra_id',
                SakipIndikatorsasaranrenstra::find()
                    ->select('refsasaranrenstra_id')
                    ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
            ])
            ->count();

        // Fetch all periods
        $periodeList = SakipPeriode::find()->all();

        // Retrieve the periode based on refperiode_id
        $selectedPeriod = SakipPeriode::find()->where(['refperiode_id' => $refperiode_id])->one();
        $selectedPeriodValue = $selectedPeriod ? $selectedPeriod->periode : null; // Get the periode value

        $triwulanTertinggi = [];
        $triwulanTerendah = [];

        $refskpd_id = Yii::$app->user->identity->refskpd_id;

        // Ambil semua indikator dalam 1 query
        $indikatorList = SakipIndikatorsasaranrenstra::find()
            ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
            ->indexBy('refindikatorsasaranrenstra_id') // agar cepat lookup
            ->all();

        // Ambil semua capaian triwulan sekaligus
        $capaianList = SakipIndikatorsasaranrenstraTriwulan::find()
            ->where([
                'refskpd_id' => $refskpd_id,
                'refperiode_id' => $refperiode_id,
            ])
            ->all();

        // Kelompokkan capaian berdasarkan triwulan dan indikator
        $capaianMap = [];
        foreach ($capaianList as $capaian) {
            $tw = $capaian->reftriwulan_id;
            $idIndikator = $capaian->refindikatorsasaranrenstra_id;
            $capaianMap[$tw][$idIndikator] = $capaian;
        }

        // Proses tiap triwulan
        foreach (range(1, 4) as $triwulan_id) {
            $nilaiTertinggi = null;
            $nilaiTerendah = null;
            $indikatorTertinggi = null;
            $indikatorTerendah = null;

            if (!isset($capaianMap[$triwulan_id])) {
                continue; // skip kalau tidak ada data capaian triwulan ini
            }

            foreach ($capaianMap[$triwulan_id] as $indikatorId => $capaianTriwulan) {
                if ($capaianTriwulan->triwulan_capaian !== null) {
                    $nilai = (float)$capaianTriwulan->triwulan_capaian;

                    $indikatorNama = $indikatorList[$indikatorId]->uraian_indikatorsasaranrenstra ?? 'Tidak diketahui';

                    if ($nilaiTertinggi === null || $nilai > $nilaiTertinggi) {
                        $nilaiTertinggi = $nilai;
                        $indikatorTertinggi = $indikatorNama;
                    }

                    if ($nilaiTerendah === null || $nilai < $nilaiTerendah) {
                        $nilaiTerendah = $nilai;
                        $indikatorTerendah = $indikatorNama;
                    }
                }
            }

            if ($nilaiTertinggi !== null) {
                $triwulanTertinggi[] = [
                    'nama' => "Triwulan $triwulan_id",
                    'value' => $nilaiTertinggi,
                    'indikator' => $indikatorTertinggi,
                    'triwulan_capaian' => $nilai, // <--- TAMBAHKAN INI
                ];
            }

            if ($nilaiTerendah !== null) {
                $triwulanTerendah[] = [
                    'nama' => "Triwulan $triwulan_id",
                    'value' => $nilaiTerendah,
                    'indikator' => $indikatorTerendah,
                    'triwulan_capaian' => $nilai, // <--- TAMBAHKAN INI
                ];
            }
        }


        $triwulanTertinggiProgram = [];
        $triwulanTerendahProgram = [];

        // Pre-fetch program list and its relations to avoid N+1 query inside loop
        $programListAll = SakipCascadingProgram::find()
            ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
            ->with(['indikatorCascadingPrograms.refIndikatorCascadingProgramTriwulan' => function ($query) use ($refskpd_id, $refperiode_id) {
                $query->andWhere([
                    'refskpd_id' => $refskpd_id,
                    'refperiode_id' => $refperiode_id,
                ]);
            }])
            ->all();

        foreach (range(1, 4) as $triwulan_id) {
            $nilaiTertinggi = null;
            $nilaiTerendah = null;
            $programTertinggi = null;
            $programTerendah = null;

            foreach ($programListAll as $program) {
                foreach ($program->indikatorCascadingPrograms as $indikator) {
                    foreach ($indikator->refIndikatorCascadingProgramTriwulan as $capaian) {
                        if ($capaian && $capaian->reftriwulan_id == $triwulan_id && $capaian->triwulan_capaian !== null && (float)$capaian->triwulan_capaian > 0) {
                            $nilai = (float)$capaian->triwulan_capaian;
                            $indikatorNama = $program->uraian_indikatorprogram ?? $indikator->uraian_indikatorprogram;

                            if ($nilaiTertinggi === null || $nilai > $nilaiTertinggi) {
                                $nilaiTertinggi = $nilai;
                                $programTertinggi = [
                                    'name' => "Triwulan $triwulan_id",
                                    'value' => $nilai,
                                    'indikator' => $indikatorNama,
                                    'triwulan_capaian' => $nilai,
                                ];
                            }

                            if ($nilaiTerendah === null || $nilai < $nilaiTerendah) {
                                $nilaiTerendah = $nilai;
                                $programTerendah = [
                                    'name' => "Triwulan $triwulan_id",
                                    'value' => $nilai,
                                    'indikator' => $indikatorNama,
                                    'triwulan_capaian' => $nilai,
                                ];
                            }
                        }
                    }
                }
            }

            if ($programTertinggi !== null) {
                $triwulanTertinggiProgram[] = $programTertinggi;
            }

            if ($programTerendah !== null) {
                $triwulanTerendahProgram[] = $programTerendah;
            }
        }


        $triwulanTertinggiKegiatan = [];
        $triwulanTerendahKegiatan = [];

        // Ambil semua kegiatan sekaligus dengan relasi indikator
        $kegiatanList = SakipCascadingkegiatan::find()
            ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
            ->with(['indikatorCascadingKegiatan.refIndikatorCascadingKegiatanTriwulan'])
            ->all();

        foreach (range(1, 4) as $triwulan_id) {
            $nilaiTertinggi = null;
            $nilaiTerendah = null;
            $kegiatanTertinggi = null;
            $kegiatanTerendah = null;

            foreach ($kegiatanList as $kegiatan) {
                foreach ($kegiatan->indikatorCascadingKegiatan as $indikator) {
                    foreach ($indikator->refIndikatorCascadingKegiatanTriwulan as $capaian) {
                        if (
                            $capaian->reftriwulan_id == $triwulan_id &&
                            $capaian->triwulan_capaian !== null &&
                            (float)$capaian->triwulan_capaian > 0
                        ) {
                            $nilai = (float)$capaian->triwulan_capaian;
                            $indikatorNama = $kegiatan->uraian_indikatorkegiatan ?? $indikator->uraian_indikatorkegiatan;

                            // Cek nilai tertinggi
                            if ($nilaiTertinggi === null || $nilai > $nilaiTertinggi) {
                                $nilaiTertinggi = $nilai;
                                $kegiatanTertinggi = [
                                    'nama' => "Triwulan $triwulan_id",
                                    'value' => $nilai,
                                    'indikator' => $indikatorNama,
                                    'triwulan_capaian' => $nilai,
                                ];
                            }

                            // Cek nilai terendah
                            if ($nilaiTerendah === null || $nilai < $nilaiTerendah) {
                                $nilaiTerendah = $nilai;
                                $kegiatanTerendah = [
                                    'nama' => "Triwulan $triwulan_id",
                                    'value' => $nilai,
                                    'indikator' => $indikatorNama,
                                    'triwulan_capaian' => $nilai,
                                ];
                            }
                        }
                    }
                }
            }

            if ($kegiatanTertinggi !== null) {
                $triwulanTertinggiKegiatan[] = $kegiatanTertinggi;
            }

            if ($kegiatanTerendah !== null) {
                $triwulanTerendahKegiatan[] = $kegiatanTerendah;
            }
        }

        $triwulanTertinggiSubkegiatan = [];
        $triwulanTerendahSubkegiatan = [];

        $subkegiatanList = SakipCascadingsubkegiatan::find()
            ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
            ->with(['refIndikatorcascadingsubkegiatan.refIndikatorCascadingSubkegiatanTriwulan'])
            ->all();

        foreach (range(1, 4) as $triwulan_id) {
            $nilaiTertinggi = null;
            $nilaiTerendah = null;
            $subkegiatanTertinggi = null;
            $subkegiatanTerendah = null;

            foreach ($subkegiatanList as $subkegiatan) {
                foreach ($subkegiatan->refIndikatorcascadingsubkegiatan as $indikator) {
                    foreach ($indikator->refIndikatorCascadingSubkegiatanTriwulan as $capaian) {
                        if (
                            $capaian->reftriwulan_id == $triwulan_id &&
                            $capaian->triwulan_capaian !== null &&
                            (float)$capaian->triwulan_capaian > 0
                        ) {
                            $nilai = (float)$capaian->triwulan_capaian;
                            $indikatorNama = $subkegiatan->uraian_indikatorsubkegiatan ?? $indikator->uraian_indikatorsubkegiatan;

                            // Nilai tertinggi
                            if ($nilaiTertinggi === null || $nilai > $nilaiTertinggi) {
                                $nilaiTertinggi = $nilai;
                                $subkegiatanTertinggi = [
                                    'nama' => "Triwulan $triwulan_id",
                                    'value' => $nilai,
                                    'indikator' => $indikatorNama,
                                    'triwulan_capaian' => $nilai,
                                ];
                            }

                            // Nilai terendah
                            if ($nilaiTerendah === null || $nilai < $nilaiTerendah) {
                                $nilaiTerendah = $nilai;
                                $subkegiatanTerendah = [
                                    'nama' => "Triwulan $triwulan_id",
                                    'value' => $nilai,
                                    'indikator' => $indikatorNama,
                                    'triwulan_capaian' => $nilai,
                                ];
                            }
                        }
                    }
                }
            }

            if ($subkegiatanTertinggi !== null) {
                $triwulanTertinggiSubkegiatan[] = $subkegiatanTertinggi;
            }

            if ($subkegiatanTerendah !== null) {
                $triwulanTerendahSubkegiatan[] = $subkegiatanTerendah;
            }
        }


        // Render halaman index dengan mengirimkan status dan data sakipSasaranRenstra ke view
        return $this->render('index-esakip', [
            'statusSasaranRenstra' => $statusSasaranRenstra,
            'statusIndikatorSasaranRenstra' => $statusIndikatorSasaranRenstra,
            'statusTujuanRenstra' => $statusTujuanRenstra,
            'statusIndikatorTujuanRenstra' => $statusIndikatorTujuanRenstra,
            'sakipSasaranRenstra' => $sakipSasaranRenstra,
            'periodeList' => $periodeList,
            'selectedPeriodId' => $refperiode_id,
            'selectedPeriodValue' => $selectedPeriodValue, // Include selected period value
            'selectedPeriodId' => $refperiode_id,
            'refskpd_id' => $refskpd_id, // Add this line
            'refperiode_id' => $refperiode_id, // Add this line
            'nama_skpd' => $nama_skpd,
            'jumlahSasaranBelumIndikator' => $jumlahSasaranBelumIndikator, // Tambahkan ini
            'triwulanTerendah' => $triwulanTerendah,
            'triwulanTertinggi' => $triwulanTertinggi, // <-- Tambahkan ini
            'triwulanTerendahProgram' => $triwulanTerendahProgram,
            'triwulanTertinggiProgram' => $triwulanTertinggiProgram, // <-- Tambahkan ini
            'triwulanTertinggiKegiatan' => $triwulanTertinggiKegiatan,
            'triwulanTerendahKegiatan' => $triwulanTerendahKegiatan,
            'triwulanTertinggiSubkegiatan' => $triwulanTertinggiSubkegiatan,
            'triwulanTerendahSubkegiatan' => $triwulanTerendahSubkegiatan,
        ]);
    }

    public function actionIndexEsakipDev($refperiode_id = null, $refskpd_id = null)
    {
        // Cek apakah pengguna sudah login
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['login']);
        }

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

        // Retrieve the periode based on refperiode_id
        $selectedPeriod = SakipPeriode::find()->where(['refperiode_id' => $refperiode_id])->one();
        $refperiode_5tahun_id = $selectedPeriod ? $selectedPeriod->refperiode_5tahun_id : null;

        // Cek status untuk setiap card berdasarkan refperiode_5tahun_id
        $statusSasaranRenstra = (bool) SakipSasaranrenstra::find()
            ->where(['refskpd_id' => $refskpd_id, 'refperiode_5tahun_id' => $refperiode_5tahun_id])
            ->exists();
        // Cek status untuk indikator sasaran renstra
        $statusIndikatorSasaranRenstra = (bool) SakipIndikatorsasaranrenstra::find()
            ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
            ->exists();
        $statusTujuanRenstra = (bool) SakipTujuanrenstra::find()
            ->where(['refskpd_id' => $refskpd_id, 'refperiode_5tahun_id' => $refperiode_5tahun_id])
            ->exists();
        $statusIndikatorTujuanRenstra = (bool) SakipIndikatortujuanrenstra::find()
            ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
            ->exists();

        // Ambil data SakipSasaranRenstra yang memiliki refskpd_id dan refperiode_5tahun_id saat ini
        $sakipSasaranRenstra = SakipSasaranrenstra::find()
            ->where(['refskpd_id' => $refskpd_id, 'refperiode_5tahun_id' => $refperiode_5tahun_id])
            ->with(['refMisi', 'refTujuan', 'refVisi']) // Pastikan relasi ini sesuai dengan model
            ->all();

        // Hitung jumlah sasaran yang belum memiliki indikator
        $jumlahSasaranBelumIndikator = SakipSasaranrenstra::find()
            ->where(['refskpd_id' => $refskpd_id, 'refperiode_5tahun_id' => $refperiode_5tahun_id])
            ->andWhere([
                'not in',
                'refsasaranrenstra_id',
                SakipIndikatorsasaranrenstra::find()
                    ->select('refsasaranrenstra_id')
                    ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
            ])
            ->count();

        // Fetch all periods
        $periodeList = SakipPeriode::find()->all();

        // Retrieve the periode based on refperiode_id
        $selectedPeriod = SakipPeriode::find()->where(['refperiode_id' => $refperiode_id])->one();
        $selectedPeriodValue = $selectedPeriod ? $selectedPeriod->periode : null; // Get the periode value

        $triwulanTertinggi = [];
        $triwulanTerendah = [];

        $refskpd_id = Yii::$app->user->identity->refskpd_id;

        // Ambil semua indikator dalam 1 query
        $indikatorList = SakipIndikatorsasaranrenstra::find()
            ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
            ->indexBy('refindikatorsasaranrenstra_id') // agar cepat lookup
            ->all();

        // Ambil semua capaian triwulan sekaligus
        $capaianList = SakipIndikatorsasaranrenstraTriwulan::find()
            ->where([
                'refskpd_id' => $refskpd_id,
                'refperiode_id' => $refperiode_id,
            ])
            ->all();

        // Kelompokkan capaian berdasarkan triwulan dan indikator
        $capaianMap = [];
        foreach ($capaianList as $capaian) {
            $tw = $capaian->reftriwulan_id;
            $idIndikator = $capaian->refindikatorsasaranrenstra_id;
            $capaianMap[$tw][$idIndikator] = $capaian;
        }

        // Proses tiap triwulan
        foreach (range(1, 4) as $triwulan_id) {
            $nilaiTertinggi = null;
            $nilaiTerendah = null;
            $indikatorTertinggi = null;
            $indikatorTerendah = null;

            if (!isset($capaianMap[$triwulan_id])) {
                continue; // skip kalau tidak ada data capaian triwulan ini
            }

            foreach ($capaianMap[$triwulan_id] as $indikatorId => $capaianTriwulan) {
                if ($capaianTriwulan->triwulan_capaian !== null) {
                    $nilai = (float)$capaianTriwulan->triwulan_capaian;

                    $indikatorNama = $indikatorList[$indikatorId]->uraian_indikatorsasaranrenstra ?? 'Tidak diketahui';

                    if ($nilaiTertinggi === null || $nilai > $nilaiTertinggi) {
                        $nilaiTertinggi = $nilai;
                        $indikatorTertinggi = $indikatorNama;
                    }

                    if ($nilaiTerendah === null || $nilai < $nilaiTerendah) {
                        $nilaiTerendah = $nilai;
                        $indikatorTerendah = $indikatorNama;
                    }
                }
            }

            if ($nilaiTertinggi !== null) {
                $triwulanTertinggi[] = [
                    'nama' => "Triwulan $triwulan_id",
                    'value' => $nilaiTertinggi,
                    'indikator' => $indikatorTertinggi,
                    'triwulan_capaian' => $nilai, // <--- TAMBAHKAN INI
                ];
            }

            if ($nilaiTerendah !== null) {
                $triwulanTerendah[] = [
                    'nama' => "Triwulan $triwulan_id",
                    'value' => $nilaiTerendah,
                    'indikator' => $indikatorTerendah,
                    'triwulan_capaian' => $nilai, // <--- TAMBAHKAN INI
                ];
            }
        }


        $triwulanTertinggiProgram = [];
        $triwulanTerendahProgram = [];

        // Pre-fetch program list and its relations to avoid N+1 query inside loop
        $programListAll = SakipCascadingProgram::find()
            ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
            ->with(['indikatorCascadingPrograms.refIndikatorCascadingProgramTriwulan' => function ($query) use ($refskpd_id, $refperiode_id) {
                $query->andWhere([
                    'refskpd_id' => $refskpd_id,
                    'refperiode_id' => $refperiode_id,
                ]);
            }])
            ->all();

        foreach (range(1, 4) as $triwulan_id) {
            $nilaiTertinggi = null;
            $nilaiTerendah = null;
            $programTertinggi = null;
            $programTerendah = null;

            foreach ($programListAll as $program) {
                foreach ($program->indikatorCascadingPrograms as $indikator) {
                    foreach ($indikator->refIndikatorCascadingProgramTriwulan as $capaian) {
                        if ($capaian && $capaian->reftriwulan_id == $triwulan_id && $capaian->triwulan_capaian !== null && (float)$capaian->triwulan_capaian > 0) {
                            $nilai = (float)$capaian->triwulan_capaian;
                            $indikatorNama = $program->uraian_indikatorprogram ?? $indikator->uraian_indikatorprogram;

                            if ($nilaiTertinggi === null || $nilai > $nilaiTertinggi) {
                                $nilaiTertinggi = $nilai;
                                $programTertinggi = [
                                    'name' => "Triwulan $triwulan_id",
                                    'value' => $nilai,
                                    'indikator' => $indikatorNama,
                                    'triwulan_capaian' => $nilai,
                                ];
                            }

                            if ($nilaiTerendah === null || $nilai < $nilaiTerendah) {
                                $nilaiTerendah = $nilai;
                                $programTerendah = [
                                    'name' => "Triwulan $triwulan_id",
                                    'value' => $nilai,
                                    'indikator' => $indikatorNama,
                                    'triwulan_capaian' => $nilai,
                                ];
                            }
                        }
                    }
                }
            }

            if ($programTertinggi !== null) {
                $triwulanTertinggiProgram[] = $programTertinggi;
            }

            if ($programTerendah !== null) {
                $triwulanTerendahProgram[] = $programTerendah;
            }
        }


        $triwulanTertinggiKegiatan = [];
        $triwulanTerendahKegiatan = [];

        // Ambil semua kegiatan sekaligus dengan relasi indikator
        $kegiatanList = SakipCascadingkegiatan::find()
            ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
            ->with(['indikatorCascadingKegiatan.refIndikatorCascadingKegiatanTriwulan'])
            ->all();

        foreach (range(1, 4) as $triwulan_id) {
            $nilaiTertinggi = null;
            $nilaiTerendah = null;
            $kegiatanTertinggi = null;
            $kegiatanTerendah = null;

            foreach ($kegiatanList as $kegiatan) {
                foreach ($kegiatan->indikatorCascadingKegiatan as $indikator) {
                    foreach ($indikator->refIndikatorCascadingKegiatanTriwulan as $capaian) {
                        if (
                            $capaian->reftriwulan_id == $triwulan_id &&
                            $capaian->triwulan_capaian !== null &&
                            (float)$capaian->triwulan_capaian > 0
                        ) {
                            $nilai = (float)$capaian->triwulan_capaian;
                            $indikatorNama = $kegiatan->uraian_indikatorkegiatan ?? $indikator->uraian_indikatorkegiatan;

                            // Cek nilai tertinggi
                            if ($nilaiTertinggi === null || $nilai > $nilaiTertinggi) {
                                $nilaiTertinggi = $nilai;
                                $kegiatanTertinggi = [
                                    'nama' => "Triwulan $triwulan_id",
                                    'value' => $nilai,
                                    'indikator' => $indikatorNama,
                                    'triwulan_capaian' => $nilai,
                                ];
                            }

                            // Cek nilai terendah
                            if ($nilaiTerendah === null || $nilai < $nilaiTerendah) {
                                $nilaiTerendah = $nilai;
                                $kegiatanTerendah = [
                                    'nama' => "Triwulan $triwulan_id",
                                    'value' => $nilai,
                                    'indikator' => $indikatorNama,
                                    'triwulan_capaian' => $nilai,
                                ];
                            }
                        }
                    }
                }
            }

            if ($kegiatanTertinggi !== null) {
                $triwulanTertinggiKegiatan[] = $kegiatanTertinggi;
            }

            if ($kegiatanTerendah !== null) {
                $triwulanTerendahKegiatan[] = $kegiatanTerendah;
            }
        }

        $triwulanTertinggiSubkegiatan = [];
        $triwulanTerendahSubkegiatan = [];

        $subkegiatanList = SakipCascadingsubkegiatan::find()
            ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
            ->with(['refIndikatorcascadingsubkegiatan.refIndikatorCascadingSubkegiatanTriwulan'])
            ->all();

        foreach (range(1, 4) as $triwulan_id) {
            $nilaiTertinggi = null;
            $nilaiTerendah = null;
            $subkegiatanTertinggi = null;
            $subkegiatanTerendah = null;

            foreach ($subkegiatanList as $subkegiatan) {
                foreach ($subkegiatan->refIndikatorcascadingsubkegiatan as $indikator) {
                    foreach ($indikator->refIndikatorCascadingSubkegiatanTriwulan as $capaian) {
                        if (
                            $capaian->reftriwulan_id == $triwulan_id &&
                            $capaian->triwulan_capaian !== null &&
                            (float)$capaian->triwulan_capaian > 0
                        ) {
                            $nilai = (float)$capaian->triwulan_capaian;
                            $indikatorNama = $subkegiatan->uraian_indikatorsubkegiatan ?? $indikator->uraian_indikatorsubkegiatan;

                            // Nilai tertinggi
                            if ($nilaiTertinggi === null || $nilai > $nilaiTertinggi) {
                                $nilaiTertinggi = $nilai;
                                $subkegiatanTertinggi = [
                                    'nama' => "Triwulan $triwulan_id",
                                    'value' => $nilai,
                                    'indikator' => $indikatorNama,
                                    'triwulan_capaian' => $nilai,
                                ];
                            }

                            // Nilai terendah
                            if ($nilaiTerendah === null || $nilai < $nilaiTerendah) {
                                $nilaiTerendah = $nilai;
                                $subkegiatanTerendah = [
                                    'nama' => "Triwulan $triwulan_id",
                                    'value' => $nilai,
                                    'indikator' => $indikatorNama,
                                    'triwulan_capaian' => $nilai,
                                ];
                            }
                        }
                    }
                }
            }

            if ($subkegiatanTertinggi !== null) {
                $triwulanTertinggiSubkegiatan[] = $subkegiatanTertinggi;
            }

            if ($subkegiatanTerendah !== null) {
                $triwulanTerendahSubkegiatan[] = $subkegiatanTerendah;
            }
        }


        // Render halaman index dengan mengirimkan status dan data sakipSasaranRenstra ke view
        return $this->render('index-esakip-dev', [
            'statusSasaranRenstra' => $statusSasaranRenstra,
            'statusIndikatorSasaranRenstra' => $statusIndikatorSasaranRenstra,
            'statusTujuanRenstra' => $statusTujuanRenstra,
            'statusIndikatorTujuanRenstra' => $statusIndikatorTujuanRenstra,
            'sakipSasaranRenstra' => $sakipSasaranRenstra,
            'periodeList' => $periodeList,
            'selectedPeriodId' => $refperiode_id,
            'selectedPeriodValue' => $selectedPeriodValue, // Include selected period value
            'selectedPeriodId' => $refperiode_id,
            'refskpd_id' => $refskpd_id, // Add this line
            'refperiode_id' => $refperiode_id, // Add this line
            'nama_skpd' => $nama_skpd,
            'jumlahSasaranBelumIndikator' => $jumlahSasaranBelumIndikator, // Tambahkan ini
            'triwulanTerendah' => $triwulanTerendah,
            'triwulanTertinggi' => $triwulanTertinggi, // <-- Tambahkan ini
            'triwulanTerendahProgram' => $triwulanTerendahProgram,
            'triwulanTertinggiProgram' => $triwulanTertinggiProgram, // <-- Tambahkan ini
            'triwulanTertinggiKegiatan' => $triwulanTertinggiKegiatan,
            'triwulanTerendahKegiatan' => $triwulanTerendahKegiatan,
            'triwulanTertinggiSubkegiatan' => $triwulanTertinggiSubkegiatan,
            'triwulanTerendahSubkegiatan' => $triwulanTerendahSubkegiatan,
        ]);
    }

    public function actionChangeProfile($id)
    {
        // Check if the requested ID matches the currently logged-in user
        if ($id != Yii::$app->user->id) {
            throw new \yii\web\ForbiddenHttpException('You are not allowed to perform this action.');
        }

        $model = User::findOne($id);

        // Check if the model is found
        if (!$model) {
            throw new \yii\web\NotFoundHttpException('The requested user does not exist.');
        }

        // Store the current hashed password for comparison
        $oldPasswordHash = $model->password_hash;

        // Handle the form submission
        if ($model->load(Yii::$app->request->post())) {
            // Debugging: Check the posted data
            Yii::debug(Yii::$app->request->post(), 'ChangeProfile');

            // Check if the password has been changed
            if (!empty($model->password_hash) && $model->password_hash !== $oldPasswordHash) {
                // Hash the new password
                $model->setPassword($model->password_hash);
            } else {
                // If password remains empty or unchanged, restore the previous hash
                $model->password_hash = $oldPasswordHash;
            }

            // Validate the model
            if ($model->validate()) {
                // Save the model
                if ($model->save()) {
                    Yii::$app->user->logout();
                    Yii::$app->session->setFlash('success', 'Password Telah Diganti Harap Login Kembali!');
                    return $this->redirect(['site/login']);
                } else {
                    // Log the error if save fails
                    Yii::error('Failed to save user model: ' . json_encode($model->getErrors()), 'ChangeProfile');
                }
            } else {
                // Debugging: Check validation errors
                Yii::debug($model->getErrors(), 'ChangeProfileErrors');
            }
        }

        return $this->render('change-profile', [
            'model' => $model,
        ]);
    }


    public function actionIndexSimona($refperiode_id = null)
    {
        $this->layout = 'main-simona';
        // Cek apakah pengguna sudah login
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['login']);
        }

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

        // Render halaman index dengan mengirimkan status dan data sakipSasaranRenstra ke view
        return $this->render('index-simona', [
            'periodeList' => SakipPeriode::find()->all(),
            'selectedPeriodId' => $refperiode_id,
            'refskpd_id' => $refskpd_id, // Add this line
            'refperiode_id' => $refperiode_id, // Add this line
            'nama_skpd' => $nama_skpd,
        ]);
    }

    public function actionIndexDokrenbang($refperiode_id = null)
    {
        $this->layout = 'main-dokrenbang';
        // Cek apakah pengguna sudah login
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['login']);
        }

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

        // Render halaman index dengan mengirimkan status dan data sakipSasaranRenstra ke view
        return $this->render('index-dokrenbang', [
            'periodeList' => SakipPeriode::find()->all(),
            'selectedPeriodId' => $refperiode_id,
            'refskpd_id' => $refskpd_id, // Add this line
            'refperiode_id' => $refperiode_id, // Add this line
            'nama_skpd' => $nama_skpd,
        ]);
    }


    public function actionPortal()
    {
        $this->layout = 'main-portal';
        return $this->render('portal');
    }

    public function actionPortalPublik($refperiode_id = null, $target_page = null, $refskpd_id = null)
    {
        $this->layout = 'main-portal';

        // Redirect dengan membawa parameter refskpd_id jika ada
        if ($target_page && $refperiode_id) {
            // Jika halaman yang dipilih adalah tabulasi, pastikan refskpd_id ada
            if ($target_page === 'portal-publik-tabulasi' && empty($refskpd_id)) {
                Yii::$app->session->setFlash('error', 'Silahkan pilih Perangkat Daerah terlebih dahulu.');
                return $this->redirect(['portal-publik', 'refperiode_id' => $refperiode_id]);
            }

            return $this->redirect([
                $target_page,
                'refperiode_id' => $refperiode_id,
                'refskpd_id' => $refskpd_id
            ]);
        }

        if ($refperiode_id === null) {
            $currentYear = date('Y');
            $defaultPeriod = SakipPeriode::find()->where(['periode' => $currentYear])->one();
            $refperiode_id = $defaultPeriod ? $defaultPeriod->refperiode_id : null;
        }

        $periodeList = SakipPeriode::find()->all();
        $skpdList = SakipSkpd::find()->all(); // Ambil data SKPD

        $selectedPeriod = SakipPeriode::find()->where(['refperiode_id' => $refperiode_id])->one();
        $selectedPeriodValue = $selectedPeriod ? $selectedPeriod->periode : null;

        return $this->render('portal-publik', [
            'periodeList' => $periodeList,
            'skpdList' => $skpdList, // Kirim ke view
            'selectedPeriodId' => $refperiode_id,
            'refperiode_id' => $refperiode_id,
            'selectedPeriodValue' => $selectedPeriodValue,
        ]);
    }

    // public function actionPortalPublikTabulasi($refperiode_id = null, $target_page = null, $refskpd_id = null)
    // {
    //     $this->layout = 'main-portal';

    //     if ($target_page && $refperiode_id) {
    //         return $this->redirect([$target_page, 'refperiode_id' => $refperiode_id, 'refskpd_id' => $refskpd_id]);
    //     }

    //     if ($refperiode_id === null) {
    //         $currentYear = date('Y');
    //         $defaultPeriod = SakipPeriode::find()->where(['periode' => $currentYear])->one();
    //         $refperiode_id = $defaultPeriod ? $defaultPeriod->refperiode_id : null;
    //     }

    //     $periodeList = SakipPeriode::find()->all();
    //     // 1. Ambil SEMUA SKPD untuk Dropdown Filter & Perhitungan Ranking Global
    //     $allSkpdList = SakipSkpd::find()->all();

    //     // 2. Tentukan SKPD mana yang akan ditampilkan datanya (Charts & Tabel Detail)
    //     $processingSkpdList = $allSkpdList;
    //     if ($refskpd_id) {
    //         // Jika ada filter, kita hanya memproses SKPD yang dipilih
    //         $processingSkpdList = array_filter($allSkpdList, function ($skpd) use ($refskpd_id) {
    //             return $skpd->refskpd_id == $refskpd_id;
    //         });
    //     }

    //     $selectedPeriod = SakipPeriode::find()->where(['refperiode_id' => $refperiode_id])->one();
    //     $selectedPeriodValue = $selectedPeriod ? $selectedPeriod->periode : null;

    //     // =========================================================================
    //     // [OPTIMALISASI TOTAL] Logika untuk Tabel dan Modal yang Efisien
    //     // =========================================================================
    //     $dataRenstra = [];

    //     if ($refperiode_id) {
    //         // 1. Ambil SEMUA data yang dibutuhkan di awal (hanya beberapa query)
    //         $sasarans = SakipSasaranrenstra::find()->where(['refperiode_id' => $refperiode_id])->all();
    //         $indikators = SakipIndikatorsasaranrenstra::find()->where(['refperiode_id' => $refperiode_id])->all();
    //         $allTriwulanData = SakipIndikatorsasaranrenstraTriwulan::find()->where(['refperiode_id' => $refperiode_id])->all();

    //         $cascadingPrograms = SakipCascadingprogram::find()->where(['refperiode_id' => $refperiode_id])->all();
    //         $cascadingKegiatans = SakipCascadingkegiatan::find()->where(['refperiode_id' => $refperiode_id])->all();
    //         $cascadingSubkegiatans = SakipCascadingsubkegiatan::find()->where(['refperiode_id' => $refperiode_id])->all();

    //         $programMaster = SakipProgram::find()->asArray()->all();
    //         $kegiatanMaster = SakipKegiatan::find()->asArray()->all();
    //         $subkegiatanMaster = SakipSubkegiatan::find()->asArray()->all();

    //         // 2. Buat "Peta" (Map) dari semua data untuk pencarian cepat di PHP
    //         $programNameMap = ArrayHelper::map($programMaster, 'refprogram_id', 'nama_program');
    //         $kegiatanNameMap = ArrayHelper::map($kegiatanMaster, 'refkegiatan_id', 'nama_kegiatan');
    //         $subkegiatanNameMap = ArrayHelper::map($subkegiatanMaster, 'refsubkegiatan_id', 'nama_subkegiatan');

    //         $triwulanGrouped = ArrayHelper::index($allTriwulanData, null, 'refindikatorsasaranrenstra_id');
    //         $indikatorGrouped = ArrayHelper::index($indikators, null, 'refsasaranrenstra_id');
    //         $cascadingProgramGrouped = ArrayHelper::index($cascadingPrograms, null, 'refindikatorsasaranrenstra_id');
    //         $cascadingKegiatanGrouped = ArrayHelper::index($cascadingKegiatans, null, 'refcascadingprogram_id');

    //         // Buat Peta Anggaran Sub Kegiatan per Kegiatan Induknya
    //         $kegiatanBudgetMap = [];
    //         $subkegiatanGrouped = ArrayHelper::index($cascadingSubkegiatans, null, 'refcascadingkegiatan_id');
    //         foreach ($subkegiatanGrouped as $kegiatanId => $subkegiatans) {
    //             $kegiatanBudgetMap[$kegiatanId] = array_sum(ArrayHelper::getColumn($subkegiatans, 'subkegiatan_anggaran'));
    //         }

    //         // Buat Peta Anggaran Program per Program Induknya
    //         $programBudgetMap = [];
    //         foreach ($cascadingKegiatanGrouped as $programId => $kegiatans) {
    //             $total = 0;
    //             foreach ($kegiatans as $keg) {
    //                 $total += $kegiatanBudgetMap[$keg->refcascadingkegiatan_id] ?? 0;
    //             }
    //             $programBudgetMap[$programId] = $total;
    //         }


    //         // 3. Susun struktur data akhir dari data yang sudah diambil (tanpa query lagi)
    //         foreach ($sasarans as $sasaran) {
    //             $refskpd_id = $sasaran->refskpd_id;
    //             $indikatorData = [];

    //             $indikatorListForThisSasaran = $indikatorGrouped[$sasaran->refsasaranrenstra_id] ?? [];

    //             foreach ($indikatorListForThisSasaran as $indikator) {
    //                 // Siapkan data triwulan
    //                 $triwulans = [];
    //                 for ($i = 1; $i <= 4; $i++) {
    //                     $triwulans[$i] = ['realisasi' => '', 'capaian' => ''];
    //                 }
    //                 $realTriwulanData = $triwulanGrouped[$indikator->refindikatorsasaranrenstra_id] ?? [];
    //                 foreach ($realTriwulanData as $tw) {
    //                     $triwulans[$tw->reftriwulan_id] = ['realisasi' => $tw->triwulan_realisasi, 'capaian' => $tw->triwulan_capaian];
    //                 }

    //                 // Siapkan data untuk modal (program, kegiatan, subkegiatan, dan anggaran)
    //                 $programsForThisIndicator = $cascadingProgramGrouped[$indikator->refindikatorsasaranrenstra_id] ?? [];
    //                 $programsWithDetails = [];
    //                 foreach ($programsForThisIndicator as $cp) {
    //                     $kegiatansForThisProgram = $cascadingKegiatanGrouped[$cp->refcascadingprogram_id] ?? [];
    //                     $kegiatansWithDetails = [];
    //                     foreach ($kegiatansForThisProgram as $keg) {
    //                         $subkegiatansForThisKegiatan = $subkegiatanGrouped[$keg->refcascadingkegiatan_id] ?? [];
    //                         $subkegiatanDetails = [];
    //                         foreach ($subkegiatansForThisKegiatan as $subkeg) {
    //                             $subkegiatanDetails[] = [
    //                                 'name' => $subkegiatanNameMap[$subkeg->refsubkegiatan_id] ?? '?',
    //                                 'budget' => (float) $subkeg->subkegiatan_anggaran
    //                             ];
    //                         }

    //                         $kegiatansWithDetails[] = [
    //                             'name' => $kegiatanNameMap[$keg->refkegiatan_id] ?? '?',
    //                             'budget' => $kegiatanBudgetMap[$keg->refcascadingkegiatan_id] ?? 0,
    //                             'subkegiatans' => $subkegiatanDetails
    //                         ];
    //                     }

    //                     $programsWithDetails[] = [
    //                         'name' => $programNameMap[$cp->refprogram_id] ?? '?',
    //                         'budget' => $programBudgetMap[$cp->refcascadingprogram_id] ?? 0,
    //                         'kegiatans' => $kegiatansWithDetails
    //                     ];
    //                 }

    //                 $indikatorData[] = [
    //                     'id' => $indikator->refindikatorsasaranrenstra_id,
    //                     'uraian_indikator' => $indikator->uraian_indikatorsasaranrenstra,
    //                     'satuan' => $indikator->indikatorsasaranrenstra_satuan,
    //                     'triwulan' => $triwulans,
    //                     'programs' => $programsWithDetails,
    //                 ];
    //             }

    //             if (!empty($indikatorData)) {
    //                 $dataRenstra[$refskpd_id][] = [
    //                     'uraian_sasaran' => $sasaran->uraian_sasaranrenstra,
    //                     'indikator' => $indikatorData,
    //                 ];
    //             }
    //         }
    //     }

    //     $triwulanTertinggiPerSkpd = [];
    //     $triwulanTerendahPerSkpd = [];

    //     // Ambil semua indikator SKPD dan capaian sekaligus
    //     $indikatorAll = SakipIndikatorsasaranrenstra::find()
    //         ->where(['refperiode_id' => $refperiode_id])
    //         ->all();

    //     $capaianAll = SakipIndikatorsasaranrenstraTriwulan::find()
    //         ->where(['refperiode_id' => $refperiode_id])
    //         ->all();

    //     // Data baru yang kita ambil
    //     $cascadingProgramsAll = SakipCascadingprogram::find()
    //         ->where(['refperiode_id' => $refperiode_id])
    //         ->all();

    //     // [BARU] Ambil data cascading dan master kegiatan
    //     $cascadingKegiatansAll = SakipCascadingkegiatan::find()
    //         ->where(['refperiode_id' => $refperiode_id])
    //         ->all();

    //     // [BARU] Ambil data cascading dan master subkegiatan
    //     $cascadingSubkegiatansAll = SakipCascadingsubkegiatan::find()
    //         ->where(['refperiode_id' => $refperiode_id])
    //         ->all();

    //     // Ambil data master program untuk mendapatkan namanya
    //     $programsMaster = SakipProgram::find()->asArray()->all();
    //     $programNameMap = ArrayHelper::map($programsMaster, 'refprogram_id', 'nama_program');
    //     $kegiatansMaster = SakipKegiatan::find()->asArray()->all();
    //     $kegiatanNameMap = ArrayHelper::map($kegiatansMaster, 'refkegiatan_id', 'nama_kegiatan');
    //     $subkegiatansMaster = SakipSubkegiatan::find()->asArray()->all();
    //     $subkegiatanNameMap = ArrayHelper::map($subkegiatansMaster, 'refsubkegiatan_id', 'nama_subkegiatan');

    //     // Mapping cepat
    //     $indikatorMapBySkpd = [];
    //     foreach ($indikatorAll as $ind) {
    //         $indikatorMapBySkpd[$ind->refskpd_id][] = $ind;
    //     }

    //     $capaianMap = [];
    //     foreach ($capaianAll as $cap) {
    //         $key = $cap->refskpd_id . '-' . $cap->refindikatorsasaranrenstra_id . '-' . $cap->reftriwulan_id;
    //         $capaianMap[$key] = $cap;
    //     }

    //     // Mapping baru untuk cascading program
    //     $cascadingProgramMap = [];
    //     foreach ($cascadingProgramsAll as $cp) {
    //         $cascadingProgramMap[$cp->refindikatorsasaranrenstra_id][] = $cp;
    //     }

    //     // Mapping baru untuk cascading kegiatan
    //     $cascadingKegiatanMap = [];
    //     foreach ($cascadingKegiatansAll as $ck) {
    //         $cascadingKegiatanMap[$ck->refindikatorsasaranrenstra_id][] = $ck;
    //     }

    //     // Mapping baru untuk cascading subkegiatan
    //     $cascadingSubkegiatanMap = [];
    //     foreach ($cascadingSubkegiatansAll as $cs) {
    //         $cascadingSubkegiatanMap[$cs->refindikatorsasaranrenstra_id][] = $cs;
    //     }

    //     // Loop per triwulan
    //     foreach (range(1, 4) as $triwulan_id) {
    //         $nilaiTertinggiGlobal = null;
    //         $nilaiTerendahGlobal = null;
    //         $skpdTertinggi = null;
    //         $skpdTerendah = null;

    //         foreach ($processingSkpdList as $skpd) {
    //             $refskpd_id = $skpd->refskpd_id;
    //             if ($refskpd_id == 1) continue;

    //             $indikatorList = $indikatorMapBySkpd[$refskpd_id] ?? [];

    //             $nilaiTertinggiSkpd = null;
    //             $nilaiTerendahSkpd = null;
    //             $indikatorTertinggi = null;
    //             $indikatorTerendah = null;

    //             foreach ($indikatorList as $indikator) {
    //                 $key = $refskpd_id . '-' . $indikator->refindikatorsasaranrenstra_id . '-' . $triwulan_id;
    //                 $capaian = $capaianMap[$key] ?? null;

    //                 if ($capaian && $capaian->triwulan_capaian !== null && (float)$capaian->triwulan_capaian > 0) {
    //                     $nilai = (float)$capaian->triwulan_capaian;

    //                     if ($nilaiTertinggiSkpd === null || $nilai > $nilaiTertinggiSkpd) {
    //                         $nilaiTertinggiSkpd = $nilai;
    //                         $indikatorTertinggi = $indikator;
    //                     }

    //                     if ($nilaiTerendahSkpd === null || $nilai < $nilaiTerendahSkpd) {
    //                         $nilaiTerendahSkpd = $nilai;
    //                         $indikatorTerendah = $indikator;
    //                     }
    //                 }
    //             }

    //             if ($nilaiTertinggiSkpd !== null && ($nilaiTertinggiGlobal === null || $nilaiTertinggiSkpd > $nilaiTertinggiGlobal)) {
    //                 $nilaiTertinggiGlobal = $nilaiTertinggiSkpd;
    //                 $programs = [];
    //                 $kegiatans = [];
    //                 $subkegiatans = [];
    //                 // Ambil program terkait dari map
    //                 $relatedPrograms = $cascadingProgramMap[$indikatorTertinggi->refindikatorsasaranrenstra_id] ?? [];
    //                 foreach ($relatedPrograms as $p) {
    //                     // Ambil nama program dari map nama
    //                     $programs[] = $programNameMap[$p->refprogram_id] ?? 'Nama Program Tidak Ditemukan';
    //                 }

    //                 // [BARU] Ambil data kegiatan terkait
    //                 $relatedKegiatans = $cascadingKegiatanMap[$indikatorTertinggi->refindikatorsasaranrenstra_id] ?? [];
    //                 foreach ($relatedKegiatans as $k) {
    //                     $kegiatans[] = $kegiatanNameMap[$k->refkegiatan_id] ?? 'Nama Kegiatan Tidak Ditemukan';
    //                 }

    //                 // [BARU] Ambil data subkegiatan terkait
    //                 $relatedSubkegiatans = $cascadingSubkegiatanMap[$indikatorTertinggi->refindikatorsasaranrenstra_id] ?? [];
    //                 foreach ($relatedSubkegiatans as $s) {
    //                     $subkegiatans[] = $subkegiatanNameMap[$s->refsubkegiatan_id] ?? 'Nama Sub Kegiatan Tidak Ditemukan';
    //                 }
    //                 $skpdTertinggi = [
    //                     'nama' => "Triwulan $triwulan_id - {$skpd->nama_skpd}",
    //                     'value' => $nilaiTertinggiSkpd,
    //                     'indikator' => $indikatorTertinggi->uraian_indikatorsasaranrenstra,
    //                     'programs' => array_unique($programs),
    //                     'kegiatans' => array_unique($kegiatans),
    //                     'subkegiatans' => array_unique($subkegiatans),
    //                 ];
    //             }

    //             if ($nilaiTerendahSkpd !== null && ($nilaiTerendahGlobal === null || $nilaiTerendahSkpd < $nilaiTerendahGlobal)) {
    //                 $nilaiTerendahGlobal = $nilaiTerendahSkpd;
    //                 $programs = [];
    //                 $kegiatans = []; // [BARU] 
    //                 $subkegiatans = []; // [BARU] 
    //                 // Ambil program terkait dari map
    //                 $relatedPrograms = $cascadingProgramMap[$indikatorTerendah->refindikatorsasaranrenstra_id] ?? [];
    //                 foreach ($relatedPrograms as $p) {
    //                     // Ambil nama program dari map nama
    //                     $programs[] = $programNameMap[$p->refprogram_id] ?? 'Nama Program Tidak Ditemukan';
    //                 }

    //                 // [BARU] Ambil data kegiatan terkait
    //                 $relatedKegiatans = $cascadingKegiatanMap[$indikatorTerendah->refindikatorsasaranrenstra_id] ?? [];
    //                 foreach ($relatedKegiatans as $k) {
    //                     $kegiatans[] = $kegiatanNameMap[$k->refkegiatan_id] ?? 'Nama Kegiatan Tidak Ditemukan';
    //                 }

    //                 // [BARU] Ambil data subkegiatan terkait
    //                 $relatedSubkegiatans = $cascadingSubkegiatanMap[$indikatorTerendah->refindikatorsasaranrenstra_id] ?? [];
    //                 foreach ($relatedSubkegiatans as $s) {
    //                     $subkegiatans[] = $subkegiatanNameMap[$s->refsubkegiatan_id] ?? 'Nama Sub Kegiatan Tidak Ditemukan';
    //                 }

    //                 $skpdTerendah = [
    //                     'nama' => "Triwulan $triwulan_id - {$skpd->nama_skpd}",
    //                     'value' => $nilaiTerendahSkpd,
    //                     // [FIX] Gunakan variabel objek untuk mendapatkan uraian
    //                     'indikator' => $indikatorTerendah->uraian_indikatorsasaranrenstra,
    //                     'programs' => array_unique($programs),
    //                     'kegiatans' => array_unique($kegiatans),
    //                     'subkegiatans' => array_unique($subkegiatans),
    //                 ];
    //             }
    //         }

    //         if ($skpdTertinggi !== null) {
    //             $triwulanTertinggiPerSkpd[] = $skpdTertinggi;
    //         }

    //         if ($skpdTerendah !== null) {
    //             $triwulanTerendahPerSkpd[] = $skpdTerendah;
    //         }
    //     }


    //     $dataProgram = [];

    //     foreach ($processingSkpdList as $skpd) {
    //         $refskpd_id = $skpd->refskpd_id;
    //         if ($refskpd_id == 1) continue;

    //         // Ambil program secara eager loading beserta relasinya
    //         $programList = SakipCascadingProgram::find()
    //             ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
    //             ->with([
    //                 'refProgram', // Ambil relasi ke master program
    //                 'indikatorCascadingPrograms.refIndikatorCascadingProgramTriwulan' => function ($query) use ($refskpd_id, $refperiode_id) {
    //                     $query->andWhere(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
    //                         ->orderBy(['reftriwulan_id' => SORT_ASC]);
    //                 }
    //             ])
    //             ->all();

    //         $programDataForSkpd = [];
    //         foreach ($programList as $program) {
    //             $indikatorData = [];

    //             foreach ($program->indikatorCascadingPrograms as $indikator) {
    //                 $triwulanData = $indikator->refIndikatorCascadingProgramTriwulan;

    //                 $indikatorData[] = [
    //                     'id' => $indikator->refindikatorprogram_id,
    //                     'uraian_indikator' => $program->uraian_indikatorprogram ?? $indikator->uraian_indikatorprogram,
    //                     'triwulan' => $triwulanData,
    //                     'program_satuan' => $program->program_satuan,
    //                 ];
    //             }

    //             // Ambil nama program dari relasi
    //             $namaProgram = $program->refProgram->nama_program ?? 'Nama Program Tidak Ditemukan';

    //             // Query untuk SUM anggaran sub kegiatan (logika Anda sudah benar)
    //             $totalAnggaranProgram = (float) SakipCascadingsubkegiatan::find()
    //                 ->where(['refcascadingprogram_id' => $program->refcascadingprogram_id])
    //                 ->sum('subkegiatan_anggaran');

    //             // [PENAMBAHAN BARU] Query untuk mendapatkan kegiatan terkait

    //             $relatedKegiatans = SakipCascadingkegiatan::find()
    //                 ->where(['refcascadingprogram_id' => $program->refcascadingprogram_id])
    //                 ->with('refKegiatan') // Eager load nama kegiatan
    //                 ->all();

    //             $kegiatansWithDetails = []; // [PERUBAHAN] Ganti nama variabel agar lebih jelas
    //             foreach ($relatedKegiatans as $keg) {
    //                 if (isset($keg->refKegiatan->nama_kegiatan)) {

    //                     $totalAnggaranKegiatan = (float) SakipCascadingsubkegiatan::find()
    //                         ->where(['refcascadingkegiatan_id' => $keg->refcascadingkegiatan_id])
    //                         ->sum('subkegiatan_anggaran');

    //                     // [PENAMBAHAN BARU] Query untuk mendapatkan sub kegiatan terkait
    //                     $subkegiatanDetails = [];
    //                     $relatedSubkegiatans = SakipCascadingsubkegiatan::find()
    //                         ->where(['refcascadingkegiatan_id' => $keg->refcascadingkegiatan_id])
    //                         ->with('refSubkegiatan')
    //                         ->all();

    //                     foreach ($relatedSubkegiatans as $subkeg) {
    //                         if (isset($subkeg->refSubkegiatan->nama_subkegiatan)) {
    //                             $subkegiatanDetails[] = [
    //                                 'name' => $subkeg->refSubkegiatan->nama_subkegiatan,
    //                                 'budget' => (float)$subkeg->subkegiatan_anggaran
    //                             ];
    //                         }
    //                     }
    //                     // ==========================================================

    //                     $kegiatansWithDetails[] = [
    //                         'name' => $keg->refKegiatan->nama_kegiatan,
    //                         'budget' => $totalAnggaranKegiatan,
    //                         'subkegiatans' => $subkegiatanDetails // Tambahkan daftar sub kegiatan
    //                     ];
    //                 }
    //             }

    //             // [PERBAIKAN UTAMA DI SINI]
    //             $programDataForSkpd[] = [
    //                 'uraian_program' => $program->uraian_sasaranprogram,
    //                 'nama_program' => $namaProgram,
    //                 'total_anggaran' => $totalAnggaranProgram,
    //                 'kegiatans' => $kegiatansWithDetails, // Kirim data kegiatan yang sudah lengkap
    //                 'indikator' => $indikatorData,
    //             ];
    //         }
    //         if (!empty($programDataForSkpd)) {
    //             $dataProgram[$refskpd_id] = $programDataForSkpd;
    //         }
    //     }


    //     $triwulanTertinggiProgram = [];
    //     $triwulanTerendahProgram = [];

    //     // 1. Ambil semua data yang relevan
    //     $capaianProgramAll = SakipIndikatorcascadingprogramTriwulan::find()->where(['refperiode_id' => $refperiode_id])->all();
    //     $indikatorProgramAll = SakipIndikatorcascadingprogram::find()->where(['refperiode_id' => $refperiode_id])->all();
    //     $cascadingProgramAll = SakipCascadingProgram::find()->where(['refperiode_id' => $refperiode_id])->all();
    //     $cascadingKegiatanAll = SakipCascadingkegiatan::find()->where(['refperiode_id' => $refperiode_id])->all();
    //     $cascadingSubkegiatanAll = SakipCascadingsubkegiatan::find()->where(['refperiode_id' => $refperiode_id])->all(); // BARU

    //     $programMaster = SakipProgram::find()->asArray()->all();
    //     $kegiatanMaster = SakipKegiatan::find()->asArray()->all();
    //     $subkegiatanMaster = SakipSubkegiatan::find()->asArray()->all(); // BARU

    //     // 2. Buat "Peta" (Map) untuk pencarian data super cepat
    //     $skpdMap = ArrayHelper::map($processingSkpdList, 'refskpd_id', 'nama_skpd');
    //     $programNameMap = ArrayHelper::map($programMaster, 'refprogram_id', 'nama_program');
    //     $kegiatanNameMap = ArrayHelper::map($kegiatanMaster, 'refkegiatan_id', 'nama_kegiatan');
    //     $subkegiatanNameMap = ArrayHelper::map($subkegiatanMaster, 'refsubkegiatan_id', 'nama_subkegiatan'); // BARU

    //     $indikatorProgramMap = ArrayHelper::map($indikatorProgramAll, 'refindikatorprogram_id', fn($m) => $m);
    //     $cascadingProgramMap = ArrayHelper::map($cascadingProgramAll, 'refcascadingprogram_id', fn($m) => $m);
    //     $cascadingKegiatanMap = ArrayHelper::index($cascadingKegiatanAll, null, 'refcascadingprogram_id');
    //     $cascadingSubkegiatanMap = ArrayHelper::index($cascadingSubkegiatanAll, null, 'refcascadingkegiatan_id'); // BARU

    //     // 3. Lakukan iterasi pada data utama (data capaian)
    //     foreach (range(1, 4) as $triwulan_id) {
    //         $nilaiTertinggi = null;
    //         $nilaiTerendah = null;
    //         $programTertinggi = null;
    //         $programTerendah = null;

    //         foreach ($capaianProgramAll as $capaian) {
    //             if ((int)$capaian->reftriwulan_id !== $triwulan_id || $capaian->triwulan_capaian <= 0) continue;

    //             $indikator = $indikatorProgramMap[$capaian->refindikatorprogram_id] ?? null;
    //             if (!$indikator) continue;

    //             $cascadingProgram = $cascadingProgramMap[$indikator->refcascadingprogram_id] ?? null;
    //             if (!$cascadingProgram || $cascadingProgram->refskpd_id == 1) continue;

    //             $namaSkpd = $skpdMap[$cascadingProgram->refskpd_id] ?? '?';
    //             $nilai = (float)$capaian->triwulan_capaian;
    //             $uraianIndikator = $cascadingProgram->uraian_indikatorprogram;
    //             $namaProgram = $programNameMap[$cascadingProgram->refprogram_id] ?? '?';

    //             $kegiatanList = [];
    //             $subkegiatanList = []; // BARU
    //             $relatedKegiatans = $cascadingKegiatanMap[$cascadingProgram->refcascadingprogram_id] ?? [];
    //             foreach ($relatedKegiatans as $keg) {
    //                 $kegiatanList[] = $kegiatanNameMap[$keg->refkegiatan_id] ?? '?';

    //                 // [BARU] Cari sub kegiatan dari setiap kegiatan
    //                 $relatedSubkegiatans = $cascadingSubkegiatanMap[$keg->refcascadingkegiatan_id] ?? [];
    //                 foreach ($relatedSubkegiatans as $subkeg) {
    //                     $subkegiatanList[] = $subkegiatanNameMap[$subkeg->refsubkegiatan_id] ?? '?';
    //                 }
    //             }

    //             $dataPoint = [
    //                 'nama' => "Triwulan $triwulan_id - $namaSkpd",
    //                 'value' => $nilai,
    //                 'indikator' => $uraianIndikator,
    //                 'program_name' => $namaProgram,
    //                 'kegiatans' => array_unique($kegiatanList),
    //                 'subkegiatans' => array_unique($subkegiatanList), // BARU
    //             ];

    //             if ($nilaiTertinggi === null || $nilai > $nilaiTertinggi) {
    //                 $nilaiTertinggi = $nilai;
    //                 $programTertinggi = $dataPoint;
    //             }
    //             if ($nilaiTerendah === null || $nilai < $nilaiTerendah) {
    //                 $nilaiTerendah = $nilai;
    //                 $programTerendah = $dataPoint;
    //             }
    //         }

    //         if ($programTertinggi !== null) $triwulanTertinggiProgram[] = $programTertinggi;
    //         if ($programTerendah !== null) $triwulanTerendahProgram[] = $programTerendah;
    //     }

    //     $dataKegiatan = [];

    //     foreach ($processingSkpdList as $skpd) {
    //         $refskpd_id = $skpd->refskpd_id;
    //         if ($refskpd_id == 1) continue;

    //         // [PENAMBAHAN] Tambahkan ->with('refKegiatan') untuk mengambil nama kegiatan lebih awal
    //         $kegiatanList = SakipCascadingkegiatan::find()
    //             ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
    //             ->with([
    //                 'refKegiatan', // Ambil relasi ke master kegiatan
    //                 'indikatorCascadingKegiatan.refIndikatorCascadingKegiatanTriwulan' => function ($query) use ($refskpd_id, $refperiode_id) {
    //                     $query->andWhere([
    //                         'refskpd_id' => $refskpd_id,
    //                         'refperiode_id' => $refperiode_id
    //                     ])->orderBy(['reftriwulan_id' => SORT_ASC]);
    //                 }
    //             ])
    //             ->all();

    //         $kegiatanDataForSkpd = [];
    //         foreach ($kegiatanList as $kegiatan) {
    //             $indikatorData = [];

    //             foreach ($kegiatan->indikatorCascadingKegiatan as $indikator) {
    //                 $triwulanData = $indikator->refIndikatorCascadingKegiatanTriwulan;

    //                 $indikatorData[] = [
    //                     'id' => $indikator->refindikatorkegiatan_id,
    //                     'uraian_indikator' => $kegiatan->uraian_indikatorkegiatan,
    //                     'triwulan' => $triwulanData,
    //                     'kegiatan_satuan' => $kegiatan->kegiatan_satuan,
    //                 ];
    //             }

    //             // [PENAMBAHAN] Ambil nama kegiatan dari relasi
    //             $namaKegiatan = $kegiatan->refKegiatan->nama_kegiatan ?? 'Nama Kegiatan Tidak Ditemukan';

    //             // [PENAMBAHAN BARU] Query untuk SUM anggaran sub kegiatan
    //             // Ini akan berjalan untuk setiap kegiatan, membuat performa lambat
    //             $totalAnggaranKegiatan = (float) SakipCascadingsubkegiatan::find()
    //                 ->where(['refcascadingkegiatan_id' => $kegiatan->refcascadingkegiatan_id])
    //                 ->sum('subkegiatan_anggaran');
    //             // ==========================================================

    //             // [PENAMBAHAN BARU] Query untuk mendapatkan sub kegiatan terkait beserta anggarannya
    //             $subkegiatanDetails = [];
    //             $relatedSubkegiatans = SakipCascadingsubkegiatan::find()
    //                 ->where(['refcascadingkegiatan_id' => $kegiatan->refcascadingkegiatan_id])
    //                 ->with('refSubkegiatan')
    //                 ->all();

    //             foreach ($relatedSubkegiatans as $subkeg) {
    //                 if (isset($subkeg->refSubkegiatan->nama_subkegiatan)) {
    //                     $subkegiatanDetails[] = [
    //                         'name' => $subkeg->refSubkegiatan->nama_subkegiatan,
    //                         'budget' => (float)$subkeg->subkegiatan_anggaran
    //                     ];
    //                 }
    //             }

    //             $kegiatanDataForSkpd[] = [
    //                 'uraian_kegiatan' => $kegiatan->uraian_sasarankegiatan,
    //                 'nama_kegiatan' => $namaKegiatan, // Data nama kegiatan
    //                 'total_anggaran' => $totalAnggaranKegiatan, // Kirim total anggaran
    //                 'subkegiatans' => $subkegiatanDetails, // Kirim daftar sub kegiatan
    //                 'indikator' => $indikatorData,
    //             ];
    //         }
    //         if (!empty($kegiatanDataForSkpd)) {
    //             $dataKegiatan[$refskpd_id] = $kegiatanDataForSkpd;
    //         }
    //     }


    //     // Di dalam actionPortalPublikTabulasi Anda:

    //     $triwulanTertinggiKegiatan = [];
    //     $triwulanTerendahKegiatan = [];

    //     // =========================================================================
    //     // Logika untuk Capaian Indikator Kegiatan yang Efisien
    //     // =========================================================================

    //     // 1. Ambil semua data yang relevan
    //     $capaianKegiatanAll = SakipIndikatorcascadingkegiatanTriwulan::find()
    //         ->where(['refperiode_id' => $refperiode_id])
    //         ->all();

    //     // [BARU] Tambahkan query untuk cascading dan master sub kegiatan
    //     $cascadingSubkegiatanAll = SakipCascadingsubkegiatan::find()
    //         ->where(['refperiode_id' => $refperiode_id])
    //         ->all();
    //     $subkegiatanMaster = SakipSubkegiatan::find()->asArray()->all();

    //     // Data lain yang sudah ada sebelumnya
    //     $indikatorKegiatanMapData = SakipIndikatorcascadingkegiatan::find()->where(['refperiode_id' => $refperiode_id])->all();
    //     $cascadingKegiatanMapData = SakipCascadingkegiatan::find()->where(['refperiode_id' => $refperiode_id])->all();
    //     $kegiatanNameMapData = SakipKegiatan::find()->asArray()->all();


    //     // 2. Buat "Peta" (Map) untuk pencarian data super cepat
    //     $skpdMap = ArrayHelper::map($processingSkpdList, 'refskpd_id', 'nama_skpd');
    //     $kegiatanNameMap = ArrayHelper::map($kegiatanNameMapData, 'refkegiatan_id', 'nama_kegiatan');
    //     $subkegiatanNameMap = ArrayHelper::map($subkegiatanMaster, 'refsubkegiatan_id', 'nama_subkegiatan'); // BARU

    //     $indikatorKegiatanMap = ArrayHelper::map($indikatorKegiatanMapData, 'refindikatorkegiatan_id', fn($m) => $m);
    //     $cascadingKegiatanMap = ArrayHelper::map($cascadingKegiatanMapData, 'refcascadingkegiatan_id', fn($m) => $m);

    //     // [BARU] Buat map untuk sub kegiatan, dikelompokkan berdasarkan kegiatan induknya
    //     $cascadingSubkegiatanMap = ArrayHelper::index($cascadingSubkegiatanAll, null, 'refcascadingkegiatan_id');


    //     // 3. Lakukan iterasi pada data utama (data capaian)
    //     foreach (range(1, 4) as $triwulan_id) {
    //         $nilaiTertinggi = null;
    //         $nilaiTerendah = null;
    //         $kegiatanTertinggi = null;
    //         $kegiatanTerendah = null;

    //         foreach ($capaianKegiatanAll as $capaian) {
    //             if ((int)$capaian->reftriwulan_id !== $triwulan_id || $capaian->triwulan_capaian <= 0) continue;

    //             // Hubungkan data dari capaian ke atas menggunakan Map
    //             $indikator = $indikatorKegiatanMap[$capaian->refindikatorkegiatan_id] ?? null;
    //             if (!$indikator) continue;

    //             $cascadingKegiatan = $cascadingKegiatanMap[$indikator->refcascadingkegiatan_id] ?? null;
    //             if (!$cascadingKegiatan || $cascadingKegiatan->refskpd_id == 1) continue;

    //             // Ambil semua informasi yang dibutuhkan
    //             $namaSkpd = $skpdMap[$cascadingKegiatan->refskpd_id] ?? '?';
    //             $nilai = (float)$capaian->triwulan_capaian;
    //             $uraianIndikator = $cascadingKegiatan->uraian_indikatorkegiatan;
    //             $namaKegiatan = $kegiatanNameMap[$cascadingKegiatan->refkegiatan_id] ?? '?';

    //             // [BARU] Ambil daftar sub kegiatan terkait dari map
    //             $subkegiatanList = [];
    //             $relatedSubkegiatans = $cascadingSubkegiatanMap[$cascadingKegiatan->refcascadingkegiatan_id] ?? [];
    //             foreach ($relatedSubkegiatans as $subkeg) {
    //                 $subkegiatanList[] = $subkegiatanNameMap[$subkeg->refsubkegiatan_id] ?? '?';
    //             }

    //             $dataPoint = [
    //                 'nama' => "Triwulan $triwulan_id - $namaSkpd",
    //                 'value' => $nilai,
    //                 'kegiatan_name' => $namaKegiatan,
    //                 'indikator' => $uraianIndikator,
    //                 'subkegiatans' => array_unique($subkegiatanList), // [BARU]
    //             ];

    //             // Logika perbandingan
    //             if ($nilaiTertinggi === null || $nilai > $nilaiTertinggi) {
    //                 $nilaiTertinggi = $nilai;
    //                 $kegiatanTertinggi = $dataPoint;
    //             }
    //             if ($nilaiTerendah === null || $nilai < $nilaiTerendah) {
    //                 $nilaiTerendah = $nilai;
    //                 $kegiatanTerendah = $dataPoint;
    //             }
    //         }

    //         if ($kegiatanTertinggi !== null) $triwulanTertinggiKegiatan[] = $kegiatanTertinggi;
    //         if ($kegiatanTerendah !== null) $triwulanTerendahKegiatan[] = $kegiatanTerendah;
    //     }

    //     $dataSubkegiatan = [];

    //     foreach ($processingSkpdList as $skpd) {
    //         $refskpd_id = $skpd->refskpd_id;
    //         if ($refskpd_id == 1) continue;

    //         // [PENAMBAHAN] Tambahkan ->with('refSubkegiatan') untuk mengambil nama sub kegiatan lebih awal
    //         $subkegiatanList = SakipCascadingsubkegiatan::find()
    //             ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
    //             ->with([
    //                 'refSubkegiatan', // Ambil relasi ke master sub kegiatan
    //                 'refIndikatorcascadingsubkegiatan.refIndikatorCascadingSubkegiatanTriwulan' => function ($query) use ($refperiode_id) {
    //                     $query->andWhere(['refperiode_id' => $refperiode_id])
    //                         ->orderBy(['reftriwulan_id' => SORT_ASC]);
    //                 }
    //             ])
    //             ->all();

    //         $subkegiatanDataForSkpd = [];

    //         foreach ($subkegiatanList as $subkegiatan) {
    //             $indikatorData = [];

    //             foreach ($subkegiatan->refIndikatorcascadingsubkegiatan as $indikator) {
    //                 $triwulanData = $indikator->refIndikatorCascadingSubkegiatanTriwulan;

    //                 $indikatorData[] = [
    //                     'id' => $indikator->refindikatorsubkegiatan_id,
    //                     'uraian_indikator' => $subkegiatan->uraian_indikatorsubkegiatan,
    //                     'triwulan' => $triwulanData,
    //                     'subkegiatan_satuan' => $subkegiatan->subkegiatan_satuan,
    //                 ];
    //             }

    //             // [PENAMBAHAN] Ambil nama sub kegiatan dari relasi
    //             $namaSubkegiatan = $subkegiatan->refSubkegiatan->nama_subkegiatan ?? 'Nama Sub Kegiatan Tidak Ditemukan';

    //             // [PENAMBAHAN BARU] Ambil anggaran langsung dari objek cascading sub kegiatan
    //             $totalAnggaranSubkegiatan = (float) $subkegiatan->subkegiatan_anggaran;
    //             // ====================================================================

    //             $subkegiatanDataForSkpd[] = [
    //                 'uraian_subkegiatan' => $subkegiatan->uraian_sasaransubkegiatan,
    //                 'nama_subkegiatan' => $namaSubkegiatan, // Data nama sub kegiatan
    //                 'total_anggaran' => $totalAnggaranSubkegiatan, // Kirim total anggaran
    //                 'indikator' => $indikatorData,
    //             ];
    //         }
    //         if (!empty($subkegiatanDataForSkpd)) {
    //             $dataSubkegiatan[$refskpd_id] = $subkegiatanDataForSkpd;
    //         }
    //     }


    //     $triwulanTertinggiSubkegiatan = [];
    //     $triwulanTerendahSubkegiatan = [];

    //     // 1. Ambil semua data yang relevan dalam satu kali query per tabel (flat)
    //     $capaianSubkegiatanAll = SakipIndikatorcascadingsubkegiatanTriwulan::find()
    //         ->where(['refperiode_id' => $refperiode_id])
    //         ->all();

    //     // 2. Buat "Peta" (Map) dari semua data pendukung untuk pencarian cepat
    //     $skpdMap = ArrayHelper::map($processingSkpdList, 'refskpd_id', 'nama_skpd');

    //     $indikatorSubkegiatanMap = ArrayHelper::map(
    //         SakipIndikatorcascadingsubkegiatan::find()->where(['refperiode_id' => $refperiode_id])->all(),
    //         'refindikatorsubkegiatan_id',
    //         fn($m) => $m
    //     );
    //     $cascadingSubkegiatanMap = ArrayHelper::map(
    //         SakipCascadingsubkegiatan::find()->where(['refperiode_id' => $refperiode_id])->all(),
    //         'refcascadingsubkegiatan_id',
    //         fn($m) => $m
    //     );
    //     $subkegiatanNameMap = ArrayHelper::map(SakipSubkegiatan::find()->asArray()->all(), 'refsubkegiatan_id', 'nama_subkegiatan');

    //     // 3. Lakukan iterasi pada data utama (data capaian) yang sudah diambil
    //     foreach (range(1, 4) as $triwulan_id) {
    //         $nilaiTertinggi = null;
    //         $nilaiTerendah = null;
    //         $subkegiatanTertinggi = null;
    //         $subkegiatanTerendah = null;

    //         foreach ($capaianSubkegiatanAll as $capaian) {
    //             if ((int)$capaian->reftriwulan_id !== $triwulan_id || $capaian->triwulan_capaian <= 0) continue;

    //             // Hubungkan data dari capaian ke atas menggunakan Map (sangat cepat)
    //             $indikator = $indikatorSubkegiatanMap[$capaian->refindikatorsubkegiatan_id] ?? null;
    //             if (!$indikator) continue;

    //             $cascadingSubkegiatan = $cascadingSubkegiatanMap[$indikator->refcascadingsubkegiatan_id] ?? null;
    //             if (!$cascadingSubkegiatan || $cascadingSubkegiatan->refskpd_id == 1) continue;

    //             // Ambil semua informasi yang dibutuhkan dari Map
    //             $namaSkpd = $skpdMap[$cascadingSubkegiatan->refskpd_id] ?? '?';
    //             $nilai = (float)$capaian->triwulan_capaian;
    //             $uraianIndikator = $cascadingSubkegiatan->uraian_indikatorsubkegiatan;

    //             // [INI YANG ANDA MINTA] Ambil nama Sub Kegiatan dari Map
    //             $namaSubkegiatan = $subkegiatanNameMap[$cascadingSubkegiatan->refsubkegiatan_id] ?? 'Sub Kegiatan Tidak Ditemukan';

    //             $dataPoint = [
    //                 'nama' => "Triwulan $triwulan_id - $namaSkpd",
    //                 'value' => $nilai,
    //                 'subkegiatan_name' => $namaSubkegiatan, // Data Sub Kegiatan yang Anda inginkan
    //                 'indikator' => $uraianIndikator,
    //             ];

    //             // Logika perbandingan
    //             if ($nilaiTertinggi === null || $nilai > $nilaiTertinggi) {
    //                 $nilaiTertinggi = $nilai;
    //                 $subkegiatanTertinggi = $dataPoint;
    //             }
    //             if ($nilaiTerendah === null || $nilai < $nilaiTerendah) {
    //                 $nilaiTerendah = $nilai;
    //                 $subkegiatanTerendah = $dataPoint;
    //             }
    //         }

    //         if ($subkegiatanTertinggi !== null) $triwulanTertinggiSubkegiatan[] = $subkegiatanTertinggi;
    //         if ($subkegiatanTerendah !== null) $triwulanTerendahSubkegiatan[] = $subkegiatanTerendah;
    //     }

    //     // =========================================================================
    //     // [LOGIKA BARU] Menghitung Peringkat OPD dengan Aturan Spesial
    //     // =========================================================================
    //     $opdRanking = [];
    //     if ($refperiode_id) {
    //         // 1. Ambil semua data yang relevan sekaligus
    //         $capaianProgramAll = SakipIndikatorcascadingprogramTriwulan::find()
    //             ->where(['refperiode_id' => $refperiode_id])
    //             ->andWhere(['>', 'triwulan_capaian', 0])
    //             ->orderBy(['reftriwulan_id' => SORT_DESC]) // Urutkan dari TW4 ke TW1
    //             ->all();

    //         $indikatorProgramAll = SakipIndikatorcascadingprogram::find()
    //             ->where(['refperiode_id' => $refperiode_id])
    //             ->all();

    //         // Data baru yang dibutuhkan untuk mengecek uraian
    //         $indikatorSasaranRenstraAll = SakipIndikatorsasaranrenstra::find()
    //             ->where(['refperiode_id' => $refperiode_id])
    //             ->all();

    //         // [BARU] Data untuk Skor Anggaran
    //         $penyerapanAnggaranAll = SakipIndikatorcascadingsubkegiatanTriwulan::find()
    //             ->where(['refperiode_id' => $refperiode_id])
    //             ->andWhere(['is not', 'triwulan_penyerapan_anggaran', null])
    //             ->all();

    //         // 2. Buat "Peta" (Map) dari semua data pendukung
    //         $skpdMap = ArrayHelper::map($processingSkpdList, 'refskpd_id', 'nama_skpd');
    //         $indikatorProgramMap = ArrayHelper::map($indikatorProgramAll, 'refindikatorprogram_id', fn($m) => $m);
    //         // Peta untuk uraian Indikator Sasaran Renstra
    //         $uraianSasaranRenstraMap = ArrayHelper::map($indikatorSasaranRenstraAll, 'refindikatorsasaranrenstra_id', 'uraian_indikatorsasaranrenstra');

    //         // 3. [TAHAP 1: Agregasi & Hitung SKOR per Indikator Program]
    //         // Kelompokkan capaian per indikator program
    //         $capaianPerIndikator = ArrayHelper::index($capaianProgramAll, null, 'refindikatorprogram_id');

    //         $skorPerIndikator = [];
    //         foreach ($capaianPerIndikator as $indikatorId => $capaianTriwulans) {
    //             $indikatorProgram = $indikatorProgramMap[$indikatorId] ?? null;
    //             if (!$indikatorProgram) continue;

    //             $skorIndikator = 0;
    //             // Dapatkan ID Indikator Sasaran Renstra untuk pengecekan
    //             $refIndikatorSasaranRenstraId = $indikatorProgram->refindikatorsasaranrenstra_id;
    //             $uraian = $uraianSasaranRenstraMap[$refIndikatorSasaranRenstraId] ?? '';

    //             // Normalisasi teks untuk pengecekan (case-insensitive, tanpa spasi)
    //             $normalizedUraian = str_replace(' ', '', strtolower($uraian));

    //             // Cek apakah ini indikator spesial
    //             if (str_contains($normalizedUraian, 'lheakip') || str_contains($normalizedUraian, 'indekskepuasanmasyarakat')) {
    //                 // ATURAN SKOR SPESIAL: Ambil capaian dari triwulan terakhir yang diisi.
    //                 // Karena sudah diurutkan (DESC), kita cukup ambil yang pertama dari array.
    //                 if (!empty($capaianTriwulans)) {
    //                     $skorIndikator = (float)$capaianTriwulans[0]->triwulan_capaian;
    //                 }
    //             } else {
    //                 // ATURAN SKOR NORMAL: Jumlahkan semua capaian triwulan
    //                 foreach ($capaianTriwulans as $capaian) {
    //                     $skorIndikator += (float)$capaian->triwulan_capaian;
    //                 }
    //             }

    //             // Simpan skor yang sudah dihitung untuk setiap indikator
    //             if ($skorIndikator > 0) {
    //                 $skorPerIndikator[$indikatorId] = $skorIndikator;
    //             }
    //         }

    //         // 4. [TAHAP 2: Agregasi per SKPD]
    //         $skpdScores = [];
    //         foreach ($skorPerIndikator as $indikatorId => $skor) {
    //             $indikatorProgram = $indikatorProgramMap[$indikatorId] ?? null;
    //             if (!$indikatorProgram) continue;

    //             $skpdId = $indikatorProgram->refskpd_id;
    //             if ($skpdId == 1) continue;

    //             if (!isset($skpdScores[$skpdId])) {
    //                 $skpdScores[$skpdId] = ['total_skor' => 0, 'jumlah_indikator' => 0];
    //             }
    //             $skpdScores[$skpdId]['total_skor'] += $skor;
    //             $skpdScores[$skpdId]['jumlah_indikator']++;
    //         }

    //         // [BARU] 4. Hitung SKOR PENYERAPAN ANGGARAN per OPD
    //         $skpdAnggaranScores = [];
    //         foreach ($penyerapanAnggaranAll as $serapan) {
    //             $skpdId = $serapan->refskpd_id;
    //             if ($skpdId == 1) continue;

    //             if (!isset($skpdAnggaranScores[$skpdId])) {
    //                 $skpdAnggaranScores[$skpdId] = ['total_penyerapan' => 0, 'jumlah_data' => 0];
    //             }
    //             $skpdAnggaranScores[$skpdId]['total_penyerapan'] += (float)$serapan->triwulan_penyerapan_anggaran;
    //             $skpdAnggaranScores[$skpdId]['jumlah_data']++;
    //         }


    //         // 5. Gabungkan kedua skor dan hitung rata-rata
    //         $skpdListForRanking = [];
    //         foreach ($skpdMap as $skpdId => $namaSkpd) {
    //             if ($skpdId == 1) continue;

    //             // Rata-rata Skor Capaian
    //             $avgSkor = 0;
    //             if (isset($skpdScores[$skpdId]) && $skpdScores[$skpdId]['jumlah_indikator'] > 0) {
    //                 $avgSkor = $skpdScores[$skpdId]['total_skor'] / $skpdScores[$skpdId]['jumlah_indikator'];
    //             }

    //             // Rata-rata Penyerapan Anggaran
    //             $avgAnggaran = 0;
    //             if (isset($skpdAnggaranScores[$skpdId]) && $skpdAnggaranScores[$skpdId]['jumlah_data'] > 0) {
    //                 $avgAnggaran = $skpdAnggaranScores[$skpdId]['total_penyerapan'] / $skpdAnggaranScores[$skpdId]['jumlah_data'];
    //             }

    //             $skpdListForRanking[] = [
    //                 'nama_skpd' => $namaSkpd,
    //                 'skor' => $avgSkor,
    //                 'anggaran' => $avgAnggaran, // Data baru
    //             ];
    //         }

    //         // 6. [PERBAIKAN] Urutkan OPD dengan 2 kriteria
    //         usort($skpdListForRanking, function ($a, $b) {
    //             // Kriteria 1: Urutkan berdasarkan skor (menurun)
    //             if ($b['skor'] != $a['skor']) {
    //                 return $b['skor'] <=> $a['skor'];
    //             }
    //             // Kriteria 2: Jika skor sama, urutkan berdasarkan anggaran (menurun)
    //             return $b['anggaran'] <=> $a['anggaran'];
    //         });

    //         // 7. Beri nomor peringkat
    //         foreach ($skpdListForRanking as $index => $rankedSkpd) {
    //             $opdRanking[] = [
    //                 'rank' => $index + 1,
    //                 'nama_skpd' => $rankedSkpd['nama_skpd'],
    //                 'skor' => $rankedSkpd['skor'],
    //                 'anggaran' => $rankedSkpd['anggaran'], // Kirim data anggaran ke view
    //             ];
    //         }
    //     }



    //     return $this->render('portal-publik-tabulasi', [
    //         'target_page' => $target_page,
    //         'periodeList' => $periodeList,
    //         'selectedPeriodId' => $refperiode_id,
    //         'refperiode_id' => $refperiode_id,
    //         'selectedPeriodValue' => $selectedPeriodValue,
    //         'skpdList' => $allSkpdList, // Kirim SEMUA untuk dropdown filter
    //         'displayedSkpdList' => $processingSkpdList, // Kirim YANG DIFILTER untuk loop tampilan tabel
    //         'selectedSkpdId' => $refskpd_id, // Kirim ID SKPD terpilih untuk default value dropdown
    //         'dataRenstra' => $dataRenstra,
    //         'triwulanTerendahPerSkpd' => $triwulanTerendahPerSkpd,
    //         'triwulanTertinggiPerSkpd' => $triwulanTertinggiPerSkpd, // <-- Tambahkan ini
    //         'triwulanTerendahProgram' => $triwulanTerendahProgram,
    //         'triwulanTertinggiProgram' => $triwulanTertinggiProgram, // <-- Tambahkan ini
    //         'triwulanTertinggiKegiatan' => $triwulanTertinggiKegiatan,
    //         'triwulanTerendahKegiatan' => $triwulanTerendahKegiatan,
    //         'triwulanTertinggiSubkegiatan' => $triwulanTertinggiSubkegiatan,
    //         'triwulanTerendahSubkegiatan' => $triwulanTerendahSubkegiatan,

    //         'dataProgram' => $dataProgram,
    //         'dataKegiatan' => $dataKegiatan,
    //         'dataSubkegiatan' => $dataSubkegiatan,
    //         'opdRanking' => $opdRanking, // Kirim data peringkat ke view
    //     ]);
    // }
    public function actionPortalPublikTabulasi($refperiode_id = null, $target_page = null, $refskpd_id = null)
    {
        $this->layout = 'main-portal';

        // Logika Redirect dengan validasi SKPD jika ke halaman tabulasi
        if ($target_page && $refperiode_id) {
            if ($target_page == 'portal-publik-tabulasi' && empty($refskpd_id)) {
                Yii::$app->session->setFlash('error', 'Silahkan pilih Perangkat Daerah terlebih dahulu.');
            } else {
                return $this->redirect([
                    $target_page,
                    'refperiode_id' => $refperiode_id,
                    'refskpd_id' => $refskpd_id
                ]);
            }
        }

        // 1. Ambil List untuk Dropdown (Data Ringan)
        $periodeList = SakipPeriode::find()->all();
        $allSkpdList = SakipSkpd::find()->all();

        // =========================================================================
        // INISIALISASI VARIABEL KOSONG (DEFAULT)
        // Agar tidak error "Undefined Variable" di View saat halaman pertama kali dibuka
        // =========================================================================
        $selectedPeriodValue = null;
        $processingSkpdList = [];
        $dataRenstra = [];
        $triwulanTerendahPerSkpd = [];
        $triwulanTertinggiPerSkpd = [];
        $triwulanTerendahProgram = [];
        $triwulanTertinggiProgram = [];
        $triwulanTertinggiKegiatan = [];
        $triwulanTerendahKegiatan = [];
        $triwulanTertinggiSubkegiatan = [];
        $triwulanTerendahSubkegiatan = [];
        $dataProgram = [];
        $dataKegiatan = [];
        $dataSubkegiatan = [];
        $opdRanking = [];

        // =========================================================================
        // LOGIKA UTAMA: Hanya jalankan query berat JIKA periode sudah dipilih user
        // =========================================================================
        if ($refperiode_id !== null) {

            $selectedPeriod = SakipPeriode::find()->where(['refperiode_id' => $refperiode_id])->one();
            $selectedPeriodValue = $selectedPeriod ? $selectedPeriod->periode : null;

            // Filter SKPD: Jika ada filter, ambil yang dipilih saja. Jika tidak, ambil semua.
            $processingSkpdList = $allSkpdList;
            if ($refskpd_id) {
                $processingSkpdList = array_filter($allSkpdList, function ($skpd) use ($refskpd_id) {
                    return $skpd->refskpd_id == $refskpd_id;
                });
            }

            $refperiode_5tahun_id = $selectedPeriod ? $selectedPeriod->refperiode_5tahun_id : null;
            // --- MULAI PROSES DATA RENSTRA ---
            // 1. Ambil SEMUA data yang dibutuhkan di awal (hanya beberapa query)
            $sasarans = SakipSasaranrenstra::find()->where(['refperiode_5tahun_id' => $refperiode_5tahun_id])->all();
            $indikators = SakipIndikatorsasaranrenstra::find()->where(['refperiode_id' => $refperiode_id])->all();
            $allTriwulanData = SakipIndikatorsasaranrenstraTriwulan::find()->where(['refperiode_id' => $refperiode_id])->all();

            $cascadingPrograms = SakipCascadingprogram::find()->where(['refperiode_id' => $refperiode_id])->all();
            $cascadingKegiatans = SakipCascadingkegiatan::find()->where(['refperiode_id' => $refperiode_id])->all();
            $cascadingSubkegiatans = SakipCascadingsubkegiatan::find()->where(['refperiode_id' => $refperiode_id])->all();

            $programMaster = SakipProgram::find()->asArray()->all();
            $kegiatanMaster = SakipKegiatan::find()->asArray()->all();
            $subkegiatanMaster = SakipSubkegiatan::find()->asArray()->all();

            // 2. Buat "Peta" (Map) dari semua data untuk pencarian cepat di PHP
            $programNameMap = ArrayHelper::map($programMaster, 'refprogram_id', 'nama_program');
            $kegiatanNameMap = ArrayHelper::map($kegiatanMaster, 'refkegiatan_id', 'nama_kegiatan');
            $subkegiatanNameMap = ArrayHelper::map($subkegiatanMaster, 'refsubkegiatan_id', 'nama_subkegiatan');

            $triwulanGrouped = ArrayHelper::index($allTriwulanData, null, 'refindikatorsasaranrenstra_id');
            $indikatorGrouped = ArrayHelper::index($indikators, null, 'refsasaranrenstra_id');
            $cascadingProgramGrouped = ArrayHelper::index($cascadingPrograms, null, 'refindikatorsasaranrenstra_id');
            $cascadingKegiatanGrouped = ArrayHelper::index($cascadingKegiatans, null, 'refcascadingprogram_id');

            // Buat Peta Anggaran Sub Kegiatan per Kegiatan Induknya
            $kegiatanBudgetMap = [];
            $subkegiatanGrouped = ArrayHelper::index($cascadingSubkegiatans, null, 'refcascadingkegiatan_id');
            foreach ($subkegiatanGrouped as $kegiatanId => $subkegiatans) {
                $kegiatanBudgetMap[$kegiatanId] = array_sum(ArrayHelper::getColumn($subkegiatans, 'subkegiatan_anggaran'));
            }

            // Buat Peta Anggaran Program per Program Induknya
            $programBudgetMap = [];
            foreach ($cascadingKegiatanGrouped as $programId => $kegiatans) {
                $total = 0;
                foreach ($kegiatans as $keg) {
                    $total += $kegiatanBudgetMap[$keg->refcascadingkegiatan_id] ?? 0;
                }
                $programBudgetMap[$programId] = $total;
            }

            // 3. Susun struktur data akhir dari data yang sudah diambil
            foreach ($sasarans as $sasaran) {
                $refskpd_id_loop = $sasaran->refskpd_id; // Rename variable agar aman
                $indikatorData = [];

                $indikatorListForThisSasaran = $indikatorGrouped[$sasaran->refsasaranrenstra_id] ?? [];

                foreach ($indikatorListForThisSasaran as $indikator) {
                    // Siapkan data triwulan
                    $triwulans = [];
                    for ($i = 1; $i <= 4; $i++) {
                        $triwulans[$i] = ['realisasi' => '', 'capaian' => ''];
                    }
                    $realTriwulanData = $triwulanGrouped[$indikator->refindikatorsasaranrenstra_id] ?? [];
                    foreach ($realTriwulanData as $tw) {
                        $triwulans[$tw->reftriwulan_id] = ['realisasi' => $tw->triwulan_realisasi, 'capaian' => $tw->triwulan_capaian];
                    }

                    // Siapkan data untuk modal
                    $programsForThisIndicator = $cascadingProgramGrouped[$indikator->refindikatorsasaranrenstra_id] ?? [];
                    $programsWithDetails = [];
                    foreach ($programsForThisIndicator as $cp) {
                        $kegiatansForThisProgram = $cascadingKegiatanGrouped[$cp->refcascadingprogram_id] ?? [];
                        $kegiatansWithDetails = [];
                        foreach ($kegiatansForThisProgram as $keg) {
                            $subkegiatansForThisKegiatan = $subkegiatanGrouped[$keg->refcascadingkegiatan_id] ?? [];
                            $subkegiatanDetails = [];
                            foreach ($subkegiatansForThisKegiatan as $subkeg) {
                                $subkegiatanDetails[] = [
                                    'name' => $subkegiatanNameMap[$subkeg->refsubkegiatan_id] ?? '?',
                                    'budget' => (float) $subkeg->subkegiatan_anggaran
                                ];
                            }

                            $kegiatansWithDetails[] = [
                                'name' => $kegiatanNameMap[$keg->refkegiatan_id] ?? '?',
                                'budget' => $kegiatanBudgetMap[$keg->refcascadingkegiatan_id] ?? 0,
                                'subkegiatans' => $subkegiatanDetails
                            ];
                        }

                        $programsWithDetails[] = [
                            'name' => $programNameMap[$cp->refprogram_id] ?? '?',
                            'budget' => $programBudgetMap[$cp->refcascadingprogram_id] ?? 0,
                            'kegiatans' => $kegiatansWithDetails
                        ];
                    }

                    $indikatorData[] = [
                        'id' => $indikator->refindikatorsasaranrenstra_id,
                        'uraian_indikator' => $indikator->uraian_indikatorsasaranrenstra,
                        'satuan' => $indikator->indikatorsasaranrenstra_satuan,
                        'triwulan' => $triwulans,
                        'programs' => $programsWithDetails,
                    ];
                }

                if (!empty($indikatorData)) {
                    $dataRenstra[$refskpd_id_loop][] = [
                        'uraian_sasaran' => $sasaran->uraian_sasaranrenstra,
                        'indikator' => $indikatorData,
                    ];
                }
            }

            // --- MULAI LOGIKA CHART (Perhitungan Tertinggi/Terendah) ---
            $indikatorAll = SakipIndikatorsasaranrenstra::find()->where(['refperiode_id' => $refperiode_id])->all();
            $capaianAll = SakipIndikatorsasaranrenstraTriwulan::find()->where(['refperiode_id' => $refperiode_id])->all();
            $cascadingProgramsAll = SakipCascadingprogram::find()->where(['refperiode_id' => $refperiode_id])->all();
            $cascadingKegiatansAll = SakipCascadingkegiatan::find()->where(['refperiode_id' => $refperiode_id])->all();
            $cascadingSubkegiatansAll = SakipCascadingsubkegiatan::find()->where(['refperiode_id' => $refperiode_id])->all();

            $indikatorMapBySkpd = [];
            foreach ($indikatorAll as $ind) {
                $indikatorMapBySkpd[$ind->refskpd_id][] = $ind;
            }

            $capaianMap = [];
            foreach ($capaianAll as $cap) {
                $key = $cap->refskpd_id . '-' . $cap->refindikatorsasaranrenstra_id . '-' . $cap->reftriwulan_id;
                $capaianMap[$key] = $cap;
            }

            $cascadingProgramMap = [];
            foreach ($cascadingProgramsAll as $cp) {
                $cascadingProgramMap[$cp->refindikatorsasaranrenstra_id][] = $cp;
            }
            $cascadingKegiatanMap = [];
            foreach ($cascadingKegiatansAll as $ck) {
                $cascadingKegiatanMap[$ck->refindikatorsasaranrenstra_id][] = $ck;
            }
            $cascadingSubkegiatanMap = [];
            foreach ($cascadingSubkegiatansAll as $cs) {
                $cascadingSubkegiatanMap[$cs->refindikatorsasaranrenstra_id][] = $cs;
            }

            // Loop untuk Chart Sasaran Strategis
            foreach (range(1, 4) as $triwulan_id) {
                $nilaiTertinggiGlobal = null;
                $nilaiTerendahGlobal = null;
                $skpdTertinggi = null;
                $skpdTerendah = null;

                foreach ($processingSkpdList as $skpd) {
                    $refskpd_id_loop = $skpd->refskpd_id;
                    if ($refskpd_id_loop == 1) continue;

                    $indikatorList = $indikatorMapBySkpd[$refskpd_id_loop] ?? [];
                    $nilaiTertinggiSkpd = null;
                    $nilaiTerendahSkpd = null;
                    $indikatorTertinggi = null;
                    $indikatorTerendah = null;

                    foreach ($indikatorList as $indikator) {
                        $key = $refskpd_id_loop . '-' . $indikator->refindikatorsasaranrenstra_id . '-' . $triwulan_id;
                        $capaian = $capaianMap[$key] ?? null;

                        if ($capaian && $capaian->triwulan_capaian !== null && (float)$capaian->triwulan_capaian > 0) {
                            $nilai = (float)$capaian->triwulan_capaian;
                            if ($nilaiTertinggiSkpd === null || $nilai > $nilaiTertinggiSkpd) {
                                $nilaiTertinggiSkpd = $nilai;
                                $indikatorTertinggi = $indikator;
                            }
                            if ($nilaiTerendahSkpd === null || $nilai < $nilaiTerendahSkpd) {
                                $nilaiTerendahSkpd = $nilai;
                                $indikatorTerendah = $indikator;
                            }
                        }
                    }

                    if ($nilaiTertinggiSkpd !== null && ($nilaiTertinggiGlobal === null || $nilaiTertinggiSkpd > $nilaiTertinggiGlobal)) {
                        $nilaiTertinggiGlobal = $nilaiTertinggiSkpd;
                        $programs = [];
                        $kegiatans = [];
                        $subkegiatans = [];
                        $relatedPrograms = $cascadingProgramMap[$indikatorTertinggi->refindikatorsasaranrenstra_id] ?? [];
                        foreach ($relatedPrograms as $p) {
                            $programs[] = $programNameMap[$p->refprogram_id] ?? 'Nama Program Tidak Ditemukan';
                        }
                        $relatedKegiatans = $cascadingKegiatanMap[$indikatorTertinggi->refindikatorsasaranrenstra_id] ?? [];
                        foreach ($relatedKegiatans as $k) {
                            $kegiatans[] = $kegiatanNameMap[$k->refkegiatan_id] ?? 'Nama Kegiatan Tidak Ditemukan';
                        }
                        $relatedSubkegiatans = $cascadingSubkegiatanMap[$indikatorTertinggi->refindikatorsasaranrenstra_id] ?? [];
                        foreach ($relatedSubkegiatans as $s) {
                            $subkegiatans[] = $subkegiatanNameMap[$s->refsubkegiatan_id] ?? 'Nama Sub Kegiatan Tidak Ditemukan';
                        }
                        $skpdTertinggi = [
                            'nama' => "Triwulan $triwulan_id - {$skpd->nama_skpd}",
                            'value' => $nilaiTertinggiSkpd,
                            'indikator' => $indikatorTertinggi->uraian_indikatorsasaranrenstra,
                            'programs' => array_unique($programs),
                            'kegiatans' => array_unique($kegiatans),
                            'subkegiatans' => array_unique($subkegiatans),
                        ];
                    }

                    if ($nilaiTerendahSkpd !== null && ($nilaiTerendahGlobal === null || $nilaiTerendahSkpd < $nilaiTerendahGlobal)) {
                        $nilaiTerendahGlobal = $nilaiTerendahSkpd;
                        $programs = [];
                        $kegiatans = [];
                        $subkegiatans = [];
                        $relatedPrograms = $cascadingProgramMap[$indikatorTerendah->refindikatorsasaranrenstra_id] ?? [];
                        foreach ($relatedPrograms as $p) {
                            $programs[] = $programNameMap[$p->refprogram_id] ?? 'Nama Program Tidak Ditemukan';
                        }
                        $relatedKegiatans = $cascadingKegiatanMap[$indikatorTerendah->refindikatorsasaranrenstra_id] ?? [];
                        foreach ($relatedKegiatans as $k) {
                            $kegiatans[] = $kegiatanNameMap[$k->refkegiatan_id] ?? 'Nama Kegiatan Tidak Ditemukan';
                        }
                        $relatedSubkegiatans = $cascadingSubkegiatanMap[$indikatorTerendah->refindikatorsasaranrenstra_id] ?? [];
                        foreach ($relatedSubkegiatans as $s) {
                            $subkegiatans[] = $subkegiatanNameMap[$s->refsubkegiatan_id] ?? 'Nama Sub Kegiatan Tidak Ditemukan';
                        }
                        $skpdTerendah = [
                            'nama' => "Triwulan $triwulan_id - {$skpd->nama_skpd}",
                            'value' => $nilaiTerendahSkpd,
                            'indikator' => $indikatorTerendah->uraian_indikatorsasaranrenstra,
                            'programs' => array_unique($programs),
                            'kegiatans' => array_unique($kegiatans),
                            'subkegiatans' => array_unique($subkegiatans),
                        ];
                    }
                }

                if ($skpdTertinggi !== null) $triwulanTertinggiPerSkpd[] = $skpdTertinggi;
                if ($skpdTerendah !== null) $triwulanTerendahPerSkpd[] = $skpdTerendah;
            }

            // --- DATA PROGRAM (Tabel Detail) ---
            // Fetch all programs with relations outside loop
            $programAllWithRelations = SakipCascadingProgram::find()
                ->where(['refperiode_id' => $refperiode_id])
                ->with([
                    'refProgram',
                    'indikatorCascadingPrograms.refIndikatorCascadingProgramTriwulan' => function ($query) use ($refperiode_id) {
                        $query->andWhere(['refperiode_id' => $refperiode_id])
                            ->orderBy(['reftriwulan_id' => SORT_ASC]);
                    }
                ])->all();
            $programBySkpd = ArrayHelper::index($programAllWithRelations, null, 'refskpd_id');

            // Fetch cascading kegiatans and subkegiatans with relations for mapping
            $cascadingKegiatansAllWithRef = SakipCascadingkegiatan::find()
                ->where(['refperiode_id' => $refperiode_id])
                ->with('refKegiatan')
                ->all();
            $cascadingSubkegiatansAllWithRef = SakipCascadingsubkegiatan::find()
                ->where(['refperiode_id' => $refperiode_id])
                ->with('refSubkegiatan')
                ->all();

            $cascadingKegiatanByProgram = ArrayHelper::index($cascadingKegiatansAllWithRef, null, 'refcascadingprogram_id');
            $cascadingSubkegiatanByKegiatan = ArrayHelper::index($cascadingSubkegiatansAllWithRef, null, 'refcascadingkegiatan_id');
            $cascadingSubkegiatanByProgram = ArrayHelper::index($cascadingSubkegiatansAllWithRef, null, 'refcascadingprogram_id');

            foreach ($processingSkpdList as $skpd) {
                $refskpd_id_loop = $skpd->refskpd_id;
                if ($refskpd_id_loop == 1) continue;

                $programList = $programBySkpd[$refskpd_id_loop] ?? [];
                $programDataForSkpd = [];
                foreach ($programList as $program) {
                    $indikatorData = [];
                    foreach ($program->indikatorCascadingPrograms as $indikator) {
                        $indikatorData[] = [
                            'id' => $indikator->refindikatorprogram_id,
                            'uraian_indikator' => $program->uraian_indikatorprogram ?? $indikator->uraian_indikatorprogram,
                            'triwulan' => $indikator->refIndikatorCascadingProgramTriwulan,
                            'program_satuan' => $program->program_satuan,
                        ];
                    }

                    $namaProgram = $program->refProgram->nama_program ?? 'Nama Program Tidak Ditemukan';
                    
                    // Sum program budget in memory
                    $totalAnggaranProgram = 0;
                    if (isset($cascadingSubkegiatanByProgram[$program->refcascadingprogram_id])) {
                        foreach ($cascadingSubkegiatanByProgram[$program->refcascadingprogram_id] as $subkeg) {
                            $totalAnggaranProgram += (float)$subkeg->subkegiatan_anggaran;
                        }
                    }

                    $relatedKegiatans = $cascadingKegiatanByProgram[$program->refcascadingprogram_id] ?? [];
                    $kegiatansWithDetails = [];
                    foreach ($relatedKegiatans as $keg) {
                        if (isset($keg->refKegiatan->nama_kegiatan)) {
                            // Sum kegiatan budget in memory
                            $totalAnggaranKegiatan = 0;
                            if (isset($cascadingSubkegiatanByKegiatan[$keg->refcascadingkegiatan_id])) {
                                foreach ($cascadingSubkegiatanByKegiatan[$keg->refcascadingkegiatan_id] as $subkeg) {
                                    $totalAnggaranKegiatan += (float)$subkeg->subkegiatan_anggaran;
                                }
                            }

                            $subkegiatanDetails = [];
                            $relatedSubkegiatans = $cascadingSubkegiatanByKegiatan[$keg->refcascadingkegiatan_id] ?? [];
                            foreach ($relatedSubkegiatans as $subkeg) {
                                if (isset($subkeg->refSubkegiatan->nama_subkegiatan)) {
                                    $subkegiatanDetails[] = [
                                        'name' => $subkeg->refSubkegiatan->nama_subkegiatan,
                                        'budget' => (float)$subkeg->subkegiatan_anggaran
                                    ];
                                }
                            }

                            $kegiatansWithDetails[] = [
                                'name' => $keg->refKegiatan->nama_kegiatan,
                                'budget' => $totalAnggaranKegiatan,
                                'subkegiatans' => $subkegiatanDetails
                            ];
                        }
                    }

                    $programDataForSkpd[] = [
                        'uraian_program' => $program->uraian_sasaranprogram,
                        'nama_program' => $namaProgram,
                        'total_anggaran' => $totalAnggaranProgram,
                        'kegiatans' => $kegiatansWithDetails,
                        'indikator' => $indikatorData,
                    ];
                }
                if (!empty($programDataForSkpd)) {
                    $dataProgram[$refskpd_id_loop] = $programDataForSkpd;
                }
            }

            // --- CHART PROGRAM (Tertinggi/Terendah) ---
            $capaianProgramAll = SakipIndikatorcascadingprogramTriwulan::find()->where(['refperiode_id' => $refperiode_id])->all();
            $indikatorProgramAll = SakipIndikatorcascadingprogram::find()->where(['refperiode_id' => $refperiode_id])->all();
            $cascadingProgramAll = SakipCascadingProgram::find()->where(['refperiode_id' => $refperiode_id])->all();
            $cascadingKegiatanAll = SakipCascadingkegiatan::find()->where(['refperiode_id' => $refperiode_id])->all();
            $cascadingSubkegiatanAll = SakipCascadingsubkegiatan::find()->where(['refperiode_id' => $refperiode_id])->all();

            $skpdMap = ArrayHelper::map($processingSkpdList, 'refskpd_id', 'nama_skpd');
            $indikatorProgramMap = ArrayHelper::map($indikatorProgramAll, 'refindikatorprogram_id', fn($m) => $m);
            $cascadingProgramMap = ArrayHelper::map($cascadingProgramAll, 'refcascadingprogram_id', fn($m) => $m);
            $cascadingKegiatanMap = ArrayHelper::index($cascadingKegiatanAll, null, 'refcascadingprogram_id');
            $cascadingSubkegiatanMap = ArrayHelper::index($cascadingSubkegiatanAll, null, 'refcascadingkegiatan_id');

            foreach (range(1, 4) as $triwulan_id) {
                $nilaiTertinggi = null;
                $nilaiTerendah = null;
                $programTertinggi = null;
                $programTerendah = null;

                foreach ($capaianProgramAll as $capaian) {
                    if ((int)$capaian->reftriwulan_id !== $triwulan_id || $capaian->triwulan_capaian <= 0) continue;

                    $indikator = $indikatorProgramMap[$capaian->refindikatorprogram_id] ?? null;
                    if (!$indikator) continue;

                    $cascadingProgram = $cascadingProgramMap[$indikator->refcascadingprogram_id] ?? null;
                    if (!$cascadingProgram || $cascadingProgram->refskpd_id == 1) continue;

                    $namaSkpd = $skpdMap[$cascadingProgram->refskpd_id] ?? '?';
                    $nilai = (float)$capaian->triwulan_capaian;
                    $uraianIndikator = $cascadingProgram->uraian_indikatorprogram;
                    $namaProgram = $programNameMap[$cascadingProgram->refprogram_id] ?? '?';

                    $kegiatanList = [];
                    $subkegiatanList = [];
                    $relatedKegiatans = $cascadingKegiatanMap[$cascadingProgram->refcascadingprogram_id] ?? [];
                    foreach ($relatedKegiatans as $keg) {
                        $kegiatanList[] = $kegiatanNameMap[$keg->refkegiatan_id] ?? '?';
                        $relatedSubkegiatans = $cascadingSubkegiatanMap[$keg->refcascadingkegiatan_id] ?? [];
                        foreach ($relatedSubkegiatans as $subkeg) {
                            $subkegiatanList[] = $subkegiatanNameMap[$subkeg->refsubkegiatan_id] ?? '?';
                        }
                    }

                    $dataPoint = [
                        'nama' => "Triwulan $triwulan_id - $namaSkpd",
                        'value' => $nilai,
                        'indikator' => $uraianIndikator,
                        'program_name' => $namaProgram,
                        'kegiatans' => array_unique($kegiatanList),
                        'subkegiatans' => array_unique($subkegiatanList),
                    ];

                    if ($nilaiTertinggi === null || $nilai > $nilaiTertinggi) {
                        $nilaiTertinggi = $nilai;
                        $programTertinggi = $dataPoint;
                    }
                    if ($nilaiTerendah === null || $nilai < $nilaiTerendah) {
                        $nilaiTerendah = $nilai;
                        $programTerendah = $dataPoint;
                    }
                }
                if ($programTertinggi !== null) $triwulanTertinggiProgram[] = $programTertinggi;
                if ($programTerendah !== null) $triwulanTerendahProgram[] = $programTerendah;
            }

            // --- DATA KEGIATAN (Tabel Detail) ---
            // Fetch all kegiatans with relations outside loop to avoid N+1 query
            $kegiatanAllWithRelations = SakipCascadingkegiatan::find()
                ->where(['refperiode_id' => $refperiode_id])
                ->with([
                    'refKegiatan',
                    'indikatorCascadingKegiatan.refIndikatorCascadingKegiatanTriwulan' => function ($query) use ($refperiode_id) {
                        $query->andWhere(['refperiode_id' => $refperiode_id])
                            ->orderBy(['reftriwulan_id' => SORT_ASC]);
                    }
                ])->all();
            $kegiatanBySkpd = ArrayHelper::index($kegiatanAllWithRelations, null, 'refskpd_id');

            foreach ($processingSkpdList as $skpd) {
                $refskpd_id_loop = $skpd->refskpd_id;
                if ($refskpd_id_loop == 1) continue;

                $kegiatanList = $kegiatanBySkpd[$refskpd_id_loop] ?? [];
                $kegiatanDataForSkpd = [];
                foreach ($kegiatanList as $kegiatan) {
                    $indikatorData = [];
                    foreach ($kegiatan->indikatorCascadingKegiatan as $indikator) {
                        $indikatorData[] = [
                            'id' => $indikator->refindikatorkegiatan_id,
                            'uraian_indikator' => $kegiatan->uraian_indikatorkegiatan,
                            'triwulan' => $indikator->refIndikatorCascadingKegiatanTriwulan,
                            'kegiatan_satuan' => $kegiatan->kegiatan_satuan,
                        ];
                    }

                    $namaKegiatan = $kegiatan->refKegiatan->nama_kegiatan ?? 'Nama Kegiatan Tidak Ditemukan';
                    
                    // Sum kegiatan budget in memory
                    $totalAnggaranKegiatan = 0;
                    if (isset($cascadingSubkegiatanByKegiatan[$kegiatan->refcascadingkegiatan_id])) {
                        foreach ($cascadingSubkegiatanByKegiatan[$kegiatan->refcascadingkegiatan_id] as $subkeg) {
                            $totalAnggaranKegiatan += (float)$subkeg->subkegiatan_anggaran;
                        }
                    }

                    $subkegiatanDetails = [];
                    $relatedSubkegiatans = $cascadingSubkegiatanByKegiatan[$kegiatan->refcascadingkegiatan_id] ?? [];
                    foreach ($relatedSubkegiatans as $subkeg) {
                        if (isset($subkeg->refSubkegiatan->nama_subkegiatan)) {
                            $subkegiatanDetails[] = [
                                'name' => $subkeg->refSubkegiatan->nama_subkegiatan,
                                'budget' => (float)$subkeg->subkegiatan_anggaran
                            ];
                        }
                    }

                    $kegiatanDataForSkpd[] = [
                        'uraian_kegiatan' => $kegiatan->uraian_sasarankegiatan,
                        'nama_kegiatan' => $namaKegiatan,
                        'total_anggaran' => $totalAnggaranKegiatan,
                        'subkegiatans' => $subkegiatanDetails,
                        'indikator' => $indikatorData,
                    ];
                }
                if (!empty($kegiatanDataForSkpd)) {
                    $dataKegiatan[$refskpd_id_loop] = $kegiatanDataForSkpd;
                }
            }

            // --- CHART KEGIATAN ---
            $capaianKegiatanAll = SakipIndikatorcascadingkegiatanTriwulan::find()->where(['refperiode_id' => $refperiode_id])->all();
            $cascadingSubkegiatanAll = SakipCascadingsubkegiatan::find()->where(['refperiode_id' => $refperiode_id])->all();
            $indikatorKegiatanMapData = SakipIndikatorcascadingkegiatan::find()->where(['refperiode_id' => $refperiode_id])->all();
            $cascadingKegiatanMapData = SakipCascadingkegiatan::find()->where(['refperiode_id' => $refperiode_id])->all();

            $indikatorKegiatanMap = ArrayHelper::map($indikatorKegiatanMapData, 'refindikatorkegiatan_id', fn($m) => $m);
            $cascadingKegiatanMap = ArrayHelper::map($cascadingKegiatanMapData, 'refcascadingkegiatan_id', fn($m) => $m);
            $cascadingSubkegiatanMap = ArrayHelper::index($cascadingSubkegiatanAll, null, 'refcascadingkegiatan_id');

            foreach (range(1, 4) as $triwulan_id) {
                $nilaiTertinggi = null;
                $nilaiTerendah = null;
                $kegiatanTertinggi = null;
                $kegiatanTerendah = null;

                foreach ($capaianKegiatanAll as $capaian) {
                    if ((int)$capaian->reftriwulan_id !== $triwulan_id || $capaian->triwulan_capaian <= 0) continue;

                    $indikator = $indikatorKegiatanMap[$capaian->refindikatorkegiatan_id] ?? null;
                    if (!$indikator) continue;

                    $cascadingKegiatan = $cascadingKegiatanMap[$indikator->refcascadingkegiatan_id] ?? null;
                    if (!$cascadingKegiatan || $cascadingKegiatan->refskpd_id == 1) continue;

                    $namaSkpd = $skpdMap[$cascadingKegiatan->refskpd_id] ?? '?';
                    $nilai = (float)$capaian->triwulan_capaian;
                    $uraianIndikator = $cascadingKegiatan->uraian_indikatorkegiatan;
                    $namaKegiatan = $kegiatanNameMap[$cascadingKegiatan->refkegiatan_id] ?? '?';

                    $subkegiatanList = [];
                    $relatedSubkegiatans = $cascadingSubkegiatanMap[$cascadingKegiatan->refcascadingkegiatan_id] ?? [];
                    foreach ($relatedSubkegiatans as $subkeg) {
                        $subkegiatanList[] = $subkegiatanNameMap[$subkeg->refsubkegiatan_id] ?? '?';
                    }

                    $dataPoint = [
                        'nama' => "Triwulan $triwulan_id - $namaSkpd",
                        'value' => $nilai,
                        'kegiatan_name' => $namaKegiatan,
                        'indikator' => $uraianIndikator,
                        'subkegiatans' => array_unique($subkegiatanList),
                    ];

                    if ($nilaiTertinggi === null || $nilai > $nilaiTertinggi) {
                        $nilaiTertinggi = $nilai;
                        $kegiatanTertinggi = $dataPoint;
                    }
                    if ($nilaiTerendah === null || $nilai < $nilaiTerendah) {
                        $nilaiTerendah = $nilai;
                        $kegiatanTerendah = $dataPoint;
                    }
                }
                if ($kegiatanTertinggi !== null) $triwulanTertinggiKegiatan[] = $kegiatanTertinggi;
                if ($kegiatanTerendah !== null) $triwulanTerendahKegiatan[] = $kegiatanTerendah;
            }

            // --- DATA SUBKEGIATAN (Tabel Detail) ---
            // Fetch all subkegiatans with relations outside loop
            $subkegiatanAllWithRelations = SakipCascadingsubkegiatan::find()
                ->where(['refperiode_id' => $refperiode_id])
                ->with([
                    'refSubkegiatan',
                    'refIndikatorcascadingsubkegiatan.refIndikatorCascadingSubkegiatanTriwulan' => function ($query) use ($refperiode_id) {
                        $query->andWhere(['refperiode_id' => $refperiode_id])
                            ->orderBy(['reftriwulan_id' => SORT_ASC]);
                    }
                ])->all();
            $subkegiatanBySkpd = ArrayHelper::index($subkegiatanAllWithRelations, null, 'refskpd_id');

            foreach ($processingSkpdList as $skpd) {
                $refskpd_id_loop = $skpd->refskpd_id;
                if ($refskpd_id_loop == 1) continue;

                $subkegiatanList = $subkegiatanBySkpd[$refskpd_id_loop] ?? [];
                $subkegiatanDataForSkpd = [];
                foreach ($subkegiatanList as $subkegiatan) {
                    $indikatorData = [];
                    foreach ($subkegiatan->refIndikatorcascadingsubkegiatan as $indikator) {
                        $indikatorData[] = [
                            'id' => $indikator->refindikatorsubkegiatan_id,
                            'uraian_indikator' => $subkegiatan->uraian_indikatorsubkegiatan,
                            'triwulan' => $indikator->refIndikatorCascadingSubkegiatanTriwulan,
                            'subkegiatan_satuan' => $subkegiatan->subkegiatan_satuan,
                        ];
                    }

                    $namaSubkegiatan = $subkegiatan->refSubkegiatan->nama_subkegiatan ?? 'Nama Sub Kegiatan Tidak Ditemukan';
                    $totalAnggaranSubkegiatan = (float) $subkegiatan->subkegiatan_anggaran;

                    $subkegiatanDataForSkpd[] = [
                        'uraian_subkegiatan' => $subkegiatan->uraian_sasaransubkegiatan,
                        'nama_subkegiatan' => $namaSubkegiatan,
                        'total_anggaran' => $totalAnggaranSubkegiatan,
                        'indikator' => $indikatorData,
                    ];
                }
                if (!empty($subkegiatanDataForSkpd)) {
                    $dataSubkegiatan[$refskpd_id_loop] = $subkegiatanDataForSkpd;
                }
            }

            // --- CHART SUBKEGIATAN ---
            $capaianSubkegiatanAll = SakipIndikatorcascadingsubkegiatanTriwulan::find()->where(['refperiode_id' => $refperiode_id])->all();
            $indikatorSubkegiatanMap = ArrayHelper::map(SakipIndikatorcascadingsubkegiatan::find()->where(['refperiode_id' => $refperiode_id])->all(), 'refindikatorsubkegiatan_id', fn($m) => $m);
            $cascadingSubkegiatanMap = ArrayHelper::map(SakipCascadingsubkegiatan::find()->where(['refperiode_id' => $refperiode_id])->all(), 'refcascadingsubkegiatan_id', fn($m) => $m);

            foreach (range(1, 4) as $triwulan_id) {
                $nilaiTertinggi = null;
                $nilaiTerendah = null;
                $subkegiatanTertinggi = null;
                $subkegiatanTerendah = null;

                foreach ($capaianSubkegiatanAll as $capaian) {
                    if ((int)$capaian->reftriwulan_id !== $triwulan_id || $capaian->triwulan_capaian <= 0) continue;

                    $indikator = $indikatorSubkegiatanMap[$capaian->refindikatorsubkegiatan_id] ?? null;
                    if (!$indikator) continue;

                    $cascadingSubkegiatan = $cascadingSubkegiatanMap[$indikator->refcascadingsubkegiatan_id] ?? null;
                    if (!$cascadingSubkegiatan || $cascadingSubkegiatan->refskpd_id == 1) continue;

                    $namaSkpd = $skpdMap[$cascadingSubkegiatan->refskpd_id] ?? '?';
                    $nilai = (float)$capaian->triwulan_capaian;
                    $uraianIndikator = $cascadingSubkegiatan->uraian_indikatorsubkegiatan;
                    $namaSubkegiatan = $subkegiatanNameMap[$cascadingSubkegiatan->refsubkegiatan_id] ?? 'Sub Kegiatan Tidak Ditemukan';

                    $dataPoint = [
                        'nama' => "Triwulan $triwulan_id - $namaSkpd",
                        'value' => $nilai,
                        'subkegiatan_name' => $namaSubkegiatan,
                        'indikator' => $uraianIndikator,
                    ];

                    if ($nilaiTertinggi === null || $nilai > $nilaiTertinggi) {
                        $nilaiTertinggi = $nilai;
                        $subkegiatanTertinggi = $dataPoint;
                    }
                    if ($nilaiTerendah === null || $nilai < $nilaiTerendah) {
                        $nilaiTerendah = $nilai;
                        $subkegiatanTerendah = $dataPoint;
                    }
                }
                if ($subkegiatanTertinggi !== null) $triwulanTertinggiSubkegiatan[] = $subkegiatanTertinggi;
                if ($subkegiatanTerendah !== null) $triwulanTerendahSubkegiatan[] = $subkegiatanTerendah;
            }

            // --- PERHITUNGAN RANKING OPD ---
            $capaianProgramAllDesc = SakipIndikatorcascadingprogramTriwulan::find()
                ->where(['refperiode_id' => $refperiode_id])
                ->andWhere(['>', 'triwulan_capaian', 0])
                ->orderBy(['reftriwulan_id' => SORT_DESC])
                ->all();

            $indikatorSasaranRenstraAll = SakipIndikatorsasaranrenstra::find()->where(['refperiode_id' => $refperiode_id])->all();
            $penyerapanAnggaranAll = SakipIndikatorcascadingsubkegiatanTriwulan::find()
                ->where(['refperiode_id' => $refperiode_id])
                ->andWhere(['is not', 'triwulan_penyerapan_anggaran', null])
                ->all();

            $uraianSasaranRenstraMap = ArrayHelper::map($indikatorSasaranRenstraAll, 'refindikatorsasaranrenstra_id', 'uraian_indikatorsasaranrenstra');
            $capaianPerIndikator = ArrayHelper::index($capaianProgramAllDesc, null, 'refindikatorprogram_id');

            $skorPerIndikator = [];
            foreach ($capaianPerIndikator as $indikatorId => $capaianTriwulans) {
                $indikatorProgram = $indikatorProgramMap[$indikatorId] ?? null;
                if (!$indikatorProgram) continue;

                $skorIndikator = 0;
                $refIndikatorSasaranRenstraId = $indikatorProgram->refindikatorsasaranrenstra_id;
                $uraian = $uraianSasaranRenstraMap[$refIndikatorSasaranRenstraId] ?? '';
                $normalizedUraian = str_replace(' ', '', strtolower($uraian));

                if (str_contains($normalizedUraian, 'lheakip') || str_contains($normalizedUraian, 'indekskepuasanmasyarakat')) {
                    if (!empty($capaianTriwulans)) {
                        $skorIndikator = (float)$capaianTriwulans[0]->triwulan_capaian;
                    }
                } else {
                    foreach ($capaianTriwulans as $capaian) {
                        $skorIndikator += (float)$capaian->triwulan_capaian;
                    }
                }

                if ($skorIndikator > 0) {
                    $skorPerIndikator[$indikatorId] = $skorIndikator;
                }
            }

            $skpdScores = [];
            foreach ($skorPerIndikator as $indikatorId => $skor) {
                $indikatorProgram = $indikatorProgramMap[$indikatorId] ?? null;
                if (!$indikatorProgram) continue;

                $skpdId = $indikatorProgram->refskpd_id;
                if ($skpdId == 1) continue;

                if (!isset($skpdScores[$skpdId])) {
                    $skpdScores[$skpdId] = ['total_skor' => 0, 'jumlah_indikator' => 0];
                }
                $skpdScores[$skpdId]['total_skor'] += $skor;
                $skpdScores[$skpdId]['jumlah_indikator']++;
            }

            $skpdAnggaranScores = [];
            foreach ($penyerapanAnggaranAll as $serapan) {
                $skpdId = $serapan->refskpd_id;
                if ($skpdId == 1) continue;

                if (!isset($skpdAnggaranScores[$skpdId])) {
                    $skpdAnggaranScores[$skpdId] = ['total_penyerapan' => 0, 'jumlah_data' => 0];
                }
                $skpdAnggaranScores[$skpdId]['total_penyerapan'] += (float)$serapan->triwulan_penyerapan_anggaran;
                $skpdAnggaranScores[$skpdId]['jumlah_data']++;
            }

            $skpdListForRanking = [];
            foreach ($skpdMap as $skpdId => $namaSkpd) {
                if ($skpdId == 1) continue;

                $avgSkor = 0;
                if (isset($skpdScores[$skpdId]) && $skpdScores[$skpdId]['jumlah_indikator'] > 0) {
                    $avgSkor = $skpdScores[$skpdId]['total_skor'] / $skpdScores[$skpdId]['jumlah_indikator'];
                }

                $avgAnggaran = 0;
                if (isset($skpdAnggaranScores[$skpdId]) && $skpdAnggaranScores[$skpdId]['jumlah_data'] > 0) {
                    $avgAnggaran = $skpdAnggaranScores[$skpdId]['total_penyerapan'] / $skpdAnggaranScores[$skpdId]['jumlah_data'];
                }

                $skpdListForRanking[] = [
                    'nama_skpd' => $namaSkpd,
                    'skor' => $avgSkor,
                    'anggaran' => $avgAnggaran,
                ];
            }

            usort($skpdListForRanking, function ($a, $b) {
                if ($b['skor'] != $a['skor']) {
                    return $b['skor'] <=> $a['skor'];
                }
                return $b['anggaran'] <=> $a['anggaran'];
            });

            foreach ($skpdListForRanking as $index => $rankedSkpd) {
                $opdRanking[] = [
                    'rank' => $index + 1,
                    'nama_skpd' => $rankedSkpd['nama_skpd'],
                    'skor' => $rankedSkpd['skor'],
                    'anggaran' => $rankedSkpd['anggaran'],
                ];
            }
        } // <--- AKHIR BLOK IF ($refperiode_id !== null)

        return $this->render('portal-publik-tabulasi', [
            'target_page' => $target_page,
            'periodeList' => $periodeList,
            'selectedPeriodId' => $refperiode_id,
            'refperiode_id' => $refperiode_id,
            'selectedPeriodValue' => $selectedPeriodValue,
            'skpdList' => $allSkpdList,
            'displayedSkpdList' => $processingSkpdList,
            'selectedSkpdId' => $refskpd_id,
            'dataRenstra' => $dataRenstra,
            'triwulanTerendahPerSkpd' => $triwulanTerendahPerSkpd,
            'triwulanTertinggiPerSkpd' => $triwulanTertinggiPerSkpd,
            'triwulanTerendahProgram' => $triwulanTerendahProgram,
            'triwulanTertinggiProgram' => $triwulanTertinggiProgram,
            'triwulanTertinggiKegiatan' => $triwulanTertinggiKegiatan,
            'triwulanTerendahKegiatan' => $triwulanTerendahKegiatan,
            'triwulanTertinggiSubkegiatan' => $triwulanTertinggiSubkegiatan,
            'triwulanTerendahSubkegiatan' => $triwulanTerendahSubkegiatan,
            'dataProgram' => $dataProgram,
            'dataKegiatan' => $dataKegiatan,
            'dataSubkegiatan' => $dataSubkegiatan,
            'opdRanking' => $opdRanking,
        ]);
    }




    public function actionPortalPublikPerencanaan($refperiode_id = null, $target_page = null)
    {
        $this->layout = 'main-portal';

        // Redirect to the selected target page if set
        if ($target_page && $refperiode_id) {
            return $this->redirect([$target_page, 'refperiode_id' => $refperiode_id]);
        }

        // Set default period to this year if not provided
        if ($refperiode_id === null) {
            $currentYear = date('Y');
            $defaultPeriod = SakipPeriode::find()->where(['periode' => $currentYear])->one();
            $refperiode_id = $defaultPeriod ? $defaultPeriod->refperiode_id : null;
        }

        // Fetch all periods
        $periodeList = SakipPeriode::find()->all();

        // Fetch SKPDs
        $skpdList = SakipSkpd::find()->all();

        // Retrieve the periode based on refperiode_id
        $selectedPeriod = SakipPeriode::find()->where(['refperiode_id' => $refperiode_id])->one();
        $refperiode_5tahun_id = $selectedPeriod ? $selectedPeriod->refperiode_5tahun_id : null;
        $selectedPeriodValue = $selectedPeriod ? $selectedPeriod->periode : null; // Get the periode value

        // Fetch all Sasaran Renstra for the selected period
        $allSasaranRenstra = SakipSasaranrenstra::find()
            ->where(['refperiode_5tahun_id' => $refperiode_5tahun_id])
            ->with(['refVisi', 'refMisi', 'refTujuan', 'refSasaran'])
            ->all();

        // Map Sasaran Renstra by SKPD ID for fast lookup
        $sasaranBySkpd = ArrayHelper::index($allSasaranRenstra, null, 'refskpd_id');

        // Map Sasaran Renstra by ID for fast formulation lookup
        $sasaranMap = ArrayHelper::index($allSasaranRenstra, 'refsasaranrenstra_id');

        // Fetch all Strategi for the selected period
        $allStrategi = SakipStrategi::find()
            ->where(['refperiode_5tahun_id' => $refperiode_5tahun_id])
            ->all();
        $strategiBySkpd = ArrayHelper::index($allStrategi, null, 'refskpd_id');

        // Fetch all Kebijakan for the selected period
        $allKebijakan = SakipKebijakan::find()
            ->where(['refperiode_5tahun_id' => $refperiode_5tahun_id])
            ->all();
        $kebijakanBySkpd = ArrayHelper::index($allKebijakan, null, 'refskpd_id');

        // Fetch all IKU Indicators for the selected period
        $allIndikatorsIku = SakipIndikatorsasaranrenstra::find()
            ->where(['refperiode_id' => $refperiode_id])
            ->all();
        $indikatorsIkuBySkpd = ArrayHelper::index($allIndikatorsIku, null, 'refskpd_id');

        // Fetch data for _view-renstra
        $sasaranRenstraList = [];
        $strategiList = [];
        $kebijakanList = [];
        $namaSkpdList = []; // To store SKPD names for each ID
        $indikatorsIkuList = [];
        $sasaranRenstraIkuList = [];
        $sasaranRenstraRktList = [];
        $sasaranRenstraPkList = [];
        $sasaranRenstraPkpList = [];

        foreach ($skpdList as $skpd) {
            $refskpd_id = $skpd->refskpd_id;
            
            $sasaranRenstraList[$refskpd_id] = $sasaranBySkpd[$refskpd_id] ?? [];
            $strategiList[$refskpd_id] = $strategiBySkpd[$refskpd_id] ?? [];
            $kebijakanList[$refskpd_id] = $kebijakanBySkpd[$refskpd_id] ?? [];
            $namaSkpdList[$refskpd_id] = $skpd->nama_skpd; // Store SKPD name

            // Fetch indicators for IKU
            $indikatorsIkuList[$refskpd_id] = $indikatorsIkuBySkpd[$refskpd_id] ?? [];

            // Prepare the sasaran for IKU with their formulations
            $sasaranRenstraIkuList[$refskpd_id] = [];
            foreach ($indikatorsIkuList[$refskpd_id] as $indikator) {
                $sasaran = $sasaranMap[$indikator->refsasaranrenstra_id] ?? null;
                if ($sasaran) {
                    $sasaranRenstraIkuList[$refskpd_id][] = [
                        'indikator' => $indikator,
                        'formulasi' => $sasaran->formulasi_sasaranrenstra,
                    ];
                }
            }

            // Map pre-fetched lists
            $sasaranRenstraRktList[$refskpd_id] = $sasaranBySkpd[$refskpd_id] ?? [];
            $sasaranRenstraPkList[$refskpd_id] = $sasaranBySkpd[$refskpd_id] ?? [];
            $sasaranRenstraPkpList[$refskpd_id] = $sasaranBySkpd[$refskpd_id] ?? [];
        }

        return $this->render('portal-publik-perencanaan', [
            'periodeList' => $periodeList,
            'selectedPeriodId' => $refperiode_id,
            'refperiode_id' => $refperiode_id,
            'selectedPeriodValue' => $selectedPeriodValue, // Include selected period value
            'skpdList' => $skpdList,
            'sasaranRenstraList' => $sasaranRenstraList,
            'strategiList' => $strategiList,
            'kebijakanList' => $kebijakanList,
            'namaSkpdList' => $namaSkpdList, // Pass SKPD names
            'indikatorsIkuList' => $indikatorsIkuList,
            'sasaranRenstraIkuList' => $sasaranRenstraIkuList,
            'sasaranRenstraRktList' => $sasaranRenstraRktList,
            'sasaranRenstraPkList' => $sasaranRenstraPkList,
            'sasaranRenstraPkpList' => $sasaranRenstraPkpList,
        ]);
    }


    public function actionPortalPublikCapkin($refperiode_id = null, $target_page = null)
    {
        $this->layout = 'main-portal';

        // Redirect to the selected target page if set
        if ($target_page && $refperiode_id) {
            return $this->redirect([$target_page, 'refperiode_id' => $refperiode_id]);
        }

        // Set default period to this year if not provided
        if ($refperiode_id === null) {
            $currentYear = date('Y');
            $defaultPeriod = SakipPeriode::find()->where(['periode' => $currentYear])->one();
            $refperiode_id = $defaultPeriod ? $defaultPeriod->refperiode_id : null;
        }

        // Fetch all periods
        $periodeList = SakipPeriode::find()->all();

        // Fetch SKPDs
        $skpdList = SakipSkpd::find()->all();

        // Retrieve the periode based on refperiode_id
        $selectedPeriod = SakipPeriode::find()->where(['refperiode_id' => $refperiode_id])->one();
        $selectedPeriodValue = $selectedPeriod ? $selectedPeriod->periode : null; // Get the periode value

        // Fetch all IKU Indicators for the selected period
        $allIndikators = SakipIndikatorSasaranRenstra::find()
            ->andWhere(['refperiode_id' => $refperiode_id])
            ->with('refSasaranrenstra')
            ->all();

        // Group indicators by SKPD ID
        $indikatorsBySkpd = ArrayHelper::index($allIndikators, null, 'refskpd_id');

        // Fetch all Triwulan Data for the selected period
        $allTriwulanData = SakipIndikatorSasaranRenstraTriwulan::find()
            ->where(['refperiode_id' => $refperiode_id])
            ->all();

        // Map triwulan data by a compound key (skpd-sasaran-indicator)
        $triwulanDataMap = [];
        foreach ($allTriwulanData as $tw) {
            $key = $tw->refskpd_id . '-' . $tw->refsasaranrenstra_id . '-' . $tw->refindikatorsasaranrenstra_id;
            $triwulanDataMap[$key][] = $tw;
        }

        // Fetch data for _view-renstra
        $namaSkpdList = []; // To store SKPD names for each ID
        $indikatorsCapkinUtamaList = [];
        $indikatorsCapkinStrategiList = [];

        foreach ($skpdList as $skpd) {
            $refskpd_id = $skpd->refskpd_id;
            $namaSkpdList[$refskpd_id] = $skpd->nama_skpd; // Store SKPD name
            
            // Assign the pre-fetched indicators
            $indikatorsCapkinUtamaList[$refskpd_id] = $indikatorsBySkpd[$refskpd_id] ?? [];
            $indikatorsCapkinStrategiList[$refskpd_id] = $indikatorsBySkpd[$refskpd_id] ?? [];
        }

        return $this->render('portal-publik-capkin', [
            'periodeList' => $periodeList,
            'selectedPeriodId' => $refperiode_id,
            'refperiode_id' => $refperiode_id,
            'selectedPeriodValue' => $selectedPeriodValue, // Include selected period value
            'skpdList' => $skpdList,
            'namaSkpdList' => $namaSkpdList, // Pass SKPD names
            'indikatorsCapkinUtamaList' => $indikatorsCapkinUtamaList,
            'indikatorsCapkinStrategiList' => $indikatorsCapkinStrategiList,
            'triwulanDataMap' => $triwulanDataMap, // Pass the triwulan data map to view
        ]);
    }

    public function actionPortalPublikEvaluasiRenja($refperiode_id = null, $refskpd_id = null, $tahun = null)
    {
        $this->layout = 'main-portal';

        // Jika tahun tidak dipilih, default ke periode dari refperiode_id atau tahun sekarang
        if ($tahun === null) {
            if ($refperiode_id) {
                $periode = SakipPeriode::find()->where(['refperiode_id' => $refperiode_id])->one();
                if ($periode) {
                    $tahun = $periode->periode;
                }
            }
            if ($tahun === null) {
                $tahun = date('Y');
            }
        }

        $skpdList = SakipSkpd::find()->where(['skpd_isaktif' => 'T'])->orderBy('nama_skpd ASC')->all();
        $periodeList = SakipPeriode::find()->all();

        $totalPaguProgram = 0;
        $totalPaguKegiatan = 0;
        $totalPaguSubkegiatan = 0;

        // Hitung total pagu (target_renja_anggaran)
        $queryProgram = SakipEvaluasiRenja::find()->where(['tahun' => $tahun, 'level' => 'program']);
        $queryKegiatan = SakipEvaluasiRenja::find()->where(['tahun' => $tahun, 'level' => 'kegiatan']);
        $querySubKegiatan = SakipEvaluasiRenja::find()->where(['tahun' => $tahun, 'level' => 'sub_kegiatan']);

        if (!empty($refskpd_id)) {
            $queryProgram->andWhere(['refskpd_id' => $refskpd_id]);
            $queryKegiatan->andWhere(['refskpd_id' => $refskpd_id]);
            $querySubKegiatan->andWhere(['refskpd_id' => $refskpd_id]);
        }

        $totalPaguProgram = $queryProgram->sum('target_renja_anggaran') ?: 0;
        $totalPaguKegiatan = $queryKegiatan->sum('target_renja_anggaran') ?: 0;
        $totalPaguSubkegiatan = $querySubKegiatan->sum('target_renja_anggaran') ?: 0;

        return $this->render('portal-publik-evaluasi-renja', [
            'tahun' => $tahun,
            'refskpd_id' => $refskpd_id,
            'skpdList' => $skpdList,
            'periodeList' => $periodeList,
            'refperiode_id' => $refperiode_id,
            'totalPaguProgram' => $totalPaguProgram,
            'totalPaguKegiatan' => $totalPaguKegiatan,
            'totalPaguSubkegiatan' => $totalPaguSubkegiatan,
        ]);
    }

    public function actionPortalDokumenKegiatan()
    {
        $this->layout = 'main-portal';

        // Ambil keyword pencarian
        $searchKeyword = Yii::$app->request->get('searchKeyword', '');

        // Jika ada kata kunci pencarian, lakukan query
        $dokumenList = [];
        if (!empty($searchKeyword)) {
            $dokumenList = SimonaKeluaranmediacascadingkegiatan::find()
                ->andWhere(['like', 'nama_file', $searchKeyword])
                ->all();
        }

        if (Yii::$app->request->isAjax) {
            // Jika request adalah AJAX, return hasil pencarian dalam format JSON
            return $this->asJson([
                'dokumenList' => $dokumenList,
                'searchKeyword' => $searchKeyword
            ]);
        }

        return $this->render('portal-dokumen-kegiatan', [
            'dokumenList' => $dokumenList,
            'searchKeyword' => $searchKeyword, // Kirim kembali keyword ke view
        ]);
    }


    public function actionPortalDokumenSubkegiatan()
    {
        $this->layout = 'main-portal';

        // Ambil keyword pencarian
        $searchKeyword = Yii::$app->request->get('searchKeyword', '');

        // Jika ada kata kunci pencarian, lakukan query
        $dokumenList = [];
        if (!empty($searchKeyword)) {
            $dokumenList = SimonaKeluaranmediacascadingsubkegiatan::find()
                ->andWhere(['like', 'nama_file', $searchKeyword])
                ->all();
        }

        if (Yii::$app->request->isAjax) {
            // Jika request adalah AJAX, return hasil pencarian dalam format JSON
            return $this->asJson([
                'dokumenList' => $dokumenList,
                'searchKeyword' => $searchKeyword
            ]);
        }

        return $this->render('portal-dokumen-subkegiatan', [
            'dokumenList' => $dokumenList,
            'searchKeyword' => $searchKeyword, // Kirim kembali keyword ke view
        ]);
    }


    /**
     * Logs in a user.
     *
     * @return mixed
     */
    // public function actionLogin()
    // {
    //     if (!Yii::$app->user->isGuest) {
    //         // Mengalihkan pengguna yang sudah login ke index
    //         return $this->redirect(['index-main']);
    //     }

    //     $this->layout = 'blank';

    //     $model = new LoginForm();
    //     if ($model->load(Yii::$app->request->post()) && $model->login()) {
    //         // Mengalihkan pengguna ke index setelah berhasil login
    //         return $this->redirect(['index-main']);
    //     }

    //     $model->password = '';

    //     return $this->render('login', [
    //         'model' => $model,
    //     ]);
    // }

    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->redirect(['index-main']);
        }

        $this->layout = 'blank';
        $model = new LoginForm();

        if (Yii::$app->request->isPost) {
            if ($model->load(Yii::$app->request->post()) && $model->login()) {
                Yii::$app->session->setFlash('success', 'Login berhasil! Anda akan dialihkan ke Pilihan Dashboard dalam 5 detik.');
            } else {
                Yii::$app->session->setFlash('error', 'Username atau password salah!');
            }
        }

        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logs out the current user.
     *
     * @return mixed
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        // Mengalihkan pengguna ke portal setelah logout
        return $this->redirect(['login']);
    }


    /**
     * Displays contact page.
     *
     * @return mixed
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail(Yii::$app->params['adminEmail'])) {
                Yii::$app->session->setFlash('success', 'Thank you for contacting us. We will respond to you as soon as possible.');
            } else {
                Yii::$app->session->setFlash('error', 'There was an error sending your message.');
            }

            return $this->refresh();
        }

        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return mixed
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

    /**
     * Signs user up.
     *
     * @return mixed
     */
    public function actionSignup()
    {
        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post()) && $model->signup()) {
            Yii::$app->session->setFlash('success', 'Thank you for registration. Please check your inbox for verification email.');
            return $this->goHome();
        }

        return $this->render('signup', [
            'model' => $model,
        ]);
    }

    public function actionRegister()
    {
        $model = new SignupForm();
        $this->layout = 'blank';
        // $this->layout = 'error';

        if ($model->load(Yii::$app->request->post()) && $model->signup()) {
            $user = User::findOne(['email' => $model->email]);
            Yii::$app->session->setFlash('success', 'Berhasil membuat akun. Silahkan cek WhatsApp untuk ke Link Verifikasi OTP');
            // return $this->redirect(['site/verify-otp', 'id' => $user->id]);
            return $this->redirect(['site/login']);
        }

        return $this->render('register', [
            'model' => $model,
        ]);
    }



    public function actionVerifyOtp($id)
    {
        $this->layout = 'blank';
        $user = User::findOne(['id' => $id, 'status' => User::STATUS_INACTIVE]);

        if (!$user) {
            throw new NotFoundHttpException('Halaman tidak ditemukan.');
        }

        $model = new VerifyOtpForm();

        if ($model->load(Yii::$app->request->post())) {
            if ($model->verifyOtp($id)) {
                Yii::$app->session->setFlash('success', 'Verifikasi OTP berhasil, akun Anda sudah aktif. Silakan login.');
                return $this->redirect(['site/login']);
            } else {
                Yii::$app->session->setFlash('error', 'Verifikasi OTP atau password tidak valid. Pastikan Anda memasukkan data yang benar.');
            }
        }

        return $this->render('verify-otp', [
            'model' => $model,
        ]);
    }

    /**
     * Requests password reset.
     *
     * @return mixed
     */
    public function actionRequestPasswordReset()
    {
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');

                return $this->goHome();
            }

            Yii::$app->session->setFlash('error', 'Sorry, we are unable to reset password for the provided email address.');
        }

        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }

    /**
     * Resets password.
     *
     * @param string $token
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionResetPassword($token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', 'New password saved.');

            return $this->goHome();
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }

    /**
     * Verify email address
     *
     * @param string $token
     * @throws BadRequestHttpException
     * @return yii\web\Response
     */
    public function actionVerifyEmail($token)
    {
        try {
            $model = new VerifyEmailForm($token);
        } catch (InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
        if (($user = $model->verifyEmail()) && Yii::$app->user->login($user)) {
            Yii::$app->session->setFlash('success', 'Your email has been confirmed!');
            return $this->goHome();
        }

        Yii::$app->session->setFlash('error', 'Sorry, we are unable to verify your account with provided token.');
        return $this->goHome();
    }

    /**
     * Resend verification email
     *
     * @return mixed
     */
    public function actionResendVerificationEmail()
    {
        $model = new ResendVerificationEmailForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');
                return $this->goHome();
            }
            Yii::$app->session->setFlash('error', 'Sorry, we are unable to resend verification email for the provided email address.');
        }

        return $this->render('resendVerificationEmail', [
            'model' => $model
        ]);
    }
}
