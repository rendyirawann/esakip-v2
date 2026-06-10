<?php

namespace frontend\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\SakipIndikatorsasaranrenstraPTriwulan;

/**
 * SakipIndikatorsasaranrenstraTriwulanSearch represents the model behind the search form of `frontend\models\SakipIndikatorsasaranrenstraTriwulan`.
 */
class SakipIndikatorsasaranrenstraPTriwulanSearch extends SakipIndikatorsasaranrenstraPTriwulan
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['refindikatorsasaranrenstratriwulan_p_id', 'refindikatorsasaranrenstra_p_id', 'refsasaranrenstra_p_id', 'refskpd_id', 'refperiode_id', 'reftriwulan_id'], 'integer'],
            [['triwulan_target_rkt', 'triwulan_target_rkt_p', 'triwulan_target_pk', 'triwulan_target_pk_p', 'triwulan_realisasi', 'triwulan_capaian', 'triwulan_keterangan', 'triwulan_keterangan_pk_p', 'triwulan_analisis'], 'safe'],
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
        $query = SakipIndikatorsasaranrenstraPTriwulan::find();

        // Eager load relations for performance
        $query->with(['refPeriode', 'refIndikatorsasaranrenstra']);

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
            'refindikatorsasaranrenstratriwulan_p_id' => $this->refindikatorsasaranrenstratriwulan_p_id,
            'refindikatorsasaranrenstra_p_id' => $this->refindikatorsasaranrenstra_p_id,
            'refsasaranrenstra_p_id' => $this->refsasaranrenstra_p_id,
            'refskpd_id' => $this->refskpd_id,
            'refperiode_id' => $this->refperiode_id,
            'reftriwulan_id' => $this->reftriwulan_id,
        ]);

        $query->andFilterWhere(['like', 'triwulan_target_rkt', $this->triwulan_target_rkt])
            ->andFilterWhere(['like', 'triwulan_target_rkt_p', $this->triwulan_target_rkt_p])
            ->andFilterWhere(['like', 'triwulan_target_pk', $this->triwulan_target_pk])
            ->andFilterWhere(['like', 'triwulan_target_pk_p', $this->triwulan_target_pk_p])
            ->andFilterWhere(['like', 'triwulan_realisasi', $this->triwulan_realisasi])
            ->andFilterWhere(['like', 'triwulan_capaian', $this->triwulan_capaian])
            ->andFilterWhere(['like', 'triwulan_keterangan', $this->triwulan_keterangan])
            ->andFilterWhere(['like', 'triwulan_keterangan_pk_p', $this->triwulan_keterangan_pk_p])
            ->andFilterWhere(['like', 'triwulan_analisis', $this->triwulan_analisis]);

        return $dataProvider;
    }
}
