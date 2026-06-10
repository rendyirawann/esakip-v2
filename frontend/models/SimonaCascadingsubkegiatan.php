<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "simona_cascadingsubkegiatan".
 *
 * @property int $refsimonacascadingsubkegiatan_id
 * @property int|null $refcascadingprogram_id
 * @property int|null $refcascadingkegiatan_id
 * @property int|null $refcascadingsubkegiatan_id
 * @property int|null $refskpd_id
 * @property int|null $refsasaranrenstra_id
 * @property int|null $refindikatorsasaranrenstra_id
 * @property int|null $refprogram_id
 * @property int|null $refkegiatan_id
 * @property int|null $refsubkegiatan_id
 * @property string|null $uraian_sasaransubkegiatan
 * @property string|null $uraian_indikatorsubkegiatan
 * @property int|null $refperiode_id
 * @property string|null $subkegiatan_target
 * @property string|null $subkegiatan_satuan
 * @property int|null $refpegawaibappeda_id
 * @property string|null $date_start
 * @property string|null $expired_date
 * @property string|null $status_simonacascadingsubkegiatan
 */
class SimonaCascadingsubkegiatan extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'simona_cascadingsubkegiatan';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('db1');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['refcascadingprogram_id', 'refcascadingkegiatan_id', 'refcascadingsubkegiatan_id', 'refskpd_id', 'refsasaranrenstra_id', 'refindikatorsasaranrenstra_id', 'refprogram_id', 'refkegiatan_id', 'refsubkegiatan_id', 'refperiode_id', 'refpegawaibappeda_id'], 'integer'],
            [['uraian_sasaransubkegiatan', 'uraian_indikatorsubkegiatan', 'nama_tahapansubkegiatan'], 'string'],
            [['date_start', 'expired_date'], 'safe'],
            [['subkegiatan_target', 'subkegiatan_satuan'], 'string', 'max' => 35],
            [['status_simonacascadingsubkegiatan'], 'string', 'max' => 15],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'refsimonacascadingsubkegiatan_id' => 'Refsimonacascadingsubkegiatan ID',
            'refcascadingprogram_id' => 'Refcascadingprogram ID',
            'refcascadingkegiatan_id' => 'Refcascadingkegiatan ID',
            'refcascadingsubkegiatan_id' => 'Refcascadingsubkegiatan ID',
            'refskpd_id' => 'Refskpd ID',
            'refsasaranrenstra_id' => 'Refsasaranrenstra ID',
            'refindikatorsasaranrenstra_id' => 'Refindikatorsasaranrenstra ID',
            'refprogram_id' => 'Refprogram ID',
            'refkegiatan_id' => 'Refkegiatan ID',
            'refsubkegiatan_id' => 'Refsubkegiatan ID',
            'uraian_sasaransubkegiatan' => 'Uraian Sasaransubkegiatan',
            'uraian_indikatorsubkegiatan' => 'Uraian Indikatorsubkegiatan',
            'refperiode_id' => 'Refperiode ID',
            'subkegiatan_target' => 'Subkegiatan Target',
            'subkegiatan_satuan' => 'Subkegiatan Satuan',
            'refpegawaibappeda_id' => 'Refpegawaibappeda ID',
            'nama_tahapansubkegiatan' => 'nama_tahapansubkegiatan',
            'date_start' => 'Date Start',
            'expired_date' => 'Expired Date',
            'status_simonacascadingsubkegiatan' => 'Status Simonacascadingsubkegiatan',
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
}
