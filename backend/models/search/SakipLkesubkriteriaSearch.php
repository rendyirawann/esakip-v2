<?php

namespace backend\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\SakipLkesubkriteria;

/**
 * SakipLkesubkriteriaSearch represents the model behind the search form of `backend\models\SakipLkesubkriteria`.
 */
class SakipLkesubkriteriaSearch extends SakipLkesubkriteria
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['reflkesubkriteria_id', 'reflkekomponen_id', 'reflkesubkomponen_id'], 'integer'],
            [['uraian_lkesubkriteria'], 'safe'],
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
        $query = SakipLkesubkriteria::find();

        // add conditions that should always apply here
        $query->with(['refLkekomponen', 'refLkesubkomponen']);

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
            'reflkesubkriteria_id' => $this->reflkesubkriteria_id,
            'reflkekomponen_id' => $this->reflkekomponen_id,
            'reflkesubkomponen_id' => $this->reflkesubkomponen_id,
        ]);

        $query->andFilterWhere(['like', 'uraian_lkesubkriteria', $this->uraian_lkesubkriteria]);

        return $dataProvider;
    }
}
