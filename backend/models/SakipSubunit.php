<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "sakip_subunit".
 *
 * @property int $refsubunit_id
 * @property string $kode_subunit
 * @property string $nama_subunit
 * @property string|null $subunit_isaktif
 */
class SakipSubunit extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'sakip_subunit';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['kode_subunit', 'nama_subunit'], 'required'],
            [['nama_subunit'], 'string'],
            [['kode_subunit'], 'string', 'max' => 150],
            [['subunit_isaktif'], 'string', 'max' => 10],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'refsubunit_id' => 'Refsubunit ID',
            'kode_subunit' => 'Kode Subunit',
            'nama_subunit' => 'Nama Subunit',
            'subunit_isaktif' => 'Subunit Isaktif',
        ];
    }
}
