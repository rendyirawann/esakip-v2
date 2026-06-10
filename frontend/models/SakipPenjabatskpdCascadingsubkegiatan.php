<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "sakip_penjabatskpd_cascadingsubkegiatan".
 *
 * @property int $refpenjabatcascadingsubkegiatan_id
 * @property int|null $refpenjabatskpd_id
 * @property int|null $refeselon_id
 * @property int|null $refcascadingprogram_id
 * @property int|null $refcascadingkegiatan_id
 * @property int|null $refcascadingsubkegiatan_id
 * @property int|null $refindikatorsubkegiatan_id
 * @property int|null $refskpd_id
 * @property int|null $refperiode_id
 * @property int|null $refsasaranrenstra_id
 * @property int|null $refindikatorsasaranrenstra_id
 * @property int|null $refprogram_id
 * @property int|null $refkegiatan_id
 * @property int|null $refsubkegiatan_id
 * @property string|null $uraian_sasaransubkegiatan
 * @property string|null $uraian_indikatorsubkegiatan
 * @property string|null $subkegiatan_target
 * @property string|null $subkegiatan_satuan
 * @property string|null $target_rkt
 * @property string|null $target_rkt_p
 * @property string|null $target_pk
 * @property string|null $target_pk_p
 * @property string|null $realisasi
 * @property string|null $capaian
 * @property string|null $keterangan
 * @property string|null $analisis
 */
class SakipPenjabatskpdCascadingsubkegiatan extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'v2_sakip_penjabatskpd_cascadingsubkegiatan';
    }


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['refpenjabatskpd_id', 'refeselon_id', 'refcascadingprogram_id', 'refcascadingkegiatan_id', 'refcascadingsubkegiatan_id', 'refindikatorsubkegiatan_id', 'refskpd_id', 'refperiode_id', 'refsasaranrenstra_id', 'refindikatorsasaranrenstra_id', 'refprogram_id', 'refkegiatan_id', 'refsubkegiatan_id'], 'integer'],
            [['uraian_sasaransubkegiatan', 'uraian_indikatorsubkegiatan', 'keterangan', 'analisis'], 'string'],
            [['subkegiatan_target', 'subkegiatan_satuan', 'target_rkt', 'target_rkt_p', 'target_pk', 'target_pk_p', 'realisasi', 'capaian'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'refpenjabatcascadingsubkegiatan_id' => 'Refpenjabatcascadingsubkegiatan ID',
            'refpenjabatskpd_id' => 'Refpenjabatskpd ID',
            'refeselon_id' => 'Refeselon ID',
            'refcascadingprogram_id' => 'Refcascadingprogram ID',
            'refcascadingkegiatan_id' => 'Refcascadingkegiatan ID',
            'refcascadingsubkegiatan_id' => 'Refcascadingsubkegiatan ID',
            'refindikatorsubkegiatan_id' => 'Refindikatorsubkegiatan ID',
            'refskpd_id' => 'Refskpd ID',
            'refperiode_id' => 'Refperiode ID',
            'refsasaranrenstra_id' => 'Refsasaranrenstra ID',
            'refindikatorsasaranrenstra_id' => 'Refindikatorsasaranrenstra ID',
            'refprogram_id' => 'Refprogram ID',
            'refkegiatan_id' => 'Refkegiatan ID',
            'refsubkegiatan_id' => 'Refsubkegiatan ID',
            'uraian_sasaransubkegiatan' => 'Uraian Sasaransubkegiatan',
            'uraian_indikatorsubkegiatan' => 'Uraian Indikatorsubkegiatan',
            'subkegiatan_target' => 'Subkegiatan Target',
            'subkegiatan_satuan' => 'Subkegiatan Satuan',
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

    public function getRefCascadingprogram()
    {
        return $this->hasOne(SakipCascadingprogram::class, ['refcascadingprogram_id' => 'refcascadingprogram_id']);
    }

    public function getRefCascadingkegiatan()
    {
        return $this->hasOne(SakipCascadingkegiatan::class, ['refcascadingkegiatan_id' => 'refcascadingkegiatan_id']);
    }

    public function getRefSasaranrenstra()
    {
        return $this->hasOne(SakipSasaranrenstra::class, ['refsasaranrenstra_id' => 'refsasaranrenstra_id']);
    }

    public function getRefIndikatorsasaranrenstra()
    {
        return $this->hasOne(SakipIndikatorsasaranrenstra::class, ['refindikatorsasaranrenstra_id' => 'refindikatorsasaranrenstra_id']);
    }

    public function getRefProgram()
    {
        return $this->hasOne(SakipProgram::class, ['refprogram_id' => 'refprogram_id']);
    }

    public function getRefKegiatan()
    {
        return $this->hasOne(SakipKegiatan::class, ['refkegiatan_id' => 'refkegiatan_id']);
    }

    public function getRefSubkegiatan()
    {
        return $this->hasOne(SakipSubkegiatan::class, ['refsubkegiatan_id' => 'refsubkegiatan_id']);
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
