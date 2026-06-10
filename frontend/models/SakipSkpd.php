<?php

namespace frontend\models;

use Yii;
use yii\base\NotSupportedException;

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
 * @property string|null $refskpd_unit
 * @property string|null $refskpd_keterangan
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
            [['kode_skpd', 'nama_skpd', 'kepala_skpd', 'nip_kepala', 'jabatan_kepala', 'pangkat_kepala'], 'required'],
            [['nama_skpd', 'kepala_skpd', 'refskpd_keterangan'], 'string'],
            [['refurusan_id', 'refbidang_id'], 'integer'],
            [['kode_skpd'], 'string', 'max' => 150],
            [['nip_kepala', 'jabatan_kepala', 'pangkat_kepala'], 'string', 'max' => 50],
            [['refskpd_unit'], 'string', 'max' => 20],
            [['skpd_isaktif'], 'string', 'max' => 10],
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
            'kepala_skpd' => 'Kepala Skpd',
            'nip_kepala' => 'Nip Kepala',
            'jabatan_kepala' => 'Jabatan Kepala',
            'pangkat_kepala' => 'Pangkat Kepala',
            'refurusan_id' => 'Refurusan ID',
            'refbidang_id' => 'Refbidang ID',
            'refskpd_unit' => 'Refskpd Unit',
            'refskpd_keterangan' => 'Refskpd Keterangan',
            'skpd_isaktif' => 'Skpd Isaktif',
        ];
    }

    public function getRefKoordinasis()
    {
        return $this->hasMany(SakipKoordinasi::class, ['refskpd_id' => 'refskpd_id']);
    }
}
