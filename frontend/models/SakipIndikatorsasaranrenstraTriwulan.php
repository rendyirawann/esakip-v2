<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "sakip_indikatorsasaranrenstra_triwulan".
 *
 * @property int $refindikatorsasaranrenstratriwulan_id
 * @property int|null $refindikatorsasaranrenstra_id
 * @property int|null $refsasaranrenstra_id
 * @property int|null $refskpd_id
 * @property int|null $refperiode_id
 * @property int|null $reftriwulan_id
 * @property string|null $triwulan_target_rkt
 * @property string|null $triwulan_target_rkt_p
 * @property string|null $triwulan_target_pk
 * @property string|null $triwulan_target_pk_p
 * @property string|null $triwulan_realisasi
 * @property string|null $triwulan_capaian
 * @property string|null $triwulan_keterangan
 * @property string|null $triwulan_analisis
 */
class SakipIndikatorsasaranrenstraTriwulan extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'v2_sakip_indikatorsasaranrenstra_triwulan';
    }



    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['refindikatorsasaranrenstra_id', 'refsasaranrenstra_id', 'refskpd_id', 'refperiode_id', 'reftriwulan_id'], 'integer'],
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
            'refindikatorsasaranrenstratriwulan_id' => 'Refindikatorsasaranrenstratriwulan ID',
            'refindikatorsasaranrenstra_id' => 'Refindikatorsasaranrenstra ID',
            'refsasaranrenstra_id' => 'Refsasaranrenstra ID',
            'refskpd_id' => 'Refskpd ID',
            'refperiode_id' => 'Refperiode ID',
            'reftriwulan_id' => 'Reftriwulan ID',
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

    public function getRefIndikatorsasaranrenstra()
    {
        return $this->hasOne(SakipIndikatorsasaranrenstra::class, ['refindikatorsasaranrenstra_id' => 'refindikatorsasaranrenstra_id']);
    }
}
