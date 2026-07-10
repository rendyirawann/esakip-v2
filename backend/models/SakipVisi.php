<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "v2_sakip_visi".
 *
 * @property int $refvisi_id
 * @property string $uraian_visi
 * @property string $penjabaran_visi
 * @property int|null $refperiode_5tahun_id
 * @property string|null $visi_isaktif
 */
class SakipVisi extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'v2_sakip_visi';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['uraian_visi', 'penjabaran_visi'], 'required'],
            [['uraian_visi', 'penjabaran_visi'], 'string'],
            [['refperiode_5tahun_id', 'refperiode_id'], 'integer'],
            [['visi_isaktif'], 'string', 'max' => 10],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'refvisi_id' => 'Refvisi ID',
            'uraian_visi' => 'Uraian Visi',
            'penjabaran_visi' => 'Penjabaran Visi',
            'refperiode_5tahun_id' => 'Refperiode 5 Tahun ID',
            'refperiode_id' => 'Refperiode ID',
            'visi_isaktif' => 'Visi Isaktif',
        ];
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

    public function getPeriode5Tahun()
    {
        return $this->hasOne(SakipPeriode5tahun::class, ['refperiode_5tahun_id' => 'refperiode_5tahun_id']);
    }

    /**
     * Relasi ke tahun spesifik yang DIPILIH user (kolom refperiode_id).
     */
    public function getPeriodePilihan()
    {
        return $this->hasOne(SakipPeriode::class, ['refperiode_id' => 'refperiode_id']);
    }

    /**
     * Label periode untuk tampilan, mis. "2024 – 2029 (Tahun 2025)".
     */
    public function periodeLabel()
    {
        $p5 = $this->periode5Tahun;
        $range = $p5 ? ($p5->tahun_mulai . ' – ' . $p5->tahun_selesai) : '-';
        $thn = $this->periodePilihan ? $this->periodePilihan->periode : null;
        return $range . ($thn ? ' (Tahun ' . $thn . ')' : '');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPeriode()
    {
        return $this->hasOne(SakipPeriode::class, ['refperiode_5tahun_id' => 'refperiode_5tahun_id'])
            ->orderBy(['periode_isaktif' => SORT_DESC, 'periode' => SORT_ASC]);
    }
}
