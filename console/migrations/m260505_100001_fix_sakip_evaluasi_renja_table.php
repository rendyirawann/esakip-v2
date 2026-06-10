<?php

use yii\db\Migration;

/**
 * Fix migration: ubah refskpd_id ke bigint(20) agar cocok dengan sakip_skpd,
 * tambah FK, dan pecah nama_program_kegiatan jadi 5 kolom per level.
 */
class m260505_100001_fix_sakip_evaluasi_renja_table extends Migration
{
    public function safeUp()
    {
        // 1. Ubah refskpd_id dari INT ke BIGINT(20) agar cocok dengan sakip_skpd.refskpd_id
        $this->execute("ALTER TABLE `sakip_evaluasi_renja` MODIFY `refskpd_id` BIGINT(20) NULL COMMENT 'FK ke sakip_skpd'");

        // 2. Tambah FK ke sakip_skpd
        $this->addForeignKey(
            'fk-evaluasi_renja-refskpd_id',
            '{{%sakip_evaluasi_renja}}',
            'refskpd_id',
            '{{%sakip_skpd}}',
            'refskpd_id',
            'SET NULL',
            'CASCADE'
        );

        // 3. Tambah 5 kolom terpisah per level
        $this->addColumn('{{%sakip_evaluasi_renja}}', 'nama_unsur', 'TEXT NULL COMMENT "Nama Unsur Penunjang" AFTER `level`');
        $this->addColumn('{{%sakip_evaluasi_renja}}', 'nama_bidang_urusan', 'TEXT NULL COMMENT "Nama Bidang Urusan" AFTER `nama_unsur`');
        $this->addColumn('{{%sakip_evaluasi_renja}}', 'nama_program', 'TEXT NULL COMMENT "Nama Program" AFTER `nama_bidang_urusan`');
        $this->addColumn('{{%sakip_evaluasi_renja}}', 'nama_kegiatan', 'TEXT NULL COMMENT "Nama Kegiatan" AFTER `nama_program`');
        $this->addColumn('{{%sakip_evaluasi_renja}}', 'nama_sub_kegiatan', 'TEXT NULL COMMENT "Nama Sub Kegiatan" AFTER `nama_kegiatan`');

        // 4. Migrasi data dari kolom lama ke kolom baru sesuai level
        $this->execute("UPDATE `sakip_evaluasi_renja` SET `nama_unsur` = `nama_program_kegiatan` WHERE `level` = 'unsur'");
        $this->execute("UPDATE `sakip_evaluasi_renja` SET `nama_bidang_urusan` = `nama_program_kegiatan` WHERE `level` = 'bidang_urusan'");
        $this->execute("UPDATE `sakip_evaluasi_renja` SET `nama_program` = `nama_program_kegiatan` WHERE `level` = 'program'");
        $this->execute("UPDATE `sakip_evaluasi_renja` SET `nama_kegiatan` = `nama_program_kegiatan` WHERE `level` = 'kegiatan'");
        $this->execute("UPDATE `sakip_evaluasi_renja` SET `nama_sub_kegiatan` = `nama_program_kegiatan` WHERE `level` = 'sub_kegiatan'");

        // 5. Hapus kolom lama
        $this->dropColumn('{{%sakip_evaluasi_renja}}', 'nama_program_kegiatan');
    }

    public function safeDown()
    {
        // Kembalikan kolom nama_program_kegiatan
        $this->addColumn('{{%sakip_evaluasi_renja}}', 'nama_program_kegiatan', 'TEXT NULL AFTER `level`');
        $this->execute("UPDATE `sakip_evaluasi_renja` SET `nama_program_kegiatan` = COALESCE(`nama_unsur`, `nama_bidang_urusan`, `nama_program`, `nama_kegiatan`, `nama_sub_kegiatan`)");

        // Hapus kolom baru
        $this->dropColumn('{{%sakip_evaluasi_renja}}', 'nama_unsur');
        $this->dropColumn('{{%sakip_evaluasi_renja}}', 'nama_bidang_urusan');
        $this->dropColumn('{{%sakip_evaluasi_renja}}', 'nama_program');
        $this->dropColumn('{{%sakip_evaluasi_renja}}', 'nama_kegiatan');
        $this->dropColumn('{{%sakip_evaluasi_renja}}', 'nama_sub_kegiatan');

        // Kembalikan FK
        $this->dropForeignKey('fk-evaluasi_renja-refskpd_id', '{{%sakip_evaluasi_renja}}');
        $this->execute("ALTER TABLE `sakip_evaluasi_renja` MODIFY `refskpd_id` INT(11) NULL COMMENT 'FK ke sakip_skpd'");
    }
}
