<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%sakip_unit_kerja}}`.
 */
class m260114_055144_create_sakip_unit_kerja_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            CREATE TABLE `sakip_unit_kerja` (
              `id` varchar(10) NOT NULL,
              `keterangan` varchar(255) NOT NULL,
              `nama` varchar(255) DEFAULT NULL,
              `kode` varchar(50) DEFAULT NULL,
              `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
              `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
              PRIMARY KEY (`id`) USING BTREE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC;
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%sakip_unit_kerja}}');
    }
}
