<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "sakip_lke".
 *
 * @property int $reflke_id
 * @property int|null $refperiode_id
 * @property int|null $refskpd_id
 * @property int|null $reflkekomponen_id
 * @property int|null $reflkesubkomponen_id
 * @property string|null $unit_jawaban
 * @property string|null $unit_nilai
 */
class SakipLke extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'sakip_lke';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['refperiode_id', 'refskpd_id', 'reflkekomponen_id', 'reflkesubkomponen_id'], 'integer'],
            [['unit_jawaban'], 'string'],
            [['unit_nilai'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'reflke_id' => 'Reflke ID',
            'refperiode_id' => 'Refperiode ID',
            'refskpd_id' => 'Refskpd ID',
            'reflkekomponen_id' => 'Reflkekomponen ID',
            'reflkesubkomponen_id' => 'Reflkesubkomponen ID',
            'unit_jawaban' => 'Unit Jawaban',
            'unit_nilai' => 'Unit Nilai',
        ];
    }
}
