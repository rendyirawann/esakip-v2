<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "sakip_pimpinan".
 *
 * @property int $refpimpinan_id
 * @property int|null $refperiode_id
 * @property string $nama_pimpinan
 * @property string $jabatan_pimpinan
 * @property string $nama_wpimpinan
 * @property string $jabatan_wpimpinan
 * @property string|null $user_edit
 * @property string|null $date_edit
 */
class SakipPimpinan extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'sakip_pimpinan';
    }



    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['refperiode_id'], 'integer'],
            [['nama_pimpinan', 'jabatan_pimpinan', 'nama_wpimpinan', 'jabatan_wpimpinan'], 'required'],
            [['date_edit'], 'safe'],
            [['nama_pimpinan', 'jabatan_pimpinan', 'nama_wpimpinan', 'jabatan_wpimpinan'], 'string', 'max' => 50],
            [['user_edit'], 'string', 'max' => 25],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'refpimpinan_id' => 'Refpimpinan ID',
            'refperiode_id' => 'Refperiode ID',
            'nama_pimpinan' => 'Nama Pimpinan',
            'jabatan_pimpinan' => 'Jabatan Pimpinan',
            'nama_wpimpinan' => 'Nama Wpimpinan',
            'jabatan_wpimpinan' => 'Jabatan Wpimpinan',
            'user_edit' => 'User Edit',
            'date_edit' => 'Date Edit',
        ];
    }
}
