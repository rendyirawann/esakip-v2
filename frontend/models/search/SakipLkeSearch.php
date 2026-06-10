<?php

namespace frontend\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\SakipLke;

/**
 * SakipLkeSearch represents the model behind the search form of `frontend\models\SakipLke`.
 */
class SakipLkeSearch extends SakipLke
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['reflke_id', 'refperiode_id', 'refskpd_id', 'reflkekomponen_id', 'reflkesubkomponen_id'], 'integer'],
            [['unit_jawaban', 'unit_nilai'], 'safe'],
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
        $query = SakipLke::find();

        // Eager load relations for performance
        $query->with(['refPeriode']);

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
            'reflke_id' => $this->reflke_id,
            'refperiode_id' => $this->refperiode_id,
            'refskpd_id' => $this->refskpd_id,
            'reflkekomponen_id' => $this->reflkekomponen_id,
            'reflkesubkomponen_id' => $this->reflkesubkomponen_id,
        ]);

        $query->andFilterWhere(['like', 'unit_jawaban', $this->unit_jawaban])
            ->andFilterWhere(['like', 'unit_nilai', $this->unit_nilai]);

        return $dataProvider;
    }
}
