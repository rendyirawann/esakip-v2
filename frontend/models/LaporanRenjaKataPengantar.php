<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "laporan_renja_kata_pengantar".
 *
 * @property int $laporan_renja_kata_pengantar_id
 * @property string|null $uraian_katapengantar
 * @property int|null $refperiode_id
 * @property int|null $refskpd_id
 * @property string|null $halaman_renja
 */
class LaporanRenjaKataPengantar extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'laporan_renja_kata_pengantar';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['uraian_katapengantar'], 'string'],
            [['refperiode_id', 'refskpd_id'], 'integer'],
            [['halaman_renja'], 'string', 'max' => 75],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'laporan_renja_kata_pengantar_id' => 'Laporan Renja Kata Pengantar ID',
            'uraian_katapengantar' => 'Uraian Katapengantar',
            'refperiode_id' => 'Refperiode ID',
            'refskpd_id' => 'Refskpd ID',
            'halaman_renja' => 'Halaman Renja',
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
}
