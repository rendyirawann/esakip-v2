<?php

namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\helpers\ArrayHelper;
use frontend\models\SakipSkpd;
use frontend\models\SakipPeriode;
use frontend\models\SakipKoordinasi;
use frontend\models\SakipSasaranrenstra;
use frontend\models\SakipCascadingprogram;
use frontend\models\SakipCascadingkegiatan;
use frontend\models\SakipCascadingsubkegiatan;
use yii\helpers\Html; // <-- Pastikan ini ada


class SakipProgressController extends Controller
{
    /**
     * Halaman Monitoring Utama untuk Koordinator/Admin.
     */
    public function actionIndexDev($refperiode_id = null, $refskpd_id = null)
    {
        // =========================================================================
        // Tahap 1: Logika Keamanan dan Filter SKPD (Pola yang sama seperti sebelumnya)
        // =========================================================================
        $user = Yii::$app->user->identity;
        $assignments = Yii::$app->authManager->getAssignments($user->id);

        $skpdList = [];
        $allowedSkpdIds = [];

        if (isset($assignments['superadmin']) || isset($assignments['admin'])) {
            $allSkpd = SakipSkpd::find()->where(['skpd_isaktif' => 'T'])->orderBy('nama_skpd ASC')->all();
            $skpdList = ArrayHelper::map($allSkpd, 'refskpd_id', 'nama_skpd');
            $allowedSkpdIds = array_keys($skpdList);
        } else {
            $coordinatedSkpdIds = SakipKoordinasi::find()->select('refskpd_id')->where(['refuser_id' => $user->id])->column();
            $allowedSkpdIds = $coordinatedSkpdIds;
            if (!empty($allowedSkpdIds)) {
                $skpdList = ArrayHelper::map(
                    SakipSkpd::find()->where(['refskpd_id' => $allowedSkpdIds, 'skpd_isaktif' => 'T'])->orderBy('nama_skpd ASC')->all(),
                    'refskpd_id',
                    'nama_skpd'
                );
            }
        }

        // --- Validasi Keamanan ---
        // Cek apakah refskpd_id dari URL diizinkan untuk diakses
        if ($refskpd_id !== null && !in_array($refskpd_id, $allowedSkpdIds)) {
            throw new ForbiddenHttpException('Anda tidak memiliki hak akses untuk melihat data SKPD ini.');
        }

        // Tentukan refskpd_id yang akan digunakan jika tidak ada di URL atau tidak valid
        if ($refskpd_id === null || !in_array($refskpd_id, $allowedSkpdIds)) {
            $refskpd_id = !empty($allowedSkpdIds) ? $allowedSkpdIds[0] : null;
        }

        // =========================================================================
        // AKHIR DARI BLOK LOGIKA BARU
        // Kode di bawah ini sekarang menggunakan $refskpd_id yang sudah aman
        // =========================================================================

        // Get the name of the SKPD based on refskpd_id
        $nama_skpd = $refskpd_id ? SakipSkpd::find()->select('nama_skpd')->where(['refskpd_id' => $refskpd_id])->scalar() : 'Tidak ada SKPD dipilih';

        // Fetch all periods
        $periodeList = SakipPeriode::find()->all();

        // Set default period to this year if not provided
        if ($refperiode_id === null) {
            $currentYear = date('Y');
            $defaultPeriod = SakipPeriode::find()->where(['periode' => $currentYear])->one();
            $refperiode_id = $defaultPeriod ? $defaultPeriod->refperiode_id : null;
        }

        // Tentukan SKPD mana yang akan kita proses datanya
        $skpdsToMonitorQuery = SakipSkpd::find()->where(['skpd_isaktif' => 'T']);
        if ($refskpd_id !== null) {
            // Jika satu SKPD dipilih dari filter, hanya proses SKPD itu
            $skpdsToMonitorQuery->andWhere(['refskpd_id' => $refskpd_id]);
        } elseif (!empty($allowedSkpdIds)) {
            // Jika tidak ada yg dipilih, proses semua SKPD yang diizinkan
            $skpdsToMonitorQuery->andWhere(['refskpd_id' => $allowedSkpdIds]);
        } else {
            // Jika user tidak punya akses sama sekali
            $skpdsToMonitorQuery->andWhere('0=1'); // Query yang tidak akan menghasilkan apa-apa
        }

        $skpdsToMonitor = $skpdsToMonitorQuery->all();

        // =========================================================================
        // Tahap 3: Agregasi Data (Menghitung Jumlah Isian)
        // =========================================================================
        $monitoringData = [];
        foreach ($skpdsToMonitor as $skpd) {
            $monitoringData[] = [
                'skpd_id' => $skpd->refskpd_id, // <-- TAMBAHKAN BARIS INI
                'nama_skpd' => $skpd->nama_skpd,
                'jumlah_sasaran' => (int) SakipSasaranrenstra::find()->where(['refskpd_id' => $skpd->refskpd_id, 'refperiode_id' => $refperiode_id])->count(),
                'jumlah_program' => (int) SakipCascadingprogram::find()->where(['refskpd_id' => $skpd->refskpd_id, 'refperiode_id' => $refperiode_id])->count(),
                'jumlah_kegiatan' => (int) SakipCascadingkegiatan::find()->where(['refskpd_id' => $skpd->refskpd_id, 'refperiode_id' => $refperiode_id])->count(),
                'jumlah_subkegiatan' => (int) SakipCascadingsubkegiatan::find()->where(['refskpd_id' => $skpd->refskpd_id, 'refperiode_id' => $refperiode_id])->count(),
            ];
        }

        // Retrieve the periode based on refperiode_id
        $selectedPeriod = SakipPeriode::find()->where(['refperiode_id' => $refperiode_id])->one();
        $selectedPeriodValue = $selectedPeriod ? $selectedPeriod->periode : null; // Get the periode value

        // Kirim semua data yang diperlukan ke view
        return $this->render('index-dev', [
            'periodeList' => $periodeList,
            'nama_skpd' => $nama_skpd,
            'skpdList' => $skpdList,
            'selectedPeriodId' => $refperiode_id,
            'selectedPeriodValue' => $selectedPeriodValue, // Include selected period value
            'selectedSkpdId' => $refskpd_id,
            'monitoringData' => $monitoringData,
        ]);
    }

    public function actionGetMonitoringDetail($type, $refskpd_id, $refperiode_id)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $data = [];

        switch ($type) {
            case 'sasaran':
                $query = SakipSasaranrenstra::find()->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])->with('refIndikatorsasaranrenstra')->asArray()->all();
                foreach ($query as $item) {
                    $indikatorText = [];
                    foreach ($item['refIndikatorsasaranrenstra'] as $indikator) {
                        $indikatorText[] = '- ' . Html::encode($indikator['uraian_indikatorsasaranrenstra']);
                    }
                    $data[] = ['kolom1' => Html::encode($item['uraian_sasaranrenstra']), 'kolom2' => implode('<br>', $indikatorText) ?: '(Belum ada indikator)'];
                }
                break;

            case 'program':
                // Query tetap sama, kita butuh semua data terkait
                $query = SakipCascadingprogram::find()
                    ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
                    ->with(['refProgram', 'sasaranRenstra'])
                    ->asArray()
                    ->all();

                // --- LOGIKA BARU UNTUK GROUPING ---
                $groupedData = [];
                // 1. Kelompokkan semua nama program ke dalam array berdasarkan sasaran
                foreach ($query as $item) {
                    $sasaranUraian = $item['sasaranRenstra']['uraian_sasaranrenstra'] ?? 'Tanpa Sasaran Terkait';
                    $programName = $item['refProgram']['nama_program'] ?? 'N/A';

                    // Jika sasaran ini belum ada di array, buat entri baru
                    if (!isset($groupedData[$sasaranUraian])) {
                        $groupedData[$sasaranUraian] = [];
                    }
                    // Tambahkan nama program ke sasaran yang sesuai
                    $groupedData[$sasaranUraian][] = $programName;
                }

                // 2. Format ulang data yang sudah dikelompokkan agar sesuai dengan view
                foreach ($groupedData as $sasaran => $programs) {
                    // Buat daftar program dengan format "- Nama Program"
                    $programListHtml = implode('<br>', array_map(function ($p) {
                        return '- ' . Html::encode($p);
                    }, $programs));

                    $data[] = [
                        'kolom1' => Html::encode($sasaran), // Kolom 1 sekarang adalah Sasaran
                        'kolom2' => $programListHtml      // Kolom 2 adalah daftar Program
                    ];
                }
                // --- AKHIR LOGIKA BARU ---
                break;

            case 'kegiatan':
                // Query ini sudah benar, mengambil semua data yang dibutuhkan
                $query = SakipCascadingkegiatan::find()
                    ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
                    ->with(['refKegiatan', 'refCascadingProgram.refProgram'])
                    ->asArray()
                    ->all();

                // --- LOGIKA BARU UNTUK GROUPING ---
                $groupedData = [];
                // 1. Kelompokkan semua nama kegiatan berdasarkan programnya
                foreach ($query as $item) {
                    $programName = $item['refCascadingProgram']['refProgram']['nama_program'] ?? 'Tanpa Program Terkait';
                    $kegiatanName = $item['refKegiatan']['nama_kegiatan'] ?? 'N/A';

                    if (!isset($groupedData[$programName])) {
                        $groupedData[$programName] = [];
                    }
                    $groupedData[$programName][] = $kegiatanName;
                }

                // 2. Format ulang data yang sudah dikelompokkan agar sesuai view
                foreach ($groupedData as $program => $kegiatans) {
                    $kegiatanListHtml = implode('<br>', array_map(function ($k) {
                        return '- ' . Html::encode($k);
                    }, $kegiatans));

                    $data[] = [
                        'kolom1' => Html::encode($program), // Kolom 1 sekarang adalah Program
                        'kolom2' => $kegiatanListHtml      // Kolom 2 adalah daftar Kegiatan
                    ];
                }
                // --- AKHIR LOGIKA BARU ---
                break;

            case 'subkegiatan':
                // Query ini sudah benar, mengambil semua data yang dibutuhkan
                $query = SakipCascadingsubkegiatan::find()
                    ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
                    ->with(['refSubkegiatan', 'refCascadingKegiatan.refKegiatan'])
                    ->asArray()
                    ->all();

                // --- LOGIKA BARU UNTUK GROUPING ---
                $groupedData = [];
                // 1. Kelompokkan semua nama sub kegiatan berdasarkan kegiatan induknya
                foreach ($query as $item) {
                    $kegiatanName = $item['refCascadingKegiatan']['refKegiatan']['nama_kegiatan'] ?? 'Tanpa Kegiatan Terkait';
                    $subkegiatanName = $item['refSubkegiatan']['nama_subkegiatan'] ?? 'N/A';

                    if (!isset($groupedData[$kegiatanName])) {
                        $groupedData[$kegiatanName] = [];
                    }
                    $groupedData[$kegiatanName][] = $subkegiatanName;
                }

                // 2. Format ulang data yang sudah dikelompokkan agar sesuai view
                foreach ($groupedData as $kegiatan => $subkegiatans) {
                    $subkegiatanListHtml = implode('<br>', array_map(function ($sk) {
                        return '- ' . Html::encode($sk);
                    }, $subkegiatans));

                    $data[] = [
                        'kolom1' => Html::encode($kegiatan), // Kolom 1 sekarang adalah Kegiatan
                        'kolom2' => $subkegiatanListHtml     // Kolom 2 adalah daftar Sub Kegiatan
                    ];
                }
                // --- AKHIR LOGIKA BARU ---
                break;
        }
        return ['data' => $data];
    }
}
