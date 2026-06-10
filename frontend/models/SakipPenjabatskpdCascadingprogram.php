<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "sakip_penjabatskpd_cascadingprogram".
 *
 * @property int $refpenjabatcascadingprogram_id
 * @property int|null $refpenjabatskpd_id
 * @property int|null $refeselon_id
 * @property int|null $refcascadingprogram_id
 * @property int|null $refindikatorprogram_id
 * @property int|null $refskpd_id
 * @property int|null $refperiode_id
 * @property int|null $refsasaranrenstra_id
 * @property int|null $refindikatorsasaranrenstra_id
 * @property int|null $refbidang_id
 * @property int|null $refprogram_id
 * @property string|null $uraian_sasaranprogram
 * @property string|null $uraian_indikatorprogram
 * @property string|null $program_target
 * @property string|null $program_satuan
 * @property string|null $target_rkt
 * @property string|null $target_rkt_p
 * @property string|null $target_pk
 * @property string|null $target_pk_p
 * @property string|null $realisasi
 * @property string|null $capaian
 * @property string|null $keterangan
 * @property string|null $analisis
 */
class SakipPenjabatskpdCascadingprogram extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'v2_sakip_penjabatskpd_cascadingprogram';
    }



    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['refpenjabatskpd_id', 'refeselon_id', 'refcascadingprogram_id', 'refindikatorprogram_id', 'refskpd_id', 'refperiode_id', 'refsasaranrenstra_id', 'refindikatorsasaranrenstra_id', 'refbidang_id', 'refprogram_id'], 'integer'],
            [['uraian_sasaranprogram', 'uraian_indikatorprogram', 'keterangan', 'analisis'], 'string'],
            [['program_target', 'program_satuan', 'target_rkt', 'target_rkt_p', 'target_pk', 'target_pk_p', 'realisasi', 'capaian'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'refpenjabatcascadingprogram_id' => 'Refpenjabatcascadingprogram ID',
            'refpenjabatskpd_id' => 'Refpenjabatskpd ID',
            'refeselon_id' => 'Refeselon ID',
            'refcascadingprogram_id' => 'Refcascadingprogram ID',
            'refindikatorprogram_id' => 'Refindikatorprogram ID',
            'refskpd_id' => 'Refskpd ID',
            'refperiode_id' => 'Refperiode ID',
            'refsasaranrenstra_id' => 'Refsasaranrenstra ID',
            'refindikatorsasaranrenstra_id' => 'Refindikatorsasaranrenstra ID',
            'refbidang_id' => 'Refbidang ID',
            'refprogram_id' => 'Refprogram ID',
            'uraian_sasaranprogram' => 'Uraian Sasaranprogram',
            'uraian_indikatorprogram' => 'Uraian Indikatorprogram',
            'program_target' => 'Program Target',
            'program_satuan' => 'Program Satuan',
            'target_rkt' => 'Target Rkt',
            'target_rkt_p' => 'Target Rkt P',
            'target_pk' => 'Target Pk',
            'target_pk_p' => 'Target Pk P',
            'realisasi' => 'Realisasi',
            'capaian' => 'Capaian',
            'keterangan' => 'Keterangan',
            'analisis' => 'Analisis',
        ];
    }

    public function getRefSasaranrenstra()
    {
        return $this->hasOne(SakipSasaranrenstra::class, ['refsasaranrenstra_id' => 'refsasaranrenstra_id']);
    }

    public function getRefIndikatorsasaranrenstra()
    {
        return $this->hasOne(SakipIndikatorsasaranrenstra::class, ['refindikatorsasaranrenstra_id' => 'refindikatorsasaranrenstra_id']);
    }

    public function getRefBidang()
    {
        return $this->hasOne(SakipBidang::class, ['refbidang_id' => 'refbidang_id']);
    }

    public function getRefProgram()
    {
        return $this->hasOne(SakipProgram::class, ['refprogram_id' => 'refprogram_id']);
    }

    public function getRefPeriode()
    {
        return $this->hasOne(SakipPeriode::class, ['refperiode_id' => 'refperiode_id']);
    }

    public function getRefPenjabatskpd()
    {
        return $this->hasOne(SakipPenjabatSkpd::class, ['refpenjabatskpd_id' => 'refpenjabatskpd_id']);
    }

    public function getRefEselon()
    {
        return $this->hasOne(SakipEselon::class, ['refeselon_id' => 'refeselon_id']);
    }
}
