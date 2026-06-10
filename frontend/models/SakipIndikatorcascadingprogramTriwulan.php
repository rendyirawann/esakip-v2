<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "sakip_indikatorcascadingprogram_triwulan".
 *
 * @property int $refindikatorprogramtriwulan_id
 * @property int|null $refindikatorprogram_id
 * @property int|null $refcascadingprogram_id
 * @property int|null $refsasaranrenstra_id
 * @property int|null $refskpd_id
 * @property int|null $refperiode_id
 * @property int|null $reftriwulan_id
 * @property int|null $refbidang_id
 * @property int|null $refprogram_id
 * @property string|null $triwulan_target_rkt
 * @property string|null $triwulan_target_rkt_p
 * @property string|null $triwulan_target_pk
 * @property string|null $triwulan_target_pk_p
 * @property string|null $triwulan_realisasi
 * @property string|null $triwulan_capaian
 * @property string|null $triwulan_keterangan
 * @property string|null $triwulan_analisis
 */
class SakipIndikatorcascadingprogramTriwulan extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'v2_sakip_indikatorcascadingprogram_triwulan';
    }



    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['refindikatorprogram_id', 'refcascadingprogram_id', 'refsasaranrenstra_id', 'refindikatorsasaranrenstra_id', 'refskpd_id', 'refperiode_id', 'reftriwulan_id', 'refbidang_id', 'refprogram_id'], 'integer'],
            [['triwulan_keterangan', 'triwulan_keterangan_pk_p', 'triwulan_analisis'], 'string'],
            [['triwulan_target_rkt', 'triwulan_target_rkt_p', 'triwulan_target_pk', 'triwulan_target_pk_p', 'triwulan_realisasi', 'triwulan_capaian'], 'string', 'max' => 30],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'refindikatorprogramtriwulan_id' => 'Refindikatorprogramtriwulan ID',
            'refindikatorprogram_id' => 'Refindikatorprogram ID',
            'refcascadingprogram_id' => 'Refcascadingprogram ID',
            'refsasaranrenstra_id' => 'Refsasaranrenstra ID',
            'refindikatorsasaranrenstra_id' => 'refindikatorsasaranrenstra ID',
            'refskpd_id' => 'Refskpd ID',
            'refperiode_id' => 'Refperiode ID',
            'reftriwulan_id' => 'Reftriwulan ID',
            'refbidang_id' => 'Refbidang ID',
            'refprogram_id' => 'Refprogram ID',
            'triwulan_target_rkt' => 'Triwulan Target Rkt',
            'triwulan_target_rkt_p' => 'Triwulan Target Rkt P',
            'triwulan_target_pk' => 'Triwulan Target Pk',
            'triwulan_target_pk_p' => 'Triwulan Target Pk P',
            'triwulan_realisasi' => 'Triwulan Realisasi',
            'triwulan_capaian' => 'Triwulan Capaian',
            'triwulan_keterangan' => 'Triwulan Keterangan',
            'triwulan_keterangan_pk_p' => 'Triwulan Keterangan PK Perubahan',
            'triwulan_analisis' => 'Triwulan Analisis',
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

    public function getRefIndikatorCascadingProgram()
    {
        return $this->hasOne(SakipIndikatorcascadingprogram::class, ['refindikatorprogram_id' => 'refindikatorprogram_id']);
    }
}
