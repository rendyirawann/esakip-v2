import os

file_path = r"c:\xampp\htdocs\esakip-v2\frontend\controllers\SiteController.php"

with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

# 1. Patch actionIndexEsakip
old_index_block = """        // Cek status untuk setiap card berdasarkan refperiode_id
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
            ->andWhere(["""

new_index_block = """        // Retrieve the periode based on refperiode_id
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
            ->andWhere(["""

content = content.replace(old_index_block, new_index_block)

# 2. Patch actionIndexEsakipDev (which is the same block but in actionIndexEsakipDev)
# The first replacement only replaced the first occurrence. Let's replace the second occurrence as well.
content = content.replace(old_index_block, new_index_block)

# 3. Patch actionPortal (line 2012)
old_portal_block = """            // --- MULAI PROSES DATA RENSTRA ---
            // 1. Ambil SEMUA data yang dibutuhkan di awal (hanya beberapa query)
            $sasarans = SakipSasaranrenstra::find()->where(['refperiode_id' => $refperiode_id])->all();"""

new_portal_block = """            $refperiode_5tahun_id = $selectedPeriod ? $selectedPeriod->refperiode_5tahun_id : null;
            // --- MULAI PROSES DATA RENSTRA ---
            // 1. Ambil SEMUA data yang dibutuhkan di awal (hanya beberapa query)
            $sasarans = SakipSasaranrenstra::find()->where(['refperiode_5tahun_id' => $refperiode_5tahun_id])->all();"""

content = content.replace(old_portal_block, new_portal_block)

# 4. Patch actionPortalPublikPerencanaan (around line 2760)
old_portal_pub_block = """        // Retrieve the periode based on refperiode_id
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
                ->all();"""

new_portal_pub_block = """        // Retrieve the periode based on refperiode_id
        $selectedPeriod = SakipPeriode::find()->where(['refperiode_id' => $refperiode_id])->one();
        $selectedPeriodValue = $selectedPeriod ? $selectedPeriod->periode : null; // Get the periode value
        $refperiode_5tahun_id = $selectedPeriod ? $selectedPeriod->refperiode_5tahun_id : null;

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
                ->where(['refskpd_id' => $refskpd_id, 'refperiode_5tahun_id' => $refperiode_5tahun_id])
                ->all();
            $strategiList[$refskpd_id] = SakipStrategi::find()
                ->where(['refskpd_id' => $refskpd_id, 'refperiode_5tahun_id' => $refperiode_5tahun_id])
                ->all();
            $kebijakanList[$refskpd_id] = SakipKebijakan::find()
                ->where(['refskpd_id' => $refskpd_id, 'refperiode_5tahun_id' => $refperiode_5tahun_id])
                ->all();"""

content = content.replace(old_portal_pub_block, old_portal_pub_block.replace(
    "->where(['refskpd_id' => $refskpd_id, 'refperiode_id' => $refperiode_id])",
    "->where(['refskpd_id' => $refskpd_id, 'refperiode_5tahun_id' => $refperiode_5tahun_id])"
).replace(
    "// Retrieve the periode based on refperiode_id\n        $selectedPeriod = SakipPeriode::find()->where(['refperiode_id' => $refperiode_id])->one();",
    "// Retrieve the periode based on refperiode_id\n        $selectedPeriod = SakipPeriode::find()->where(['refperiode_id' => $refperiode_id])->one();\n        $refperiode_5tahun_id = $selectedPeriod ? $selectedPeriod->refperiode_5tahun_id : null;"
))

# 5. Patch RktList, PkList, PkpList in actionPortalPublikPerencanaan (around lines 2790-2805)
old_lists_block = """            // Fetch data based on refskpd_id and refperiode_id
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
                ->all();"""

new_lists_block = """            // Fetch data based on refskpd_id and refperiode_5tahun_id
            $sasaranRenstraRktList[$refskpd_id] = SakipSasaranRenstra::find()
                ->where(['refskpd_id' => $refskpd_id, 'refperiode_5tahun_id' => $refperiode_5tahun_id])
                ->with(['refVisi', 'refMisi', 'refTujuan', 'refSasaran'])
                ->all();

            $sasaranRenstraPkList[$refskpd_id] = SakipSasaranRenstra::find()
                ->where(['refskpd_id' => $refskpd_id, 'refperiode_5tahun_id' => $refperiode_5tahun_id])
                ->with(['refVisi', 'refMisi', 'refTujuan', 'refSasaran'])
                ->all();

            $sasaranRenstraPkpList[$refskpd_id] = SakipSasaranRenstra::find()
                ->where(['refskpd_id' => $refskpd_id, 'refperiode_5tahun_id' => $refperiode_5tahun_id])
                ->with(['refVisi', 'refMisi', 'refTujuan', 'refSasaran'])
                ->all();"""

content = content.replace(old_lists_block, new_lists_block)

with open(file_path, "w", encoding="utf-8") as f:
    f.write(content)

print("SiteController.php patched successfully!")
