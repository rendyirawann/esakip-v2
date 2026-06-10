<?php

use yii\db\Migration;

class m260114_074308_add_sakip_unit_id_to_user_table extends Migration
{
    public function safeUp()
    {
        // 1. Buat Kolom menggunakan SQL Murni (Pasti berhasil karena kita tulis manual sintaksnya)
        // Kita paksa kolom ini pakai utf8mb4 biar sama dengan tabel sakip_unit_kerja
        $this->execute("
            ALTER TABLE `user` 
            ADD COLUMN `sakip_unit_id` VARCHAR(10) 
            CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci 
            NULL DEFAULT NULL 
            AFTER `nama_user`
        ");

        // 2. Buat Index (Penting untuk performa join)
        $this->createIndex(
            '{{%idx-user-sakip_unit_id}}',
            '{{%user}}',
            'sakip_unit_id'
        );

        // 3. Tambahkan Foreign Key
        // Sekarang pasti bisa karena tipe data & charset sudah sama persis (VARCHAR 10 & utf8mb4)
        $this->addForeignKey(
            '{{%fk-user-sakip_unit_id}}', // Nama Key
            '{{%user}}',                  // Tabel Anak
            'sakip_unit_id',              // Kolom Anak
            '{{%sakip_unit_kerja}}',      // Tabel Induk
            'id',                         // Kolom Induk
            'SET NULL',                   // ON DELETE
            'CASCADE'                     // ON UPDATE
        );
    }

    public function safeDown()
    {
        // Hapus Foreign Key dulu
        $this->dropForeignKey('{{%fk-user-sakip_unit_id}}', '{{%user}}');

        // Hapus Index
        $this->dropIndex('{{%idx-user-sakip_unit_id}}', '{{%user}}');

        // Hapus Kolom
        $this->dropColumn('{{%user}}', 'sakip_unit_id');
    }
}
