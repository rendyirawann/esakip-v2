<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "sakip_eselon".
 *
 * @property int $refeselon_id
 * @property string|null $pangkat_eselon
 */
class SakipEselon extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'sakip_eselon';
    }


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pangkat_eselon'], 'string', 'max' => 75],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'refeselon_id' => 'Refeselon ID',
            'pangkat_eselon' => 'Pangkat Eselon',
        ];
    }
}
