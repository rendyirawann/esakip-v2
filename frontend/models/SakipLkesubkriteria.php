<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "sakip_lkesubkriteria".
 *
 * @property int $reflkesubkriteria_id
 * @property int|null $reflkekomponen_id
 * @property int|null $reflkesubkomponen_id
 * @property string|null $uraian_lkesubkriteria
 */
class SakipLkesubkriteria extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'sakip_lkesubkriteria';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['reflkekomponen_id', 'reflkesubkomponen_id'], 'integer'],
            [['uraian_lkesubkriteria'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'reflkesubkriteria_id' => 'Reflkesubkriteria ID',
            'reflkekomponen_id' => 'Reflkekomponen ID',
            'reflkesubkomponen_id' => 'Reflkesubkomponen ID',
            'uraian_lkesubkriteria' => 'Uraian Lkesubkriteria',
        ];
    }
}
