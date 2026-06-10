<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "sakip_program".
 *
 * @property int $refprogram_id
 * @property string $kode_program
 * @property string $nama_program
 * @property int|null $refurusan_id
 * @property int|null $refbidang_id
 * @property string|null $program_isaktif
 */
class SakipProgram extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'sakip_program';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['kode_program', 'nama_program'], 'required'],
            [['nama_program'], 'string'],
            [['refurusan_id', 'refbidang_id'], 'integer'],
            [['kode_program'], 'string', 'max' => 150],
            [['program_isaktif'], 'string', 'max' => 10],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'refprogram_id' => 'Refprogram ID',
            'kode_program' => 'Kode Program',
            'nama_program' => 'Nama Program',
            'refurusan_id' => 'Refurusan ID',
            'refbidang_id' => 'Refbidang ID',
            'program_isaktif' => 'Program Isaktif',
        ];
    }

    public function getUrusan()
    {
        return $this->hasOne(SakipUrusan::class, ['urusan_id' => 'refurusan_id']);
    }
    
    public function getBidang()
    {
        return $this->hasOne(SakipBidang::class, ['refbidang_id' => 'refbidang_id']);
    }
}
