<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "sakip_urusan".
 *
 * @property int $urusan_id
 * @property string $kode_urusan
 * @property string $nama_urusan
 */
class SakipUrusan extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'sakip_urusan';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['kode_urusan', 'nama_urusan'], 'required'],
            [['nama_urusan'], 'string'],
            [['kode_urusan'], 'string', 'max' => 255],
            [['urusan_isaktif'], 'string', 'max' => 10],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'urusan_id' => 'Urusan ID',
            'kode_urusan' => 'Kode Urusan',
            'nama_urusan' => 'Nama Urusan',
            'urusan_isaktif' => 'Urusan Aktif/Tidak',
        ];
    }


    public function getPeriode()
    {
        return $this->hasOne(SakipPeriode::class, ['refperiode_id' => 'refperiode_id']);
    }

    public function getVisi()
    {
        return $this->hasOne(SakipVisi::class, ['refvisi_id' => 'refvisi_id']);
    }

    public function getMisi()
    {
        return $this->hasOne(SakipMisi::class, ['refmisi_id' => 'refmisi_id']);
    }

    public function getTujuan()
    {
        return $this->hasOne(SakipTujuan::class, ['reftujuan_id' => 'reftujuan_id']);
    }
}
