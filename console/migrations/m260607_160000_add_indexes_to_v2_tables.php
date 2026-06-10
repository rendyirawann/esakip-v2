<?php

use yii\db\Migration;

/**
 * Class m260607_160000_add_indexes_to_v2_tables
 * Adds indexes to foreign key columns of all v2 SAKIP tables and user table for query optimization.
 */
class m260607_160000_add_indexes_to_v2_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        echo "Adding indexes to v2 SAKIP and user tables...\n";

        // v2_sakip_periode
        $this->createIndex('idx-v2_sakip_periode-5tahun', 'v2_sakip_periode', 'refperiode_5tahun_id');

        // v2_sakip_visi & v2_sakip_visi_p
        $this->createIndex('idx-v2_sakip_visi-5tahun', 'v2_sakip_visi', 'refperiode_5tahun_id');
        $this->createIndex('idx-v2_sakip_visi_p-5tahun', 'v2_sakip_visi_p', 'refperiode_5tahun_id');

        // v2_sakip_misi & v2_sakip_misi_p
        $this->createIndex('idx-v2_sakip_misi-5tahun', 'v2_sakip_misi', 'refperiode_5tahun_id');
        $this->createIndex('idx-v2_sakip_misi-visi', 'v2_sakip_misi', 'refvisi_id');
        $this->createIndex('idx-v2_sakip_misi_p-5tahun', 'v2_sakip_misi_p', 'refperiode_5tahun_id');
        $this->createIndex('idx-v2_sakip_misi_p-visi_p', 'v2_sakip_misi_p', 'refvisi_p_id');

        // v2_sakip_tujuan & v2_sakip_tujuan_p
        $this->createIndex('idx-v2_sakip_tujuan-5tahun', 'v2_sakip_tujuan', 'refperiode_5tahun_id');
        $this->createIndex('idx-v2_sakip_tujuan-visi', 'v2_sakip_tujuan', 'refvisi_id');
        $this->createIndex('idx-v2_sakip_tujuan-misi', 'v2_sakip_tujuan', 'refmisi_id');
        $this->createIndex('idx-v2_sakip_tujuan_p-5tahun', 'v2_sakip_tujuan_p', 'refperiode_5tahun_id');
        $this->createIndex('idx-v2_sakip_tujuan_p-visi_p', 'v2_sakip_tujuan_p', 'refvisi_p_id');
        $this->createIndex('idx-v2_sakip_tujuan_p-misi_p', 'v2_sakip_tujuan_p', 'refmisi_p_id');

        // v2_sakip_sasaran & v2_sakip_sasaran_p
        $this->createIndex('idx-v2_sakip_sasaran-5tahun', 'v2_sakip_sasaran', 'refperiode_5tahun_id');
        $this->createIndex('idx-v2_sakip_sasaran-visi', 'v2_sakip_sasaran', 'refvisi_id');
        $this->createIndex('idx-v2_sakip_sasaran-misi', 'v2_sakip_sasaran', 'refmisi_id');
        $this->createIndex('idx-v2_sakip_sasaran-tujuan', 'v2_sakip_sasaran', 'reftujuan_id');
        $this->createIndex('idx-v2_sakip_sasaran_p-5tahun', 'v2_sakip_sasaran_p', 'refperiode_5tahun_id');
        $this->createIndex('idx-v2_sakip_sasaran_p-visi_p', 'v2_sakip_sasaran_p', 'refvisi_p_id');
        $this->createIndex('idx-v2_sakip_sasaran_p-misi_p', 'v2_sakip_sasaran_p', 'refmisi_p_id');
        $this->createIndex('idx-v2_sakip_sasaran_p-tujuan_p', 'v2_sakip_sasaran_p', 'reftujuan_p_id');

        // v2_sakip_tujuanrenstra & v2_sakip_tujuanrenstra_p
        $this->createIndex('idx-v2_sakip_tujuanrenstra-5tahun', 'v2_sakip_tujuanrenstra', 'refperiode_5tahun_id');
        $this->createIndex('idx-v2_sakip_tujuanrenstra-skpd', 'v2_sakip_tujuanrenstra', 'refskpd_id');
        $this->createIndex('idx-v2_sakip_tujuanrenstra-misi', 'v2_sakip_tujuanrenstra', 'refmisi_id');
        $this->createIndex('idx-v2_sakip_tujuanrenstra-tujuan', 'v2_sakip_tujuanrenstra', 'reftujuan_id');
        $this->createIndex('idx-v2_sakip_tujuanrenstra-sasaran', 'v2_sakip_tujuanrenstra', 'refsasaran_id');
        $this->createIndex('idx-v2_sakip_tujuanrenstra_p-5tahun', 'v2_sakip_tujuanrenstra_p', 'refperiode_5tahun_id');
        $this->createIndex('idx-v2_sakip_tujuanrenstra_p-skpd', 'v2_sakip_tujuanrenstra_p', 'refskpd_id');
        $this->createIndex('idx-v2_sakip_tujuanrenstra_p-misi_p', 'v2_sakip_tujuanrenstra_p', 'refmisi_p_id');
        $this->createIndex('idx-v2_sakip_tujuanrenstra_p-tujuan_p', 'v2_sakip_tujuanrenstra_p', 'reftujuan_p_id');
        $this->createIndex('idx-v2_sakip_tujuanrenstra_p-sasaran_p', 'v2_sakip_tujuanrenstra_p', 'refsasaran_p_id');

        // v2_sakip_sasaranrenstra & v2_sakip_sasaranrenstra_p
        $this->createIndex('idx-v2_sakip_sasaranrenstra-5tahun', 'v2_sakip_sasaranrenstra', 'refperiode_5tahun_id');
        $this->createIndex('idx-v2_sakip_sasaranrenstra-skpd', 'v2_sakip_sasaranrenstra', 'refskpd_id');
        $this->createIndex('idx-v2_sakip_sasaranrenstra-sasaran', 'v2_sakip_sasaranrenstra', 'refsasaran_id');
        $this->createIndex('idx-v2_sakip_sasaranrenstra-visi', 'v2_sakip_sasaranrenstra', 'refvisi_id');
        $this->createIndex('idx-v2_sakip_sasaranrenstra-misi', 'v2_sakip_sasaranrenstra', 'refmisi_id');
        $this->createIndex('idx-v2_sakip_sasaranrenstra-tujuan', 'v2_sakip_sasaranrenstra', 'reftujuan_id');
        $this->createIndex('idx-v2_sakip_sasaranrenstra-tujuanrenstra', 'v2_sakip_sasaranrenstra', 'reftujuanrenstra_id');
        $this->createIndex('idx-v2_sakip_sasaranrenstra_p-5tahun', 'v2_sakip_sasaranrenstra_p', 'refperiode_5tahun_id');
        $this->createIndex('idx-v2_sakip_sasaranrenstra_p-skpd', 'v2_sakip_sasaranrenstra_p', 'refskpd_id');
        $this->createIndex('idx-v2_sakip_sasaranrenstra_p-sasaran_p', 'v2_sakip_sasaranrenstra_p', 'refsasaran_p_id');
        $this->createIndex('idx-v2_sakip_sasaranrenstra_p-visi_p', 'v2_sakip_sasaranrenstra_p', 'refvisi_p_id');
        $this->createIndex('idx-v2_sakip_sasaranrenstra_p-misi_p', 'v2_sakip_sasaranrenstra_p', 'refmisi_p_id');
        $this->createIndex('idx-v2_sakip_sasaranrenstra_p-tujuan_p', 'v2_sakip_sasaranrenstra_p', 'reftujuan_p_id');
        $this->createIndex('idx-v2_sakip_sasaranrenstra_p-tujuanrenstra_p', 'v2_sakip_sasaranrenstra_p', 'reftujuanrenstra_p_id');

        // v2_sakip_strategi
        $this->createIndex('idx-v2_sakip_strategi-5tahun', 'v2_sakip_strategi', 'refperiode_5tahun_id');
        $this->createIndex('idx-v2_sakip_strategi-skpd', 'v2_sakip_strategi', 'refskpd_id');
        $this->createIndex('idx-v2_sakip_strategi-misi', 'v2_sakip_strategi', 'refmisi_id');
        $this->createIndex('idx-v2_sakip_strategi-sasaranrenstra', 'v2_sakip_strategi', 'refsasaranrenstra_id');
        $this->createIndex('idx-v2_sakip_strategi-sasaran', 'v2_sakip_strategi', 'refsasaran_id');
        $this->createIndex('idx-v2_sakip_strategi-tujuan', 'v2_sakip_strategi', 'reftujuan_id');

        // v2_sakip_kebijakan
        $this->createIndex('idx-v2_sakip_kebijakan-5tahun', 'v2_sakip_kebijakan', 'refperiode_5tahun_id');
        $this->createIndex('idx-v2_sakip_kebijakan-skpd', 'v2_sakip_kebijakan', 'refskpd_id');
        $this->createIndex('idx-v2_sakip_kebijakan-strategi', 'v2_sakip_kebijakan', 'refstrategi_id');
        $this->createIndex('idx-v2_sakip_kebijakan-misi', 'v2_sakip_kebijakan', 'refmisi_id');
        $this->createIndex('idx-v2_sakip_kebijakan-sasaranrenstra', 'v2_sakip_kebijakan', 'refsasaranrenstra_id');
        $this->createIndex('idx-v2_sakip_kebijakan-sasaran', 'v2_sakip_kebijakan', 'refsasaran_id');
        $this->createIndex('idx-v2_sakip_kebijakan-tujuan', 'v2_sakip_kebijakan', 'reftujuan_id');

        // v2_sakip_indikatorsasaranrenstra & v2_sakip_indikatorsasaranrenstra_p
        $this->createIndex('idx-v2_sakip_indikatorsasaranrenstra-periode', 'v2_sakip_indikatorsasaranrenstra', 'refperiode_id');
        $this->createIndex('idx-v2_sakip_indikatorsasaranrenstra-skpd', 'v2_sakip_indikatorsasaranrenstra', 'refskpd_id');
        $this->createIndex('idx-v2_sakip_indikatorsasaranrenstra-sasaranrenstra', 'v2_sakip_indikatorsasaranrenstra', 'refsasaranrenstra_id');
        $this->createIndex('idx-v2_sakip_indikatorsasaranrenstra_p-periode', 'v2_sakip_indikatorsasaranrenstra_p', 'refperiode_id');
        $this->createIndex('idx-v2_sakip_indikatorsasaranrenstra_p-skpd', 'v2_sakip_indikatorsasaranrenstra_p', 'refskpd_id');
        $this->createIndex('idx-v2_sakip_indikatorsasaranrenstra_p-sasaranrenstra_p', 'v2_sakip_indikatorsasaranrenstra_p', 'refsasaranrenstra_p_id');

        // v2_sakip_indikatorsasaranrenstra_triwulan
        $this->createIndex('idx-v2_sakip_indikatorsasaranrenstra_triwulan-periode', 'v2_sakip_indikatorsasaranrenstra_triwulan', 'refperiode_id');
        $this->createIndex('idx-v2_sakip_indikatorsasaranrenstra_triwulan-skpd', 'v2_sakip_indikatorsasaranrenstra_triwulan', 'refskpd_id');
        $this->createIndex('idx-v2_sakip_indikatorsasaranrenstra_triwulan-indikator', 'v2_sakip_indikatorsasaranrenstra_triwulan', 'refindikatorsasaranrenstra_id');

        // v2_sakip_cascadingprogram, v2_sakip_cascadingkegiatan, v2_sakip_cascadingsubkegiatan
        $this->createIndex('idx-v2_sakip_cascadingprogram-periode', 'v2_sakip_cascadingprogram', 'refperiode_id');
        $this->createIndex('idx-v2_sakip_cascadingprogram-skpd', 'v2_sakip_cascadingprogram', 'refskpd_id');
        $this->createIndex('idx-v2_sakip_cascadingprogram-program', 'v2_sakip_cascadingprogram', 'refprogram_id');

        $this->createIndex('idx-v2_sakip_cascadingkegiatan-periode', 'v2_sakip_cascadingkegiatan', 'refperiode_id');
        $this->createIndex('idx-v2_sakip_cascadingkegiatan-skpd', 'v2_sakip_cascadingkegiatan', 'refskpd_id');
        $this->createIndex('idx-v2_sakip_cascadingkegiatan-kegiatan', 'v2_sakip_cascadingkegiatan', 'refkegiatan_id');

        $this->createIndex('idx-v2_sakip_cascadingsubkegiatan-periode', 'v2_sakip_cascadingsubkegiatan', 'refperiode_id');
        $this->createIndex('idx-v2_sakip_cascadingsubkegiatan-skpd', 'v2_sakip_cascadingsubkegiatan', 'refskpd_id');
        $this->createIndex('idx-v2_sakip_cascadingsubkegiatan-subkegiatan', 'v2_sakip_cascadingsubkegiatan', 'refsubkegiatan_id');

        // user
        $this->createIndex('idx-user-skpd', 'user', 'refskpd_id');
        $this->createIndex('idx-user-group', 'user', 'kode_group');

        echo "All indexes created successfully.\n";
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "Dropping indexes...\n";

        $this->dropIndex('idx-v2_sakip_periode-5tahun', 'v2_sakip_periode');
        $this->dropIndex('idx-v2_sakip_visi-5tahun', 'v2_sakip_visi');
        $this->dropIndex('idx-v2_sakip_visi_p-5tahun', 'v2_sakip_visi_p');
        $this->dropIndex('idx-v2_sakip_misi-5tahun', 'v2_sakip_misi');
        $this->dropIndex('idx-v2_sakip_misi-visi', 'v2_sakip_misi');
        $this->dropIndex('idx-v2_sakip_misi_p-5tahun', 'v2_sakip_misi_p');
        $this->dropIndex('idx-v2_sakip_misi_p-visi_p', 'v2_sakip_misi_p');
        $this->dropIndex('idx-v2_sakip_tujuan-5tahun', 'v2_sakip_tujuan');
        $this->dropIndex('idx-v2_sakip_tujuan-visi', 'v2_sakip_tujuan');
        $this->dropIndex('idx-v2_sakip_tujuan-misi', 'v2_sakip_tujuan');
        $this->dropIndex('idx-v2_sakip_tujuan_p-5tahun', 'v2_sakip_tujuan_p');
        $this->dropIndex('idx-v2_sakip_tujuan_p-visi_p', 'v2_sakip_tujuan_p');
        $this->dropIndex('idx-v2_sakip_tujuan_p-misi_p', 'v2_sakip_tujuan_p');
        $this->dropIndex('idx-v2_sakip_sasaran-5tahun', 'v2_sakip_sasaran');
        $this->dropIndex('idx-v2_sakip_sasaran-visi', 'v2_sakip_sasaran');
        $this->dropIndex('idx-v2_sakip_sasaran-misi', 'v2_sakip_sasaran');
        $this->dropIndex('idx-v2_sakip_sasaran-tujuan', 'v2_sakip_sasaran');
        $this->dropIndex('idx-v2_sakip_sasaran_p-5tahun', 'v2_sakip_sasaran_p');
        $this->dropIndex('idx-v2_sakip_sasaran_p-visi_p', 'v2_sakip_sasaran_p');
        $this->dropIndex('idx-v2_sakip_sasaran_p-misi_p', 'v2_sakip_sasaran_p');
        $this->dropIndex('idx-v2_sakip_sasaran_p-tujuan_p', 'v2_sakip_sasaran_p');
        $this->dropIndex('idx-v2_sakip_tujuanrenstra-5tahun', 'v2_sakip_tujuanrenstra');
        $this->dropIndex('idx-v2_sakip_tujuanrenstra-skpd', 'v2_sakip_tujuanrenstra');
        $this->dropIndex('idx-v2_sakip_tujuanrenstra-misi', 'v2_sakip_tujuanrenstra');
        $this->dropIndex('idx-v2_sakip_tujuanrenstra-tujuan', 'v2_sakip_tujuanrenstra');
        $this->dropIndex('idx-v2_sakip_tujuanrenstra-sasaran', 'v2_sakip_tujuanrenstra');
        $this->dropIndex('idx-v2_sakip_tujuanrenstra_p-5tahun', 'v2_sakip_tujuanrenstra_p');
        $this->dropIndex('idx-v2_sakip_tujuanrenstra_p-skpd', 'v2_sakip_tujuanrenstra_p');
        $this->dropIndex('idx-v2_sakip_tujuanrenstra_p-misi_p', 'v2_sakip_tujuanrenstra_p');
        $this->dropIndex('idx-v2_sakip_tujuanrenstra_p-tujuan_p', 'v2_sakip_tujuanrenstra_p');
        $this->dropIndex('idx-v2_sakip_tujuanrenstra_p-sasaran_p', 'v2_sakip_tujuanrenstra_p');
        $this->dropIndex('idx-v2_sakip_sasaranrenstra-5tahun', 'v2_sakip_sasaranrenstra');
        $this->dropIndex('idx-v2_sakip_sasaranrenstra-skpd', 'v2_sakip_sasaranrenstra');
        $this->dropIndex('idx-v2_sakip_sasaranrenstra-sasaran', 'v2_sakip_sasaranrenstra');
        $this->dropIndex('idx-v2_sakip_sasaranrenstra-visi', 'v2_sakip_sasaranrenstra');
        $this->dropIndex('idx-v2_sakip_sasaranrenstra-misi', 'v2_sakip_sasaranrenstra');
        $this->dropIndex('idx-v2_sakip_sasaranrenstra-tujuan', 'v2_sakip_sasaranrenstra');
        $this->dropIndex('idx-v2_sakip_sasaranrenstra-tujuanrenstra', 'v2_sakip_sasaranrenstra');
        $this->dropIndex('idx-v2_sakip_sasaranrenstra_p-5tahun', 'v2_sakip_sasaranrenstra_p');
        $this->dropIndex('idx-v2_sakip_sasaranrenstra_p-skpd', 'v2_sakip_sasaranrenstra_p');
        $this->dropIndex('idx-v2_sakip_sasaranrenstra_p-sasaran_p', 'v2_sakip_sasaranrenstra_p');
        $this->dropIndex('idx-v2_sakip_sasaranrenstra_p-visi_p', 'v2_sakip_sasaranrenstra_p');
        $this->dropIndex('idx-v2_sakip_sasaranrenstra_p-misi_p', 'v2_sakip_sasaranrenstra_p');
        $this->dropIndex('idx-v2_sakip_sasaranrenstra_p-tujuan_p', 'v2_sakip_sasaranrenstra_p');
        $this->dropIndex('idx-v2_sakip_sasaranrenstra_p-tujuanrenstra_p', 'v2_sakip_sasaranrenstra_p');
        $this->dropIndex('idx-v2_sakip_strategi-5tahun', 'v2_sakip_strategi');
        $this->dropIndex('idx-v2_sakip_strategi-skpd', 'v2_sakip_strategi');
        $this->dropIndex('idx-v2_sakip_strategi-misi', 'v2_sakip_strategi');
        $this->dropIndex('idx-v2_sakip_strategi-sasaranrenstra', 'v2_sakip_strategi');
        $this->dropIndex('idx-v2_sakip_strategi-sasaran', 'v2_sakip_strategi');
        $this->dropIndex('idx-v2_sakip_strategi-tujuan', 'v2_sakip_strategi');
        $this->dropIndex('idx-v2_sakip_kebijakan-5tahun', 'v2_sakip_kebijakan');
        $this->dropIndex('idx-v2_sakip_kebijakan-skpd', 'v2_sakip_kebijakan');
        $this->dropIndex('idx-v2_sakip_kebijakan-strategi', 'v2_sakip_kebijakan');
        $this->dropIndex('idx-v2_sakip_kebijakan-misi', 'v2_sakip_kebijakan');
        $this->dropIndex('idx-v2_sakip_kebijakan-sasaranrenstra', 'v2_sakip_kebijakan');
        $this->dropIndex('idx-v2_sakip_kebijakan-sasaran', 'v2_sakip_kebijakan');
        $this->dropIndex('idx-v2_sakip_kebijakan-tujuan', 'v2_sakip_kebijakan');
        $this->dropIndex('idx-v2_sakip_indikatorsasaranrenstra-periode', 'v2_sakip_indikatorsasaranrenstra');
        $this->dropIndex('idx-v2_sakip_indikatorsasaranrenstra-skpd', 'v2_sakip_indikatorsasaranrenstra');
        $this->dropIndex('idx-v2_sakip_indikatorsasaranrenstra-sasaranrenstra', 'v2_sakip_indikatorsasaranrenstra');
        $this->dropIndex('idx-v2_sakip_indikatorsasaranrenstra_p-periode', 'v2_sakip_indikatorsasaranrenstra_p');
        $this->dropIndex('idx-v2_sakip_indikatorsasaranrenstra_p-skpd', 'v2_sakip_indikatorsasaranrenstra_p');
        $this->dropIndex('idx-v2_sakip_indikatorsasaranrenstra_p-sasaranrenstra_p', 'v2_sakip_indikatorsasaranrenstra_p');
        $this->dropIndex('idx-v2_sakip_indikatorsasaranrenstra_triwulan-periode', 'v2_sakip_indikatorsasaranrenstra_triwulan');
        $this->dropIndex('idx-v2_sakip_indikatorsasaranrenstra_triwulan-skpd', 'v2_sakip_indikatorsasaranrenstra_triwulan');
        $this->dropIndex('idx-v2_sakip_indikatorsasaranrenstra_triwulan-indikator', 'v2_sakip_indikatorsasaranrenstra_triwulan');
        $this->dropIndex('idx-v2_sakip_cascadingprogram-periode', 'v2_sakip_cascadingprogram');
        $this->dropIndex('idx-v2_sakip_cascadingprogram-skpd', 'v2_sakip_cascadingprogram');
        $this->dropIndex('idx-v2_sakip_cascadingprogram-program', 'v2_sakip_cascadingprogram');
        $this->dropIndex('idx-v2_sakip_cascadingkegiatan-periode', 'v2_sakip_cascadingkegiatan');
        $this->dropIndex('idx-v2_sakip_cascadingkegiatan-skpd', 'v2_sakip_cascadingkegiatan');
        $this->dropIndex('idx-v2_sakip_cascadingkegiatan-kegiatan', 'v2_sakip_cascadingkegiatan');
        $this->dropIndex('idx-v2_sakip_cascadingsubkegiatan-periode', 'v2_sakip_cascadingsubkegiatan');
        $this->dropIndex('idx-v2_sakip_cascadingsubkegiatan-skpd', 'v2_sakip_cascadingsubkegiatan');
        $this->dropIndex('idx-v2_sakip_cascadingsubkegiatan-subkegiatan', 'v2_sakip_cascadingsubkegiatan');
        $this->dropIndex('idx-user-skpd', 'user');
        $this->dropIndex('idx-user-group', 'user');
    }
}
