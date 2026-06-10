<?php
$file = __DIR__ . '/../console/migrations/m260603_120000_reconstruct_sakip_to_5yearly.php';
if (!file_exists($file)) {
    die("Migration file not found\n");
}

$content = file_get_contents($file);

// 1. --- I. Tujuan Renstra & Sasaran Renstra (Interlinked) ---
$targetI = <<<'EOD'
        // --- I. Tujuan Renstra & Sasaran Renstra (Interlinked) ---
        // Karena saling merujuk, kita migrasikan Tujuan Renstra dulu tanpa menyetel refsasaranrenstra_id, 
        // lalu migrasikan Sasaran Renstra, dan terakhir perbarui Tujuan Renstra.
        $tujuanRenstraMap = [];
        $oldTujuanRenstraRows = (new \yii\db\Query())->from('sakip_tujuanrenstra')->all();
        foreach ($oldTujuanRenstraRows as $row) {
            $p5Id = isset($periodeMap[$row['refperiode_id']]) ? $periodeMap[$row['refperiode_id']] : 2;
            $newMisiId = isset($misiMap[$row['refmisi_id']]) ? $misiMap[$row['refmisi_id']] : 0;
            $newTujuanId = isset($tujuanMap[$row['reftujuan_id']]) ? $tujuanMap[$row['reftujuan_id']] : 0;
            $newSasaranId = isset($sasaranMap[$row['refsasaran_id']]) ? $sasaranMap[$row['refsasaran_id']] : 0;
            
            $existing = (new \yii\db\Query())
                ->from('v2_sakip_tujuanrenstra')
                ->where([
                    'uraian_tujuanrenstra' => $row['uraian_tujuanrenstra'],
                    'refskpd_id' => $row['refskpd_id'],
                    'refperiode_5tahun_id' => $p5Id,
                ])->one();
                
            if ($existing) {
                $tujuanRenstraMap[$row['reftujuanrenstra_id']] = $existing['reftujuanrenstra_id'];
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
                $tujuanRenstraMap[$row['reftujuanrenstra_id']] = $this->db->getLastInsertID();
            }
        }

        $sasaranRenstraMap = [];
        $oldSasaranRenstraRows = (new \yii\db\Query())->from('sakip_sasaranrenstra')->all();
        foreach ($oldSasaranRenstraRows as $row) {
            $p5Id = isset($periodeMap[$row['refperiode_id']]) ? $periodeMap[$row['refperiode_id']] : 2;
            $newVisiId = isset($visiMap[$row['refvisi_id']]) ? $visiMap[$row['refvisi_id']] : 0;
            $newMisiId = isset($misiMap[$row['refmisi_id']]) ? $misiMap[$row['refmisi_id']] : 0;
            $newTujuanId = isset($tujuanMap[$row['reftujuan_id']]) ? $tujuanMap[$row['reftujuan_id']] : 0;
            $newSasaranId = isset($sasaranMap[$row['refsasaran_id']]) ? $sasaranMap[$row['refsasaran_id']] : 0;
            $newTujuanRenstraId = isset($tujuanRenstraMap[$row['reftujuanrenstra_id']]) ? $tujuanRenstraMap[$row['reftujuanrenstra_id']] : 0;

            $existing = (new \yii\db\Query())
                ->from('v2_sakip_sasaranrenstra')
                ->where([
                    'uraian_sasaranrenstra' => $row['uraian_sasaranrenstra'],
                    'refskpd_id' => $row['refskpd_id'],
                    'refperiode_5tahun_id' => $p5Id,
                ])->one();

            if ($existing) {
                $sasaranRenstraMap[$row['refsasaranrenstra_id']] = $existing['refsasaranrenstra_id'];
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
                $sasaranRenstraMap[$row['refsasaranrenstra_id']] = $this->db->getLastInsertID();
            }
        }
EOD;

$replaceI = <<<'EOD'
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
EOD;

// 2. --- J. Tujuan & Sasaran Renstra Perubahan (P) ---
$targetJ = <<<'EOD'
        // --- J. Tujuan & Sasaran Renstra Perubahan (P) ---
        $tujuanRenstraPMap = [];
        $oldTujuanRenstraPRows = (new \yii\db\Query())->from('sakip_tujuanrenstra_p')->all();
        foreach ($oldTujuanRenstraPRows as $row) {
            $p5Id = isset($periodeMap[$row['refperiode_id']]) ? $periodeMap[$row['refperiode_id']] : 2;
            $newMisiPId = isset($misiPMap[$row['refmisi_p_id']]) ? $misiPMap[$row['refmisi_p_id']] : 0;
            $newTujuanPId = isset($tujuanPMap[$row['reftujuan_p_id']]) ? $tujuanPMap[$row['reftujuan_p_id']] : 0;
            $newSasaranPId = isset($sasaranPMap[$row['refsasaran_p_id']]) ? $sasaranPMap[$row['refsasaran_p_id']] : 0;
            
            $existing = (new \yii\db\Query())
                ->from('v2_sakip_tujuanrenstra_p')
                ->where([
                    'uraian_tujuanrenstra_p' => $row['uraian_tujuanrenstra_p'],
                    'refskpd_id' => $row['refskpd_id'],
                    'refperiode_5tahun_id' => $p5Id,
                ])->one();
                
            if ($existing) {
                $tujuanRenstraPMap[$row['reftujuanrenstra_p_id']] = $existing['reftujuanrenstra_p_id'];
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
                $tujuanRenstraPMap[$row['reftujuanrenstra_p_id']] = $this->db->getLastInsertID();
            }
        }

        $sasaranRenstraPMap = [];
        $oldSasaranRenstraPRows = (new \yii\db\Query())->from('sakip_sasaranrenstra_p')->all();
        foreach ($oldSasaranRenstraPRows as $row) {
            $p5Id = isset($periodeMap[$row['refperiode_id']]) ? $periodeMap[$row['refperiode_id']] : 2;
            $newVisiPId = isset($visiPMap[$row['refvisi_p_id']]) ? $visiPMap[$row['refvisi_p_id']] : 0;
            $newMisiPId = isset($misiPMap[$row['refmisi_p_id']]) ? $misiPMap[$row['refmisi_p_id']] : 0;
            $newTujuanPId = isset($tujuanPMap[$row['reftujuan_p_id']]) ? $tujuanPMap[$row['reftujuan_p_id']] : 0;
            $newSasaranPId = isset($sasaranPMap[$row['refsasaran_p_id']]) ? $sasaranPMap[$row['refsasaran_p_id']] : 0;
            $newTujuanRenstraPId = isset($tujuanRenstraPMap[$row['reftujuanrenstra_p_id']]) ? $tujuanRenstraPMap[$row['reftujuanrenstra_p_id']] : 0;

            $existing = (new \yii\db\Query())
                ->from('v2_sakip_sasaranrenstra_p')
                ->where([
                    'uraian_sasaranrenstra_p' => $row['uraian_sasaranrenstra_p'],
                    'refskpd_id' => $row['refskpd_id'],
                    'refperiode_5tahun_id' => $p5Id,
                ])->one();

            if ($existing) {
                $sasaranRenstraPMap[$row['refsasaranrenstra_p_id']] = $existing['refsasaranrenstra_p_id'];
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
                $sasaranRenstraPMap[$row['refsasaranrenstra_p_id']] = $this->db->getLastInsertID();
            }
        }
EOD;

$replaceJ = <<<'EOD'
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
EOD;

// 3. --- K. Strategi ---
$targetK = <<<'EOD'
        // --- K. Strategi ---
        $strategiMap = [];
        $oldStrategiRows = (new \yii\db\Query())->from('sakip_strategi')->all();
        foreach ($oldStrategiRows as $row) {
            $p5Id = isset($periodeMap[$row['refperiode_id']]) ? $periodeMap[$row['refperiode_id']] : 2;
            $newMisiId = isset($misiMap[$row['refmisi_id']]) ? $misiMap[$row['refmisi_id']] : 0;
            $newSasaranRenstraId = isset($sasaranRenstraMap[$row['refsasaranrenstra_id']]) ? $sasaranRenstraMap[$row['refsasaranrenstra_id']] : 0;
            $newSasaranId = isset($sasaranMap[$row['refsasaran_id']]) ? $sasaranMap[$row['refsasaran_id']] : 0;
            $newTujuanId = isset($tujuanMap[$row['reftujuan_id']]) ? $tujuanMap[$row['reftujuan_id']] : 0;

            $existing = (new \yii\db\Query())
                ->from('v2_sakip_strategi')
                ->where([
                    'uraian_strategi' => $row['uraian_strategi'],
                    'refskpd_id' => $row['refskpd_id'],
                    'refperiode_5tahun_id' => $p5Id,
                ])->one();

            if ($existing) {
                $strategiMap[$row['refstrategi_id']] = $existing['refstrategi_id'];
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
                $strategiMap[$row['refstrategi_id']] = $this->db->getLastInsertID();
            }
        }
EOD;

$replaceK = <<<'EOD'
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
EOD;

// 4. --- L. Kebijakan ---
$targetL = <<<'EOD'
        // --- L. Kebijakan ---
        $kebijakanMap = [];
        $oldKebijakanRows = (new \yii\db\Query())->from('sakip_kebijakan')->all();
        foreach ($oldKebijakanRows as $row) {
            $p5Id = isset($periodeMap[$row['refperiode_id']]) ? $periodeMap[$row['refperiode_id']] : 2;
            $newStrategiId = isset($strategiMap[$row['refstrategi_id']]) ? $strategiMap[$row['refstrategi_id']] : 0;
            $newMisiId = isset($misiMap[$row['refmisi_id']]) ? $misiMap[$row['refmisi_id']] : 0;
            $newSasaranRenstraId = isset($sasaranRenstraMap[$row['refsasaranrenstra_id']]) ? $sasaranRenstraMap[$row['refsasaranrenstra_id']] : 0;
            $newSasaranId = isset($sasaranMap[$row['refsasaran_id']]) ? $sasaranMap[$row['refsasaran_id']] : 0;
            $newTujuanId = isset($tujuanMap[$row['reftujuan_id']]) ? $tujuanMap[$row['reftujuan_id']] : 0;

            $existing = (new \yii\db\Query())
                ->from('v2_sakip_kebijakan')
                ->where([
                    'uraian_kebijakan' => $row['uraian_kebijakan'],
                    'refskpd_id' => $row['refskpd_id'],
                    'refperiode_5tahun_id' => $p5Id,
                ])->one();

            if ($existing) {
                $kebijakanMap[$row['refkebijakan_id']] = $existing['refkebijakan_id'];
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
                $kebijakanMap[$row['refkebijakan_id']] = $this->db->getLastInsertID();
            }
        }
EOD;

$replaceL = <<<'EOD'
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
EOD;

// Apply replacements I-L
$content = str_replace($targetI, $replaceI, $content);
$content = str_replace($targetJ, $replaceJ, $content);
$content = str_replace($targetK, $replaceK, $content);
$content = str_replace($targetL, $replaceL, $content);

file_put_contents($file, $content);
echo "Replacement I-L done!\n";
