<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "v2_sakip_tujuan_p".
 *
 * @property int $reftujuan_p_id
 * @property string $uraian_tujuan_p
 * @property string $indikator_tujuan_p
 * @property int|null $refvisi_p_id
 * @property int|null $refmisi_p_id
 * @property int|null $refperiode_5tahun_id
 * @property string|null $tujuan_p_isaktif
 */
class SakipTujuanP extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'v2_sakip_tujuan_p';
    }

    public $refperiode_id;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['uraian_tujuan_p'], 'required'],
            [['uraian_tujuan_p', 'indikator_tujuan_p'], 'string'],
            [['refvisi_p_id', 'refmisi_p_id', 'refperiode_5tahun_id', 'refperiode_id'], 'integer'],
            [['tujuan_p_isaktif'], 'string', 'max' => 10],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'reftujuan_p_id' => 'Reftujuan ID',
            'uraian_tujuan_p' => 'Uraian Tujuan',
            'indikator_tujuan_p' => 'Indikator Tujuan',
            'refvisi_p_id' => 'Refvisi ID',
            'refmisi_p_id' => 'Refmisi ID',
            'refperiode_5tahun_id' => 'Refperiode 5 Tahun ID',
            'refperiode_id' => 'Refperiode ID',
            'tujuan_p_isaktif' => 'Tujuan Isaktif',
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
        return $this->hasOne(SakipVisiP::class, ['refvisi_p_id' => 'refvisi_p_id']);
    }

    public function getMisi()
    {
        return $this->hasOne(SakipMisiP::class, ['refmisi_p_id' => 'refmisi_p_id']);
    }
}
