<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "v2_sakip_periode_5tahun".
 *
 * @property int $refperiode_5tahun_id
 * @property int $tahun_mulai
 * @property int $tahun_selesai
 * @property string $nama_periode
 * @property string|null $is_aktif
 */
class SakipPeriode5tahun extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'v2_sakip_periode_5tahun';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tahun_mulai', 'tahun_selesai', 'nama_periode'], 'required'],
            [['tahun_mulai', 'tahun_selesai'], 'integer'],
            [['nama_periode'], 'string', 'max' => 50],
            [['is_aktif'], 'string', 'max' => 1],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'refperiode_5tahun_id' => 'Refperiode 5 Tahun ID',
            'tahun_mulai' => 'Tahun Mulai',
            'tahun_selesai' => 'Tahun Selesai',
            'nama_periode' => 'Nama Periode',
            'is_aktif' => 'Is Aktif',
        ];
    }
}
