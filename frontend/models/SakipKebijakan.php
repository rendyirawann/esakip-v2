<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "v2_sakip_kebijakan".
 *
 * @property int $refkebijakan_id
 * @property string $uraian_kebijakan
 * @property int|null $refskpd_id
 * @property int|null $refstrategi_id
 * @property int|null $refmisi_id
 * @property int|null $refsasaranrenstra_id
 * @property int|null $refsasaran_id
 * @property int|null $reftujuan_id
 * @property int|null $refperiode_5tahun_id
 * @property string|null $user_create
 * @property string|null $date_create
 * @property string|null $user_edit
 * @property string|null $date_edit
 * @property string|null $user_delete
 * @property string|null $date_delete
 */
class SakipKebijakan extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'v2_sakip_kebijakan';
    }


    public $refperiode_id;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            // [['uraian_kebijakan'], 'required'],
            [['uraian_kebijakan'], 'required', 'message' => 'Data tidak boleh kosong.'],
            [['uraian_kebijakan'], 'string'],
            [['refskpd_id', 'refstrategi_id', 'refmisi_id', 'refsasaranrenstra_id', 'refsasaran_id', 'reftujuan_id', 'refperiode_5tahun_id', 'refperiode_id'], 'integer'],
            [['date_create', 'date_edit', 'date_delete'], 'safe'],
            [['user_create', 'user_edit', 'user_delete'], 'string', 'max' => 20],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'refkebijakan_id' => 'Refkebijakan ID',
            'uraian_kebijakan' => 'Uraian Kebijakan',
            'refskpd_id' => 'Refskpd ID',
            'refstrategi_id' => 'Refstrategi ID',
            'refmisi_id' => 'Refmisi ID',
            'refsasaranrenstra_id' => 'Refsasaranrenstra ID',
            'refsasaran_id' => 'Refsasaran ID',
            'reftujuan_id' => 'Reftujuan ID',
            'refperiode_5tahun_id' => 'Refperiode 5 Tahun ID',
            'refperiode_id' => 'Refperiode ID',
            'user_create' => 'User Create',
            'date_create' => 'Date Create',
            'user_edit' => 'User Edit',
            'date_edit' => 'Date Edit',
            'user_delete' => 'User Delete',
            'date_delete' => 'Date Delete',
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

    public function getSasaranRenstra()
    {
        return $this->hasOne(SakipSasaranRenstra::class, ['refsasaranrenstra_id' => 'refsasaranrenstra_id']);
    }

    public function getStrategiRenstra()
    {
        return $this->hasOne(SakipStrategi::class, ['refstrategi_id' => 'refstrategi_id']);
    }

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

    public function getKebijakan()
    {
        return $this->hasMany(SakipKebijakan::class, ['refstrategi_id' => 'refstrategi_id']);
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
