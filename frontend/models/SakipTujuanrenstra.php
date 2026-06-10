<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "v2_sakip_tujuanrenstra".
 *
 * @property int $reftujuanrenstra_id
 * @property string $uraian_tujuanrenstra
 * @property int|null $refskpd_id
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
class SakipTujuanrenstra extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'v2_sakip_tujuanrenstra';
    }


    public $refperiode_id;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            // [['uraian_tujuanrenstra'], 'required'],
            [['uraian_tujuanrenstra'], 'required', 'message' => 'Data tidak boleh kosong.'],
            [['uraian_tujuanrenstra'], 'string'],
            [['refskpd_id', 'refmisi_id', 'refsasaranrenstra_id', 'refsasaran_id', 'reftujuan_id', 'refperiode_5tahun_id', 'refperiode_id'], 'integer'],
            [['user_create', 'date_create', 'user_edit', 'date_edit', 'user_delete', 'date_delete'], 'string', 'max' => 20],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'reftujuanrenstra_id' => 'Reftujuanrenstra ID',
            'uraian_tujuanrenstra' => 'Uraian Tujuan Renstra',
            'refskpd_id' => 'Refskpd ID',
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

    // Relasi ke indikator tujuan renstra
    public function getSasaranRenstra()
    {
        return $this->hasOne(SakipSasaranrenstra::class, ['refsasaranrenstra_id' => 'refsasaranrenstra_id']);
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

    public function getIndikatorTujuanRenstra()
    {
        return $this->hasMany(SakipIndikatortujuanrenstra::class, ['reftujuanrenstra_id' => 'reftujuanrenstra_id']);
    }
}
