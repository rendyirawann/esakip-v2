<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "sakip_rekening".
 *
 * @property int $refrekening_id
 * @property string $kode_rekening
 * @property string $nama_rekening
 * @property string|null $rekening_isaktif
 */
class SakipRekening extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'sakip_rekening';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['kode_rekening', 'nama_rekening'], 'required'],
            [['nama_rekening'], 'string'],
            [['kode_rekening'], 'string', 'max' => 150],
            [['rekening_isaktif'], 'string', 'max' => 10],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'refrekening_id' => 'Refrekening ID',
            'kode_rekening' => 'Kode Rekening',
            'nama_rekening' => 'Nama Rekening',
            'rekening_isaktif' => 'Rekening Isaktif',
        ];
    }
}
