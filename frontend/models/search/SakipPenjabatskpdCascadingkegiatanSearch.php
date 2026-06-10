<?php

namespace frontend\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\SakipPenjabatskpdCascadingkegiatan;

/**
 * SakipPenjabatskpdCascadingkegiatanSearch represents the model behind the search form of `frontend\models\SakipPenjabatskpdCascadingkegiatan`.
 */
class SakipPenjabatskpdCascadingkegiatanSearch extends SakipPenjabatskpdCascadingkegiatan
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['refpenjabatcascadingkegiatan_id', 'refpenjabatskpd_id', 'refeselon_id', 'refcascadingprogram_id', 'refcascadingkegiatan_id', 'refindikatorkegiatan_id', 'refskpd_id', 'refperiode_id', 'refsasaranrenstra_id', 'refindikatorsasaranrenstra_id', 'refprogram_id', 'refkegiatan_id'], 'integer'],
            [['uraian_sasarankegiatan', 'uraian_indikatorkegiatan', 'kegiatan_target', 'kegiatan_satuan', 'target_rkt', 'target_rkt_p', 'target_pk', 'target_pk_p', 'realisasi', 'capaian', 'keterangan', 'analisis'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = SakipPenjabatskpdCascadingkegiatan::find();

        // Eager load relations for performance
        $query->with(['refProgram', 'refKegiatan', 'refPeriode']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'refpenjabatcascadingkegiatan_id' => $this->refpenjabatcascadingkegiatan_id,
            'refpenjabatskpd_id' => $this->refpenjabatskpd_id,
            'refeselon_id' => $this->refeselon_id,
            'refcascadingprogram_id' => $this->refcascadingprogram_id,
            'refcascadingkegiatan_id' => $this->refcascadingkegiatan_id,
            'refindikatorkegiatan_id' => $this->refindikatorkegiatan_id,
            'refskpd_id' => $this->refskpd_id,
            'refperiode_id' => $this->refperiode_id,
            'refsasaranrenstra_id' => $this->refsasaranrenstra_id,
            'refindikatorsasaranrenstra_id' => $this->refindikatorsasaranrenstra_id,
            'refprogram_id' => $this->refprogram_id,
            'refkegiatan_id' => $this->refkegiatan_id,
        ]);

        $query->andFilterWhere(['like', 'uraian_sasarankegiatan', $this->uraian_sasarankegiatan])
            ->andFilterWhere(['like', 'uraian_indikatorkegiatan', $this->uraian_indikatorkegiatan])
            ->andFilterWhere(['like', 'kegiatan_target', $this->kegiatan_target])
            ->andFilterWhere(['like', 'kegiatan_satuan', $this->kegiatan_satuan])
            ->andFilterWhere(['like', 'target_rkt', $this->target_rkt])
            ->andFilterWhere(['like', 'target_rkt_p', $this->target_rkt_p])
            ->andFilterWhere(['like', 'target_pk', $this->target_pk])
            ->andFilterWhere(['like', 'target_pk_p', $this->target_pk_p])
            ->andFilterWhere(['like', 'realisasi', $this->realisasi])
            ->andFilterWhere(['like', 'capaian', $this->capaian])
            ->andFilterWhere(['like', 'keterangan', $this->keterangan])
            ->andFilterWhere(['like', 'analisis', $this->analisis]);

        return $dataProvider;
    }
}
