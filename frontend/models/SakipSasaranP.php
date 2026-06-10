<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "v2_sakip_sasaran_p".
 *
 * @property int $refsasaran_p_id
 * @property string $uraian_sasaran_p
 * @property int|null $refperiode_5tahun_id
 * @property int|null $refvisi_p_id
 * @property int|null $refmisi_p_id
 * @property int|null $reftujuan_p_id
 * @property string|null $sasaran_p_isaktif
 */
class SakipSasaranP extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'v2_sakip_sasaran_p';
    }

    public $refperiode_id;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['uraian_sasaran_p'], 'required'],
            [['uraian_sasaran_p'], 'string'],
            [['refperiode_5tahun_id', 'refvisi_p_id', 'refmisi_p_id', 'reftujuan_p_id', 'refperiode_id'], 'integer'],
            [['sasaran_p_isaktif'], 'string', 'max' => 10],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'refsasaran_p_id' => 'Refsasaran ID',
            'uraian_sasaran_p' => 'Uraian Sasaran',
            'refperiode_5tahun_id' => 'Refperiode 5 Tahun ID',
            'refperiode_id' => 'Refperiode ID',
            'refvisi_p_id' => 'Refvisi ID',
            'refmisi_p_id' => 'Refmisi ID',
            'reftujuan_p_id' => 'Reftujuan ID',
            'sasaran_p_isaktif' => 'Sasaran Status',
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

    public function getTujuan()
    {
        return $this->hasOne(SakipTujuanP::class, ['reftujuan_p_id' => 'reftujuan_p_id']);
    }

    public function getRefVisi()
    {
        return $this->hasOne(SakipVisiP::class, ['refvisi_p_id' => 'refvisi_p_id']);
    }

    public function getRefMisi()
    {
        return $this->hasOne(SakipMisiP::class, ['refmisi_p_id' => 'refmisi_p_id']);
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

    public function getRefPeriode()
    {
        return $this->getPeriode5Tahun();
    }

    public function getRefTujuan()
    {
        return $this->hasOne(SakipTujuanP::class, ['reftujuan_p_id' => 'reftujuan_p_id']);
    }
}
