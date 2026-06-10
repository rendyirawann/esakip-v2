<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "sakip_indikatortujuanrenstra".
 *
 * @property int $refindikatortujuanrenstra_id
 * @property string $uraian_indikatortujuanrenstra
 * @property int|null $reftujuanrenstra_id
 * @property int|null $refskpd_id
 */
class SakipIndikatortujuanrenstra extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'v2_sakip_indikatortujuanrenstra';
    }



    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            // [['uraian_indikatortujuanrenstra'], 'required'],
            [['uraian_indikatortujuanrenstra'], 'required', 'message' => 'Data tidak boleh kosong.'],
            [['uraian_indikatortujuanrenstra'], 'string'],
            [['reftujuanrenstra_id', 'refskpd_id', 'refperiode_id', 'refsasaranrenstra_id'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'refindikatortujuanrenstra_id' => 'Refindikatortujuanrenstra ID',
            'uraian_indikatortujuanrenstra' => 'Uraian Indikatortujuanrenstra',
            'reftujuanrenstra_id' => 'Reftujuanrenstra ID',
            'refsasaranrenstra_id' => 'Resasaranrenstra ID',
            'refskpd_id' => 'Refskpd ID',
            'refperiode_id' => 'Refperiode ID',
        ];
    }

    public function getRefPeriode()
    {
        return $this->hasOne(SakipPeriode::class, ['refperiode_id' => 'refperiode_id']);
    }

    public function getTujuanRenstra()
    {
        return $this->hasOne(SakipTujuanrenstra::class, ['reftujuanrenstra_id' => 'reftujuanrenstra_id']);
    }

    public function getSasaranRenstra()
    {
        return $this->hasOne(SakipSasaranrenstra::class, ['refsasaranrenstra_id' => 'refsasaranrenstra_id']);
    }
}
