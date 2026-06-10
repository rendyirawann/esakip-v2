<?php

namespace frontend\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\SakipIndikatorsasaranrenstraP;

/**
 * SakipIndikatorsasaranrenstraSearch represents the model behind the search form of `frontend\models\SakipIndikatorsasaranrenstra`.
 */
class SakipIndikatorsasaranrenstraPSearch extends SakipIndikatorsasaranrenstraP
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['refindikatorsasaranrenstra_p_id', 'refsasaranrenstra_p_id', 'refskpd_id', 'refperiode_id'], 'integer'],
            [['uraian_indikatorsasaranrenstra_p'], 'string'],
            [['indikatorsasaranrenstra_p_satuan', 'indikatorsasaranrenstra_p_target', 'target_rkt', 'target_rkt_p', 'target_pk', 'target_pk_p', 'realisasi', 'capaian', 'analisis', 'keterangan', 'keterangan_pk', 'keterangan_pk_p', 'indikatorsasaranrenstra_p_isaktif', 'iku_isaktif', 'pk_isaktif', 'alasan_sasaranrenstra_p', 'formulasi_sasaranrenstra_p', 'kriteria_sasaranrenstra_p'], 'safe'],
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
        $query = SakipIndikatorsasaranrenstraP::find();

        // Eager load relations for performance
        $query->with(['refPeriode', 'refSasaranrenstra']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->query->orderBy(['refsasaranrenstra_p_id' => SORT_ASC, 'refindikatorsasaranrenstra_p_id' => SORT_ASC]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'refindikatorsasaranrenstra_p_id' => $this->refindikatorsasaranrenstra_p_id,
            'refsasaranrenstra_p_id' => $this->refsasaranrenstra_p_id,
            'refskpd_id' => $this->refskpd_id,
            'refperiode_id' => $this->refperiode_id,
        ]);

        $query->andFilterWhere(['like', 'uraian_indikatorsasaranrenstra_p', $this->uraian_indikatorsasaranrenstra_p])
            ->andFilterWhere(['like', 'indikatorsasaranrenstra_p_satuan', $this->indikatorsasaranrenstra_p_satuan])
            ->andFilterWhere(['like', 'indikatorsasaranrenstra_p_target', $this->indikatorsasaranrenstra_p_target])
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
            ->andFilterWhere(['like', 'indikatorsasaranrenstra_p_isaktif', $this->indikatorsasaranrenstra_p_isaktif])
            ->andFilterWhere(['like', 'iku_isaktif', $this->iku_isaktif])
            ->andFilterWhere(['like', 'pk_isaktif', $this->pk_isaktif])
            ->andFilterWhere(['like', 'alasan_sasaranrenstra_p', $this->alasan_sasaranrenstra_p])
            ->andFilterWhere(['like', 'formulasi_sasaranrenstra_p', $this->formulasi_sasaranrenstra_p])
            ->andFilterWhere(['like', 'kriteria_sasaranrenstra_p', $this->kriteria_sasaranrenstra_p]);

        return $dataProvider;
    }
}
