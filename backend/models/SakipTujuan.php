<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "v2_sakip_tujuan".
 *
 * @property int $reftujuan_id
 * @property string $uraian_tujuan
 * @property string $indikator_tujuan
 * @property int|null $refvisi_id
 * @property int|null $refmisi_id
 * @property int|null $refperiode_5tahun_id
 * @property string|null $tujuan_isaktif
 */
class SakipTujuan extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'v2_sakip_tujuan';
    }

    public $refperiode_id;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['uraian_tujuan'], 'required'],
            [['uraian_tujuan', 'indikator_tujuan'], 'string'],
            [['refvisi_id', 'refmisi_id', 'refperiode_5tahun_id', 'refperiode_id'], 'integer'],
            [['tujuan_isaktif'], 'string', 'max' => 10],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'reftujuan_id' => 'Reftujuan ID',
            'uraian_tujuan' => 'Uraian Tujuan',
            'indikator_tujuan' => 'Indikator Tujuan',
            'refvisi_id' => 'Refvisi ID',
            'refmisi_id' => 'Refmisi ID',
            'refperiode_5tahun_id' => 'Refperiode 5 Tahun ID',
            'refperiode_id' => 'Refperiode ID',
            'tujuan_isaktif' => 'Tujuan Isaktif',
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
    
    public function getVisi()
    {
        return $this->hasOne(SakipVisi::class, ['refvisi_id' => 'refvisi_id']);
    }

    public function getMisi()
    {
        return $this->hasOne(SakipMisi::class, ['refmisi_id' => 'refmisi_id']);
    }
}
