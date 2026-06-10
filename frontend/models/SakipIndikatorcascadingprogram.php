<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "sakip_indikatorcascadingprogram".
 *
 * @property int $refindikatorprogram_id
 * @property int|null $refcascadingprogram_id
 * @property int|null $refsasaranrenstra_id
 * @property int|null $refskpd_id
 * @property int|null $refperiode_id
 * @property int|null $refbidang_id
 * @property int|null $refprogram_id
 * @property string|null $target_rkt
 * @property string|null $target_rkt_p
 * @property string|null $target_pk
 * @property string|null $target_pk_p
 * @property string|null $realisasi
 * @property string|null $capaian
 * @property string|null $keterangan
 * @property string|null $analisis
 */
class SakipIndikatorcascadingprogram extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'v2_sakip_indikatorcascadingprogram';
    }



    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['refcascadingprogram_id', 'refsasaranrenstra_id', 'refindikatorsasaranrenstra_id', 'refskpd_id', 'refperiode_id', 'refbidang_id', 'refprogram_id'], 'integer'],
            [['keterangan', 'analisis', 'keterangan_pk', 'keterangan_pk_p'], 'string'],
            [['target_rkt', 'target_rkt_p', 'target_pk', 'target_pk_p', 'realisasi', 'capaian'], 'string', 'max' => 30],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'refindikatorprogram_id' => 'Refindikatorprogram ID',
            'refcascadingprogram_id' => 'Refcascadingprogram ID',
            'refsasaranrenstra_id' => 'Refsasaranrenstra ID',
            'refindikatorsasaranrenstra_id' => 'refindikatorsasaranrenstra ID',
            'refskpd_id' => 'Refskpd ID',
            'refperiode_id' => 'Refperiode ID',
            'refbidang_id' => 'Refbidang ID',
            'refprogram_id' => 'Refprogram ID',
            'target_rkt' => 'Target Rkt',
            'target_rkt_p' => 'Target Rkt P',
            'target_pk' => 'Target Pk',
            'target_pk_p' => 'Target Pk P',
            'realisasi' => 'Realisasi',
            'capaian' => 'Capaian',
            'keterangan' => 'Keterangan',
            'keterangan_pk' => 'Keterangan PK',
            'keterangan_pk_p' => 'Keterangan PKP',
            'analisis' => 'Analisis',
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

    public function getRefIndikatorCascadingProgramTriwulan()
    {
        return $this->hasMany(SakipIndikatorcascadingprogramTriwulan::class, ['refindikatorprogram_id' => 'refindikatorprogram_id']);
    }
}
