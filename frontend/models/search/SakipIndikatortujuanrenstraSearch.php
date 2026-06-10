<?php

namespace frontend\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\SakipIndikatortujuanrenstra;

/**
 * SakipIndikatortujuanrenstraSearch represents the model behind the search form of `frontend\models\SakipIndikatortujuanrenstra`.
 */
class SakipIndikatortujuanrenstraSearch extends SakipIndikatortujuanrenstra
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['refindikatortujuanrenstra_id', 'reftujuanrenstra_id', 'refskpd_id', 'refperiode_id', 'refsasaranrenstra_id'], 'integer'],
            [['uraian_indikatortujuanrenstra'], 'string'],
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
        $query = SakipIndikatortujuanrenstra::find();

        // Eager load relations for performance
        $query->with(['refPeriode', 'tujuanRenstra']);

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
            'refindikatortujuanrenstra_id' => $this->refindikatortujuanrenstra_id,
            'reftujuanrenstra_id' => $this->reftujuanrenstra_id,
            'refsasaranrenstra_id' => $this->refsasaranrenstra_id,
            'refskpd_id' => $this->refskpd_id,
            'refperiode_id' => $this->refperiode_id,
        ]);

        $query->andFilterWhere(['like', 'uraian_indikatortujuanrenstra', $this->uraian_indikatortujuanrenstra]);

        return $dataProvider;
    }
}
