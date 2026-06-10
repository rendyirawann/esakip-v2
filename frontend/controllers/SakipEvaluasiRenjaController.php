<?php

namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\UploadedFile;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use frontend\models\SakipEvaluasiRenja;
use frontend\models\SakipSkpd;
use PhpOffice\PhpSpreadsheet\IOFactory;

/**
 * Controller untuk fitur Import & Tampil Evaluasi Renja
 */
class SakipEvaluasiRenjaController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['index', 'import', 'delete', 'download-template'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Cek apakah user adalah superadmin/admin
     */
    private function isSuperAdmin()
    {
        $user = Yii::$app->user->identity;
        $assignments = Yii::$app->authManager->getAssignments($user->id);
        return isset($assignments['superadmin']) || isset($assignments['admin']);
    }

    /**
     * Ambil daftar SKPD untuk dropdown (superadmin only)
     */
    private function getSkpdList()
    {
        $allSkpd = SakipSkpd::find()
            ->where(['skpd_isaktif' => 'T'])
            ->orderBy('nama_skpd ASC')
            ->all();
        return ArrayHelper::map($allSkpd, 'refskpd_id', 'nama_skpd');
    }

    /**
     * Tentukan refskpd_id yang aktif berdasarkan role user
     * Superadmin bisa pilih via parameter, user biasa pakai refskpd_id sendiri
     */
    private function resolveSkpdId()
    {
        $user = Yii::$app->user->identity;
        $request = Yii::$app->request;

        if ($this->isSuperAdmin()) {
            // Superadmin: ambil dari parameter GET/POST
            $selectedSkpd = $request->get('refskpd_id', $request->post('refskpd_id'));
            if (!empty($selectedSkpd)) {
                return (int)$selectedSkpd;
            }
            // Fallback ke SKPD user sendiri jika ada
            return $user->refskpd_id ?? null;
        }

        // User biasa: pakai refskpd_id sendiri
        return $user->refskpd_id ?? null;
    }

    /**
     * Halaman utama - menampilkan data yang sudah diimport
     */
    public function actionIndex()
    {
        $request = Yii::$app->request;
        $user = Yii::$app->user->identity;
        $isSuperAdmin = $this->isSuperAdmin();
        $refskpdId = $this->resolveSkpdId();
        $year = $request->get('year', date('Y'));

        $data = [];
        $skpdName = '-';
        $importHistory = [];
        $skpdList = [];

        // Superadmin: siapkan dropdown list
        if ($isSuperAdmin) {
            $skpdList = $this->getSkpdList();
        }

        if (empty($refskpdId)) {
            if (!$isSuperAdmin) {
                Yii::$app->session->setFlash('warning', 'Akun Anda belum terhubung dengan SKPD manapun. Silakan hubungi Admin.');
            }
        } else {
            // Ambil nama SKPD
            $skpd = SakipSkpd::findOne($refskpdId);
            if ($skpd) {
                $skpdName = $skpd->nama_skpd;
            }

            // Ambil data evaluasi renja
            $data = SakipEvaluasiRenja::find()
                ->where(['refskpd_id' => $refskpdId, 'tahun' => $year])
                ->orderBy(['row_order' => SORT_ASC])
                ->all();

            // Ambil daftar tahun yang sudah pernah diimport
            $importHistory = SakipEvaluasiRenja::find()
                ->select(['tahun', 'COUNT(*) as jumlah', 'MAX(created_at) as last_import'])
                ->where(['refskpd_id' => $refskpdId])
                ->groupBy('tahun')
                ->orderBy(['tahun' => SORT_DESC])
                ->asArray()
                ->all();
        }

        return $this->render('index', [
            'data' => $data,
            'year' => $year,
            'skpdName' => $skpdName,
            'refskpdId' => $refskpdId,
            'importHistory' => $importHistory,
            'isSuperAdmin' => $isSuperAdmin,
            'skpdList' => $skpdList,
            'selectedSkpdId' => $refskpdId,
        ]);
    }

    /**
     * Import data dari file Excel
     */
    public function actionImport()
    {
        $request = Yii::$app->request;

        if (!$request->isPost) {
            return $this->redirect(['index']);
        }

        $user = Yii::$app->user->identity;
        $isSuperAdmin = $this->isSuperAdmin();

        // Tentukan refskpd_id: superadmin bisa pilih, user biasa pakai SKPD sendiri
        if ($isSuperAdmin) {
            $refskpdId = $request->post('refskpd_id');
            if (empty($refskpdId)) {
                $refskpdId = $user->refskpd_id ?? null;
            }
        } else {
            $refskpdId = $user->refskpd_id ?? null;
        }

        if (empty($refskpdId)) {
            Yii::$app->session->setFlash('error', 'SKPD belum dipilih atau akun belum terhubung dengan SKPD.');
            return $this->redirect(['index']);
        }

        // Handle file upload
        $uploadedFile = UploadedFile::getInstanceByName('excel_file');

        if (!$uploadedFile) {
            Yii::$app->session->setFlash('error', 'File tidak ditemukan. Silakan pilih file Excel (.xlsx).');
            return $this->redirect(['index', 'refskpd_id' => $refskpdId]);
        }

        // Validasi ekstensi
        $allowedExtensions = ['xlsx', 'xls'];
        if (!in_array(strtolower($uploadedFile->extension), $allowedExtensions)) {
            Yii::$app->session->setFlash('error', 'Format file tidak didukung. Hanya file .xlsx dan .xls yang diizinkan.');
            return $this->redirect(['index', 'refskpd_id' => $refskpdId]);
        }

        // Validasi ukuran (max 10MB)
        if ($uploadedFile->size > 10 * 1024 * 1024) {
            Yii::$app->session->setFlash('error', 'Ukuran file terlalu besar. Maksimal 10MB.');
            return $this->redirect(['index', 'refskpd_id' => $refskpdId]);
        }

        $transaction = Yii::$app->db->beginTransaction();

        try {
            // Baca file Excel
            $spreadsheet = IOFactory::load($uploadedFile->tempName);
            $sheet = $spreadsheet->getSheet(0);
            $highestRow = $sheet->getHighestRow();

            // Ambil tahun dari baris 3 atau dari input user
            $tahunInput = $request->post('tahun_import');
            $tahunExcel = $this->extractTahun($sheet);
            $tahun = !empty($tahunInput) ? (int)$tahunInput : ($tahunExcel ?: (int)date('Y'));

            // Opsi: hapus data lama sebelum import ulang
            $replaceExisting = $request->post('replace_existing', 0);
            if ($replaceExisting) {
                SakipEvaluasiRenja::deleteAll([
                    'refskpd_id' => $refskpdId,
                    'tahun' => $tahun,
                ]);
            }

            // Variabel untuk carry-forward merged cell values
            $lastVisi = null;
            $lastMisi = null;
            $lastTujuan = null;
            $lastSasaranStrategis = null;
            $lastSasaranOpd = null;

            $rowOrder = 0;
            $importCount = 0;

            // Loop baris data (mulai dari baris 11 sampai akhir)
            for ($row = 11; $row <= $highestRow; $row++) {
                $colG = $this->getCellValue($sheet, 'G', $row);
                $colH = $this->getCellValue($sheet, 'H', $row);

                // Skip baris kosong (tidak ada data di G dan H)
                if (empty(trim($colG ?? '')) && empty(trim($colH ?? ''))) {
                    continue;
                }

                // Baca kolom B-F (carry forward untuk merged cells)
                $colB = $this->getCellValue($sheet, 'B', $row);
                $colC = $this->getCellValue($sheet, 'C', $row);
                $colD = $this->getCellValue($sheet, 'D', $row);
                $colE = $this->getCellValue($sheet, 'E', $row);
                $colF = $this->getCellValue($sheet, 'F', $row);

                // Update carry-forward jika ada nilai baru
                if (!empty(trim($colB ?? ''))) $lastVisi = trim($colB);
                if (!empty(trim($colC ?? ''))) $lastMisi = trim($colC);
                if (!empty(trim($colD ?? ''))) $lastTujuan = trim($colD);
                if (!empty(trim($colE ?? ''))) $lastSasaranStrategis = trim($colE);
                if (!empty(trim($colF ?? '')) && stripos($colF, 'Perangkat Daerah :') === false) {
                    $lastSasaranOpd = trim($colF);
                }

                // Tentukan level berdasarkan warna cell
                $level = $this->determineLevel($colG, $row, $sheet);

                // Baca kolom numerik
                $colA = $this->getCellValue($sheet, 'A', $row);
                $noUrut = is_numeric($colA) ? (int)$colA : null;

                $namaValue = trim($colG ?? '');

                $model = new SakipEvaluasiRenja();
                $model->refskpd_id = $refskpdId;
                $model->tahun = $tahun;
                $model->no_urut = $noUrut;
                $model->row_order = $rowOrder++;
                $model->level = $level;

                // Simpan nama ke kolom yang sesuai level
                $model->nama_unsur = ($level === 'unsur') ? $namaValue : null;
                $model->nama_bidang_urusan = ($level === 'bidang_urusan') ? $namaValue : null;
                $model->nama_program = ($level === 'program') ? $namaValue : null;
                $model->nama_kegiatan = ($level === 'kegiatan') ? $namaValue : null;
                $model->nama_sub_kegiatan = ($level === 'sub_kegiatan') ? $namaValue : null;

                $model->visi = $lastVisi;
                $model->misi = $lastMisi;
                $model->tujuan = $lastTujuan;
                $model->sasaran_strategis = $lastSasaranStrategis;
                $model->sasaran_opd = $lastSasaranOpd;
                $model->indikator_kinerja = trim($colH ?? '');
                $model->satuan = trim($this->getCellValue($sheet, 'I', $row) ?? '');

                // Kolom numerik - gunakan getCalculatedValue untuk mengevaluasi formula
                $model->target_renstra_kinerja = $this->getNumericValue($sheet, 'J', $row);
                $model->target_renstra_anggaran = $this->getNumericValue($sheet, 'K', $row);
                $model->realisasi_sd_lalu_kinerja = $this->getNumericValue($sheet, 'L', $row);
                $model->realisasi_sd_lalu_anggaran = $this->getNumericValue($sheet, 'M', $row);
                $model->target_renja_kinerja = $this->getNumericValue($sheet, 'N', $row);
                $model->target_renja_anggaran = $this->getNumericValue($sheet, 'O', $row);

                // Triwulan I
                $model->tw1_kinerja = $this->getNumericValue($sheet, 'P', $row);
                $model->tw1_persen = $this->getNumericValue($sheet, 'Q', $row);
                $model->tw1_anggaran = $this->getNumericValue($sheet, 'R', $row);

                // Triwulan II
                $model->tw2_kinerja = $this->getNumericValue($sheet, 'S', $row);
                $model->tw2_persen = $this->getNumericValue($sheet, 'T', $row);
                $model->tw2_anggaran = $this->getNumericValue($sheet, 'U', $row);

                // Triwulan III
                $model->tw3_kinerja = $this->getNumericValue($sheet, 'V', $row);
                $model->tw3_persen = $this->getNumericValue($sheet, 'W', $row);
                $model->tw3_anggaran = $this->getNumericValue($sheet, 'X', $row);

                // Triwulan IV
                $model->tw4_kinerja = $this->getNumericValue($sheet, 'Y', $row);
                $model->tw4_persen = $this->getNumericValue($sheet, 'Z', $row);
                $model->tw4_anggaran = $this->getNumericValue($sheet, 'AA', $row);

                // Realisasi capaian
                $model->realisasi_capaian_kinerja = $this->getNumericValue($sheet, 'AB', $row);
                $model->realisasi_capaian_anggaran = $this->getNumericValue($sheet, 'AC', $row);

                // Realisasi renstra
                $model->realisasi_renstra_kinerja = $this->getNumericValue($sheet, 'AD', $row);
                $model->realisasi_renstra_anggaran = $this->getNumericValue($sheet, 'AE', $row);

                // Tingkat capaian
                $model->tingkat_capaian_kinerja = $this->getNumericValue($sheet, 'AF', $row);
                $model->tingkat_capaian_anggaran = $this->getNumericValue($sheet, 'AG', $row);

                if (!$model->save()) {
                    throw new \Exception("Gagal menyimpan baris $row: " . json_encode($model->errors));
                }

                $importCount++;
            }

            $transaction->commit();

            Yii::$app->session->setFlash('success',
                "Berhasil mengimport <strong>$importCount</strong> baris data Evaluasi Renja Tahun <strong>$tahun</strong>."
            );

            return $this->redirect(['index', 'year' => $tahun, 'refskpd_id' => $refskpdId]);

        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::$app->session->setFlash('error', 'Gagal mengimport data: ' . $e->getMessage());
            return $this->redirect(['index', 'refskpd_id' => $refskpdId]);
        }
    }

    /**
     * Hapus data evaluasi renja berdasarkan tahun
     */
    public function actionDelete()
    {
        $request = Yii::$app->request;

        if (!$request->isPost) {
            return $this->redirect(['index']);
        }

        $user = Yii::$app->user->identity;
        $isSuperAdmin = $this->isSuperAdmin();

        // Tentukan refskpd_id
        if ($isSuperAdmin) {
            $refskpdId = $request->post('refskpd_id', $this->resolveSkpdId());
        } else {
            $refskpdId = $user->refskpd_id ?? null;
        }

        $tahun = $request->post('tahun');

        if (empty($refskpdId) || empty($tahun)) {
            Yii::$app->session->setFlash('error', 'Parameter tidak lengkap.');
            return $this->redirect(['index']);
        }

        $deleted = SakipEvaluasiRenja::deleteAll([
            'refskpd_id' => $refskpdId,
            'tahun' => $tahun,
        ]);

        Yii::$app->session->setFlash('success',
            "Berhasil menghapus <strong>$deleted</strong> baris data Evaluasi Renja Tahun <strong>$tahun</strong>."
        );

        return $this->redirect(['index', 'refskpd_id' => $refskpdId]);
    }

    /**
     * Download template Excel
     */
    public function actionDownloadTemplate()
    {
        $filePath = Yii::getAlias('@app') . '/../EVALUASI RENJA BAPPEDALITBANG TAHUN 2025.xlsx';

        if (file_exists($filePath)) {
            return Yii::$app->response->sendFile($filePath, 'Template_Evaluasi_Renja.xlsx');
        }

        Yii::$app->session->setFlash('error', 'File template tidak ditemukan.');
        return $this->redirect(['index']);
    }

    // ===================== HELPER METHODS =====================

    /**
     * Ambil tahun dari baris 3 di Excel
     */
    private function extractTahun($sheet)
    {
        $cell3 = $sheet->getCell('A3')->getValue();
        if ($cell3) {
            if (preg_match('/(\d{4})/', $cell3, $matches)) {
                return (int)$matches[1];
            }
        }
        return null;
    }

    /**
     * Ambil nilai sel (handle merged cells)
     */
    private function getCellValue($sheet, $col, $row)
    {
        $cell = $sheet->getCell($col . $row);
        $value = $cell->getValue();

        if ($value === null || $value === '') {
            $mergedCells = $sheet->getMergeCells();
            foreach ($mergedCells as $mergeRange) {
                if ($cell->isInRange($mergeRange)) {
                    preg_match('/([A-Z]+)(\d+)/', $mergeRange, $matches);
                    $value = $sheet->getCell($matches[1] . $matches[2])->getValue();
                    break;
                }
            }
        }

        return $value;
    }

    /**
     * Ambil nilai numerik dari sel (evaluasi formula)
     */
    private function getNumericValue($sheet, $col, $row)
    {
        try {
            $cell = $sheet->getCell($col . $row);
            $value = $cell->getCalculatedValue();

            if ($value === null || $value === '' || $value === ' ') {
                return null;
            }

            if (is_numeric($value)) {
                return round((float)$value, 4);
            }

            $cleaned = preg_replace('/[^\d\.\-]/', '', $value);
            if (is_numeric($cleaned)) {
                return round((float)$cleaned, 4);
            }
        } catch (\Exception $e) {
            try {
                $rawValue = $sheet->getCell($col . $row)->getValue();
                if (is_numeric($rawValue)) {
                    return round((float)$rawValue, 4);
                }
            } catch (\Exception $e2) {
                // Skip
            }
        }

        return null;
    }

    /**
     * Tentukan level hierarki berdasarkan warna background cell di kolom G
     *
     * Mapping warna dari Excel:
     * - 9BBB59 (hijau)     = Header OPD → unsur
     * - 558ED5 (biru tua)  = Unsur
     * - 8DB4E3 (biru sedang) = Bidang Urusan
     * - DBE5F2 (biru muda) = Program
     * - FCD5B6 (cokelat)   = Kegiatan
     * - FFFFFF (putih)      = Sub Kegiatan
     */
    private function determineLevel($colG, $row, $sheet)
    {
        if (empty(trim($colG ?? ''))) {
            return 'sub_kegiatan';
        }

        $color = $this->getCellBackgroundColor($sheet, 'G', $row);

        switch ($color) {
            case '9BBB59':
            case '558ED5':
                return 'unsur';

            case '8DB4E3':
                return 'bidang_urusan';

            case 'DBE5F2':
                return 'program';

            case 'FCD5B6':
                return 'kegiatan';

            case 'FFFFFF':
            case '000000':
            default:
                return 'sub_kegiatan';
        }
    }

    /**
     * Ambil warna background (fill) dari cell
     */
    private function getCellBackgroundColor($sheet, $col, $row)
    {
        try {
            $style = $sheet->getStyle($col . $row);
            $fill = $style->getFill();

            if ($fill->getFillType() === \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID) {
                return strtoupper($fill->getStartColor()->getRGB());
            }
        } catch (\Exception $e) {
            // Fallback
        }

        return 'FFFFFF';
    }
}
