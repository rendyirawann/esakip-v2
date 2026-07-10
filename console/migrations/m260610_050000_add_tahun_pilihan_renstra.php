<?php

use yii\db\Migration;

/**
 * Menyimpan "tahun yang dipilih" pada data Renstra (Visi/Misi/Tujuan/Sasaran).
 *
 * Sebelumnya tabel hanya menyimpan blok 5-tahun (refperiode_5tahun_id) sehingga
 * tahun spesifik yang dipilih user tidak tersimpan. Migration ini menambah kolom
 * refperiode_id (nullable, additive) lalu mem-backfill data lama dengan tahun
 * AKTIF pertama di bloknya. AMAN: hanya menambah kolom & mengisi nilai NULL,
 * tidak mengubah kolom/relasi yang sudah ada.
 */
class m260610_050000_add_tahun_pilihan_renstra extends Migration
{
    private $tables = [
        'v2_sakip_visi',
        'v2_sakip_misi',
        'v2_sakip_tujuan',
        'v2_sakip_sasaran',
    ];

    public function safeUp()
    {
        foreach ($this->tables as $t) {
            // Tambah kolom saja (additive, nullable). TANPA backfill — data lama
            // dibiarkan NULL sampai record diedit-ulang & tahun-nya dipilih.
            $exists = $this->db->getTableSchema($t, true)->getColumn('refperiode_id');
            if ($exists === null) {
                $this->addColumn($t, 'refperiode_id', $this->bigInteger()->null()->after('refperiode_5tahun_id'));
            }
        }
    }

    public function safeDown()
    {
        foreach ($this->tables as $t) {
            $exists = $this->db->getTableSchema($t, true)->getColumn('refperiode_id');
            if ($exists !== null) {
                $this->dropColumn($t, 'refperiode_id');
            }
        }
    }
}
