<?php
$file = __DIR__ . '/../console/migrations/m260603_120000_reconstruct_sakip_to_5yearly.php';
if (!file_exists($file)) {
    die("Migration file not found\n");
}

$content = file_get_contents($file);

// 1. --- A. Visi ---
$targetA = <<<'EOD'
        // --- A. Visi ---
        $visiMap = [];
        $oldVisiRows = (new \yii\db\Query())->from('sakip_visi')->all();
        foreach ($oldVisiRows as $row) {
            $p5Id = isset($periodeMap[$row['refperiode_id']]) ? $periodeMap[$row['refperiode_id']] : 2;
            // Cari apakah sudah ada Visi dengan konten yang sama untuk periode ini
            $existing = (new \yii\db\Query())
                ->from('v2_sakip_visi')
                ->where(['uraian_visi' => $row['uraian_visi'], 'refperiode_5tahun_id' => $p5Id])
                ->one();
            if ($existing) {
                $visiMap[$row['refvisi_id']] = $existing['refvisi_id'];
            } else {
                $this->db->createCommand()->insert('v2_sakip_visi', [
                    'uraian_visi' => $row['uraian_visi'],
                    'penjabaran_visi' => $row['penjabaran_visi'],
                    'refperiode_5tahun_id' => $p5Id,
                    'visi_isaktif' => $row['visi_isaktif'],
                ])->execute();
                $newId = $this->db->getLastInsertID();
                $visiMap[$row['refvisi_id']] = $newId;
            }
        }
EOD;

$replaceA = <<<'EOD'
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
EOD;

// 2. --- B. Visi Perubahan (P) ---
$targetB = <<<'EOD'
        // --- B. Visi Perubahan (P) ---
        $visiPMap = [];
        $oldVisiPRows = (new \yii\db\Query())->from('sakip_visi_p')->all();
        foreach ($oldVisiPRows as $row) {
            $p5Id = isset($periodeMap[$row['refperiode_id']]) ? $periodeMap[$row['refperiode_id']] : 2;
            $existing = (new \yii\db\Query())
                ->from('v2_sakip_visi_p')
                ->where(['uraian_visi_p' => $row['uraian_visi_p'], 'refperiode_5tahun_id' => $p5Id])
                ->one();
            if ($existing) {
                $visiPMap[$row['refvisi_p_id']] = $existing['refvisi_p_id'];
            } else {
                $this->db->createCommand()->insert('v2_sakip_visi_p', [
                    'uraian_visi_p' => $row['uraian_visi_p'],
                    'penjabaran_visi_p' => $row['penjabaran_visi_p'],
                    'refperiode_5tahun_id' => $p5Id,
                    'visi_p_isaktif' => $row['visi_p_isaktif'],
                ])->execute();
                $newId = $this->db->getLastInsertID();
                $visiPMap[$row['refvisi_p_id']] = $newId;
            }
        }
EOD;

$replaceB = <<<'EOD'
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
EOD;

// 3. --- C. Misi ---
$targetC = <<<'EOD'
        // --- C. Misi ---
        $misiMap = [];
        $oldMisiRows = (new \yii\db\Query())->from('sakip_misi')->all();
        foreach ($oldMisiRows as $row) {
            $p5Id = isset($periodeMap[$row['refperiode_id']]) ? $periodeMap[$row['refperiode_id']] : 2;
            $newVisiId = isset($visiMap[$row['refvisi_id']]) ? $visiMap[$row['refvisi_id']] : null;
            $existing = (new \yii\db\Query())
                ->from('v2_sakip_misi')
                ->where(['uraian_misi' => $row['uraian_misi'], 'refperiode_5tahun_id' => $p5Id, 'refvisi_id' => $newVisiId])
                ->one();
            if ($existing) {
                $misiMap[$row['refmisi_id']] = $existing['refmisi_id'];
            } else {
                $this->db->createCommand()->insert('v2_sakip_misi', [
                    'uraian_misi' => $row['uraian_misi'],
                    'refvisi_id' => $newVisiId,
                    'refperiode_5tahun_id' => $p5Id,
                    'misi_isaktif' => $row['misi_isaktif'],
                ])->execute();
                $newId = $this->db->getLastInsertID();
                $misiMap[$row['refmisi_id']] = $newId;
            }
        }
EOD;

$replaceC = <<<'EOD'
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
EOD;

// 4. --- D. Misi Perubahan (P) ---
$targetD = <<<'EOD'
        // --- D. Misi Perubahan (P) ---
        $misiPMap = [];
        $oldMisiPRows = (new \yii\db\Query())->from('sakip_misi_p')->all();
        foreach ($oldMisiPRows as $row) {
            $p5Id = isset($periodeMap[$row['refperiode_id']]) ? $periodeMap[$row['refperiode_id']] : 2;
            $newVisiPId = isset($visiPMap[$row['refvisi_p_id']]) ? $visiPMap[$row['refvisi_p_id']] : null;
            $existing = (new \yii\db\Query())
                ->from('v2_sakip_misi_p')
                ->where(['uraian_misi_p' => $row['uraian_misi_p'], 'refperiode_5tahun_id' => $p5Id, 'refvisi_p_id' => $newVisiPId])
                ->one();
            if ($existing) {
                $misiPMap[$row['refmisi_p_id']] = $existing['refmisi_p_id'];
            } else {
                $this->db->createCommand()->insert('v2_sakip_misi_p', [
                    'uraian_misi_p' => $row['uraian_misi_p'],
                    'refvisi_p_id' => $newVisiPId,
                    'refperiode_5tahun_id' => $p5Id,
                    'misi_p_isaktif' => $row['misi_p_isaktif'],
                ])->execute();
                $newId = $this->db->getLastInsertID();
                $misiPMap[$row['refmisi_p_id']] = $newId;
            }
        }
EOD;

$replaceD = <<<'EOD'
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
EOD;

// 5. --- E. Tujuan ---
$targetE = <<<'EOD'
        // --- E. Tujuan ---
        $tujuanMap = [];
        $oldTujuanRows = (new \yii\db\Query())->from('sakip_tujuan')->all();
        foreach ($oldTujuanRows as $row) {
            $p5Id = isset($periodeMap[$row['refperiode_id']]) ? $periodeMap[$row['refperiode_id']] : 2;
            $newVisiId = isset($visiMap[$row['refvisi_id']]) ? $visiMap[$row['refvisi_id']] : null;
            $newMisiId = isset($misiMap[$row['refmisi_id']]) ? $misiMap[$row['refmisi_id']] : null;
            $existing = (new \yii\db\Query())
                ->from('v2_sakip_tujuan')
                ->where(['uraian_tujuan' => $row['uraian_tujuan'], 'refperiode_5tahun_id' => $p5Id, 'refmisi_id' => $newMisiId])
                ->one();
            if ($existing) {
                $tujuanMap[$row['reftujuan_id']] = $existing['reftujuan_id'];
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
                $tujuanMap[$row['reftujuan_id']] = $newId;
            }
        }
EOD;

$replaceE = <<<'EOD'
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
EOD;

// 6. --- F. Tujuan Perubahan (P) ---
$targetF = <<<'EOD'
        // --- F. Tujuan Perubahan (P) ---
        $tujuanPMap = [];
        $oldTujuanPRows = (new \yii\db\Query())->from('sakip_tujuan_p')->all();
        foreach ($oldTujuanPRows as $row) {
            $p5Id = isset($periodeMap[$row['refperiode_id']]) ? $periodeMap[$row['refperiode_id']] : 2;
            $newVisiPId = isset($visiPMap[$row['refvisi_p_id']]) ? $visiPMap[$row['refvisi_p_id']] : null;
            $newMisiPId = isset($misiPMap[$row['refmisi_p_id']]) ? $misiPMap[$row['refmisi_p_id']] : null;
            $existing = (new \yii\db\Query())
                ->from('v2_sakip_tujuan_p')
                ->where(['uraian_tujuan_p' => $row['uraian_tujuan_p'], 'refperiode_5tahun_id' => $p5Id, 'refmisi_p_id' => $newMisiPId])
                ->one();
            if ($existing) {
                $tujuanPMap[$row['reftujuan_p_id']] = $existing['reftujuan_p_id'];
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
                $tujuanPMap[$row['reftujuan_p_id']] = $newId;
            }
        }
EOD;

$replaceF = <<<'EOD'
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
EOD;

// 7. --- G. Sasaran ---
$targetG = <<<'EOD'
        // --- G. Sasaran ---
        $sasaranMap = [];
        $oldSasaranRows = (new \yii\db\Query())->from('sakip_sasaran')->all();
        foreach ($oldSasaranRows as $row) {
            $p5Id = isset($periodeMap[$row['refperiode_id']]) ? $periodeMap[$row['refperiode_id']] : 2;
            $newVisiId = isset($visiMap[$row['refvisi_id']]) ? $visiMap[$row['refvisi_id']] : null;
            $newMisiId = isset($misiMap[$row['refmisi_id']]) ? $misiMap[$row['refmisi_id']] : null;
            $newTujuanId = isset($tujuanMap[$row['reftujuan_id']]) ? $tujuanMap[$row['reftujuan_id']] : null;
            $existing = (new \yii\db\Query())
                ->from('v2_sakip_sasaran')
                ->where(['uraian_sasaran' => $row['uraian_sasaran'], 'refperiode_5tahun_id' => $p5Id, 'reftujuan_id' => $newTujuanId])
                ->one();
            if ($existing) {
                $sasaranMap[$row['refsasaran_id']] = $existing['refsasaran_id'];
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
                $sasaranMap[$row['refsasaran_id']] = $newId;
            }
        }
EOD;

$replaceG = <<<'EOD'
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
EOD;

// 8. --- H. Sasaran Perubahan (P) ---
$targetH = <<<'EOD'
        // --- H. Sasaran Perubahan (P) ---
        $sasaranPMap = [];
        $oldSasaranPRows = (new \yii\db\Query())->from('sakip_sasaran_p')->all();
        foreach ($oldSasaranPRows as $row) {
            $p5Id = isset($periodeMap[$row['refperiode_id']]) ? $periodeMap[$row['refperiode_id']] : 2;
            $newVisiPId = isset($visiPMap[$row['refvisi_p_id']]) ? $visiPMap[$row['refvisi_p_id']] : null;
            $newMisiPId = isset($misiPMap[$row['refmisi_p_id']]) ? $misiPMap[$row['refmisi_p_id']] : null;
            $newTujuanPId = isset($tujuanPMap[$row['reftujuan_p_id']]) ? $tujuanPMap[$row['reftujuan_p_id']] : null;
            $existing = (new \yii\db\Query())
                ->from('v2_sakip_sasaran_p')
                ->where(['uraian_sasaran_p' => $row['uraian_sasaran_p'], 'refperiode_5tahun_id' => $p5Id, 'reftujuan_p_id' => $newTujuanPId])
                ->one();
            if ($existing) {
                $sasaranPMap[$row['refsasaran_p_id']] = $existing['refsasaran_p_id'];
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
                $sasaranPMap[$row['refsasaran_p_id']] = $newId;
            }
        }
EOD;

$replaceH = <<<'EOD'
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
EOD;

// Apply replacements A-H
$content = str_replace($targetA, $replaceA, $content);
$content = str_replace($targetB, $replaceB, $content);
$content = str_replace($targetC, $replaceC, $content);
$content = str_replace($targetD, $replaceD, $content);
$content = str_replace($targetE, $replaceE, $content);
$content = str_replace($targetF, $replaceF, $content);
$content = str_replace($targetG, $replaceG, $content);
$content = str_replace($targetH, $replaceH, $content);

file_put_contents($file, $content);
echo "Replacement A-H done!\n";
