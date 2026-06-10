<?php

use yii\db\Migration;

/**
 * Migration: Rekonstruksi Total Database SAKIP ke Periode 5 Tahunan & Tahunan.
 * Membuat tabel baru dengan prefix v2_ dan memindahkan data lama.
 */
class m260603_120000_reconstruct_sakip_to_5yearly extends Migration
{
    public function safeUp()
    {
        // 1. Buat Tabel Master Periode 5 Tahunan
        $this->execute("DROP TABLE IF EXISTS `v2_sakip_periode_5tahun`");
        $this->execute("
            CREATE TABLE `v2_sakip_periode_5tahun` (
              `refperiode_5tahun_id` bigint(20) NOT NULL AUTO_INCREMENT,
              `tahun_mulai` int(11) NOT NULL,
              `tahun_selesai` int(11) NOT NULL,
              `nama_periode` varchar(50) NOT NULL,
              `is_aktif` char(1) DEFAULT '0',
              PRIMARY KEY (`refperiode_5tahun_id`) USING BTREE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC;
        ");

        // Insert Default Periode 5 Tahunan
        $this->insert('v2_sakip_periode_5tahun', [
            'refperiode_5tahun_id' => 1,
            'tahun_mulai' => 2019,
            'tahun_selesai' => 2023,
            'nama_periode' => 'Periode RPJMD 2019-2023',
            'is_aktif' => '0'
        ]);

        $this->insert('v2_sakip_periode_5tahun', [
            'refperiode_5tahun_id' => 2,
            'tahun_mulai' => 2024,
            'tahun_selesai' => 2029,
            'nama_periode' => 'Periode RPJMD 2025-2029',
            'is_aktif' => '1'
        ]);

        // 2. Duplikasi tabel-tabel lama ke v2_
        $tablesToCopy = [
            'sakip_periode',
            'sakip_visi', 'sakip_visi_p',
            'sakip_misi', 'sakip_misi_p',
            'sakip_tujuan', 'sakip_tujuan_p',
            'sakip_sasaran', 'sakip_sasaran_p',
            'sakip_tujuanrenstra', 'sakip_tujuanrenstra_p',
            'sakip_sasaranrenstra', 'sakip_sasaranrenstra_p',
            'sakip_indikatortujuanrenstra',
            'sakip_indikatorsasaranrenstra', 'sakip_indikatorsasaranrenstra_p',
            'sakip_indikatorsasaranrenstra_triwulan', 'sakip_indikatorsasaranrenstra_p_triwulan',
            'sakip_strategi', 'sakip_kebijakan',
            'sakip_cascadingprogram', 'sakip_cascadingkegiatan', 'sakip_cascadingsubkegiatan',
            'sakip_indikatorcascadingprogram', 'sakip_indikatorcascadingkegiatan', 'sakip_indikatorcascadingsubkegiatan',
            'sakip_indikatorcascadingprogram_triwulan', 'sakip_indikatorcascadingkegiatan_triwulan', 'sakip_indikatorcascadingsubkegiatan_triwulan',
            'sakip_penjabatskpd_cascadingprogram', 'sakip_penjabatskpd_cascadingkegiatan', 'sakip_penjabatskpd_cascadingsubkegiatan'
        ];

        foreach ($tablesToCopy as $table) {
            $this->execute("DROP TABLE IF EXISTS `v2_{$table}`");
            $this->execute("CREATE TABLE `v2_{$table}` LIKE `{$table}`");
        }

        // 4. Migrasi data sakip_periode ke v2_sakip_periode (lakukan sebelum menambah kolom baru)
        $this->execute("INSERT INTO `v2_sakip_periode` SELECT * FROM `sakip_periode`");

        // 3. Tambahkan kolom refperiode_5tahun_id dan sesuaikan kolom refperiode_id
        $this->execute("ALTER TABLE `v2_sakip_periode` ADD COLUMN `refperiode_5tahun_id` bigint(20) NULL AFTER `periode`");

        $fiveYearTables = [
            'sakip_visi' => 'penjabaran_visi',
            'sakip_visi_p' => 'penjabaran_visi_p',
            'sakip_misi' => 'uraian_misi',
            'sakip_misi_p' => 'uraian_misi_p',
            'sakip_tujuan' => 'refmisi_id',
            'sakip_tujuan_p' => 'refmisi_p_id',
            'sakip_sasaran' => 'reftujuan_id',
            'sakip_sasaran_p' => 'reftujuan_p_id',
            'sakip_tujuanrenstra' => 'refsasaran_id',
            'sakip_tujuanrenstra_p' => 'refsasaran_p_id',
            'sakip_sasaranrenstra' => 'reftujuanrenstra_id',
            'sakip_sasaranrenstra_p' => 'reftujuanrenstra_p_id',
            'sakip_strategi' => 'reftujuan_id',
            'sakip_kebijakan' => 'reftujuan_id',
        ];

        foreach ($fiveYearTables as $table => $afterCol) {
            $this->execute("ALTER TABLE `v2_{$table}` DROP COLUMN `refperiode_id`");
            $this->execute("ALTER TABLE `v2_{$table}` ADD COLUMN `refperiode_5tahun_id` bigint(20) NULL AFTER `{$afterCol}`");
        }

        $this->execute("UPDATE `v2_sakip_periode` SET `refperiode_5tahun_id` = 1 WHERE `periode` BETWEEN 2019 AND 2023");
        $this->execute("UPDATE `v2_sakip_periode` SET `refperiode_5tahun_id` = 2 WHERE `periode` BETWEEN 2024 AND 2029");

        $transaction = $this->db->beginTransaction();
        try {

        // Ambil pemetaan refperiode_id ke refperiode_5tahun_id
        $periodeMap = [];
        $periodeRows = (new \yii\db\Query())->from('v2_sakip_periode')->all();
        foreach ($periodeRows as $row) {
            $periodeMap[$row['refperiode_id']] = $row['refperiode_5tahun_id'];
        }

        // 5. Migrasi data dengan pemetaan ID (Deduplikasi & Integritas Data)
        
        // --- A. Visi ---
        $visiMap = [];
        $inserted_visi = [];
        $oldVisiRows = (new \yii\db\Query())->from('sakip_visi')->all();
        foreach ($oldVisiRows as $row) {
            $p5Id = isset($periodeMap[$row['refperiode_id']]) ? $periodeMap[$row['refperiode_id']] : 2;
            $key = $row['uraian_visi'] . '|' . $p5Id;
            if (isset($inserted_visi[$key])) {
                $visiMap[$row['refvisi_id']] = $inserted_visi[$key];
            } else {
                $this->db->createCommand()->insert('v2_sakip_visi', [
                    'uraian_visi' => $row['uraian_visi'],
                    'penjabaran_visi' => $row['penjabaran_visi'],
                    'refperiode_5tahun_id' => $p5Id,
                    'visi_isaktif' => $row['visi_isaktif'],
                ])->execute();
                $newId = $this->db->getLastInsertID();
                $inserted_visi[$key] = $newId;
                $visiMap[$row['refvisi_id']] = $newId;
            }
        }

        // --- B. Visi Perubahan (P) ---
        $visiPMap = [];
        $inserted_visi_p = [];
        $oldVisiPRows = (new \yii\db\Query())->from('sakip_visi_p')->all();
        foreach ($oldVisiPRows as $row) {
            $p5Id = isset($periodeMap[$row['refperiode_id']]) ? $periodeMap[$row['refperiode_id']] : 2;
            $key = $row['uraian_visi_p'] . '|' . $p5Id;
            if (isset($inserted_visi_p[$key])) {
                $visiPMap[$row['refvisi_p_id']] = $inserted_visi_p[$key];
            } else {
                $this->db->createCommand()->insert('v2_sakip_visi_p', [
                    'uraian_visi_p' => $row['uraian_visi_p'],
                    'penjabaran_visi_p' => $row['penjabaran_visi_p'],
                    'refperiode_5tahun_id' => $p5Id,
                    'visi_p_isaktif' => $row['visi_p_isaktif'],
                ])->execute();
                $newId = $this->db->getLastInsertID();
                $inserted_visi_p[$key] = $newId;
                $visiPMap[$row['refvisi_p_id']] = $newId;
            }
        }

        // --- C. Misi ---
        $misiMap = [];
        $inserted_misi = [];
        $oldMisiRows = (new \yii\db\Query())->from('sakip_misi')->all();
        foreach ($oldMisiRows as $row) {
            $p5Id = isset($periodeMap[$row['refperiode_id']]) ? $periodeMap[$row['refperiode_id']] : 2;
            $newVisiId = isset($visiMap[$row['refvisi_id']]) ? $visiMap[$row['refvisi_id']] : null;
            $key = $row['uraian_misi'] . '|' . $p5Id . '|' . $newVisiId;
            if (isset($inserted_misi[$key])) {
                $misiMap[$row['refmisi_id']] = $inserted_misi[$key];
            } else {
                $this->db->createCommand()->insert('v2_sakip_misi', [
                    'uraian_misi' => $row['uraian_misi'],
                    'refvisi_id' => $newVisiId,
                    'refperiode_5tahun_id' => $p5Id,
                    'misi_isaktif' => $row['misi_isaktif'],
                ])->execute();
                $newId = $this->db->getLastInsertID();
                $inserted_misi[$key] = $newId;
                $misiMap[$row['refmisi_id']] = $newId;
            }
        }

        // --- D. Misi Perubahan (P) ---
        $misiPMap = [];
        $inserted_misi_p = [];
        $oldMisiPRows = (new \yii\db\Query())->from('sakip_misi_p')->all();
        foreach ($oldMisiPRows as $row) {
            $p5Id = isset($periodeMap[$row['refperiode_id']]) ? $periodeMap[$row['refperiode_id']] : 2;
            $newVisiPId = isset($visiPMap[$row['refvisi_p_id']]) ? $visiPMap[$row['refvisi_p_id']] : null;
            $key = $row['uraian_misi_p'] . '|' . $p5Id . '|' . $newVisiPId;
            if (isset($inserted_misi_p[$key])) {
                $misiPMap[$row['refmisi_p_id']] = $inserted_misi_p[$key];
            } else {
                $this->db->createCommand()->insert('v2_sakip_misi_p', [
                    'uraian_misi_p' => $row['uraian_misi_p'],
                    'refvisi_p_id' => $newVisiPId,
                    'refperiode_5tahun_id' => $p5Id,
                    'misi_p_isaktif' => $row['misi_p_isaktif'],
                ])->execute();
                $newId = $this->db->getLastInsertID();
                $inserted_misi_p[$key] = $newId;
                $misiPMap[$row['refmisi_p_id']] = $newId;
            }
        }

        // --- E. Tujuan ---
        $tujuanMap = [];
        $inserted_tujuan = [];
        $oldTujuanRows = (new \yii\db\Query())->from('sakip_tujuan')->all();
        foreach ($oldTujuanRows as $row) {
            $p5Id = isset($periodeMap[$row['refperiode_id']]) ? $periodeMap[$row['refperiode_id']] : 2;
            $newVisiId = isset($visiMap[$row['refvisi_id']]) ? $visiMap[$row['refvisi_id']] : null;
            $newMisiId = isset($misiMap[$row['refmisi_id']]) ? $misiMap[$row['refmisi_id']] : null;
            $key = $row['uraian_tujuan'] . '|' . $p5Id . '|' . $newMisiId;
            if (isset($inserted_tujuan[$key])) {
                $tujuanMap[$row['reftujuan_id']] = $inserted_tujuan[$key];
            } else {
                $this->db->createCommand()->insert('v2_sakip_tujuan', [
                    'uraian_tujuan' => $row['uraian_tujuan'],
                    'indikator_tujuan' => $row['indikator_tujuan'],
                    'refvisi_id' => $newVisiId,
                    'refmisi_id' => $newMisiId,
                    'refperiode_5tahun_id' => $p5Id,
                    'tujuan_isaktif' => $row['tujuan_isaktif'],
                ])->execute();
                $newId = $this->db->getLastInsertID();
                $inserted_tujuan[$key] = $newId;
                $tujuanMap[$row['reftujuan_id']] = $newId;
            }
        }

        // --- F. Tujuan Perubahan (P) ---
        $tujuanPMap = [];
        $inserted_tujuan_p = [];
        $oldTujuanPRows = (new \yii\db\Query())->from('sakip_tujuan_p')->all();
        foreach ($oldTujuanPRows as $row) {
            $p5Id = isset($periodeMap[$row['refperiode_id']]) ? $periodeMap[$row['refperiode_id']] : 2;
            $newVisiPId = isset($visiPMap[$row['refvisi_p_id']]) ? $visiPMap[$row['refvisi_p_id']] : null;
            $newMisiPId = isset($misiPMap[$row['refmisi_p_id']]) ? $misiPMap[$row['refmisi_p_id']] : null;
            $key = $row['uraian_tujuan_p'] . '|' . $p5Id . '|' . $newMisiPId;
            if (isset($inserted_tujuan_p[$key])) {
                $tujuanPMap[$row['reftujuan_p_id']] = $inserted_tujuan_p[$key];
            } else {
                $this->db->createCommand()->insert('v2_sakip_tujuan_p', [
                    'uraian_tujuan_p' => $row['uraian_tujuan_p'],
                    'indikator_tujuan_p' => $row['indikator_tujuan_p'],
                    'refvisi_p_id' => $newVisiPId,
                    'refmisi_p_id' => $newMisiPId,
                    'refperiode_5tahun_id' => $p5Id,
                    'tujuan_p_isaktif' => $row['tujuan_p_isaktif'],
                ])->execute();
                $newId = $this->db->getLastInsertID();
                $inserted_tujuan_p[$key] = $newId;
                $tujuanPMap[$row['reftujuan_p_id']] = $newId;
            }
        }

        // --- G. Sasaran ---
        $sasaranMap = [];
        $inserted_sasaran = [];
        $oldSasaranRows = (new \yii\db\Query())->from('sakip_sasaran')->all();
        foreach ($oldSasaranRows as $row) {
            $p5Id = isset($periodeMap[$row['refperiode_id']]) ? $periodeMap[$row['refperiode_id']] : 2;
            $newVisiId = isset($visiMap[$row['refvisi_id']]) ? $visiMap[$row['refvisi_id']] : null;
            $newMisiId = isset($misiMap[$row['refmisi_id']]) ? $misiMap[$row['refmisi_id']] : null;
            $newTujuanId = isset($tujuanMap[$row['reftujuan_id']]) ? $tujuanMap[$row['reftujuan_id']] : null;
            $key = $row['uraian_sasaran'] . '|' . $p5Id . '|' . $newTujuanId;
            if (isset($inserted_sasaran[$key])) {
                $sasaranMap[$row['refsasaran_id']] = $inserted_sasaran[$key];
            } else {
                $this->db->createCommand()->insert('v2_sakip_sasaran', [
                    'uraian_sasaran' => $row['uraian_sasaran'],
                    'refvisi_id' => $newVisiId,
                    'refmisi_id' => $newMisiId,
                    'reftujuan_id' => $newTujuanId,
                    'refperiode_5tahun_id' => $p5Id,
                    'sasaran_isaktif' => $row['sasaran_isaktif'],
                ])->execute();
                $newId = $this->db->getLastInsertID();
                $inserted_sasaran[$key] = $newId;
                $sasaranMap[$row['refsasaran_id']] = $newId;
            }
        }

        // --- H. Sasaran Perubahan (P) ---
        $sasaranPMap = [];
        $inserted_sasaran_p = [];
        $oldSasaranPRows = (new \yii\db\Query())->from('sakip_sasaran_p')->all();
        foreach ($oldSasaranPRows as $row) {
            $p5Id = isset($periodeMap[$row['refperiode_id']]) ? $periodeMap[$row['refperiode_id']] : 2;
            $newVisiPId = isset($visiPMap[$row['refvisi_p_id']]) ? $visiPMap[$row['refvisi_p_id']] : null;
            $newMisiPId = isset($misiPMap[$row['refmisi_p_id']]) ? $misiPMap[$row['refmisi_p_id']] : null;
            $newTujuanPId = isset($tujuanPMap[$row['reftujuan_p_id']]) ? $tujuanPMap[$row['reftujuan_p_id']] : null;
            $key = $row['uraian_sasaran_p'] . '|' . $p5Id . '|' . $newTujuanPId;
            if (isset($inserted_sasaran_p[$key])) {
                $sasaranPMap[$row['refsasaran_p_id']] = $inserted_sasaran_p[$key];
            } else {
                $this->db->createCommand()->insert('v2_sakip_sasaran_p', [
                    'uraian_sasaran_p' => $row['uraian_sasaran_p'],
                    'refvisi_p_id' => $newVisiPId,
                    'refmisi_p_id' => $newMisiPId,
                    'reftujuan_p_id' => $newTujuanPId,
                    'refperiode_5tahun_id' => $p5Id,
                    'sasaran_p_isaktif' => $row['sasaran_p_isaktif'],
                ])->execute();
                $newId = $this->db->getLastInsertID();
                $inserted_sasaran_p[$key] = $newId;
                $sasaranPMap[$row['refsasaran_p_id']] = $newId;
            }
        }

        // --- I. Tujuan Renstra & Sasaran Renstra (Interlinked) ---
        // Karena saling merujuk, kita migrasikan Tujuan Renstra dulu tanpa menyetel refsasaranrenstra_id, 
        // lalu migrasikan Sasaran Renstra, dan terakhir perbarui Tujuan Renstra.
        $tujuanRenstraMap = [];
        $inserted_tujuanrenstra = [];
        $oldTujuanRenstraRows = (new \yii\db\Query())->from('sakip_tujuanrenstra')->all();
        foreach ($oldTujuanRenstraRows as $row) {
            $p5Id = isset($periodeMap[$row['refperiode_id']]) ? $periodeMap[$row['refperiode_id']] : 2;
            $newMisiId = isset($misiMap[$row['refmisi_id']]) ? $misiMap[$row['refmisi_id']] : 0;
            $newTujuanId = isset($tujuanMap[$row['reftujuan_id']]) ? $tujuanMap[$row['reftujuan_id']] : 0;
            $newSasaranId = isset($sasaranMap[$row['refsasaran_id']]) ? $sasaranMap[$row['refsasaran_id']] : 0;
            
            $key = $row['uraian_tujuanrenstra'] . '|' . $row['refskpd_id'] . '|' . $p5Id;
            if (isset($inserted_tujuanrenstra[$key])) {
                $tujuanRenstraMap[$row['reftujuanrenstra_id']] = $inserted_tujuanrenstra[$key];
            } else {
                $this->db->createCommand()->insert('v2_sakip_tujuanrenstra', [
                    'uraian_tujuanrenstra' => $row['uraian_tujuanrenstra'],
                    'refskpd_id' => $row['refskpd_id'],
                    'refmisi_id' => $newMisiId,
                    'reftujuan_id' => $newTujuanId,
                    'refsasaranrenstra_id' => 0, // Placeholder
                    'refsasaran_id' => $newSasaranId,
                    'refperiode_5tahun_id' => $p5Id,
                    'user_create' => $row['user_create'],
                    'date_create' => $row['date_create'],
                ])->execute();
                $newId = $this->db->getLastInsertID();
                $inserted_tujuanrenstra[$key] = $newId;
                $tujuanRenstraMap[$row['reftujuanrenstra_id']] = $newId;
            }
        }

        $sasaranRenstraMap = [];
        $inserted_sasaranrenstra = [];
        $oldSasaranRenstraRows = (new \yii\db\Query())->from('sakip_sasaranrenstra')->all();
        foreach ($oldSasaranRenstraRows as $row) {
            $p5Id = isset($periodeMap[$row['refperiode_id']]) ? $periodeMap[$row['refperiode_id']] : 2;
            $newVisiId = isset($visiMap[$row['refvisi_id']]) ? $visiMap[$row['refvisi_id']] : 0;
            $newMisiId = isset($misiMap[$row['refmisi_id']]) ? $misiMap[$row['refmisi_id']] : 0;
            $newTujuanId = isset($tujuanMap[$row['reftujuan_id']]) ? $tujuanMap[$row['reftujuan_id']] : 0;
            $newSasaranId = isset($sasaranMap[$row['refsasaran_id']]) ? $sasaranMap[$row['refsasaran_id']] : 0;
            $newTujuanRenstraId = isset($tujuanRenstraMap[$row['reftujuanrenstra_id']]) ? $tujuanRenstraMap[$row['reftujuanrenstra_id']] : 0;

            $key = $row['uraian_sasaranrenstra'] . '|' . $row['refskpd_id'] . '|' . $p5Id;
            if (isset($inserted_sasaranrenstra[$key])) {
                $sasaranRenstraMap[$row['refsasaranrenstra_id']] = $inserted_sasaranrenstra[$key];
            } else {
                $this->db->createCommand()->insert('v2_sakip_sasaranrenstra', [
                    'uraian_sasaranrenstra' => $row['uraian_sasaranrenstra'],
                    'refskpd_id' => $row['refskpd_id'],
                    'refsasaran_id' => $newSasaranId,
                    'refvisi_id' => $newVisiId,
                    'refmisi_id' => $newMisiId,
                    'reftujuan_id' => $newTujuanId,
                    'refperiode_5tahun_id' => $p5Id,
                    'reftujuanrenstra_id' => $newTujuanRenstraId,
                    'sasaranrenstra_isaktif' => $row['sasaranrenstra_isaktif'],
                    'alasan_sasaranrenstra' => $row['alasan_sasaranrenstra'],
                    'formulasi_sasaranrenstra' => $row['formulasi_sasaranrenstra'],
                    'kriteria_sasaranrenstra' => $row['kriteria_sasaranrenstra'],
                ])->execute();
                $newId = $this->db->getLastInsertID();
                $inserted_sasaranrenstra[$key] = $newId;
                $sasaranRenstraMap[$row['refsasaranrenstra_id']] = $newId;
            }
        }

        // Perbarui refsasaranrenstra_id di v2_sakip_tujuanrenstra menggunakan map sasaran renstra
        foreach ($oldTujuanRenstraRows as $row) {
            $newTujuanRenstraId = $tujuanRenstraMap[$row['reftujuanrenstra_id']];
            $newSasaranRenstraId = isset($sasaranRenstraMap[$row['refsasaranrenstra_id']]) ? $sasaranRenstraMap[$row['refsasaranrenstra_id']] : 0;
            $this->db->createCommand()->update('v2_sakip_tujuanrenstra', ['refsasaranrenstra_id' => $newSasaranRenstraId], ['reftujuanrenstra_id' => $newTujuanRenstraId])->execute();
        }

        // --- J. Tujuan & Sasaran Renstra Perubahan (P) ---
        $tujuanRenstraPMap = [];
        $inserted_tujuanrenstra_p = [];
        $oldTujuanRenstraPRows = (new \yii\db\Query())->from('sakip_tujuanrenstra_p')->all();
        foreach ($oldTujuanRenstraPRows as $row) {
            $p5Id = isset($periodeMap[$row['refperiode_id']]) ? $periodeMap[$row['refperiode_id']] : 2;
            $newMisiPId = isset($misiPMap[$row['refmisi_p_id']]) ? $misiPMap[$row['refmisi_p_id']] : 0;
            $newTujuanPId = isset($tujuanPMap[$row['reftujuan_p_id']]) ? $tujuanPMap[$row['reftujuan_p_id']] : 0;
            $newSasaranPId = isset($sasaranPMap[$row['refsasaran_p_id']]) ? $sasaranPMap[$row['refsasaran_p_id']] : 0;
            
            $key = $row['uraian_tujuanrenstra_p'] . '|' . $row['refskpd_id'] . '|' . $p5Id;
            if (isset($inserted_tujuanrenstra_p[$key])) {
                $tujuanRenstraPMap[$row['reftujuanrenstra_p_id']] = $inserted_tujuanrenstra_p[$key];
            } else {
                $this->db->createCommand()->insert('v2_sakip_tujuanrenstra_p', [
                    'reftujuanrenstra_id' => isset($tujuanRenstraMap[$row['reftujuanrenstra_id']]) ? $tujuanRenstraMap[$row['reftujuanrenstra_id']] : 0,
                    'uraian_tujuanrenstra_p' => $row['uraian_tujuanrenstra_p'],
                    'refskpd_id' => $row['refskpd_id'],
                    'refmisi_p_id' => $newMisiPId,
                    'reftujuan_p_id' => $newTujuanPId,
                    'refsasaranrenstra_p_id' => 0, // Placeholder
                    'refsasaran_p_id' => $newSasaranPId,
                    'refperiode_5tahun_id' => $p5Id,
                    'user_create' => $row['user_create'],
                    'date_create' => $row['date_create'],
                ])->execute();
                $newId = $this->db->getLastInsertID();
                $inserted_tujuanrenstra_p[$key] = $newId;
                $tujuanRenstraPMap[$row['reftujuanrenstra_p_id']] = $newId;
            }
        }

        $sasaranRenstraPMap = [];
        $inserted_sasaranrenstra_p = [];
        $oldSasaranRenstraPRows = (new \yii\db\Query())->from('sakip_sasaranrenstra_p')->all();
        foreach ($oldSasaranRenstraPRows as $row) {
            $p5Id = isset($periodeMap[$row['refperiode_id']]) ? $periodeMap[$row['refperiode_id']] : 2;
            $newVisiPId = isset($visiPMap[$row['refvisi_p_id']]) ? $visiPMap[$row['refvisi_p_id']] : 0;
            $newMisiPId = isset($misiPMap[$row['refmisi_p_id']]) ? $misiPMap[$row['refmisi_p_id']] : 0;
            $newTujuanPId = isset($tujuanPMap[$row['reftujuan_p_id']]) ? $tujuanPMap[$row['reftujuan_p_id']] : 0;
            $newSasaranPId = isset($sasaranPMap[$row['refsasaran_p_id']]) ? $sasaranPMap[$row['refsasaran_p_id']] : 0;
            $newTujuanRenstraPId = isset($tujuanRenstraPMap[$row['reftujuanrenstra_p_id']]) ? $tujuanRenstraPMap[$row['reftujuanrenstra_p_id']] : 0;

            $key = $row['uraian_sasaranrenstra_p'] . '|' . $row['refskpd_id'] . '|' . $p5Id;
            if (isset($inserted_sasaranrenstra_p[$key])) {
                $sasaranRenstraPMap[$row['refsasaranrenstra_p_id']] = $inserted_sasaranrenstra_p[$key];
            } else {
                $this->db->createCommand()->insert('v2_sakip_sasaranrenstra_p', [
                    'refsasaranrenstra_id' => isset($sasaranRenstraMap[$row['refsasaranrenstra_id']]) ? $sasaranRenstraMap[$row['refsasaranrenstra_id']] : 0,
                    'uraian_sasaranrenstra_p' => $row['uraian_sasaranrenstra_p'],
                    'refskpd_id' => $row['refskpd_id'],
                    'refsasaran_p_id' => $newSasaranPId,
                    'refvisi_p_id' => $newVisiPId,
                    'refmisi_p_id' => $newMisiPId,
                    'reftujuan_p_id' => $newTujuanPId,
                    'refperiode_5tahun_id' => $p5Id,
                    'reftujuanrenstra_p_id' => $newTujuanRenstraPId,
                    'sasaranrenstra_p_isaktif' => $row['sasaranrenstra_p_isaktif'],
                    'alasan_sasaranrenstra_p' => $row['alasan_sasaranrenstra_p'],
                    'formulasi_sasaranrenstra_p' => $row['formulasi_sasaranrenstra_p'],
                    'kriteria_sasaranrenstra_p' => $row['kriteria_sasaranrenstra_p'],
                ])->execute();
                $newId = $this->db->getLastInsertID();
                $inserted_sasaranrenstra_p[$key] = $newId;
                $sasaranRenstraPMap[$row['refsasaranrenstra_p_id']] = $newId;
            }
        }

        // Perbarui refsasaranrenstra_p_id di v2_sakip_tujuanrenstra_p
        foreach ($oldTujuanRenstraPRows as $row) {
            $newTujuanRenstraPId = $tujuanRenstraPMap[$row['reftujuanrenstra_p_id']];
            $newSasaranRenstraPId = isset($sasaranRenstraPMap[$row['refsasaranrenstra_p_id']]) ? $sasaranRenstraPMap[$row['refsasaranrenstra_p_id']] : 0;
            $this->db->createCommand()->update('v2_sakip_tujuanrenstra_p', ['refsasaranrenstra_p_id' => $newSasaranRenstraPId], ['reftujuanrenstra_p_id' => $newTujuanRenstraPId])->execute();
        }

        // --- K. Strategi ---
        $strategiMap = [];
        $inserted_strategi = [];
        $oldStrategiRows = (new \yii\db\Query())->from('sakip_strategi')->all();
        foreach ($oldStrategiRows as $row) {
            $p5Id = isset($periodeMap[$row['refperiode_id']]) ? $periodeMap[$row['refperiode_id']] : 2;
            $newMisiId = isset($misiMap[$row['refmisi_id']]) ? $misiMap[$row['refmisi_id']] : 0;
            $newSasaranRenstraId = isset($sasaranRenstraMap[$row['refsasaranrenstra_id']]) ? $sasaranRenstraMap[$row['refsasaranrenstra_id']] : 0;
            $newSasaranId = isset($sasaranMap[$row['refsasaran_id']]) ? $sasaranMap[$row['refsasaran_id']] : 0;
            $newTujuanId = isset($tujuanMap[$row['reftujuan_id']]) ? $tujuanMap[$row['reftujuan_id']] : 0;

            $key = $row['uraian_strategi'] . '|' . $row['refskpd_id'] . '|' . $p5Id;
            if (isset($inserted_strategi[$key])) {
                $strategiMap[$row['refstrategi_id']] = $inserted_strategi[$key];
            } else {
                $this->db->createCommand()->insert('v2_sakip_strategi', [
                    'uraian_strategi' => $row['uraian_strategi'],
                    'refskpd_id' => $row['refskpd_id'],
                    'refmisi_id' => $newMisiId,
                    'refsasaranrenstra_id' => $newSasaranRenstraId,
                    'refsasaran_id' => $newSasaranId,
                    'reftujuan_id' => $newTujuanId,
                    'refperiode_5tahun_id' => $p5Id,
                    'user_create' => $row['user_create'],
                    'date_create' => $row['date_create'],
                ])->execute();
                $newId = $this->db->getLastInsertID();
                $inserted_strategi[$key] = $newId;
                $strategiMap[$row['refstrategi_id']] = $newId;
            }
        }

        // --- L. Kebijakan ---
        $kebijakanMap = [];
        $inserted_kebijakan = [];
        $oldKebijakanRows = (new \yii\db\Query())->from('sakip_kebijakan')->all();
        foreach ($oldKebijakanRows as $row) {
            $p5Id = isset($periodeMap[$row['refperiode_id']]) ? $periodeMap[$row['refperiode_id']] : 2;
            $newStrategiId = isset($strategiMap[$row['refstrategi_id']]) ? $strategiMap[$row['refstrategi_id']] : 0;
            $newMisiId = isset($misiMap[$row['refmisi_id']]) ? $misiMap[$row['refmisi_id']] : 0;
            $newSasaranRenstraId = isset($sasaranRenstraMap[$row['refsasaranrenstra_id']]) ? $sasaranRenstraMap[$row['refsasaranrenstra_id']] : 0;
            $newSasaranId = isset($sasaranMap[$row['refsasaran_id']]) ? $sasaranMap[$row['refsasaran_id']] : 0;
            $newTujuanId = isset($tujuanMap[$row['reftujuan_id']]) ? $tujuanMap[$row['reftujuan_id']] : 0;

            $key = $row['uraian_kebijakan'] . '|' . $row['refskpd_id'] . '|' . $p5Id;
            if (isset($inserted_kebijakan[$key])) {
                $kebijakanMap[$row['refkebijakan_id']] = $inserted_kebijakan[$key];
            } else {
                $this->db->createCommand()->insert('v2_sakip_kebijakan', [
                    'uraian_kebijakan' => $row['uraian_kebijakan'],
                    'refskpd_id' => $row['refskpd_id'],
                    'refstrategi_id' => $newStrategiId,
                    'refmisi_id' => $newMisiId,
                    'refsasaranrenstra_id' => $newSasaranRenstraId,
                    'refsasaran_id' => $newSasaranId,
                    'reftujuan_id' => $newTujuanId,
                    'refperiode_5tahun_id' => $p5Id,
                    'user_create' => $row['user_create'],
                    'date_create' => $row['date_create'],
                ])->execute();
                $newId = $this->db->getLastInsertID();
                $inserted_kebijakan[$key] = $newId;
                $kebijakanMap[$row['refkebijakan_id']] = $newId;
            }
        }

        // --- M. Indikator Tujuan Renstra (Tahunan) ---
        $oldIndikatorTujuanRenstraRows = (new \yii\db\Query())->from('sakip_indikatortujuanrenstra')->all();
        foreach ($oldIndikatorTujuanRenstraRows as $row) {
            $this->db->createCommand()->insert('v2_sakip_indikatortujuanrenstra', [
                'uraian_indikatortujuanrenstra' => $row['uraian_indikatortujuanrenstra'],
                'reftujuanrenstra_id' => isset($tujuanRenstraMap[$row['reftujuanrenstra_id']]) ? $tujuanRenstraMap[$row['reftujuanrenstra_id']] : null,
                'refsasaranrenstra_id' => isset($sasaranRenstraMap[$row['refsasaranrenstra_id']]) ? $sasaranRenstraMap[$row['refsasaranrenstra_id']] : null,
                'refskpd_id' => $row['refskpd_id'],
                'refperiode_id' => $row['refperiode_id']
            ])->execute();
        }

        // --- N. Indikator Sasaran Renstra (Tahunan) ---
        $indSasaranMap = [];
        $oldIndSasaranRows = (new \yii\db\Query())->from('sakip_indikatorsasaranrenstra')->all();
        foreach ($oldIndSasaranRows as $row) {
            $newSasaranRenstraId = isset($sasaranRenstraMap[$row['refsasaranrenstra_id']]) ? $sasaranRenstraMap[$row['refsasaranrenstra_id']] : 0;
            $this->db->createCommand()->insert('v2_sakip_indikatorsasaranrenstra', [
                'uraian_indikatorsasaranrenstra' => $row['uraian_indikatorsasaranrenstra'],
                'refsasaranrenstra_id' => $newSasaranRenstraId,
                'refskpd_id' => $row['refskpd_id'],
                'refperiode_id' => $row['refperiode_id'],
                'indikatorsasaranrenstra_satuan' => $row['indikatorsasaranrenstra_satuan'],
                'indikatorsasaranrenstra_target' => $row['indikatorsasaranrenstra_target'],
                'target_rkt' => $row['target_rkt'],
                'target_rkt_p' => $row['target_rkt_p'],
                'target_pk' => $row['target_pk'],
                'target_pk_p' => $row['target_pk_p'],
                'realisasi' => $row['realisasi'],
                'capaian' => $row['capaian'],
                'analisis' => $row['analisis'],
                'keterangan' => $row['keterangan'],
                'indikatorsasaranrenstra_isaktif' => $row['indikatorsasaranrenstra_isaktif'],
                'iku_isaktif' => $row['iku_isaktif'],
                'pk_isaktif' => $row['pk_isaktif'],
                'alasan_sasaranrenstra' => $row['alasan_sasaranrenstra'],
                'formulasi_sasaranrenstra' => $row['formulasi_sasaranrenstra'],
                'kriteria_sasaranrenstra' => $row['kriteria_sasaranrenstra'],
                'keterangan_pk' => $row['keterangan_pk'],
                'keterangan_pk_p' => $row['keterangan_pk_p'],
            ])->execute();
            $indSasaranMap[$row['refindikatorsasaranrenstra_id']] = $this->db->getLastInsertID();
        }

        // --- O. Indikator Sasaran Renstra Perubahan (P) ---
        $indSasaranPMap = [];
        $oldIndSasaranPRows = (new \yii\db\Query())->from('sakip_indikatorsasaranrenstra_p')->all();
        foreach ($oldIndSasaranPRows as $row) {
            $newSasaranRenstraPId = isset($sasaranRenstraPMap[$row['refsasaranrenstra_p_id']]) ? $sasaranRenstraPMap[$row['refsasaranrenstra_p_id']] : 0;
            $this->db->createCommand()->insert('v2_sakip_indikatorsasaranrenstra_p', [
                'refsasaranrenstra_p_id' => $newSasaranRenstraPId,
                'refskpd_id' => $row['refskpd_id'],
                'refperiode_id' => $row['refperiode_id'],
                'uraian_indikatorsasaranrenstra_p' => $row['uraian_indikatorsasaranrenstra_p'],
                'indikatorsasaranrenstra_p_satuan' => $row['indikatorsasaranrenstra_p_satuan'],
                'indikatorsasaranrenstra_p_target' => $row['indikatorsasaranrenstra_p_target'],
                'target_rkt_p' => $row['target_rkt_p'],
                'target_pk_p' => $row['target_pk_p'],
                'realisasi' => $row['realisasi'],
                'capaian' => $row['capaian'],
                'analisis' => $row['analisis'],
                'keterangan' => $row['keterangan'],
                'indikatorsasaranrenstra_p_isaktif' => $row['indikatorsasaranrenstra_p_isaktif'],
                'iku_isaktif' => $row['iku_isaktif'],
                'pk_isaktif' => $row['pk_isaktif'],
                'alasan_sasaranrenstra_p' => $row['alasan_sasaranrenstra_p'],
                'formulasi_sasaranrenstra_p' => $row['formulasi_sasaranrenstra_p'],
                'kriteria_sasaranrenstra_p' => $row['kriteria_sasaranrenstra_p'],
                'keterangan_pk_p' => $row['keterangan_pk_p'],
            ])->execute();
            $indSasaranPMap[$row['refindikatorsasaranrenstra_p_id']] = $this->db->getLastInsertID();
        }

        // --- P. Indikator Sasaran Renstra Triwulan ---
        $oldIndSasaranTriwulanRows = (new \yii\db\Query())->from('sakip_indikatorsasaranrenstra_triwulan')->all();
        foreach ($oldIndSasaranTriwulanRows as $row) {
            $this->db->createCommand()->insert('v2_sakip_indikatorsasaranrenstra_triwulan', [
                'refindikatorsasaranrenstra_id' => isset($indSasaranMap[$row['refindikatorsasaranrenstra_id']]) ? $indSasaranMap[$row['refindikatorsasaranrenstra_id']] : null,
                'refsasaranrenstra_id' => isset($sasaranRenstraMap[$row['refsasaranrenstra_id']]) ? $sasaranRenstraMap[$row['refsasaranrenstra_id']] : null,
                'refskpd_id' => $row['refskpd_id'],
                'refperiode_id' => $row['refperiode_id'],
                'reftriwulan_id' => $row['reftriwulan_id'],
                'triwulan_target_rkt' => $row['triwulan_target_rkt'],
                'triwulan_target_rkt_p' => $row['triwulan_target_rkt_p'],
                'triwulan_target_pk' => $row['triwulan_target_pk'],
                'triwulan_target_pk_p' => $row['triwulan_target_pk_p'],
                'triwulan_realisasi' => $row['triwulan_realisasi'],
                'triwulan_capaian' => $row['triwulan_capaian'],
                'triwulan_keterangan' => $row['triwulan_keterangan'],
                'triwulan_analisis' => $row['triwulan_analisis'],
                'triwulan_keterangan_pk_p' => $row['triwulan_keterangan_pk_p'],
            ])->execute();
        }

        // --- Q. Indikator Sasaran Renstra Perubahan Triwulan (P) ---
        $oldIndSasaranPTriwulanRows = (new \yii\db\Query())->from('sakip_indikatorsasaranrenstra_p_triwulan')->all();
        foreach ($oldIndSasaranPTriwulanRows as $row) {
            $this->db->createCommand()->insert('v2_sakip_indikatorsasaranrenstra_p_triwulan', [
                'refindikatorsasaranrenstra_p_id' => isset($indSasaranPMap[$row['refindikatorsasaranrenstra_p_id']]) ? $indSasaranPMap[$row['refindikatorsasaranrenstra_p_id']] : null,
                'refsasaranrenstra_p_id' => isset($sasaranRenstraPMap[$row['refsasaranrenstra_p_id']]) ? $sasaranRenstraPMap[$row['refsasaranrenstra_p_id']] : null,
                'refskpd_id' => $row['refskpd_id'],
                'refperiode_id' => $row['refperiode_id'],
                'reftriwulan_id' => $row['reftriwulan_id'],
                'triwulan_target_rkt_p' => $row['triwulan_target_rkt_p'],
                'triwulan_target_pk_p' => $row['triwulan_target_pk_p'],
                'triwulan_realisasi' => $row['triwulan_realisasi'],
                'triwulan_capaian' => $row['triwulan_capaian'],
                'triwulan_keterangan' => $row['triwulan_keterangan'],
                'triwulan_analisis' => $row['triwulan_analisis'],
                'triwulan_keterangan_pk_p' => $row['triwulan_keterangan_pk_p'],
            ])->execute();
        }

        // --- R. Cascading Program (Tahunan) ---
        $cascadingProgramMap = [];
        $oldCascadingProgRows = (new \yii\db\Query())->from('sakip_cascadingprogram')->all();
        foreach ($oldCascadingProgRows as $row) {
            $this->db->createCommand()->insert('v2_sakip_cascadingprogram', [
                'refcascadingprogram_id' => $row['refcascadingprogram_id'],
                'refsasaran_id' => isset($sasaranMap[$row['refsasaran_id']]) ? $sasaranMap[$row['refsasaran_id']] : 0,
                'refskpd_id' => $row['refskpd_id'],
                'reftujuan_id' => isset($tujuanMap[$row['reftujuan_id']]) ? $tujuanMap[$row['reftujuan_id']] : 0,
                'refmisi_id' => isset($misiMap[$row['refmisi_id']]) ? $misiMap[$row['refmisi_id']] : 0,
                'refsasaranrenstra_id' => isset($sasaranRenstraMap[$row['refsasaranrenstra_id']]) ? $sasaranRenstraMap[$row['refsasaranrenstra_id']] : 0,
                'refindikatorsasaranrenstra_id' => isset($indSasaranMap[$row['refindikatorsasaranrenstra_id']]) ? $indSasaranMap[$row['refindikatorsasaranrenstra_id']] : 0,
                'refbidang_id' => $row['refbidang_id'],
                'refprogram_id' => $row['refprogram_id'],
                'uraian_sasaranprogram' => $row['uraian_sasaranprogram'],
                'uraian_indikatorprogram' => $row['uraian_indikatorprogram'],
                'refperiode_id' => $row['refperiode_id'],
                'program_target' => $row['program_target'],
                'program_satuan' => $row['program_satuan'],
            ])->execute();
            $cascadingProgramMap[$row['refcascadingprogram_id']] = $this->db->getLastInsertID();
        }

        // --- S. Indikator Cascading Program (Tahunan) ---
        $indProgramMap = [];
        $oldIndProgRows = (new \yii\db\Query())->from('sakip_indikatorcascadingprogram')->all();
        foreach ($oldIndProgRows as $row) {
            $this->db->createCommand()->insert('v2_sakip_indikatorcascadingprogram', [
                'refcascadingprogram_id' => isset($cascadingProgramMap[$row['refcascadingprogram_id']]) ? $cascadingProgramMap[$row['refcascadingprogram_id']] : null,
                'refsasaranrenstra_id' => isset($sasaranRenstraMap[$row['refsasaranrenstra_id']]) ? $sasaranRenstraMap[$row['refsasaranrenstra_id']] : null,
                'refindikatorsasaranrenstra_id' => isset($indSasaranMap[$row['refindikatorsasaranrenstra_id']]) ? $indSasaranMap[$row['refindikatorsasaranrenstra_id']] : null,
                'refskpd_id' => $row['refskpd_id'],
                'refperiode_id' => $row['refperiode_id'],
                'refbidang_id' => $row['refbidang_id'],
                'refprogram_id' => $row['refprogram_id'],
                'target_rkt' => $row['target_rkt'],
                'target_rkt_p' => $row['target_rkt_p'],
                'target_pk' => $row['target_pk'],
                'target_pk_p' => $row['target_pk_p'],
                'realisasi' => $row['realisasi'],
                'capaian' => $row['capaian'],
                'keterangan' => $row['keterangan'],
                'analisis' => $row['analisis'],
                'keterangan_pk' => $row['keterangan_pk'],
                'keterangan_pk_p' => $row['keterangan_pk_p'],
            ])->execute();
            $indProgramMap[$row['refindikatorprogram_id']] = $this->db->getLastInsertID();
        }

        // --- T. Indikator Cascading Program Triwulan ---
        $oldIndProgTriwulanRows = (new \yii\db\Query())->from('sakip_indikatorcascadingprogram_triwulan')->all();
        foreach ($oldIndProgTriwulanRows as $row) {
            $this->db->createCommand()->insert('v2_sakip_indikatorcascadingprogram_triwulan', [
                'refindikatorprogram_id' => isset($indProgramMap[$row['refindikatorprogram_id']]) ? $indProgramMap[$row['refindikatorprogram_id']] : null,
                'refcascadingprogram_id' => isset($cascadingProgramMap[$row['refcascadingprogram_id']]) ? $cascadingProgramMap[$row['refcascadingprogram_id']] : null,
                'refsasaranrenstra_id' => isset($sasaranRenstraMap[$row['refsasaranrenstra_id']]) ? $sasaranRenstraMap[$row['refsasaranrenstra_id']] : 0,
                'refindikatorsasaranrenstra_id' => isset($indSasaranMap[$row['refindikatorsasaranrenstra_id']]) ? $indSasaranMap[$row['refindikatorsasaranrenstra_id']] : 0,
                'refskpd_id' => $row['refskpd_id'],
                'refperiode_id' => $row['refperiode_id'],
                'reftriwulan_id' => $row['reftriwulan_id'],
                'refbidang_id' => $row['refbidang_id'],
                'refprogram_id' => $row['refprogram_id'],
                'triwulan_target_rkt' => $row['triwulan_target_rkt'],
                'triwulan_target_rkt_p' => $row['triwulan_target_rkt_p'],
                'triwulan_target_pk' => $row['triwulan_target_pk'],
                'triwulan_target_pk_p' => $row['triwulan_target_pk_p'],
                'triwulan_realisasi' => $row['triwulan_realisasi'],
                'triwulan_capaian' => $row['triwulan_capaian'],
                'triwulan_keterangan' => $row['triwulan_keterangan'],
                'triwulan_analisis' => $row['triwulan_analisis'],
                'triwulan_keterangan_pk_p' => $row['triwulan_keterangan_pk_p'],
            ])->execute();
        }

        // --- U. Cascading Kegiatan (Tahunan) ---
        $cascadingKegiatanMap = [];
        $oldCascadingKegRows = (new \yii\db\Query())->from('sakip_cascadingkegiatan')->all();
        foreach ($oldCascadingKegRows as $row) {
            $this->db->createCommand()->insert('v2_sakip_cascadingkegiatan', [
                'refcascadingkegiatan_id' => $row['refcascadingkegiatan_id'],
                'refcascadingprogram_id' => isset($cascadingProgramMap[$row['refcascadingprogram_id']]) ? $cascadingProgramMap[$row['refcascadingprogram_id']] : 0,
                'refsasaranrenstra_id' => isset($sasaranRenstraMap[$row['refsasaranrenstra_id']]) ? $sasaranRenstraMap[$row['refsasaranrenstra_id']] : 0,
                'refindikatorsasaranrenstra_id' => isset($indSasaranMap[$row['refindikatorsasaranrenstra_id']]) ? $indSasaranMap[$row['refindikatorsasaranrenstra_id']] : 0,
                'refprogram_id' => $row['refprogram_id'],
                'refkegiatan_id' => $row['refkegiatan_id'],
                'uraian_sasarankegiatan' => $row['uraian_sasarankegiatan'],
                'uraian_indikatorkegiatan' => $row['uraian_indikatorkegiatan'],
                'refperiode_id' => $row['refperiode_id'],
                'refskpd_id' => $row['refskpd_id'],
                'kegiatan_target' => $row['kegiatan_target'],
                'kegiatan_satuan' => $row['kegiatan_satuan'],
            ])->execute();
            $cascadingKegiatanMap[$row['refcascadingkegiatan_id']] = $this->db->getLastInsertID();
        }

        // --- V. Indikator Cascading Kegiatan (Tahunan) ---
        $indKegiatanMap = [];
        $oldIndKegRows = (new \yii\db\Query())->from('sakip_indikatorcascadingkegiatan')->all();
        foreach ($oldIndKegRows as $row) {
            $this->db->createCommand()->insert('v2_sakip_indikatorcascadingkegiatan', [
                'refcascadingkegiatan_id' => isset($cascadingKegiatanMap[$row['refcascadingkegiatan_id']]) ? $cascadingKegiatanMap[$row['refcascadingkegiatan_id']] : null,
                'refcascadingprogram_id' => isset($cascadingProgramMap[$row['refcascadingprogram_id']]) ? $cascadingProgramMap[$row['refcascadingprogram_id']] : null,
                'refsasaranrenstra_id' => isset($sasaranRenstraMap[$row['refsasaranrenstra_id']]) ? $sasaranRenstraMap[$row['refsasaranrenstra_id']] : null,
                'refindikatorsasaranrenstra_id' => isset($indSasaranMap[$row['refindikatorsasaranrenstra_id']]) ? $indSasaranMap[$row['refindikatorsasaranrenstra_id']] : null,
                'refskpd_id' => $row['refskpd_id'],
                'refperiode_id' => $row['refperiode_id'],
                'refprogram_id' => $row['refprogram_id'],
                'refkegiatan_id' => $row['refkegiatan_id'],
                'target_rkt' => $row['target_rkt'],
                'target_rkt_p' => $row['target_rkt_p'],
                'target_pk' => $row['target_pk'],
                'target_pk_p' => $row['target_pk_p'],
                'realisasi' => $row['realisasi'],
                'capaian' => $row['capaian'],
                'keterangan' => $row['keterangan'],
                'analisis' => $row['analisis'],
                'keterangan_pk' => $row['keterangan_pk'],
                'keterangan_pk_p' => $row['keterangan_pk_p'],
            ])->execute();
            $indKegiatanMap[$row['refindikatorkegiatan_id']] = $this->db->getLastInsertID();
        }

        // --- W. Indikator Cascading Kegiatan Triwulan ---
        $oldIndKegTriwulanRows = (new \yii\db\Query())->from('sakip_indikatorcascadingkegiatan_triwulan')->all();
        foreach ($oldIndKegTriwulanRows as $row) {
            $this->db->createCommand()->insert('v2_sakip_indikatorcascadingkegiatan_triwulan', [
                'refindikatorkegiatan_id' => isset($indKegiatanMap[$row['refindikatorkegiatan_id']]) ? $indKegiatanMap[$row['refindikatorkegiatan_id']] : null,
                'refcascadingkegiatan_id' => isset($cascadingKegiatanMap[$row['refcascadingkegiatan_id']]) ? $cascadingKegiatanMap[$row['refcascadingkegiatan_id']] : null,
                'refcascadingprogram_id' => isset($cascadingProgramMap[$row['refcascadingprogram_id']]) ? $cascadingProgramMap[$row['refcascadingprogram_id']] : 0,
                'refsasaranrenstra_id' => isset($sasaranRenstraMap[$row['refsasaranrenstra_id']]) ? $sasaranRenstraMap[$row['refsasaranrenstra_id']] : 0,
                'refindikatorsasaranrenstra_id' => isset($indSasaranMap[$row['refindikatorsasaranrenstra_id']]) ? $indSasaranMap[$row['refindikatorsasaranrenstra_id']] : 0,
                'refskpd_id' => $row['refskpd_id'],
                'refperiode_id' => $row['refperiode_id'],
                'reftriwulan_id' => $row['reftriwulan_id'],
                'refprogram_id' => $row['refprogram_id'],
                'refkegiatan_id' => $row['refkegiatan_id'],
                'triwulan_target_rkt' => $row['triwulan_target_rkt'],
                'triwulan_target_rkt_p' => $row['triwulan_target_rkt_p'],
                'triwulan_target_pk' => $row['triwulan_target_pk'],
                'triwulan_target_pk_p' => $row['triwulan_target_pk_p'],
                'triwulan_realisasi' => $row['triwulan_realisasi'],
                'triwulan_capaian' => $row['triwulan_capaian'],
                'triwulan_keterangan' => $row['triwulan_keterangan'],
                'triwulan_analisis' => $row['triwulan_analisis'],
                'triwulan_keterangan_pk_p' => $row['triwulan_keterangan_pk_p'],
            ])->execute();
        }

        // --- X. Cascading Subkegiatan (Tahunan) ---
        $cascadingSubkegiatanMap = [];
        $oldCascadingSubRows = (new \yii\db\Query())->from('sakip_cascadingsubkegiatan')->all();
        foreach ($oldCascadingSubRows as $row) {
            $this->db->createCommand()->insert('v2_sakip_cascadingsubkegiatan', [
                'refcascadingsubkegiatan_id' => $row['refcascadingsubkegiatan_id'],
                'refcascadingkegiatan_id' => isset($cascadingKegiatanMap[$row['refcascadingkegiatan_id']]) ? $cascadingKegiatanMap[$row['refcascadingkegiatan_id']] : 0,
                'refcascadingprogram_id' => isset($cascadingProgramMap[$row['refcascadingprogram_id']]) ? $cascadingProgramMap[$row['refcascadingprogram_id']] : 0,
                'refsasaranrenstra_id' => isset($sasaranRenstraMap[$row['refsasaranrenstra_id']]) ? $sasaranRenstraMap[$row['refsasaranrenstra_id']] : 0,
                'refindikatorsasaranrenstra_id' => isset($indSasaranMap[$row['refindikatorsasaranrenstra_id']]) ? $indSasaranMap[$row['refindikatorsasaranrenstra_id']] : 0,
                'refprogram_id' => $row['refprogram_id'],
                'refkegiatan_id' => $row['refkegiatan_id'],
                'refsubkegiatan_id' => $row['refsubkegiatan_id'],
                'uraian_sasaransubkegiatan' => $row['uraian_sasaransubkegiatan'],
                'uraian_indikatorsubkegiatan' => $row['uraian_indikatorsubkegiatan'],
                'refperiode_id' => $row['refperiode_id'],
                'refskpd_id' => $row['refskpd_id'],
                'subkegiatan_target' => $row['subkegiatan_target'],
                'subkegiatan_satuan' => $row['subkegiatan_satuan'],
                'subkegiatan_anggaran' => $row['subkegiatan_anggaran'],
            ])->execute();
            $cascadingSubkegiatanMap[$row['refcascadingsubkegiatan_id']] = $this->db->getLastInsertID();
        }

        // --- Y. Indikator Cascading Subkegiatan (Tahunan) ---
        $indSubkegiatanMap = [];
        $oldIndSubRows = (new \yii\db\Query())->from('sakip_indikatorcascadingsubkegiatan')->all();
        foreach ($oldIndSubRows as $row) {
            $this->db->createCommand()->insert('v2_sakip_indikatorcascadingsubkegiatan', [
                'refcascadingsubkegiatan_id' => isset($cascadingSubkegiatanMap[$row['refcascadingsubkegiatan_id']]) ? $cascadingSubkegiatanMap[$row['refcascadingsubkegiatan_id']] : null,
                'refcascadingkegiatan_id' => isset($cascadingKegiatanMap[$row['refcascadingkegiatan_id']]) ? $cascadingKegiatanMap[$row['refcascadingkegiatan_id']] : null,
                'refcascadingprogram_id' => isset($cascadingProgramMap[$row['refcascadingprogram_id']]) ? $cascadingProgramMap[$row['refcascadingprogram_id']] : null,
                'refsasaranrenstra_id' => isset($sasaranRenstraMap[$row['refsasaranrenstra_id']]) ? $sasaranRenstraMap[$row['refsasaranrenstra_id']] : null,
                'refindikatorsasaranrenstra_id' => isset($indSasaranMap[$row['refindikatorsasaranrenstra_id']]) ? $indSasaranMap[$row['refindikatorsasaranrenstra_id']] : null,
                'refskpd_id' => $row['refskpd_id'],
                'refperiode_id' => $row['refperiode_id'],
                'refprogram_id' => $row['refprogram_id'],
                'refkegiatan_id' => $row['refkegiatan_id'],
                'refsubkegiatan_id' => $row['refsubkegiatan_id'],
                'target_rkt' => $row['target_rkt'],
                'target_rkt_p' => $row['target_rkt_p'],
                'target_pk' => $row['target_pk'],
                'target_pk_p' => $row['target_pk_p'],
                'realisasi' => $row['realisasi'],
                'capaian' => $row['capaian'],
                'keterangan' => $row['keterangan'],
                'analisis' => $row['analisis'],
                'keterangan_pk' => $row['keterangan_pk'],
                'keterangan_pk_p' => $row['keterangan_pk_p'],
            ])->execute();
            $indSubkegiatanMap[$row['refindikatorsubkegiatan_id']] = $this->db->getLastInsertID();
        }

        // --- Z. Indikator Cascading Subkegiatan Triwulan ---
        $oldIndSubTriwulanRows = (new \yii\db\Query())->from('sakip_indikatorcascadingsubkegiatan_triwulan')->all();
        foreach ($oldIndSubTriwulanRows as $row) {
            $this->db->createCommand()->insert('v2_sakip_indikatorcascadingsubkegiatan_triwulan', [
                'refindikatorsubkegiatan_id' => isset($indSubkegiatanMap[$row['refindikatorsubkegiatan_id']]) ? $indSubkegiatanMap[$row['refindikatorsubkegiatan_id']] : null,
                'refcascadingsubkegiatan_id' => isset($cascadingSubkegiatanMap[$row['refcascadingsubkegiatan_id']]) ? $cascadingSubkegiatanMap[$row['refcascadingsubkegiatan_id']] : null,
                'refcascadingprogram_id' => isset($cascadingProgramMap[$row['refcascadingprogram_id']]) ? $cascadingProgramMap[$row['refcascadingprogram_id']] : 0,
                'refcascadingkegiatan_id' => isset($cascadingKegiatanMap[$row['refcascadingkegiatan_id']]) ? $cascadingKegiatanMap[$row['refcascadingkegiatan_id']] : 0,
                'refsasaranrenstra_id' => isset($sasaranRenstraMap[$row['refsasaranrenstra_id']]) ? $sasaranRenstraMap[$row['refsasaranrenstra_id']] : 0,
                'refindikatorsasaranrenstra_id' => isset($indSasaranMap[$row['refindikatorsasaranrenstra_id']]) ? $indSasaranMap[$row['refindikatorsasaranrenstra_id']] : 0,
                'refskpd_id' => $row['refskpd_id'],
                'refperiode_id' => $row['refperiode_id'],
                'reftriwulan_id' => $row['reftriwulan_id'],
                'refprogram_id' => $row['refprogram_id'],
                'refkegiatan_id' => $row['refkegiatan_id'],
                'refsubkegiatan_id' => $row['refsubkegiatan_id'],
                'triwulan_target_rkt' => $row['triwulan_target_rkt'],
                'triwulan_target_rkt_p' => $row['triwulan_target_rkt_p'],
                'triwulan_target_pk' => $row['triwulan_target_pk'],
                'triwulan_target_pk_p' => $row['triwulan_target_pk_p'],
                'triwulan_realisasi' => $row['triwulan_realisasi'],
                'triwulan_capaian' => $row['triwulan_capaian'],
                'triwulan_keterangan' => $row['triwulan_keterangan'],
                'triwulan_analisis' => $row['triwulan_analisis'],
                'triwulan_penyerapan_anggaran' => $row['triwulan_penyerapan_anggaran'],
                'triwulan_keterangan_pk_p' => $row['triwulan_keterangan_pk_p'],
            ])->execute();
        }

        // --- AA. Penjabat SKPD Cascading Tables ---
        $oldPenjabatProgRows = (new \yii\db\Query())->from('sakip_penjabatskpd_cascadingprogram')->all();
        foreach ($oldPenjabatProgRows as $row) {
            $this->db->createCommand()->insert('v2_sakip_penjabatskpd_cascadingprogram', [
                'refpenjabatskpd_id' => $row['refpenjabatskpd_id'],
                'refeselon_id' => $row['refeselon_id'],
                'refcascadingprogram_id' => isset($cascadingProgramMap[$row['refcascadingprogram_id']]) ? $cascadingProgramMap[$row['refcascadingprogram_id']] : null,
                'refindikatorprogram_id' => isset($indProgramMap[$row['refindikatorprogram_id']]) ? $indProgramMap[$row['refindikatorprogram_id']] : null,
                'refskpd_id' => $row['refskpd_id'],
                'refperiode_id' => $row['refperiode_id'],
                'refsasaranrenstra_id' => isset($sasaranRenstraMap[$row['refsasaranrenstra_id']]) ? $sasaranRenstraMap[$row['refsasaranrenstra_id']] : null,
                'refindikatorsasaranrenstra_id' => isset($indSasaranMap[$row['refindikatorsasaranrenstra_id']]) ? $indSasaranMap[$row['refindikatorsasaranrenstra_id']] : null,
                'refbidang_id' => $row['refbidang_id'],
                'refprogram_id' => $row['refprogram_id'],
                'uraian_sasaranprogram' => $row['uraian_sasaranprogram'],
                'uraian_indikatorprogram' => $row['uraian_indikatorprogram'],
                'program_target' => $row['program_target'],
                'program_satuan' => $row['program_satuan'],
                'target_rkt' => $row['target_rkt'],
                'target_rkt_p' => $row['target_rkt_p'],
                'target_pk' => $row['target_pk'],
                'target_pk_p' => $row['target_pk_p'],
                'realisasi' => $row['realisasi'],
                'capaian' => $row['capaian'],
                'keterangan' => $row['keterangan'],
                'analisis' => $row['analisis'],
            ])->execute();
        }

        $oldPenjabatKegRows = (new \yii\db\Query())->from('sakip_penjabatskpd_cascadingkegiatan')->all();
        foreach ($oldPenjabatKegRows as $row) {
            $this->db->createCommand()->insert('v2_sakip_penjabatskpd_cascadingkegiatan', [
                'refpenjabatskpd_id' => $row['refpenjabatskpd_id'],
                'refeselon_id' => $row['refeselon_id'],
                'refcascadingkegiatan_id' => isset($cascadingKegiatanMap[$row['refcascadingkegiatan_id']]) ? $cascadingKegiatanMap[$row['refcascadingkegiatan_id']] : null,
                'refindikatorkegiatan_id' => isset($indKegiatanMap[$row['refindikatorkegiatan_id']]) ? $indKegiatanMap[$row['refindikatorkegiatan_id']] : null,
                'refskpd_id' => $row['refskpd_id'],
                'refperiode_id' => $row['refperiode_id'],
                'refsasaranrenstra_id' => isset($sasaranRenstraMap[$row['refsasaranrenstra_id']]) ? $sasaranRenstraMap[$row['refsasaranrenstra_id']] : null,
                'refindikatorsasaranrenstra_id' => isset($indSasaranMap[$row['refindikatorsasaranrenstra_id']]) ? $indSasaranMap[$row['refindikatorsasaranrenstra_id']] : null,
                'refcascadingprogram_id' => isset($cascadingProgramMap[$row['refcascadingprogram_id']]) ? $cascadingProgramMap[$row['refcascadingprogram_id']] : null,
                'refindikatorprogram_id' => isset($indProgramMap[$row['refindikatorprogram_id']]) ? $indProgramMap[$row['refindikatorprogram_id']] : null,
                'refbidang_id' => $row['refbidang_id'],
                'refprogram_id' => $row['refprogram_id'],
                'refkegiatan_id' => $row['refkegiatan_id'],
                'uraian_sasarankegiatan' => $row['uraian_sasarankegiatan'],
                'uraian_indikatorkegiatan' => $row['uraian_indikatorkegiatan'],
                'kegiatan_target' => $row['kegiatan_target'],
                'kegiatan_satuan' => $row['kegiatan_satuan'],
                'target_rkt' => $row['target_rkt'],
                'target_rkt_p' => $row['target_rkt_p'],
                'target_pk' => $row['target_pk'],
                'target_pk_p' => $row['target_pk_p'],
                'realisasi' => $row['realisasi'],
                'capaian' => $row['capaian'],
                'keterangan' => $row['keterangan'],
                'analisis' => $row['analisis'],
            ])->execute();
        }

        $oldPenjabatSubRows = (new \yii\db\Query())->from('sakip_penjabatskpd_cascadingsubkegiatan')->all();
        foreach ($oldPenjabatSubRows as $row) {
            $this->db->createCommand()->insert('v2_sakip_penjabatskpd_cascadingsubkegiatan', [
                'refpenjabatskpd_id' => $row['refpenjabatskpd_id'],
                'refeselon_id' => $row['refeselon_id'],
                'refcascadingsubkegiatan_id' => isset($cascadingSubkegiatanMap[$row['refcascadingsubkegiatan_id']]) ? $cascadingSubkegiatanMap[$row['refcascadingsubkegiatan_id']] : null,
                'refindikatorsubkegiatan_id' => isset($indSubkegiatanMap[$row['refindikatorsubkegiatan_id']]) ? $indSubkegiatanMap[$row['refindikatorsubkegiatan_id']] : null,
                'refskpd_id' => $row['refskpd_id'],
                'refperiode_id' => $row['refperiode_id'],
                'refsasaranrenstra_id' => isset($sasaranRenstraMap[$row['refsasaranrenstra_id']]) ? $sasaranRenstraMap[$row['refsasaranrenstra_id']] : null,
                'refindikatorsasaranrenstra_id' => isset($indSasaranMap[$row['refindikatorsasaranrenstra_id']]) ? $indSasaranMap[$row['refindikatorsasaranrenstra_id']] : null,
                'refcascadingprogram_id' => isset($cascadingProgramMap[$row['refcascadingprogram_id']]) ? $cascadingProgramMap[$row['refcascadingprogram_id']] : null,
                'refindikatorprogram_id' => isset($indProgramMap[$row['refindikatorprogram_id']]) ? $indProgramMap[$row['refindikatorprogram_id']] : null,
                'refcascadingkegiatan_id' => isset($cascadingKegiatanMap[$row['refcascadingkegiatan_id']]) ? $cascadingKegiatanMap[$row['refcascadingkegiatan_id']] : null,
                'refindikatorkegiatan_id' => isset($indKegiatanMap[$row['refindikatorkegiatan_id']]) ? $indKegiatanMap[$row['refindikatorkegiatan_id']] : null,
                'refbidang_id' => $row['refbidang_id'],
                'refprogram_id' => $row['refprogram_id'],
                'refkegiatan_id' => $row['refkegiatan_id'],
                'refsubkegiatan_id' => $row['refsubkegiatan_id'],
                'uraian_sasaransubkegiatan' => $row['uraian_sasaransubkegiatan'],
                'uraian_indikatorsubkegiatan' => $row['uraian_indikatorsubkegiatan'],
                'subkegiatan_target' => $row['subkegiatan_target'],
                'subkegiatan_satuan' => $row['subkegiatan_satuan'],
                'target_rkt' => $row['target_rkt'],
                'target_rkt_p' => $row['target_rkt_p'],
                'target_pk' => $row['target_pk'],
                'target_pk_p' => $row['target_pk_p'],
                'realisasi' => $row['realisasi'],
                'capaian' => $row['capaian'],
                'keterangan' => $row['keterangan'],
                'analisis' => $row['analisis'],
            ])->execute();
        }
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    public function safeDown()
    {
        $tablesToDrop = [
            'sakip_periode_5tahun',
            'v2_sakip_periode',
            'v2_sakip_visi', 'v2_sakip_visi_p',
            'v2_sakip_misi', 'v2_sakip_misi_p',
            'v2_sakip_tujuan', 'v2_sakip_tujuan_p',
            'v2_sakip_sasaran', 'v2_sakip_sasaran_p',
            'v2_sakip_tujuanrenstra', 'v2_sakip_tujuanrenstra_p',
            'v2_sakip_sasaranrenstra', 'v2_sakip_sasaranrenstra_p',
            'v2_sakip_indikatortujuanrenstra',
            'v2_sakip_indikatorsasaranrenstra', 'v2_sakip_indikatorsasaranrenstra_p',
            'v2_sakip_indikatorsasaranrenstra_triwulan', 'v2_sakip_indikatorsasaranrenstra_p_triwulan',
            'v2_sakip_strategi', 'v2_sakip_kebijakan',
            'v2_sakip_cascadingprogram', 'v2_sakip_cascadingkegiatan', 'v2_sakip_cascadingsubkegiatan',
            'v2_sakip_indikatorcascadingprogram', 'v2_sakip_indikatorcascadingkegiatan', 'v2_sakip_indikatorcascadingsubkegiatan',
            'v2_sakip_indikatorcascadingprogram_triwulan', 'v2_sakip_indikatorcascadingkegiatan_triwulan', 'v2_sakip_indikatorcascadingsubkegiatan_triwulan',
            'v2_sakip_penjabatskpd_cascadingprogram', 'v2_sakip_penjabatskpd_cascadingkegiatan', 'v2_sakip_penjabatskpd_cascadingsubkegiatan'
        ];

        foreach ($tablesToDrop as $table) {
            $this->execute("DROP TABLE IF EXISTS `{$table}`");
        }
    }
}
