<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "sakip_kegiatan".
 *
 * @property int $refkegiatan_id
 * @property string $kode_kegiatan
 * @property string $nama_kegiatan
 * @property int|null $refurusan_id
 * @property int|null $refbidang_id
 * @property int|null $refprogram_id
 * @property string|null $kegiatan_isaktif
 */
class SakipKegiatan extends \yii\db\ActiveRecord
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
        return 'sakip_kegiatan';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['kode_kegiatan', 'nama_kegiatan'], 'required'],
            [['nama_kegiatan'], 'string'],
            [['refurusan_id', 'refbidang_id', 'refprogram_id'], 'integer'],
            [['kode_kegiatan'], 'string', 'max' => 150],
            [['kegiatan_isaktif'], 'string', 'max' => 10],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'refkegiatan_id' => 'Refkegiatan ID',
            'kode_kegiatan' => 'Kode Kegiatan',
            'nama_kegiatan' => 'Nama Kegiatan',
            'refurusan_id' => 'Refurusan ID',
            'refbidang_id' => 'Refbidang ID',
            'refprogram_id' => 'Refprogram ID',
            'kegiatan_isaktif' => 'Kegiatan Isaktif',
        ];
    }
}
