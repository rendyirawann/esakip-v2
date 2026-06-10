<?php

namespace frontend\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * Model untuk tabel sakip_evaluasi_renja
 * Menyimpan data Evaluasi Terhadap Hasil RKPD (Renja) yang diimport dari Excel
 *
 * @property int $id
 * @property int|null $refskpd_id
 * @property int $tahun
 * @property int|null $no_urut
 * @property int $row_order
 * @property string|null $level
 * @property string|null $nama_unsur
 * @property string|null $nama_bidang_urusan
 * @property string|null $nama_program
 * @property string|null $nama_kegiatan
 * @property string|null $nama_sub_kegiatan
 * @property string|null $visi
 * @property string|null $misi
 * @property string|null $tujuan
 * @property string|null $sasaran_strategis
 * @property string|null $sasaran_opd
 * @property string|null $indikator_kinerja
 * @property string|null $satuan
 * @property float|null $target_renstra_kinerja
 * @property float|null $target_renstra_anggaran
 * @property float|null $realisasi_sd_lalu_kinerja
 * @property float|null $realisasi_sd_lalu_anggaran
 * @property float|null $target_renja_kinerja
 * @property float|null $target_renja_anggaran
 * @property float|null $tw1_kinerja
 * @property float|null $tw1_persen
 * @property float|null $tw1_anggaran
 * @property float|null $tw2_kinerja
 * @property float|null $tw2_persen
 * @property float|null $tw2_anggaran
 * @property float|null $tw3_kinerja
 * @property float|null $tw3_persen
 * @property float|null $tw3_anggaran
 * @property float|null $tw4_kinerja
 * @property float|null $tw4_persen
 * @property float|null $tw4_anggaran
 * @property float|null $realisasi_capaian_kinerja
 * @property float|null $realisasi_capaian_anggaran
 * @property float|null $realisasi_renstra_kinerja
 * @property float|null $realisasi_renstra_anggaran
 * @property float|null $tingkat_capaian_kinerja
 * @property float|null $tingkat_capaian_anggaran
 * @property string|null $created_at
 * @property string|null $updated_at
 */
class SakipEvaluasiRenja extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%sakip_evaluasi_renja}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tahun'], 'required'],
            [['tahun', 'no_urut', 'row_order', 'refskpd_id'], 'integer'],
            [['visi', 'misi', 'tujuan', 'sasaran_strategis', 'sasaran_opd', 'indikator_kinerja'], 'string'],
            [['nama_unsur', 'nama_bidang_urusan', 'nama_program', 'nama_kegiatan', 'nama_sub_kegiatan'], 'string'],
            [['satuan'], 'string', 'max' => 100],
            [['level'], 'in', 'range' => ['unsur', 'bidang_urusan', 'program', 'kegiatan', 'sub_kegiatan']],
            [
                [
                    'target_renstra_kinerja', 'target_renstra_anggaran',
                    'realisasi_sd_lalu_kinerja', 'realisasi_sd_lalu_anggaran',
                    'target_renja_kinerja', 'target_renja_anggaran',
                    'tw1_kinerja', 'tw1_persen', 'tw1_anggaran',
                    'tw2_kinerja', 'tw2_persen', 'tw2_anggaran',
                    'tw3_kinerja', 'tw3_persen', 'tw3_anggaran',
                    'tw4_kinerja', 'tw4_persen', 'tw4_anggaran',
                    'realisasi_capaian_kinerja', 'realisasi_capaian_anggaran',
                    'realisasi_renstra_kinerja', 'realisasi_renstra_anggaran',
                    'tingkat_capaian_kinerja', 'tingkat_capaian_anggaran',
                ],
                'number'
            ],
            // FK constraint ke sakip_skpd dijaga di level database
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'refskpd_id' => 'SKPD',
            'tahun' => 'Tahun',
            'no_urut' => 'No',
            'row_order' => 'Urutan Baris',
            'level' => 'Level',
            'nama_unsur' => 'Unsur',
            'nama_bidang_urusan' => 'Bidang Urusan',
            'nama_program' => 'Program',
            'nama_kegiatan' => 'Kegiatan',
            'nama_sub_kegiatan' => 'Sub Kegiatan',
            'visi' => 'Visi',
            'misi' => 'Misi',
            'tujuan' => 'Tujuan',
            'sasaran_strategis' => 'Sasaran Strategis',
            'sasaran_opd' => 'Sasaran Perangkat Daerah',
            'indikator_kinerja' => 'Indikator Kinerja',
            'satuan' => 'Satuan',
            'target_renstra_kinerja' => 'Target Renstra (K)',
            'target_renstra_anggaran' => 'Target Renstra (Rp)',
            'realisasi_sd_lalu_kinerja' => 'Realisasi s/d Tahun Lalu (K)',
            'realisasi_sd_lalu_anggaran' => 'Realisasi s/d Tahun Lalu (Rp)',
            'target_renja_kinerja' => 'Target Renja (K)',
            'target_renja_anggaran' => 'Target Renja (Rp)',
            'tw1_kinerja' => 'TW I (K)',
            'tw1_persen' => 'TW I (%)',
            'tw1_anggaran' => 'TW I (Rp)',
            'tw2_kinerja' => 'TW II (K)',
            'tw2_persen' => 'TW II (%)',
            'tw2_anggaran' => 'TW II (Rp)',
            'tw3_kinerja' => 'TW III (K)',
            'tw3_persen' => 'TW III (%)',
            'tw3_anggaran' => 'TW III (Rp)',
            'tw4_kinerja' => 'TW IV (K)',
            'tw4_persen' => 'TW IV (%)',
            'tw4_anggaran' => 'TW IV (Rp)',
            'realisasi_capaian_kinerja' => 'Realisasi Capaian (K)',
            'realisasi_capaian_anggaran' => 'Realisasi Capaian (Rp)',
            'realisasi_renstra_kinerja' => 'Realisasi Renstra (K)',
            'realisasi_renstra_anggaran' => 'Realisasi Renstra (Rp)',
            'tingkat_capaian_kinerja' => 'Tingkat Capaian K (%)',
            'tingkat_capaian_anggaran' => 'Tingkat Capaian Rp (%)',
            'created_at' => 'Dibuat',
            'updated_at' => 'Diperbarui',
        ];
    }

    /**
     * Relasi ke tabel SKPD
     */
    public function getSkpd()
    {
        return $this->hasOne(SakipSkpd::class, ['refskpd_id' => 'refskpd_id']);
    }

    /**
     * Ambil nama yang sesuai level baris ini
     */
    public function getNamaByLevel()
    {
        switch ($this->level) {
            case 'unsur': return $this->nama_unsur;
            case 'bidang_urusan': return $this->nama_bidang_urusan;
            case 'program': return $this->nama_program;
            case 'kegiatan': return $this->nama_kegiatan;
            case 'sub_kegiatan': return $this->nama_sub_kegiatan;
            default: return $this->nama_sub_kegiatan;
        }
    }

    /**
     * Format angka anggaran ke format Rupiah
     */
    public static function formatRupiah($value)
    {
        if ($value === null || $value === '') return '-';
        return 'Rp ' . number_format((float)$value, 0, ',', '.');
    }

    /**
     * Format angka kinerja
     */
    public static function formatKinerja($value)
    {
        if ($value === null || $value === '') return '-';
        $floatVal = (float)$value;
        // Jika bilangan bulat, tampilkan tanpa desimal
        if ($floatVal == floor($floatVal)) {
            return number_format($floatVal, 0, ',', '.');
        }
        return number_format($floatVal, 2, ',', '.');
    }

    /**
     * Format persen
     */
    public static function formatPersen($value)
    {
        if ($value === null || $value === '') return '-';
        return number_format((float)$value, 2, ',', '.') . '%';
    }
}
