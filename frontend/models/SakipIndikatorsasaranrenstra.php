<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "sakip_indikatorsasaranrenstra".
 *
 * @property int $refindikatorsasaranrenstra_id
 * @property string|null $uraian_indikatorsasaranrenstra
 * @property int|null $refsasaranrenstra_id
 * @property int|null $refskpd_id
 * @property int|null $refperiode_id
 * @property string|null $satuan_target
 * @property string|null $target
 * @property string|null $target_rkt_p
 * @property string|null $target_pk
 * @property string|null $target_pk_p
 * @property string|null $realisasi
 * @property string|null $capaian
 * @property string|null $keterangan
 * @property string|null $indikatorsasaranrenstra_isaktif
 * @property string|null $iku_isaktif
 * @property string|null $pk_isaktif
 */
class SakipIndikatorsasaranrenstra extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'v2_sakip_indikatorsasaranrenstra';
    }



    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['uraian_indikatorsasaranrenstra'], 'required', 'message' => 'Indikator sasaran renstra tidak boleh kosong.'],
            // [['refsasaranrenstra_id'], 'required', 'message' => 'Sarsaranrenstra tidak boleh kosong.'],
            [['uraian_indikatorsasaranrenstra', 'keterangan', 'keterangan_pk', 'keterangan_pk_p'], 'string'],
            [['refsasaranrenstra_id', 'refskpd_id', 'refperiode_id'], 'integer'],
            [['indikatorsasaranrenstra_target', 'target_rkt', 'target_rkt_p', 'target_pk', 'target_pk_p', 'realisasi'], 'match', 'pattern' => '/^\d+(\.\d{1,6})?$/', 'message' => 'Hanya boleh diisi dengan angka atau angka desimal.'],
            [['indikatorsasaranrenstra_satuan', 'indikatorsasaranrenstra_target', 'target_rkt', 'target_rkt_p', 'target_pk', 'target_pk_p', 'realisasi', 'capaian', 'analisis', 'indikatorsasaranrenstra_isaktif', 'iku_isaktif', 'pk_isaktif', 'alasan_sasaranrenstra', 'formulasi_sasaranrenstra', 'kriteria_sasaranrenstra'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'refindikatorsasaranrenstra_id' => 'Refindikatorsasaranrenstra ID',
            'uraian_indikatorsasaranrenstra' => 'Uraian Indikatorsasaranrenstra',
            'refsasaranrenstra_id' => 'Refsasaranrenstra ID',
            'refskpd_id' => 'Refskpd ID',
            'refperiode_id' => 'Refperiode ID',
            'indikatorsasaranrenstra_satuan' => 'Satuan Indikator Sasaran Renstra',
            'indikatorsasaranrenstra_target' => 'Target Indikator Sasaran Renstra',
            'target_rkt' => 'Target RKT',
            'target_rkt_p' => 'Target Rkt P',
            'target_pk' => 'Target Pk',
            'target_pk_p' => 'Target Pk P',
            'realisasi' => 'Realisasi',
            'capaian' => 'Capaian',
            'analisis' => 'Analisis',
            'keterangan' => 'Keterangan',
            'keterangan_pk' => 'Keterangan PK',
            'keterangan_pk_p' => 'Keterangan PKP',
            'indikatorsasaranrenstra_isaktif' => 'Indikatorsasaranrenstra Isaktif',
            'iku_isaktif' => 'Iku Isaktif',
            'pk_isaktif' => 'Pk Isaktif',
            'alasan_sasaranrenstra' => 'Alasan Sasaranrenstra',
            'formulasi_sasaranrenstra' => 'Formulasi Sasaranrenstra',
            'kriteria_sasaranrenstra' => 'Kriteria Sasaranrenstra',
        ];
    }

    // public function beforeSave($insert)
    // {
    //     if (parent::beforeSave($insert)) {
    //         // Konversi koma ke titik sebelum menyimpan
    //         $this->indikatorsasaranrenstra_target = str_replace(',', '.', $this->indikatorsasaranrenstra_target);
    //         $this->target_rkt = str_replace(',', '.', $this->indikatorsasaranrenstra_target);

    //         return true;
    //     }
    //     return false;
    // }


    public function getRefPeriode()
    {
        return $this->hasOne(SakipPeriode::class, ['refperiode_id' => 'refperiode_id']);
    }

    public function getRefSasaranrenstra()
    {
        return $this->hasOne(SakipSasaranrenstra::class, ['refsasaranrenstra_id' => 'refsasaranrenstra_id']);
    }

    public function getRefIndikatorsasaranrenstraTriwulan()
    {
        return $this->hasMany(SakipIndikatorsasaranrenstraTriwulan::class, ['refsasaranrenstra_id' => 'refsasaranrenstra_id']);
    }

    public function getIndikatorsasaranrenstraTriwulan()
    {
        return $this->hasMany(SakipIndikatorsasaranrenstraTriwulan::class, ['refsasaranrenstra_id' => 'refsasaranrenstra_id']);
    }

    public function getTriwulan()
    {
        return $this->hasMany(SakipIndikatorsasaranrenstraTriwulan::class, ['refsasaranrenstra_id' => 'refsasaranrenstra_id']);
    }

    // In frontend/models/SakipIndikatorsasaranrenstra.php
    public function getCascadingPrograms()
    {
        return $this->hasMany(SakipCascadingprogram::class, ['refindikatorsasaranrenstra_id' => 'refindikatorsasaranrenstra_id']);
    }

    // In frontend/models/SakipIndikatorsasaranrenstra.php
    public function getCascadingKegiatans()
    {
        return $this->hasMany(SakipCascadingkegiatan::class, ['refindikatorsasaranrenstra_id' => 'refindikatorsasaranrenstra_id']);
    }

    public function getCascadingSubkegiatans()
    {
        return $this->hasMany(SakipCascadingsubkegiatan::class, ['refindikatorsasaranrenstra_id' => 'refindikatorsasaranrenstra_id']);
    }
}
