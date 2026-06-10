<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "simona_rincianbelanjacascadingkegiatan".
 *
 * @property int $refsimonarincianbelanjacascadingkegiatan_id
 * @property int|null $refsimonacascadingkegiatan_id
 * @property string|null $detail_rincianbelanja
 * @property string|null $satuan_rincianbelanja
 * @property string|null $jumlah_rincianbelanja
 * @property string|null $anggaran_rincianbelanja
 */
class SimonaRincianbelanjacascadingkegiatan extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'simona_rincianbelanjacascadingkegiatan';
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
            [['refsimonacascadingkegiatan_id', 'refcascadingkegiatan_id', 'refkegiatan_id', 'refcascadingprogram_id', 'refprogram_id', 'refskpd_id', 'refperiode_id'], 'integer'],
            [['detail_rincianbelanja'], 'string'],
            [['anggaran_subkegiatan'], 'safe'],
            [['satuan_rincianbelanja', 'jumlah_rincianbelanja'], 'string', 'max' => 35],
            [['anggaran_rincianbelanja'], 'string', 'max' => 50],
            [['anggaran_rincianbelanja'], 'required', 'message' => 'Anggaran rincian belanja tidak boleh kosong.'],
        ];
    }




    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'refsimonarincianbelanjacascadingkegiatan_id' => 'Refsimonarincianbelanjacascadingkegiatan ID',
            'refsimonacascadingkegiatan_id' => 'Refsimonacascadingkegiatan ID',
            'refcascadingkegiatan_id' => 'refcascadingkegiatan_id ID',
            'refkegiatan_id' => 'refkegiatan_id ID',
            'refcacadingprogram_id' => 'refcacadingprogram_id ID',
            'refprogram_id' => 'refprogram_id ID',
            'refskpd_id' => 'refskpd_id ID',
            'refperiode_id' => 'refperiode_id ID',
            'detail_rincianbelanja' => 'Detail Rincianbelanja',
            'satuan_rincianbelanja' => 'Satuan Rincianbelanja',
            'jumlah_rincianbelanja' => 'Jumlah Rincianbelanja',
            'anggaran_rincianbelanja' => 'Anggaran Rincianbelanja',
        ];
    }

    public function getRefSimonacascadingkegiatan()
    {
        return $this->hasOne(SimonaCascadingkegiatan::class, ['refsimonacascadingkegiatan_id' => 'refsimonacascadingkegiatan_id']);
    }

    public function getRefCascadingkegiatan()
    {
        return $this->hasOne(SakipCascadingkegiatan::class, ['refcascadingkegiatan_id' => 'refcascadingkegiatan_id']);
    }

    public function getRefKegiatan()
    {
        return $this->hasOne(SakipKegiatan::class, ['refkegiatan_id' => 'refkegiatan_id']);
    }

    public function getRefCascadingprogram()
    {
        return $this->hasOne(SakipCascadingprogram::class, ['refcascadingprogram_id' => 'refcascadingprogram_id']);
    }

    public function getRefProgram()
    {
        return $this->hasOne(SakipProgram::class, ['refprogram_id' => 'refprogram_id']);
    }
}
