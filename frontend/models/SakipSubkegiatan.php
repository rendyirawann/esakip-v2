<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "sakip_subkegiatan".
 *
 * @property int $refsubkegiatan_id
 * @property string $kode_subkegiatan
 * @property string $nama_subkegiatan
 * @property int|null $refurusan_id
 * @property int|null $refbidang_id
 * @property int|null $refprogram_id
 * @property int|null $refkegiatan_id
 * @property string|null $subkegiatan_isaktif
 */
class SakipSubkegiatan extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'sakip_subkegiatan';
    }


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['kode_subkegiatan', 'nama_subkegiatan'], 'required'],
            [['nama_subkegiatan'], 'string'],
            [['refurusan_id', 'refbidang_id', 'refprogram_id', 'refkegiatan_id'], 'integer'],
            [['kode_subkegiatan'], 'string', 'max' => 150],
            [['subkegiatan_isaktif'], 'string', 'max' => 10],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'refsubkegiatan_id' => 'Refsubkegiatan ID',
            'kode_subkegiatan' => 'Kode Subkegiatan',
            'nama_subkegiatan' => 'Nama Subkegiatan',
            'refurusan_id' => 'Refurusan ID',
            'refbidang_id' => 'Refbidang ID',
            'refprogram_id' => 'Refprogram ID',
            'refkegiatan_id' => 'Refkegiatan ID',
            'subkegiatan_isaktif' => 'Subkegiatan Isaktif',
        ];
    }
}
