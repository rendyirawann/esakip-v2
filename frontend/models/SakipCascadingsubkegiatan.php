<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "sakip_cascadingsubkegiatan".
 *
 * @property int $refcascadingsubkegiatan_id
 * @property int|null $refcascadingkegiatan_id
 * @property int|null $refcascadingprogram_id
 * @property int|null $refprogram_id
 * @property int|null $refkegiatan_id
 * @property int|null $refsubkegiatan_id
 * @property string $uraian_sasaransubkegiatan
 * @property string $uraian_indikatorsubkegiatan
 * @property int|null $refperiode_id
 * @property int|null $refskpd_id
 * @property string|null $subkegiatan_target
 * @property string|null $subkegiatan_satuan
 * @property string|null $subkegiatan_anggaran
 */
class SakipCascadingsubkegiatan extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'v2_sakip_cascadingsubkegiatan';
    }

    public static function getDb()
    {
        return Yii::$app->get('db'); // Koneksi ke database `db`
    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['refcascadingkegiatan_id', 'refcascadingprogram_id', 'refprogram_id', 'refkegiatan_id', 'refsubkegiatan_id', 'refperiode_id', 'refskpd_id', 'refsasaranrenstra_id', 'refindikatorsasaranrenstra_id'], 'integer'],
            [
                [
                    'refcascadingkegiatan_id',
                    'refcascadingprogram_id',
                    'refsasaranrenstra_id',
                    'refindikatorsasaranrenstra_id',
                    'refprogram_id',
                    'refkegiatan_id',
                    'refsubkegiatan_id',
                    'uraian_sasaransubkegiatan',
                    'uraian_indikatorsubkegiatan',
                    'subkegiatan_anggaran' // Anggaran juga wajib diisi
                ],
                'required',
                'message' => '{attribute} tidak boleh kosong.'
            ],
            [['uraian_sasaransubkegiatan', 'uraian_indikatorsubkegiatan'], 'string'],
            [['subkegiatan_target', 'subkegiatan_satuan'], 'string', 'max' => 20],
            [['subkegiatan_anggaran'], 'required'], // Make it required
            [['subkegiatan_anggaran'], 'match', 'pattern' => '/^\d+$/', 'message' => 'Anggaran harus berupa angka.'], // Validate that it contains only digits
            [['subkegiatan_anggaran'], 'string', 'max' => 100],
        ];
    }


    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'refcascadingsubkegiatan_id' => 'Refcascadingsubkegiatan ID',
            'refcascadingkegiatan_id' => 'Refcascadingkegiatan ID',
            'refcascadingprogram_id' => 'Refcascadingprogram ID',
            'refsasaranrenstra_id' => 'refsasaranrenstra ID',
            'refindikatorsasaranrenstra_id' => 'refindikatorsasaranrenstra ID',
            'refprogram_id' => 'Refprogram ID',
            'refkegiatan_id' => 'Refkegiatan ID',
            'refsubkegiatan_id' => 'Refsubkegiatan ID',
            'uraian_sasaransubkegiatan' => 'Uraian Sasaransubkegiatan',
            'uraian_indikatorsubkegiatan' => 'Uraian Indikatorsubkegiatan',
            'refperiode_id' => 'Refperiode ID',
            'refskpd_id' => 'Refskpd ID',
            'subkegiatan_target' => 'Subkegiatan Target',
            'subkegiatan_satuan' => 'Subkegiatan Satuan',
            'subkegiatan_anggaran' => 'Subkegiatan Anggaran',
        ];
    }

    public function getRefCascadingProgram()
    {
        return $this->hasOne(SakipCascadingprogram::class, ['refcascadingprogram_id' => 'refcascadingprogram_id']);
    }

    public function getRefCascadingKegiatan()
    {
        return $this->hasOne(SakipCascadingkegiatan::class, ['refcascadingkegiatan_id' => 'refcascadingkegiatan_id']);
    }

    public function getRefSasaranRenstra()
    {
        return $this->hasOne(SakipSasaranrenstra::class, ['refsasaranrenstra_id' => 'refsasaranrenstra_id']);
    }

    public function getRefIndikatorSasaranRenstra()
    {
        return $this->hasOne(SakipIndikatorsasaranrenstra::class, ['refindikatorsasaranrenstra_id' => 'refindikatorsasaranrenstra_id']);
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

    public function getRefPeriode()
    {
        return $this->hasOne(SakipPeriode::class, ['refperiode_id' => 'refperiode_id']);
    }

    public function getRefIndikatorcascadingsubkegiatan()
    {
        return $this->hasMany(SakipIndikatorcascadingsubkegiatan::class, ['refcascadingsubkegiatan_id' => 'refcascadingsubkegiatan_id']);
    }

    public function getIndikatorTriwulan()
    {
        return $this->hasMany(SakipIndikatorcascadingsubkegiatanTriwulan::class, ['refcascadingsubkegiatan_id' => 'refcascadingsubkegiatan_id']);
    }
}
