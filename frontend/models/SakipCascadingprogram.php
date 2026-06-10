<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "sakip_cascadingprogram".
 *
 * @property int $refcascadingprogram_id
 * @property int|null $refsasaran_id
 * @property int|null $refskpd_id
 * @property int|null $reftujuan_id
 * @property int|null $refmisi_id
 * @property int|null $refsasaranrenstra_id
 * @property int|null $refindikatorsasaranrenstra_id
 * @property int|null $refbidang_id
 * @property int|null $refprogram_id
 * @property string $uraian_sasaranprogram
 * @property string $uraian_indikatorprogram
 * @property int|null $refperiode_id
 * @property string $program_target
 * @property string $program_satuan
 */
class SakipCascadingprogram extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'v2_sakip_cascadingprogram';
    }


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            // Aturan ini memastikan bahwa input adalah angka (integer)
            [['refsasaran_id', 'refskpd_id', 'reftujuan_id', 'refmisi_id', 'refsasaranrenstra_id', 'refindikatorsasaranrenstra_id', 'refbidang_id', 'refprogram_id', 'refperiode_id'], 'integer'],

            // Aturan ini mewajibkan field-field di bawah ini tidak boleh kosong
            // SAYA MENAMBAHKAN 'refprogram_id' dan 'refindikatorsasaranrenstra_id' DI SINI
            [
                [
                    'uraian_sasaranprogram',
                    'uraian_indikatorprogram',
                    'program_target',
                    'program_satuan',
                    'refsasaran_id',
                    'reftujuan_id',
                    'refmisi_id',
                    'refsasaranrenstra_id',
                    'refprogram_id',
                    'refindikatorsasaranrenstra_id' // <-- Penambahan di sini
                ],
                'required',
                'message' => '{attribute} tidak boleh kosong.' // Pesan error kustom (opsional)
            ],

            [['program_target'], 'match', 'pattern' => '/^\d+(\.\d{1,2})?$/', 'message' => 'Hanya boleh diisi dengan angka atau angka desimal.'],
            [['uraian_sasaranprogram', 'uraian_indikatorprogram'], 'string'],
            [['program_target', 'program_satuan'], 'string', 'max' => 20],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'refcascadingprogram_id' => 'Refcascadingprogram ID',
            'refsasaran_id' => 'Refsasaran ID',
            'refskpd_id' => 'Refskpd ID',
            'reftujuan_id' => 'Reftujuan ID',
            'refmisi_id' => 'Refmisi ID',
            'refsasaranrenstra_id' => 'Refsasaranrenstra ID',
            'refindikatorsasaranrenstra_id' => 'Refindikatorsasaranrenstra ID',
            'refbidang_id' => 'Refbidang ID',
            'refprogram_id' => 'Refprogram ID',
            'uraian_sasaranprogram' => 'Uraian Sasaranprogram',
            'uraian_indikatorprogram' => 'Uraian Indikatorprogram',
            'refperiode_id' => 'Refperiode ID',
            'program_target' => 'Program Target',
            'program_satuan' => 'Program Satuan',
        ];
    }

    public function getRefPeriode()
    {
        return $this->hasOne(SakipPeriode::class, ['refperiode_id' => 'refperiode_id']);
    }

    public function getSasaranRenstra()
    {
        return $this->hasOne(SakipSasaranrenstra::class, ['refsasaranrenstra_id' => 'refsasaranrenstra_id']);
    }

    public function getRefmisi()
    {
        return $this->hasOne(SakipMisi::class, ['refmisi_id' => 'refmisi_id']);
    }

    public function getReftujuan()
    {
        return $this->hasOne(SakipTujuan::class, ['reftujuan_id' => 'reftujuan_id']);
    }

    public function getRefsasaran()
    {
        return $this->hasOne(SakipSasaran::class, ['refsasaran_id' => 'refsasaran_id']);
    }

    public function getRefIndikatorSasaranRenstra()
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

    public function getCascadingKegiatans()
    {
        return $this->hasMany(SakipCascadingkegiatan::class, ['refcascadingprogram_id' => 'refcascadingprogram_id']);
    }

    public function getIndikatorCascadingPrograms()
    {
        return $this->hasMany(SakipIndikatorcascadingprogram::class, ['refcascadingprogram_id' => 'refcascadingprogram_id']);
    }
    public function getCascadingSubkegiatans()
    {
        return $this->hasMany(SakipCascadingsubkegiatan::class, ['refcascadingprogram_id' => 'refcascadingprogram_id']);
    }
}
