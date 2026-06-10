<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "sakip_title".
 *
 * @property int $reftitle_id
 * @property string|null $nama_title
 */
class SakipTitle extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'sakip_title';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nama_title'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'reftitle_id' => 'Reftitle ID',
            'nama_title' => 'Nama Title',
        ];
    }
}
