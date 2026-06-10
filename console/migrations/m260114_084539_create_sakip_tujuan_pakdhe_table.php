<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%sakip_tujuan_pakdhe}}`.
 */
class m260114_084539_create_sakip_tujuan_pakdhe_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // 1. Buat Tabel dengan SQL Murni (Raw SQL)
        // Kita simpan 'tahun' juga agar mudah difilter di lokal nanti
        $this->execute("
            CREATE TABLE `sakip_tujuan_pakdhe` (
                `id_detail_renstra` VARCHAR(255) NOT NULL,
                `id_unit` VARCHAR(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL,
                `kode_urut` VARCHAR(50) NULL,
                `uraian` TEXT NULL,
                `tahun` INT(11) NULL,
                `sasaran_strategis` TEXT NULL,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id_detail_renstra`)
            ) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE=InnoDB;
        ");

        // 2. Buat Index untuk id_unit (Optimasi query)
        $this->createIndex(
            '{{%idx-sakip_tujuan_pakdhe-id_unit}}',
            '{{%sakip_tujuan_pakdhe}}',
            'id_unit'
        );

        // 3. Tambahkan Foreign Key ke sakip_unit_kerja (Opsional, menjaga relasi data)
        // Pastikan tabel sakip_unit_kerja sudah ada (dari langkah sebelumnya)
        $this->addForeignKey(
            '{{%fk-sakip_tujuan_pakdhe-id_unit}}',
            '{{%sakip_tujuan_pakdhe}}',
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
        $this->dropForeignKey('{{%fk-sakip_tujuan_pakdhe-id_unit}}', '{{%sakip_tujuan_pakdhe}}');
        $this->dropIndex('{{%idx-sakip_tujuan_pakdhe-id_unit}}', '{{%sakip_tujuan_pakdhe}}');
        $this->dropTable('{{%sakip_tujuan_pakdhe}}');
    }
}
