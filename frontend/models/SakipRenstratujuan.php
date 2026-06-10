<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "sakip_renstratujuan".
 *
 * @property int $refrenstratujuan_id
 * @property string $uraian_renstratujuan
 * @property int|null $refskpd_id
 * @property int|null $refmisi_id
 * @property int|null $refsasaranrenstra_id
 * @property int|null $refsasaran_id
 * @property int|null $reftujuan_id
 * @property int|null $refperiode_id
 * @property string|null $user_create
 * @property string|null $date_create
 * @property string|null $user_edit
 * @property string|null $date_edit
 * @property string|null $user_delete
 * @property string|null $date_delete
 */
class SakipRenstratujuan extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'sakip_renstratujuan';
    }


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['uraian_renstratujuan'], 'required'],
            [['uraian_renstratujuan'], 'string'],
            [['refskpd_id', 'refmisi_id', 'refsasaranrenstra_id', 'refsasaran_id', 'reftujuan_id', 'refperiode_id'], 'integer'],
            [['user_create', 'date_create', 'user_edit', 'date_edit', 'user_delete', 'date_delete'], 'string', 'max' => 20],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'refrenstratujuan_id' => 'Refrenstratujuan ID',
            'uraian_renstratujuan' => 'Uraian Renstratujuan',
            'refskpd_id' => 'Refskpd ID',
            'refmisi_id' => 'Refmisi ID',
            'refsasaranrenstra_id' => 'Refsasaranrenstra ID',
            'refsasaran_id' => 'Refsasaran ID',
            'reftujuan_id' => 'Reftujuan ID',
            'refperiode_id' => 'Refperiode ID',
            'user_create' => 'User Create',
            'date_create' => 'Date Create',
            'user_edit' => 'User Edit',
            'date_edit' => 'Date Edit',
            'user_delete' => 'User Delete',
            'date_delete' => 'Date Delete',
        ];
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

    public function getRefPeriode()
    {
        return $this->hasOne(SakipPeriode::class, ['refperiode_id' => 'refperiode_id']);
    }
}
