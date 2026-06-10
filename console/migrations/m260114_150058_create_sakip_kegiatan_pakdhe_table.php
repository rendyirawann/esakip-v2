<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%sakip_kegiatan_pakdhe}}`.
 */
class m260114_150058_create_sakip_kegiatan_pakdhe_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // 1. Buat Tabel dengan SQL Murni
        // id_detail_renstra format: ID_API + "-" + TAHUN (Agar unik per tahun)
        $this->execute("
            CREATE TABLE `sakip_kegiatan_pakdhe` (
                `id_detail_renstra` VARCHAR(255) NOT NULL,
                `id_unit` VARCHAR(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL,
                `id_parent` VARCHAR(255) NULL COMMENT 'ID Program (Parent)',
                `kode_unsur` VARCHAR(50) NULL COMMENT 'Kode Kegiatan (ex: 2.16.2.2.01)',
                `kode_urut` VARCHAR(50) NULL,
                `uraian` TEXT NULL,
                `tahun` INT(11) NULL,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id_detail_renstra`)
            ) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE=InnoDB;
        ");

        // 2. Index untuk performa
        $this->createIndex('idx-sakip_kegiatan-id_unit', '{{%sakip_kegiatan_pakdhe}}', 'id_unit');
        $this->createIndex('idx-sakip_kegiatan-tahun', '{{%sakip_kegiatan_pakdhe}}', 'tahun');
        $this->createIndex('idx-sakip_kegiatan-id_parent', '{{%sakip_kegiatan_pakdhe}}', 'id_parent');

        // 3. Foreign Key ke Unit Kerja
        $this->addForeignKey(
            'fk-sakip_kegiatan-id_unit',
            '{{%sakip_kegiatan_pakdhe}}',
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
        $this->dropForeignKey('fk-sakip_kegiatan-id_unit', '{{%sakip_kegiatan_pakdhe}}');
        $this->dropTable('{{%sakip_kegiatan_pakdhe}}');
    }
}
