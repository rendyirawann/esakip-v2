<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "v2_sakip_sasaran".
 *
 * @property int $refsasaran_id
 * @property string $uraian_sasaran
 * @property int|null $refperiode_5tahun_id
 * @property int|null $refvisi_id
 * @property int|null $refmisi_id
 * @property int|null $reftujuan_id
 * @property string|null $sasaran_isaktif
 */
class SakipSasaran extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'v2_sakip_sasaran';
    }



    public $refperiode_id;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['uraian_sasaran'], 'required'],
            [['uraian_sasaran'], 'string'],
            [['refperiode_5tahun_id', 'refvisi_id', 'refmisi_id', 'reftujuan_id', 'refperiode_id'], 'integer'],
            [['sasaran_isaktif'], 'string', 'max' => 10],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'refsasaran_id' => 'Refsasaran ID',
            'uraian_sasaran' => 'Uraian Sasaran',
            'refperiode_5tahun_id' => 'Refperiode 5 Tahun ID',
            'refperiode_id' => 'Refperiode ID',
            'refvisi_id' => 'Refvisi ID',
            'refmisi_id' => 'Refmisi ID',
            'reftujuan_id' => 'Reftujuan ID',
            'sasaran_isaktif' => 'Sasaran Isaktif',
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
        return $this->hasOne(SakipTujuan::class, ['reftujuan_id' => 'reftujuan_id']);
    }

    public function getRefVisi()
    {
        return $this->hasOne(SakipVisi::class, ['refvisi_id' => 'refvisi_id']);
    }

    public function getRefMisi()
    {
        return $this->hasOne(SakipMisi::class, ['refmisi_id' => 'refmisi_id']);
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
        // Tetap adakan relasi ini jika dipakai di frontend, tapi kembalikan getPeriode5Tahun
        return $this->getPeriode5Tahun();
    }

    public function getRefTujuan()
    {
        return $this->hasOne(SakipTujuan::class, ['reftujuan_id' => 'reftujuan_id']);
    }
}
