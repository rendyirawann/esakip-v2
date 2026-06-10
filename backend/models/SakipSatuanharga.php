<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "sakip_satuanharga".
 *
 * @property int $refsatuanharga_id
 * @property string $kode_satuanharga
 * @property string $nama_satuanharga
 * @property string|null $satuanharga_isaktif
 */
class SakipSatuanharga extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'sakip_satuanharga';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['kode_satuanharga', 'nama_satuanharga'], 'required'],
            [['nama_satuanharga'], 'string'],
            [['kode_satuanharga'], 'string', 'max' => 150],
            [['satuanharga_isaktif'], 'string', 'max' => 10],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'refsatuanharga_id' => 'Refsatuanharga ID',
            'kode_satuanharga' => 'Kode Satuanharga',
            'nama_satuanharga' => 'Nama Satuanharga',
            'satuanharga_isaktif' => 'Satuanharga Isaktif',
        ];
    }
}
