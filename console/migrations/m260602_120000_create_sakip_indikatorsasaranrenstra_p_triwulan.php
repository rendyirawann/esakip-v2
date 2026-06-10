<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%sakip_indikatorsasaranrenstra_p_triwulan}}` safely.
 */
class m260602_120000_create_sakip_indikatorsasaranrenstra_p_triwulan extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        if ($this->db->getTableSchema('sakip_indikatorsasaranrenstra_p_triwulan') === null) {
            $this->execute("
                CREATE TABLE `sakip_indikatorsasaranrenstra_p_triwulan` (
                  `refindikatorsasaranrenstratriwulan_p_id` bigint(20) NOT NULL AUTO_INCREMENT,
                  `refindikatorsasaranrenstra_p_id` bigint(20) DEFAULT NULL,
                  `refsasaranrenstra_p_id` bigint(20) DEFAULT NULL,
                  `refskpd_id` bigint(20) DEFAULT NULL,
                  `refperiode_id` bigint(20) DEFAULT NULL,
                  `reftriwulan_id` int(11) DEFAULT NULL,
                  `triwulan_target_rkt` varchar(30) DEFAULT NULL,
                  `triwulan_target_rkt_p` varchar(30) DEFAULT NULL,
                  `triwulan_target_pk` varchar(30) DEFAULT NULL,
                  `triwulan_target_pk_p` varchar(30) DEFAULT NULL,
                  `triwulan_realisasi` varchar(30) DEFAULT NULL,
                  `triwulan_capaian` varchar(30) DEFAULT NULL,
                  `triwulan_keterangan` longtext DEFAULT NULL,
                  `triwulan_analisis` longtext DEFAULT NULL,
                  `triwulan_keterangan_pk_p` longtext DEFAULT NULL,
                  PRIMARY KEY (`refindikatorsasaranrenstratriwulan_p_id`) USING BTREE
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC;
            ");
            echo "Table sakip_indikatorsasaranrenstra_p_triwulan created successfully.\n";
        } else {
            echo "Table sakip_indikatorsasaranrenstra_p_triwulan already exists, skipping creation.\n";
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%sakip_indikatorsasaranrenstra_p_triwulan}}');
    }
}
