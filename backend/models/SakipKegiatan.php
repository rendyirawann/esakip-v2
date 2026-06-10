<?php

namespace backend\models;

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

    public function getUrusan()
    {
        return $this->hasOne(SakipUrusan::class, ['urusan_id' => 'refurusan_id']);
    }
    
    public function getBidang()
    {
        return $this->hasOne(SakipBidang::class, ['refbidang_id' => 'refbidang_id']);
    }

    public function getProgram()
    {
        return $this->hasOne(SakipProgram::class, ['refprogram_id' => 'refprogram_id']);
    }


}
