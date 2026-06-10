<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "v2_sakip_sasaranrenstra_p".
 *
 * @property int $refsasaranrenstra_p_id
 * @property int $refsasaranrenstra_id
 * @property string $uraian_sasaranrenstra_p
 * @property int|null $refskpd_id
 * @property int $refsasaran_p_id
 * @property int $refvisi_p_id
 * @property int $refmisi_p_id
 * @property int $reftujuan_p_id
 * @property int|null $refperiode_5tahun_id
 * @property int|null $reftujuanrenstra_p_id
 * @property string|null $sasaranrenstra_p_isaktif
 * @property string|null $alasan_sasaranrenstra_p
 * @property string|null $formulasi_sasaranrenstra_p
 * @property string|null $kriteria_sasaranrenstra_p
 */
class SakipSasaranrenstraP extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'v2_sakip_sasaranrenstra_p';
    }

    public $refperiode_id;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['uraian_sasaranrenstra_p', 'refsasaran_p_id', 'refvisi_p_id', 'refmisi_p_id', 'reftujuan_p_id'], 'required'],
            [['refsasaranrenstra_id', 'refskpd_id', 'refsasaran_p_id', 'refvisi_p_id', 'refmisi_p_id', 'reftujuan_p_id', 'refperiode_5tahun_id', 'reftujuanrenstra_p_id', 'refperiode_id'], 'integer'],
            [['uraian_sasaranrenstra_p', 'alasan_sasaranrenstra_p', 'kriteria_sasaranrenstra_p'], 'string'],
            [['sasaranrenstra_p_isaktif'], 'string', 'max' => 10],
            [['formulasi_sasaranrenstra_p'], 'string', 'max' => 155],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'refsasaranrenstra_p_id' => 'Refsasaranrenstra P ID',
            'refsasaranrenstra_id' => 'Refsasaranrenstra ID',
            'uraian_sasaranrenstra_p' => 'Uraian Sasaranrenstra P',
            'refskpd_id' => 'Refskpd ID',
            'refsasaran_p_id' => 'Refsasaran P ID',
            'refvisi_p_id' => 'Refvisi P ID',
            'refmisi_p_id' => 'Refmisi P ID',
            'reftujuan_p_id' => 'Reftujuan P ID',
            'refperiode_5tahun_id' => 'Refperiode 5 Tahun ID',
            'refperiode_id' => 'Refperiode ID',
            'reftujuanrenstra_p_id' => 'Reftujuanrenstra P ID',
            'sasaranrenstra_p_isaktif' => 'Sasaranrenstra P Isaktif',
            'alasan_sasaranrenstra_p' => 'Alasan Sasaranrenstra P',
            'formulasi_sasaranrenstra_p' => 'Formulasi Sasaranrenstra P',
            'kriteria_sasaranrenstra_p' => 'Kriteria Sasaranrenstra P',
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

    // Di dalam model SakipSasaranrenstra

    public function getSasaran()
    {
        return $this->hasOne(SakipSasaranP::class, ['refsasaran_p_id' => 'refsasaran_p_id']);
    }

    public function getTujuan()
    {
        return $this->hasOne(SakipTujuanP::class, ['reftujuan_p_id' => 'reftujuan_p_id'])
            ->via('sasaranP');  // Relasi via sakip_sasaran
    }

    public function getVisi()
    {
        return $this->hasOne(SakipVisiP::class, ['refvisi_p_id' => 'refvisi_p_id'])
            ->via('sasaranP');  // Relasi via sakip_sasaran
    }

    public function getMisi()
    {
        return $this->hasOne(SakipMisiP::class, ['refmisi_p_id' => 'refmisi_p_id'])
            ->via('sasaranP');  // Relasi via sakip_sasaran
    }

    public function getRefTujuan()
    {
        return $this->hasOne(SakipTujuanP::class, ['reftujuan_p_id' => 'reftujuan_p_id'])
            ->via('sasaranP');  // Relasi via sakip_sasaran
    }

    public function getRefSasaran()
    {
        return $this->hasOne(SakipSasaranP::class, ['refsasaran_p_id' => 'refsasaran_p_id']);
    }

    public function getRefVisi()
    {
        return $this->hasOne(SakipVisiP::class, ['refvisi_p_id' => 'refvisi_p_id'])
            ->via('sasaranP');  // Relasi via sakip_sasaran
    }

    public function getRefMisi()
    {
        return $this->hasOne(SakipMisiP::class, ['refmisi_p_id' => 'refmisi_p_id'])
            ->via('sasaranP');  // Relasi via sakip_sasaran
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

    public function getIndikators()
    {
        return $this->hasMany(SakipIndikatorsasaranrenstraP::class, ['refsasaranrenstra_p_id' => 'refsasaranrenstra_p_id']);
    }

    // Relasi ke tujuan renstra
    public function getTujuanRenstra()
    {
        return $this->hasMany(SakipTujuanrenstraP::class, ['refsasaranrenstra_p_id' => 'refsasaranrenstra_p_id']);
    }

    // Relasi ke SakipStrategi
    public function getRenstraTujuan()
    {
        return $this->hasMany(SakipRenstratujuan::className(), ['refsasaranrenstra_id' => 'refsasaranrenstra_id']);
    }

    // Relasi ke SakipStrategi
    public function getStrategiRenstra()
    {
        return $this->hasMany(SakipStrategi::className(), ['refsasaranrenstra_id' => 'refsasaranrenstra_id']);
    }

    // Relasi ke indikator tujuan renstra
    public function getIndikatorTujuanRenstra()
    {
        return $this->hasOne(SakipIndikatortujuanrenstra::class, ['reftujuanrenstra_id' => 'reftujuanrenstra_id']);
    }

    public function getIndikatorSasaran()
    {
        return $this->hasMany(SakipIndikatorsasaranrenstraP::class, ['refsasaranrenstra_p_id' => 'refsasaranrenstra_p_id']);
    }

    public function getRefIndikatorsasaranrenstra()
    {
        return $this->hasMany(SakipIndikatorsasaranrenstraP::class, ['refsasaranrenstra_p_id' => 'refsasaranrenstra_p_id']);
    }
}
