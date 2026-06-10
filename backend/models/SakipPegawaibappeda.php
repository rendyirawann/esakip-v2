<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "sakip_pegawaibappeda".
 *
 * @property int $refpegawai_id
 * @property string|null $nama_pegawai
 * @property string|null $nip
 * @property int|null $refeselon_id
 * @property int|null $reftitle_id
 */
class SakipPegawaibappeda extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'sakip_pegawaibappeda';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nama_pegawai'], 'string'],
            [['refeselon_id', 'reftitle_id', 'refbidangbappeda_id', 'statusAparatur'], 'integer'],
            [['nip'], 'string', 'max' => 35],
            [['no_hp'], 'string', 'max' => 75],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'refpegawai_id' => 'Refpegawai ID',
            'statusAparatur' => 'Status Aparatur',
            'nama_pegawai' => 'Nama Pegawai',
            'nip' => 'Nip',
            'refeselon_id' => 'Refeselon ID',
            'reftitle_id' => 'Reftitle ID',
            'refbidangbappeda_id' => 'Refbidang Bappeda ID',
            'no_hp' => 'Nomor Handphone',
        ];
    }

    public function getRefEselon()
    {
        return $this->hasOne(SakipEselon::class, ['refeselon_id' => 'refeselon_id']);
    }

    public function getRefTitle()
    {
        return $this->hasOne(SakipTitle::class, ['reftitle_id' => 'reftitle_id']);
    }

    public function getRefBidangbappeda()
    {
        return $this->hasOne(SakipBidangbappeda::class, ['refbidangbappeda_id' => 'refbidangbappeda_id']);
    }
}
