<?php

namespace frontend\models;

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
            [['refeselon_id', 'reftitle_id'], 'integer'],
            [['nip'], 'string', 'max' => 35],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'refpegawai_id' => 'Refpegawai ID',
            'nama_pegawai' => 'Nama Pegawai',
            'nip' => 'Nip',
            'refeselon_id' => 'Refeselon ID',
            'reftitle_id' => 'Reftitle ID',
        ];
    }
}
