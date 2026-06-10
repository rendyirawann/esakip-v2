<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "sakip_penjabat_skpd".
 *
 * @property int $refpenjabatskpd_id
 * @property int|null $refskpd_id
 * @property int|null $refperiode_id
 * @property string|null $nama_penjabat
 * @property string|null $nip_penjabat
 * @property string|null $jabatan_eselon
 * @property string|null $pangkat_eselon
 */
class SakipPenjabatSkpd extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'sakip_penjabat_skpd';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['refskpd_id', 'refperiode_id', 'refeselon_id'], 'integer'],
            [['nama_penjabat', 'jabatan_eselon'], 'string'],
            [['nip_penjabat', 'pangkat_eselon'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'refpenjabatskpd_id' => 'Refpenjabatskpd ID',
            'refskpd_id' => 'Refskpd ID',
            'refperiode_id' => 'Refperiode ID',
            'nama_penjabat' => 'Nama Penjabat',
            'nip_penjabat' => 'Nip Penjabat',
            'jabatan_eselon' => 'Jabatan Eselon',
            'pangkat_eselon' => 'Pangkat Eselon',
            'refeselon_id' => 'Refeselon ID',
        ];
    }

    public function getRefPeriode()
    {
        return $this->hasOne(SakipPeriode::class, ['refperiode_id' => 'refperiode_id']);
    }

    public function getRefSkpd()
    {
        return $this->hasOne(SakipSkpd::class, ['refskpd_id' => 'refskpd_id']);
    }

    public function getRefEselon()
    {
        return $this->hasOne(SakipEselon::class, ['refeselon_id' => 'refeselon_id']);
    }
}
