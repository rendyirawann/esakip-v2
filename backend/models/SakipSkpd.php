<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "sakip_skpd".
 *
 * @property int $refskpd_id
 * @property string $kode_skpd
 * @property string $nama_skpd
 * @property string $kepala_skpd
 * @property string $nip_kepala
 * @property string $jabatan_kepala
 * @property string $pangkat_kepala
 * @property int|null $refurusan_id
 * @property int|null $refbidang_id
 * @property string|null $skpd_isaktif
 */
class SakipSkpd extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'sakip_skpd';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['kode_skpd', 'nama_skpd'], 'required'],
            [['nama_skpd', 'kepala_skpd', 'refskpd_keterangan'], 'string'],
            // [['refurusan_id', 'refbidang_id', 'refpenjabatskpd_id'], 'integer'],
            [['refurusan_id', 'refbidang_id'], 'integer'],
            [['kode_skpd'], 'string', 'max' => 150],
            [['nip_kepala', 'jabatan_kepala', 'pangkat_kepala'], 'string', 'max' => 50],
            [['skpd_isaktif'], 'string', 'max' => 10],
            [['refskpd_unit'], 'string', 'max' => 20],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'refskpd_id' => 'Refskpd ID',
            'kode_skpd' => 'Kode Skpd',
            'nama_skpd' => 'Nama Skpd',
            // 'refpenjabatskpd_id' => 'Refpenjabat SKPD ID',
            'refurusan_id' => 'Refurusan ID',
            'refbidang_id' => 'Refbidang ID',
            'refskpd_unit' => 'Unit SKPD',
            'refskpd_keterangan' => 'Keterangan SKPD',
            'skpd_isaktif' => 'Skpd Isaktif',
        ];
    }
    public function getUrusan()
    {
        return $this->hasOne(SakipUrusan::class, ['urusan_id' => 'refurusan_id']);
    }

    // public function getRefPenjabatSkpd()
    // {
    //     return $this->hasOne(SakipPenjabatSkpd::class, ['refpenjabatskpd_id' => 'refrefpenjabatskpd_id']);
    // }

    public function getBidang()
    {
        return $this->hasOne(SakipBidang::class, ['refbidang_id' => 'refbidang_id']);
    }

    public function getRefKoordinasis()
    {
        return $this->hasMany(SakipKoordinasi::class, ['refskpd_id' => 'refskpd_id']);
    }
}
