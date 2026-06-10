<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "sakip_indikatorcascadingsubkegiatan".
 *
 * @property int $refindikatorsubkegiatan_id
 * @property int|null $refcascadingprogram_id
 * @property int|null $refcascadingkegiatan_id
 * @property int|null $refcascadingsubkegiatan_id
 * @property int|null $refsasaranrenstra_id
 * @property int|null $refskpd_id
 * @property int|null $refperiode_id
 * @property int|null $refprogram_id
 * @property int|null $refkegiatan_id
 * @property int|null $refsubkegiatan_id
 * @property string|null $target_rkt
 * @property string|null $anggaran_rkt
 * @property string|null $target_rkt_p
 * @property string|null $anggaran_rkt_p
 * @property string|null $target_pk
 * @property string|null $anggaran_pk
 * @property string|null $target_pk_p
 * @property string|null $anggaran_pk_p
 * @property string|null $realisasi
 * @property string|null $capaian
 * @property string|null $keterangan
 * @property string|null $analisis
 */
class SakipIndikatorcascadingsubkegiatan extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'v2_sakip_indikatorcascadingsubkegiatan';
    }


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['refcascadingprogram_id', 'refcascadingkegiatan_id', 'refcascadingsubkegiatan_id', 'refsasaranrenstra_id', 'refindikatorsasaranrenstra_id', 'refskpd_id', 'refperiode_id', 'refprogram_id', 'refkegiatan_id', 'refsubkegiatan_id'], 'integer'],
            [['keterangan', 'analisis', 'keterangan_pk', 'keterangan_pk_p'], 'string'],
            [['target_rkt', 'anggaran_rkt', 'target_rkt_p', 'anggaran_rkt_p', 'target_pk', 'anggaran_pk', 'target_pk_p', 'anggaran_pk_p', 'realisasi', 'capaian'], 'string', 'max' => 40],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'refindikatorsubkegiatan_id' => 'Refindikatorsubkegiatan ID',
            'refcascadingprogram_id' => 'Refcascadingrenstraprogram ID',
            'refcascadingkegiatan_id' => 'Refcascadingrenstrakegiatan ID',
            'refcascadingsubkegiatan_id' => 'Refcascadingrenstrasubkegiatan ID',
            'refsasaranrenstra_id' => 'Refsasaranrenstra ID',
            'refindikatorsasaranrenstra_id' => 'refindikatorsasaranrenstra ID',
            'refskpd_id' => 'Refskpd ID',
            'refperiode_id' => 'Refperiode ID',
            'refprogram_id' => 'Refprogram ID',
            'refkegiatan_id' => 'Refkegiatan ID',
            'refsubkegiatan_id' => 'Refsubkegiatan ID',
            'target_rkt' => 'Target Rkt',
            'anggaran_rkt' => 'Anggaran Rkt',
            'target_rkt_p' => 'Target Rkt P',
            'anggaran_rkt_p' => 'Anggaran Rkt P',
            'target_pk' => 'Target Pk',
            'anggaran_pk' => 'Anggaran Pk',
            'target_pk_p' => 'Target Pk P',
            'anggaran_pk_p' => 'Anggaran Pk P',
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

    public function getRefIndikatorCascadingSubkegiatanTriwulan()
    {
        return $this->hasMany(SakipIndikatorcascadingsubkegiatanTriwulan::class, ['refindikatorsubkegiatan_id' => 'refindikatorsubkegiatan_id']);
    }
}
