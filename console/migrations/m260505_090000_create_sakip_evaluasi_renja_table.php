<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%sakip_evaluasi_renja}}`.
 * Tabel untuk menyimpan data Evaluasi Terhadap Hasil RKPD (Renja)
 * yang diimport dari file Excel.
 */
class m260505_090000_create_sakip_evaluasi_renja_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            CREATE TABLE `sakip_evaluasi_renja` (
                `id` INT(11) NOT NULL AUTO_INCREMENT,
                `id_unit` VARCHAR(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT 'FK ke sakip_unit_kerja',
                `tahun` INT(11) NOT NULL COMMENT 'Tahun Evaluasi',
                `no_urut` INT(11) NULL COMMENT 'Nomor urut dari kolom A',
                `row_order` INT(11) NOT NULL DEFAULT 0 COMMENT 'Urutan baris untuk display',
                `level` ENUM('unsur','bidang_urusan','program','kegiatan','sub_kegiatan') NULL COMMENT 'Level hierarki baris',
                `visi` TEXT NULL COMMENT 'Kolom B - Visi RPJMD',
                `misi` TEXT NULL COMMENT 'Kolom C - Misi RPJMD',
                `tujuan` TEXT NULL COMMENT 'Kolom D - Tujuan Pembangunan',
                `sasaran_strategis` TEXT NULL COMMENT 'Kolom E - Sasaran Strategis',
                `sasaran_opd` TEXT NULL COMMENT 'Kolom F - Sasaran Perangkat Daerah',
                `nama_program_kegiatan` TEXT NULL COMMENT 'Kolom G - Urusan/Program/Kegiatan',
                `indikator_kinerja` TEXT NULL COMMENT 'Kolom H - Indikator Kinerja',
                `satuan` VARCHAR(100) NULL COMMENT 'Kolom I - Satuan',
                `target_renstra_kinerja` DECIMAL(20,4) NULL COMMENT 'Kolom J - Target Renstra Kinerja',
                `target_renstra_anggaran` DECIMAL(20,2) NULL COMMENT 'Kolom K - Target Renstra Anggaran',
                `realisasi_sd_lalu_kinerja` DECIMAL(20,4) NULL COMMENT 'Kolom L - Realisasi s/d Tahun Lalu Kinerja',
                `realisasi_sd_lalu_anggaran` DECIMAL(20,2) NULL COMMENT 'Kolom M - Realisasi s/d Tahun Lalu Anggaran',
                `target_renja_kinerja` DECIMAL(20,4) NULL COMMENT 'Kolom N - Target Renja Kinerja',
                `target_renja_anggaran` DECIMAL(20,2) NULL COMMENT 'Kolom O - Target Renja Anggaran',
                `tw1_kinerja` DECIMAL(20,4) NULL COMMENT 'Kolom P - TW I Kinerja',
                `tw1_persen` DECIMAL(10,2) NULL COMMENT 'Kolom Q - TW I Persen',
                `tw1_anggaran` DECIMAL(20,2) NULL COMMENT 'Kolom R - TW I Anggaran',
                `tw2_kinerja` DECIMAL(20,4) NULL COMMENT 'Kolom S - TW II Kinerja',
                `tw2_persen` DECIMAL(10,2) NULL COMMENT 'Kolom T - TW II Persen',
                `tw2_anggaran` DECIMAL(20,2) NULL COMMENT 'Kolom U - TW II Anggaran',
                `tw3_kinerja` DECIMAL(20,4) NULL COMMENT 'Kolom V - TW III Kinerja',
                `tw3_persen` DECIMAL(10,2) NULL COMMENT 'Kolom W - TW III Persen',
                `tw3_anggaran` DECIMAL(20,2) NULL COMMENT 'Kolom X - TW III Anggaran',
                `tw4_kinerja` DECIMAL(20,4) NULL COMMENT 'Kolom Y - TW IV Kinerja',
                `tw4_persen` DECIMAL(10,2) NULL COMMENT 'Kolom Z - TW IV Persen',
                `tw4_anggaran` DECIMAL(20,2) NULL COMMENT 'Kolom AA - TW IV Anggaran',
                `realisasi_capaian_kinerja` DECIMAL(20,4) NULL COMMENT 'Kolom AB - Realisasi Capaian Kinerja',
                `realisasi_capaian_anggaran` DECIMAL(20,2) NULL COMMENT 'Kolom AC - Realisasi Capaian Anggaran',
                `realisasi_renstra_kinerja` DECIMAL(20,4) NULL COMMENT 'Kolom AD - Realisasi Renstra s/d thn ini Kinerja',
                `realisasi_renstra_anggaran` DECIMAL(20,2) NULL COMMENT 'Kolom AE - Realisasi Renstra s/d thn ini Anggaran',
                `tingkat_capaian_kinerja` DECIMAL(10,2) NULL COMMENT 'Kolom AF - Tingkat Capaian Kinerja (%)',
                `tingkat_capaian_anggaran` DECIMAL(10,2) NULL COMMENT 'Kolom AG - Tingkat Capaian Anggaran (%)',
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`)
            ) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE=InnoDB;
        ");

        // Index untuk performa query
        $this->createIndex('idx-evaluasi_renja-id_unit', '{{%sakip_evaluasi_renja}}', 'id_unit');
        $this->createIndex('idx-evaluasi_renja-tahun', '{{%sakip_evaluasi_renja}}', 'tahun');
        $this->createIndex('idx-evaluasi_renja-level', '{{%sakip_evaluasi_renja}}', 'level');
        $this->createIndex('idx-evaluasi_renja-unit_tahun', '{{%sakip_evaluasi_renja}}', ['id_unit', 'tahun']);

        // Foreign Key ke Unit Kerja
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

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-evaluasi_renja-id_unit', '{{%sakip_evaluasi_renja}}');
        $this->dropTable('{{%sakip_evaluasi_renja}}');
    }
}
