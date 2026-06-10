<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%sakip_program_pakdhe}}`.
 */
class m260114_141835_create_sakip_program_pakdhe_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // 1. Buat Tabel dengan SQL Murni
        // id_detail_renstra format: ID_API + "-" + TAHUN
        $this->execute("
            CREATE TABLE `sakip_program_pakdhe` (
                `id_detail_renstra` VARCHAR(255) NOT NULL,
                `id_unit` VARCHAR(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL,
                `id_parent` VARCHAR(255) NULL COMMENT 'ID Sasaran (Parent)',
                `kode_unsur` VARCHAR(50) NULL COMMENT 'Kode Program (ex: 2.16.2)',
                `kode_urut` VARCHAR(50) NULL,
                `uraian` TEXT NULL,
                `tahun` INT(11) NULL,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id_detail_renstra`)
            ) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE=InnoDB;
        ");

        // 2. Index
        $this->createIndex('idx-sakip_program-id_unit', '{{%sakip_program_pakdhe}}', 'id_unit');
        $this->createIndex('idx-sakip_program-tahun', '{{%sakip_program_pakdhe}}', 'tahun');
        $this->createIndex('idx-sakip_program-id_parent', '{{%sakip_program_pakdhe}}', 'id_parent');

        // 3. Foreign Key ke Unit Kerja
        $this->addForeignKey(
            'fk-sakip_program-id_unit',
            '{{%sakip_program_pakdhe}}',
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
        $this->dropForeignKey('fk-sakip_program-id_unit', '{{%sakip_program_pakdhe}}');
        $this->dropTable('{{%sakip_program_pakdhe}}');
    }
}
