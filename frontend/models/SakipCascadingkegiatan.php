<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "sakip_cascadingkegiatan".
 *
 * @property int $refcascadingkegiatan_id
 * @property int|null $refcascadingprogram_id
 * @property int|null $refprogram_id
 * @property int|null $refkegiatan_id
 * @property string $uraian_sasarankegiatan
 * @property string $uraian_indikatorkegiatan
 * @property int|null $refperiode_id
 * @property int|null $refskpd_id
 * @property string|null $kegiatan_target
 * @property string|null $kegiatan_satuan
 */
class SakipCascadingkegiatan extends \yii\db\ActiveRecord
{

    // Tentukan koneksi database yang digunakan untuk model ini
    public static function getDb()
    {
        return Yii::$app->get('db'); // Menggunakan koneksi db (esakip)
    }
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'v2_sakip_cascadingkegiatan';
    }


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['refcascadingprogram_id', 'refsasaranrenstra_id', 'refindikatorsasaranrenstra_id', 'refprogram_id', 'refkegiatan_id', 'refperiode_id', 'refskpd_id'], 'integer'],
            [
                [
                    'refcascadingprogram_id',
                    'refsasaranrenstra_id',
                    'refindikatorsasaranrenstra_id',
                    'refprogram_id',
                    'refkegiatan_id',
                    'uraian_sasarankegiatan',
                    'uraian_indikatorkegiatan'
                ],
                'required',
                'message' => '{attribute} tidak boleh kosong.'
            ],
            [['uraian_sasarankegiatan', 'uraian_indikatorkegiatan'], 'string'],
            [['kegiatan_target', 'kegiatan_satuan'], 'string', 'max' => 20],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'refcascadingkegiatan_id' => 'Refcascadingkegiatan ID',
            'refcascadingprogram_id' => 'Refcascadingprogram ID',
            'refsasaranrenstra_id' => 'refsasaranrenstra ID',
            'refindikatorsasaranrenstra_id' => 'refindikatorsasaranrenstra ID',
            'refprogram_id' => 'Refprogram ID',
            'refkegiatan_id' => 'Refkegiatan ID',
            'uraian_sasarankegiatan' => 'Uraian Sasarankegiatan',
            'uraian_indikatorkegiatan' => 'Uraian Indikatorkegiatan',
            'refperiode_id' => 'Refperiode ID',
            'refskpd_id' => 'Refskpd ID',
            'kegiatan_target' => 'Kegiatan Target',
            'kegiatan_satuan' => 'Kegiatan Satuan',
        ];
    }

    public function getRefCascadingProgram()
    {
        return $this->hasOne(SakipCascadingprogram::class, ['refcascadingprogram_id' => 'refcascadingprogram_id']);
    }

    public function getRefSasaranRenstra()
    {
        return $this->hasOne(SakipSasaranrenstra::class, ['refsasaranrenstra_id' => 'refsasaranrenstra_id']);
    }

    public function getRefIndikatorsasaranRenstra()
    {
        return $this->hasOne(SakipIndikatorsasaranrenstra::class, ['refindikatorsasaranrenstra_id' => 'refindikatorsasaranrenstra_id']);
    }

    public function getRefKegiatan()
    {
        return $this->hasOne(SakipKegiatan::class, ['refkegiatan_id' => 'refkegiatan_id']);
    }

    public function getRefProgram()
    {
        return $this->hasOne(SakipProgram::class, ['refprogram_id' => 'refprogram_id']);
    }

    public function getRefPeriode()
    {
        return $this->hasOne(SakipPeriode::class, ['refperiode_id' => 'refperiode_id']);
    }

    public function getSimonaKeluaranmediacascadingkegiatans()
    {
        return $this->hasMany(SimonaKeluaranmediacascadingkegiatan::class, ['refcascadingkegiatan_id' => 'refcascadingkegiatan_id']);
    }

    public function getIndikatorCascadingKegiatan()
    {
        return $this->hasMany(SakipIndikatorcascadingkegiatan::class, ['refcascadingkegiatan_id' => 'refcascadingkegiatan_id']);
    }

    public function getCascadingSubkegiatans()
    {
        return $this->hasMany(SakipCascadingsubkegiatan::class, ['refcascadingkegiatan_id' => 'refcascadingkegiatan_id']);
    }
}
