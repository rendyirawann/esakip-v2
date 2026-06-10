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

        // Cek status untuk setiap card berdasarkan refperiode_id
        $statusSasaranRenstra = (bool) SakipSasaranrenstra::find()
            ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id]) // Memasukkan refperiode_id
            ->exists();
        // Cek status untuk indikator sasaran renstra
        $statusIndikatorSasaranRenstra = (bool) SakipIndikatorsasaranrenstra::find()
            ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
            ->exists();
        $statusTujuanRenstra = (bool) SakipTujuanrenstra::find()
            ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id]) // Memasukkan refperiode_id
            ->exists();
        $statusIndikatorTujuanRenstra = (bool) SakipIndikatortujuanrenstra::find()
            ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id]) // Memasukkan refperiode_id
            ->exists();

        // Ambil data SakipSasaranRenstra yang memiliki refskpd_id dan refperiode_id saat ini
        $sakipSasaranRenstra = SakipSasaranrenstra::find()
            ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id]) // Memasukkan refperiode_id
            ->with(['refMisi', 'refTujuan', 'refVisi']) // Pastikan relasi ini sesuai dengan model
            ->all();

        // Hitung jumlah sasaran yang belum memiliki indikator
        $jumlahSasaranBelumIndikator = SakipSasaranrenstra::find()
            ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
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

        foreach (range(1, 4) as $triwulan_id) {
            $nilaiTertinggi = null;
            $nilaiTerendah = null;
            $programTertinggi = null;
            $programTerendah = null;

            $programList = SakipCascadingProgram::find()
                ->with(['indikatorCascadingPrograms.refIndikatorCascadingProgramTriwulan' => function ($query) use ($refskpd_id, $refperiode_id, $triwulan_id) {
                    $query->andWhere([
                        'refskpd_id' => $refskpd_id,
                        'refperiode_id' => $refperiode_id,
                        'reftriwulan_id' => $triwulan_id,
                    ]);
                }])
                ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
                ->all();

            foreach ($programList as $program) {
                foreach ($program->indikatorCascadingPrograms as $indikator) {
                    foreach ($indikator->refIndikatorCascadingProgramTriwulan as $capaian) {
                        if ($capaian && $capaian->triwulan_capaian !== null && (float)$capaian->triwulan_capaian > 0) {
                            $nilai = (float)$capaian->triwulan_capaian;
                            $indikatorNama = $program->uraian_indikatorprogram ?? $indikator->uraian_indikatorprogram;

                            if ($nilaiTertinggi === null || $nilai > $nilaiTertinggi) {
                                $nilaiTertinggi = $nilai;
                                $programTertinggi = [
                                    'nama' => "Triwulan $triwulan_id",
                                    'value' => $nilai,
                                    'indikator' => $indikatorNama,
                                    'triwulan_capaian' => $nilai,
                                ];
                            }

                            if ($nilaiTerendah === null || $nilai < $nilaiTerendah) {
                                $nilaiTerendah = $nilai;
                                $programTerendah = [
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

    public function actionPortalPublik($refperiode_id = null, $target_page = null)
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

        // Retrieve the periode based on refperiode_id
        $selectedPeriod = SakipPeriode::find()->where(['refperiode_id' => $refperiode_id])->one();
        $selectedPeriodValue = $selectedPeriod ? $selectedPeriod->periode : null; // Get the periode value

        return $this->render('portal-publik', [
            'periodeList' => $periodeList,
            'selectedPeriodId' => $refperiode_id,
            'refperiode_id' => $refperiode_id,
            'selectedPeriodValue' => $selectedPeriodValue, // Include selected period value
        ]);
    }

    public function actionPortalPublikTabulasi($refperiode_id = null, $target_page = null)
    {
        $this->layout = 'main-portal';

        if ($target_page && $refperiode_id) {
            return $this->redirect([$target_page, 'refperiode_id' => $refperiode_id]);
        }

        if ($refperiode_id === null) {
            $currentYear = date('Y');
            $defaultPeriod = SakipPeriode::find()->where(['periode' => $currentYear])->one();
            $refperiode_id = $defaultPeriod ? $defaultPeriod->refperiode_id : null;
        }

        $periodeList = SakipPeriode::find()->all();
        $skpdList = SakipSkpd::find()->all();
        $selectedPeriod = SakipPeriode::find()->where(['refperiode_id' => $refperiode_id])->one();
        $selectedPeriodValue = $selectedPeriod ? $selectedPeriod->periode : null;

        $dataRenstra = [];

        foreach ($skpdList as $skpd) {
            $refskpd_id = $skpd->refskpd_id;

            $sasaranList = SakipSasaranrenstra::find()
                ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
                ->all();

            foreach ($sasaranList as $sasaran) {
                $indikatorList = SakipIndikatorsasaranrenstra::find()
                    ->where(['refsasaranrenstra_id' => $sasaran->refsasaranrenstra_id, 'refperiode_id' => $refperiode_id])
                    ->all();

                $indikatorData = [];

                foreach ($indikatorList as $indikator) {
                    // Ambil data per triwulan untuk indikator ini
                    $triwulanData = SakipIndikatorsasaranrenstraTriwulan::find()
                        ->where([
                            'refindikatorsasaranrenstra_id' => $indikator->refindikatorsasaranrenstra_id,
                            'refsasaranrenstra_id' => $sasaran->refsasaranrenstra_id,
                            'refskpd_id' => $refskpd_id,
                            'refperiode_id' => $refperiode_id,
                        ])
                        ->orderBy(['reftriwulan_id' => SORT_ASC])
                        ->all();

                    $indikatorData[] = [
                        'uraian_indikator' => $indikator->uraian_indikatorsasaranrenstra,
                        'triwulan' => $triwulanData,
                    ];
                }

                $dataRenstra[$refskpd_id][] = [
                    'uraian_sasaran' => $sasaran->uraian_sasaranrenstra,
                    'indikator' => $indikatorData,
                ];
            }
        }

        $triwulanTertinggiPerSkpd = [];
        $triwulanTerendahPerSkpd = [];

        // Ambil semua indikator SKPD dan capaian sekaligus
        $indikatorAll = SakipIndikatorsasaranrenstra::find()
            ->where(['refperiode_id' => $refperiode_id])
            ->all();

        $capaianAll = SakipIndikatorsasaranrenstraTriwulan::find()
            ->where(['refperiode_id' => $refperiode_id])
            ->all();

        // Mapping cepat
        $indikatorMapBySkpd = [];
        foreach ($indikatorAll as $ind) {
            $indikatorMapBySkpd[$ind->refskpd_id][] = $ind;
        }

        $capaianMap = [];
        foreach ($capaianAll as $cap) {
            $key = $cap->refskpd_id . '-' . $cap->refindikatorsasaranrenstra_id . '-' . $cap->reftriwulan_id;
            $capaianMap[$key] = $cap;
        }

        // Loop per triwulan
        foreach (range(1, 4) as $triwulan_id) {
            $nilaiTertinggiGlobal = null;
            $nilaiTerendahGlobal = null;
            $skpdTertinggi = null;
            $skpdTerendah = null;

            foreach ($skpdList as $skpd) {
                $refskpd_id = $skpd->refskpd_id;
                if ($refskpd_id == 1) continue;

                $indikatorList = $indikatorMapBySkpd[$refskpd_id] ?? [];

                $nilaiTertinggiSkpd = null;
                $nilaiTerendahSkpd = null;
                $indikatorTertinggi = null;
                $indikatorTerendah = null;

                foreach ($indikatorList as $indikator) {
                    $key = $refskpd_id . '-' . $indikator->refindikatorsasaranrenstra_id . '-' . $triwulan_id;
                    $capaian = $capaianMap[$key] ?? null;

                    if ($capaian && $capaian->triwulan_capaian !== null && (float)$capaian->triwulan_capaian > 0) {
                        $nilai = (float)$capaian->triwulan_capaian;

                        if ($nilaiTertinggiSkpd === null || $nilai > $nilaiTertinggiSkpd) {
                            $nilaiTertinggiSkpd = $nilai;
                            $indikatorTertinggi = $indikator->uraian_indikatorsasaranrenstra;
                        }

                        if ($nilaiTerendahSkpd === null || $nilai < $nilaiTerendahSkpd) {
                            $nilaiTerendahSkpd = $nilai;
                            $indikatorTerendah = $indikator->uraian_indikatorsasaranrenstra;
                        }
                    }
                }

                if ($nilaiTertinggiSkpd !== null && ($nilaiTertinggiGlobal === null || $nilaiTertinggiSkpd > $nilaiTertinggiGlobal)) {
                    $nilaiTertinggiGlobal = $nilaiTertinggiSkpd;
                    $skpdTertinggi = [
                        'nama' => "Triwulan $triwulan_id - {$skpd->nama_skpd}",
                        'value' => $nilaiTertinggiSkpd,
                        'indikator' => $indikatorTertinggi,
                    ];
                }

                if ($nilaiTerendahSkpd !== null && ($nilaiTerendahGlobal === null || $nilaiTerendahSkpd < $nilaiTerendahGlobal)) {
                    $nilaiTerendahGlobal = $nilaiTerendahSkpd;
                    $skpdTerendah = [
                        'nama' => "Triwulan $triwulan_id - {$skpd->nama_skpd}",
                        'value' => $nilaiTerendahSkpd,
                        'indikator' => $indikatorTerendah,
                    ];
                }
            }

            if ($skpdTertinggi !== null) {
                $triwulanTertinggiPerSkpd[] = $skpdTertinggi;
            }

            if ($skpdTerendah !== null) {
                $triwulanTerendahPerSkpd[] = $skpdTerendah;
            }
        }


        $dataProgram = [];

        foreach ($skpdList as $skpd) {
            $refskpd_id = $skpd->refskpd_id;
            if ($refskpd_id == 1) continue;

            // Ambil program secara eager loading beserta relasinya
            $programList = SakipCascadingProgram::find()
                ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
                ->with([
                    'indikatorCascadingPrograms.refIndikatorCascadingProgramTriwulan' => function ($query) use ($refskpd_id, $refperiode_id) {
                        $query->andWhere(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
                            ->orderBy(['reftriwulan_id' => SORT_ASC]);
                    }
                ])
                ->all();

            foreach ($programList as $program) {
                $indikatorData = [];

                foreach ($program->indikatorCascadingPrograms as $indikator) {
                    $triwulanData = $indikator->refIndikatorCascadingProgramTriwulan;

                    $indikatorData[] = [
                        'uraian_indikator' => $program->uraian_indikatorprogram ?? $indikator->uraian_indikatorprogram,
                        'triwulan' => $triwulanData,
                        'program_satuan' => $program->program_satuan,
                    ];
                }

                $dataProgram[$refskpd_id][] = [
                    'uraian_program' => $program->uraian_sasaranprogram,
                    'indikator' => $indikatorData,
                ];
            }
        }


        $triwulanTertinggiProgram = [];
        $triwulanTerendahProgram = [];

        $skpdMap = ArrayHelper::map($skpdList, 'refskpd_id', 'nama_skpd');

        $programList = SakipCascadingProgram::find()
            ->where(['refperiode_id' => $refperiode_id])
            ->with([
                'indikatorCascadingPrograms.refIndikatorCascadingProgramTriwulan' => function ($q) use ($refperiode_id) {
                    $q->andWhere(['refperiode_id' => $refperiode_id]);
                }
            ])
            ->all();

        foreach (range(1, 4) as $triwulan_id) {
            $nilaiTertinggi = null;
            $nilaiTerendah = null;
            $programTertinggi = null;
            $programTerendah = null;

            foreach ($programList as $program) {
                $refskpd_id = $program->refskpd_id;

                // Skip SKPD ID = 1
                if ($refskpd_id == 1) continue;

                $nama_skpd = $skpdMap[$refskpd_id] ?? 'Tidak Diketahui';

                foreach ($program->indikatorCascadingPrograms as $indikator) {
                    foreach ($indikator->refIndikatorCascadingProgramTriwulan as $tw) {
                        if ((int)$tw->reftriwulan_id === $triwulan_id && $tw->triwulan_capaian > 0) {
                            $nilai = (float)$tw->triwulan_capaian;
                            $uraian = $program->uraian_indikatorprogram ?? $indikator->uraian_indikatorprogram;

                            if ($nilaiTertinggi === null || $nilai > $nilaiTertinggi) {
                                $nilaiTertinggi = $nilai;
                                $programTertinggi = [
                                    'nama' => "Triwulan $triwulan_id - $nama_skpd",
                                    'value' => $nilai,
                                    'indikator' => $uraian,
                                ];
                            }

                            if ($nilaiTerendah === null || $nilai < $nilaiTerendah) {
                                $nilaiTerendah = $nilai;
                                $programTerendah = [
                                    'nama' => "Triwulan $triwulan_id - $nama_skpd",
                                    'value' => $nilai,
                                    'indikator' => $uraian,
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



        $dataKegiatan = [];

        foreach ($skpdList as $skpd) {
            $refskpd_id = $skpd->refskpd_id;

            $kegiatanList = SakipCascadingkegiatan::find()
                ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
                ->with([
                    'indikatorCascadingKegiatan.refIndikatorCascadingKegiatanTriwulan' => function ($query) use ($refskpd_id, $refperiode_id) {
                        $query->andWhere([
                            'refskpd_id' => $refskpd_id,
                            'refperiode_id' => $refperiode_id
                        ])->orderBy(['reftriwulan_id' => SORT_ASC]);
                    }
                ])
                ->all();

            foreach ($kegiatanList as $kegiatan) {
                $indikatorData = [];

                foreach ($kegiatan->indikatorCascadingKegiatan as $indikator) {
                    $triwulanData = $indikator->refIndikatorCascadingKegiatanTriwulan;

                    $indikatorData[] = [
                        'uraian_indikator' => $kegiatan->uraian_indikatorkegiatan,
                        'triwulan' => $triwulanData,
                        'kegiatan_satuan' => $kegiatan->kegiatan_satuan,
                    ];
                }

                $dataKegiatan[$refskpd_id][] = [
                    'uraian_kegiatan' => $kegiatan->uraian_sasarankegiatan,
                    'indikator' => $indikatorData,
                ];
            }
        }


        $triwulanTertinggiKegiatan = [];
        $triwulanTerendahKegiatan = [];

        foreach (range(1, 4) as $triwulan_id) {
            $nilaiTertinggiGlobal = null;
            $nilaiTerendahGlobal = null;
            $kegiatanTertinggi = null;
            $kegiatanTerendah = null;

            foreach ($skpdList as $skpd) {
                $refskpd_id = $skpd->refskpd_id;
                if ($refskpd_id == 1) continue;

                $kegiatanList = SakipCascadingkegiatan::find()
                    ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
                    ->with([
                        'indikatorCascadingKegiatan.refIndikatorCascadingKegiatanTriwulan' => function ($query) use ($triwulan_id, $refskpd_id, $refperiode_id) {
                            $query->andWhere([
                                'reftriwulan_id' => $triwulan_id,
                                'refskpd_id' => $refskpd_id,
                                'refperiode_id' => $refperiode_id,
                            ]);
                        }
                    ])
                    ->all();

                foreach ($kegiatanList as $kegiatan) {
                    foreach ($kegiatan->indikatorCascadingKegiatan as $indikator) {
                        foreach ($indikator->refIndikatorCascadingKegiatanTriwulan as $capaian) {
                            $nilai = (float) $capaian->triwulan_capaian;
                            if ($nilai <= 0) continue;

                            $uraianIndikator = $indikator->uraian_indikatorkegiatan ?? $kegiatan->uraian_indikatorkegiatan;

                            // Cek nilai tertinggi
                            if ($nilaiTertinggiGlobal === null || $nilai > $nilaiTertinggiGlobal) {
                                $nilaiTertinggiGlobal = $nilai;
                                $kegiatanTertinggi = [
                                    'nama' => "Triwulan $triwulan_id - {$skpd->nama_skpd}",
                                    'value' => $nilai,
                                    'indikator' => $uraianIndikator,
                                ];
                            }

                            // Cek nilai terendah
                            if ($nilaiTerendahGlobal === null || $nilai < $nilaiTerendahGlobal) {
                                $nilaiTerendahGlobal = $nilai;
                                $kegiatanTerendah = [
                                    'nama' => "Triwulan $triwulan_id - {$skpd->nama_skpd}",
                                    'value' => $nilai,
                                    'indikator' => $uraianIndikator,
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




        $dataSubkegiatan = [];

        foreach ($skpdList as $skpd) {
            $refskpd_id = $skpd->refskpd_id;

            $subkegiatanList = SakipCascadingsubkegiatan::find()
                ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
                ->with([
                    'refIndikatorcascadingsubkegiatan.refIndikatorCascadingSubkegiatanTriwulan' => function ($query) use ($refperiode_id) {
                        $query->andWhere(['refperiode_id' => $refperiode_id])
                            ->orderBy(['reftriwulan_id' => SORT_ASC]);
                    }
                ])
                ->all();

            foreach ($subkegiatanList as $subkegiatan) {
                $indikatorData = [];

                foreach ($subkegiatan->refIndikatorcascadingsubkegiatan as $indikator) {
                    $triwulanData = $indikator->refIndikatorCascadingSubkegiatanTriwulan;

                    $indikatorData[] = [
                        'uraian_indikator' => $subkegiatan->uraian_indikatorsubkegiatan,
                        'triwulan' => $triwulanData,
                        'subkegiatan_satuan' => $subkegiatan->subkegiatan_satuan,
                    ];
                }

                $dataSubkegiatan[$refskpd_id][] = [
                    'uraian_subkegiatan' => $subkegiatan->uraian_sasaransubkegiatan,
                    'indikator' => $indikatorData,
                ];
            }
        }


        $triwulanTertinggiSubkegiatan = [];
        $triwulanTerendahSubkegiatan = [];

        foreach (range(1, 4) as $triwulan_id) {
            $nilaiTertinggiGlobal = null;
            $nilaiTerendahGlobal = null;
            $subkegiatanTertinggi = null;
            $subkegiatanTerendah = null;

            // Ambil semua subkegiatan yang bukan refskpd_id = 1, dengan eager load relasi
            $subkegiatanList = SakipCascadingsubkegiatan::find()
                ->where(['refperiode_id' => $refperiode_id])
                ->andWhere(['!=', 'refskpd_id', 1])
                ->with([
                    'refIndikatorcascadingsubkegiatan.refIndikatorCascadingSubkegiatanTriwulan' => function ($query) use ($triwulan_id, $refperiode_id) {
                        $query->andWhere([
                            'reftriwulan_id' => $triwulan_id,
                            'refperiode_id' => $refperiode_id,
                        ]);
                    }
                ])
                ->all();

            foreach ($subkegiatanList as $subkegiatan) {
                $skpdName = $subkegiatan->nama_skpd ?? ($skpdList[$subkegiatan->refskpd_id]->nama_skpd ?? ''); // fallback

                foreach ($subkegiatan->refIndikatorcascadingsubkegiatan as $indikator) {
                    foreach ($indikator->refIndikatorCascadingSubkegiatanTriwulan as $capaian) {
                        $nilai = (float) $capaian->triwulan_capaian;

                        if ($nilai > 0) {
                            // Tertinggi
                            if ($nilaiTertinggiGlobal === null || $nilai > $nilaiTertinggiGlobal) {
                                $nilaiTertinggiGlobal = $nilai;
                                $subkegiatanTertinggi = [
                                    'nama' => "Triwulan $triwulan_id - $skpdName",
                                    'value' => $nilai,
                                    'indikator' => $subkegiatan->uraian_indikatorsubkegiatan ?? $indikator->uraian_indikatorsubkegiatan,
                                ];
                            }

                            // Terendah
                            if ($nilaiTerendahGlobal === null || $nilai < $nilaiTerendahGlobal) {
                                $nilaiTerendahGlobal = $nilai;
                                $subkegiatanTerendah = [
                                    'nama' => "Triwulan $triwulan_id - $skpdName",
                                    'value' => $nilai,
                                    'indikator' => $subkegiatan->uraian_indikatorsubkegiatan ?? $indikator->uraian_indikatorsubkegiatan,
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


        return $this->render('portal-publik-tabulasi', [
            'periodeList' => $periodeList,
            'selectedPeriodId' => $refperiode_id,
            'refperiode_id' => $refperiode_id,
            'selectedPeriodValue' => $selectedPeriodValue,
            'skpdList' => $skpdList,
            'dataRenstra' => $dataRenstra,
            'triwulanTerendahPerSkpd' => $triwulanTerendahPerSkpd,
            'triwulanTertinggiPerSkpd' => $triwulanTertinggiPerSkpd, // <-- Tambahkan ini
            'triwulanTerendahProgram' => $triwulanTerendahProgram,
            'triwulanTertinggiProgram' => $triwulanTertinggiProgram, // <-- Tambahkan ini
            'triwulanTertinggiKegiatan' => $triwulanTertinggiKegiatan,
            'triwulanTerendahKegiatan' => $triwulanTerendahKegiatan,
            'triwulanTertinggiSubkegiatan' => $triwulanTertinggiSubkegiatan,
            'triwulanTerendahSubkegiatan' => $triwulanTerendahSubkegiatan,

            'dataProgram' => $dataProgram,
            'dataKegiatan' => $dataKegiatan,
            'dataSubkegiatan' => $dataSubkegiatan,
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
        $selectedPeriodValue = $selectedPeriod ? $selectedPeriod->periode : null; // Get the periode value

        // Fetch data for _view-renstra
        $sasaranRenstraList = [];
        $strategiList = [];
        $kebijakanList = [];
        $namaSkpdList = []; // To store SKPD names for each ID
        $indikatorsIkuList = [];
        $sasaranRenstraIkuList = [];
        $sasaranRenstraRktList = [];

        foreach ($skpdList as $skpd) {
            $refskpd_id = $skpd->refskpd_id;
            $sasaranRenstraList[$refskpd_id] = SakipSasaranRenstra::find()
                ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
                ->all();
            $strategiList[$refskpd_id] = SakipStrategi::find()
                ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
                ->all();
            $kebijakanList[$refskpd_id] = SakipKebijakan::find()
                ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
                ->all();
            $namaSkpdList[$refskpd_id] = $skpd->nama_skpd; // Store SKPD name

            // Fetch indicators for IKU
            $indikatorsIkuList[$refskpd_id] = SakipIndikatorsasaranrenstra::find()
                ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
                ->all();

            // Prepare the sasaran for IKU with their formulations
            $sasaranRenstraIkuList[$refskpd_id] = [];
            foreach ($indikatorsIkuList[$refskpd_id] as $indikator) {
                $sasaran = SakipSasaranRenstra::find()
                    ->where(['refsasaranrenstra_id' => $indikator->refsasaranrenstra_id])
                    ->one();

                if ($sasaran) {
                    $sasaranRenstraIkuList[$refskpd_id][] = [
                        'indikator' => $indikator,
                        'formulasi' => $sasaran->formulasi_sasaranrenstra,
                    ];
                }
            }

            // Fetch data based on refskpd_id and refperiode_id
            $sasaranRenstraRktList[$refskpd_id] = SakipSasaranRenstra::find()
                ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
                ->with(['refVisi', 'refMisi', 'refTujuan', 'refSasaran'])
                ->all();

            $sasaranRenstraPkList[$refskpd_id] = SakipSasaranRenstra::find()
                ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
                ->with(['refVisi', 'refMisi', 'refTujuan', 'refSasaran'])
                ->all();

            $sasaranRenstraPkpList[$refskpd_id] = SakipSasaranRenstra::find()
                ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
                ->with(['refVisi', 'refMisi', 'refTujuan', 'refSasaran'])
                ->all();
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

        // Fetch data for _view-renstra
        $namaSkpdList = []; // To store SKPD names for each ID
        $indikatorsCapkinUtamaList = [];
        $indikatorsCapkinStrategiList = [];

        foreach ($skpdList as $skpd) {
            $refskpd_id = $skpd->refskpd_id;
            $namaSkpdList[$refskpd_id] = $skpd->nama_skpd; // Store SKPD name
            // Fetch sasaran renstra with related indicators
            $indikatorsCapkinUtamaList[$refskpd_id] = SakipIndikatorSasaranRenstra::find()
                ->where(['refskpd_id' => $refskpd_id])
                ->andWhere(['refperiode_id' => $refperiode_id])
                ->with('refSasaranrenstra') // Eager load related sasaran renstra
                ->all();

            $indikatorsCapkinStrategiList[$refskpd_id] = SakipIndikatorSasaranRenstra::find()
                ->where(['refskpd_id' => $refskpd_id])
                ->andWhere(['refperiode_id' => $refperiode_id])
                ->with('refSasaranrenstra') // Eager load related sasaran renstra
                ->all();

            foreach ($indikatorsCapkinStrategiList[$refskpd_id] as $index => $indikator) {
                // Fetch related triwulan data
                $triwulanData = SakipIndikatorSasaranRenstraTriwulan::find()
                    ->where(['refindikatorsasaranrenstra_id' => $indikator->refindikatorsasaranrenstra_id])
                    ->andWhere(['refsasaranrenstra_id' => $indikator->refsasaranrenstra_id])
                    ->andWhere(['refskpd_id' => $refskpd_id])
                    ->all();
            }
        }

        return $this->render('portal-publik-capkin', [
            'periodeList' => $periodeList,
            'selectedPeriodId' => $refperiode_id,
            'refperiode_id' => $refperiode_id,
            'selectedPeriodValue' => $selectedPeriodValue, // Include selected period value
            'skpdList' => $skpdList,
            'namaSkpdList' => $namaSkpdList, // Pass SKPD names
            'indikatorsCapkinUtamaList' => $indikatorsCapkinUtamaList, // Pass SKPD names
            'indikatorsCapkinStrategiList' => $indikatorsCapkinStrategiList, // Pass SKPD names

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
