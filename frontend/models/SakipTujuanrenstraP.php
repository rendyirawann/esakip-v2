<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "v2_sakip_tujuanrenstra_p".
 *
 * @property int $reftujuanrenstra_p_id
 * @property int $reftujuanrenstra_id
 * @property string $uraian_tujuanrenstra_p
 * @property int|null $refskpd_id
 * @property int|null $refmisi_p_id
 * @property int|null $reftujuan_p_id
 * @property int|null $refsasaranrenstra_p_id
 * @property int|null $refsasaranrenstra_id
 * @property int|null $refsasaran_p_id
 * @property int|null $refperiode_5tahun_id
 * @property string|null $user_create
 * @property string|null $date_create
 * @property string|null $user_edit
 * @property string|null $date_edit
 * @property string|null $user_delete
 * @property string|null $date_delete
 * @property string|null $tujuanrenstra_p_isaktif
 */
class SakipTujuanrenstraP extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'v2_sakip_tujuanrenstra_p';
    }


    public $refperiode_id;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['uraian_tujuanrenstra_p', 'reftujuanrenstra_id', 'refsasaranrenstra_p_id'], 'required'],
            [['uraian_tujuanrenstra_p'], 'string'],
            [['refskpd_id', 'refmisi_p_id', 'reftujuan_p_id', 'refsasaranrenstra_id', 'refsasaran_p_id', 'refperiode_5tahun_id', 'refperiode_id'], 'integer'],
            [['user_create', 'date_create', 'user_edit', 'date_edit', 'user_delete', 'date_delete'], 'string', 'max' => 20],
            [['tujuanrenstra_p_isaktif'], 'string', 'max' => 10],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'reftujuanrenstra_p_id' => 'Reftujuanrenstra ID',
            'reftujuanrenstra_id' => 'Reftujuanrenstra Perubahan ID',
            'uraian_tujuanrenstra_p' => 'Uraian Tujuanrenstra Perubahan',
            'refskpd_id' => 'Refskpd Perubahan ID',
            'refmisi_p_id' => 'Refmisi Perubahan ID',
            'reftujuan_p_id' => 'Reftujuan Perubahan ID',
            'refsasaranrenstra_p_id' => 'Refsasaranrenstra Perubahan ID',
            'refsasaranrenstra_id' => 'Refsasaranrenstra ID',
            'refsasaran_p_id' => 'Refsasaranrenstra ID',
            'refperiode_5tahun_id' => 'Refperiode 5 Tahun ID',
            'refperiode_id' => 'Refperiode ID',
            'user_create' => 'User Create',
            'date_create' => 'Date Create',
            'user_edit' => 'User Edit',
            'date_edit' => 'Date Edit',
            'user_delete' => 'User Delete',
            'date_delete' => 'Date Delete',
            'tujuanrenstra_p_isaktif' => 'Tujuanrenstra Isaktif',
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
        return $this->hasOne(SakipSasaranrenstraP::class, ['refsasaranrenstra_p_id' => 'refsasaranrenstra_p_id']);
    }

    public function getSasaran()
    {
        return $this->hasOne(SakipSasaranP::class, ['refsasaran_p_id' => 'refsasaran_p_id']);
    }

    public function getTujuan()
    {
        return $this->hasOne(SakipTujuanP::class, ['reftujuan_p_id' => 'reftujuan_p_id'])
            ->via('sasaran');  // Relasi via sakip_sasaran
    }

    public function getMisi()
    {
        return $this->hasOne(SakipMisiP::class, ['refmisi_p_id' => 'refmisi_p_id'])
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
}
