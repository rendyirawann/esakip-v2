 public function actionIndexEvaluasiRkpdDev($refperiode_id = null, $refskpd_id = null)
 {
 $user = Yii::$app->user->identity;
 $refskpd_id = $user->refskpd_id;
 $assignments = Yii::$app->authManager->getAssignments($user->id);


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

     return $this->render('index-evaluasi-rkpd-dev', [
     'periodeList' => $periodeList,
     'selectedPeriodId' => $refperiode_id,
     'refskpd_id' => $refskpd_id,
     'refperiode_id' => $refperiode_id,
     'nama_skpd' => $nama_skpd,
     'selectedPeriodValue' => $selectedPeriodValue, // Include selected period value
     'selectedSkpdId' => $refskpd_id,
     'skpdList' => $skpdList,
     'laporanData' => $laporanData, // <-- Kirim data laporan ke view
         ]);
         }