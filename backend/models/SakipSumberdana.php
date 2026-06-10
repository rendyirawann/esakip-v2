<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "sakip_sumberdana".
 *
 * @property int $refsumberdana_id
 * @property string $kode_sumberdana
 * @property string $nama_sumberdana
 * @property string|null $sumberdana_isaktif
 */
class SakipSumberdana extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'sakip_sumberdana';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['kode_sumberdana', 'nama_sumberdana'], 'required'],
            [['nama_sumberdana'], 'string'],
            [['kode_sumberdana'], 'string', 'max' => 150],
            [['sumberdana_isaktif'], 'string', 'max' => 10],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'refsumberdana_id' => 'Refsumberdana ID',
            'kode_sumberdana' => 'Kode Sumberdana',
            'nama_sumberdana' => 'Nama Sumberdana',
            'sumberdana_isaktif' => 'Sumberdana Isaktif',
        ];
    }
}
