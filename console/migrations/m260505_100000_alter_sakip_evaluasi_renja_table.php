<?php

use yii\db\Migration;

/**
 * Migration untuk mengubah tabel sakip_evaluasi_renja:
 * 1. Ganti FK dari sakip_unit_kerja ke sakip_skpd (refskpd_id)
 * 2. Pecah kolom nama_program_kegiatan jadi 5 kolom terpisah per level
 */
class m260505_100000_alter_sakip_evaluasi_renja_table extends Migration
{
    public function safeUp()
    {
        // 1. Hapus FK lama ke sakip_unit_kerja
        $this->dropForeignKey('fk-evaluasi_renja-id_unit', '{{%sakip_evaluasi_renja}}');
        $this->dropIndex('idx-evaluasi_renja-id_unit', '{{%sakip_evaluasi_renja}}');
        $this->dropIndex('idx-evaluasi_renja-unit_tahun', '{{%sakip_evaluasi_renja}}');

        // 2. Hapus kolom id_unit lama
        $this->dropColumn('{{%sakip_evaluasi_renja}}', 'id_unit');

        // 3. Tambah kolom refskpd_id (FK ke sakip_skpd)
        $this->addColumn('{{%sakip_evaluasi_renja}}', 'refskpd_id', $this->integer()->null()->comment('FK ke sakip_skpd')->after('id'));

        // 4. Index dan FK baru ke sakip_skpd
        $this->createIndex('idx-evaluasi_renja-refskpd_id', '{{%sakip_evaluasi_renja}}', 'refskpd_id');
        $this->createIndex('idx-evaluasi_renja-skpd_tahun', '{{%sakip_evaluasi_renja}}', ['refskpd_id', 'tahun']);
        $this->addForeignKey(
            'fk-evaluasi_renja-refskpd_id',
            '{{%sakip_evaluasi_renja}}',
            'refskpd_id',
            '{{%sakip_skpd}}',
            'refskpd_id',
            'SET NULL',
            'CASCADE'
        );

        // 5. Pecah nama_program_kegiatan jadi 5 kolom terpisah
        $this->addColumn('{{%sakip_evaluasi_renja}}', 'nama_unsur', $this->text()->null()->comment('Nama Unsur Penunjang')->after('level'));
        $this->addColumn('{{%sakip_evaluasi_renja}}', 'nama_bidang_urusan', $this->text()->null()->comment('Nama Bidang Urusan')->after('nama_unsur'));
        $this->addColumn('{{%sakip_evaluasi_renja}}', 'nama_program', $this->text()->null()->comment('Nama Program')->after('nama_bidang_urusan'));
        $this->addColumn('{{%sakip_evaluasi_renja}}', 'nama_kegiatan', $this->text()->null()->comment('Nama Kegiatan')->after('nama_program'));
        $this->addColumn('{{%sakip_evaluasi_renja}}', 'nama_sub_kegiatan', $this->text()->null()->comment('Nama Sub Kegiatan')->after('nama_kegiatan'));

        // 6. Migrasi data lama (jika ada) dari nama_program_kegiatan ke kolom baru sesuai level
        $this->execute("
            UPDATE `sakip_evaluasi_renja` SET `nama_unsur` = `nama_program_kegiatan` WHERE `level` = 'unsur';
        ");
        $this->execute("
            UPDATE `sakip_evaluasi_renja` SET `nama_bidang_urusan` = `nama_program_kegiatan` WHERE `level` = 'bidang_urusan';
        ");
        $this->execute("
            UPDATE `sakip_evaluasi_renja` SET `nama_program` = `nama_program_kegiatan` WHERE `level` = 'program';
        ");
        $this->execute("
            UPDATE `sakip_evaluasi_renja` SET `nama_kegiatan` = `nama_program_kegiatan` WHERE `level` = 'kegiatan';
        ");
        $this->execute("
            UPDATE `sakip_evaluasi_renja` SET `nama_sub_kegiatan` = `nama_program_kegiatan` WHERE `level` = 'sub_kegiatan';
        ");

        // 7. Hapus kolom lama
        $this->dropColumn('{{%sakip_evaluasi_renja}}', 'nama_program_kegiatan');
    }

    public function safeDown()
    {
        // Kembalikan kolom nama_program_kegiatan
        $this->addColumn('{{%sakip_evaluasi_renja}}', 'nama_program_kegiatan', $this->text()->null()->after('level'));

        // Isi ulang dari kolom-kolom baru
        $this->execute("UPDATE `sakip_evaluasi_renja` SET `nama_program_kegiatan` = COALESCE(`nama_unsur`, `nama_bidang_urusan`, `nama_program`, `nama_kegiatan`, `nama_sub_kegiatan`)");

        // Hapus kolom-kolom baru
        $this->dropColumn('{{%sakip_evaluasi_renja}}', 'nama_unsur');
        $this->dropColumn('{{%sakip_evaluasi_renja}}', 'nama_bidang_urusan');
        $this->dropColumn('{{%sakip_evaluasi_renja}}', 'nama_program');
        $this->dropColumn('{{%sakip_evaluasi_renja}}', 'nama_kegiatan');
        $this->dropColumn('{{%sakip_evaluasi_renja}}', 'nama_sub_kegiatan');

        // Kembalikan FK ke sakip_unit_kerja
        $this->dropForeignKey('fk-evaluasi_renja-refskpd_id', '{{%sakip_evaluasi_renja}}');
        $this->dropIndex('idx-evaluasi_renja-refskpd_id', '{{%sakip_evaluasi_renja}}');
        $this->dropIndex('idx-evaluasi_renja-skpd_tahun', '{{%sakip_evaluasi_renja}}');
        $this->dropColumn('{{%sakip_evaluasi_renja}}', 'refskpd_id');

        $this->addColumn('{{%sakip_evaluasi_renja}}', 'id_unit', $this->string(10)->null()->after('id'));
        $this->createIndex('idx-evaluasi_renja-id_unit', '{{%sakip_evaluasi_renja}}', 'id_unit');
        $this->createIndex('idx-evaluasi_renja-unit_tahun', '{{%sakip_evaluasi_renja}}', ['id_unit', 'tahun']);
        $this->addForeignKey(
            'fk-evaluasi_renja-id_unit',
            '{{%sakip_evaluasi_renja}}',
            'id_unit',
            '{{%sakip_unit_kerja}}',
            'id',
            'SET NULL',
            'CASCADE'
        );
    }
}
