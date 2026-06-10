<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "sakip_lkesubkomponen".
 *
 * @property int $reflkesubkomponen_id
 * @property int|null $reflkekomponen_id
 * @property string|null $uraian_lkesubkomponen
 * @property string|null $bobot_lkesubkomponen
 */
class SakipLkesubkomponen extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'sakip_lkesubkomponen';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['reflkekomponen_id'], 'integer'],
            [['uraian_lkesubkomponen'], 'string'],
            [['bobot_lkesubkomponen'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'reflkesubkomponen_id' => 'Reflkesubkomponen ID',
            'reflkekomponen_id' => 'Reflkekomponen ID',
            'uraian_lkesubkomponen' => 'Uraian Lkesubkomponen',
            'bobot_lkesubkomponen' => 'Bobot Lkesubkomponen',
        ];
    }
}
