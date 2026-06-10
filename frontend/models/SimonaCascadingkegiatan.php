<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "simona_cascadingkegiatan".
 *
 * @property int $refsimonacascadingkegiatan_id
 * @property int|null $refcascadingprogram_id
 * @property int|null $refcascadingkegiatan_id
 * @property int|null $refskpd_id
 * @property int|null $refsasaranrenstra_id
 * @property int|null $refindikatorsasaranrenstra_id
 * @property int|null $refprogram_id
 * @property int|null $refkegiatan_id
 * @property string|null $uraian_sasarankegiatan
 * @property string|null $uraian_indikatorkegiatan
 * @property int|null $refperiode_id
 * @property string|null $kegiatan_target
 * @property string|null $kegiatan_satuan
 * @property int|null $refpegawaibappeda_id
 * @property string|null $date_start
 * @property string|null $expired_date
 * @property string|null $status_simonacascadingkegiatan
 */
class SimonaCascadingkegiatan extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'simona_cascadingkegiatan';
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
            [['refcascadingprogram_id', 'refcascadingkegiatan_id', 'refskpd_id', 'refsasaranrenstra_id', 'refindikatorsasaranrenstra_id', 'refprogram_id', 'refkegiatan_id', 'refperiode_id', 'refpegawaibappeda_id'], 'integer'],
            [['uraian_sasarankegiatan', 'uraian_indikatorkegiatan', 'nama_tahapankegiatan'], 'string'],
            [['date_start', 'expired_date'], 'safe'],
            [['kegiatan_target', 'kegiatan_satuan'], 'string', 'max' => 35],
            [['status_simonacascadingkegiatan'], 'string', 'max' => 15],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'refsimonacascadingkegiatan_id' => 'Refsimonacascadingkegiatan ID',
            'refcascadingprogram_id' => 'Refcascadingprogram ID',
            'refcascadingkegiatan_id' => 'Refcascadingkegiatan ID',
            'refskpd_id' => 'Refskpd ID',
            'refsasaranrenstra_id' => 'Refsasaranrenstra ID',
            'refindikatorsasaranrenstra_id' => 'Refindikatorsasaranrenstra ID',
            'refprogram_id' => 'Refprogram ID',
            'refkegiatan_id' => 'Refkegiatan ID',
            'uraian_sasarankegiatan' => 'Uraian Sasarankegiatan',
            'uraian_indikatorkegiatan' => 'Uraian Indikatorkegiatan',
            'refperiode_id' => 'Refperiode ID',
            'kegiatan_target' => 'Kegiatan Target',
            'kegiatan_satuan' => 'Kegiatan Satuan',
            'refpegawaibappeda_id' => 'Refpegawaibappeda ID',
            'nama_tahapankegiatan' => 'Nama Tahapan',
            'date_start' => 'Date Start',
            'expired_date' => 'Expired Date',
            'status_simonacascadingkegiatan' => 'Status Simonacascadingkegiatan',
        ];
    }

    public function getRefCascadingprogram()
    {
        return $this->hasOne(SakipCascadingprogram::class, ['refcascadingprogram_id' => 'refcascadingprogram_id']);
    }

    public function getRefCascadingKegiatan()
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
