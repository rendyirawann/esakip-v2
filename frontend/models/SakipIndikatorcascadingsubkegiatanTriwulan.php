<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "sakip_indikatorcascadingsubkegiatan_triwulan".
 *
 * @property int $refindikatorsubkegiatantriwulan_id
 * @property int|null $refindikatorsubkegiatan_id
 * @property int|null $refcascadingprogram_id
 * @property int|null $refcascadingkegiatan_id
 * @property int|null $refcascadingsubkegiatan_id
 * @property int|null $refsasaranrenstra_id
 * @property int|null $refskpd_id
 * @property int|null $refperiode_id
 * @property int|null $refprogram_id
 * @property int|null $refkegiatan_id
 * @property int|null $refsubkegiatan_id
 * @property string|null $triwulan_target_rkt
 * @property string|null $triwulan_target_rkt_p
 * @property string|null $triwulan_target_pk
 * @property string|null $triwulan_target_pk_p
 * @property string|null $triwulan_realisasi
 * @property string|null $triwulan_capaian
 * @property string|null $triwulan_keterangan
 * @property string|null $triwulan_analisis
 * @property string|null $triwulan_penyerapan_anggaran
 */
class SakipIndikatorcascadingsubkegiatanTriwulan extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'v2_sakip_indikatorcascadingsubkegiatan_triwulan';
    }


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['refindikatorsubkegiatan_id', 'reftriwulan_id', 'refcascadingprogram_id', 'refcascadingkegiatan_id', 'refcascadingsubkegiatan_id', 'refsasaranrenstra_id', 'refindikatorsasaranrenstra_id', 'refskpd_id', 'refperiode_id', 'refprogram_id', 'refkegiatan_id', 'refsubkegiatan_id'], 'integer'],
            [['triwulan_keterangan', 'triwulan_keterangan_pk_p', 'triwulan_analisis'], 'string'],
            [['triwulan_target_rkt', 'triwulan_target_rkt_p', 'triwulan_target_pk', 'triwulan_target_pk_p', 'triwulan_realisasi', 'triwulan_capaian', 'triwulan_penyerapan_anggaran'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'refindikatorsubkegiatantriwulan_id' => 'Refindikatorsubkegiatantriwulan ID',
            'refindikatorsubkegiatan_id' => 'Refindikatorsubkegiatan ID',
            'refcascadingprogram_id' => 'Refcascadingrestraprogram ID',
            'refcascadingkegiatan_id' => 'Refcascadingrestrakegiatan ID',
            'refcascadingsubkegiatan_id' => 'Refcascadingrestrasubkegiatan ID',
            'refsasaranrenstra_id' => 'Refsasaranrenstra ID',
            'refindikatorsasaranrenstra_id' => 'refindikatorsasaranrenstra ID',
            'refskpd_id' => 'Refskpd ID',
            'refperiode_id' => 'Refperiode ID',
            'reftriwulan_id' => 'Reftriwulan ID',
            'refprogram_id' => 'Refprogram ID',
            'refkegiatan_id' => 'Refkegiatan ID',
            'refsubkegiatan_id' => 'Refsubkegiatan ID',
            'triwulan_target_rkt' => 'Triwulan Target Rkt',
            'triwulan_target_rkt_p' => 'Triwulan Target Rkt P',
            'triwulan_target_pk' => 'Triwulan Target Pk',
            'triwulan_target_pk_p' => 'Triwulan Target Pk P',
            'triwulan_realisasi' => 'Triwulan Realisasi',
            'triwulan_capaian' => 'Triwulan Capaian',
            'triwulan_keterangan' => 'Triwulan Keterangan',
            'triwulan_keterangan_pk_p' => 'Triwulan Keterangan PK Perubahan',
            'triwulan_analisis' => 'Triwulan Analisis',
            'triwulan_penyerapan_anggaran' => 'Triwulan Penyerapan Anggaran',
        ];
    }

    public function getRefPeriode()
    {
        return $this->hasOne(SakipPeriode::class, ['refperiode_id' => 'refperiode_id']);
    }

    public function getRefSasaranrenstra()
    {
        return $this->hasOne(SakipSasaranrenstra::class, ['refsasaranrenstra_id' => 'refsasaranrenstra_id']);
    }

    public function getRefSubkegiatan()
    {
        return $this->hasOne(SakipSubkegiatan::class, ['refsubkegiatan_id' => 'refsubkegiatan_id']);
    }

    public function getRefKegiatan()
    {
        return $this->hasOne(SakipKegiatan::class, ['refkegiatan_id' => 'refkegiatan_id']);
    }

    public function getRefProgram()
    {
        return $this->hasOne(SakipProgram::class, ['refprogram_id' => 'refprogram_id']);
    }

    public function getRefBidang()
    {
        return $this->hasOne(SakipBidang::class, ['refbidang_id' => 'refbidang_id']);
    }

    public function getRefCascadingProgram()
    {
        return $this->hasOne(SakipCascadingprogram::class, ['refcascadingprogram_id' => 'refcascadingprogram_id']);
    }

    public function getRefCascadingKegiatan()
    {
        return $this->hasOne(SakipCascadingkegiatan::class, ['refcascadingkegiatan_id' => 'refcascadingkegiatan_id']);
    }

    public function getRefCascadingSubkegiatan()
    {
        return $this->hasOne(SakipCascadingsubkegiatan::class, ['refcascadingsubkegiatan_id' => 'refcascadingsubkegiatan_id']);
    }

    public function getRefIndikatorCascadingSubkegiatan()
    {
        return $this->hasOne(SakipIndikatorcascadingsubkegiatan::class, ['refindikatorsubkegiatan_id' => 'refindikatorsubkegiatan_id']);
    }
}
