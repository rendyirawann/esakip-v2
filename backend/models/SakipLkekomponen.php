<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "sakip_lkekomponen".
 *
 * @property int $reflkekomponen_id
 * @property string|null $uraian_lkekomponen
 */
class SakipLkekomponen extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'sakip_lkekomponen';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['uraian_lkekomponen'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'reflkekomponen_id' => 'Reflkekomponen ID',
            'uraian_lkekomponen' => 'Uraian Lkekomponen',
        ];
    }
}
