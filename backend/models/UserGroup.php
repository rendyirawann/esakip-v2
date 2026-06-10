<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "user_group".
 *
 * @property int $kode_group
 * @property string $nama_group
 */
class UserGroup extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_group';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nama_group'], 'required'],
            [['nama_group'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'kode_group' => 'Kode Group',
            'nama_group' => 'Nama Group',
        ];
    }
}
