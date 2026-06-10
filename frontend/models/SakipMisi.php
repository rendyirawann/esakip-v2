<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "v2_sakip_misi".
 *
 * @property int $refmisi_id
 * @property string $uraian_misi
 * @property int|null $refperiode_5tahun_id
 * @property int|null $refvisi_id
 * @property string|null $misi_isaktif
 */
class SakipMisi extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'v2_sakip_misi';
    }


    public $refperiode_id;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['uraian_misi'], 'required'],
            [['uraian_misi'], 'string'],
            [['refperiode_5tahun_id', 'refvisi_id', 'refperiode_id'], 'integer'],
            [['misi_isaktif'], 'string', 'max' => 10],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'refmisi_id' => 'Refmisi ID',
            'uraian_misi' => 'Uraian Misi',
            'refperiode_5tahun_id' => 'Refperiode 5 Tahun ID',
            'refperiode_id' => 'Refperiode ID',
            'refvisi_id' => 'Refvisi ID',
            'misi_isaktif' => 'Misi Isaktif',
        ];
    }

    public function afterFind()
    {
        parent::afterFind();
        if ($this->refperiode_5tahun_id) {
            $periode = SakipPeriode::find()
                ->where(['refperiode_5tahun_id' => $this->refperiode_5tahun_id])
                ->orderBy(['periode_isaktif' => SORT_DESC, 'periode' => SORT_ASC])
                ->one();
            if ($periode) {
                $this->refperiode_id = $periode->refperiode_id;
            }
        }
    }

    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }
        if ($this->refperiode_id) {
            $periode = SakipPeriode::findOne($this->refperiode_id);
            if ($periode) {
                $this->refperiode_5tahun_id = $periode->refperiode_5tahun_id;
            }
        }
        return true;
    }

    public function getPeriode5Tahun()
    {
        return $this->hasOne(SakipPeriode5tahun::class, ['refperiode_5tahun_id' => 'refperiode_5tahun_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPeriode()
    {
        return $this->hasOne(SakipPeriode::class, ['refperiode_5tahun_id' => 'refperiode_5tahun_id'])
            ->orderBy(['periode_isaktif' => SORT_DESC, 'periode' => SORT_ASC]);
    }

    public function getSasaranRenstra()
    {
        return $this->hasMany(SakipSasaranrenstra::class, ['refmisi_id' => 'refmisi_id']);
    }
}
