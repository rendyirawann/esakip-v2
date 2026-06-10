<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "sakip_bidang".
 *
 * @property int $refbidang_id
 * @property string $kode_bidang
 * @property string $nama_bidang
 * @property string|null $bidang_isaktif
 * @property int|null $refurusan_id
 */
class SakipBidang extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'sakip_bidang';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['kode_bidang', 'nama_bidang'], 'required'],
            [['nama_bidang'], 'string'],
            [['refurusan_id'], 'integer'],
            [['kode_bidang'], 'string', 'max' => 150],
            [['bidang_isaktif'], 'string', 'max' => 10],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'refbidang_id' => 'Refbidang ID',
            'kode_bidang' => 'Kode Bidang',
            'nama_bidang' => 'Nama Bidang',
            'bidang_isaktif' => 'Bidang Isaktif',
            'refurusan_id' => 'Refurusan ID',
        ];
    }
}
