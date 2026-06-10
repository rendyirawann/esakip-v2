<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "sakip_bidangbappeda".
 *
 * @property int $refbidangbappeda_id
 * @property string|null $nama_bidangbappeda
 */
class SakipBidangbappeda extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'sakip_bidangbappeda';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nama_bidangbappeda'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'refbidangbappeda_id' => 'Refbidangbappeda ID',
            'nama_bidangbappeda' => 'Nama Bidangbappeda',
        ];
    }
}
