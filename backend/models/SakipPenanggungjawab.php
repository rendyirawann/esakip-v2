<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "sakip_penanggungjawab".
 *
 * @property int $refpenanggungjawab_id
 * @property int|null $refpegawai_id
 * @property int|null $refbidangbappeda_id
 * @property int|null $refuser_id
 * @property int|null $refskpd_id
 */
class SakipPenanggungjawab extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'sakip_penanggungjawab';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['refpegawai_id', 'refbidangbappeda_id', 'refuser_id', 'refskpd_id'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'refpenanggungjawab_id' => 'Refpenanggungjawab ID',
            'refpegawai_id' => 'Refpegawai ID',
            'refbidangbappeda_id' => 'Refbidangbappeda ID',
            'refuser_id' => 'Refuser ID',
            'refskpd_id' => 'Refskpd ID',
        ];
    }

    public function getRefPegawai()
    {
        return $this->hasOne(SakipPegawaibappeda::class, ['refpegawai_id' => 'refpegawai_id']);
    }

    public function getRefBidangbappeda()
    {
        return $this->hasOne(SakipBidangbappeda::class, ['refbidangbappeda_id' => 'refbidangbappeda_id']);
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
