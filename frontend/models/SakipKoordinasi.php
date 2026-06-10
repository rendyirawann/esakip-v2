<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "sakip_koordinasi".
 *
 * @property int $refkoordinasi_id
 * @property int|null $refuser_id
 * @property int $refskpd_id
 */
class SakipKoordinasi extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'sakip_koordinasi';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['refuser_id', 'refskpd_id'], 'integer'],
            [['refskpd_id'], 'required'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'refkoordinasi_id' => 'Refkoordinasi ID',
            'refuser_id' => 'Refuser ID',
            'refskpd_id' => 'Refskpd ID',
        ];
    }

    public function getRefUser()
    {
        return $this->hasOne(User::class, ['id' => 'refuser_id']);
    }

    public function getRefSkpd()
    {
        return $this->hasOne(SakipSkpd::class, ['refskpd_id' => 'refskpd_id']);
    }
}
