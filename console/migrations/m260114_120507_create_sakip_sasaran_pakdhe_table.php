<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%sakip_sasaran_pakdhe}}`.
 */
class m260114_120507_create_sakip_sasaran_pakdhe_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // 1. Buat Tabel dengan SQL Murni
        // id_detail_renstra di sini akan kita isi dengan format: ID_API + TAHUN (agar unik per tahun)
        $this->execute("
            CREATE TABLE `sakip_sasaran_pakdhe` (
                `id_detail_renstra` VARCHAR(255) NOT NULL, 
                `id_unit` VARCHAR(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL,
                `id_parent` VARCHAR(255) NULL COMMENT 'ID Tujuan (Parent)',
                `kode_urut` VARCHAR(50) NULL,
                `uraian` TEXT NULL,
                `tahun` INT(11) NULL,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id_detail_renstra`)
            ) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE=InnoDB;
        ");

        // 2. Index untuk pencarian cepat
        $this->createIndex('idx-sakip_sasaran-id_unit', '{{%sakip_sasaran_pakdhe}}', 'id_unit');
        $this->createIndex('idx-sakip_sasaran-tahun', '{{%sakip_sasaran_pakdhe}}', 'tahun');

        // 3. Foreign Key ke Unit Kerja (Opsional, hapus jika error)
        $this->addForeignKey(
            'fk-sakip_sasaran-id_unit',
            '{{%sakip_sasaran_pakdhe}}',
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
        $this->dropForeignKey('fk-sakip_sasaran-id_unit', '{{%sakip_sasaran_pakdhe}}');
        $this->dropTable('{{%sakip_sasaran_pakdhe}}');
    }
}
