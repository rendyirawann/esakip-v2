<?php

namespace frontend\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\SakipIndikatorsasaranrenstra;

/**
 * SakipIndikatorsasaranrenstraSearch represents the model behind the search form of `frontend\models\SakipIndikatorsasaranrenstra`.
 */
class SakipIndikatorsasaranrenstraSearch extends SakipIndikatorsasaranrenstra
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['refindikatorsasaranrenstra_id', 'refsasaranrenstra_id', 'refskpd_id', 'refperiode_id'], 'integer'],
            [['uraian_indikatorsasaranrenstra'], 'string'],
            [['indikatorsasaranrenstra_satuan', 'indikatorsasaranrenstra_target', 'target_rkt', 'target_rkt_p', 'target_pk', 'target_pk_p', 'realisasi', 'capaian', 'analisis', 'keterangan', 'keterangan_pk', 'keterangan_pk_p', 'indikatorsasaranrenstra_isaktif', 'iku_isaktif', 'pk_isaktif', 'alasan_sasaranrenstra', 'formulasi_sasaranrenstra', 'kriteria_sasaranrenstra'], 'safe'],
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
        $query = SakipIndikatorsasaranrenstra::find();

        // Eager load relations for performance
        $query->with(['refPeriode', 'refSasaranrenstra']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->query->orderBy(['refsasaranrenstra_id' => SORT_ASC, 'refindikatorsasaranrenstra_id' => SORT_ASC]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'refindikatorsasaranrenstra_id' => $this->refindikatorsasaranrenstra_id,
            'refsasaranrenstra_id' => $this->refsasaranrenstra_id,
            'refskpd_id' => $this->refskpd_id,
            'refperiode_id' => $this->refperiode_id,
        ]);

        $query->andFilterWhere(['like', 'uraian_indikatorsasaranrenstra', $this->uraian_indikatorsasaranrenstra])
            ->andFilterWhere(['like', 'indikatorsasaranrenstra_satuan', $this->indikatorsasaranrenstra_satuan])
            ->andFilterWhere(['like', 'indikatorsasaranrenstra_target', $this->indikatorsasaranrenstra_target])
            ->andFilterWhere(['like', 'target_rkt', $this->target_rkt])
            ->andFilterWhere(['like', 'target_rkt_p', $this->target_rkt_p])
            ->andFilterWhere(['like', 'target_pk', $this->target_pk])
            ->andFilterWhere(['like', 'target_pk_p', $this->target_pk_p])
            ->andFilterWhere(['like', 'realisasi', $this->realisasi])
            ->andFilterWhere(['like', 'capaian', $this->capaian])
            ->andFilterWhere(['like', 'analisis', $this->analisis])
            ->andFilterWhere(['like', 'keterangan', $this->keterangan])
            ->andFilterWhere(['like', 'keterangan_pk', $this->keterangan_pk])
            ->andFilterWhere(['like', 'keterangan_pk_p', $this->keterangan_pk_p])
            ->andFilterWhere(['like', 'indikatorsasaranrenstra_isaktif', $this->indikatorsasaranrenstra_isaktif])
            ->andFilterWhere(['like', 'iku_isaktif', $this->iku_isaktif])
            ->andFilterWhere(['like', 'pk_isaktif', $this->pk_isaktif])
            ->andFilterWhere(['like', 'alasan_sasaranrenstra', $this->alasan_sasaranrenstra])
            ->andFilterWhere(['like', 'formulasi_sasaranrenstra', $this->formulasi_sasaranrenstra])
            ->andFilterWhere(['like', 'kriteria_sasaranrenstra', $this->kriteria_sasaranrenstra]);

        return $dataProvider;
    }
}
