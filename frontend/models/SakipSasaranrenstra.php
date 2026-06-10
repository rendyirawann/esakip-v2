<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "v2_sakip_sasaranrenstra".
 *
 * @property int $refsasaranrenstra_id
 * @property string $uraian_sasaranrenstra
 * @property int|null $refskpd_id
 * @property int|null $refsasaran_id
 * @property int|null $reftujuanrenstra_id
 * @property string|null $sasaranrenstra_isaktif
 */
class SakipSasaranrenstra extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'v2_sakip_sasaranrenstra';
    }


    public $refperiode_id;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['uraian_sasaranrenstra', 'refsasaran_id', 'refmisi_id', 'reftujuan_id', 'refvisi_id'], 'required'],
            [['uraian_sasaranrenstra', 'alasan_sasaranrenstra', 'formulasi_sasaranrenstra', 'kriteria_sasaranrenstra'], 'string'],
            [['refskpd_id', 'refperiode_5tahun_id', 'refvisi_id', 'refmisi_id', 'refsasaran_id', 'reftujuanrenstra_id', 'reftujuan_id', 'refperiode_id'], 'integer'],
            [['sasaranrenstra_isaktif'], 'string', 'max' => 10],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'refsasaranrenstra_id' => 'Refsasaranrenstra ID',
            'uraian_sasaranrenstra' => 'Uraian Sasaranrenstra',
            'refskpd_id' => 'Refskpd ID',
            'refvisi_id' => 'Visi Terkait',
            'refmisi_id' => 'Misi Terkait',
            'reftujuan_id' => 'Tujuan Terkait',
            'refperiode_5tahun_id' => 'Periode',
            'refperiode_id' => 'Refperiode ID',
            'refsasaran_id' => 'Refsasaran ID',
            'reftujuanrenstra_id' => 'Reftujuanrenstra ID',
            'sasaranrenstra_isaktif' => 'Sasaranrenstra Isaktif',
            'alasan_sasaranrenstra' => 'Alasan Sasaranrenstra',
            'formulasi_sasaranrenstra' => 'Formulasi Sasaranrenstra',
            'kriteria_sasaranrenstra' => 'Kriteria Sasaranrenstra',
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
        return $this->hasOne(SakipSasaran::class, ['refsasaran_id' => 'refsasaran_id']);
    }

    public function getTujuan()
    {
        return $this->hasOne(SakipTujuan::class, ['reftujuan_id' => 'reftujuan_id'])
            ->via('sasaran');  // Relasi via sakip_sasaran
    }

    public function getVisi()
    {
        return $this->hasOne(SakipVisi::class, ['refvisi_id' => 'refvisi_id'])
            ->via('sasaran');  // Relasi via sakip_sasaran
    }

    public function getMisi()
    {
        return $this->hasOne(SakipMisi::class, ['refmisi_id' => 'refmisi_id'])
            ->via('sasaran');  // Relasi via sakip_sasaran
    }

    public function getRefTujuan()
    {
        return $this->hasOne(SakipTujuan::class, ['reftujuan_id' => 'reftujuan_id'])
            ->via('sasaran');  // Relasi via sakip_sasaran
    }

    public function getRefSasaran()
    {
        return $this->hasOne(SakipSasaran::class, ['refsasaran_id' => 'refsasaran_id']);
    }

    public function getRefVisi()
    {
        return $this->hasOne(SakipVisi::class, ['refvisi_id' => 'refvisi_id'])
            ->via('sasaran');  // Relasi via sakip_sasaran
    }

    public function getRefMisi()
    {
        return $this->hasOne(SakipMisi::class, ['refmisi_id' => 'refmisi_id'])
            ->via('sasaran');  // Relasi via sakip_sasaran
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
        return $this->hasMany(SakipIndikatorsasaranrenstra::class, ['refsasaranrenstra_id' => 'refsasaranrenstra_id']);
    }

    // Relasi ke tujuan renstra
    public function getTujuanRenstra()
    {
        return $this->hasMany(SakipTujuanrenstra::class, ['refsasaranrenstra_id' => 'refsasaranrenstra_id']);
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
        return $this->hasMany(SakipIndikatorsasaranrenstra::class, ['refsasaranrenstra_id' => 'refsasaranrenstra_id']);
    }

    public function getRefIndikatorsasaranrenstra()
    {
        return $this->hasMany(SakipIndikatorsasaranrenstra::class, ['refsasaranrenstra_id' => 'refsasaranrenstra_id']);
    }

    public function getCascadingPrograms()
    {
        return $this->hasMany(SakipCascadingprogram::class, ['refsasaranrenstra_id' => 'refsasaranrenstra_id']);
    }

    public static function find()
    {
        return new class(static::class) extends \yii\db\ActiveQuery {
            public function prepare($builder) {
                $this->where = $this->modifyPeriodCondition($this->where);
                return parent::prepare($builder);
            }
            
            private function modifyPeriodCondition($where) {
                if (is_array($where)) {
                    if (isset($where['refperiode_id'])) {
                        $refperiode_id = $where['refperiode_id'];
                        unset($where['refperiode_id']);
                        if (is_array($refperiode_id)) {
                            $p5Ids = [];
                            foreach ($refperiode_id as $id) {
                                $periode = SakipPeriode::findOne($id);
                                if ($periode) {
                                    $p5Ids[] = $periode->refperiode_5tahun_id;
                                }
                            }
                            $where['refperiode_5tahun_id'] = array_unique($p5Ids);
                        } else {
                            $periode = SakipPeriode::findOne($refperiode_id);
                            $where['refperiode_5tahun_id'] = $periode ? $periode->refperiode_5tahun_id : null;
                        }
                    }
                    foreach ($where as $key => $value) {
                        if (is_array($value)) {
                            $where[$key] = $this->modifyPeriodCondition($value);
                        }
                    }
                }
                return $where;
            }
        };
    }
}
