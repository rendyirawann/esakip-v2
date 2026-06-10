    public function actionCetakEvaluasiRkpdExcel($refperiode_id = null, $refskpd_id = null)
    {
    $user = Yii::$app->user->identity;
    $assignments = Yii::$app->authManager->getAssignments($user->id);

    $skpdList = [];
    $allowedSkpdIds = []; // Daftar ID SKPD yang diizinkan untuk user ini

    if (isset($assignments['superadmin']) || isset($assignments['admin'])) {
    // --- BLOK UNTUK ADMIN ---
    $allSkpd = SakipSkpd::find()->where(['skpd_isaktif' => 'T'])->orderBy('nama_skpd ASC')->all();
    $skpdList = ArrayHelper::map($allSkpd, 'refskpd_id', 'nama_skpd');
    $allowedSkpdIds = array_keys($skpdList);
    } else {
    // --- BLOK UNTUK NON-ADMIN (KOORDINATOR) ---
    $coordinatedSkpdIds = SakipKoordinasi::find()
    ->select('refskpd_id')
    ->where(['refuser_id' => $user->id])
    ->column();

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

    // Set default period to this year if not provided
    if ($refperiode_id === null) {
    $currentYear = date('Y');
    $defaultPeriod = SakipPeriode::find()->where(['periode' => $currentYear])->one();
    $refperiode_id = $defaultPeriod ? $defaultPeriod->refperiode_id : null;
    }

    // Fetch all periods
    $periodeList = SakipPeriode::find()->all();

    $laporanData = [];
    if ($refskpd_id && $refperiode_id) {

    // 1. Query dan pembuatan Peta Triwulan (tetap sama)
    $allProgramTriwulan = SakipIndikatorcascadingprogramTriwulan::find()->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])->all();
    $allKegiatanTriwulan = SakipIndikatorcascadingkegiatanTriwulan::find()->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])->all();
    $allSubkegiatanTriwulan = SakipIndikatorcascadingsubkegiatanTriwulan::find()->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])->all();

    $programTriwulanMap = ArrayHelper::index($allProgramTriwulan, null, 'refcascadingprogram_id');
    $kegiatanTriwulanMap = ArrayHelper::index($allKegiatanTriwulan, null, 'refcascadingkegiatan_id');
    $subkegiatanTriwulanMap = ArrayHelper::index($allSubkegiatanTriwulan, null, 'refcascadingsubkegiatan_id');

    // 2. Query utama, pastikan relasi ke sasaranRenstra ada dan di-load
    $cascadingPrograms = SakipCascadingprogram::find()
    ->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])
    ->with([
    'sasaranRenstra', // PENTING: Eager load relasi ke Sasaran Renstra
    'refProgram',
    'cascadingKegiatans.refKegiatan',
    'cascadingKegiatans.cascadingSubkegiatans.refSubkegiatan'
    ])
    ->all();

    // 3. [BARU] Array untuk proses pengelompokan multi-level
    $groupedBySasaran = [];

    foreach ($cascadingPrograms as $cascadingProgram) {
    // Skip jika data utama tidak lengkap
    if (!$cascadingProgram->sasaranRenstra || !$cascadingProgram->refProgram) {
    continue;
    }

    $sasaranId = $cascadingProgram->refsasaranrenstra_id;
    $programId = $cascadingProgram->refProgram->refprogram_id;

    // Inisialisasi Sasaran jika belum ada
    if (!isset($groupedBySasaran[$sasaranId])) {
    $groupedBySasaran[$sasaranId] = [
    'uraian_sasaranrenstra' => $cascadingProgram->sasaranRenstra->uraian_sasaranrenstra,
    'programs' => [],
    ];
    }

    // Inisialisasi Program jika belum ada di dalam Sasaran tsb
    if (!isset($groupedBySasaran[$sasaranId]['programs'][$programId])) {
    $groupedBySasaran[$sasaranId]['programs'][$programId] = [
    'kode_program' => $cascadingProgram->refProgram->kode_program,
    'nama_program' => $cascadingProgram->refProgram->nama_program,
    'uraian_indikator' => $cascadingProgram->uraian_indikatorprogram,
    'satuan' => $cascadingProgram->program_satuan,
    'target' => $cascadingProgram->program_target,
    'realisasi' => [1 => null, 2 => null, 3 => null, 4 => null],
    'kegiatans' => [],
    ];
    }

    // Proses realisasi program
    $programTriwulans = $programTriwulanMap[$cascadingProgram->refcascadingprogram_id] ?? [];
    foreach ($programTriwulans as $tw) {
    $groupedBySasaran[$sasaranId]['programs'][$programId]['realisasi'][$tw->reftriwulan_id] = $tw->triwulan_realisasi;
    }

    // Proses Kegiatan dan Sub Kegiatan
    foreach ($cascadingProgram->cascadingKegiatans as $cascadingKegiatan) {
    if (!$cascadingKegiatan->refKegiatan) continue;

    $kegiatanId = $cascadingKegiatan->refKegiatan->refkegiatan_id;

    // Inisialisasi Kegiatan jika belum ada di dalam Program tsb
    if (!isset($groupedBySasaran[$sasaranId]['programs'][$programId]['kegiatans'][$kegiatanId])) {
    $groupedBySasaran[$sasaranId]['programs'][$programId]['kegiatans'][$kegiatanId] = [
    'kode_kegiatan' => $cascadingKegiatan->refKegiatan->kode_kegiatan,
    'nama_kegiatan' => $cascadingKegiatan->refKegiatan->nama_kegiatan,
    'uraian_indikator' => $cascadingKegiatan->uraian_indikatorkegiatan,
    'satuan' => $cascadingKegiatan->kegiatan_satuan,
    'target' => $cascadingKegiatan->kegiatan_target,
    'realisasi' => [1 => null, 2 => null, 3 => null, 4 => null],
    'subkegiatans' => [],
    ];
    }

    // Proses realisasi kegiatan
    $kegiatanTriwulans = $kegiatanTriwulanMap[$cascadingKegiatan->refcascadingkegiatan_id] ?? [];
    foreach ($kegiatanTriwulans as $tw) {
    $groupedBySasaran[$sasaranId]['programs'][$programId]['kegiatans'][$kegiatanId]['realisasi'][$tw->reftriwulan_id] = $tw->triwulan_realisasi;
    }

    // Proses Sub Kegiatan
    foreach ($cascadingKegiatan->cascadingSubkegiatans as $subkegiatan) {
    $subkegiatanTriwulans = $subkegiatanTriwulanMap[$subkegiatan->refcascadingsubkegiatan_id] ?? [];
    $realisasiSubKeg = [1 => null, 2 => null, 3 => null, 4 => null];
    $penyerapanSubKeg = [1 => 0, 2 => 0, 3 => 0, 4 => 0];
    foreach ($subkegiatanTriwulans as $tw) {
    $realisasiSubKeg[$tw->reftriwulan_id] = $tw->triwulan_realisasi;
    $penyerapanSubKeg[$tw->reftriwulan_id] = (float) $tw->triwulan_penyerapan_anggaran;
    }

    // Hitung total penyerapan untuk sub kegiatan ini
    $totalPenyerapanSubKeg = array_sum($penyerapanSubKeg);

    // Langsung tambahkan sub kegiatan tanpa grouping lebih lanjut
    $groupedBySasaran[$sasaranId]['programs'][$programId]['kegiatans'][$kegiatanId]['subkegiatans'][] = [
    'kode_subkegiatan' => $subkegiatan->refSubkegiatan->kode_subkegiatan ?? 'N/A',
    'nama_subkegiatan' => $subkegiatan->refSubkegiatan->nama_subkegiatan ?? 'N/A',
    'uraian_indikator' => $subkegiatan->uraian_indikatorsubkegiatan,
    'satuan' => $subkegiatan->subkegiatan_satuan,
    'target' => $subkegiatan->subkegiatan_target,
    'total_anggaran' => (float) $subkegiatan->subkegiatan_anggaran,
    'realisasi' => $realisasiSubKeg,
    'triwulan_penyerapan_anggaran' => $penyerapanSubKeg,
    'total_penyerapan' => $totalPenyerapanSubKeg, // <-- [TAMBAHKAN BARIS INI]
        ];
        }
        }
        }

        // 4. [BARU] Proses agregasi total dan re-indexing array
        foreach ($groupedBySasaran as $s_id=> &$sasaran) { // by reference
        foreach ($sasaran['programs'] as $p_id => &$program) { // by reference
        foreach ($program['kegiatans'] as $k_id => &$kegiatan) { // by reference
        $kegiatan['total_anggaran'] = array_sum(ArrayHelper::getColumn($kegiatan['subkegiatans'], 'total_anggaran'));
        $kegiatan['total_penyerapan'] = array_sum(ArrayHelper::getColumn($kegiatan['subkegiatans'], 'total_penyerapan'));
        $kegiatan['total_realisasi'] = array_sum(array_filter($kegiatan['realisasi'], 'is_numeric'));
        $kegiatan['triwulan_penyerapan_anggaran'] = [
        1 => array_sum(ArrayHelper::getColumn($kegiatan['subkegiatans'], ['triwulan_penyerapan_anggaran', 1])),
        2 => array_sum(ArrayHelper::getColumn($kegiatan['subkegiatans'], ['triwulan_penyerapan_anggaran', 2])),
        3 => array_sum(ArrayHelper::getColumn($kegiatan['subkegiatans'], ['triwulan_penyerapan_anggaran', 3])),
        4 => array_sum(ArrayHelper::getColumn($kegiatan['subkegiatans'], ['triwulan_penyerapan_anggaran', 4])),
        ];
        }
        $program['kegiatans'] = array_values($program['kegiatans']); // Re-index kegiatan

        $program['total_anggaran'] = array_sum(ArrayHelper::getColumn($program['kegiatans'], 'total_anggaran'));
        $program['total_penyerapan'] = array_sum(ArrayHelper::getColumn($program['kegiatans'], 'total_penyerapan'));
        $program['total_realisasi'] = array_sum(array_filter($program['realisasi'], 'is_numeric'));
        $program['triwulan_penyerapan_anggaran'] = [
        1 => array_sum(ArrayHelper::getColumn($program['kegiatans'], ['triwulan_penyerapan_anggaran', 1])),
        2 => array_sum(ArrayHelper::getColumn($program['kegiatans'], ['triwulan_penyerapan_anggaran', 2])),
        3 => array_sum(ArrayHelper::getColumn($program['kegiatans'], ['triwulan_penyerapan_anggaran', 3])),
        4 => array_sum(ArrayHelper::getColumn($program['kegiatans'], ['triwulan_penyerapan_anggaran', 4])),
        ];
        }
        $sasaran['programs'] = array_values($sasaran['programs']); // Re-index program
        }

        // Set data final untuk dikirim ke view
        $laporanData = array_values($groupedBySasaran); // Re-index sasaran
        }

        // Retrieve the periode based on refperiode_id
        $selectedPeriod = SakipPeriode::find()->where(['refperiode_id' => $refperiode_id])->one();
        $selectedPeriodValue = $selectedPeriod ? $selectedPeriod->periode : null; // Get the periode value

        // Set the dynamic title
        $this->view->title = "Laporan Renstra $selectedPeriodValue - " . Html::encode($nama_skpd);

        // --- MULAI PERUBAHAN DARI SINI ---

        // 2. Buat objek Spreadsheet baru
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Laporan Evaluasi RKPD');

        // 3. Tulis Judul Laporan (disesuaikan untuk kolom baru Q)
        $sheet->mergeCells('A1:Q1')->setCellValue('A1', 'LAPORAN EVALUASI HASIL RKPD TERHADAP RPJMD');
        $sheet->mergeCells('A2:Q2')->setCellValue('A2', strtoupper($nama_skpd));
        $sheet->mergeCells('A3:Q3')->setCellValue('A3', 'PERIODE ' . $selectedPeriodValue);
        $sheet->getStyle('A1:A3')->getFont()->setBold(true);
        $sheet->getStyle('A1:A3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // 4. Tulis Header Tabel yang Kompleks (disesuaikan dengan penambahan kolom A)
        $headerStyle = [
        'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
        'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrapText' => true],
        'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF04A9F5']] // Warna biru header
        ];

        // Baris Header
        $sheet->mergeCells('A5:A7')->setCellValue('A5', 'Sasaran Strategis'); // KOLOM BARU
        $sheet->mergeCells('B5:B7')->setCellValue('B5', 'Kode');
        $sheet->mergeCells('C5:C7')->setCellValue('C5', 'Program / Kegiatan / Sub Kegiatan');
        $sheet->mergeCells('D5:D7')->setCellValue('D5', 'Indikator');
        $sheet->mergeCells('E5:E7')->setCellValue('E5', 'Satuan');
        $sheet->mergeCells('F5:F7')->setCellValue('F5', 'Target');
        $sheet->mergeCells('G5:G7')->setCellValue('G5', 'Anggaran');
        $sheet->mergeCells('H5:O5')->setCellValue('H5', 'Realisasi Kinerja Pada Triwulan');
        $sheet->mergeCells('P5:Q6')->setCellValue('P5', 'Total Realisasi s/d Triwulan IV');

        // Sub-Header Triwulan
        $sheet->mergeCells('H6:I6')->setCellValue('H6', 'I');
        $sheet->mergeCells('J6:K6')->setCellValue('J6', 'II');
        $sheet->mergeCells('L6:M6')->setCellValue('L6', 'III');
        $sheet->mergeCells('N6:O6')->setCellValue('N6', 'IV');
        $sheet->setCellValue('H7', 'Realisasi')->setCellValue('I7', 'Penyerapan Anggaran');
        $sheet->setCellValue('J7', 'Realisasi')->setCellValue('K7', 'Penyerapan Anggaran');
        $sheet->setCellValue('L7', 'Realisasi')->setCellValue('M7', 'Penyerapan Anggaran');
        $sheet->setCellValue('N7', 'Realisasi')->setCellValue('O7', 'Penyerapan Anggaran');
        $sheet->setCellValue('P7', 'Total Realisasi')->setCellValue('Q7', 'Total Penyerapan Anggaran');

        // Terapkan style ke semua header
        $sheet->getStyle('A5:Q7')->applyFromArray($headerStyle);

        // 5. [PERUBAHAN TOTAL] Loop data dengan penggabungan sel untuk kolom Sasaran
        $row = 8; // Mulai dari baris ke-8
        $programStyle = ['font' => ['bold' => true]];

        foreach ($laporanData as $sasaran) {
        $startRowForMerge = $row; // Tandai baris awal untuk merge

        // Hitung dulu berapa banyak baris yang akan digunakan oleh sasaran ini
        $rowCountForSasaran = 0;
        foreach ($sasaran['programs'] as $program) {
        $rowCountForSasaran++; // 1 baris untuk program
        foreach ($program['kegiatans'] as $kegiatan) {
        $rowCountForSasaran++; // 1 baris untuk kegiatan
        $rowCountForSasaran += count($kegiatan['subkegiatans']); // Jumlah baris sub kegiatan
        }
        }

        // Jika tidak ada program, lewati sasaran ini
        if ($rowCountForSasaran == 0) continue;

        // Lakukan merge untuk kolom Sasaran
        $endRowForMerge = $startRowForMerge + $rowCountForSasaran - 1;
        $sheet->mergeCells('A' . $startRowForMerge . ':A' . $endRowForMerge);
        $sheet->setCellValue('A' . $startRowForMerge, $sasaran['uraian_sasaranrenstra']);
        $sheet->getStyle('A' . $startRowForMerge)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER)->setWrapText(true);

        // Sekarang, loop untuk mengisi data program, kegiatan, dan sub kegiatan
        foreach ($sasaran['programs'] as $program) {
        $sheet->fromArray([
        // Kolom A (Sasaran) sengaja dikosongkan karena sudah di-merge
        '',
        $program['kode_program'],
        $program['nama_program'],
        $program['uraian_indikator'],
        $program['satuan'],
        $program['target'],
        $program['total_anggaran'],
        $program['realisasi'][1],
        $program['triwulan_penyerapan_anggaran'][1],
        $program['realisasi'][2],
        $program['triwulan_penyerapan_anggaran'][2],
        $program['realisasi'][3],
        $program['triwulan_penyerapan_anggaran'][3],
        $program['realisasi'][4],
        $program['triwulan_penyerapan_anggaran'][4],
        $program['total_realisasi'],
        $program['total_penyerapan']
        ], null, 'A' . $row);
        $sheet->getStyle('C' . $row)->applyFromArray($programStyle); // Nama program bold
        $row++;

        foreach ($program['kegiatans'] as $kegiatan) {
        $sheet->fromArray([
        '',
        $kegiatan['kode_kegiatan'],
        $kegiatan['nama_kegiatan'],
        $kegiatan['uraian_indikator'],
        $kegiatan['satuan'],
        $kegiatan['target'],
        $kegiatan['total_anggaran'],
        $kegiatan['realisasi'][1],
        $kegiatan['triwulan_penyerapan_anggaran'][1],
        $kegiatan['realisasi'][2],
        $kegiatan['triwulan_penyerapan_anggaran'][2],
        $kegiatan['realisasi'][3],
        $kegiatan['triwulan_penyerapan_anggaran'][3],
        $kegiatan['realisasi'][4],
        $kegiatan['triwulan_penyerapan_anggaran'][4],
        $kegiatan['total_realisasi'],
        $kegiatan['total_penyerapan']
        ], null, 'A' . $row);
        $sheet->getStyle('C' . $row)->getAlignment()->setIndent(1); // Indentasi Kegiatan
        $row++;

        foreach ($kegiatan['subkegiatans'] as $subkegiatan) {
        $totalRealisasiSubKeg = array_sum(array_filter($subkegiatan['realisasi'], 'is_numeric'));
        $sheet->fromArray([
        '',
        $subkegiatan['kode_subkegiatan'],
        $subkegiatan['nama_subkegiatan'],
        $subkegiatan['uraian_indikator'],
        $subkegiatan['satuan'],
        $subkegiatan['target'],
        $subkegiatan['total_anggaran'],
        $subkegiatan['realisasi'][1],
        $subkegiatan['triwulan_penyerapan_anggaran'][1],
        $subkegiatan['realisasi'][2],
        $subkegiatan['triwulan_penyerapan_anggaran'][2],
        $subkegiatan['realisasi'][3],
        $subkegiatan['triwulan_penyerapan_anggaran'][3],
        $subkegiatan['realisasi'][4],
        $subkegiatan['triwulan_penyerapan_anggaran'][4],
        $totalRealisasiSubKeg,
        $subkegiatan['total_penyerapan']
        ], null, 'A' . $row);
        $sheet->getStyle('C' . $row)->getAlignment()->setIndent(2); // Indentasi Sub Kegiatan
        $row++;
        }
        }
        }
        }

        // 6. Format Angka dan Lebar Kolom (disesuaikan untuk kolom baru Q)
        $lastRow = $row - 1;
        $numberFormat = '#,##0';
        if ($lastRow >= 8) {
        // Format kolom Anggaran dan Penyerapan
        $sheet->getStyle('G8:G' . $lastRow)->getNumberFormat()->setFormatCode($numberFormat);
        $sheet->getStyle('I8:I' . $lastRow)->getNumberFormat()->setFormatCode($numberFormat);
        $sheet->getStyle('K8:K' . $lastRow)->getNumberFormat()->setFormatCode($numberFormat);
        $sheet->getStyle('M8:M' . $lastRow)->getNumberFormat()->setFormatCode($numberFormat);
        $sheet->getStyle('O8:O' . $lastRow)->getNumberFormat()->setFormatCode($numberFormat);
        $sheet->getStyle('Q8:Q' . $lastRow)->getNumberFormat()->setFormatCode($numberFormat);
        }

        // Atur lebar kolom
        $sheet->getColumnDimension('A')->setWidth(25); // Kolom Sasaran
        $sheet->getColumnDimension('B')->setAutoSize(true); // Kolom Kode
        $sheet->getColumnDimension('C')->setWidth(45); // Kolom Program/Kegiatan
        $sheet->getColumnDimension('D')->setWidth(30); // Kolom Indikator
        foreach (range('E', 'Q') as $columnID) {
        $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        // 7. Siapkan file untuk di-download
        $fileName = "Laporan_Evaluasi_RKPD_{$nama_skpd}_{$selectedPeriodValue}.xlsx";
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . urlencode($fileName) . '"');
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit();
        }