<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "v2_sakip_periode".
 *
 * @property int $refperiode_id
 * @property int $periode
 * @property string|null $periode_isaktif
 * @property int|null $refperiode_5tahun_id
 */
class SakipPeriode extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'v2_sakip_periode';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['periode'], 'required'],
            [['periode', 'refperiode_5tahun_id'], 'integer'],
            [['periode_isaktif'], 'string', 'max' => 10],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'refperiode_id' => 'Refperiode ID',
            'periode' => 'Periode',
            'periode_isaktif' => 'Periode Isaktif',
            'refperiode_5tahun_id' => 'Refperiode 5 Tahun ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPeriode5Tahun()
    {
        return $this->hasOne(SakipPeriode5tahun::class, ['refperiode_5tahun_id' => 'refperiode_5tahun_id']);
    }
}
